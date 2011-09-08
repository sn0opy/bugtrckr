<?php

/**
 * cproperties.php
 * 
 * Properties controller for different settings
 * 
 * @package Main
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
class cproperties extends Controller
{

    /**
     * 
     */
    function showProjectSettings()
    {
        $projectHash = $this->get('SESSION.project');

        if($projectHash != "") {      
            $project = new Project;
            $project->load(array('hash = :hash', array(':hash' => $projectHash)));

            $role = new Role();
            $roles = $role->find(array('project = :hash', array(':hash' => $projectHash)));

            $projPerms = new user_perms();
            $projPerms = $projPerms->find(array('project = :hash', array(':hash' => $projectHash)));


            $milestone = new Milestone();
            $milestones = $milestone->find(array('project = :hash', array(':hash' => $projectHash)));

            $user = new User();
            $users = $user->find();

            $categories = new Category();
            $categories = $categories->find();

            if ($project->hash == "") //|| !$roles || !$milestones || !$users || !$categories)
            {
                $this->tpfail("Failure while open Project");
                return;
            }

            $this->set('users', $users);
            $this->set('projMilestones', $milestones);
            $this->set('projRoles', $roles);
            $this->set('projMembers', $projPerms);
            $this->set('projDetails', $project);
            $this->set('projCategories', $categories);
            $this->set('template', 'projectSettings.tpl.php');
            $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}}');
            $this->set('onpage', 'settings');
            $this->tpserve();
        } else {
            $this->set('SESSION.FAILURE', 'No project set.');
            $this->set('template', 'projectSettings.tpl.php');
            $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}}');
            $this->tpserve();
        }
    }

    function projectAddMember()
    {
        $helper = new helper;
        
        if (!$helper->getPermission('proj_manageMembers'))
        {
            $this->tpfail('You are not allowed to add new members.');
            return;
        }

        $projectHash = $this->get('SESSION.project');
        $userHash = $this->get('POST.member');
        $roleHash = $this->get('POST.role');

        $role = new Role();
        $role->load(array('hash = :hash', array(':hash' => $roleHash)));

        if ($role->dry())
        {
            $this->tpfail('Failure while getting role.');
            return;
        }

        $user = new User();
        $user->load(array('hash = :hash', array(':hash' => $userHash)));

        if ($user->dry())
        {
            $this->tpfail('Failure while getting user.');
            return;
        }

        $projPerms = new ProjectPermission();
        $projPerms->load(array('user = :user AND project = :project',
            array(':user' => $user->hash, ':project' => $projectHash)));

        if (!$projPerms->dry())
        {
            $this->tpfail('User already exists in this project.');
            return;
        }

        $projPerms->user = $user->hash;
        $projPerms->role = $role->hash;
        $projPerms->project = $projectHash;
        $projPerms->save();

        $this->reroute($this->get('BASE') . '/project/settings');
    }

    function projectDelMember()
    {
        $helper = new helper;
        
        if (!$helper->getPermission('proj_manageMembers'))
        {
            $this->tpfail('You are not allowed to add new members.');
            return;
        }

        $userHash = $this->get('POST.user');
        $projectHash = $this->get('SESSION.project');

        $user = new User();
        $user->load(array('hash = :hash', array(':hash' => $userHash)));

        if ($user->dry())
        {
            $this->tpfail('Failure while getting user.');
            return;
        }

        $projPerms = new ProjectPermission();
        $projPerms->load('user = :user AND project = :project', array(':user' => $user->hash, ':project' => $projectHash));
        $projPerms->erase();

        $this->set('SESSION.SUCCESS', 'Member has been removed from the project.');
        $this->reroute($this->get('BASE') . '/project/settings');
    }

    /**
     * 
     */
    function projectSetRole()
    {
        if (!helper::getPermission('proj_manageMembers'))
        {
            $this->tpfail('You are not allowed to edit members.');
            return;
        }

        $projectHash = $this->get('SESSION.project');

        $user = new user();
        $user->load(array('hash = :hash', array(':hash' => $this->get('POST.user'))));

        if (!$user->hash)
        {
            $this->tpfail("Failure while getting User");
            return;
        }

        $role = new Role();
        $role->load(array('hash = :hash', array(':hash' => $this->get('POST.role'))));

        if (!$role->hash)
        {
            $this->tpfail("Failure while getting Role");
            return;
        }

        if ($role->project != $projectHash)
        {
            $this->tpfail("Role does not belong to this project.");
            return;
        }

        $perms = new ProjectPermission();
        $perms->load(array('project = :proj AND user = :user',
            array(':proj' => $projectHash,
                ':user' => $user->hash)));
        $perms->role = $role->hash;
        $perms->save();

        $this->reroute($this->get('BASE') . '/project/settings');
    }

    /**
     * 
     */
    function showProjectSettingsRole()
    {
        $roleHash = $this->get('PARAMS.hash');

        $role = new role();
        $role->load(array('hash = :hash', array(':hash' => $roleHash)));

        if (!$role->hash)
        {
            $this->tpfail("Failure while getting Role");
            return;
        }

        $this->set('roleData', $role);
        $this->set('template', 'projectSettingsRole.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.role}} › {{@roleData->name}}');
        $this->set('onpage', 'settings');
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

        if (!$milestone->hash)
        {
            $this->tpfail("Failure while getting Milestone");
            return;
        }

        $this->set('msData', $milestone);
        $this->set('template', 'projectSettingsMilestone.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.milestone}} › {{@msData->name}}');
        $this->set('onpage', 'settings');
        $this->tpserve();
    }

    /**
     * 
     */
    function deleteProjectSettingsMilestone()
    {
		if (!helper::getPermission('proj_manageMilestones'))
		{
			$this->tpfail("You are not allowed to do this.");
            return;
		}

        $msHash = $this->get('PARAMS.hash');

        $milestone = new Milestone();
        $milestone->load(array('hash = :hash', array(':hash' => $msHash)));

        if (!$milestone->hash)
        {
            $this->tpfail("Failure while getting Milestone");
            return;
        }

		$tickets = new Ticket();
		$count = $tickets->found('milestone = ' . $milestone->hash);

		if ($count > 0)
		{
            $this->tpfail("Milestone can not be removed");
            return;
		}

		$milestone->erase();
		$this->reroute($this->get('BASE') . '/project/settings');
	}

    /**
     * 
     */
    function addEditRole($projHash = false)
    {
        $roleHash = $this->get('POST.hash') ? $this->get('POST.hash') : helper::getFreeHash('Role');

        $role = new role();
        if ($this->exists('POST.hash'))
        {
            $role->load(array('hash = :hash', array(':hash' => $roleHash)));

            if ($role->dry())
            {
                $this->tpfail('Failure while editing role.');
                return;
            }
        }

        $role->name = ($projHash) ? 'Admin' : $this->get('POST.name');
        $role->hash = ($projHash) ? helper::getFreeHash('Role') : $roleHash;
        $role->issuesAssigneable = ($projHash) ? 1 : $this->get('POST.issuesAssigneable') == "on";
        $role->projectId = ($projHash) ? $projHash : $this->get('SESSION.project');
        $role->iss_addIssues = ($projHash) ? 1 : $this->get('POST.iss_addIssues') == "on";
        $role->proj_editProject = ($projHash) ? 1 : $this->get('POST.proj_editProject') == "on";
        $role->proj_manageMembers = ($projHash) ? 1 : $this->get('POST.proj_manageMembers') == "on";
        $role->proj_manageMilestones = ($projHash) ? 1 : $this->get('POST.proj_manageMilestones') == "on";
        $role->proj_manageRoles = ($projHash) ? 1 : $this->get('POST.proj_manageRoles') == "on";
        $role->iss_editIssues = ($projHash) ? 1 : $this->get('POST.iss_editIssues') == "on";
        $role->iss_addIssues = ($projHash) ? 1 : $this->get('POST.iss_addIssues') == "on";
        $role->iss_deleteIssues = ($projHash) ? 1 : $this->get('POST.iss_deleteIssues') == "on";
        $role->iss_moveIssue = ($projId) ? 1 : $this->get('POST.iss_moveIssue') == "on";
        $role->iss_editWatchers = ($projHash) ? 1 : $this->get('POST.iss_editWatchers') == "on";
        $role->iss_addWatchers = ($projHash) ? 1 : $this->get('POST.iss_addWatchers') == "on";
        $role->iss_viewWatchers = ($projHash) ? 1 : $this->get('POST.iss_viewWatchers') == "on";
        $role->save();

        if($projHash)
            return $role->_id;
        else
            $this->reroute($this->get('BASE') . '/project/settings/role/' . $roleHash);
    }
    
    
    function deleteRole()
    {
        $hash = $this->get('PARAMS.hash');
        
        if(helper::getPermission('proj_manageRoles')) {
            $ax = new Axon('Role');
            $ax->load(array('hash = :hash', array(':hash' => $hash)));

            $ax2 = new Axon('ProjectPermission');

            if($ax2->found('role = '.$ax->hash))
            {
                $this->tpfail('Role cannot be deleted.');
                return;
            }       

            $ax->erase();
            $this->set('SESSION.SUCCESS', 'Role has been deleted.');
            $this->reroute($this->get('BASE'). '/project/settings');     
        } else {
            $this->tpfail("You don't have permission to do this.");
        }
    }

    /**
     * 
     */
    function addEditCategory()
    {
        $category = new Category();

		if ($this->get('POST.hash') != "")
			$category->load(array('hash = :hash', array(':hash' => $this->get('POST.hash'))));
		else
		{
			$category->project = $this->get('SESSION.projectHash');
			$category->hash = helper::getFreeHash('Category');
		}

        $category->name = $this->get('POST.name');
        $category->save();

		if ($this->get('POST.hash') != "")
			$this->set('SESSION.SUCCESS', 'Category changed successfully');
		else
        	$this->set('SESSION.SUCCESS',"Category added successfully");

        $this->reroute($this->get('BASE') . '/project/settings/');
    }

	/**
	 *
	 */
	function deleteCategory()
	{
		$hash = $this->get('PARAMS.hash');

		if (helper::getPermission('proj_editProject'))
		{
			$category = new Category();
			$category->load(array('hash = :hash', array(':hash' => $hash)));

			$category->erase();
			$this->set('SESSION.SUCCESS', 'Category has been deleted.');
			$this->reroute($this->get('BASE') . '/project/settings');
		}
		else
			$this->tpfail('You don\'t have permissions to do this.');
	}

    /**
     * 
     */
    function showAddRole()
    {
        $this->set('template', 'projectSettingsRoleAdd.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.addrole}}');
        $this->set('onpage', 'settings');
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
        $this->set('onpage', 'settings');
        $this->tpserve();
    }

    /**
     * 
     */
    function showAddCategory()
    {
        $this->set('template', 'projectSettingsCategoryAdd.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.addcategory}}');
        $this->set('onpage', 'settings');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function showAddProject()
    {
        $this->set('template', 'projectAdd.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.add}}');
        $this->set('onpage', 'settings');
        $this->tpserve();
    }
    
    /**
     * 
     */
    function projectAdd() 
    {
        $hash = helper::getFreeHash('Project');
        
        $ax = new Axon('Project');
        $ax->name = $this->get('POST.name');
        $ax->description = $this->get('POST.description');
        $ax->public = ($this->get('POST.public') == 'on') ? 1 : 0;
        $ax->hash = $hash;
        $ax->save();
        $proj = $ax->_id;

        $cmain = new cmain();
        $cmain->selectProject($hash, false);
        
        $perms = new ProjectPermission();
        $perms->user = $this->get('SESSION.user.hash');
        $perms->project = $proj;
        $perms->role = self::addEditRole($proj);
        $perms->save();
        
        $this->reroute($this->get('BASE').'/');        
    }

    /**
     * 
     */
    function projectEditMain()
    {
        $project = new Project();
        $project->load(array('hash = :hash', array(':hash' => $this->get('SESSION.project'))));
        $project->name = $this->get('POST.name');
        $project->public = $this->get('POST.public')=='on';
        $project->description = $this->get('POST.description');
        $project->save();

        if (!$project->hash)
        {
            $this->tpfail("Failure while saving Project");
            return;
        }

        $this->reroute($this->get('BASE') . '/project/settings');
    }
}
