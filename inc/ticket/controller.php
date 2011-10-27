<?php

/**
 * ticket\controller.php
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
namespace ticket;

class controller extends \misc\controller
{

    /**
     *	Add Ticket into the database
     */
    function addTicket()
    {
        if (!\misc\helper::getPermission('iss_addIssues'))
            return $this->tpfail('You are not allowed to add tickets.');

        $ticket = new \ticket\model();
        $ticket->hash = \misc\helper::getFreeHash('Ticket');
        $ticket->title = $this->get('POST.title');
        $ticket->description = $this->get('POST.description');
        $ticket->owner = $this->get('SESSION.user.hash');
        $ticket->assigned = 0; // do not assign to anyone
        $ticket->type = $this->get('POST.type');
        $ticket->state = 1;
        $ticket->created = time();
        $ticket->priority = $this->get('POST.priority');
        $ticket->category = $this->get('POST.category');
        $ticket->milestone = $this->get('POST.milestone');
        $ticket->save();

        if (!$ticket->_id)
            return $this->tpfail($this->get('lng.failTicketSave'));

        \misc\helper::addActivity(
            $this->get('lng.ticket') . " '$ticket->title' " . $this->get('lng.added') . ".", $ticket->hash);

        $this->reroute($this->get('BASE') . '/ticket/' . $ticket->hash);
    }

    /**
     *	Updates a Ticket in the database
     */
    function editTicket()
    {
        if (!is_numeric($this->get('POST.state')) || $this->get('POST.state') <= 0 || $this->get('POST.state') > 5)
            return $this->tpfail($this->get('lng.failTicketSave'));

		if (!\misc\helper::getPermission('iss_editIssues'))
			return $this->tpfail('You don\'t have the permissions to do this');	

        $hash = $this->get('PARAMS.hash');

        $ticket = new \ticket\model();
        $ticket->load(array('hash = :hash', array(':hash' => $hash)));

        $changed = '';
                
        // get the diff stuff
        if($ticket->state != $this->get('POST.state'))
            $changed[] = array('field' => 'state', 'from' => $ticket->state, 'to' => $this->get('POST.state'));
        
        if($ticket->assigned != $this->get('POST.assigned'))
            $changed[] = array('field' => 'assigned', 'from' => $ticket->assigned, 'to' => $this->get('POST.assigned'));
        
        if($ticket->milestone != $this->get('POST.milestone'))
            $changed[] = array('field' => 'milestone', 'from' => $ticket->milestone, 'to' => $this->get('POST.milestone'));
        
        if($ticket->priority != $this->get('POST.priority'))
            $changed[] = array('field' => 'priority', 'from' => $ticket->priority, 'to' => $this->get('POST.priority'));
        
        $ticket->state = $this->get('POST.state');
        $ticket->priority = $this->get('POST.priority');
        
        if (ctype_alnum($this->get('POST.assigned')))
            $ticket->assigned = $this->get('POST.assigned');

        if (ctype_alnum($this->get('POST.milestone')))
            $ticket->milestone = $this->get('POST.milestone');

        $ticket->save();

        if (!$ticket->hash)
            return $this->tpfail($this->get('lng.failTicketSave'));

        \misc\helper::addActivity($this->get('lng.ticket') . " '" .$ticket->title. "' " .$this->get('lng.edited'), $ticket->hash, $this->get('POST.comment'), json_encode($changed));
        
       $this->reroute($this->get('BASE').'/ticket/'.$hash);
    }
}
