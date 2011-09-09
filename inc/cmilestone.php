<?php

/**
 * cmilestone.php
 * 
 * Milestone controller
 * 
 * @package milestone
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
class cmilestone extends Controller
{

	/**
	 *	Display a roadmap that contains the milestones of a project
	 */
    function showRoadmap()
    {
		if (!ctype_alnum($this->get('SESSION.project')))
			return $this->tpfail('Please select a project first.');

        $ms = array();
        $fullCount = 0;

        $project = $this->get('SESSION.project');		// Actual project

		// Get the milestones
        $milestones = new Milestone();
        $milestones = $milestones->find(array('project = :project', array(':project' => $project)));

		// Calculate the details of each milestone 
        foreach ($milestones as $milestone)
        {
            $ms[$milestone->id]['infos'] = $milestone;
            $ms[$milestone->id]['ticketCount'] = helper::getTicketCount($milestone->hash);

            $ms[$milestone->id]['fullTicketCount'] = 0;
            foreach ($ms[$milestone->id]['ticketCount'] as $cnt)
                $ms[$milestone->id]['fullTicketCount'] += $cnt['count'];

            $ms[$milestone->id]['openTickets'] = 0;
            foreach ($ms[$milestone->id]['ticketCount'] as $j => $cnt)
            {
                $ms[$milestone->id]['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $ms[$milestone->hash]['fullTicketCount']);

                if ($ms[$milestone->id]['ticketCount'][$j]['state'] != 5)
                    $ms[$milestone->id]['openTickets'] += $ms[$milestone->id]['ticketCount'][$j]['count'];
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
        $hash = $this->get('PARAMS.hash');

        $milestone = new Milestone();
        $milestone->load(array('hash = :hash', array(':hash' => $hash)));

        if($milestone->dry())
            return $this->tpfail('The milestone doesn\'t exist.');

        $ticket = new DisplayableTicket();
        $tickets = $ticket->find(array('milestone = :hash', array(':hash' => $milestone->hash)));

        $ms['ticketCount'] = helper::getTicketCount($milestone->hash);

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

    /**
     *	Save a milestone to the database
     */
    function addEditMilestone($projHash = false)
    {
        $name = ($projHash) ? 'First milestone' : $this->get('POST.name');
        
        if(!isset($projHash)) {
            // This params have to be set
            if ($this->get('POST.name') == "" ||
                $this->get('SESSION.project') <= 0)
                return $this->tpfail('Failure while editing milestone.');
        }

        $msHash = $this->get('POST.hash') ? $this->get('POST.hash') : helper::getFreeHash('Milestone');

        $milestone = new Milestone();
        if (F3::exists('POST.hash'))
        {
            $milestone->load(array('hash = :hash', array(':hash' => $msHash)));
            if ($milestone->dry())
                return $this->tpfail('Failure while editing milestone.');
        }

        $milestone->name = $name;
        $milestone->hash = $msHash;
        $milestone->description = ($projHash) ? 'My first milestone' : $this->get('POST.description');
        $milestone->project = ($projHash) ? $projHash : $this->get('SESSION.project');
        $milestone->finished = ($projHash) ? time()+2629743 : $this->get('POST.finished');
        $milestone->save();

        if(!$projHash)
            $this->reroute($this->get('BASE') . '/project/settings/milestone/' . $msHash);
    }
}
