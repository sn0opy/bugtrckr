<?php

	class Milestone extends F3instance
	{

		private $id;
		private $hash;
		private $name;
		private $description;
		private $finished;
		private $project;

		private $ax;

		public function __construct()
		{
			parent::__construct();

			$this->ax = new Axon('Milestone');
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
			$this->ax->load('Hash = "' . $this->hash .'"');
			$this->ax->hash = $this->hash;
			$this->ax->name = $this->name;
			$this->ax->description = $this->description;
			$this->ax->finished = $this->finished;
			$this->ax->project = $this->project;
			$this->ax->save();
		
			if ($this->ax->_id != NULL && $this->ax->_id <= 0)
				throw new Exception();
		}

		/**
		 *
		 */
		public function load($stmt)
		{
			$this->ax->load($stmt);

			if (!$this->ax->dry())
			{
				$this->id = $this->ax->id;
				$this->hash = $this->ax->hash;
				$this->name = $this->ax->name;
				$this->description = $this->ax->description;
				$this->finished = $this->finished;
				$this->project = $this->project;
			}
			else
				throw new Exception();
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
