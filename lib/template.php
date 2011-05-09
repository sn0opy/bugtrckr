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
		TEXT_Render='Template %s cannot be rendered',
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
		if (!is_file($file)) {
			if (!self::$includes)
				trigger_error(sprintf(self::TEXT_Render,$file));
			return '';
		}
		if (in_array($file,self::$includes)) {
			trigger_error(sprintf(self::TEXT_Recursive,$file));
			return '';
		}
		self::$includes[]=$file;
		if (PHP_SAPI!='cli')
			// Send HTTP header with appropriate character set
			header(self::HTTP_Content.': '.$mime.'; '.
				'charset='.self::$vars['ENCODING']);
		$hash='php.'.self::hash($file);
		$cached=Cache::cached($hash);
		if ($cached && filemtime($file)<$cached)
			// Retrieve PHP-compiled template from cache
			$text=Cache::get($hash);
		else {
			// Parse raw template
			$doc=new F3markup;
			$text=$doc->load(file_get_contents($file));
			// Save PHP-compiled template to cache
			Cache::set($hash,$text);
		}
		ob_start();
		if (ini_get('allow_url_fopen') && ini_get('allow_url_include'))
			// Stream wrap
			require 'data:text/plain,'.urlencode($text);
		else {
			// Save PHP-equivalent file in temporary folder
			self::folder(self::$vars['TEMP']);
			$temp=self::$vars['TEMP'].$_SERVER['SERVER_NAME'].'.'.$hash;
			if (!$cached || !is_file($temp)) {
				// Create semaphore
				$hash='sem.'.self::hash($file);
				$cached=Cache::cached($hash);
				while ($cached)
					// Locked by another process
					usleep(mt_rand(0,1000));
				Cache::set($hash,TRUE);
				file_put_contents($temp,$text,LOCK_EX);
				// Remove semaphore
				Cache::clear($hash);
			}
			// Render
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
		TEXT_AttribMissing='Missing attribute: %s',
		TEXT_AttribInvalid='Invalid attribute: %s';
	//@}

	private
		//! Parsed markup string
		$tree=array(),
		//! Symbol table for repeat/loop blocks
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
		$syms=&$this->syms;
		return preg_replace_callback(
				'/{{(.+?)}}/s',
				function($expr) use(&$syms,$self,$echo) {
					$out=preg_replace_callback(
						'/(?!\w)@(\w+(?:\[[^\]]+\]|\.\w+)*(?:->\w+)?)'.
						'\h*(\([^\)]*\))?(?:\/\/(.+))?/',
						function($var) use(&$syms) {
							$self=__CLASS__;
							preg_match('/^(\w+)\b(.*)/',$var[1],$match);
							if (in_array($match[1],$syms))
								return '$_'.$self::remix($var[1]);
							$str='F3::get('.var_export($var[1],TRUE).
								(isset($var[2])?$var[2]:'').
								(isset($var[3])?(',array('.$var[3].')'):
								NULL).')';
							if (!$match[2]) {
								$syms[]=$match[1];
								$str='($_'.$match[1].'='.$str.')';
							}
							return $str;
						},
						$expr[1]
					);
					return $echo?('<?php echo '.$out.'; ?>'):$out;
				},
			$str
		);
	}

	/**
		Return TRUE if all mandatory attributes are present
			@return boolean
			@param $key string
			@param $tag array
			@param $attrs array
			@public
	**/
	function isdef($key,array $tag,array $attrs) {
		$ok=TRUE;
		foreach ($attrs as $attr)
			if (!isset($tag['@attrib'][$attr])) {
				$ok=FALSE;
				break;
			}
		if ($ok)
			return TRUE;
		$out='<'.$key;
		if (isset($tag['@attrib']))
			foreach ($tag['@attrib'] as $akey=>$aval)
				$out.=' '.$akey.'="'.htmlspecialchars($aval).'"';
		$out.='>';
		trigger_error(sprintf(self::TEXT_AttribMissing,$out));
		return FALSE;

	}

	/**
		Reassemble markup string and insert appropriate PHP code
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
							// <include> directive
							if (!$this->isdef($nkey,$nval,array('href')))
								return;
							$hvar=$nval['@attrib']['href'];
							$out.='<?php echo Template::serve('.
								var_export($hvar,TRUE).'); ?>';
							break;
						case 'loop':
							// <loop> directive
							if (!$this->isdef($nkey,$nval,
								array('counter','from','to')))
								return;
							$cvar=preg_replace('/{{@(.+?)}}/','\1',
								$nval['@attrib']['counter']);
							foreach ($nval['@attrib'] as $akey=>$aval) {
								${$akey[0].'att'}=$aval;
								${$akey[0].'str'}=$this->expr($aval);
								// Syntax check
								if (${$akey[0].'str'}==$aval) {
									trigger_error(sprintf(
										self::TEXT_AttribInvalid,
										$akey.'="'.addslashes($aval).'"'));
									return;
								}
							}
							unset($nval['@attrib']);
							$this->syms[]=$cvar;
							$out.='<?php for ('.
								'$_'.$cvar.'='.(float)$fstr.';'.
								'$_'.$cvar.'<'.(float)$tstr.';'.
								'$_'.$cvar.'+='.(float)
									(isset($satt)?$sstr:'1').'): ?>'.
								$this->build($nval).
								'<?php endfor; ?>';
							unset($this->syms
								[array_search($cvar,$this->syms)]);
							break;
						case 'repeat':
							// <repeat> directive
							if (!$this->isdef($nkey,$nval,
								array('group','value')))
								return;
							$gval=$nval['@attrib']['group'];
							$gstr=$this->expr($gval);
							// Syntax check
							if ($gstr==$gval) {
								trigger_error(sprintf(
									self::TEXT_AttribInvalid,
									'group="'.addslashes($gval).'"'));
								return;
							}
							unset($nval['@attrib']['group']);
							foreach ($nval['@attrib'] as $akey=>$aval) {
								${$akey[0].'var'}=
									preg_replace('/{{@(.+?)}}/','\1',$aval);
								// Syntax check
								if (${$akey[0].'var'}==$aval) {
									trigger_error(sprintf(
										self::TEXT_AttribInvalid,
										$akey.'="'.addslashes($aval).'"'));
									return;
								}
							}
							unset($nval['@attrib']);
							if (isset($kvar))
								$this->syms[]=$kvar;
							$this->syms[]=$vvar;
							$out.='<?php foreach (('.$gstr.'?:array()) as '.
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
							// <check> directive
							if (!$this->isdef($nkey,$nval,array('if')))
								return;
							$ival=$nval['@attrib']['if'];
							$cond=$this->expr($ival);
							// Syntax check
							if ($cond==$ival) {
								trigger_error(sprintf(
									self::TEXT_AttribInvalid,
									'if="'.addslashes($ival).'"'));
								return;
							}
							$out.='<?php if ('.$cond.'): ?>'.
								$this->build($nval).
								'<?php endif; ?>';
							break;
						case 'true':
							// <true> block of <check> directive
							$out.=$this->build($nval);
							break;
						case 'false':
							// <false> block of <check> directive
							$out.='<?php else: ?>'.$this->build($nval);
							break;
					}
		}
		else
			$out.=preg_match('/<\?php/',$node)?$node:$this->expr($node,TRUE);
		return $out;
	}

	/**
		Load markup from string
			@return string
			@param $text string
			@public
	**/
	function load($text) {
		// Remove PHP code and alternative exclude-tokens
		$text=preg_replace(
			'/<\?(?:php)?.+?\?>|{{\*.+?\*}}/is','',trim($text));
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
				'(?:F3+:)?(include|exclude|loop|repeat|check|true|false)\b'.
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
							'\s*(?:"(.+?)"|\'(.+?)\')/s',$match[3],$attr,
							PREG_SET_ORDER);
						foreach ($attr as $kv)
							$node['@attrib'][$kv[1]]=$kv[2]?:$kv[3];
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
		$this->syms=explode('|',self::PHP_Globals);
	}

}
