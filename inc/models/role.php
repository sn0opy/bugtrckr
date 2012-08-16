<?php

/**
 * role.php
 * 
 * wrapper class for Axon
 * 
 * @package Role
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/

namespace models;

class Role extends \Axon 
{
    public function __construct()
    {
        $this->sync('Role');
    }
}
