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

    function showTickets()
    {     
        $order = 'id';
		$search = '';
        
        if($this->exists('SESSION.ticketOrder'))
            $order = $this->get('SESSION.ticketOrder'); // we could also reroute to /tickets/@SESSION.ticketOrder

		if ($this->exists('SESSION.ticketSearch'))
			$search = $this->get('SESSION.ticketSearch');
        
        if($this->exists('PARAMS.order'))
            $order = $this->get('PARAMS.order');

		if($this->exists('POST.search'))
			$search = $this->get('POST.search');

        $this->set('SESSION.ticketOrder', $order);
		$this->set('SESSION.ticketSearch', $search);
        
        $project = $this->get('SESSION.project');

        if($project) {        
            $milestones = new Milestone();
            $milestones = $milestones->find('project = ' . $project);

            $msids = array();
            foreach ($milestones as $ms)
                $msids[] = $ms->id;
            $string = implode($msids, ',');

            $tickets = new DisplayableTicket();
            $tickets = $tickets->find('milestone IN (' . $string . ') AND ' .
                        'title LIKE \'%'.$search.'%\'' .
                        'ORDER BY ' . $order);

            $categories = new Category();
            $categories = $categories->find();

            $this->set('milestones', $milestones);
            $this->set('tickets', $tickets);
            $this->set('categories', $categories);
            $this->set('pageTitle', '{{@lng.tickets}}');
            $this->set('template', 'tickets.tpl.php');
            $this->tpserve();
        } else {
            $this->set('pageTitle', '{{@lng.tickets}}');
            $this->set('template', 'tickets.tpl.php');
            $this->set('SESSION.failure', '{{@lng.noProject}}');
            $this->tpserve();
        }
    }

    /**
     *
     */
    function showTicket()
    {
        $hash = $this->get('PARAMS.hash');

        $ticket = new DisplayableTicket();
        $ticket->load(array("tickethash = :hash", array(':hash' => $hash)));

        if($ticket->dry()) {
            $this->tpfail("There's no such ticket");
            return;
        }

        $milestone = new Milestone();
        $milestone->load(array('id = :id', array(':id' => $ticket->milestone)));

        $activities = new DisplayableActivity();
        $activities = $activities->find("ticket = " . $ticket->id);

        if (!$ticket->id || !$milestone->id)
        {
            $this->tpfail("Can't open ticket");
            return;
        }

        $state = new State();    
        $users = new User();
        
        $this->set('ticket', $ticket);
        $this->set('milestone', $milestone);
        $this->set('activities', $activities);        
        $this->set('users', $users = $users->find());
        $this->set('states', $state->find('lang = "' .$this->get('LANGUAGE'). '"'));
        $this->set('pageTitle', '{{@lng.tickets}} › ' . $ticket->title);
        $this->set('template', 'ticket.tpl.php');
        $this->tpserve();
    }

    /**
     *
     */
    function addTicket()
    {
        if (!helper::getPermission('iss_addIssues'))
        {
            $this->tpfail('You are not allowed to add tickets.');
            return;
        }

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
        {
            $this->tpfail("Failure while saving Ticket");
            return;
        }

        helper::addActivity(
            $this->get('lng.ticket') . " '$ticket->title' " . $this->get('lng.added') . ".", $ticket->_id);

        $this->reroute($this->get('BASE') . '/ticket/' . $ticket->hash);
    }

    /**
     *
     */
    function editTicket()
    {
        $hash = $this->get('PARAMS.hash');

        $ticket = new Ticket();
        $ticket->load("hash = '$hash'");

        $ticket->assigned = $this->get('POST.userId');
        $ticket->state = $this->get('POST.state');

        $ticket->save();

        if (!$ticket->id)
        {
            $this->tpfail("Failure while saving Ticket");
            return;
        }

        helper::addActivity($this->get('lng.ticket') . " '" .$ticket->title. "' " .$this->get('lng.edited'), $ticket->id, $this->get('POST.comment'));

        $this->set('PARAMS["hash"]', $hash);
        $this->showTicket($hash);
    }

}
