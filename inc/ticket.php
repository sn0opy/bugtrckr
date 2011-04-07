<?php

	class Ticket extends F3instance
	{

		private $id;
		private $hash;
		private $title;
		private $description;
		private $owner;
		private $type;
		private $state;
		private $priority;
		private $category;
		private $project;

		function __construct()
		{
			parent::__construct();
		}

		public function setId($id)
		{
			$this->id = $id;
		}

		public function setTitle($title)
		{
			$this->title = $title;
		}

		public function setDescription($description)
		{
			$this->description = $description;
		}

		public function setOwner($owner)
		{
			$this->owner = $owner;
		}

		public function setType($type)
		{
			$this->type = $type;
		}

		public function setState($state)
		{
			$this->state = $state;
		}

		public function setPriority($priority)
		{
			$this->priority = $priority;
		}

		public function setCategory($category)
		{
			$this->category = $category;
		}

		public function setProject($project)
		{
			$this->project = $project;
		}

		public function getHash()
		{
			return $this->hash;
		}

		/**
		 * 
		 */
		public function save()
		{
			$db = F3::get('DB');
			$db = new DB($db['dsn']);

			if ($this->id > 0)
				return	$db->sql("UPDATE Ticket SET " .
						"owner = $this->owner, " .
						"state = $this->state, " .
						"priority = $this->priority " .
						"WHERE id = $this->id");
			else
				return 	$db->sql("INSERT INTO Ticket " .
						"(hash, title, description, owner, type, state, " .
						"priority, category, project) VALUES " .
						"('MD5(id)', '$this->title', '$this->description',".
						" $this->owner, $this->type, $this->state," .
						" $this->priority, $this->category, $this->project)");

		}

	}
