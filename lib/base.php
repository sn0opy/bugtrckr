<?php

//! Base structure
class Base {

	//@{ Framework details
	const
		TEXT_AppName='Fat-Free Framework',
		TEXT_Version='2.0.0-b1';
	//@}

	//@{ Locale-specific error/exception messages
	const
		TEXT_Illegal='{@CONTEXT} is not a valid framework variable name',
		TEXT_Config='The configuration file {@CONTEXT} was not found',
		TEXT_Section='{@CONTEXT} is not a valid section',
		TEXT_MSet='Invalid multi-variable assignment',
		TEXT_NotArray='{@CONTEXT} is not an array',
		TEXT_PHPExt='PHP extension {@CONTEXT} is not enabled',
		TEXT_Apache='Apache mod_rewrite module is not enabled',
		TEXT_Object='{@CONTEXT} cannot be used in object context',
		TEXT_Class='Undefined class {@CONTEXT}',
		TEXT_Method='Undefined method {@CONTEXT}',
		TEXT_Conflict='{@CONTEXT} conflicts with framework method name',
		TEXT_NotFound='The requested URL {@CONTEXT} was not found',
		TEXT_Handler='The route handler {@CONTEXT} is invalid',
		TEXT_NoRoutes='No routes specified',
		TEXT_HTTP='HTTP status code {@CONTEXT} is invalid',
		TEXT_Render='Unable to render {@CONTEXT} - file does not exist',
		TEXT_Form='The input handler for {@CONTEXT} is invalid',
		TEXT_Static='{@CONTEXT} must be a static method';
	//@}

	//@{ HTTP status codes (RFC 2616)
	const
		HTTP_100='Continue',
		HTTP_101='Switching Protocols',
		HTTP_200='OK',
		HTTP_201='Created',
		HTTP_202='Accepted',
		HTTP_203='Non-Authorative Information',
		HTTP_204='No Content',
		HTTP_205='Reset Content',
		HTTP_206='Partial Content',
		HTTP_300='Multiple Choices',
		HTTP_301='Moved Permanently',
		HTTP_302='Found',
		HTTP_303='See Other',
		HTTP_304='Not Modified',
		HTTP_305='Use Proxy',
		HTTP_306='Temporary Redirect',
		HTTP_400='Bad Request',
		HTTP_401='Unauthorized',
		HTTP_402='Payment Required',
		HTTP_403='Forbidden',
		HTTP_404='Not Found',
		HTTP_405='Method Not Allowed',
		HTTP_406='Not Acceptable',
		HTTP_407='Proxy Authentication Required',
		HTTP_408='Request Timeout',
		HTTP_409='Conflict',
		HTTP_410='Gone',
		HTTP_411='Length Required',
		HTTP_412='Precondition Failed',
		HTTP_413='Request Entity Too Large',
		HTTP_414='Request-URI Too Long',
		HTTP_415='Unsupported Media Type',
		HTTP_416='Requested Range Not Satisfiable',
		HTTP_417='Expectation Failed',
		HTTP_500='Internal Server Error',
		HTTP_501='Not Implemented',
		HTTP_502='Bad Gateway',
		HTTP_503='Service Unavailable',
		HTTP_504='Gateway Timeout',
		HTTP_505='HTTP Version Not Supported';
	//@}

	//@{ HTTP headers (RFC 2616)
	const
		HTTP_AcceptEnc='Accept-Encoding',
		HTTP_Agent='User-Agent',
		HTTP_Cache='Cache-Control',
		HTTP_Connect='Connection',
		HTTP_Content='Content-Type',
		HTTP_Disposition='Content-Disposition',
		HTTP_Encoding='Content-Encoding',
		HTTP_Expires='Expires',
		HTTP_Host='Host',
		HTTP_IfMod='If-Modified-Since',
		HTTP_Keep='Keep-Alive',
		HTTP_LastMod='Last-Modified',
		HTTP_Length='Content-Length',
		HTTP_Location='Location',
		HTTP_Partial='Accept-Ranges',
		HTTP_Powered='X-Powered-By',
		HTTP_Pragma='Pragma',
		HTTP_Referer='Referer',
		HTTP_Transfer='Content-Transfer-Encoding',
		HTTP_WebAuth='WWW-Authenticate';
	//@}

	//@{ Framework array variable sort options
	const
		SORT_Asc=1,
		SORT_Desc=-1;
	//@}

	const
		//! Framework-mapped PHP globals
		PHP_Globals='GET|POST|COOKIE|REQUEST|SESSION|FILES|SERVER|ENV',
		//! HTTP methods for RESTful interface
		HTTP_Methods='GET|HEAD|POST|PUT|DELETE|OPTIONS';

	protected static
		//! Global variables
		$vars,
		//! NULL reference
		$null;

	/**
		Convert Windows double-backslashes to slashes; Also for
		referencing namespaced classes in subdirectories
			@return string
			@param $str string
			@public
	**/
	static function fixslashes($str) {
		return $str?strtr($str,'\\','/'):$str;
	}

	/**
		Convert PHP expression/value to string
			@return string
			@param $val mixed
			@public
	**/
	static function stringify($val) {
		return preg_replace('/\s+=>\s+/','=>',var_export(
			is_object($val) && !method_exists($val,'__set_state')?
				(method_exists($val,'__toString')?
					(string)$val:get_class($val)):$val,TRUE));
	}

	/**
		Flatten array values and return as CSV string
			@return string
			@param $args mixed
			@public
	**/
	static function csv($args) {
		if (!is_array($args))
			$args=array($args);
		$str='';
		foreach ($args as $key=>$val) {
			$str.=($str?',':'');
			if (is_string($key))
				$str.=var_export($key,TRUE).'=>';
			$str.=is_array($val)?
				('array('.self::csv($val).')'):self::stringify($val);
		}
		return $str;
	}

	/**
		Generate Base36/CRC32 hash code
			@return string
			@param $str string
			@public
	**/
	static function hash($str) {
		return str_pad(base_convert(
			sprintf('%u',crc32($str)),10,36),7,'0',STR_PAD_LEFT);
	}

	/**
		Convert hexadecimal to binary-packed data
			@return string
			@param $hex string
			@public
	**/
	static function hexbin($hex) {
		return pack('H*',$hex);
	}

	/**
		Convert binary-packed data to hexadecimal
			@return string
			@param $bin string
			@public
	**/
	static function binhex($bin) {
		return implode('',unpack('H*',$bin));
	}

	/**
		Returns -1 if the specified number is negative, 0 if zero, or 1 if
		the number is positive
			@return integer
			@param $num mixed
			@public
	**/
	static function sign($num) {
		return $num?$num/abs($num):0;
	}

	/**
		Convert engineering-notated string to bytes
			@return integer
			@param $str string
			@public
	**/
	static function bytes($str) {
		$greek='KMGT';
		$exp=strpbrk($str,$greek);
		return pow(1024,strpos($greek,$exp)+1)*(int)$str;
	}

	/**
		Convert from JS dot notation to PHP array notation
			@return string
			@param $key string
			@public
	**/
	static function remix($key) {
		$out='';
		foreach (preg_split('/\[\h*[\'"]?|[\'"]?\h*\]|\./',
			$key,NULL,PREG_SPLIT_NO_EMPTY) as $fix) {
			if ($out)
				$fix='['.var_export($fix,TRUE).']';
			$out.=$fix;
		}
		return $out;
	}

	/**
		Return TRUE if specified string is a valid framework variable name
			@return boolean
			@param $key string
			@public
	**/
	static function valid($key) {
		if (preg_match('/^\w+(?:\[[^\]]+\]|\.\w+)*$/',$key))
			return TRUE;
		// Invalid variable name
		self::$vars['CONTEXT']=var_export($key,TRUE);
		trigger_error(self::TEXT_Illegal);
		return FALSE;
	}

	/**
		Get framework variable reference/contents
			@return mixed
			@param $key string
			@param $set boolean
			@public
	**/
	static function &ref($key,$set=TRUE) {
		// Traverse array
		$matches=preg_split('/\[\h*[\'"]?|[\'"]?\h*\]|\./',
			$key,NULL,PREG_SPLIT_NO_EMPTY);
		if ($set)
			$var=&self::$vars;
		else
			$var=self::$vars;
		// Grab the specified array element
		foreach ($matches as $match) {
			if ($set) {
				if (!is_array($var))
					// Create element
					$var=array();
				$var=&$var[$match];
			}
			elseif (is_array($var) && isset($var[$match]))
				$var=$var[$match];
			else
				return self::$null;
		}
		return $var;
	}

	/**
		Simple token substitution
			@return string
			@param $val mixed
			@public
	**/
	static function subst($val) {
		return preg_replace_callback(
			'/{@(\w+(?:\[[^\]]+\]|\.\w+)*)(?:->(\w+))?}/',
			function($var) {
				$self=__CLASS__;
				// Retrieve variable contents
				$val=$self::ref($var[1],FALSE);
				if (isset($var[2]) && is_object($val))
					// Use object property
					$val=$val->$var[2];
				if (is_string($val) && preg_match('/{.+}/',$val))
					// Variable variable? Call recursively
					$val=$self::subst($val);
				// Check syntax before replacing contents
				return eval('return (string)'.
					$self::stringify($val).';');
			},
			(string)$val
		);
	}

	/**
		Return TRUE if IP address is local or within a private IPv4 range
			@return boolean
			@param $addr string
			@public
	**/
	static function privateip($addr) {
		return preg_match('/^127\.0\.0\.\d{1,3}$/',$addr) ||
			!filter_var($addr,FILTER_VALIDATE_IP,
				FILTER_FLAG_IPV4|FILTER_FLAG_NO_PRIV_RANGE);
	}

	/**
		Sniff headers for real IP address
			@return string
			@public
	**/
	static function realip() {
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			// Behind proxy
			return $_SERVER['HTTP_CLIENT_IP'];
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// Use first IP address in list
			list($ip)=explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
			return $ip;
		}
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
		Intercept calls to undefined methods
			@param $func string
			@param $args array
			@public
	**/
	function __call($func,array $args) {
		self::$vars['CONTEXT']=get_called_class().'->'.
			$func.'('.self::csv($args).')';
		trigger_error(self::TEXT_Method);
	}

	/**
		Intercept calls to undefined static methods
			@param $func string
			@param $args array
			@public
	**/
	static function __callStatic($func,array $args) {
		self::$vars['CONTEXT']=get_called_class().'::'.
			$func.'('.self::csv($args).')';
		trigger_error(self::TEXT_Method);
	}

	/**
		Class constructor
			@public
	**/
	function __construct() {
		// Prohibit use of class as an object
		self::$vars['CONTEXT']=get_called_class();
		trigger_error(self::TEXT_Object);
	}

}

//! Main framework code
class F3 extends Base {

	/**
		Bind value to framework variable
			@param $key string
			@param $val mixed
			@param $persist boolean
			@param $conv boolean
			@public
	**/
	static function set($key,$val,$persist=FALSE,$conv=TRUE) {
		if (preg_match('/{.+}/',$key))
			// Variable variable
			$key=self::subst($key);
		if (!self::valid($key))
			return;
		// Referencing a SESSION variable element auto-starts a session
		if (preg_match('/^SESSION\b/',$key) && !session_id()) {
			session_start();
			// Sync framework and PHP global
			self::$vars['SESSION']=&$_SESSION;
		}
		$name=self::remix($key);
		$var=&self::ref($name);
		if (is_array($val)) {
			$var=array();
			// Recursive token substitution
			foreach ($val as $subk=>$subv)
				self::set($key.'['.var_export($subk,TRUE).']',
					$subv,FALSE,$conv);
			return;
		}
		elseif (is_string($val)) {
			$val=self::subst($val);
			if (!preg_match('/^[A-Z]+\b/',$key) && $conv)
				// Userland variable; Convert to HTML entities
				$val=htmlspecialchars($val,
					ENT_COMPAT,self::$vars['ENCODING'],FALSE);
		}
		$var=$val;
		if (preg_match('/LANGUAGE|LOCALES/',$key) &&
			extension_loaded('intl')) {
			// Determine language
			if (!self::$vars['LANGUAGE'])
				// Auto-detect
				self::$vars['LANGUAGE']=
					isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?
						Locale::acceptFromHTTP(
							$_SERVER['HTTP_ACCEPT_LANGUAGE']):
						Locale::getDefault();
			Locale::setDefault(self::$vars['LANGUAGE']);
			// Build up language list; add English as fallback
			$list=array($def=Locale::getDefault(),
				Locale::getPrimaryLanguage($def),'en');
			foreach (array_reverse(array_unique($list)) as $language) {
				$file=self::fixslashes(self::$vars['LOCALES']).
					$language.'.php';
				if (is_file($file) && ($trans=require_once $file) &&
					is_array($trans))
					// Combine dictionaries and assign key/value pairs
					self::mset($trans);
			}
		}
		elseif ($key=='PREFIX')
			self::alias($val);
		// Initialize cache if explicitly defined
		elseif ($key=='CACHE' && !is_bool($val))
			Cache::prep();
		if ($persist) {
			$hash='var.'.self::hash($name);
			Cache::set($hash,$val);
		}
	}

	/**
		Retrieve value of framework variable and apply locale rules
			@return mixed
			@param $key string
			@param $args mixed
			@public
	**/
	static function get($key,$args=NULL) {
		if (preg_match('/{.+}/',$key))
			// Variable variable
			$key=self::subst($key);
		if (!self::valid($key))
			return self::$null;
		// Referencing a SESSION variable element auto-starts a session
		if (preg_match('/^SESSION\b/',$key) && !session_id()) {
			session_start();
			// Sync framework and PHP global
			self::$vars['SESSION']=&$_SESSION;
		}
		$name=self::remix($key);
		$val=self::ref($name,FALSE);
		if (is_string($val) && extension_loaded('intl') &&
			$msg=msgfmt_create(Locale::getDefault(),$val))
			// Format string according to locale rules
			$val=$msg->format(is_array($args)?$args:array($args));
		elseif (is_null($val)) {
			// Attempt to retrieve from cache
			$hash='var.'.self::hash($name);
			if (Cache::cached($hash))
				$val=Cache::get($hash);
		}
		return $val;
	}

	/**
		Unset framework variable
			@param $key string
			@public
	**/
	static function clear($key) {
		if (preg_match('/{.+}/',$key))
			// Variable variable
			$key=self::subst($key);
		if (!self::valid($key))
			return;
		// Clearing SESSION array ends the current session
		if ($key=='SESSION') {
			if (!session_id())
				session_start();
			// End the session
			session_unset();
			session_destroy();
		}
		preg_match('/^('.self::PHP_Globals.')(.*)$/',$key,$match);
		if (isset($match[1])) {
			$name=self::remix($key,FALSE);
			eval($match[2]?'unset($_'.$name.');':'$_'.$name.'=NULL;');
		}
		$name=preg_replace('/^(\w+)/','[\'\1\']',self::remix($key));
		// Assign NULL to framework variables; do not unset
		eval(ctype_upper(preg_replace('/^\w+/','\0',$key))?
			'self::$vars'.$name.'=NULL;':'unset(self::$vars'.$name.');');
		$name=self::remix($key);
		// Remove from cache
		$hash='var.'.self::hash($name);
		if (Cache::cached($hash))
			Cache::clear($hash);
	}

	/**
		Return TRUE if framework variable has been assigned a value
			@return boolean
			@param $key string
			@public
	**/
	static function exists($key) {
		if (preg_match('/{.+}/',$key))
			// Variable variable
			$key=self::subst($key);
		if (!self::valid($key))
			return FALSE;
		$var=&self::ref(self::remix($key));
		return isset($var);
	}

	/**
		Multi-variable assignment using associative array
			@param $arg array
			@public
	**/
	static function mset($arg) {
		if (!is_array($arg))
			// Invalid argument
			trigger_error(self::TEXT_MSet);
		else
			// Bind key-value pairs
			array_map('self::set',array_keys($arg),$arg);
	}

	/**
		Determine if framework variable has been cached
			@return mixed
			@param $key string
			@public
	**/
	static function cached($key) {
		if (preg_match('/{.+}/',$key))
			// Variable variable
			$key=self::subst($key);
		return self::valid($key)?
			Cache::cached('var.'.self::hash(self::remix($key))):
			FALSE;
	}

	/**
		Configure framework according to INI-style file settings;
		Cache auto-generated PHP code to speed up execution
			@param $file string
			@public
	**/
	static function config($file) {
		// Generate hash code for config file
		$hash='php.'.self::hash($file);
		$cached=Cache::cached($hash);
		if ($cached && filemtime($file)<$cached)
			// Retrieve from cache
			$save=Cache::get($hash);
		else {
			if (!is_file($file)) {
				// Configuration file not found
				self::$vars['CONTEXT']=$file;
				trigger_error(self::TEXT_Config);
				return;
			}
			// Map sections to framework methods
			$map=array('globals'=>'set','routes'=>'route','maps'=>'map');
			// Read the .ini file
			preg_match_all(
				'/\s*(?:\[(.+?)\]|(?:;.+?)?|(?:([^=]+)=(.+?)))(?:\v|$)/s',
					file_get_contents($file),$matches,PREG_SET_ORDER);
			$cfg=array();
			$ptr=&$cfg;
			foreach ($matches as $match) {
				if (isset($match[1]) && !empty($match[1])) {
					// Section header
					if (!isset($map[$match[1]])) {
						// Unknown section
						self::$vars['CONTEXT']=$section;
						trigger_error(self::TEXT_Section);
						return;
					}
					$ptr=&$cfg[$match[1]];
				}
				elseif (isset($match[2]) && !empty($match[2])) {
					$csv=array_map(
						function($val) {
							// Typecast if necessary
							return is_numeric($val) ||
								preg_match('/^(TRUE|FALSE)\b/i',$val)?
									eval('return '.$val.';'):$val;
						},
						str_getcsv($match[3])
					);
					// Convert comma-separated values to array
					$match[3]=count($csv)>1?$csv:$csv[0];
					if (preg_match('/([^\[]+)\[([^\]]*)\]/',$match[2],$sub)) {
						if ($sub[2])
							// Associative array
							$ptr[$sub[1]][$sub[2]]=$match[3];
						else
							// Numeric-indexed array
							$ptr[$sub[1]][]=$match[3];
					}
					else
						// Key-value pair
						$ptr[$match[2]]=$match[3];
				}
			}
			ob_start();
			foreach ($cfg as $section=>$pairs)
				if (isset($map[$section]) && is_array($pairs)) {
					$func=$map[$section];
					foreach ($pairs as $key=>$val)
						// Generate PHP snippet
						echo 'self::'.$func.'('.var_export($key,TRUE).','.
							($func=='set' || !is_array($val)?
								var_export($val,TRUE):self::csv($val)).
						');'."\n";
				}
			$save=ob_get_clean();
			// Compress and save to cache
			Cache::set($hash,$save);
		}
		// Execute cached PHP code
		eval($save);
		if (!is_null(self::$vars['ERROR']))
			// Remove from cache
			Cache::clear($hash);
	}

	/**
		Retrieve values from a specified column of a multi-dimensional
		framework array variable
			@return array
			@param $key string
			@param $col mixed
			@public
	**/
	static function pick($key,$col) {
		$rows=self::ref($key);
		if (!is_array($rows)) {
			self::$vars['CONTEXT']=$key;
			trigger_error(self::TEXT_NotArray);
			return FALSE;
		}
		return array_map(
			function($row) use($col) {
				return $row[$col];
			},
			$rows
		);
	}

	/**
		Sort a multi-dimensional framework array variable on a specified
		column
			@return array
			@param $key string
			@param $col mixed
			@param $order integer
			@param $flag boolean
			@public
	**/
	static function sort($key,$col,$order=self::SORT_Asc,$flag=TRUE) {
		$val=&self::ref($key,TRUE);
		if (!is_array($val)) {
			self::$vars['CONTEXT']=$key;
			trigger_error(self::TEXT_NotArray);
			return FALSE;
		}
		usort(
			$val,
			function($val1,$val2) use($col,$order) {
				$self=__CLASS__;
				list($v1,$v2)=array($val1[$col],$val2[$col]);
				return $order*(((is_int($v1) || is_float($v1)) &&
					(is_int($v2) || is_float($v2)))?
					$self::sign($v1-$v2):strcmp($v1,$v2));
			}
		);
	}

	/**
		Rotate a two-dimensional framework array variable
			@return array
			@param $key string
			@param $flag boolean
			@public
	**/
	static function transpose($key) {
		$rows=&self::ref($key,TRUE);
		if (!is_array($rows)) {
			self::$vars['CONTEXT']=$key;
			trigger_error(self::TEXT_NotArray);
			return FALSE;
		}
		foreach ($rows as $keyx=>$cols)
			foreach ($cols as $keyy=>$valy)
				$result[$keyy][$keyx]=$valy;
		$rows=$result;
	}

	/**
		Send HTTP status header; Return text equivalent of status code
			@return mixed
			@param $code int
			@public
	**/
	static function status($code) {
		if (!defined('self::HTTP_'.$code)) {
			// Invalid status code
			self::$vars['CONTEXT']=$code;
			trigger_error(self::TEXT_HTTP);
			return FALSE;
		}
		// Get description
		$response=constant('self::HTTP_'.$code);
		// Send raw HTTP header
		if (PHP_SAPI!='cli' && !headers_sent())
			header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$response);
		return $response;
	}

	/**
		Retrieve HTTP headers
			@return array
			@public
	**/
	static function headers() {
		if (PHP_SAPI!='cli') {
			if (function_exists('getallheaders'))
				// Apache server
				return getallheaders();
			// Workaround
			$req=array();
			foreach ($_SERVER as $key=>$val)
				if (substr($key,0,5)=='HTTP_')
					$req[preg_replace_callback(
						'/\w+\b/',
						function($word) {
							return ucfirst(strtolower($word[0]));
						},
						strtr(substr($key,5),'_','-')
					)]=$val;
			return $req;
		}
		return array();
	}

	/**
		Send HTTP header with expiration date (seconds from current time)
			@param $secs integer
			@public
	**/
	static function expire($secs=0) {
		if (PHP_SAPI!='cli' && !headers_sent()) {
			header(self::HTTP_Powered.': '.self::TEXT_AppName);
			if ($secs) {
				header_remove(self::HTTP_Pragma);
				header(self::HTTP_Expires.': '.gmdate('r',time()+$secs));
				header(self::HTTP_Cache.': max-age='.$secs);
				header(self::HTTP_LastMod.': '.gmdate('r'));
			}
			else {
				header(self::HTTP_Pragma.': no-cache');
				header(self::HTTP_Cache.': no-cache, must-revalidate');
			}
		}
	}

	/**
		Reroute to specified URI
			@param $uri string
			@public
	**/
	static function reroute($uri) {
		$uri=self::subst($uri);
		if (PHP_SAPI!='cli' && !headers_sent()) {
			// HTTP redirect
			self::status($_SERVER['REQUEST_METHOD']!='GET'?303:301);
			header(self::HTTP_Location.': '.$uri);
			die;
		}
		self::mock('GET '.$uri);
		self::run();
	}

	/**
		Assign handler to route pattern
			@param $pattern string
			@param $funcs mixed
			@param $ttl integer
			@param $hotlink boolean
			@public
	**/
	static function route($pattern,$funcs,$ttl=0,$hotlink=TRUE) {
		list($methods,$route)=explode(' ',$pattern,2);
		foreach (explode('|',$methods) as $method)
			// Use pattern and HTTP methods as route indexes
			self::$vars['ROUTES'][$route][strtoupper($method)]=
				// Save handler, cache timeout and hotlink permission
				array($funcs,$ttl,$hotlink);
	}

	/**
		Provide REST interface by mapping URL to object/class
			@param $url string
			@param $obj mixed
			@param $ttl integer
			@param $hotlink boolean
			@public
	**/
	static function map($url,$obj,$ttl=0,$hotlink=TRUE) {
		foreach (explode('|',self::HTTP_Methods) as $method) {
			if (method_exists($obj,$method))
				self::route($method.' '.$url,
					array($obj,$method),$ttl,$hotlink);
		}
	}

	/**
		Call route handler
			@param $funcs string
			@public
	**/
	static function dispatch($funcs) {
		$classes=array();
		$funcs=is_string($funcs)?explode('|',$funcs):array($funcs);
		foreach ($funcs as $func) {
			if (is_string($func)) {
				// Replace tokens in route handler, if any
				$diff=FALSE;
				if (preg_match('/{.+}/',$func)) {
					$func=self::subst($func);
					$diff=TRUE;
				}
				if (preg_match('/(.+)(->|::)(.+)/',$func,$match)) {
					if ($diff && (!class_exists($match[1]) ||
						!method_exists($match[1],$match[3]))) {
						self::error(404);
						return;
					}
					$func=array($match[2]=='->'?
						new $match[1]:$match[1],$match[3]);
				}
				elseif ($diff && !function_exists($func)) {
					self::error(404);
					return;
				}
			}
			if (!is_callable($func)) {
				self::$vars['CONTEXT']=is_array($func) && count($func)>1?
					(get_class($func[0]).(is_object($func[0])?'->':'::').
						$func[1]):$func;
				trigger_error(self::TEXT_Handler);
				return;
			}
			$oop=is_array($func) &&
				(is_object($func[0]) || is_string($func[0]));
			if ($oop && method_exists($func[0],$before='beforeRoute') &&
				!in_array($func[0],$classes)) {
				// Execute beforeRoute() once per class
				call_user_func(array($func[0],$before));
				$classes[]=is_object($func[0])?get_class($func[0]):$func[0];
			}
			call_user_func($func);
			if ($oop && method_exists($func[0],$after='afterRoute') &&
				!in_array($func[0],$classes)) {
				// Execute afterRoute() once per class
				call_user_func(array($func[0],$after));
				$classes[]=is_object($func[0])?get_class($func[0]):$func[0];
			}
		}
	}

	/**
		Process routes based on incoming URI
			@public
	**/
	static function run() {
		// Validate user against spam blacklists
		if (self::$vars['DNSBL'] && !self::privateip($addr=self::realip()) &&
			(!self::$vars['EXEMPT'] ||
			!in_array($addr,explode('|',self::$vars['EXEMPT'])))) {
			// Convert to reverse IP dotted quad
			$quad=implode('.',array_reverse(explode('.',$addr)));
			foreach (explode('|',self::$vars['DNSBL']) as $list)
				// Check against DNS blacklist
				if (gethostbyname($quad.'.'.$list)!=$quad.'.'.$list) {
					if (self::$vars['SPAM'])
						// Spammer detected; Send to blackhole
						self::reroute(self::$vars['SPAM']);
					else
						// HTTP 404 message
						self::error(404);
				}
		}
		// Process routes
		if (!isset(self::$vars['ROUTES']) || !self::$vars['ROUTES']) {
			trigger_error(self::TEXT_NoRoutes);
			return;
		}
		$found=FALSE;
		// Detailed routes get matched first
		krsort(self::$vars['ROUTES']);
		// Save the current time
		$time=time();
		foreach (self::$vars['ROUTES'] as $uri=>$route) {
			if (!preg_match('/^'.
				preg_replace(
					'/{?@(\w+\b)}?/i',
					// Valid URL characters (RFC 1738)
					'(?P<\1>[\w\-\.!~\*\'"(),\h]+\b)',
					// Wildcard character in URI
					str_replace('\*','(.*)',preg_quote($uri,'/'))
				).'\/?(?:\?.*)?$/i',
				substr(rawurldecode($_SERVER['REQUEST_URI']),
					strlen(self::$vars['BASE'])),$args))
				continue;
			$found=TRUE;
			// Inspect each defined route
			foreach ($route as $method=>$proc) {
				if (!preg_match('/'.$method.'/',$_SERVER['REQUEST_METHOD']))
					continue;
				list($funcs,$ttl,$hotlink)=$proc;
				if (!$hotlink && isset(self::$vars['HOTLINK']) &&
					isset($_SERVER['HTTP_REFERER']) &&
					parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST)!=
						$_SERVER['SERVER_NAME'])
					// Hot link detected; Redirect page
					self::reroute(self::$vars['HOTLINK']);
				// Save named uri captures
				foreach ($args as $key=>$arg)
					// Remove non-zero indexed elements
					if (is_numeric($key) && $key)
						unset($args[$key]);
				self::$vars['PARAMS']=$args;
				// Default: Do not cache
				self::expire(0);
				if ($_SERVER['REQUEST_METHOD']=='GET' && $ttl) {
					$_SERVER['REQUEST_TTL']=$ttl;
					// Get HTTP request headers
					$req=self::headers();
					// Content divider
					$div=chr(0);
					// Get hash code for this Web page
					$hash='url.'.self::hash(
						$_SERVER['REQUEST_METHOD'].' '.
						$_SERVER['REQUEST_URI']
					);
					$cached=Cache::cached($hash);
					$uri='/^'.self::HTTP_Content.':.+/';
					$time=time();
					if ($cached && $time-$cached<$ttl) {
						if (!isset($req[self::HTTP_IfMod]) ||
							$cached>strtotime($req[self::HTTP_IfMod])) {
							// Activate cache timer
							self::expire($cached+$ttl-$time);
							// Retrieve from cache
							$buffer=Cache::get($hash);
							$type=strstr($buffer,$div,TRUE);
							if (PHP_SAPI!='cli' && !headers_sent() &&
								preg_match($uri,$type,$match))
								// Cached MIME type
								header($match[0]);
							// Save response
							self::$vars['RESPONSE']=substr(
								strstr($buffer,$div),1);
						}
						else {
							// Client-side cache is still fresh
							self::status(304);
							die;
						}
					}
					else {
						// Cache this page
						ob_start();
						self::dispatch($funcs);
						self::$vars['RESPONSE']=ob_get_clean();
						if (!self::$vars['ERROR'] &&
							self::$vars['RESPONSE']) {
							// Activate cache timer
							self::expire($ttl);
							$type='';
							foreach (headers_list() as $hdr)
								if (preg_match($uri,$hdr)) {
									// Add Content-Type header to buffer
									$type=$hdr;
									break;
								}
							// Compress and save to cache
							Cache::set($hash,
								$type.$div.self::$vars['RESPONSE']);
						}
					}
				}
				else {
					// Capture output
					ob_start();
					if ($_SERVER['REQUEST_METHOD']=='PUT') {
						// Associate PUT with file handle of stdin stream
						self::$vars['PUT']=fopen('php://input','rb');
						self::dispatch($funcs);
						fclose(self::$vars['PUT']);
					}
					else
						self::dispatch($funcs);
					self::$vars['RESPONSE']=ob_get_clean();
				}
				$elapsed=time()-$time;
				if (self::$vars['THROTTLE']/1e3>$elapsed)
					// Delay output
					usleep(1e6*(self::$vars['THROTTLE']/1e3-$elapsed));
				if (self::$vars['RESPONSE'] && !self::$vars['QUIET'])
					// Display response
					echo self::$vars['RESPONSE'];
				// Hail the conquering hero
				return;
			}
		}
		// No such Web page
		self::error(404);
	}

	/**
		Remove HTML tags (except those enumerated) to protect against
		XSS/code injection attacks
			@return mixed
			@param $input string
			@param $tags string
			@public
	**/
	static function scrub($input,$tags=NULL) {
		if (is_array($input))
			foreach ($input as &$val)
				$val=self::scrub($val,$tags);
		return is_string($input)?
			htmlspecialchars(
				strip_tags($input,is_string($tags)?
					('<'.implode('><',explode('|',$tags)).'>'):$tags),
				ENT_COMPAT,self::$vars['ENCODING'],FALSE):$input;
	}

	/**
		Call form field handler
			@param $fields string
			@param $funcs mixed
			@param $tags string
			@param $filter integer
			@param $opt array
			@public
	**/
	static function input($fields,$funcs,
		$tags=NULL,$filter=FILTER_UNSAFE_RAW,$opt=array()) {
		$funcs=is_string($funcs)?explode('|',$funcs):array($funcs);
		foreach (explode('|',$fields) as $field) {
			$found=NULL;
			// Sanitize relevant globals
			$php=$_SERVER['REQUEST_METHOD'].'|REQUEST|FILES';
			foreach (explode('|',$php) as $var)
				if (isset(self::$vars[$var][$field])) {
					self::$vars[$var][$field]=filter_var(
						self::scrub(self::$vars[$var][$field],$tags),
						$filter,$opt);
					if (!$found)
						$found=$var;
				}
			if ($found) {
				foreach ($funcs as $func) {
					if (is_string($func) &&
						preg_match('/([\w\\\]+)->(\w+)/',$func,$match))
						// Convert class->method syntax to callback
						$func=array(new $match[1],$match[2]);
					if (!is_callable($func)) {
						// Invalid handler
						self::$vars['CONTEXT']=$field;
						trigger_error(self::TEXT_Form);
						return;
					}
					call_user_func($func,self::$vars[$found][$field]);
				}
				return;
			}
			// Invalid handler
			self::$vars['CONTEXT']=$field;
			trigger_error(self::TEXT_Form);
			return;
		}
	}

	/**
		Clean and repair HTML
			@return string
			@param $html string
			@public
	**/
	static function tidy($html) {
		$tidy=new Tidy;
		$tidy->parseString($html,self::$vars['TIDY'],
			str_replace('-','',self::$vars['ENCODING']));
		$tidy->cleanRepair();
		return (string)$tidy;
	}

	/**
		Render user interface
			@return string
			@param $file string
			@public
	**/
	static function render($file) {
		$file=self::subst($file);
		if (is_file($view=self::fixslashes(self::$vars['GUI'].$file))) {
			$out=SandBox::grab($view);
			return self::$vars['TIDY'] && extension_loaded('tidy')?
				self::tidy($out):$out;
		}
		self::$vars['CONTEXT']=$view;
		trigger_error(self::TEXT_Render);
	}

	/**
		Return runtime performance analytics
			@return array
			@public
	**/
	static function profile() {
		$stats=&self::$vars['STATS'];
		// Compute elapsed time
		$stats['TIME']['elapsed']=microtime(TRUE)-$stats['TIME']['start'];
		// Compute memory consumption
		$stats['MEMORY']['current']=memory_get_usage();
		$stats['MEMORY']['peak']=memory_get_peak_usage();
		return $stats;
	}

	/**
		Mock environment for command-line use and/or unit testing
			@param $pattern string
			@param $params array
			@public
	**/
	static function mock($pattern,array $params=NULL) {
		// Override PHP globals
		list($method,$uri)=explode(' ',$pattern,2);
		$query=explode('&',parse_url($uri,PHP_URL_QUERY));
		foreach ($query as $pair)
			if (strpos($pair,'=')) {
				list($var,$val)=explode('=',$pair);
				self::$vars[$method][$var]=$val;
				self::$vars['REQUEST'][$var]=$val;
			}
		if (is_array($params))
			foreach ($params as $var=>$val) {
				self::$vars[$method][$var]=$val;
				self::$vars['REQUEST'][$var]=$val;
			}
		$_SERVER['REQUEST_METHOD']=$method;
		$_SERVER['REQUEST_URI']=$uri;
	}

	/**
		Perform test and append result to TEST global variable
			@return string
			@param $cond boolean
			@param $pass string
			@param $fail string
			@public
	**/
	static function expect($cond,$pass=NULL,$fail=NULL) {
		if (is_string($cond))
			$cond=self::subst($cond);
		$text=$cond?$pass:$fail;
		self::$vars['TEST'][]=array(
			'result'=>(int)(boolean)$cond,
			'text'=>is_string($text)?
				self::subst($text):var_export($text,TRUE)
		);
		return $text;
	}

	/**
		Display default error page; Use custom page if found
			@param $code integer
			@param $str string
			@param $trace array
			@public
	**/
	static function error($code,$str='',array $trace=NULL) {
		$prior=self::$vars['ERROR'];
		// Generate internal server error if code is zero
		if (!$code)
			$code=500;
		elseif ($code==404) {
			self::$vars['CONTEXT']=$_SERVER['REQUEST_URI'];
			$str=self::subst(self::TEXT_NotFound);
		}
		$out='';
		$line=0;
		if (is_null($trace))
			$trace=debug_backtrace();
		$class=NULL;
		if (is_array($trace)) {
			// Stringify the stack trace
			ob_start();
			foreach ($trace as $nexus) {
				// Remove stack trace noise
				if (!isset($nexus['line']) ||
					(self::$vars['DEBUG']<2 && ($nexus['file']==__FILE__ ||
						isset($nexus['class']) &&
						preg_match('/^Base|Cache|'.__CLASS__.'.*/',
							$nexus['class']) ||
						isset($nexus['function']) &&
						preg_match('/^(call_user_func|include|require|'.
							'trigger_error|{.+})/',$nexus['function']))))
					continue;
				if ($code!=404)
					echo '#'.$line.' '.
						(isset($nexus['line'])?
							(self::fixslashes($nexus['file']).':'.
								$nexus['line'].' '):'').
						(isset($nexus['function'])?
							((isset($nexus['class'])?$nexus['class']:'').
								(isset($nexus['type'])?$nexus['type']:'').
									$nexus['function'].
							(!preg_match('/{.+}/',$nexus['function']) &&
								isset($nexus['args'])?
								('('.self::csv($nexus['args']).')'):'')):'').
							"\n";
				$line++;
			}
			$out=ob_get_clean();
		}
		if (PHP_SAPI!='cli' && !headers_sent())
			// Remove all pending headers
			header_remove();
		// Save error details
		self::$vars['ERROR']=array(
			'code'=>$code,
			'title'=>self::status($code),
			'text'=>preg_replace('/\v/','',self::subst($str)),
			'trace'=>self::$vars['DEBUG']?$out:''
		);
		unset(self::$vars['CONTEXT']);
		if (self::$vars['DEBUG']<2 && self::$vars['QUIET'])
			return;
		// Write to server's error log (with complete stack trace)
		error_log(self::$vars['ERROR']['text']);
		foreach (explode("\n",$out) as $str)
			if ($str)
				error_log($str);
		if ($prior || self::$vars['QUIET'])
			return;
		foreach (explode('|','title|text|trace') as $sub)
			// Convert to HTML entities for safety
			self::$vars['ERROR'][$sub]=htmlspecialchars(
				rawurldecode(self::$vars['ERROR'][$sub]),
				ENT_COMPAT,self::$vars['ENCODING']);
		self::$vars['ERROR']['trace']=nl2br(self::$vars['ERROR']['trace']);
		$func=self::$vars['ONERROR'];
		if ($func)
			self::dispatch($func);
		else
			echo self::subst(
				'<html>'.
					'<head>'.
						'<title>{@ERROR.code} {@ERROR.title}</title>'.
					'</head>'.
					'<body>'.
						'<h1>{@ERROR.title}</h1>'.
						'<p><i>{@ERROR.text}</i></p>'.
						'<p>{@ERROR.trace}</p>'.
					'</body>'.
				'</html>'
			);
	}

	/**
		Execute shutdown function
			@public
	**/
	static function stop() {
		$error=error_get_last();
		if ($error && !self::$vars['QUIET'] && in_array($error['type'],
			array(E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR)))
			// Display error
			self::error(500,$error['message'],array($error));
		if (isset(self::$vars['UNLOAD'])) {
			ob_end_flush();
			if (PHP_SAPI!='cli')
				header(self::HTTP_Connect.': close');
			call_user_func(self::$vars['UNLOAD']);
		}
	}

	/**
		Intercept instantiation of objects in undefined classes
			@param $class string
			@public
	**/
	static function autoload($class) {
		// Prepend plugins folder
		foreach (explode('|',self::$vars['PLUGINS'].'|'.
			self::$vars['AUTOLOAD']) as $auto) {
			$path=realpath($auto);
			if (!$path)
				continue;
			// Allow namespaced classes
			$file=self::fixslashes($path.'/'.$class).'.php';
			// Case-insensitive check for file presence
			$glob=glob(dirname($file).'/*.php',GLOB_NOSORT);
			if (!$glob)
				continue;
			$fkey=array_search(strtolower($file),
				array_map('strtolower',$glob));
			if (is_int($fkey) && !in_array($glob[$fkey],
				array_map('self::fixslashes',get_included_files()))) {
				require $glob[$fkey];
				// Verify that the class was loaded
				if (class_exists($class,FALSE)) {
					$loaded=&self::$vars['LOADED'];
					$lower=strtolower($class);
					if (!isset($loaded[$lower])) {
						$loaded[$lower]=array_map(
							'strtolower',get_class_methods($class));
						if (in_array('onload',$loaded[$lower])) {
							// Execute onload method
							$method=new ReflectionMethod($class,'onload');
							if ($method->isStatic())
								call_user_func(array($class,'onload'));
							else {
								self::$vars['CONTEXT']=$class.'::onload';
								trigger_error(self::TEXT_Static);
							}
						}
					}
					return;
				}
			}
		}
		if (count(spl_autoload_functions())==1) {
			// No other registered autoload functions exist
			self::$vars['CONTEXT']=$class;
			trigger_error(self::TEXT_Class);
		}
	}

	/**
		Create function aliases for framework methods
			@param $prefix string
			@public
	**/
	static function alias($prefix) {
		foreach (get_class_methods(__CLASS__) as $func)
			if (!function_exists($func) && $func[0]!='_')
				eval('function '.$prefix.'_'.$func.'() {'.
					'return call_user_func_array(\''.
						__CLASS__.'::'.$func.'\','.'func_get_args());'.
					'}');
	}

	/**
		Bootstrap code
			@public
	**/
	static function start() {
		// Prohibit multiple calls
		if (self::$vars)
			return;
		// Handle all exceptions/non-fatal errors
		error_reporting(E_ALL|E_STRICT);
		ini_set('display_errors',0);
		// Get PHP settings
		$ini=ini_get_all(NULL,FALSE);
		// Intercept errors and send output to browser
		set_error_handler(
			function($errno,$errstr) {
				if (error_reporting()) {
					// Error suppression (@) is not enabled
					$self=__CLASS__;
					$self::error(500,$errstr);
				}
			}
		);
		// Do the same for PHP exceptions
		set_exception_handler(
			function($ex) {
				if (!count($trace=$ex->getTrace())) {
					// Translate exception trace
					list($trace)=debug_backtrace();
					$arg=$trace['args'][0];
					$trace=array(
						array(
							'file'=>$arg->getFile(),
							'line'=>$arg->getLine(),
							'function'=>'{main}',
							'args'=>array()
						)
					);
				}
				$self=__CLASS__;
				$self::error($ex->getCode(),$ex->getMessage(),$trace);
				// PHP aborts at this point
			}
		);
		// Apache mod_rewrite enabled?
		if (function_exists('apache_get_modules') &&
			!in_array('mod_rewrite',apache_get_modules())) {
			trigger_error(self::TEXT_Apache);
			return;
		}
		// Fix Apache's VirtualDocumentRoot limitation
		$_SERVER['DOCUMENT_ROOT']=str_replace($_SERVER['SCRIPT_NAME'],'',
			$_SERVER['SCRIPT_FILENAME']);
		// Adjust HTTP request time precision
		$_SERVER['REQUEST_TIME']=microtime(TRUE);
		// Hydrate framework variables
		$root=self::fixslashes(realpath('.')).'/';
		self::$vars=array(
			// Autoload folders
			'AUTOLOAD'=>$root,
			// Web root folder
			'BASE'=>preg_replace(
				array('/[^\/]+$/','/^\//'),'',$_SERVER['SCRIPT_NAME']),
			// Cache backend to use (autodetect if true; disable if false)
			'CACHE'=>FALSE,
			// Stack trace verbosity:
			// 0-no stack trace, 1-noise removed, 2-full stack trace
			'DEBUG'=>1,
			// DNS black lists
			'DNSBL'=>NULL,
			// Document encoding
			'ENCODING'=>'UTF-8',
			// Last error
			'ERROR'=>NULL,
			// Allow/prohibit framework class extension
			'EXTEND'=>FALSE,
			// IP addresses exempt from spam detection
			'EXEMPT'=>NULL,
			// User interface folder
			'GUI'=>$root,
			// Server hostname
			'HOSTNAME'=>$_SERVER['SERVER_NAME'],
			// URL for hotlink redirection
			'HOTLINK'=>NULL,
			// Default language (auto-detect if null)
			'LANGUAGE'=>NULL,
			// Autoloaded classes
			'LOADED'=>NULL,
			// Dictionary folder
			'LOCALES'=>$root,
			// Maximum POST size
			'MAXSIZE'=>self::bytes($ini['post_max_size']),
			// Custom error handler
			'ONERROR'=>NULL,
			// Plugins folder
			'PLUGINS'=>__DIR__,
			// Prefix for method aliases
			'PREFIX'=>__CLASS__,
			// Server protocol
			'PROTOCOL'=>'http'.
				(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off'?'s':''),
			// Allow framework to proxy for plugins
			'PROXY'=>FALSE,
			// Stream handle for HTTP PUT method
			'PUT'=>NULL,
			// Output suppression switch
			'QUIET'=>FALSE,
			// Absolute path to document root folder
			'ROOT'=>$root,
			// Framework routes
			'ROUTES'=>NULL,
			// URL for spam redirection
			'SPAM'=>NULL,
			// Profiler statistics
			'STATS'=>array(
				'MEMORY'=>array('start'=>memory_get_usage()),
				'TIME'=>array('start'=>microtime(TRUE))
			),
			// Minimum script execution time
			'THROTTLE'=>0,
			// Tidy options
			'TIDY'=>array(),
			// Framework version
			'VERSION'=>self::TEXT_AppName.' '.self::TEXT_Version
		);
		// Create convenience containers for PHP globals
		foreach (explode('|',self::PHP_Globals) as $var) {
			// Sync framework and PHP globals
			self::$vars[$var]=&$GLOBALS['_'.$var];
			if ($ini['magic_quotes_gpc'] && preg_match('/^[GPCR]/',$var))
				// Corrective action on PHP magic quotes
				array_walk_recursive(
					self::$vars[$var],
					function(&$val) {
						$val=stripslashes($val);
					}
				);
		}
		if (PHP_SAPI=='cli') {
			// Command line: Parse GET variables in URL, if any
			if (isset($_SERVER['argc']) && $_SERVER['argc']<2)
				array_push($_SERVER['argv'],'/');
			preg_match_all('/[\?&]([^=]+)=([^&$]*)/',
				$_SERVER['argv'][1],$matches,PREG_SET_ORDER);
			foreach ($matches as $match) {
				$_REQUEST[$match[1]]=$match[2];
				$_GET[$match[1]]=$match[2];
			}
			// Detect host name from environment
			$_SERVER['SERVER_NAME']=gethostname();
			// Convert URI to human-readable string
			self::mock('GET '.$_SERVER['argv'][1]);
		}
		// Initialize autoload stack and shutdown sequence
		spl_autoload_register(__CLASS__.'::autoload');
		register_shutdown_function(__CLASS__.'::stop');
		// Create aliases for framework methods
		self::alias(__CLASS__);
	}

	/**
		Intercept calls to undefined static methods and proxy for the
		called class if found in the plugins folder
			@return mixed
			@param $func string
			@param $args array
			@public
	**/
	static function __callStatic($func,array $args) {
		if (self::$vars['PROXY'])
			foreach (glob(self::fixslashes(self::$vars['PLUGINS'].
				'/*.php',GLOB_NOSORT)) as $file) {
				$class=strstr(basename($file),'.php',TRUE);
				// Prevent recursive calls
				$found=FALSE;
				foreach (debug_backtrace() as $trace)
					if (isset($trace['class']) &&
						// Support namespaces
						preg_match('/'.preg_quote($trace['class']).'/i',
						strtolower($class)) &&
						preg_match('/'.$trace['function'].'/i',
						strtolower($func))) {
						$found=TRUE;
						break;
					}
				if ($found)
					continue;
				$loaded=&self::$vars['LOADED'];
				$lower=strtolower($class);
				if (!isset($loaded[$lower])) {
					$loaded[$lower]=array_map(
						'strtolower',get_class_methods($class));
					// Execute onload method if defined
					if (in_array('onload',$loaded[$lower])) {
						// Execute onload method
						$method=new ReflectionMethod($class,'onload');
						if ($method->isStatic())
							call_user_func(array($class,'onload'));
						else {
							self::$vars['CONTEXT']=$class.'::onload';
							trigger_error(self::TEXT_Static);
						}
					}
				}
				if (in_array($func,$loaded[$class]))
					// Proxy for plugin
					return call_user_func_array(array($class,$func),$args);
			}
		if (count(spl_autoload_functions())==1) {
			// No other registered autoload functions exist
			self::$vars['CONTEXT']=$func;
			trigger_error(self::TEXT_Method);
		}
		return FALSE;
	}

}

//! Cache engine
class Cache extends Base {

	//@{ Locale-specific error/exception messages
	const
		TEXT_Backend='Cache back-end is invalid',
		TEXT_Store='Unable to save {@CONTEXT} to cache',
		TEXT_Fetch='Unable to retrieve {@CONTEXT} from cache',
		TEXT_Clear='Unable to clear {@CONTEXT} from cache',
		TEXT_Write='{@CONTEXT.0} must have write permission on {@CONTEXT.1}';
	//@}

	static
		//! Level-1 cached object
		$buffer,
		//! Cache back-end
		$backend;

	/**
		Auto-detect extensions usable as cache back-ends; MemCache must be
		explicitly activated to work properly; Fall back to file system if
		none declared or detected
			@public
	**/
	static function detect() {
		$exts=array_intersect(explode('|','apc|xcache'),
			array_map('strtolower',get_loaded_extensions()));
		$ref=array_merge($exts,array());
		self::$vars['CACHE']=array_shift($ref)?:
			('folder='.self::$vars['ROOT'].'cache/');
	}

	/**
		Initialize cache backend
			@return boolean
			@public
	**/
	static function prep() {
		if (!self::$vars['CACHE'])
			return TRUE;
		if (preg_match(
			'/^(apc)|(memcache)=(.+)|(xcache)|(folder)\=(.+\/)/i',
			self::$vars['CACHE'],$match)) {
			if (isset($match[5]) && $match[5]) {
				if (!is_dir($match[6])) {
					if (!is_writable(dirname($match[6])) &&
						function_exists('posix_getpwuid')) {
							$uid=posix_getpwuid(posix_geteuid());
							self::$vars['CONTEXT']=array($uid['name'],
								realpath(dirname($match[6])));
							trigger_error(self::TEXT_Write);
							return FALSE;
					}
					// Create the framework's cache folder
					umask(0);
					mkdir($match[6],0755);
				}
				// File system
				self::$backend=array('type'=>'folder','id'=>$match[6]);
			}
			else {
				$ext=strtolower($match[1]?:($match[2]?:$match[4]));
				if (!extension_loaded($ext)) {
					self::$vars['CONTEXT']=$ext;
					trigger_error(self::TEXT_PHPExt);
					return FALSE;
				}
				if (isset($match[2]) && $match[2]) {
					// Open persistent MemCache connection(s)
					// Multiple servers separated by semi-colon
					$pool=explode(';',$match[3]);
					$mcache=NULL;
					foreach ($pool as $server) {
						// Hostname:port
						list($host,$port)=explode(':',$server);
						if (is_null($port))
							// Use default port
							$port=11211;
						// Connect to each server
						if (is_null($mcache))
							$mcache=memcache_pconnect($host,$port);
						else
							memcache_add_server($mcache,$host,$port);
					}
					// MemCache
					self::$backend=array('type'=>$ext,'id'=>$mcache);
				}
				else
					// APC and XCache
					self::$backend=array('type'=>$ext);
			}
			self::$buffer=NULL;
			return TRUE;
		}
		// Unknown back-end
		trigger_error(self::TEXT_Backend);
		return FALSE;
	}

	/**
		Store data in framework cache; Return TRUE/FALSE on success/failure
			@return boolean
			@param $name string
			@param $data mixed
			@public
	**/
	static function set($name,$data) {
		if (!self::$vars['CACHE'])
			return TRUE;
		if (is_null(self::$backend)) {
			// Auto-detect back-end
			self::detect();
			if (!self::prep())
				return FALSE;
		}
		$key=$_SERVER['SERVER_NAME'].'.'.$name;
		// Serialize data for storage
		$time=time();
		// Add timestamp
		$val=gzdeflate(serialize(array($time,$data)));
		// Instruct back-end to store data
		switch (self::$backend['type']) {
			case 'apc':
				$ok=apc_store($key,$val);
				break;
			case 'memcache':
				$ok=memcache_set(self::$backend['id'],$key,$val);
				break;
			case 'xcache':
				$ok=xcache_set($key,$val);
				break;
			case 'folder':
				$ok=file_put_contents(
					self::$backend['id'].$key,$val,LOCK_EX);
				break;
		}
		if (is_bool($ok) && !$ok) {
			self::$vars['CONTEXT']=$name;
			trigger_error(self::TEXT_Store);
			return FALSE;
		}
		// Free up space for level-1 cache
		while (count(self::$buffer) && strlen(serialize($data))+
			strlen(serialize(array_slice(self::$buffer,1)))>
			ini_get('memory_limit')-memory_get_peak_usage())
				self::$buffer=array_slice(self::$buffer,1);
		self::$buffer[$name]=array('data'=>$data,'time'=>$time);
		return TRUE;
	}

	/**
		Retrieve value from framework cache
			@return mixed
			@param $name string
			@param $quiet boolean
			@public
	**/
	static function get($name,$quiet=FALSE) {
		if (!self::$vars['CACHE'])
			return FALSE;
		if (is_null(self::$backend)) {
			// Auto-detect back-end
			self::detect();
			if (!self::prep())
				return FALSE;
		}
		$stats=&self::$vars['STATS'];
		if (!isset($stats['CACHE']))
			$stats['CACHE']=array(
				'level-1'=>array('hits'=>0,'misses'=>0),
				'backend'=>array('hits'=>0,'misses'=>0)
			);
		// Check level-1 cache first
		if (isset(self::$buffer) && isset(self::$buffer[$name])) {
			$stats['CACHE']['level-1']['hits']++;
			return self::$buffer[$name]['data'];
		}
		else
			$stats['CACHE']['level-1']['misses']++;
		$key=$_SERVER['SERVER_NAME'].'.'.$name;
		// Instruct back-end to fetch data
		switch (self::$backend['type']) {
			case 'apc':
				$val=apc_fetch($key);
				break;
			case 'memcache':
				$val=memcache_get(self::$backend['id'],$key);
				break;
			case 'xcache':
				$val=xcache_get($key);
				break;
			case 'folder':
				$val=is_file(self::$backend['id'].$key)?
					file_get_contents(self::$backend['id'].$key):FALSE;
				break;
		}
		if (is_bool($val)) {
			$stats['CACHE']['backend']['misses']++;
			// No error display if specified
			if (!$quiet) {
				self::$vars['CONTEXT']=$name;
				trigger_error(self::TEXT_Fetch);
			}
			self::$buffer[$name]=NULL;
			return FALSE;
		}
		// Unserialize timestamp and data
		list($time,$data)=unserialize(gzinflate($val));
		$stats['CACHE']['backend']['hits']++;
		// Free up space for level-1 cache
		while (count(self::$buffer) && strlen(serialize($data))+
			strlen(serialize(array_slice(self::$buffer,1)))>
			ini_get('memory_limit')-memory_get_peak_usage())
				self::$buffer=array_slice(self::$buffer,1);
		self::$buffer[$name]=array('data'=>$data,'time'=>$time);
		return $data;
	}

	/**
		Delete variable from framework cache
			@return boolean
			@param $name string
			@public
	**/
	static function clear($name) {
		if (!self::$vars['CACHE'])
			return TRUE;
		if (is_null(self::$backend)) {
			// Auto-detect back-end
			self::detect();
			if (!self::prep())
				return FALSE;
		}
		$key=$_SERVER['SERVER_NAME'].'.'.$name;
		// Instruct back-end to clear data
		switch (self::$backend['type']) {
			case 'apc':
				$ok=!apc_exists($key) || apc_delete($key);
				break;
			case 'memcache':
				$ok=memcache_delete(self::$backend['id'],$key);
				break;
			case 'xcache':
				$ok=!xcache_isset($key) || xcache_unset($key);
				break;
			case 'folder':
				$ok=is_file(self::$backend['id'].$key) &&
					unlink(self::$backend['id'].$key);
				break;
		}
		if (is_bool($ok) && !$ok) {
			self::$vars['CONTEXT']=$name;
			trigger_error(self::TEXT_Clear);
			return FALSE;
		}
		// Check level-1 cache first
		if (isset(self::$buffer) && isset(self::$buffer[$name]))
			unset(self::$buffer[$name]);
		return TRUE;
	}

	/**
		Return FALSE if specified variable is not in cache;
		otherwise, return Un*x timestamp
			@return mixed
			@param $name string
			@public
	**/
	static function cached($name) {
		return self::get($name,TRUE)?self::$buffer[$name]['time']:FALSE;
	}

}

// Sandbox for isolating PHP functions
class SandBox {

	/**
		Grab file contents and run PHP code in sandbox
			@return mixed
			@param $file string
			@public
	**/
	static function grab($file) {
		// Use framework symbol table to hide local variable
		F3::set('FILE',F3::subst($file));
		unset($file);
		// Interpret PHP code
		ob_start();
		require F3::get('FILE');
		return ob_get_clean();
	}

}

//! F3 object mode
class F3instance {

	/**
		Get framework variable reference; Workaround for PHP's
		call_user_func() reference limitation
			@return mixed
			@param $key string
			@param $set boolean
			@public
	**/
	function &ref($key,$set=TRUE) {
		return F3::ref($key,$set);
	}

	/**
		Grab file contents and run PHP code in sandbox;
		Workaround for PHP's require() scope limitation
			@return mixed
			@param $file string
			@public
	**/
	function grab($file) {
		// Use framework symbol table to hide local variable
		F3::set('FILE',F3::subst($file));
		unset($file);
		// Interpret PHP code
		ob_start();
		require F3::get('FILE');
		return ob_get_clean();
	}

	/**
		Render user interface; Enables use of $this in views
			@return string
			@param $file string
			@param $opt boolean
			@public
	**/
	function render($file) {
		$file=F3::subst($file);
		if (is_file($view=F3::fixslashes(F3::ref('GUI').$file))) {
			$out=$this->grab($view);
			return F3::ref('TIDY') && extension_loaded('tidy')?
				F3::tidy($out):$out;
		}
		$var=&F3::ref('CONTEXT');
		$var=$view;
		trigger_error(F3::TEXT_Render);
	}

	/**
		Proxy for framework methods
			@return mixed
			@param $func string
			@param $args array
			@public
	**/
	function __call($func,array $args) {
		return call_user_func_array('F3::'.$func,$args);
	}

	/**
		Class constructor
			@public
	**/
	function __construct($boot=FALSE) {
		if ($boot)
			F3::start();
		// Allow application to override framework methods?
		if (F3::ref('EXTEND'))
			// User assumes risk
			return;
		// Get all framework methods not defined in this class
		$def=array_diff(get_class_methods('F3'),get_class_methods(__CLASS__));
		// Check for conflicts
		$class=new ReflectionClass($this);
		foreach ($class->getMethods() as $func)
			if (in_array($func->name,$def)) {
				F3::set('CONTEXT',get_called_class().'->'.$func->name);
				trigger_error(F3::TEXT_Conflict);
			}
	}

}

// Bootstrap
return new F3instance(TRUE);
