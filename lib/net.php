<?php

/**
	Network utilities for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3::Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package Network
		@version 2.0.0
**/

//! Network utilities
class Net extends Base {

	//@{ Locale-specific error/exception messages
	const
		TEXT_Timeout='Connection timed out';
	//@}

	const
		//! Carriage return/line feed sequence
		EOL="\r\n";

	/**
		Send ICMP echo request to specified host; Return array containing
		minimum/average/maximum round-trip time (in millisecs) and number of
		packets received, or FALSE if host is unreachable
			@return mixed
			@param $addr string
			@param $dns boolean
			@param $count integer
			@param $wait integer
			@param $ttl integer
			@public
	**/
	static function ping($addr,$dns=FALSE,$count=3,$wait=3,$ttl=30) {
		// ICMP transmit socket
		$tsocket=socket_create(AF_INET,SOCK_RAW,1);
		// Set TTL
		socket_set_option($tsocket,0,PHP_OS!='Linux'?4:2,$ttl);
		// ICMP receive socket
		$rsocket=socket_create(AF_INET,SOCK_RAW,1);
		// Bind to all network interfaces
		socket_bind($rsocket,0,0);
		// Initialize counters
		list($rtt,$rcv,$min,$max)=array(0,0,0,0);
		for ($i=0;$i<$count;$i++) {
			// Send ICMP header and payload
			$data=uniqid();
			$payload=self::hexbin('0800000000000000').$data;
			// Recalculate ICMP checksum
			if (strlen($payload)%2)
				$payload.=self::hexbin('00');
			$bits=unpack('n*',$payload);
			$sum=array_sum($bits);
			while ($sum>>16)
				$sum=($sum>>16)+($sum&0xFFFF);
			$payload=self::hexbin('0800').pack('n*',~$sum).
				self::hexbin('00000000').$data;
			// Transmit ICMP packet
			@socket_sendto($tsocket,$payload,strlen($payload),0,$addr,0);
			// Start timer
			$time=microtime(TRUE);
			$rset=array($rsocket);
			$tset=array();
			$xset=array();
			// Wait for incoming ICMP packet
			socket_select($rset,$tset,$xset,$wait);
			if ($rset &&
				@socket_recvfrom($rsocket,$reply,255,0,$host,$port)) {
				$elapsed=1e3*(microtime(TRUE)-$time);
				// Socket didn't timeout; Record round-trip time
				$rtt+=$elapsed;
				if ($elapsed>$max)
					$max=$elapsed;
				if (!($min>0) || $elapsed<$min)
					$min=$elapsed;
				// Count packets received
				$rcv++;
				if ($host)
					$addr=$host;
			}
		}
		socket_close($tsocket);
		socket_close($rsocket);
		return $rcv?
			array(
				'host'=>$dns?gethostbyaddr($addr):$addr,
				'min'=>(int)round($min),
				'max'=>(int)round($max),
				'avg'=>(int)round($rtt/$rcv),
				'packets'=>$rcv
			):
			FALSE;
	}

	/**
		Return the path taken by packets to a specified network destination
			@return array
			@param $addr string
			@param $dns boolean
			@param $wait integer
			@param $hops integer
			@public
	**/
	static function traceroute($addr,$dns=FALSE,$wait=3,$hops=30) {
		$route=array();
		for ($i=0;$i<$hops;$i++) {
			set_time_limit(ini_get('default_socket_timeout'));
			$result=self::ping($addr,$dns,3,$wait,$i+1);
			$route[]=$result;
			if (gethostbyname($result['host'])==gethostbyname($addr))
				break;
		}
		return $route;
	}

	/**
		Send HTTP/S request to another host; Follow 30x redirects (default);
		Forward headers received (if specified) and return content
			@return mixed
			@param $pattern string
			@param $query string
			@param $reqhdrs array
			@param $follow boolean
			@param $forward boolean
			@public
	**/
	static function http(
		$pattern,$query='',$reqhdrs=array(),$follow=TRUE,$forward=FALSE) {
		// Check if valid route pattern
		list($method,$route)=explode(' ',$pattern,2);
		// Content divider
		$div=chr(0);
		$url=parse_url($route);
		if (!$url['path'])
			// Set to Web root
			$url['path']='/';
		if ($method!='GET') {
			if (isset($url['query']) && $url['query']) {
				// Non-GET method; Query is distinct from URI
				$query=$url['query'];
				$url['query']='';
			}
		}
		else {
			if ($query) {
				// GET method; Query is integral part of URI
				$url['query']=$query;
				$query='';
			}
		}
		// Set up host name and TCP port for socket connection
		if (preg_match('/https/',$url['scheme'])) {
			if (!isset($url['port']))
				$url['port']=443;
			$target='ssl://'.$url['host'].':'.$url['port'];
		}
		else {
			if (!isset($url['port']))
				$url['port']=80;
			$target=$url['host'].':'.$url['port'];
		}
		$socket=@fsockopen($target,$url['port'],$errno,$text);
		if (!$socket) {
			// Can't establish connection
			trigger_error($text);
			return FALSE;
		}
		// Set connection timeout parameters
		stream_set_blocking($socket,TRUE);
		stream_set_timeout($socket,ini_get('default_socket_timeout'));
		// Send HTTP request
		fputs($socket,
			$method.' '.$url['path'].
				(isset($url['query']) && $url['query']?
					('?'.$url['query']):'').' '.
					'HTTP/1.0'.self::EOL.
				self::HTTP_Host.': '.$url['host'].self::EOL.
				self::HTTP_Agent.': Mozilla/5.0 '.
					'(compatible;'.PHP_OS.')'.self::EOL.
				($reqhdrs?
					(implode(self::EOL,$reqhdrs).self::EOL):'').
				($method!='GET'?(
					'Content-Type: '.
						'application/x-www-form-urlencoded'.self::EOL.
					'Content-Length: '.strlen($query).self::EOL):'').
				self::HTTP_AcceptEnc.': gzip'.self::EOL.
				self::HTTP_Connect.': close'.self::EOL.self::EOL.
			$query.self::EOL.self::EOL
		);
		$found=FALSE;
		$expires=FALSE;
		$gzip=FALSE;
		$rcvhdrs='';
		$info=stream_get_meta_data($socket);
		// Get headers and response
		$response='';
		while (!feof($socket) && !$info['timed_out']) {
			$response.=fgets($socket,4096); // MDFK97
			$info=stream_get_meta_data($socket);
			if (!$found && is_int(strpos($response,self::EOL.self::EOL))) {
				$found=TRUE;
				$rcvhdrs=strstr($response,self::EOL.self::EOL,TRUE);
				ob_start();
				if ($follow &&
					preg_match('/HTTP\/1\.\d\s30\d/',$rcvhdrs)) {
					// Redirection
					preg_match('/'.self::HTTP_Location.
						':\s*(.+?)/',$rcvhdrs,$loc);
					return self::http($method.' '.$loc[1],$query,$reqhdrs);
				}
				foreach (explode(self::EOL,$rcvhdrs) as $hdr) {
					self::$vars['HEADERS'][]=$hdr;
					if (PHP_SAPI!='cli' && $forward)
						// Forward HTTP header
						header($hdr);
					elseif (preg_match('/^'.
						self::HTTP_Encoding.':\s*.*gzip/',$hdr))
						// Uncompress content
						$gzip=TRUE;
				}
				ob_end_flush();
				// Split content from HTTP response headers
				$response=substr(strstr($response,self::EOL.self::EOL),4);
			}
		}
		fclose($socket);
		if ($info['timed_out']) {
			trigger_error(self::TEXT_Timeout);
			return FALSE;
		}
		if (PHP_SAPI!='cli') {
			if ($gzip)
				$response=gzinflate(substr($response,10));
		}
		// Return content
		return $response;
	}

	/**
		Transmit a file for downloading by HTTP client; If kilobytes per
		second is specified, output is throttled (bandwidth will not be
		controlled by default); Return TRUE if successful, FALSE otherwise;
		Support for partial downloads is indicated by third argument
			@param $file string
			@param $kbps integer
			@param $partial
			@public
	**/
	static function send($file,$kbps=0,$partial=TRUE) {
		$file=subst($file);
		if (!is_file($file)) {
			error(404);
			return FALSE;
		}
		if (PHP_SAPI!='cli') {
			header(self::HTTP_Content.': application/octet-stream');
			header(self::HTTP_Partial.': '.($partial?'bytes':'none'));
			header(self::HTTP_Disposition.': '.
				'attachment; filename='.basename($file));
			header(self::HTTP_Length.': '.filesize($file));
			expire(0);
			ob_end_flush();
		}
		$max=ini_get('max_execution_time');
		$ctr=1;
		$handle=fopen($file,'r');
		$time=time();
		while (!feof($handle) && !connection_aborted()) {
			if ($kbps>0) {
				// Throttle bandwidth
				$ctr++;
				$elapsed=microtime(TRUE)-$time;
				if (($ctr/$kbps)>$elapsed)
					usleep(1e6*($ctr/$kbps-$elapsed));
			}
			// Send 1KiB and reset timer
			echo fread($handle,1024);
			set_time_limit($max);
		}
		fclose($handle);
		return TRUE;
	}

	/**
		Return TRUE if HTTP request origin is AJAX
			@return boolean
			@public
	**/
	static function isajax() {
		return $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest';
	}

	/**
		Class initializer
			@public
	**/
	static function onload() {
		if (!extension_loaded('sockets')) {
			// Sockets extension required
			self::$vars['CONTEXT']='sockets';
			trigger_error(self::TEXT_PHPExt);
		}
	}

}
