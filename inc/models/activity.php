<?php

	class Activity extends Axon
	{
		public function __construct()
		{
			$this->sync('Activity');
		}
	}
