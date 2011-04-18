<?php

	class Milestone extends F3instance
	{

		private $id;
		private $hash;
		private $name;
		private $description;
		private $finished;
		private $project;

		public function __construct()
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

		public function setDescription($description)
		{
			$this->description = $description;
		}

		public function getDescription()
		{
			return $this->description;
		}

		public function setFinished($finished)
		{
			$this->finished = $finished;
		}

		public function getFinished()
		{
			return $this->finished;
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
			if ($this->id > 0)
			{
				$stat = F3::get('DB')->sql("UPDATE Milestone SET " .
						"finished = $finished" .
						"WHERE id = $this->id");

				return is_array($stat) ? $this->hash : 0;
			}
			else
			{
				$id = F3::get('DB')->sql("SELECT max(id)+1 as next FROM Milestone");
				$stat = F3::get('DB')->sql("INSERT INTO Milestone ".
						"(hash, name, description, finished, project) VALUES ".
						"('". md5($id[0]['next']) ."', '$this->name', ".
						"'$this->description', $this->finished, $this->project)");

				return is_array($stat) ? md5($id[0]['next']) : 0;
			}
		}

		/**
		 *
		 */
		public function load($stmt)
		{
			$result = F3::get('DB')->sql("SELECT * FROM Milestone WHERE $stmt");

			if (is_array($result))
			{
				$this->id = $result[0]['id'];
				$this->hash = $result[0]['hash'];
				$this->name = $result[0]['name'];
				$this->description = $result[0]['description'];
				$this->finished = $result[0]['finished'];
				$this->project = $result[0]['project'];
			}
		}

		/**
		 *
		 */
		public function toArray()
		{
			$milestone = array();

			$milestone['id'] = $this->id;
			$milestone['hash'] = $this->hash;
			$milestone['name'] = $this->name;
			$milestone['description'] = $this->description;
			$milestone['finished'] = date('d.m.Y', $this->finished);
			$milestone['project'] = $this->project;

			return $milestone;
		}
	}
