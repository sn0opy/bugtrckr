<?php

/**
 * main.php
 * 
 * Everything comes together in here
 * 
 * @package Main
 * @author Sascha Ohms
 * @author Phillipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
**/

require_once F3::get('BASE').'inc/models.php';

class main extends F3instance
{
    function  __construct() {
        parent::__construct();

        if(F3::get('SESSION.userId'))
        {
            $user = new user();
            $user->load('id = ' .F3::get('SESSION.userId'));

            if($user->getLastProject() > 0) {
                F3::set('SESSION.project', $user->getLastProject());
            }
        }

        require_once 'inc/mapping.inc.php';
    }

    function start()
    {
        F3::set('pageTitle', '{{@lng.home}}');
        F3::set('template', 'home.tpl.php');
        $this->tpserve();
    }

    /**
     * 
     */
    function showMilestone()
    {
        $hash = F3::get('PARAMS.hash');

		$milestone = new Milestone();
		$milestone->load('hash = "' . $hash .'"');

		$ticket = new Ticket();
		$tickets = $ticket->find('milestone = ' . $milestone->getId());

/*
        try {
	 	    $ms = new Milestone();
	        $ms->load('hash = "' .$hash.'"');
            $tickets = Dao::getTickets('milestone = ' .$ms->getId());
        } catch (Exception $e) {
            $this->tpfail("Failure while open Tickets", $e);
            return ;
        }

        foreach($tickets as $i=>$ticket)
            $tickets[$i] = $ticket->toArray();

        $stats['ticketCount'] = Dao::getTicketCount($ms->getId());
*/
/*        $fullCount = 0;
        foreach($stats['ticketCount'] as $cnt)
            $fullCount += $cnt['count'];

        $stats['fullTicketCount'] = $fullCount;

        foreach($stats['ticketCount'] as $j=>$cnt)
        {
            $stats['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $fullCount);
            $stats['ticketCount'][$j]['title'] = F3::get("ticket_state.".$stats['ticketCount'][$j]['state']);

            if($stats['ticketCount'][$j]['state'] == 5)
                $stats['openTickets'] = $fullCount - $stats['ticketCount'][$j]['count'];
        }

        $stats['openTickets'] = ($fullCount) ? $stats['openTickets'] : 0 ;
*/
        F3::set('tickets', $tickets);
//        F3::set('stats', $stats);
        F3::set('milestone', $ms->toArray());
        F3::set('pageTitle', '{{@lng.milestone}} › '. $ms->getName());
        F3::set('template', 'milestone.tpl.php');
        $this->tpserve();

    }


    /**
     *
     */
    function showRoadmap()
    {
        $road = array();

        /* Get Project */
        $project = F3::get('SESSION.project');

        /* Get Milestones */
        try {			
            $milestones = Dao::getMilestones("project = $project");
       	} catch (Exception $e) {
            $this->tpfail("Failure while open Milestones", $e);
            return ;
        }

        /*  */
        foreach($milestones as $i=>$milestone)
        {
            $road[$i]['milestone'] = $milestone->toArray();
            $road[$i]['ticketCount'] = Dao::getTicketCount($milestone->getId());

            $fullCount = 0;
            foreach($road[$i]['ticketCount'] as $cnt)
                $fullCount += $cnt['count'];

            $road[$i]['fullTicketCount'] = $fullCount;

            foreach($road[$i]['ticketCount'] as $j=>$cnt)
            {
                $road[$i]['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $fullCount);
                $road[$i]['ticketCount'][$j]['title'] = F3::get("ticket_state.".$road[$i]['ticketCount'][$j]['state']);

                if($road[$i]['ticketCount'][$j]['state'] == 5)
                    $road[$i]['openTickets'] = $fullCount - $road[$i]['ticketCount'][$j]['count'];
            }

            $road[$i]['openTickets'] = ($fullCount) ? $road[$i]['openTickets'] : 0 ;

        }

        F3::set('road', $road);
        F3::set('pageTitle', '{{@lng.roadmap}}');
        F3::set('template', 'roadmap.tpl.php');
        $this->tpserve();
    }

    /**
     *
     */
    function showTimeline()
    {
        $timeline = array();

        /* Get Project */
        $project = F3::get('SESSION.project');

        try {
            $activities = Dao::getActivities("project = $project");
        } catch (Exception $e) {
            $this->tpfail("Failure while open Activities", $e);
            return ;
        }

        foreach($activities as $activity)
        {
            $timeline[] = $activity->toArray();
        }

        F3::set('activities', $timeline);
        F3::set('pageTitle', '{{@lng.timeline}}');
        F3::set('template', 'timeline.tpl.php');

        $this->tpserve();
    }

    /**
     *
     */
    function showTickets()
    {
        /* Get ordering */
        $order = F3::get('PARAMS.order') != NULL ?
                F3::get('PARAMS.order') : "id";

        /* Get Project */
        $project = F3::get('SESSION.project');

        /* Get Milestones of the Project */
        try {
            $milestones = Dao::getMilestones("project = $project");
        } catch (Exception $e) {
            $this->tpfail("Failure while open Milestones", $e);
            return ;
        }

        foreach($milestones as $i=>$milestone)
        {
            $milestones[$i] = $milestone->toArray();
        }

        /* Get Data from DB */
        try {
            $tickets = Dao::getTickets("milestone IN " .
                "(SELECT id FROM Milestone WHERE project = $project)" .
                "ORDER BY $order");
        } catch (Exception $e) {
            $this->tpfail("Failure while open Tickets", $e);
            return ;
        }

        foreach($tickets as $i=>$ticket)
            $tickets[$i] = $ticket->toArray();

        F3::set('milestones', $milestones);
        F3::set('tickets', $tickets);
        F3::set('pageTitle', '{{@lng.tickets}}');
        F3::set('template', 'tickets.tpl.php');
        $this->tpserve();
    }

    /**
     *
     */
    function showTicket()
    {
        $hash = F3::get('PARAMS.hash');

        try {
            $ticket = new Ticket();
            $ticket->load("hash = '$hash'");

            $milestone = new Milestone();
            $milestone->load("id = ". $ticket->getMilestone());
        } catch (Exception $e) {
            $this->tpfail("Can't open ticket", $e);
            return ;
        }

        if (Dao::getPermission('iss_editIssue'))
        {
			try {
            	$users = Dao::getUsers("1 = 1");
        	} catch (Exception $e) {
           		$this->tpfail("Can't get Permissions", $e);
           		return ;
        	}

            foreach($users as $i=>$user)
            {
                $users[$i] = $user->toArray();
            }

            F3::set('users', $users);
        }

        F3::set('ticket', $ticket->toArray());
        F3::set('milestone', $milestone->toArray());
        F3::set('pageTitle', '{{@lng.tickets}} › '. $ticket->getTitle());
        F3::set('template', 'ticket.tpl.php');
        $this->tpserve();
    }

    /**
     *
     */
    function addTicket()
    {
        if (!Dao::getPermission("iss_addIssues"))
            $this->tpdeny();

        $post = F3::get('POST');
        $owner = F3::get('SESSION.userId');

        $ticket = new Ticket();
        $ticket->setHash(helper::getFreeHash('Ticket'));
        $ticket->setTitle($post['title']);
        $ticket->setDescription($post['description']);
        $ticket->setOwner($owner);
        $ticket->setAssigned(0); // do not assign to anyone
        $ticket->setType($post['type']);
        $ticket->setState(1);
        $ticket->setPriority($post['priority']);
        $ticket->setCategory(1);
        $ticket->setMilestone($post['milestone']);

        try {
            $ticket->save();

            Dao::addActivity("created Ticket ". $ticket->getTitle());
            F3::set('PARAMS.hash', $ticket->getHash());
            $this->showTicket();
        } catch (Exception $e) {
            $this->tpfail("Failure while saving Ticket", $e);
            return ;
        }
    }

    /**
     *
     */
    function editTicket()
    {
        if (!Dao::getPermission("iss_editIssues"))
            $this->tpdeny();

        require_once 'ticket.php';

        $post = F3::get('POST');
        $hash = F3::get('PARAMS.hash');

        try {
            $ticket = new Ticket();
            $ticket->load("hash = '$hash'");

            $ticket->setAssigned($post['userId']);
            $ticket->setState($post['state']);

            $ticket->save();
        } catch (Exception $e) {
            $this->tpfail("Failure while saving Ticket", $e);
            return ;
        }

        Dao::addActivity("changed Ticket ". $ticket->getTitle());
        F3::set('PARAMS["hash"]', $hash);
        $this->showTicket($hash);
    }

    /**
     *
     */
    function selectProject()
    {
        $url = F3::get('SERVER.HTTP_REFERER');

        try {
            $project = new Project();
            $project->load("hash = '" .F3::get('POST.project'). "'");

            if(F3::get('SESSION.userId'))
            {
                $user = new User();
                $user->load('id = ' .F3::get('SESSION.userId'));
                $user->setLastProject($project->getId());
                $user->save();
            }
        } catch (Exception $e) {
            $this->tpfail("Failure while changing Project", $e);
            return ;
        }

        F3::set('SESSION.project', $project->getId());
        F3::reroute($url);
    }    
    
    /**
     * 
     */
    function showProjectSettings()
    {    
        $project = F3::get('SESSION.project');

		try {
    	    $proj = new Project;
       		$proj->load('id = ' .$project);
        
      		$roles = Dao::getRoles('projectId = ' . $project);
        } catch (Exception $e) {
            $this->tpfail("Failure while open Project", $e);
            return ;
        }

        foreach($roles as $i => $role)
            $roles[$i] = $role->toArray();

		try {
 	    	$milestones = Dao::getMilestones('project = '.F3::get('SESSION.project'));        
		} catch (Exception $e) {
            $this->tpfail("Failure while getting Milestones", $e);
            return ;
        }

		foreach($milestones as $i => $milestone)
            $milestones[$i] = $milestone->toArray();
        
        $users = Dao::getUsers();
        F3::set('users2', $users[1]);
        
        foreach($users as $i => $user)
            $users[$i] = $user->toArray();
        
        F3::set('users', $users);
        F3::set('projMilestones', $milestones);
        F3::set('projRoles', $roles);
        F3::set('projMembers', Dao::getProjectMembers($project));
        F3::set('projDetails', $proj->toArray());        
        F3::set('template', 'projectSettings.tpl.php');
        F3::set('pageTitle', '{{@lng.project}} › {{@lng.settings}}');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function projectSetRole()
    {
        $projectId = F3::get('SESSION.project');
        
		try {
        	$user = new user();
        	$user->load('hash = "'.F3::get('POST.user').'"');
        	$userId = $user->getId();
		} catch (Exception $e) {
            $this->tpfail("Failure while getting User", $e);
            return ;
        }

		try {
    		$role = new Role();
        	$role->load('hash = "'.F3::get('POST.role').'"');
        	$roleId = $role->getId();
		} catch (Exception $e) {
            $this->tpfail("Failure while getting Role", $e);
            return ;
        }

        try {
	  	    $perms = new ProjectPermission();
	        $perms->load('projectId = ' .$projectId. ' AND userId = ' .$userId);
	        $perms->setRoleId($roleId);
 		    $perms->save();
		} catch (Exception $e) {
            $this->tpfail("Failure while saving Permissions", $e);
            return ;
        }
        
        F3::reroute('/'.F3::get('BASE').'project/settings');
    }
    
    /**
     * 
     */
    function showProjectSettingsRole()
    {
        $roleHash = F3::get('PARAMS.hash');

		try {
	        $role = new role();
    	    $role->load('hash = "' .$roleHash. '"');
		} catch (Exception $e) {
            $this->tpfail("Failure while getting Role", $e);
            return ;
        }

        F3::set('roleData', $role->toArray());        
        
        F3::set('template', 'projectSettingsRole.tpl.php');
        F3::set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.role}} › {{@roleData.name}}');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function showProjectSettingsMilestone()
    {
        $msHash = F3::get('PARAMS.hash');

		try {
        	$milestone = new Milestone();
        	$milestone->load('hash = "' .$msHash. '"');
		} catch (Exception $e) {
            $this->tpfail("Failure while getting Milestone", $e);
            return ;
        }

        F3::set('msData', $milestone->toArray());        
        
        F3::set('template', 'projectSettingsMilestone.tpl.php');
        F3::set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.milestone}} › {{@msData.name}}');
        $this->tpserve();        
    }
    
    /**
     * 
     */
    function addEditRole()
    {
        $roleHash = F3::get('POST.hash') ? F3::get('POST.hash') : helper::getFreeHash('Role');
        
		try {
	        $role = new role();
			if (F3::exists('POST.hash'))
	       		$role->load('hash = "' .$roleHash. '"');

	        $role->setName(F3::get('POST.name'));
	        $role->setHash($roleHash);
    	    $role->setIssuesAssigneable(F3::get('POST.issuesAssigneable'));
        	$role->setProjectId(F3::get('SESSION.project'));
        	$role->setIss_addIssues(F3::get('POST.iss_addIssues'));
        	$role->setProj_editProject(F3::get('POST.proj_editProject'));
        	$role->setProj_manageMembers(F3::get('POST.proj_manageMembers'));
        	$role->setproj_manageMilestones(F3::get('POST.proj_manageMilestones'));
        	$role->setProj_manageRoles(F3::get('POST.proj_manageRoles'));
        	$role->setIss_editIssues(F3::get('POST.iss_editIssues'));
        	$role->setIss_addIssues(F3::get('POST.iss_addIssues'));
        	$role->setIss_deleteIssues(F3::get('POST.iss_deleteIssues'));
        	$role->setIss_moveIssue(F3::get('POST.iss_moveIssue'));
        	$role->setIss_editWatchers(F3::get('POST.iss_editWatchers'));
        	$role->setIss_addWatchers(F3::get('POST.iss_addWatchers'));
        	$role->setIss_viewWatchers(F3::get('POST.iss_viewWatchers'));
        	$role->save();

		} catch (Exception $e) {
            $this->tpfail("Failure while saving Role", $e);
            return ;
        }

        F3::reroute('/'.F3::get('BASE').'project/settings/role/'. $roleHash);
    }
    
    /**
     * 
     */
    function addEditMilestone()
    {
        $msHash = F3::get('POST.hash') ? F3::get('POST.hash') : helper::getFreeHash('Milestone');
        
		try {
	        $milestone = new Milestone();
		    
			if (F3::exists('POST.hash'))
    	    	$milestone->load('hash = "' .$msHash. '"');

	        $milestone->setName(F3::get('POST.name'));
	        $milestone->setHash($msHash);
	        $milestone->setDescription(F3::get('POST.description'));
	        $milestone->setProject(F3::get('SESSION.project'));
	        $milestone->save();
		} catch (Exception $e) {
            $this->tpfail("Failure while saving Milestone", $e);
            return ;
        }

        F3::reroute('/'.F3::get('BASE').'project/settings/milestone/'. $msHash);
    }
    
    /**
     * 
     */
    function showAddRole()
    {
        F3::set('template', 'projectSettingsRoleAdd.tpl.php');
        F3::set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.addrole}}');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function showAddMilestone()
    {
        F3::set('today', date('Y-m-d', time()));
        F3::set('template', 'projectSettingsMilestoneAdd.tpl.php');
        F3::set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.addmilestone}}');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function projectEditMain()
    {
		try {
	        $project = new Project();
	        $project->load('id = ' . F3::get('SESSION.project'));
	        $project->setName(F3::get('POST.name'));
	        $project->setPublic(F3::get('POST.public'));
	        $project->setDescription(F3::get('POST.description'));
	        $project->save();
		} catch (Exception $e) {
            $this->tpfail("Failure while saving Project", $e);
            return ;
        }

        F3::reroute('/'.F3::get('BASE').'project/settings');
    }

    /**
     *
     */
    function showUser()
    {
        $name = F3::get('PARAMS.name');
        $result = F3::get('DB')->sql('SELECT * FROM User WHERE name = :name', array(':name' => $name));

        if(!$result) 
            F3::set('FAILURE', 'User not found.');
        else
            F3::set('user', $result[0]);


		try {
        	$userTickets = Dao::getTickets('owner = ' .F3::get('user.id'));
		} catch (Exception $e) {
            $this->tpfail("Failure while getting User's infos", $e);
            return ;
        }

        foreach($userTickets as $i => $userTicket)
            $userTickets[$i] = $userTicket->toArray();

        F3::set('tickets', $userTickets);
        F3::set('template', 'user.tpl.php');
        F3::set('pageTitle', '{{@lng.user}} › '.$name);
        $this->tpserve();
    }

    /**
     *
     */
    function showUserRegister()
    {
        F3::set('template', 'userRegister.tpl.php');
        F3::set('pageTitle', '{{@lng.user}} › {@lng.registration}');
        $this->tpserve();
    }

    /**
     *
     */
    function registerUser()
    {
        $salt = helper::randStr();

		try {
	        $user = new user();
	        $user->setName(F3::get('POST.name'));
	        $user->setEmail(F3::get('POST.email'));
    	    $user->setPassword(helper::salting($salt, F3::get('POST.password')));
        	$user->setSalt($salt);
        	$user->setHash(helper::getFreeHash('User'));
        	$user->setAdmin(0);
        	$user->save();
		} catch (Exception $e) {
            $this->tpfail("Failure while saving User", $e);
            return ;
        }

		F3::set('SESSION.SUCCESS', 'User registred successfully');
    	$this->reroute('/'. F3::get('BASE'));
	}

    /**
     *
     */
    function showUserLogin()
    {
        $this->set('template', 'userLogin.tpl.php');
        F3::set('pageTitle', '{{@lng.user}} › {@lng.login}');
        $this->tpserve();
    }

    /**
     *
     */
    function loginUser()
    {
		try {
	        $user = new user();
     		$user->load('email = "' .$this->get('POST.email'). '"'); // get salt
    	    $salt = $user->getSalt();

	        $user->load("email = '" .$this->get('POST.email'). "' AND password = '" . helper::salting($salt, $this->get('POST.password')). "'");

		} catch (Exception $e) {
            $this->tpfail("Failure while login User", $e);
            return ;
        }

        if(!$user->getId()) {
            $this->set('FAILURE', 'Login failed.');
            $this->reroute('/'. F3::get('BASE') .'user/login');
        } else {
            $this->set('SESSION.userName', $user->getName());
            $this->set('SESSION.userPassword', $user->getPassword());
            $this->set('SESSION.userHash', $user->getHash());
            $this->set('SESSION.userId', $user->getId());
		
			F3::set('SESSION.SUCCESS', 'Login successful');
            $this->reroute('/'. F3::get('BASE'));
        }
    }

    /**
     *
     */
    function logoutUser()
    {
        $this->set('SESSION.userName', NULL);
        $this->set('SESSION.userPassword', NULL);
        $this->set('SESSION.userHash', NULL);
        $this->set('SESSION.userId', NULL);
        session_destroy();

		F3::set('SESSION.SUCCESS', 'User logged out');
        $this->reroute('/'. F3::get('BASE'));  
    }

    /**
     * 
     */
    private function tpserve()
    {
/*		try {
        	$projects = Dao::getProjects('1 = 1');
     		foreach($projects as $i=>$project)
        	    $projects[$i] = $project->toArray();
		} catch (Exception $e) {
            $this->tpfail("Failure while loading Projects", $e);
            return ;
        }*/

        F3::set('projects', $projects);
        echo Template::serve('main.tpl.php');
    }

    /**
     *
     */
    private function tpdeny()
    {
        F3::set('FAILURE', 'You are not allowed to do this.');			
        F3::set('template', 'error404.tpl.php');
        echo Template::serve('main.tpl.php');
    }

    /**
     *
     */
    private function tpfail($msg, &$e)
    {
		if (F3::get('DEBUG') > 0)
			$msg .= ": " . print_r($e);

        F3::set('FAILURE', $msg);
        F3::set('template', 'error404.tpl.php');
        echo Template::serve('main.tpl.php');
    }

}
