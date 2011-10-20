<?php

	namespace wiki;

	class WikiDiscussion extends \Axon
	{
		public function __construct()
		{
			$this->sync('WikiDiscussion');
		}
	}
