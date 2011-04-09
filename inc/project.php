<?php


	class Project extends F3instance
	{
		private $id;
		private $hash;
		private $name;

		function __construct()
		{
			parent::__construct();
		}


		public function getId()
		{
			return $this->id;
		}

		public function getHash()
		{
			return $this->hash;
		}

		public function setName($name)
		{
			$this->name = $name;
		}

		public function getName()
		{
			return $this->name;
		}

		/**
		 *
		 */
		public function save()
		{
			$db = new DB(F3::get('DB.dsn'));

			if ($this->id > 0)
			{
				$stat = $db->sql("UPDATE Project SET name = $this->name " .
						"WHERE id = $this->id");

				return is_array($stat) ? $this->hash : 0;
			}
			else
			{
				$id = $db->sql("SELECT max(id)+1 as next FROM Project");
				$stat = $db->sql("INSERT INTO Project " .
						"(hash, name) VALUES " .
						"('". md5($id[0]['next']) ."', '$this->name')");

				return is_array($stat) ? md5($id[0]['next']) : 0;
			}
		}

		/**
		 *
		 */
		public function load($stmt)
		{
			$db = new DB(F3::get('DB.dsn'));

			$result = $db->sql("SELECT * FROM Project WHERE $stmt");

			if (is_array($result))
			{
				$this->id = $result[0]['id'];
				$this->hash = $result[0]['hash'];
				$this->name = $result[0]['name'];
			}
		}


		/**
		 *
		 */
		public function toArray()
		{
			$project = array();

			$project['id'] = $this->id;
			$project['hash'] = $this->hash;
			$project['name'] = $this->name;

			return $project;
		}
	}
