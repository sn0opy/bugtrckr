<?php

namespace milestone;

class view {

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
        $milestones = new \milestone\model();
        $milestones = $milestones->find(array('project = :project', array(':project' => $project)));

		// Calculate the details of each milestone 
        foreach ($milestones as $milestone)
        {
            $ms[$milestone->id]['infos'] = $milestone;
            $ms[$milestone->id]['ticketCount'] = \misc\helper::getTicketCount($milestone->hash);

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

        $milestone = new \milestone\model();
        $milestone->load(array('hash = :hash', array(':hash' => $hash)));

        if($milestone->dry())
            return $this->tpfail('The milestone doesn\'t exist.');

        $ticket = new DisplayableTicket();
        $tickets = $ticket->find(array('milestone = :hash', array(':hash' => $milestone->hash)));

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
?>