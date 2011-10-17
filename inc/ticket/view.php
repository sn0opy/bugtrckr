<?php

/**
 * ticket\view.php
 * 
 * Ticket view
 * 
 * @package ticket
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

namespace ticket;

class view extends \misc\controller 
{
	/**
	 *	Show a list of tickets of the project
	 */
    function showTickets()
    {   
		if (!ctype_alnum($this->get('SESSION.project')))
			return $this->tpfail('Please select a project.');

        $order = 'created';
		$search = '';
        
		if ($this->exists('SESSION.ticketSearch'))
			$search = $this->get('SESSION.ticketSearch');

		if($this->exists('POST.search'))
			$search = $this->get('POST.search');

        $this->set('SESSION.ticketOrder', $order);
		$this->set('SESSION.ticketSearch', $search);
        
        $project = $this->get('SESSION.project');

        $milestones = new \milestone\model();
        $milestones = $milestones->find(array('project = :project', array(':project' => $project)));

        $mshashs = array();
        foreach ($milestones as $ms)
            $mshashs[] = $ms->hash;
        $string = implode($mshashs, '\',\'');
            
        $tickets = new \ticket\displayable();
        $tickets = $tickets->find('milestone IN (\'' . $string . '\') AND ' .
                    'title LIKE \'%'.$search.'%\'' .
                    'ORDER BY ' . $order. ' DESC');

        $categories = new \category\model();
        $categories = $categories->find();

        $this->set('milestones', $milestones);
        $this->set('tickets', $tickets);
        $this->set('categories', $categories);
        $this->set('pageTitle', '{{@lng.tickets}}');
        $this->set('template', 'tickets.tpl.php');
        $this->set('onpage', 'tickets');
        $this->tpserve();
    }

    /**
     *	Show the details of a ticket 
     */
    function showTicket()
	{ 
        $hash = $this->get('PARAMS.hash');

        $ticket = new \ticket\displayable();
        $ticket->load(array("tickethash = :hash", array(':hash' => $hash)));

        if($ticket->dry())
            return $this->tpfail("Ticket doesn't exist.");

        $milestone = new \milestone\model();

        $activities = new DisplayableActivity();
        $activities = $activities->find(array("ticket = :ticket", array(':ticket' => $ticket->hash)));

        if (!$ticket->hash)
            return $this->tpfail("Can't open ticket");
   
        $users = new \user\model();
        
        $this->set('ticket', $ticket);
        $this->set('milestones', $milestone->find());
        $this->set('activities', $activities);        
        $this->set('users', $users = $users->find());
        $this->set('pageTitle', '{{@lng.tickets}} › ' .$ticket->title);
        $this->set('template', 'ticket.tpl.php');
        $this->set('onpage', 'tickets');
        $this->tpserve();
    }
    
}
?>
