<?php

/**
	Web pack for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3::Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package Expansion
		@version 2.0.0
**/

//! Web pack
class Web extends Base {

	//@{ Locale-specific error/exception messages
	const
		TEXT_Minify='Unable to minify {@CONTEXT}';
	//@}

	/**
		Return translation table for Latin diacritics and 7-bit equivalents
			@return array
			@public
	**/
	static function diacritics() {
		return array(
			'À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Å'=>'A','Ä'=>'AE','Æ'=>'AE',
			'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','å'=>'a','ä'=>'ae','æ'=>'ae',
			'Þ'=>'B','þ'=>'b','Č'=>'C','Ć'=>'C','Ç'=>'C','č'=>'c','ć'=>'c',
			'ç'=>'c','ð'=>'d','Đ'=>'Dj','đ'=>'dj','È'=>'E','É'=>'E','Ê'=>'E',
			'Ë'=>'E','è'=>'e','é'=>'e','ê'=>'e','ë'=>'e','Ì'=>'I','Í'=>'I',
			'Î'=>'I','Ï'=>'I','ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','Ñ'=>'N',
			'ñ'=>'n','Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ø'=>'O','Ö'=>'OE',
			'Œ'=>'OE','ð'=>'o','ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'oe',
			'œ'=>'oe','ø'=>'o','Ŕ'=>'R','ŕ'=>'r','Š'=>'S','š'=>'s','ß'=>'ss',
			'Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'UE','ù'=>'u','ú'=>'u','û'=>'u',
			'ü'=>'ue','Ý'=>'Y','ý'=>'y','ý'=>'y','ÿ'=>'yu','Ž'=>'Z','ž'=>'z'
		);
	}

	/**
		Return an RFC 1738-compliant URL-friendly version of string
			@return string
			@param $text string
			@param $maxlen integer
	**/
	static function slug($text,$maxlen=0) {
		$out=preg_replace('/[^\w\.!~\*\'"(),]+/','-',
			trim(strtr($text,self::diacritics())));
		return $maxlen?substr($out,0,$maxlen):$out;
	}

	/**
		Strip Javascript/CSS files of extraneous whitespaces and comments;
		Return combined output as a minified string
			@param $base string
			@param $files array
			@public
	**/
	static function minify($base,array $files) {
		preg_match('/\.(js|css)$/',$files[0],$ext);
		if (!$ext[1]) {
			// Not a JavaSript/CSS file
			error(404);
			return;
		}
		$mime=array(
			'js'=>'application/x-javascript',
			'css'=>'text/css'
		);
		$path=self::fixslashes(self::$vars['GUI'].$base);
		foreach ($files as $file)
			if (!is_file($path.$file)) {
				self::$vars['CONTEXT']=$file;
				trigger_error(self::TEXT_Minify);
				return;
			}
		$src='';
		if (PHP_SAPI!='cli')
			header(self::HTTP_Content.': '.$mime[$ext[1]].'; '.
				'charset='.self::$vars['ENCODING']);
		foreach ($files as $file) {
			$stats=&self::ref('STATS');
			$stats['FILES']['minified']
				[basename($file)]=filesize($path.$file);
			// Rewrite relative URLs in CSS
			$src.=preg_replace_callback(
				'/\b(?<=url)\(([\"\'])*([^\1]+?)\1*\)/',
				function($url) use($path,$file) {
					$fdir=dirname($file);
					$rewrite=explode(
						'/',$path.($fdir!='.'?$fdir.'/':'').$url[2]
					);
					$i=0;
					while ($i<count($rewrite))
						// Analyze each URL segment
						if ($i>0 &&
							$rewrite[$i]=='..' &&
							$rewrite[$i-1]!='..') {
							// Simplify URL
							unset($rewrite[$i],$rewrite[$i-1]);
							$rewrite=array_values($rewrite);
							$i--;
						}
						else
							$i++;
					// Reconstruct simplified URL
					return
						'('.implode('/',array_merge($rewrite,array())).')';
				},
				// Retrieve CSS/Javascript file
				file_get_contents($path.$file)
			);
		}
		$ptr=0;
		$dst='';
		while ($ptr<strlen($src)) {
			if ($src[$ptr]=='/') {
				// Presume it's a regex pattern
				$regex=TRUE;
				if ($ptr>0) {
					// Backtrack and validate
					$ofs=$ptr;
					while ($ofs>0) {
						$ofs--;
					// Pattern should be preceded by parenthesis,
					// colon or assignment operator
					if ($src[$ofs]=='(' || $src[$ofs]==':' ||
						$src[$ofs]=='=') {
							while ($ptr<strlen($src)) {
								$str=strstr(substr($src,$ptr+1),'/',TRUE);
								if (!strlen($str) && $src[$ptr-1]!='/' ||
									strpos($str,"\n")!==FALSE) {
									// Not a regex pattern
									$regex=FALSE;
									break;
								}
								$dst.='/'.$str;
								$ptr+=strlen($str)+1;
								if ($src[$ptr-1]!='\\' ||
									$src[$ptr-2]=='\\') {
										$dst.='/';
										$ptr++;
										break;
								}
							}
							break;
						}
						elseif ($src[$ofs]!="\t" && $src[$ofs]!=' ') {
							// Not a regex pattern
							$regex=FALSE;
							break;
						}
					}
					if ($regex && $ofs<1)
						$regex=FALSE;
				}
				if (!$regex || $ptr<1) {
					if (substr($src,$ptr+1,2)=='*@') {
						// Conditional block
						$str=strstr(substr($src,$ptr+3),'@*/',TRUE);
						$dst.='/*@'.$str.$src[$ptr].'@*/';
						$ptr+=strlen($str)+6;
					}
					elseif ($src[$ptr+1]=='*') {
						// Multiline comment
						$str=strstr(substr($src,$ptr+2),'*/',TRUE);
						$ptr+=strlen($str)+4;
					}
					elseif ($src[$ptr+1]=='/') {
						// Single-line comment
						$str=strstr(substr($src,$ptr+2),"\n",TRUE);
						$ptr+=strlen($str)+2;
					}
					else {
						// Division operator
						$dst.=$src[$ptr];
						$ptr++;
					}
				}
				continue;
			}
			if ($src[$ptr]=='\'' || $src[$ptr]=='"') {
				$match=$src[$ptr];
				// String literal
				while ($ptr<strlen($src)) {
					$str=strstr(substr($src,$ptr+1),$src[$ptr],TRUE);
					$dst.=$match.$str;
					$ptr+=strlen($str)+1;
					if ($src[$ptr-1]!='\\' || $src[$ptr-2]=='\\') {
						$dst.=$match;
						$ptr++;
						break;
					}
				}
				continue;
			}
			if (ctype_space($src[$ptr])) {
				$last=substr($dst,-1);
				$ofs=$ptr+1;
				if ($ofs+1<strlen($src)) {
					while (ctype_space($src[$ofs]))
						$ofs++;
					if (preg_match('/\w[\w'.
						// IE is sensitive about certain spaces in CSS
						($ext[1]=='css'?'#\-*\.':'').'$]/',$last.$src[$ofs]))
							$dst.=$src[$ptr];
				}
				$ptr=$ofs;
			}
			else {
				$dst.=$src[$ptr];
				$ptr++;
			}
		}
		echo $dst;
	}

	/**
		Convert seconds to frequency (in words)
			@return integer
			@param $secs string
			@public
	**/
	static function frequency($secs) {
		$freq['hourly']=3600;
		$freq['daily']=86400;
		$freq['weekly']=604800;
		$freq['monthly']=2592000;
		foreach ($freq as $key=>$val)
			if ($secs<=$val)
				return $key;
		return 'yearly';
	}

	/**
		Parse each URL recursively and generate sitemap
			@param $url string
			@public
	**/
	static function sitemap($url='/') {
		if (isset(self::$vars['SITEMAP'][$url]) &&
			!is_null(self::$vars['SITEMAP'][$url]['status']))
			// Already crawled
			return;
		preg_match('/^http[s]*:\/\/([^\/$]+)/',$url,$host);
		if (!empty($host) && $host[1]!=$_SERVER['SERVER_NAME']) {
			// Remote URL
			self::$vars['SITEMAP'][$url]['status']=FALSE;
			return;
		}
		$state=self::$vars['QUIET'];
		self::$vars['QUIET']=TRUE;
		self::mock('GET '.$url);
		self::run();
		// Check if an error occurred or no HTTP response
		if (self::$vars['ERROR'] || !self::$vars['RESPONSE']) {
			self::$vars['SITEMAP'][$url]['status']=FALSE;
			// Reset error flag for next page
			self::$vars['ERROR']=NULL;
			return;
		}
		$doc=new domdocument('1.0',self::$vars['ENCODING']);
		// Suppress errors caused by invalid HTML structures
		libxml_use_internal_errors(TRUE);
		if ($doc->loadHTML(self::$vars['RESPONSE'])) {
			// Valid HTML; add to sitemap
			if (!self::$vars['SITEMAP'][$url]['level'])
				// Web root
				self::$vars['SITEMAP'][$url]['level']=0;
			self::$vars['SITEMAP'][$url]['status']=TRUE;
			self::$vars['SITEMAP'][$url]['mod']=time();
			self::$vars['SITEMAP'][$url]['freq']=0;
			// Cached page
			$hash='url.'.hash('GET '.$url);
			$cached=cache\cached($hash);
			if ($cached) {
				self::$vars['SITEMAP'][$url]['mod']=$cached['time'];
				self::$vars['SITEMAP'][$url]['freq']=$_SERVER['REQUEST_TTL'];
			}
			// Parse all links
			$links=$doc->getElementsByTagName('a');
			foreach ($links as $link) {
				$ref=$link->getAttribute('href');
				$rel=$link->getAttribute('rel');
				if (!$ref || $rel && preg_match('/nofollow/',$rel))
					// Don't crawl this link!
					continue;
				if (!isset(self::$vars['SITEMAP'][$ref]))
					self::$vars['SITEMAP'][$ref]=array(
						'level'=>self::$vars['SITEMAP'][$url]['level']+1,
						'status'=>NULL
					);
			}
			// Parse each link
			array_walk(array_keys(self::$vars['SITEMAP']),'self::sitemap');
		}
		unset($doc);
		if (!self::$vars['SITEMAP'][$url]['level']) {
			// Finalize sitemap
			$depth=1;
			while ($ref=current(self::$vars['SITEMAP']))
				// Find deepest level while iterating
				if (!$ref['status'])
					// Remove remote URLs and pages with errors
					unset(self::$vars['SITEMAP']
						[key(self::$vars['SITEMAP'])]);
				else {
					$depth=max($depth,$ref['level']+1);
					next(self::$vars['SITEMAP']);
				}
			// Create XML document
			$xml=simplexml_load_string(
				'<?xml version="1.0" encoding="'.
					self::$vars['ENCODING'].'"?>'.
				'<urlset xmlns="'.
					'http://www.sitemaps.org/schemas/sitemap/0.9'.
				'"/>'
			);
			$host='http://'.$_SERVER['SERVER_NAME'];
			foreach (self::$vars['SITEMAP'] as $key=>$ref) {
				// Add new URL
				$item=$xml->addChild('url');
				// Add URL elements
				$item->addChild('loc',$host.$key);
				$item->addChild('lastMod',gmdate('c',$ref['mod']));
				$item->addChild('changefreq',
					self::frequency($ref['freq']));
				$item->addChild('priority',
					sprintf('%1.1f',1-$ref['level']/$depth));
			}
			// Send output
			self::$vars['QUIET']=$state;
			if (PHP_SAPI!='cli')
				header(self::HTTP_Content.': application/xml; '.
					'charset='.self::$vars['ENCODING']);
			$xml=dom_import_simplexml($xml)->ownerDocument;
			$xml->formatOutput=TRUE;
			echo $xml->saveXML();
		}
	}

	/**
		Class initializer
			@public
	**/
	static function onload() {
		// Site structure
		$vars['SITEMAP']=NULL;
	}

}
