<?php

	require_once F3::get('BASE').'lib/db.php';

	class Activity extends Axon
	{
		public function __construct()
		{
			$this->sync('Activity');
		}

	}

	class Milestone extends Axon 
	{
		public function __construct()
		{
			$this->sync('Milestone');		
		}
	}

	class Project extends Axon 
	{
		public function __construct()
		{
			$this->sync('Project');			
		}
	}

	class ProjectPermissions extends Axon
	{
		public function __construct()
		{
			$this->sync('ProjectPermissions');
		}
	}

	class Role extends Axon 
	{
		public function __construct()
		{
			$this->sync('Role');
		}
	}

	class Ticket extends Axon 
	{
		public function __construct()
		{
			$this->sync('Ticket');
		}
	}

	class User extends Axon 
	{
		public function __construct()
		{
			$this->sync('User');
		}
	}
