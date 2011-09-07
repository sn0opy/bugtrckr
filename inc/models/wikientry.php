<?php

/**
 * wikientry.php
 * 
 * wrapper class for Axon
 * 
 * @package WikiEntry
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

	class WikiEntry extends Axon
	{
		public function __construct()
		{
			$this->sync('WikiEntry');
		}
	}
