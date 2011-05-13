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
        $this->set('pageTitle', '{{@lng.home}}');
        $this->set('template', 'home.tpl.php');
        $this->tpserve();
    }
    
    function showTickets()
    {
        $order = $this->get('PARAMS.order') ? $this->get('PARAMS.order') : "id";
        $project = $this->get('SESSION.project');
        
        $milestones = new Milestone();
        $milestones = $milestones->find('project = ' .$project);
        
        $string = '';
        foreach($milestones as $ms)
            $string .= $ms->id.',';
        
        $tickets = new user_ticket();
        $tickets = $tickets->find('milestone IN (' .$string. '0)');
        
        $this->set('milestones', $milestones);
        $this->set('tickets', $tickets);
        $this->set('pageTitle', '{{@lng.tickets}}');
        $this->set('template', 'tickets.tpl.php');
        $this->tpserve();
    }
    
    function showMilestone()
    {
        $hash = $this->get('PARAMS.hash');

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
            $stats['ticketCount'][$j]['title'] = $this->get("ticket_state.".$stats['ticketCount'][$j]['state']);

            if($stats['ticketCount'][$j]['state'] == 5)
                $stats['openTickets'] = $fullCount - $stats['ticketCount'][$j]['count'];
        }

        $stats['openTickets'] = ($fullCount) ? $stats['openTickets'] : 0 ;
*/
        $this->set('tickets', $tickets);
//        $this->set('stats', $stats);
        $this->set('milestone', $milestone);
        $this->set('pageTitle', '{{@lng.milestone}} › '. $milestone->name);
        $this->set('template', 'milestone.tpl.php');
        $this->tpserve();

    }


    function showRoadmap()
    {        
        $ms = array();
        $fullCount = 0;
        
        $helper = new helper();

        $milestones = new Milestone();
        $milestones = $milestones->find('project = '.$this->get('SESSION.project'));

        foreach($milestones as $milestone)
        { 
            $ticket = new Ticket();
            $ticket->find('milestone = '.$milestone->id);
            
            $ms[$milestone->id]['infos'] = $milestone;
            $ms[$milestone->id]['ticketCount'] = $helper->getTicketCount($milestone->id);
            
            $ms[$milestone->id]['fullTicketCount'] = 0;
            foreach($ms[$milestone->id]['ticketCount'] as $cnt)
                $ms[$milestone->id]['fullTicketCount'] += $cnt['count'];

            $ms[$milestone->id]['openTickets'] = 0;
            foreach($ms[$milestone->id]['ticketCount'] as $j => $cnt)
            {
                $ms[$milestone->id]['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $ms[$milestone->id]['fullTicketCount']);
                $ms[$milestone->id]['ticketCount'][$j]['title'] = $this->get("ticket_state.".$ms[$milestone->id]['ticketCount'][$j]['state']);

                if($ms[$milestone->id]['ticketCount'][$j]['state'] != 5)
                     $ms[$milestone->id]['openTickets'] += $ms[$milestone->id]['ticketCount'][$j]['count'];

 
            }
        }

        $this->set('road', $ms);
        $this->set('pageTitle', '{{@lng.roadmap}}');
        $this->set('template', 'roadmap.tpl.php');
        $this->tpserve();
    }

    /**
     *
     */
    function showTimeline()
    {
        $timeline = array();

        /* Get Project */
        $project = $this->get('SESSION.project');

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

        $this->set('activities', $timeline);
        $this->set('pageTitle', '{{@lng.timeline}}');
        $this->set('template', 'timeline.tpl.php');

        $this->tpserve();
    }

    /**
     *
     */
    function showTicket()
    {
        $hash = $this->get('PARAMS.hash');

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

            $this->set('users', $users);
        }
*/

        $this->set('ticket', $ticket);
        $this->set('milestone', $milestone);
        $this->set('pageTitle', '{{@lng.tickets}} › '. $ticket->title);
        $this->set('template', 'ticket.tpl.php');
        $this->tpserve();
    }

    /**
     *
     */
    function addTicket()
    {
        //if (!Dao::getPermission("iss_addIssues"))
        //    $this->tpdeny();

        $owner = $this->get('SESSION.user');

        $ticket = new Ticket();
        $ticket->hash = helper::getFreeHash('Ticket');
        $ticket->title = $this->get('POST.title');
        $ticket->description = $this->get('POST.description');
        $ticket->owner = $owner->id;
        $ticket->assigned = 0; // do not assign to anyone
        $ticket->type = $this->get('POST.type');
        $ticket->state = 1;
        $ticket->priority = $this->get('POST.priority');
        $ticket->category = 1;
        $ticket->milestone = $this->get('POST.milestone');

        $ticket->save();

		if (!$ticket->id)
		{       
            $this->tpfail("Failure while saving Ticket");
            return ;
        }

        //Dao::addActivity('created Ticket ' . $ticket->title);

        $this->set('PARAMS.hash', $ticket->hash);
        $this->showTicket();
    }

    /**
     *
     */
    function editTicket()
    {
        $hash = $this->get('PARAMS.hash');

        $ticket = new Ticket();
        $ticket->load('hash = :hash', array(':hash' => $hash));

        $ticket->assigned = $this->get('POST.userId');
        $ticket->state = $this->get('POST.state');

        $ticket->save();

		if (!$ticket->id)
        {
            $this->tpfail("Failure while saving Ticket");
            return ;
        }

        //Dao::addActivity('changed Ticket '. $ticket->title);
        $this->set('PARAMS["hash"]', $hash);
        $this->showTicket($hash);
    }

    /**
     *
     */
    function selectProject()
    {
        $url = $this->get('SERVER.HTTP_REFERER');

        $project = new Project();
        $project->load(array('hash = :hash', array(':hash' => $this->get('POST.project'))));

		if (!$project->id)
		{
            $this->tpfail("Failure while changing Project");
            return ;
		}

        if($this->get('SESSION.user'))
        {
            $user = $this->get('SESSION.user');
            $user->lastProject = $project->id;
            $user->save();
        }
		
        $this->set('SESSION.project', $project->id);
        $this->reroute($url);
    }    
    
    /**
     * 
     */
    function showProjectSettings()
    {    
        $projectId = $this->get('SESSION.project');

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
        
        $this->set('users', $users);
        $this->set('projMilestones', $milestones);
        $this->set('projRoles', $roles);
        $this->set('projMembers', $projPerms);
        $this->set('projDetails', $project);        
        $this->set('template', 'projectSettings.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}}');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function projectSetRole()
    {
        $projectId = $this->get('SESSION.project');
        
       	$user = new user();
       	$user->load(array('hash = :hash', array(':hash', $this->get('POST.user'))));

		if (!$user->id)
		{
            $this->tpfail("Failure while getting User");
            return ;
        }

  		$role = new Role();
       	$role->load('hash = :hash', array(':hash', $this->get('POST.role')));
		
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
        
        $this->reroute('/'.$this->get('BASE').'project/settings');
    }
    
    /**
     * 
     */
    function showProjectSettingsRole()
    {
        $roleHash = $this->get('PARAMS.hash');

		$role = new role();
    	$role->load('hash = :hash', array(':hash' => $roleHash));
		
		if (!$role->id)
		{
            $this->tpfail("Failure while getting Role");
            return ;
        }

        $this->set('roleData', $role->toArray());        
        
        $this->set('template', 'projectSettingsRole.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.role}} › {{@roleData.name}}');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function showProjectSettingsMilestone()
    {
        $msHash = $this->get('PARAMS.hash');

       	$milestone = new Milestone();
        $milestone->load('hash = :hash', array(':hash' => $msHash));

		if (!$milestone->id)
		{
            $this->tpfail("Failure while getting Milestone");
            return ;
        }

        $this->set('msData', $milestone->toArray());        
        $this->set('template', 'projectSettingsMilestone.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.milestone}} › {{@msData.name}}');
        $this->tpserve();        
    }
    
    /**
     * 
     */
    function addEditRole()
    {
        $roleHash = $this->get('POST.hash') ? $this->get('POST.hash') : helper::getFreeHash('Role');
        
	    $role = new role();
		if (F3::exists('POST.hash'))
	    	$role->load('hash = :hash', array(':hash' => $roleHash));

	        $role->name = $this->get('POST.name');
	        $role->hash = $roleHash;
    	    $role->issuesAssigneable = $this->get('POST.issuesAssigneable');
        	$role->projectId = $this->get('SESSION.project');
        	$role->iss_addIssues = $this->get('POST.iss_addIssues');
        	$role->proj_editProject = $this->get('POST.proj_editProject');
        	$role->proj_manageMembers = $this->get('POST.proj_manageMembers');
        	$role->proj_manageMilestones = $this->get('POST.proj_manageMilestones');
        	$role->proj_manageRoles = $this->get('POST.proj_manageRoles');
        	$role->iss_editIssues = $this->get('POST.iss_editIssues');
        	$role->iss_addIssues = $this->get('POST.iss_addIssues');
        	$role->iss_deleteIssues = $this->get('POST.iss_deleteIssues');
        	$role->iss_moveIssue = $this->get('POST.iss_moveIssue');
        	$role->iss_editWatchers = $this->get('POST.iss_editWatchers');
        	$role->iss_addWatchers = $this->get('POST.iss_addWatchers');
        	$role->iss_viewWatchers = $this->get('POST.iss_viewWatchers');
        	$role->save();

		if (!$role->id)
		{
            $this->tpfail("Failure while saving Role");
            return ;
        }

        $this->reroute('/'.$this->get('BASE').'project/settings/role/'. $roleHash);
    }
    
    /**
     * 
     */
    function addEditMilestone()
    {
        $msHash = $this->get('POST.hash') ? $this->get('POST.hash') : helper::getFreeHash('Milestone');
        
        $milestone = new Milestone();
		    
		if (F3::exists('POST.hash'))
    	   	$milestone->load('hash = "' .$msHash. '"');

	    $milestone->name = $this->get('POST.name');
        $milestone->hash = $msHash;
        $milestone->description = $this->get('POST.description');
        $milestone->project = $this->get('SESSION.project');
        $milestone->save();

		if (!$milestone->id)
		{
            $this->tpfail("Failure while saving Milestone");
            return ;
        }

        $this->reroute('/'.$this->get('BASE').'project/settings/milestone/'. $msHash);
    }
    
    /**
     * 
     */
    function showAddRole()
    {
        $this->set('template', 'projectSettingsRoleAdd.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.addrole}}');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function showAddMilestone()
    {
        $this->set('today', date('Y-m-d', time()));
        $this->set('template', 'projectSettingsMilestoneAdd.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.addmilestone}}');
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

        $this->reroute('/'.$this->get('BASE').'project/settings');
    }

    /**
     *
     */
    function showUser()
    {
        $name = $this->get('PARAMS.name');
		
        $user = new User();
		$user->load(array('name = :name', array(':name' => $name)));

        if(!$user)
		{
	    	$this->tpfail("User not found", $e);
            return ;	
		}

		$ticket = new User_ticket();
		$tickets = $ticket->find('owner = '.$user->id);

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
    	$user->password = helper::salting($salt, $this->get('POST.password'));
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
    	$this->reroute('/'. $this->get('BASE'));
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
		$user->load(array('email = :email', array(':email' => $this->get('POST.email'))));
		$user->load(array('email = :email AND password = :password',
					array(	':email' => $this->get('POST.email'),
							':password' => helper::salting($user->salt, $this->get('POST.password')))));
		
        if (!$user)
		{
			$this->set('FAILURE', 'Login failed.');
            $this->reroute('/'. $this->get('BASE') .'user/login');
		}

		$this->set('SESSION.user', array('name' => $user->name, 'id' => $user->id, 'admin' => $user->admin, 'hash' => $user->hash));
        $this->set('SESSION.SUCCESS', 'Login successful');
        $this->reroute('/'. $this->get('BASE'));
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

		$this->set('SESSION.SUCCESS', 'User logged out');
        $this->reroute('/'. $this->get('BASE'));  
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
        
        $this->set('projects', $projects);
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
        $this->set('FAILURE', $msg);
        $this->set('template', 'error404.tpl.php');
        echo Template::serve('main.tpl.php');
    }
}
