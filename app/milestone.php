<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Milestone extends Controller
{
	/**
	 * 
	 * @param type $f3
	 * @param type $projHash
	 * @return type
	 */
  function addEditMilestone($f3 = false, $url = false, $projHash = false)
  {
    if(!$f3)
      $f3 = Base::instance();

    $f3->get("log")->write("Calling /project/settings/milestone/edit or addEditMilestone is called by projectAdd");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));

    if(!Helper::getPermission('proj_manageMilestones'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $name = $projHash ? $f3->get('lng.firstMilestone') : $f3->get('POST.name');
        
    if(!isset($projHash) && $f3->get('POST.name') == "" || $f3->get('SESSION.project') <= 0)
      return $this->tpfail( $f3->get('lng.failMilestoneSave'),
                            "projHash = $projHash, POST.name = " . $f3->get('POST.name') . ", SESSION.project = " . $f3->get('SESSION.project'));

    $msHash = $f3->get('POST.hash') ? $f3->get('POST.hash') : Helper::getFreeHash('Milestone');

    $milestone = new DB\SQL\Mapper($this->db, 'Milestone');
    if($f3->exists('POST.hash'))
    {
      $milestone->load(array('hash = :hash', array(':hash' => $msHash)));
      if($milestone->dry())
        return $this->tpfail($f3->get('lng.failMilestoneSave'));
    }

		$milestone->name = $name;
    $milestone->hash = $msHash;
    $milestone->description = $projHash ? $f3->get('lng.firstMilestone') : $f3->get('POST.description');
    $milestone->project = $projHash ? $projHash : $f3->get('SESSION.project');
    $milestone->finished = $projHash ? time()+2629743 : $f3->get('POST.finished');
    $milestone->save();

    if($f3->exists('POST.hash'))
      $f3->set('SESSION.SUCCESS', $f3->get('lng.milestoneEdited'));
    else
      $f3->set('SESSION.SUCCESS', $f3->get('lng.milestoneAdded'));
		
    if(!$projHash)
      $f3->reroute('/project/settings#milestones');
  }
	
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  function deleteMilestone($f3)
  {
    $f3->get("log")->write("Calling /project/settings/milestone/delete/@hash with @hash = " . $f3->get('PARAMS.hash'));

    if (!Helper::getPermission('proj_manageMilestones'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));
        
    $msHash = $f3->get('PARAMS.hash');
        
    $tickets = new DB\SQL\Mapper($this->db, 'Ticket');
    $milestones = new DB\SQL\Mapper($this->db, 'Milestone');
        
    if($tickets->count(array('milestone = :ms', array(':ms' => $msHash))) < 1 && $milestones->count() > 1)
    {
      $milestones->load(array('hash = :hash', array(':hash' => $msHash)));
      $milestones->erase();
            
      $f3->set('SESSION.SUCCESS', $f3->get('lng.milestonedDeleted'));
      $f3->reroute('/project/settings#milestones');
    }
    else
      $this->tpfail($f3->get('lng.cannotDeleteMilestone'));
  }

	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  function showRoadmap($f3)
  {
    $f3->get("log")->write("Calling /roadmap");

    if (!ctype_alnum($f3->get('SESSION.project')))
      return $this->tpfail($f3->get('lng.noProject'));

    if (!Helper::canRead($f3->get('SESSION.project')))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $ms = array();
    $fullCount = 0;

    $project = $f3->get('SESSION.project');

    // Get the milestones
    $milestones = new DB\SQL\Mapper($this->db, 'Milestone');
    $milestones = $milestones->find(array('project = :project', array(':project' => $project)));

		// Calculate the details of each milestone 
    foreach ($milestones as $milestone)
    {
      $ms[$milestone->hash]['infos'] = $milestone;
      $ms[$milestone->hash]['ticketCount'] = Helper::getTicketCount($milestone->hash);

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

    $f3->set('road', $ms);
    $f3->set('pageTitle', $f3->get('lng.roadmap'));
    $f3->set('template', 'roadmap.tpl.php');
    $f3->set('onpage', 'roadmap');        
  }

	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  function showMilestone($f3)
  {
    $f3->get("log")->write("Calling /milestone/@hash with @hash = " . $f3->get('PARAMS.hash'));

    if (!ctype_alnum($f3->get('SESSION.project')))
      return $this->tpfail($f3->get('lng.noProject'));

    if (!Helper::canRead($f3->get('SESSION.project')))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $hash = $f3->get('PARAMS.hash');

    $milestone = new DB\SQL\Mapper($this->db, 'Milestone');
    $milestone->load(array('hash = :hash', array(':hash' => $hash)));

    if($milestone->dry())
      return $this->tpfail($f3->get("lng.milestoneDoesNotExist"));

    $ticket = new DB\SQL\Mapper($this->db, 'displayableticket');
    $tickets = $ticket->find(array('milestone = :hash', array(':hash' => $milestone->hash)));

    if($milestone->dry())
      return $this->tpfail($f3->get("lng.milestoneTicketsNotLoaded"));

    $ms['ticketCount'] = Helper::getTicketCount($milestone->hash);

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

    $f3->set('tickets', $tickets);
    $f3->set('stats', $ms);
    $f3->set('milestone', $milestone);
    $f3->set('pageTitle', $f3->get('lng.milestone') . ' › ' . $milestone->name);
    $f3->set('template', 'milestone.tpl.php');
    $f3->set('onpage', 'roadmap');
  }
}
