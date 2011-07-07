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
        $order = $this->get('PARAMS.order') ? $this->get('PARAMS.order') : "id";
        $project = $this->get('SESSION.project');

        $milestones = new Milestone();
        $milestones = $milestones->find('project = ' . $project);

        $string = '';
        foreach ($milestones as $ms)
            $string .= $ms->id . ',';

        $tickets = new DisplayableTicket();
        $tickets = $tickets->find('milestone IN (' . $string . '0) ORDER BY ' . $order);

        $categories = new Category();
        $categories = $categories->find();

        $this->set('milestones', $milestones);
        $this->set('tickets', $tickets);
        $this->set('categories', $categories);
        $this->set('pageTitle', '{{@lng.tickets}}');
        $this->set('template', 'tickets.tpl.php');
        $this->tpserve();
    }

    /**
     *
     */
    function showTicket()
    {
        $hash = $this->get('PARAMS.hash');

        $ticket = new DisplayableTicket();
        $ticket = $ticket->findone("tickethash = '$hash'");

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
        if (!$this->helper->getPermission('iss_addIssues'))
        {
            $this->tpfail('You are not allowed to add tickets.');
            return;
        }

        $ticket = new Ticket();
        $ticket->hash = $this->helper->getFreeHash('Ticket');
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
            $this->get('lng.ticket') . " '$ticket->title' " . $this->get('lng.ticket.added') . ".", $ticket->_id);

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

        helper::addActivity(
            $this->get('lng.ticket') . " '$ticket->title' " .
            $this->get('lng.edited_by') . " '<a href=\"/user/" . $this->get('SESSION.user.name') . "\">" .
            $this->get('SESSION.user.name') . "</a>'.", $ticket->id);

        $this->set('PARAMS["hash"]', $hash);
        $this->showTicket($hash);
    }

}