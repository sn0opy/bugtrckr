<?php

/**
 * main.php
 * 
 * Everything comes together in here
 * 
 * @package Main
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
*/

class main extends F3instance
{
    function start()
    {
        F3::set('pageTitle', '{{@lng.home}}');
        F3::set('template', 'home.tpl.php');
        $this->tpserve();
    }
    
    function showTickets()
    {
        $order = F3::get('PARAMS.order') ? F3::get('PARAMS.order') : "id";
        $project = F3::get('SESSION.project');
        
        $milestones = new Milestone();
        $milestones = $milestones->find('project = ' .$project);
        
        $string = '';
        foreach($milestones as $ms)
            $string .= $ms->id.',';
        
        $tickets = new user_ticket();
        $tickets = $tickets->find('milestone IN (' .$string. '0)');
        
        F3::set('milestones', $milestones);
        F3::set('tickets', $tickets);
        F3::set('pageTitle', '{{@lng.tickets}}');
        F3::set('template', 'tickets.tpl.php');
        $this->tpserve();
    }
    
    function showMilestone()
    {
        $hash = F3::get('PARAMS.hash');

		$milestone = new Milestone();
		$milestone->load('hash = "' . $hash .'"');

		$ticket = new Ticket();
		$tickets = $ticket->find('milestone = ' . $milestone->id);

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
        F3::set('milestone', $milestone);
        F3::set('pageTitle', '{{@lng.milestone}} › '. $milestone->name);
        F3::set('template', 'milestone.tpl.php');
        $this->tpserve();

    }


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
    function showTicket()
    {
        $hash = F3::get('PARAMS.hash');

        $ticket = new Ticket();
        $ticket->load(array('hash = :hash', array(':hash' => $hash)));

        $milestone = new Milestone();
        $milestone->load(array("id = :id", array(':id' => $ticket->milestone)));

		if (!$ticket->id || !$milestone->id)
		{
            $this->tpfail("Can't open ticket");
            return ;
        }
/*
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
*/

        F3::set('ticket', $ticket);
        F3::set('milestone', $milestone);
        F3::set('pageTitle', '{{@lng.tickets}} › '. $ticket->title);
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

        $owner = F3::get('SESSION.user');

        $ticket = new Ticket();
        $ticket->hash = helper::getFreeHash('Ticket');
        $ticket->title = F3::get('POST.title');
        $ticket->description = F3::get('POST.description');
        $ticket->owner = $owner->id;
        $ticket->assigned = 0; // do not assign to anyone
        $ticket->type = F3::get('POST.type');
        $ticket->state = 1;
        $ticket->priority = F3::get('POST.priority');
        $ticket->category = 1;
        $ticket->milestone = F3::get('POST.milestone');

        $ticket->save();

		if (!$ticket->id)
		{       
            $this->tpfail("Failure while saving Ticket");
            return ;
        }

        Dao::addActivity('created Ticket ' . $ticket->title);

        F3::set('PARAMS.hash', $ticket->hash);
        $this->showTicket();
    }

    /**
     *
     */
    function editTicket()
    {
        require_once 'ticket.php';

        $hash = F3::get('PARAMS.hash');

        $ticket = new Ticket();
        $ticket->load('hash = :hash', array(':hash' => $hash));

        $ticket->assigned = F3::get('POST.userId');
        $ticket->state = F3::get('POST.state');

        $ticket->save();

		if (!$ticket->id)
        {
            $this->tpfail("Failure while saving Ticket");
            return ;
        }

        Dao::addActivity('changed Ticket '. $ticket->title);
        F3::set('PARAMS["hash"]', $hash);
        $this->showTicket($hash);
    }

    /**
     *
     */
    function selectProject()
    {
        $url = F3::get('SERVER.HTTP_REFERER');

        $project = new Project();
        $project->load(array('hash = :hash', array(':hash' => F3::get('POST.project'))));

		if (!$project->id)
		{
            $this->tpfail("Failure while changing Project");
            return ;
		}

        if(F3::get('SESSION.user'))
        {
            $user = F3::get('SESSION.user');
            $user->lastProject = $project->id;
            $user->save();
        }
		
        F3::set('SESSION.project', $project->getId());
        F3::reroute($url);
    }    
    
    /**
     * 
     */
    function showProjectSettings()
    {    
        $projectId = F3::get('SESSION.project');

    	$project = new Project;
       	$project->load(array('id = :id', array(':id' => $projectId)));
       
		$role = new Role(); 
      	$roles = $role->find('projectId = '.$projectId);

        $projPerms = new user_perms();
        $projPerms = $projPerms->find('projectId = '.$projectId);
        
		$milestone = new Milestone();
		$milestones = $milestone->find('project = '.$projectId);

		$user = new User();
		$users = $user->find();

		if (!$project->id || !$roles || !$milestones || !$users)
        {
            $this->tpfail("Failure while open Project");
            return ;
        }
        
        F3::set('users', $users);
        F3::set('projMilestones', $milestones);
        F3::set('projRoles', $roles);
        F3::set('projMembers', $projPerms);
        F3::set('projDetails', $project);        
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
        
       	$user = new user();
       	$user->load('hash = :hash', array(':hash', F3::get('POST.user')));

		if (!$user->id)
		{
            $this->tpfail("Failure while getting User");
            return ;
        }

  		$role = new Role();
       	$role->load('hash = :hash', array(':hash', F3::get('POST.role')));
		
		if (!$role->id)
		{
            $this->tpfail("Failure while getting Role");
            return ;
        }

  	    $perms = new ProjectPermission();
        $perms->load('projectId = :proj AND userId = :user',
						array(	':proj' => $projectId,
								':user' => $user->id));
        $perms->roleId = $role->id;
	    $perms->save();
        
        F3::reroute('/'.F3::get('BASE').'project/settings');
    }
    
    /**
     * 
     */
    function showProjectSettingsRole()
    {
        $roleHash = F3::get('PARAMS.hash');

		$role = new role();
    	$role->load('hash = :hash', array(':hash' => $roleHash));
		
		if (!$role->id)
		{
            $this->tpfail("Failure while getting Role");
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

       	$milestone = new Milestone();
        $milestone->load('hash = :hash', array(':hash' => $msHash));

		if (!$milestone->id)
		{
            $this->tpfail("Failure while getting Milestone");
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
        
	    $role = new role();
		if (F3::exists('POST.hash'))
	    	$role->load('hash = :hash', array(':hash' => $roleHash));

	        $role->name = F3::get('POST.name');
	        $role->hash = $roleHash;
    	    $role->issuesAssigneable = F3::get('POST.issuesAssigneable');
        	$role->projectId = F3::get('SESSION.project');
        	$role->iss_addIssues = F3::get('POST.iss_addIssues');
        	$role->proj_editProject = F3::get('POST.proj_editProject');
        	$role->proj_manageMembers = F3::get('POST.proj_manageMembers');
        	$role->proj_manageMilestones = F3::get('POST.proj_manageMilestones');
        	$role->proj_manageRoles = F3::get('POST.proj_manageRoles');
        	$role->iss_editIssues = F3::get('POST.iss_editIssues');
        	$role->iss_addIssues = F3::get('POST.iss_addIssues');
        	$role->iss_deleteIssues = F3::get('POST.iss_deleteIssues');
        	$role->iss_moveIssue = F3::get('POST.iss_moveIssue');
        	$role->iss_editWatchers = F3::get('POST.iss_editWatchers');
        	$role->iss_addWatchers = F3::get('POST.iss_addWatchers');
        	$role->iss_viewWatchers = F3::get('POST.iss_viewWatchers');
        	$role->save();

		if (!$role->id)
		{
            $this->tpfail("Failure while saving Role");
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
        
        $milestone = new Milestone();
		    
		if (F3::exists('POST.hash'))
    	   	$milestone->load('hash = "' .$msHash. '"');

	    $milestone->name = F3::get('POST.name');
        $milestone->hash = $msHash;
        $milestone->description = F3::get('POST.description');
        $milestone->project = F3::get('SESSION.project');
        $milestone->save();

		if (!$milestone->id)
		{
            $this->tpfail("Failure while saving Milestone");
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
        $project = new Project();
        $project->load(array('id = :id', array(':id' => $this->get('SESSION.project'))));
        $project->name = $this->get('POST.name');
        $project->public = $this->get('POST.public');
        $project->description = $this->get('POST.description');
        $project->save();
        
		if (!$project->id)
		{    
			$this->tpfail("Failure while saving Project");
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
		$user = new User();
		$user->load('name = :name', array(':name' => $name));

        if(!$user)
		{
	    	$this->tpfail("User not found", $e);
            return ;	
		}

		$ticket = new Ticket();
		$tickets = $ticket->find('owner = :owner', array(':owner' => $user->id));

		if (!$tickets)
		{
            $this->tpfail("Failure while getting User's infos");
            return ;
        }

        $this->set('user', $user);
        $this->set('tickets', $tickets);
        $this->set('template', 'user.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › '.$name);
        $this->tpserve();
    }

    /**
     *
     */
    function showUserRegister()
    {
        $this->set('template', 'userRegister.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › {@lng.registration}');
        $this->tpserve();
    }

    /**
     *
     */
    function registerUser()
    {
        $salt = helper::randStr();

	    $user = new user();
	    $user->name = $this->get('POST.name');
	    $user->email = $this->get('POST.email');
    	$user->password = helper::salting($salt, F3::get('POST.password'));
        $user->salt = $salt;
       	$user->hash = helper::getFreeHash('User');
       	$user->admin = 0;
       	$user->save();
       
		if (!$user->id)
		{	
    		$this->tpfail("Failure while saving User");
            return ;
		}

		$this->set('SESSION.SUCCESS', 'User registred successfully');
    	$this->reroute('/'. F3::get('BASE'));
	}

    /**
     *
     */
    function showUserLogin()
    {
        $this->set('template', 'userLogin.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › {@lng.login}');
        $this->tpserve();
    }

    /**
     *
     */
    function loginUser()
    {
		$user = new User();
		$user->load('email = :email', array(':email' => $this->get('POST.email')));
		$user->load('email = :email AND password = :password',
					array(	':email' => $this->get('POST.email'),
							':password' => helper::salting($user->salt, $this->get('POST.password'))));

		if (!$user)
		{
			$this->set('FAILURE', 'Login failed.');
            $this->reroute('/'. $this->get('BASE') .'user/login');
		}

		$this->set('SESSION.user', $user);
        $this->get('SESSION.user')->hash;
		F3::set('SESSION.SUCCESS', 'Login successful');
        #$this->reroute('/'. F3::get('BASE'));

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
		$project = new Project();
        $projects = $project->find();
		
		if (!$projects)
		{
        	$this->tpfail("Failure while loading Projects");
        	return ;
        }
        
        F3::set('projects', $projects);
        echo Template::serve('main.tpl.php');
    }

    /**
     *
     */
    private function tpdeny()
    {
        echo Template::serve('main.tpl.php');
    }

   
    /**
     *
     */
    private function tpfail($msg)
    {
        F3::set('FAILURE', $msg);
        F3::set('template', 'error404.tpl.php');
        echo Template::serve('main.tpl.php');
    }
}
