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

    function showRoadmap()
    {
        $ms = array();
        $fullCount = 0;

        $helper = new helper();

        $milestones = new Milestone();
        $milestones = $milestones->find('project = ' . $this->get('SESSION.project'));

        foreach ($milestones as $milestone)
        {
            $ms[$milestone->id]['infos'] = $milestone;
            $ms[$milestone->id]['ticketCount'] = $helper->getTicketCount($milestone->id);

            $ms[$milestone->id]['fullTicketCount'] = 0;
            foreach ($ms[$milestone->id]['ticketCount'] as $cnt)
                $ms[$milestone->id]['fullTicketCount'] += $cnt['count'];

            $ms[$milestone->id]['openTickets'] = 0;
            foreach ($ms[$milestone->id]['ticketCount'] as $j => $cnt)
            {
                $ms[$milestone->id]['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $ms[$milestone->id]['fullTicketCount']);

                if ($ms[$milestone->id]['ticketCount'][$j]['state'] != 5)
                    $ms[$milestone->id]['openTickets'] += $ms[$milestone->id]['ticketCount'][$j]['count'];
            }
        }

        $this->set('road', $ms);
        $this->set('pageTitle', '{{@lng.roadmap}}');
        $this->set('template', 'roadmap.tpl.php');
        $this->tpserve();
    }

    function showMilestone()
    {
        $hash = $this->get('PARAMS.hash');

        $helper = new helper();

        $milestone = new Milestone();
        $milestone->load(array('hash = :hash', array(':hash' => $hash)));

        if($milestone->dry())
        {
            $this->tpfail('No such milestone exists');
            return;
        }

        $ticket = new DisplayableTicket();
        $tickets = $ticket->find('milestone = ' . $milestone->id);

        $ms['ticketCount'] = $helper->getTicketCount($milestone->id);

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
        $this->tpserve();
    }

    /**
     * 
     */
    function addEditMilestone()
    {
        $msHash = $this->get('POST.hash') ? $this->get('POST.hash') : helper::getFreeHash('Milestone');

        $milestone = new Milestone();
        if (F3::exists('POST.hash'))
        {
            $milestone->load('hash = "' . $msHash . '"');
            if ($milestone->dry())
            {
                $this->tpfail('Failure while editing milestone.');
                return;
            }
        }

        $milestone->name = $this->get('POST.name');
        $milestone->hash = $msHash;
        $milestone->description = $this->get('POST.description');
        $milestone->project = $this->get('SESSION.project');
        $milestone->finished = $this->get('POST.finished');
        $milestone->save();

        $this->reroute($this->get('BASE') . '/project/settings/milestone/' . $msHash);
    }

}