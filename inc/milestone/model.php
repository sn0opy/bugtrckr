<?php

/**
 * milestone.php
 * 
 * wrapper class for Axon
 * 
 * @package Milestone
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/
namespace milestone;

class model extends \Axon 
{
    public function __construct()
    {
        $this->sync('Milestone');		
    }
}
