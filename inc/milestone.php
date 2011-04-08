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


		public function setId($id)
		{
			$this->id = $id;
			$this->hash = md5($id);
		}

		public function setName($name)
		{
			$this->name = $name;
		}

		public function setDescription($description)
		{
			$this->description = $description;
		}

		public function setFinished($finished)
		{
			$this->finished = $finished;
		}

		public function setProject($project)
		{
			$this->project = $project;
		}


		public function save()
		{
			$db = F3::get('DB');
			$db = new DB($db['dsn']);

			if ($this->id > 0)
			{
				$stat = $db->sql("UPDATE Milestone SET " .
						"finished = $finished" .
						"WHERE id = $this->id");

				return is_array($stat) ? $this->hash : 0;
			}
			else
			{
				$id = $db->sql("SELECT max(id)+1 as next FROM Milestone");
				$stat = $db->sql("INSERT INTO Milestone ".
						"(hash, name, description, finished, project) VALUES ".
						"('". md5($id[0]['next']) ."', '$this->name', ".
						"'$this->description', $this->finished, $this->project)";

				return is_array($stat) ? md5($id[0]['next']) : 0;
			}
		}
	}
