<?php

	class Activity extends F3instance
	{
		private $id;
		private $hash;
		private $description;
		private $user;
		private $project;
		private $changed;

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

		public function setDescription($description)
		{
			$this->description = $description;
		}

		public function getDescription()
		{
			return $this->description;
		}

		public function setUser($user)
		{
			$this->user = $user;
		}

		public function getUser()
		{
			return $this->user;
		}

		public function getChanged()
		{
			return $this->changed;
		}

		public function setProject($project)
		{
			$this->project = $project;
		}

		public function getProject()
		{
			return $this->project;
		}


		/**
		 *
		 */
		public function save()
		{
			$id = F3::get('DB')->sql("SELECT max(id)+1 as next FROM Activity");
			$stat = F3::get('DB')->sql("INSERT INTO Activity " .
					"(hash, description, user, changed, project) VALUES " .
					"('". md5($id[0]['next']) ."', '$this->description', " .
					"$this->user, " . time() . ", $this->project)");

			return is_array($stat) ? md5($id[0]['next']) : 0;
		}

		/**
		 *
		 */
		public function load($stmt)
		{
			$result = F3::get('DB')->sql("SELECT * FROM Activity WHERE $stmt");

			if (is_array($result))
			{
				$this->id = $result[0]['id'];
				$this->hash = $result[0]['hash'];
				$this->description = $result[0]['description'];
				$this->user = $result[0]['user'];
				$this->changed = $result[0]['changed'];
				$this->project = $result[0]['project'];
			}
		}

		/**
		 *
		 */
		public function toArray()
		{
			$activity = array();

			$activity['id'] = $this->id;
			$activity['hash'] = $this->hash;
			$activity['description'] = $this->description;
			$activity['user'] = Dao::getUserName($this->user);
			$activity['changed'] = date('d.m.Y H:i', $this->changed);
			$activity['project'] = $this->project;

			return $activity;
		}
	}
    