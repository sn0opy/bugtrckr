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

class controller extends \misc\controller
{
    function projectAddMember()
    {
        $helper = new \misc\helper;
        
        if (!$helper->getPermission('proj_manageMembers'))
        {
            $this->tpfail($this->get('lng.insuffPermissions'));
            return;
        }

        $projectHash = $this->get('SESSION.project');
        $userHash = $this->get('POST.member');
        $roleHash = $this->get('POST.role');

        $role = new \role\model();
        $role->load(array('hash = :hash', array(':hash' => $roleHash)));

        if ($role->dry())
        {
            $this->tpfail('Failure while getting role.');
            return;
        }

        $user = new \user\model();
        $user->load(array('hash = :hash', array(':hash' => $userHash)));

        if ($user->dry())
        {
            $this->tpfail('Failure while getting user.');
            return;
        }

        $projPerms = new \projPerms\model();
        $projPerms->load(array('user = :user AND project = :project',
            array(':user' => $user->hash, ':project' => $projectHash)));

        if (!$projPerms->dry())
        {
            $this->tpfail($this->get('lng.userExistsInProj'));
            return;
        }

        $projPerms->user = $user->hash;
        $projPerms->role = $role->hash;
        $projPerms->project = $projectHash;
        $projPerms->save();

        $this->reroute('/project/settings#members');
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

        $user = new \user\model();
        $user->load(array('hash = :hash', array(':hash' => $userHash)));

        if ($user->dry())
        {
            $this->tpfail('Failure while getting user.');
            return;
        }

        $projPerms = new \projPerms\model();
        $projPerms->load(array('user = :user AND project = :project', array(':user' => $user->hash, ':project' => $projectHash)));
        $projPerms->erase();

        $this->set('SESSION.SUCCESS', $this->get('lng.memberRemoved'));
        $this->reroute('/project/settings#members');
    }

    /**
     * 
     */
    function projectSetRole()
    {
        if (!\misc\helper::getPermission('proj_manageMembers'))
        {
            $this->tpfail($this->get('lng.insuffPermissions'));
            return;
        }

        $projectHash = $this->get('SESSION.project');

        $user = new \user\model();
        $user->load(array('hash = :hash', array(':hash' => $this->get('POST.user'))));

        if (!$user->hash)
            return $this->tpfail($this->get('lng.gettingUserFail'));

        $role = new \role\model();
        $role->load(array('hash = :hash', array(':hash' => $this->get('POST.role'))));

        if (!$role->hash)
            return $this->tpfail($this->get('lng.gettingRoleFail'));

        if ($role->project != $projectHash)
            return $this->tpfail($this->get('lng.roleDoesNotBelong'));

        $perms = new \projPerms\model();
        $perms->load(array('project = :proj AND user = :user',
            array(':proj' => $projectHash, ':user' => $user->hash)));
        $perms->role = $role->hash;
        $perms->save();

        $this->reroute('/project/settings#members');
    }


    /**
     * 
     */
    function deleteProjectSettingsMilestone()
    {
        if (!\misc\helper::getPermission('proj_manageMilestones'))
            return $this->tpfail($this->get('lng.insuffPermissions'));

        $msHash = $this->get('PARAMS.hash');

        $milestone = new \milestone\model();
        $milestone->load(array('hash = :hash', array(':hash' => $msHash)));

        if (!$milestone->hash)
            return $this->tpfail($this->get('lng.gettingMSFail'));

        $tickets = new \ticket\model();
        $count = $tickets->found(array('milestone = :ms', array(':ms' => $milestone->hash)));

        if ($count > 0)
            return $this->tpfail($this->get('lng.removeMSFail'));

        $milestone->erase();
        $this->reroute('/project/settings#milestones');
    }

    /**
     * 
     */
    function addEditRole($projHash = false)
    {
        $roleHash = $this->get('POST.hash') ? $this->get('POST.hash') : \misc\helper::getFreeHash('Role');

        $role = new \role\model();
        if ($this->exists('POST.hash'))
        {
            $role->load(array('hash = :hash', array(':hash' => $roleHash)));

            if ($role->dry())
                return $this->tpfail($this->get('lng.editRoleFail'));
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
        $role->iss_moveIssue = ($projHash) ? 1 : $this->get('POST.iss_moveIssue') == "on";
        $role->iss_editWatchers = ($projHash) ? 1 : $this->get('POST.iss_editWatchers') == "on";
        $role->iss_addWatchers = ($projHash) ? 1 : $this->get('POST.iss_addWatchers') == "on";
        $role->iss_viewWatchers = ($projHash) ? 1 : $this->get('POST.iss_viewWatchers') == "on";
        $role->save();

        if($projHash)
            return $roleHash;
        else
            $this->reroute('/project/settings#roles');
    }
    
    
    function deleteRole()
    {
        $hash = $this->get('PARAMS.hash');
        
        if(\misc\helper::getPermission('proj_manageRoles')) {
            $ax = new Axon('Role');
            $ax->load(array('hash = :hash', array(':hash' => $hash)));

            $ax2 = new \Axon('ProjectPermission');

            if($ax2->found('role = '.$ax->hash))
	            return $this->tpfail($this->get('lng.deleteRoleFail'));

            $ax->erase();
            $this->set('SESSION.SUCCESS', $this->get('lng.roleDeleted'));
            $this->reroute('/project/settings#roles');     
        } else {
            $this->tpfail($this->get('lng.insuffPermissions'));
        }
    }

    /**
     * 
     */
    function addEditCategory($projHash = false, $name = false)
    {     
		if (!\misc\helper::getPermission('proj_editProject'))
			return $this->tpfail($this->get('lng.insuffPermissions'));

        $category = new \category\model();

        if ($this->get('POST.hash') != "")
            $category->load(array('hash = :hash', array(':hash' => $this->get('POST.hash'))));
        else
        {
            $category->project = ($projHash) ? $projHash : $this->get('SESSION.projectHash');
            $category->hash = \misc\helper::getFreeHash('Category');
        }

        $category->name = ($name) ? $name : $this->get('POST.name');
        $category->save();

        if ($this->get('POST.hash') != "")
            $this->set('SESSION.SUCCESS', $this->get('lng.categoryEdited'));
        else
            if(!$projHash)
                $this->set('SESSION.SUCCESS', $this->get('lng.categoryAdded'));

        if(!$projHash)
            $this->reroute('/project/settings#categories');

    }

	/**
	 *
	 */
	function deleteCategory()
	{
		if (!\misc\helper::getPermission('proj_editProject'))
			return $this->tpfail($this->get('lng.insuffPermissions'));			

            $hash = $this->get('PARAMS.hash');

            if (\misc\helper::getPermission('proj_editProject'))
            {
                $category = new \category\model();
                $category->load(array('hash = :hash', array(':hash' => $hash)));

                $category->erase();
                $this->set('SESSION.SUCCESS', $this->get('lng.categoryDeleted'));
                $this->reroute('/project/settings#categories');
            }
            else
                $this->tpfail($this->get('lng.insuffPermissions'));
	}
    
    /**
     * 
     */
    function projectAdd() 
    {
		if (!$this->get('SESSION.user.admin'))
			return $this->tpfail($this->get('lng.insuffPermissions'));			

        $hash = \misc\helper::getFreeHash('Project');
        
        $ax = new \Axon('Project');
        $ax->name = $this->get('POST.name');
        $ax->description = $this->get('POST.description');
        $ax->public = ($this->get('POST.public') == 'on') ? 1 : 0;
        $ax->hash = $hash;
        $ax->save();

        $cmain = new \misc\main();
        $cmain->selectProject($hash, false);
        
        $perms = new \projPerms\model();
        $perms->user = $this->get('SESSION.user.hash');
        $perms->project = $hash;
        $perms->role = $this->addEditRole($hash);
        $perms->save();
        
        $milestone = new \milestone\controller;
        $milestone->addEditMilestone($hash);
        
        $this->addEditCategory($hash, $this->get('lng.uncategorized'));
        
        \misc\helper::addActivity($this->get('lng.projCreated'), 0, '', '', $hash);
        
        $this->reroute('/');        
    }

    /**
     * 
     */
    function projectEditMain()
    {
		if (!\misc\helper::getPermission('proj_editProject'))
			return $this->tpfail($this->get('lng.insuffPermissions'));

        $project = new \project\model();
        $project->load(array('hash = :hash', array(':hash' => $this->get('SESSION.project'))));
        $project->name = $this->get('POST.name');
        $project->public = $this->get('POST.public')=='on';
        $project->description = $this->get('POST.description');
        $project->save();

        if (!$project->hash)
        {
            $this->tpfail($this->get('lng.saveProjectFail'));
            return;
        }

        $this->reroute('/project/settings');
    }
}
