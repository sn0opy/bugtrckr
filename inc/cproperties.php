<?php

class cproperties extends Controller
{

    /**
     * 
     */
    function showProjectSettings()
    {
        $projectId = $this->get('SESSION.project');

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
        $this->tpserve();
    }

    function projectAddMember()
    {
        if (!$this->helper->getPermission('proj_manageMembers'))
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
        if (!$this->helper->getPermission('proj_manageMembers'))
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
        if (!$this->helper->getPermission('proj_manageMembers'))
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
        {
            $role->load(array('hash = :hash', array(':hash' => $roleHash)));

            if ($role->dry())
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
    function showAddCategory()
    {
        $this->set('template', 'projectSettingsCategoryAdd.tpl.php');
        $this->set('pageTitle', '{{@lng.project}} › {{@lng.settings}} › {{@lng.addcategory}}');
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
            return;
        }

        $this->reroute($this->get('BASE') . '/project/settings');
    }

}