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
			if ($this->id > 0)
			{
				$stat = F3::get('DB')->sql("UPDATE Ticket SET " .
						"owner = $this->owner, " .
                        "assigned = $this->assigned, " .
						"state = $this->state, " .
						"priority = $this->priority, " .
						"milestone = $this->milestone ".
						"WHERE id = $this->id");
				return is_array($stat) ? $this->hash : 0;
			}
			else
			{

                $helper = new helper();
                $hash = $helper->getFreeHash('Ticket');
                
				$stat = F3::get('DB')->sql("INSERT INTO Ticket " .
						"(hash, title, description, owner, assigned, type, state, " .
						"priority, category, milestone, created) VALUES " .
						"('".$hash."', '$this->title', '$this->description',".
						" $this->owner, $this->assigned, $this->type, $this->state," .
						" $this->priority, '$this->category', $this->milestone, ".
						time() .")");

				return is_array($stat) ? $hash : 0;
			}
		}

		/*
		 *
		 */
		public function load($stmt)
		{
			$result = F3::get('DB')->sql("SELECT * FROM Ticket WHERE $stmt");

			if (is_array($result))
			{
				$this->id = $result[0]['id'];
				$this->hash = $result[0]['hash'];
				$this->title = $result[0]['title'];
				$this->description = $result[0]['description'];
				$this->created = $result[0]['created'];
				$this->owner = $result[0]['owner'];
                $this->assigned = $result[0]['assigned'];
				$this->type = $result[0]['type'];
				$this->state = $result[0]['state'];
				$this->priority = $result[0]['priority'];
				$this->category = $result[0]['category'];
				$this->milestone = $result[0]['milestone'];
			}
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
