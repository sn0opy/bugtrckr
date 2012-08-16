<?php

/**
 * models\displayableactivity.php
 * 
 * wrapper class for Axon (table is a VIEW)
 * 
 * @package User_Ticket
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/
namespace models;

class DisplayableActivity extends \Axon 
{
    public function __construct()
    {
        $this->sync('displayableactivity');
    }
}
