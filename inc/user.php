<?php

	class User extends Axon 
	{
		public function __construct()
		{
			$this->sync('User');
		}
	}
