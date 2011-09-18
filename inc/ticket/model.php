<?php

/**
 * ticket.php
 * 
 * wrapper class for Axon
 * 
 * @package Ticket
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/

class Ticket extends \Axon 
{
    public function __construct()
    {
        $this->sync('Ticket');
    }
}
