<?php

	class User_ticket extends Axon 
	{
		public function __construct()
		{
			$this->sync('user_ticket');
		}
	}
