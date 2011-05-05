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

	//@{ Locale-specific error/exception messages
	const
		TEXT_Recursive='Recursive <include %s> detected';
	//@}

	private static
		//! Tracker for included files
		$includes=array();

	/**
		Render template
			@return string
			@param $file string
			@param $mime string
			@public
	**/
	static function serve($file,$mime='text/html') {
		$file=self::resolve(self::fixslashes(self::$vars['GUI']).$file);
		if (!is_file($file))
			return '';
		if (in_array($file,self::$includes)) {
			trigger_error(sprintf(self::TEXT_Recursive,$file));
			return '';
		}
		self::$includes[]=$file;
		if (PHP_SAPI!='cli')
			// Send HTTP header with appropriate character set
			header(self::HTTP_Content.': '.$mime.'; '.
				'charset='.self::$vars['ENCODING']);
		$doc=new F3markup;
		$text=$doc->file($file);
		ob_start();
		if (ini_get('allow_url_include'))
			require 'data:text/plain,'.urlencode($text);
		else {
			self::folder(self::$vars['TEMP']);
			$temp=tempnam(self::$vars['TEMP'],
				$_SERVER['SERVER_NAME'].'.tpl.');
			file_put_contents($temp,$text);
			require $temp;
		}
		$out=ob_get_clean();
		unset(self::$includes[array_search($file,self::$includes)]);
		return $out;
	}

}

//! Markup loader/parser/builder
class F3markup extends Base {

	//@{ Locale-specific error/exception messages
	const
		TEXT_Attribute='Missing attribute in <%s> directive';
	//@}

	private
		//! Parsed markup string
		$tree=array(),
		//! Symbol table for <repeat> variables
		$syms=array();

	/**
		Return stringified framework variable
			@return string
			@param $str string
			@param $echo boolean
			@public
	**/
	function expr($str,$echo=FALSE) {
		$self=$this;
		$syms=$this->syms;
		return preg_replace_callback(
				'/{{(.+?)}}/',
				function($expr) use($self,$syms,$echo) {
					$out=preg_replace_callback(
						'/(?!\w)@(\w+(?:\[[^\]]+\]|\.\w+)*(?:->\w+)?'.
						'\h*(?:\(([^\)]*?)\))?)/',
						function($var) use($syms) {
							$self=__CLASS__;
							if ($syms)
								foreach ($syms as $sym)
									if (preg_match('/^'.preg_quote($sym).'\b/',
										$mix=$self::remix($var[1])))
										return '$_'.$mix;
							return 'F3::get('.var_export($var[1],TRUE).')';
						},
						$expr[1]
					);
					return $echo?('<?php echo '.$out.'; ?>'):$out;
				},
			$str
		);
	}

	/**
		Build markup string
			@return string
			@param $node mixed
			@public
	**/
	function build($node) {
		$out='';
		if (is_array($node)) {
			foreach ($node as $nkey=>$nval)
				if (is_int($nkey))
					$out.=$this->build($nval);
				else
					switch ($nkey) {
						case 'include':
							if (!isset($nval['@attrib']['href'])) {
								trigger_error(sprintf(
									self::TEXT_Attribute,$nkey));
								return;
							}
							$hvar=$nval['@attrib']['href'];
							$out.='<?php echo Template::serve('.
								var_export($hvar,TRUE).'); ?>';
							break;
						case 'repeat':
							if (!isset($nval['@attrib']['group']) ||
								!isset($nval['@attrib']['value'])) {
								trigger_error(sprintf(
									self::TEXT_Attribute,$nkey));
								return;
							}
							foreach ($nval['@attrib'] as $akey=>$aval) {
								${$akey[0].'att'}=$aval;
								${$akey[0].'var'}=
									preg_replace('/{{@(.+?)}}/','\1',$aval);
								${$akey[0].'str'}=
									var_export(${$akey[0].'var'},TRUE);
							}
							unset($nval['@attrib']);
							if (isset($kvar))
								$this->syms[]=$kvar;
							$this->syms[]=$vvar;
							$found=FALSE;
							$out.='<?php foreach ('.
								($found?('$_'.$gvar):
								('('.$this->expr($gatt).')')).'?:array() as '.
								(isset($kvar)?('$_'.$kvar.'=>'):'').
									'$_'.$vvar.'): ?>'.
								$this->build($nval).
								'<?php endforeach; ?>';
							if (isset($kvar))
								unset($this->syms
									[array_search($kvar,$this->syms)]);
							unset($this->syms
								[array_search($vvar,$this->syms)]);
							break;
						case 'check':
							if (!isset($nval['@attrib']['if'])) {
								trigger_error(sprintf(
									self::TEXT_Attribute,$nkey));
								return;
							}
							$ivar=$nval['@attrib']['if'];
							$out.='<?php if ('.$this->expr($ivar).'): ?>'.
								$this->build($nval).
								'<?php endif; ?>';
							break;
						case 'true':
							$out.=$this->build($nval);
							break;
						case 'false':
							$out.='<?php else: ?>'.$this->build($nval);
							break;
					}
		}
		else
			$out.=preg_match('/<\?php/',$node)?$node:$this->expr($node,TRUE);
		return $out;
	}

	/**
		Load markup from file
			@return string
			@param $file string
			@public
	**/
	function file($file) {
		$hash='tpl.'.self::hash($file);
		$cached=Cache::cached($hash);
		if ($cached && filemtime($file)<$cached)
			// Retrieve from cache
			return Cache::get($hash);
		$out=$this->load(file_get_contents($file));
		Cache::set($hash,$out);
		return $out;
	}

	/**
		Load markup from string
			@return string
			@param $text string
			@public
	**/
	function load($text) {
		// Remove PHP tags and alternative exclude-tokens
		$text=preg_replace('/<\?php.+?\?>|{{\*.+?\*}}/s','',trim($text));
		// Define root node
		$node=&$this->tree;
		// Define stack and depth variables
		$stack=array();
		$depth=0;
		// Define string parser variables
		$len=strlen($text);
		$ptr=0;
		$temp='';
		while ($ptr<$len) {
			if (preg_match('/^<(\/?)'.
				'(?:F3+:)?(include|exclude|repeat|check|true|false)\b'.
				'(.*?)(\/?)>/is',substr($text,$ptr),$match)) {
				if (strlen($temp))
					$node[]=$temp;
				// Element node
				if ($match[1]) {
					// Find matching start tag
					$save=$depth;
					$found=FALSE;
					while ($depth>0) {
						$depth--;
						foreach ($stack[$depth] as $item)
							if (is_array($item) && isset($item[$match[2]])) {
								// Start tag found
								$found=TRUE;
								break 2;
							}
					}
					if (!$found)
						// Unbalanced tag
						$depth=$save;
					$node=&$stack[$depth];
				}
				else {
					// Start tag
					$stack[$depth]=&$node;
					$node=&$node[][$match[2]];
					if ($match[3]) {
						// Process attributes
						preg_match_all('/\s+(\w+\b)\s*='.
							'\s*([\'"])([^\'"]+)\2/s',$match[3],$attr,
							PREG_SET_ORDER);
						foreach ($attr as $kv)
							$node['@attrib'][$kv[1]]=$kv[3];
					}
					if ($match[4])
						// Empty tag
						$node=&$stack[$depth];
					else
						$depth++;
				}
				$temp='';
				$ptr+=strlen($match[0]);
			}
			else {
				// Text node
				$temp.=$text[$ptr];
				$ptr++;
			}
		}
		if (strlen($temp))
			$node[]=$temp;
		unset($node);
		unset($stack);
		return $this->build($this->tree);
	}

	/**
		Override base constructor
			@public
	**/
	function __construct() {
	}

}
