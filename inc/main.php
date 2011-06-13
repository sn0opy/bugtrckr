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
    private $helper;
    
    function __construct()
    {
        $this->helper = new helper();
    }
    
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
        
        $tickets = new DisplayableTicket();
        $tickets = $tickets->find('milestone IN (' .$string. '0) ORDER BY '. $order);
            
        $categories = new Category();
        $categories = $categories->find();

        $this->set('milestones', $milestones);
        $this->set('tickets', $tickets);
        $this->set('categories', $categories);
        $this->set('pageTitle', '{{@lng.tickets}}');
        $this->set('template', 'tickets.tpl.php');
        $this->tpserve();
    }
    
    function showMilestone()
    {
        $hash = $this->get('PARAMS.hash');
        
        $helper = new helper();

		$milestone = new Milestone();
		$milestone->load('hash = "' . $hash .'"');

        $ticket = new DisplayableTicket();
        $tickets = $ticket->find('milestone = '.$milestone->id);
        
        $ms['ticketCount'] = $helper->getTicketCount($milestone->id);

        $ms['fullTicketCount'] = 0;
        foreach($ms['ticketCount'] as $cnt)
            $ms['fullTicketCount'] += $cnt['count'];

        $ms['openTickets'] = 0;
        foreach($ms['ticketCount'] as $j => $cnt)
        {
            $ms['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $ms['fullTicketCount']);
            $ms['ticketCount'][$j]['title'] = $this->get("ticket_state.".$ms['ticketCount'][$j]['state']);

            if($ms['ticketCount'][$j]['state'] != 5)
                 $ms['openTickets'] += $ms['ticketCount'][$j]['count'];
        }
            
        $this->set('tickets', $tickets);
        $this->set('stats', $ms);
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

        $activities = new Activity();
        $activities = $activities->find("project = $project");

        $this->set('activities', $activities);
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

        $ticket = new DisplayableTicket();
        $ticket = $ticket->findone("tickethash = '$hash'");

        $milestone = new Milestone();
        $milestone->load(array('id = :id', array(':id' => $ticket->milestone)));

		if (!$ticket->id || !$milestone->id)
		{
            $this->tpfail("Can't open ticket");
            return ;
        }

        $users = new User();
        $users = $users->find();

        $this->set('ticket', $ticket);
        $this->set('milestone', $milestone);
        $this->set('users', $users);
        $this->set('pageTitle', '{{@lng.tickets}} › '. $ticket->title);
        $this->set('template', 'ticket.tpl.php');
        $this->tpserve();
    }

    /**
     *
     */
    function addTicket()
    {
        if(!$this->helper->getPermission('iss_addIssues'))
        {
            $this->tpfail('You are not allowed to add tickets.');
            return;
        }
        
        $ticket = new Ticket(); 
        $ticket->hash = $this->helper->getFreeHash('Ticket');
        $ticket->title = $this->get('POST.title');
        $ticket->description = $this->get('POST.description');
        $ticket->owner = $this->get('SESSION.user.id');
        $ticket->assigned = 0; // do not assign to anyone
        $ticket->type = $this->get('POST.type');
        $ticket->state = 1;
        $ticket->created = time();
        $ticket->priority = $this->get('POST.priority');
        $ticket->category = $this->get('POST.category');
        $ticket->milestone = $this->get('POST.milestone');
        $ticket->save();

		if (!$ticket->_id)
		{       
            $this->tpfail("Failure while saving Ticket");
            return ;
        }
        
        $this->reroute('/'.$this->get('BASE').'ticket/'.$ticket->hash);
    }

    /**
     *
     */
    function editTicket()
    {
        $hash = $this->get('PARAMS.hash');

        $ticket = new Ticket();
        $ticket->load("hash = '$hash'");

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

		$projectId = $this->get('POST.project');

        $project = new Project();
        $project->load("hash = '$projectId'");//array('hash = :hash', array(':hash' => $this->get('POST.project'))));

		if (!$project->id)
		{
            $this->tpfail("Failure while changing Project");
            return ;
		}

        if($this->get('SESSION.user.id'))
        {
            $user = new User();
			$user->load("id = ".$this->get('SESSION.user.id'));
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
    
    function projectAddMember()
    {
        if(!$this->helper->getPermission('proj_manageMembers'))
        {
            $this->tpfail('You are not allowed to add new members.');
            return;
        }
        
        $projectId = $this->get('SESSION.project');
        $userHash = $this->get('POST.member');
        $roleHash = $this->get('POST.role');
        
		$role = new Role(); 
      	$role->load(array('hash = :hash', array(':hash' => $roleHash)));
        
        if($role->dry())
        {
            $this->tpfail('Failure while getting role.');
            return;
        }
        
        $user = new User();
        $user->load(array('hash = :hash', array(':hash' => $userHash)));
        
        if($user->dry())
        {
            $this->tpfail('Failure while getting user.');
            return;
        }
        
        $projPerms = new ProjectPermission();
        $projPerms->load(array('userId = :userId AND projectId = :projectId',  
                            array(':userId' => $user->id, ':projectId' => $projectId)));
        
        if(!$projPerms->dry())
        {
            $this->tpfail('User already exists in this project.');
            return;
        }
        
        $projPerms->userId = $user->id;
        $projPerms->roleId = $role->id;
        $projPerms->projectId = $projectId;
        $projPerms->save();
        
        $this->reroute('/'.$this->get('BASE').'project/settings');
    }
    
    function projectDelMember()
    {
        if(!$this->helper->getPermission('proj_manageMembers'))
        {
            $this->tpfail('You are not allowed to add new members.');
            return;
        }
        
        $userHash = $this->get('POST.user');
        $projectId = $this->get('SESSION.project');
        
        $user = new User();
        $user->load(array('hash = :hash', array(':hash' => $userHash)));
                
        if($user->dry())
        {
            $this->tpfail('Failure while getting user.');
            return;
        }        
        
        $projPerms = new ProjectPermission();
        $projPerms->load('userId = '.$user->id.' AND projectId = '.$projectId);
        $projPerms->erase();
        
        $this->set('SESSION.SUCCESS', 'Member has been removed from the project.');
        $this->reroute('/'.$this->get('BASE').'project/settings');        
    }
    
    /**
     * 
     */
    function projectSetRole()
    {
        if(!$this->helper->getPermission('proj_manageMembers'))
        {
            $this->tpfail('You are not allowed to edit members.');
            return;
        }
        
        $projectId = $this->get('SESSION.project');
        
       	$user = new user();
       	$user->load(array('hash = :hash', array(':hash' => $this->get('POST.user'))));

		if (!$user->id)
		{
            $this->tpfail("Failure while getting User");
            return ;
        }

  		$role = new Role();
       	$role->load(array('hash = :hash', array(':hash' => $this->get('POST.role'))));
		
		if (!$role->id)
		{
            $this->tpfail("Failure while getting Role");
            return ;
        }
        
        if($role->projectId != $projectId)
        {
            $this->tpfail("Role does not belong to this project.");
            return;
        }

  	    $perms = new ProjectPermission();
        $perms->load(array('projectId = :proj AND userId = :user',
						array(	':proj' => $projectId,
								':user' => $user->id)));
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
    	$role->load(array('hash = :hash', array(':hash' => $roleHash)));
		
		if (!$role->id)
		{
            $this->tpfail("Failure while getting Role");
            return ;
        }

        $this->set('roleData', $role);  
        $this->set('template', 'projectSettingsRole.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.role}} › {{@roleData->name}}');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function showProjectSettingsMilestone()
    {
        $msHash = $this->get('PARAMS.hash');

       	$milestone = new Milestone();
        $milestone->load(array('hash = :hash', array(':hash' => $msHash)));

		if (!$milestone->id)
		{
            $this->tpfail("Failure while getting Milestone");
            return ;
        }

        $this->set('msData', $milestone);        
        $this->set('template', 'projectSettingsMilestone.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.milestone}} › {{@msData->name}}');
        $this->tpserve();        
    }
    
    /**
     * 
     */
    function addEditRole()
    {
        $roleHash = $this->get('POST.hash') ? $this->get('POST.hash') : helper::getFreeHash('Role');
        
	    $role = new role();        
		if (F3::exists('POST.hash')) {
	    	$role->load(array('hash = :hash', array(':hash' => $roleHash)));
            
            if($role->dry())
            {
                $this->tpfail('Failure while editing role.');
                return;
            }
        }

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
        {
            $milestone->load('hash = "' .$msHash. '"');
            if($milestone->dry())
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

        if(!$user->id)
		{
	    	$this->tpfail("User not found");
            return ;	
		}

		$ticket = new User_ticket();
		$tickets = $ticket->find('owner = '.$user->id);

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

		if (!$user->_id)
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
        $this->tpserve();
    }
}
