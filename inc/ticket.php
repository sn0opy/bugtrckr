<?php

	class Ticket extends F3instance
	{

		private $id;
		private $hash;
		private $title;
		private $description;
		private $created;
		private $owner;
        private $assigned;
		private $type;
		private $state;
		private $priority;
		private $category;
		private $milestone;

        private $ax;		

		function __construct()
		{
			parent::__construct();

            $this->ax = new Axon('Ticket');			
		}

		public function getId()
		{
			return $this->id;
		}

		public function setHash($hash)
		{
			$this->hash = $hash;
		}
		
		public function getHash()
		{
			return $this->hash;
		}

		public function setTitle($title)
		{
			$this->title = $title;
		}

		public function getTitle()
		{
			return $this->title;
		}

		public function setDescription($description)
		{
			$this->description = $description;
		}

		public function getDescription()
		{
			return $this->description;
		}

		public function getCreated()
		{
			return $this->created;
		}

		public function setOwner($owner)
		{
			$this->owner = $owner;
		}

		public function getOwner()
		{
			return $this->owner;
		}

        public function setAssigned($assigned)
        {
            $this->assigned = $assigned;
        }

        public function getAssigned()
        {
            return $this->assigned;
        }

		public function setType($type)
		{
			$this->type = $type;
		}

		public function getType()
		{
			return $this->type;
		}

		public function setState($state)
		{
			$this->state = $state;
		}

		public function getState()
		{
			return $this->state;
		}

		public function setPriority($priority)
		{
			$this->priority = $priority;
		}

		public function getPriority($priority)
		{
			return $this->priority;
		}

		public function setCategory($category)
		{
			$this->category = $category;
		}

		public function getCategory()
		{
			return $this->category;
		}

		public function setMilestone($milestone)
		{
			$this->milestone = $milestone;
		}

		public function getMilestone()
		{
			return $this->milestone;
		}

		/**
		 * 
		 */
		public function save()
		{
			$this->ax->load('hash = "' .$this->hash. '"');
			$this->ax->hash = $this->hash;
			$this->ax->title = $this->title;
			$this->ax->description = $this->description;
			$this->ax->owner = $this->owner;
			$this->ax->assigned = $this->assigned;
			$this->ax->type = $this->type;
			$this->ax->state = $this->state;
			$this->ax->priority = $this->priority;
			$this->ax->category = $this->category;
			$this->ax->milestone = $this->milestone;
			$this->ax->time = isset($this->time) ? $this->time : time();

			$this->ax->save();

			if ($this->ax->_id != NULL && $this->ax->_id <= 0)
				throw new Exception();
		}

		/*
		 *
		 */
		public function load($stmt)
		{
			$this->ax->load($stmt);
			
			if (!$this->ax->dry())
			{
				$this->id = $this->ax->id;
				$this->hash = $this->ax->hash;
				$this->title = $this->ax->title;
				$this->description = $this->ax->description;
				$this->created = $this->ax->created;
				$this->owner = $this->ax->owner;
                $this->assigned = $this->ax->assigned;
				$this->type = $this->ax->type;
				$this->state = $this->ax->state;
				$this->priority = $this->ax->priority;
				$this->category = $this->ax->category;
				$this->milestone =  $this->ax->milestone;
			}
			else
				throw new Exception();
		}


		/*
		 *
		 */
		public function toArray()
		{
			$ticket = array();
			$ticket_state = F3::get('ticket_state');
			$ticket_type = F3::get('ticket_type');
			$ticket_priority = F3::get('ticket_priority');
			
			$ticket['id'] = $this->id;
			$ticket['hash'] = $this->hash;
			$ticket['title'] = $this->title;
			$ticket['description'] = $this->description;
			$ticket['created'] = date('d.m.Y H:i', $this->created);
			$ticket['owner'] = Dao::getUserName($this->owner);
            $ticket['assigned'] = Dao::getUserName($this->assigned);
			$ticket['type'] = $ticket_type[$this->type];
			$ticket['state'] = $ticket_state[$this->state];
			$ticket['priority'] = $ticket_priority[$this->priority];
			$ticket['category'] = $this->category;
			$ticket['milestone'] = $this->milestone;

			return $ticket;
		}
	}
