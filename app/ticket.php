<?php

/**
 * Ticket controller
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2013, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Ticket extends Controller {

	/**
	 * 
	 * @return type
	 */
    function addTicket($f3) {
        if (!helper::getPermission('iss_addIssues'))
            return $this->tpfail($f3->get('lng.insuffPermissions'));

        $ticket = new \DB\SQL\Mapper($f3->db, 'Ticket');
        $ticket->hash = helper::getFreeHash('Ticket');
        $ticket->title = $f3->get('POST.title');
        $ticket->description = $f3->get('POST.description');
        $ticket->owner = $f3->get('SESSION.user.hash');
        $ticket->assigned = 0; // do not assign to anyone
        $ticket->type = $f3->get('POST.type');
        $ticket->state = 1;
        $ticket->created = time();
        $ticket->priority = $f3->get('POST.priority');
        $ticket->category = $f3->get('POST.category');
        $ticket->milestone = $f3->get('POST.milestone');
        $ticket->save();

        if (!$ticket->_id)
            return $this->tpfail($f3->get('lng.saveTicketFail'));

        helper::addActivity($f3->get('lng.ticket') . " '$f3->title' " . $f3->get('lng.added') . ".", $ticket->hash);

        $f3->reroute('/ticket/' . $ticket->hash);
    }

    /**
     *	Updates a Ticket in the database
     */
    function editTicket()
    {
        if (!is_numeric($this->get('POST.state')) || $this->get('POST.state') <= 0 || $this->get('POST.state') > 5)
            return $this->tpfail($this->get('lng.saveTicketFail'));

		if (!\misc\helper::getPermission('iss_editIssues'))
			return $this->tpfail($this->get('lng.insuffPermissions'));	

        $hash = $this->get('PARAMS.hash');

        $ticket = new \models\Ticket();
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
            return $this->tpfail($this->get('lng.saveTicketFail'));

        \misc\helper::addActivity($this->get('lng.ticket') . " '" .$ticket->title. "' " .$this->get('lng.edited'), $ticket->hash, $this->get('POST.comment'), json_encode($changed));
        
       $this->reroute('/ticket/'.$hash);
    }
	
	/**
	 *	Show a list of tickets of the project
	 */
    function showTickets($f3)
    {
		if (!ctype_alnum($f3->get('SESSION.project')))
			return $this->tpfail($f3->get('lng.noProject'));

		if (!helper::canRead($f3->get('SESSION.project')))
			return $this->tpfail($f3->get('lng.insuffPermissions'));

        $order = 'created';
		$search = '';
        
		if ($f3->exists('SESSION.ticketSearch'))
			$search = $f3->get('SESSION.ticketSearch');

		if($f3->exists('POST.search'))
			$search = $f3->get('POST.search');

        $f3->set('SESSION.ticketOrder', $order);
		$f3->set('SESSION.ticketSearch', $search);
        
        $project = $f3->get('SESSION.project');

        $milestones = new DB\SQL\Mapper($this->db, 'Milestone');
        $milestones = $milestones->find(array('project = :project', array(':project' => $project)));

        $mshashs = array();
        foreach ($milestones as $ms)
            $mshashs[] = $ms->hash;
        $string = implode($mshashs, '\',\'');

        $tickets = new \models\Displayableticket();
        $tickets = $tickets->find('milestone IN (\'' . $string . '\') AND ' .
                    'title LIKE \'%'.$search.'%\'' .
                    'ORDER BY ' . $order. ' DESC');

        $categories = new DB\SQL\Mapper($this->db, 'Category');
        $categories = $categories->find();

        $this->set('milestones', $milestones);
        $this->set('tickets', $tickets);
        $this->set('categories', $categories);
        $this->set('pageTitle', $f3->get('lng.tickets'));
        $this->set('template', 'tickets.tpl.php');
        $this->set('onpage', 'tickets');
        $this->tpserve();
    }

    /**
     *	Show the details of a ticket 
     */
    function showTicket($f3)
	{
        $hash = $f3->get('PARAMS.hash');

        $ticket = new \models\Displayableticket();
        $ticket->load(array("tickethash = :hash", array(':hash' => $hash)));

        if($ticket->dry())
            return $this->tpfail($f3->get('lng.noTicket'));

		if (!\misc\helper::canRead($f3->get('SESSION.project')))
			return $this->tpfail($f3->get('lng.insuffPermissions'));

        $milestone = new DB\SQL\Mapper($this->db, 'Milestone');

        $activities = new \models\DisplayableActivity();
        $activities = $activities->find(array("ticket = :ticket", array(':ticket' => $ticket->hash)));

        foreach($activities as $key => $activity) {
            $activities[$key]->changedFields = json_decode($activity->changedFields);
        }

        $users = new \models\User();
        
        $this->set('ticket', $ticket);
        $this->set('milestones', $milestone->find());
        $this->set('activities', $activities);        
        $this->set('users', $users = $users->find());
        $this->set('pageTitle', $f3->get('lng.tickets') . ' › ' .$ticket->title);
        $this->set('template', 'ticket.tpl.php');
        $this->set('onpage', 'tickets');
        $this->tpserve();
    }
}
