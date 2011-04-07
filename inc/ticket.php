<?php

	class Ticket extends F3instance
	{

		private $id;
		private $title;
		private $description;
		private $owner;
		private $type;
		private $state;
		private $priority;
		private $category;

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

		/** 
		 *
		 */
		public function save()
		{
			if ($id > 0)
				return 
				F3::sql("UPDATE Ticket SET " .
						"owner = " . $this->owner->getId() . ", ".
						"state = $this->state, " .
						"priority = $this->priority ".
						"WHERE id = $this->id");
			else
				return
				F3::sql("INSERT INTO " .
						"(title, description, owner, type, state, ".
						"priority, category) VALUES ".
						"('$this->title', '$this->description', ". 
						$owner->getId() .", $this->type, $this->state, " .
						"$this->priority, $this->category)"));
		}

	}
