<?php

	class Ticket extends F3instance
	{

		private $id;

		function __construct()
		{
			parent::__construct();
			$this->id = rand();
		}


		public function getId()
		{
			return $this->id;
		}

	}
