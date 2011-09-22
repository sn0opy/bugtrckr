<?php


/**
 * user_perms.php
 * 
 * wrapper class for Axon (table is a VIEW)
 * 
 * @package User_Perms
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/

namespace userPerms;

class model extends \Axon 
{
    public function __construct()
    {
        $this->sync('user_perms');
    }
}
