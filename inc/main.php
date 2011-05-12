<?php

/**
 * main.php
 * 
 * Everything comes together in here
 * 
 * @package Main
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/

class main extends F3instance
{
    function start()
    {
        F3::set('pageTitle', '{{@lng.home}}');
        F3::set('template', 'home.tpl.php');
        $this->tpserve();
    }
    
    function showTickets()
    {
        $order = F3::get('PARAMS.order') ? F3::get('PARAMS.order') : "id";
        $project = F3::get('SESSION.project');
        
        $milestones = new Milestone();
        $milestones = $milestones->find('project = ' .$project);
        
        $string = '';
        foreach($milestones as $ms)
            $string .= $ms->id.',';
        
        $tickets = new user_ticket();
        $tickets = $tickets->find('milestone IN (' .$string. '0)');
        
        F3::set('milestones', $milestones);
        F3::set('tickets', $tickets);
        F3::set('pageTitle', '{{@lng.tickets}}');
        F3::set('template', 'tickets.tpl.php');
        $this->tpserve();
    }
    
    function tpserve()
    {
        echo Template::serve('main.tpl.php');
    }
}
