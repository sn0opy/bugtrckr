<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Ticket extends Controller {
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
    function addTicket($f3) {
		if(!$f3)
			$f3 = Base::instance();
	
        if (!helper::getPermission('iss_addIssues'))
            return $this->tpfail($f3->get('lng.insuffPermissions'));

        $ticket = new \DB\SQL\Mapper($this->db, 'Ticket');
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

        if(!$ticket->_id)
            return $this->tpfail($f3->get('lng.saveTicketFail'));

        helper::addActivity($f3->get('lng.ticket') . ' ' . $ticket->title . ' ' . $f3->get('lng.added') . ".", $ticket->hash);

        $f3->reroute('/ticket/' . $ticket->hash);
    }

	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
    function editTicket($f3) {
        if (!is_numeric($f3->get('POST.state')) || $f3->get('POST.state') <= 0 || $f3->get('POST.state') > 5)
            return $this->tpfail($f3->get('lng.saveTicketFail'));

		if (!helper::getPermission('iss_editIssues'))
			return $this->tpfail($f3->get('lng.insuffPermissions'));	

        $hash = $f3->get('PARAMS.hash');

        $ticket = new DB\SQL\Mapper($this->db, 'Ticket');
        $ticket->load(array('hash = :hash', array(':hash' => $hash)));

        $changed = '';
                
        // get the diff stuff
        if($ticket->state != $f3->get('POST.state'))
            $changed[] = array('field' => 'state', 'from' => $ticket->state, 'to' => $f3->get('POST.state'));
        
        if($ticket->assigned != $f3->get('POST.assigned'))
            $changed[] = array('field' => 'assigned', 'from' => $ticket->assigned, 'to' => $f3->get('POST.assigned'));
        
        if($ticket->milestone != $f3->get('POST.milestone'))
            $changed[] = array('field' => 'milestone', 'from' => $ticket->milestone, 'to' => $f3->get('POST.milestone'));
        
        if($ticket->priority != $f3->get('POST.priority'))
            $changed[] = array('field' => 'priority', 'from' => $ticket->priority, 'to' => $f3->get('POST.priority'));
        
        $ticket->state = $f3->get('POST.state');
        $ticket->priority = $f3->get('POST.priority');
        
        if (ctype_alnum($f3->get('POST.assigned')))
            $ticket->assigned = $f3->get('POST.assigned');

        if (ctype_alnum($f3->get('POST.milestone')))
            $ticket->milestone = $f3->get('POST.milestone');

        $ticket->save();

        if (!$ticket->hash)
            return $this->tpfail($f3->get('lng.saveTicketFail'));

        helper::addActivity($f3->get('lng.ticket') . " '" .$ticket->title. "' " .$f3->get('lng.edited'), $ticket->hash, $f3->get('POST.comment'), json_encode($changed));
        
       $f3->reroute('/ticket/'.$hash);
    }
	
	
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
    function showTickets($f3) {
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

        $tickets = new DB\SQL\Mapper($this->db, 'displayableticket');
        $tickets = $tickets->find('milestone IN (\'' . $string . '\') AND ' .
                    'title LIKE \'%'.$search.'%\'' .
                    'ORDER BY ' . $order. ' DESC');

        $categories = new DB\SQL\Mapper($this->db, 'Category');
        $categories = $categories->find();

        $f3->set('milestones', $milestones);
        $f3->set('tickets', $tickets);
        $f3->set('categories', $categories);
        $f3->set('pageTitle', $f3->get('lng.tickets'));
        $f3->set('template', 'tickets.tpl.php');
        $f3->set('onpage', 'tickets');
    }

	
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
    function showTicket($f3) {
        $hash = $f3->get('PARAMS.hash');

        $ticket = new DB\SQL\Mapper($this->db, 'displayableticket');
        $ticket->load(array("tickethash = :hash", array(':hash' => $hash)));

        if($ticket->dry())
            return $this->tpfail($f3->get('lng.noTicket'));

		if (!helper::canRead($f3->get('SESSION.project')))
			return $this->tpfail($f3->get('lng.insuffPermissions'));

        $milestone = new DB\SQL\Mapper($this->db, 'Milestone');

        $activities = new DB\SQL\Mapper($this->db, 'displayableactivity');
        $activities = $activities->find(array("ticket = :ticket", array(':ticket' => $ticket->hash)));

        foreach($activities as $key => $activity) {
            $activities[$key]->changedFields = json_decode($activity->changedFields);
        }

        $users = new DB\SQL\Mapper($this->db, 'User');        
        $f3->set('ticket', $ticket);
        $f3->set('milestones', $milestone->find());
        $f3->set('activities', $activities);        
        $f3->set('users', $users = $users->find());
        $f3->set('pageTitle', $f3->get('lng.tickets') . ' › ' .$ticket->title);
        $f3->set('template', 'ticket.tpl.php');
        $f3->set('onpage', 'tickets');
    }
}
