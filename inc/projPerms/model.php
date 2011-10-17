<?php

/**
 * projectpermission.php
 * 
 * wrapper class for Axon
 * 
 * @package ProjectPermission
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/

	namespace projPerms;

	class model extends \Axon
	{
		public function __construct()
		{
			$this->sync('ProjectPermission');
		}
	}
