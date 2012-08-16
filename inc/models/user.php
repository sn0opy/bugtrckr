<?php

/**
 * user\model.php
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
namespace models;

class User extends \Axon 
{
    public function __construct()
    {
        $this->sync('User');
    }
}
