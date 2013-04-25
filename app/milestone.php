<?php

/**
 * Milestone controller
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

class Milestone extends Controller
{

    /**
     *	Save a milestone to the database
     */
    function addEditMilestone($projHash = false)
    {
        if (!\misc\helper::getPermission('proj_manageMilestones'))
            return $this->tpfail($this->get('lng.insuffPermissions'));

        $name = ($projHash) ? $this->get('lng.firstMilestone') : $this->get('POST.name');
        
        if(!isset($projHash)) {
            // This params have to be set
            if ($this->get('POST.name') == "" || $this->get('SESSION.project') <= 0)
                return $this->tpfail($this->get('lng.failMilestoneSave'));
        }

        $msHash = $this->get('POST.hash') ? $this->get('POST.hash') : \misc\helper::getFreeHash('Milestone');

        $milestone = new \models\Milestone();
        if ($this->exists('POST.hash'))
        {
            $milestone->load(array('hash = :hash', array(':hash' => $msHash)));
            if ($milestone->dry())
                return $this->tpfail($this->get('lng.failMilestoneSave'));
        }

        $milestone->name = $name;
        $milestone->hash = $msHash;
        $milestone->description = ($projHash) ? $this->get('lng.firstMilestone') : $this->get('POST.description');
        $milestone->project = ($projHash) ? $projHash : $this->get('SESSION.project');
        $milestone->finished = ($projHash) ? time()+2629743 : $this->get('POST.finished');
        $milestone->save();

        if(!$projHash)
            $this->reroute('/project/settings#milestones');
    }
    
    function deleteMilestone() 
    {
        if (!\misc\helper::getPermission('proj_manageMilestones'))
            return $this->tpfail($this->get('lng.insuffPermissions'));
        
        $msHash = $this->get('PARAMS.hash');
        
        $tickets = new \models\Ticket();
        $milestones = new \models\Milestone();
        
        if($tickets->found(array('milestone = :ms', array(':ms' => $msHash))) < 1 && $milestones->found() > 1) {            
            $milestones->load(array('hash = :hash', array(':hash' => $msHash)));
            $milestones->erase();
            
            $this->set('SESSION.SUCCESS', $this->set('lng.milestonedDeleted'));
            $this->reroute('/project/settings#milestones');
        } else {
            $this->tpfail($this->get('lng.cannotDeleteMilestone'));
        }
    }
	

    /**
     *	Display a roadmap that contains the milestones of a project
     */
    function showRoadmap()
    {
		if (!ctype_alnum($this->get('SESSION.project')))
			return $this->tpfail($this->get('lng.noProject'));

		if (!\misc\helper::canRead($this->get('SESSION.project')))
			return $this->tpfail($this->get('lng.insuffPermissions'));

        $ms = array();
        $fullCount = 0;

        $project = $this->get('SESSION.project');		// Actual project

		// Get the milestones
        $milestones = new \models\Milestone();
        $milestones = $milestones->find(array('project = :project', array(':project' => $project)));

		// Calculate the details of each milestone 
        foreach ($milestones as $milestone)
        {
            $ms[$milestone->hash]['infos'] = $milestone;
            $ms[$milestone->hash]['ticketCount'] = \misc\helper::getTicketCount($milestone->hash);

            $ms[$milestone->hash]['fullTicketCount'] = 0;
            foreach ($ms[$milestone->hash]['ticketCount'] as $cnt)
                $ms[$milestone->hash]['fullTicketCount'] += $cnt['count'];

            $ms[$milestone->hash]['openTickets'] = 0;
            foreach ($ms[$milestone->hash]['ticketCount'] as $j => $cnt)
            {
                $ms[$milestone->hash]['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $ms[$milestone->hash]['fullTicketCount']);

                if ($ms[$milestone->hash]['ticketCount'][$j]['state'] != 5)
                    $ms[$milestone->hash]['openTickets'] += $ms[$milestone->hash]['ticketCount'][$j]['count'];
            }
        }

        $this->set('road', $ms);
        $this->set('pageTitle', '{{@lng.roadmap}}');
        $this->set('template', 'roadmap.tpl.php');
        $this->set('onpage', 'roadmap');
        $this->tpserve();            
    }

	/**
	 *	Displaying the tickets and the status of a milestone
	 */
    function showMilestone()
    {
		if (!ctype_alnum($this->get('SESSION.project')))
			return $this->tpfail($this->get('lng.noProject'));

		if (!\misc\helper::canRead($this->get('SESSION.project')))
			return $this->tpfail($this->get('lng.insuffPermissions'));

        $hash = $this->get('PARAMS.hash');

        $milestone = new \models\Milestone();
        $milestone->load(array('hash = :hash', array(':hash' => $hash)));

        if($milestone->dry())
            return $this->tpfail('The milestone doesn\'t exist.');

        $ticket = new \models\Displayableticket();
        $tickets = $ticket->find(array('milestone = :hash', array(':hash' => $milestone->hash)));

        if($milestone->dry())
            return $this->tpfail('The milestone tickets can\'t be loaded.');

        $ms['ticketCount'] = \misc\helper::getTicketCount($milestone->hash);

        $ms['fullTicketCount'] = 0;
        foreach ($ms['ticketCount'] as $cnt)
            $ms['fullTicketCount'] += $cnt['count'];

        $ms['openTickets'] = 0;
        foreach ($ms['ticketCount'] as $j => $cnt)
        {
            $ms['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $ms['fullTicketCount']);

            if ($ms['ticketCount'][$j]['state'] != 5)
                $ms['openTickets'] += $ms['ticketCount'][$j]['count'];
        }

        $this->set('tickets', $tickets);
        $this->set('stats', $ms);
        $this->set('milestone', $milestone);
        $this->set('pageTitle', '{{@lng.milestone}} › ' . $milestone->name);
        $this->set('template', 'milestone.tpl.php');
        $this->set('onpage', 'roadmap');
        $this->tpserve();
    }
}
