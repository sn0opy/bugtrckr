<?php

	class User_perms extends Axon 
	{
		public function __construct()
		{
			$this->sync('user_perms');
		}
	}
