<?php

/**
 * project\controller.php
 * 
 * Project controller for different settings
 * 
 * @package Main
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
namespace project;

class controller extends \misc\Controller
{
    function projectAddMember()
    {
        $helper = new \misc\helper;
        
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
        $helper = new \misc\helper;
        
        if (!$helper->getPermission('proj_manageMembers'))
        {
            $this->tpfail($this->get('lng.addMemberNotAllowed'));
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
        $projPerms->load(array('user = :user AND project = :project', array(':user' => $user->hash, ':project' => $projectHash)));
        $projPerms->erase();

        $this->set('SESSION.SUCCESS', $this->get('lng.memberRemoved'));
        $this->reroute($this->get('BASE') . '/project/settings');
    }

    /**
     * 
     */
    function projectSetRole()
    {
        if (!\misc\helper::getPermission('proj_manageMembers'))
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
    function deleteProjectSettingsMilestone()
    {
		if (!\misc\helper::getPermission('proj_manageMilestones'))
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
        $roleHash = $this->get('POST.hash') ? $this->get('POST.hash') : \misc\helper::getFreeHash('Role');

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
        $role->hash = $roleHash;
        $role->issuesAssigneable = ($projHash) ? 1 : $this->get('POST.issuesAssigneable') == "on";
        $role->project = ($projHash) ? $projHash : $this->get('SESSION.project');
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
            return $roleHash;
        else
            $this->reroute($this->get('BASE') . '/project/settings/role/' . $roleHash);
    }
    
    
    function deleteRole()
    {
        $hash = $this->get('PARAMS.hash');
        
        if(\misc\helper::getPermission('proj_manageRoles')) {
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
			$category->hash = \misc\helper::getFreeHash('Category');
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

		if (\misc\helper::getPermission('proj_editProject'))
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
    function projectAdd() 
    {
        $hash = \misc\helper::getFreeHash('Project');
        
        $ax = new Axon('Project');
        $ax->name = $this->get('POST.name');
        $ax->description = $this->get('POST.description');
        $ax->public = ($this->get('POST.public') == 'on') ? 1 : 0;
        $ax->hash = $hash;
        $ax->save();

        $cmain = new cmain();
        $cmain->selectProject($hash, false);
        
        $perms = new ProjectPermission();
        $perms->user = $this->get('SESSION.user.hash');
        $perms->project = $hash;
        $perms->role = $this->addEditRole($hash);
        $perms->save();
        
        $milestone = new cmilestone;
        $milestone->addEditMilestone($hash);
        
        #$this->addCategory($this->get('lng.uncategorized'), $projId);
        
        \misc\helper::addActivity(F3::get('lng.projCreated'));
        
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
