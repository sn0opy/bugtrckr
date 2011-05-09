<?php

/**
	SQL database plugin for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3::Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package DB
		@version 2.0.0
**/

//! SQL data access layer
class DB extends Base {

	public
		//! Exposed data object properties
		$backend,$pdo,$result;
	private
		//! Connection parameters
		$dsn,$user,$pw,$opt;

	/**
		Force PDO instantiation
			@public
	**/
	function instantiate() {
		$this->pdo=new PDO($this->dsn,$this->user,$this->pw,$this->opt);
	}

	/**
		Begin SQL transaction
			@public
	**/
	function begin() {
		if (!$this->pdo)
			self::instantiate();
		$this->pdo->beginTransaction();
	}

	/**
		Rollback SQL transaction
			@public
	**/
	function rollback() {
		if (!$this->pdo)
			self::instantiate();
		$this->pdo->rollback();
	}

	/**
		Commit SQL transaction
			@public
	**/
	function commit() {
		if (!$this->pdo)
			self::instantiate();
		$this->pdo->commit();
	}

	/**
		Process SQL statement(s)
			@return array
			@param $cmds mixed
			@param $args array
			@param $ttl int
			@public
	**/
	function exec($cmds,array $args=NULL,$ttl=0) {
		if (!$this->pdo)
			self::instantiate();
		$stats=&self::ref('STATS');
		if (!isset($stats[$this->dsn]))
			$stats[$this->dsn]=array(
				'cache'=>array(),
				'queries'=>array()
			);
		$batch=is_array($cmds);
		if (!$batch) {
			$cmds=array($cmds);
			$args=array($args);
		}
		elseif (!$this->pdo->inTransaction())
			$this->pdo->beginTransaction();
		foreach (array_combine($cmds,$args) as $cmd=>$arg) {
			$hash='sql.'.self::hash($cmd);
			$cached=Cache::cached($hash);
			if ($ttl && $cached && $_SERVER['REQUEST_TIME']-$cached<$ttl) {
				// Gather cached queries for profiler
				if (!isset($stats[$this->dsn]['cache'][$cmd]))
					$stats[$this->dsn]['cache'][$cmd]=0;
				$stats[$this->dsn]['cache'][$cmd]++;
				$this->result=Cache::get($hash);
			}
			else {
				if (is_null($arg))
					$query=$this->pdo->query($cmd);
				elseif (is_object($query=$this->pdo->prepare($cmd))) {
					foreach ($arg as $key=>$value)
						if (is_array($value))
							$query->bindvalue($key,$value[0],$value[1]);
						else
							$query->bindvalue($key,$value,
								$this->type($value));
					$query->execute();
				}
				// Check SQLSTATE
				foreach (array($this->pdo,$query) as $obj)
					if ($obj->errorCode()!=PDO::ERR_NONE) {
						$this->pdo->rollback();
						$error=$obj->errorinfo();
						trigger_error($error[2]);
						return FALSE;
					}
				$this->result=preg_match(
					'/^\s*(?:INSERT|UPDATE|DELETE)\s/i',$cmd)?
						$query->rowCount():
						$query->fetchall(PDO::FETCH_ASSOC);
				if ($ttl)
					Cache::set($hash,$this->result,$ttl);
				// Gather real queries for profiler
				if (!isset($stats[$this->dsn]['queries'][$cmd]))
					$stats[$this->dsn]['queries'][$cmd]=0;
				$stats[$this->dsn]['queries'][$cmd]++;
			}
		}
		if ($batch && !$this->pdo->inTransaction())
			$this->pdo->commit();
		return $this->result;
	}

	/**
		Convenience method for direct SQL queries (static call)
			@return array
			@param $cmds mixed
			@param $args mixed
			@param $ttl int
			@param $db string
			@public
	**/
	static function sql($cmds,array $args=NULL,$ttl=0,$db='DB') {
		return self::$vars[$db]->exec($cmds,$args,$ttl);
	}

	/**
		Return auto-detected PDO data type of specified value
			@return int
			@param $val mixed
			@public
	**/
	function type($val) {
		foreach (
			array(
				'null'=>'NULL',
				'bool'=>'BOOL',
				'string'=>'STR',
				'int'=>'INT',
				'float'=>'STR'
			) as $php=>$pdo)
			if (call_user_func('is_'.$php,$val))
				return constant('PDO::PARAM_'.$pdo);
		return PDO::PARAM_LOB;
	}

	/**
		Class constructor
			@param $dsn string
			@param $user string
			@param $pw string
			@param $opt array
			@param $force boolean
			@public
	**/
	function __construct($dsn,$user=NULL,$pw=NULL,$opt=NULL,$force=FALSE) {
		if (!isset(self::$vars['MYSQL']))
			// Default MySQL character set
			self::$vars['MYSQL']='utf8';
		if (!$opt)
			// Append other default options
			$opt=array(PDO::ATTR_EMULATE_PREPARES=>FALSE)+(
				extension_loaded('pdo_mysql') &&
				preg_match('/^mysql:/',$dsn)?
					array(PDO::MYSQL_ATTR_INIT_COMMAND=>
						'SET NAMES '.self::$vars['MYSQL']):array()
			);
		list($this->dsn,$this->user,$this->pw,$this->opt)=
			array($this->resolve($dsn),$user,$pw,$opt);
		$this->backend=strstr($this->dsn,':',TRUE);
		if (!isset(self::$vars['DB']))
			self::$vars['DB']=$this;
		if ($force)
			$this->pdo=new PDO($this->dsn,$this->user,$this->pw,$this->opt);
	}

}

//! Axon ORM
class Axon extends Base {

	//@{ Locale-specific error/exception messages
	const
		TEXT_AxonConnect='Undefined database',
		TEXT_AxonTable='Unable to map table %s to Axon',
		TEXT_AxonEmpty='Axon is empty',
		TEXT_AxonArray='Must be an array of Axon objects',
		TEXT_AxonNotMapped='The field %s does not exist',
		TEXT_AxonCantUndef='Cannot undefine an Axon-mapped field',
		TEXT_AxonCantUnset='Cannot unset an Axon-mapped field',
		TEXT_AxonConflict='Name conflict with Axon-mapped field',
		TEXT_AxonInvalid='Invalid virtual field expression',
		TEXT_AxonReadOnly='Virtual fields are read-only',
		TEXT_AxonEngine='Database engine is not supported';
	//@}

	//@{
	//! Axon properties
	public
		$_id;
	private
		$db,$table,$pkeys,$fields,$adhoc,$mod,$empty,$cond,$seq,$ofs;
	//@}

	/**
		Axon factory
			@return object
			@param $row array
			@private
	**/
	private function factory($row) {
		$axon=new self($this->table,$this->db);
		foreach ($row as $field=>$val) {
			if (array_key_exists($field,$this->fields)) {
				$axon->fields[$field]=$val;
				if (is_array($this->pkeys) &&
					array_key_exists($field,$this->pkeys))
					$axon->pkeys[$field]=$val;
			}
			else
				$axon->adhoc[$field]=array($this->adhoc[$field][0],$val);
			if ($axon->empty && $val)
				$axon->empty=FALSE;
		}
		return $axon;
	}

	/**
		Return current record contents as an array
			@return array
			@public
	**/
	function cast() {
		return $this->fields;
	}

	/**
		SQL select statement wrapper
			@return array
			@param $fields string
			@param $cond mixed
			@param $group string
			@param $seq string
			@param $limit int
			@param $ofs int
			@public
	**/
	function select(
		$fields=NULL,$cond=NULL,$group=NULL,$seq=NULL,$limit=0,$ofs=0) {
		$rows=is_array($cond)?
			$this->db->exec(
				'SELECT '.($fields?:'*').' FROM '.$this->table.
					($cond?(' WHERE '.$cond[0]):'').
					($group?(' GROUP BY '.$group):'').
					($seq?(' ORDER '.$seq):'').
					($limit?(' LIMIT '.$limit):'').
					($ofs?(' OFFSET '.$ofs):'').';',
				$cond[1]
			):
			$this->db->exec(
				'SELECT '.($fields?:'*').' FROM '.$this->table.
					($cond?(' WHERE '.$cond):'').
					($group?(' GROUP BY '.$group):'').
					($seq?(' ORDER '.$seq):'').
					($limit?(' LIMIT '.$limit):'').
					($ofs?(' OFFSET '.$ofs):'').';'
			);
		// Convert array elements to Axon objects
		foreach ($rows as &$row)
			$row=$this->factory($row);
		return $rows;
	}

	/**
		Retrieve all records that match criteria
			@return array
			@param $cond mixed
			@param $seq string
			@param $limit int
			@param $ofs int
			@public
	**/
	function find($cond=NULL,$seq=NULL,$limit=0,$ofs=0) {
		$adhoc='';
		if ($this->adhoc)
			foreach ($this->adhoc as $field=>$val)
				$adhoc.=','.$val[0].' AS '.$field;
		return $this->select('*'.$adhoc,$cond,NULL,$seq,$limit,$ofs);
	}

	/**
		Retrieve first record that matches criteria
			@return array
			@param $cond mixed
			@param $seq string
			@param $ofs int
			@public
	**/
	function findone($cond=NULL,$seq=NULL,$ofs=0) {
		list($result)=$this->find($cond,$seq,1,$ofs)?:array(NULL);
		return $result;
	}

	/**
		Count records that match condition
			@return int
			@param $cond mixed
			@public
	**/
	function found($cond=NULL) {
		$this->def('_found','COUNT(*)');
		list($result)=$this->find($cond);
		$found=$result->_found;
		$this->undef('_found');
		return $found;
	}

	/**
		Dehydrate Axon
			@public
	**/
	function reset() {
		foreach (array_keys($this->fields) as $field)
			$this->fields[$field]=NULL;
		if ($this->pkeys)
			foreach (array_keys($this->pkeys) as $pkey)
				$this->pkeys[$pkey]=NULL;
		if ($this->adhoc)
			foreach (array_keys($this->adhoc) as $adhoc)
				$this->adhoc[$adhoc][1]=NULL;
		$this->empty=TRUE;
		$this->mod=NULL;
		$this->cond=NULL;
		$this->seq=NULL;
		$this->ofs=0;
	}

	/**
		Hydrate Axon with first record that matches criteria
			@return mixed
			@param $cond mixed
			@param $seq string
			@param $ofs int
			@public
	**/
	function load($cond=NULL,$seq=NULL,$ofs=0) {
		if ($ofs>-1) {
			$this->ofs=0;
			if ($axon=$this->findone($cond,$seq,$ofs)) {
				if (method_exists($this,'beforeLoad') &&
					!$this->beforeLoad())
					return;
				// Hydrate Axon
				foreach ($axon->fields as $field=>$val) {
					$this->fields[$field]=$val;
					if (array_key_exists($field,$this->pkeys))
						$this->pkeys[$field]=$val;
				}
				if ($axon->adhoc)
					foreach ($axon->adhoc as $field=>$val)
						$this->adhoc[$field][1]=$val[1];
				list($this->empty,$this->cond,$this->seq,$this->ofs)=
					array(FALSE,$cond,$seq,$ofs);
				if (method_exists($this,'afterLoad'))
					$this->afterLoad();
				return $this;
			}
		}
		$this->reset();
		return FALSE;
	}

	/**
		Hydrate Axon with nth record relative to current position
			@return mixed
			@param $ofs int
			@public
	**/
	function skip($ofs=1) {
		if ($this->dry()) {
			trigger_error(self::TEXT_AxonEmpty);
			return FALSE;
		}
		return $this->load($this->cond,$this->seq,$this->ofs+$ofs);
	}

	/**
		Return next record
			@return array
			@public
	**/
	function next() {
		return $this->skip();
	}

	/**
		Return previous record
			@return array
			@public
	**/
	function prev() {
		return $this->skip(-1);
	}

	/**
		Insert record/update database
			@public
	**/
	function save() {
		if ($this->dry() ||
			method_exists($this,'beforeSave') && !$this->beforeSave())
			return;
		$new=TRUE;
		if ($this->pkeys)
			// If all primary keys are NULL, this is a new record
			foreach ($this->pkeys as $pkey)
				if (!is_null($pkey)) {
					$new=FALSE;
					break;
				}
		if ($new) {
			// Insert record
			$fields=$values='';
			foreach ($this->fields as $field=>$val) {
				$fields.=($fields?',':'').$field;
				$values.=($values?',':'').':'.$field;
				$bind[':'.$field]=array($val,$this->db->type($val));
			}
			$this->db->exec(
				'INSERT INTO '.$this->table.' ('.$fields.') '.
					'VALUES ('.$values.');',$bind);
			$this->_id=$this->db->pdo->lastinsertid();
		}
		else {
			// Update record
			$set=$cond='';
			foreach ($this->fields as $field=>$val)
				if (isset($this->mod[$field])) {
					$set.=($set?',':'').$field.'=:'.$field;
					$bind[':'.$field]=array($val,$this->db->type($val));
				}
			// Use primary keys to find record
			foreach ($this->pkeys as $pkey=>$val) {
				$cond.=($cond?' AND ':'').$pkey.'=:c_'.$pkey;
				$bind[':c_'.$pkey]=array($val,$this->db->type($val));
			}
			if ($set)
				$this->db->exec(
					'UPDATE '.$this->table.' SET '.$set.
						($cond?(' WHERE '.$cond):'').';',$bind);
		}
		if ($this->pkeys)
			// Update primary keys with new values
			foreach (array_keys($this->pkeys) as $pkey)
				$this->pkeys[$pkey]=$this->fields[$pkey];
		if (method_exists($this,'afterSave'))
			$this->afterSave();
	}

	/**
		Delete record/s
			@param $force boolean
			@public
	**/
	function erase($force=FALSE) {
		if (method_exists($this,'beforeErase') && !$this->beforeErase())
			return;
		$this->db->exec('DELETE FROM '.$this->table.
			(($cond=$this->cond)?(' WHERE '.$cond):($force?'':'FALSE')).';');
		$this->reset();
		if (method_exists($this,'afterErase'))
			$this->afterErase();
	}

	/**
		Return TRUE if Axon is empty
			@return bool
			@public
	**/
	function dry() {
		return $this->empty;
	}

	/**
		Hydrate Axon with elements from array variable;
		Adhoc fields are not modified
			@param $name string
			@param $keys string
			@public
	**/
	function copyFrom($name,$keys=NULL) {
		$var=self::ref($name);
		$keys=is_null($keys)?array_keys($var):explode('|',$keys);
		foreach ($keys as $key)
			if (in_array($key,array_keys($var)) &&
				in_array($key,array_keys($this->fields)))
				$this->fields[$key]=$var[$key];
		$this->empty=FALSE;
	}

	/**
		Populate array variable with Axon properties
			@param $name string
			@param $keys string
			@public
	**/
	function copyTo($name,$keys=NULL) {
		$list=array_diff(explode('|',$keys),array(''));
		$keys=array_keys($this->fields);
		$adhoc=$this->adhoc?array_keys($this->adhoc):NULL;
		foreach ($adhoc?array_merge($keys,$adhoc):$keys as $key)
			if (empty($list) || in_array($key,$list)) {
				$var=&self::ref($name);
				if (in_array($key,array_keys($this->fields)))
					$var[$key]=$this->fields[$key];
				if ($this->adhoc &&
					in_array($key,array_keys($this->adhoc)))
					$var[$key]=$this->adhoc[$key];
			}
	}

	/**
		Synchronize Axon and SQL table structure
			@param $table string
			@param $db object
			@param $freq int
			@public
	**/
	function sync($table,$db=NULL,$freq=60) {
		if (!$db) {
			if (isset(self::$vars['DB']) && is_a(self::$vars['DB'],'DB'))
				$db=self::$vars['DB'];
			else {
				trigger_error(self::TEXT_AxonConnect);
				return;
			}
		}
		// DB schema
		$result=array(
			'mysql'=>array(
				'SHOW columns FROM '.$table.';','Field','Key','PRI'),
			'sqlite2?'=>array(
				'PRAGMA table_info('.$table.');','name','pk',1),
			'(mssql|sybase|dblib|pgsql)'=>array(
				'SELECT c.column_name AS field,t.constraint_type AS key '.
				'FROM information_schema.columns AS c '.
				'LEFT OUTER JOIN '.
					'information_schema.key_column_usage AS k ON '.
						'c.table_name=k.table_name AND '.
						'c.column_name=k.column_name '.
				'LEFT OUTER JOIN '.
					'information_schema.table_constraints AS t ON '.
						'k.table_name=t.table_name AND '.
						'k.constraint_name=t.constraint_name '.
				'WHERE '.
					'c.table_name="'.$table.'";','field','key','PRIMARY KEY')
		);
		$match=FALSE;
		foreach ($result as $dsn=>$val)
			if (preg_match('/^'.$dsn.'$/',$db->backend)) {
				$match=TRUE;
				break;
			}
		if (!$match) {
			trigger_error(self::TEXT_AxonEngine);
			return;
		}
		if (method_exists($this,'beforeSync') && !$this->beforeSync())
			return;
		// Initialize Axon
		list($this->db,$this->table)=array($db,$table);
		$rows=$db->exec($val[0],NULL,$freq);
		if (!$rows) {
			trigger_error(sprintf(self::TEXT_AxonTable,$table));
			return;
		}
		// Populate properties
		foreach ($rows as $row) {
			$this->fields[$row[$val[1]]]=NULL;
			if ($row[$val[2]]==$val[3])
				// Save primary key
				$this->pkeys[$row[$val[1]]]=NULL;
		}
		$this->empty=TRUE;
		if (method_exists($this,'afterSync'))
			$this->afterSync();
	}

	/**
		Create an adhoc field
			@param $field string
			@param $expr string
			@public
	**/
	function def($field,$expr) {
		if (isset($this->fields[$field])) {
			trigger_error(self::TEXT_AxonConflict);
			return;
		}
		$this->adhoc[$field]=array($expr,NULL);
	}

	/**
		Destroy an adhoc field
			@param $field string
			@public
	**/
	function undef($field) {
		if (isset($this->fields[$field]) || !self::isdef($field)) {
			trigger_error(sprintf(self::TEXT_AxonCantUndef,$field));
			return;
		}
		unset($this->adhoc[$field]);
	}

	/**
		Return TRUE if adhoc field exists
			@param $field string
			@public
	**/
	function isdef($field) {
		return isset($this->adhoc[$field]);
	}

	/**
		Return value of mapped field
			@return mixed
			@param $field string
			@public
	**/
	function __get($field) {
		if (isset($this->fields[$field]))
			return $this->fields[$field];
		if (self::isdef($field))
			return $this->adhoc[$field][1];
		return FALSE;
	}

	/**
		Assign value to mapped field
			@return bool
			@param $field string
			@param $val mixed
			@public
	**/
	function __set($field,$val) {
		if (array_key_exists($field,$this->fields)) {
			if ($this->fields[$field]!=$val)
				$this->mod[$field]=1;
			$this->fields[$field]=$val;
			if (!is_null($val))
				$this->empty=FALSE;
			return TRUE;
		}
		if (self::isdef($field))
			trigger_error(self::TEXT_AxonReadOnly);
		return FALSE;
	}

	/**
		Trigger error in case a field is unset
			@param $field string
			@public
	**/
	function __unset($field) {
		trigger_error(str_replace('@FIELD',$field,self::TEXT_AxonCantUnset));
	}

	/**
		Return TRUE if mapped field is set
			@return bool
			@param $field string
			@public
	**/
	function __isset($field) {
		return isset($this->fields[$field]);
	}

	/**
		Class constructor
			@public
	**/
	function __construct() {
		// Execute mandatory sync method
		call_user_func_array(
			array(get_called_class(),'sync'),func_get_args());
	}

}
