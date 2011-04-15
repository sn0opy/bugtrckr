<?php

/**
	Template engine for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3::Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package Network
		@version 2.0.0
**/

//! Template engine
class Template extends Base {

	private static
		// Functions allowed in template
		$funcs=array();

	const
		//! Default extensions allowed in templates
		FUNCS_Default='standard|date|pcre',
		//! Empty HTML tags
		HTML_Tags='area|base|br|col|frame|hr|img|input|link|meta|param';

	/**
		Fix mangled braces
			@return string
			@param $str string
			@public
	**/
	static function fixbraces($str) {
		return strtr(
			$str,array('%7B'=>'{','%7D'=>'}','%5B'=>'[','%5D'=>']','%20'=>' ')
		);
	}

	/**
		Evaluate template expressions in string
			@return mixed
			@param $str string
			@public
	**/
	static function resolve($str) {
		// Analyze string for correct framework expression syntax
		$str=preg_replace_callback(
			// Expression
			'/{('.
				// Capture group
				'(?:'.
					// Look-ahead group
					'(?:'.
						// Framework variable
						'@\w+(?:\[[^\]]+\]|\.\w+)*(?:->\w+)?|'.
						// Function
						'@?\w+\h*(?=\(.*\))|'.
						// String
						'\'[^\']*\'|"[^"]*"|'.
						// Number
						'(?:\d+\.)?\d*(?:e[+\-]?\d+)?|'.
						// Null and boolean constants
						'NULL|TRUE|FALSE'.
					// End of look-ahead
					')(?!\h*[@\w\'"])|'.
					// Whitespace and operators
					'[\h\.\-\/()+*,%!?=<>|&:]'.
				// End of captured string
				')+'.
			// End of expression
			')}/i',
			function($expr) {
				$self=__CLASS__;
				// Evaluate expression
				$out=preg_replace_callback(
					// Framework variable
					'/@(\w+(?:\[[^\]]+\]|\.\w+)*)(?:->(\w+))?/',
					function($var) use($self) {
						// Retrieve variable contents
						$val=$self::ref($var[1],FALSE);
						if (isset($var[2]) && is_object($val))
							// Use object property
							$val=$val->$var[2];
						if (is_string($val) && preg_match('/{.+}/',$val))
							// Variable variable? Call recursively
							$val=$self::resolve($val);
						// Check syntax before replacing contents
						return $self::stringify($val);
					},
					preg_replace_callback(
						// Function
						'/(@?)(\w+)\h*\(([^\)]*)\)/',
						function($val) use($self) {
							if ($val[1] &&
								is_callable($self::ref($val[2],FALSE)))
								// Variable holds an anonymous function
								return call_user_func_array(
									$self::ref($val[2],FALSE),
									str_getcsv($val[3])
								);
							// Transform empty array to NULL
							return ($val[2].trim($val[3]))=='array'?
								'NULL':
								// check if prohibited function
								($self::allowed($val[2])?
									$val[0]:var_export($val[0],TRUE));
						},
						$expr[1]
					)
				);
				return eval('return (string)'.$out.';');
			},
			self::fixbraces($str)
		);
		return $str;
	}

	/**
		Process <F3:include> directives
			@return string
			@param $file string
			@param $path string
			@public
	**/
	static function embed($file,$path) {
		if (!$file || !is_file($path.$file))
			return '';
		$file=$path.$file;
		$hash='tpl.'.self::hash($file);
		$cached=Cache::cached($hash);
		if (!isset(self::$vars['STATS']['TEMPLATES']))
			self::$vars['STATS']['TEMPLATES']=array(
				'cache'=>array(),
				'loaded'=>array()
			);
		if ($cached && filemtime($file)<$cached['time']) {
			$text=Cache::get($hash);
			// Gather template file info for profiler
			self::$vars['STATS']['TEMPLATES']
				['cache'][$file]=$cached['size'];
		}
		else {
			// Remove inline comments
			$text=preg_replace('/{\*.+?\*}/s','',file_get_contents($file));
			Cache::set($hash,$text);
			// Gather template file info for profiler
			self::$vars['STATS']['TEMPLATES']
				['loaded'][$file]=filesize($file);
		}
		// Search/replace <F3:include> regex pattern
		$regex='/<(?:F3:)?include\h*href\h*=\h*"([^"]+)"\h*\/>/i';
		return preg_match($regex,$text)?
			// Call recursively if included file also has <F3:include>
			preg_replace_callback(
				$regex,
				function($attr) use($path) {
					$self=__CLASS__;
					// Load file
					return $self::embed($self::resolve($attr[1]),$path);
				},
				$text
			):
			$text;
	}

	/**
		Parse all directives and render HTML/XML template
			@return mixed
			@param $file string
			@param $mime string
			@param $path string
			@public
	**/
	static function serve($file,$mime='text/html',$path=NULL) {
		if (is_null($path))
			$path=self::fixslashes(self::$vars['GUI']);
		// Remove <F3::exclude> blocks
		$text=preg_replace(
			'/<(?:F3:)?exclude>.*?<\/(?:F3:)?exclude>/is','',
			// Link <F3:include> files
			self::embed($file,$path)
		);
		if (PHP_SAPI!='cli')
			// Send HTTP header with appropriate character set
			header(self::HTTP_Content.': '.$mime.'; '.
				'charset='.self::$vars['ENCODING']);
		if (!preg_match('/<.+>/s',$text))
			// Plain text
			return self::resolve($text);
		// Initialize XML tree
		$tree=new XMLtree('1.0',self::$vars['ENCODING']);
		// Suppress errors caused by invalid HTML structures
		$ishtml=preg_match('/text\/html|application\/xhtml+xml/',$mime);
		libxml_use_internal_errors($ishtml);
		// Populate XML tree
		if ($ishtml) {
			// HTML template; Keep track of existing tags so
			// those added by libxml can be removed later
			$tags=array(
				'!DOCTYPE\s+html','[\/]?html','[\/]?head','[\/]?body'
			);
			$undef=array();
			foreach ($tags as $tag) {
				$regex='/<'.$tag.'[^>]*>\h*\v*/is';
				if (!preg_match($regex,$text))
					$undef[]=$regex;
			}
			$tree->loadHTML($text);
		}
		else
			// XML template
			$tree->loadXML($text,LIBXML_COMPACT|LIBXML_NOERROR);
		// Prepare for XML tree traversal
		$tree->fragment=$tree->createDocumentFragment();
		$pass2=FALSE;
		$time=microtime(TRUE);
		$tree->traverse(
			function() use($tree,&$pass2) {
				$self=__CLASS__;
				$node=&$tree->nodeptr;
				$tag=$node->tagName;
				$parent=$node->parentNode;
				$next=$node;
				// Node removal flag
				$remove=FALSE;
				if ($tag=='repeat') {
					// Process <F3:repeat> directive
					$inner=$tree->innerHTML($node);
					if ($inner) {
						// Analyze attributes
						foreach ($node->attributes as $attr) {
							$val=$self::fixbraces($attr->value);
							preg_match(
								'/{?(@\w+(\[[^\]]+\]|\.\w+)*)}?/',$val,$cap);
							$name=$attr->name;
							if (!$cap[1] || isset($cap[2]) && $cap[2] &&
								$name!='group')
								// Invalid attribute
								break;
							$regex='/'.preg_quote($cap[1]).'\b/';
							if ($name=='key' || $name=='value')
								${$name[0].'reg'}=$regex;
							elseif ($name=='group') {
								$gcap=$cap[1];
								$gvar=$self::ref(substr($cap[1],1),FALSE);
							}
						}
						if (isset($gvar) && is_array($gvar) && count($gvar)) {
							ob_start();
							// Iterate thru group elements
							foreach (array_keys($gvar) as $key) {
								$kstr=var_export($key,TRUE);
								echo preg_replace($vreg,
									// Replace index token
									$gcap.'['.$kstr.']',
									isset($kreg)?
										// Replace key token
										preg_replace($kreg,$kstr,$inner):
										$inner
								);
							}
							$block=ob_get_clean();
							if (strlen($block)) {
								$tree->fragment->appendXML($block);
								// Insert fragment before current node
								$next=$parent->
									insertBefore($tree->fragment,$node);
							}
						}
					}
					$remove=TRUE;
				}
				elseif ($tag=='check' && !$pass2)
					// Found <F3:check> directive
					$pass2=TRUE;
				if ($remove) {
					// Find next node
					if ($node->isSameNode($next))
						$next=$node->nextSibling?
							$node->nextSibling:$parent;
					// Remove current node
					$parent->removeChild($node);
					// Replace with next node
					$node=$next;
				}
			}
		);
		if ($pass2) {
			// Template contains <F3:check> directive
			$tree->traverse(
				function() use($tree) {
					$self=__CLASS__;
					$node=&$tree->nodeptr;
					$tag=$node->tagName;
					$parent=$node->parentNode;
					// Process <F3:check> directive
					if ($tag=='check') {
						$val=var_export(
							(boolean)$self::resolve($self::fixbraces(
								$node->getAttribute('if'))),TRUE);
						ob_start();
						foreach ($node->childNodes as $child)
							if ($child->nodeType==XML_ELEMENT_NODE &&
								preg_match('/'.$val.'/i',$child->tagName))
									echo $tree->innerHTML($child)?:'';
						$block=ob_get_clean();
						if (strlen($block)) {
							$tree->fragment->appendXML($block);
							$parent->insertBefore($tree->fragment,$node);
						}
						// Remove current node
						$parent->removeChild($node);
						// Re-process parent node
						$node=$parent;
					}
				}
			);
		}
		return self::resolve(
			$ishtml?
				// Remove tags inserted by libxml
				preg_replace($undef,'',
					// Normalize empty HTML tags
					preg_replace(
						'/<((?:'.self::HTML_Tags.')\b.*?)\/?>/is','<\1/>',
						$tree->saveHTML()
					)
				):
				XMLdata::encode($tree->saveXML(),TRUE)
		);
	}

	/**
		Allow PHP and user-defined functions to be used in templates
			@param $str string
			@public
	**/
	static function allow($str='') {
		// Create lookup table of functions allowed in templates
		$legal=array();
		// Get list of all defined functions
		$dfuncs=get_defined_functions();
		foreach (explode('|',$str) as $ext) {
			$funcs=array();
			if (extension_loaded($ext))
				$funcs=get_extension_funcs($ext);
			elseif ($ext=='user')
				$funcs=$dfuncs['user'];
			$legal=array_merge($legal,$funcs);
		}
		// Remove prohibited functions
		$illegal='/^('.
			'apache_|call|chdir|env|escape|exec|extract|fclose|fflush|'.
			'fget|file_put|flock|fopen|fprint|fput|fread|fseek|fscanf|'.
			'fseek|fsockopen|fstat|ftell|ftp_|ftrunc|get|header|http_|'.
			'import|ini_|ldap_|link|log_|magic|mail|mcrypt_|mkdir|ob_|'.
			'php|popen|posix_|proc|rename|rmdir|rpc|set_|sleep|stream|'.
			'sys|thru|unreg'.
		')/i';
		$legal=array_merge(
			array_filter(
				$legal,
				function($func) use($illegal) {
					return !preg_match($illegal,$func);
				}
			),
			// PHP language constructs that may be used in expressions
			array('array','isset')
		);
		self::$funcs=array_map('strtolower',$legal);
	}

	/**
		Return TRUE if function can be used in templates
			@return boolean
			@param $func string
			@public
	**/
	static function allowed($func) {
		return in_array($func,self::$funcs);
	}

	/**
		Class initializer
			@public
	**/
	static function onload() {
		self::allow(self::FUNCS_Default);
	}

}

//! PHP DOMDocument extension
class XMLtree extends DOMDocument {

	public
		//! Default DOMDocument fragment
		$fragment,
		//! Current node pointer
		$nodeptr;

	/**
		Get inner HTML contents of node
			@return string
			@param $node DOMElement
			@public
	**/
	function innerHTML($node) {
		return preg_replace(
			'/^.+?>(.*)<.+?$/s','\1',
			$node->ownerDocument->saveXML($node)
		);
	}

	/**
		General-purpose pre-order XML tree traversal
			@param $pre mixed
			@param $type integer
			@public
	**/
	function traverse($pre) {
		// Start at document root
		$root=$this->documentElement;
		$node=&$this->nodeptr;
		$node=$root;
		$flag=FALSE;
		for (;;) {
			if (!$flag) {
				if ($node->nodeType==XML_ELEMENT_NODE)
					// Call pre-order handler
					call_user_func($pre);
				if ($node->firstChild) {
					// Descend to branch
					$node=$node->firstChild;
					continue;
				}
			}
			if ($node->isSameNode($root))
				// Root node reached; Exit loop
				break;
			// Post-order sequence
			if ($node->nextSibling) {
				// Stay on same level
				$flag=FALSE;
				$node=$node->nextSibling;
			}
			else {
				// Ascend to parent node
				$flag=TRUE;
				$node=$node->parentNode;
			}
		}
	}

	/**
		Class constructor
			@public
	**/
	function __construct() {
		// Default XMLTree settings
		$this->formatOutput=TRUE;
		$this->preserveWhiteSpace=TRUE;
		$this->strictErrorChecking=FALSE;
	}

}
