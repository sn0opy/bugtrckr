<?php

/**
 * cticket.php
 * 
 * Ticket controller
 * 
 * @package ticket
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
class cticket extends Controller
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

        $milestones = new Milestone();
        $milestones = $milestones->find(array('project = :project', array(':project' => $project)));

        $mshashs = array();
        foreach ($milestones as $ms)
            $mshashs[] = $ms->hash;
        $string = implode($mshashs, '\',\'');
            
        $tickets = new DisplayableTicket();
        $tickets = $tickets->find('milestone IN (\'' . $string . '\') AND ' .
                    'title LIKE \'%'.$search.'%\'' .
                    'ORDER BY ' . $order. ' DESC');

        $categories = new Category();
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

        $ticket = new DisplayableTicket();
        $ticket->load(array("tickethash = :hash", array(':hash' => $hash)));

        if($ticket->dry())
            return $this->tpfail("Ticket doesn't exist.");

        $milestone = new Milestone();

        $activities = new DisplayableActivity();
        $activities = $activities->find(array("ticket = :ticket", array(':ticket' => $ticket->hash)));

        if (!$ticket->hash)
            return $this->tpfail("Can't open ticket");

        $state = new State();    
        $users = new User();
        
        $this->set('ticket', $ticket);
        $this->set('milestones', $milestone->find());
        $this->set('activities', $activities);        
        $this->set('users', $users = $users->find());
        $this->set('states', $state->find('lang = "' .$this->get('LANGUAGE'). '"'));
        $this->set('pageTitle', '{{@lng.tickets}} › ' .$ticket->title);
        $this->set('template', 'ticket.tpl.php');
        $this->set('onpage', 'tickets');
        $this->tpserve();
    }

    /**
     *	Add Ticket into the database
     */
    function addTicket()
    {
        if (!helper::getPermission('iss_addIssues'))
            return $this->tpfail('You are not allowed to add tickets.');

        $ticket = new Ticket();
        $ticket->hash = helper::getFreeHash('Ticket');
        $ticket->title = $this->get('POST.title');
        $ticket->description = $this->get('POST.description');
        $ticket->owner = $this->get('SESSION.user.id');
        $ticket->assigned = 0; // do not assign to anyone
        $ticket->type = $this->get('POST.type');
        $ticket->state = 1;
        $ticket->created = time();
        $ticket->priority = $this->get('POST.priority');
        $ticket->category = $this->get('POST.category');
        $ticket->milestone = $this->get('POST.milestone');
        $ticket->save();

        if (!$ticket->_id)
            return $this->tpfail("Failure while saving Ticket");

        helper::addActivity(
            $this->get('lng.ticket') . " '$ticket->title' " . $this->get('lng.added') . ".", $ticket->_id);

        $this->reroute($this->get('BASE') . '/ticket/' . $ticket->hash);
    }

    /**
     *	Updates a Ticket in the database
     */
    function editTicket()
    {
		if (!is_numeric($this->get('POST.state')) || 
			$this->get('POST.state') <= 0 || 
			$this->get('POST.state') > 5)
			return $this->tpfail("Failure while saving Ticket");

        $hash = $this->get('PARAMS.hash');

        $ticket = new Ticket();
        $ticket->load(array('hash = :hash', array(':hash' => $hash)));

        $milestone = new cmilestone();
        
        $ticket->state = $this->get('POST.state');
		if (is_numeric($this->get('POST.user')))
			$ticket->assigned = $this->get('POST.user');
		if (ctype_alnum($this->get('POST.milestone')))
        	$ticket->milestone = $milestone->getMilestoneID($this->get('POST.milestone'));

        $ticket->save();

        if (!$ticket->hash)
            return $this->tpfail("Failure while saving Ticket");

        helper::addActivity(
			$this->get('lng.ticket') . " '" .$ticket->title. "' " .$this->get('lng.edited'), $ticket->hash, $this->get('POST.comment'));

        $this->set('PARAMS["hash"]', $hash);
        $this->showTicket($hash);
    }
}
