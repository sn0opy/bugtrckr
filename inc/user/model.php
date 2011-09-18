<?php

/**
 * user.php
 * 
 * wrapper class for Axon
 * 
 * @package User
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/
namespace user;

class model extends \Axon 
{
    public function __construct()
    {
        $this->sync('User');
    }
}
