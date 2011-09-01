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
        $projectId = $this->get('SESSION.project');

        if($projectId) {      
            $project = new Project;
            $project->load(array('id = :id', array(':id' => $projectId)));

            $role = new Role();
            $roles = $role->find('projectId = ' . $projectId);

            $projPerms = new user_perms();
            $projPerms = $projPerms->find('projectId = ' . $projectId);

            $milestone = new Milestone();
            $milestones = $milestone->find('project = ' . $projectId);

            $user = new User();
            $users = $user->find();

            $categories = new Category();
            $categories = $categories->find();

            if (!$project->id) //|| !$roles || !$milestones || !$users || !$categories)
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

        $projectId = $this->get('SESSION.project');
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
        $projPerms->load(array('userId = :userId AND projectId = :projectId',
            array(':userId' => $user->id, ':projectId' => $projectId)));

        if (!$projPerms->dry())
        {
            $this->tpfail('User already exists in this project.');
            return;
        }

        $projPerms->userId = $user->id;
        $projPerms->roleId = $role->id;
        $projPerms->projectId = $projectId;
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
        $projectId = $this->get('SESSION.project');

        $user = new User();
        $user->load(array('hash = :hash', array(':hash' => $userHash)));

        if ($user->dry())
        {
            $this->tpfail('Failure while getting user.');
            return;
        }

        $projPerms = new ProjectPermission();
        $projPerms->load('userId = ' . $user->id . ' AND projectId = ' . $projectId);
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

        $projectId = $this->get('SESSION.project');

        $user = new user();
        $user->load(array('hash = :hash', array(':hash' => $this->get('POST.user'))));

        if (!$user->id)
        {
            $this->tpfail("Failure while getting User");
            return;
        }

        $role = new Role();
        $role->load(array('hash = :hash', array(':hash' => $this->get('POST.role'))));

        if (!$role->id)
        {
            $this->tpfail("Failure while getting Role");
            return;
        }

        if ($role->projectId != $projectId)
        {
            $this->tpfail("Role does not belong to this project.");
            return;
        }

        $perms = new ProjectPermission();
        $perms->load(array('projectId = :proj AND userId = :user',
            array(':proj' => $projectId,
                ':user' => $user->id)));
        $perms->roleId = $role->id;
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

        if (!$role->id)
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

        if (!$milestone->id)
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
    function addEditRole($projId = false)
    {
        $roleHash = $this->get('POST.hash') ? $this->get('POST.hash') : helper::getFreeHash('Role');

        $role = new role();
        if (F3::exists('POST.hash'))
        {
            $role->load(array('hash = :hash', array(':hash' => $roleHash)));

            if ($role->dry())
            {
                $this->tpfail('Failure while editing role.');
                return;
            }
        }

        $role->name = ($projId) ? 'Admin' : $this->get('POST.name');
        $role->hash = ($projId) ? helper::getFreeHash('Role') : $roleHash;
        $role->issuesAssigneable = ($projId) ? 1 : $this->get('POST.issuesAssigneable') == "on";
        $role->projectId = ($projId) ? $projId : $this->get('SESSION.project');
        $role->iss_addIssues = ($projId) ? 1 : $this->get('POST.iss_addIssues') == "on";
        $role->proj_editProject = ($projId) ? 1 : $this->get('POST.proj_editProject') == "on";
        $role->proj_manageMembers = ($projId) ? 1 : $this->get('POST.proj_manageMembers') == "on";
        $role->proj_manageMilestones = ($projId) ? 1 : $this->get('POST.proj_manageMilestones') == "on";
        $role->proj_manageRoles = ($projId) ? 1 : $this->get('POST.proj_manageRoles') == "on";
        $role->iss_editIssues = ($projId) ? 1 : $this->get('POST.iss_editIssues') == "on";
        $role->iss_addIssues = ($projId) ? 1 : $this->get('POST.iss_addIssues') == "on";
        $role->iss_deleteIssues = ($projId) ? 1 : $this->get('POST.iss_deleteIssues') == "on";
        $role->iss_moveIssue = ($projId) ? 1 : $this->get('POST.iss_moveIssue') == "on";
        $role->iss_editWatchers = ($projId) ? 1 : $this->get('POST.iss_editWatchers') == "on";
        $role->iss_addWatchers = ($projId) ? 1 : $this->get('POST.iss_addWatchers') == "on";
        $role->iss_viewWatchers = ($projId) ? 1 : $this->get('POST.iss_viewWatchers') == "on";
        $role->save();

        if($projId)
            return $role->_id;
        else
            $this->reroute($this->get('BASE') . '/project/settings/role/' . $roleHash);
    }

    /**
     * 
     */
    function addCategory()
    {

        $category = new Category();
        $category->name = $this->get('POST.name');
        $category->save();

        $_SESSION['SUCCESS'] = "Category added successfully";

        $this->reroute($this->get('BASE') . '/project/settings/');
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
        
        $cmain = new cmain;
        $cmain->selectProject($hash, false);
        
        $projId = $ax->_id;
        
        $perms = new ProjectPermission();
        $perms->userId = $this->get('SESSION.user.id');
        $perms->projectId = $projId;
        $perms->roleId = self::addEditRole($projId);
        $perms->save();
        
        $this->reroute($this->get('BASE').'/');        
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
            return;
        }

        $this->reroute($this->get('BASE') . '/project/settings');
    }

}
