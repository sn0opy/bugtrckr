<?php

/**
 * project.php
 * 
 * wrapper class for Axon
 * 
 * @package Project
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/

namespace project;

class Model extends \Axon 
{
    public function __construct()
    {
        $this->sync('Project');			
    }
}
