<?php

/**
 * wrapper class for Axon
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/

class State extends Axon
{
    public function __construct()
    {
        $this->sync('Status'); // was too lazy to rename the table
    }
}
