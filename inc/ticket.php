<?php

	class Ticket extends Axon 
	{
		public function __construct()
		{
			$this->sync('Ticket');
		}
	}
