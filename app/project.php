<?php

/**
 *
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Project extends Controller
{
  public function projectAddMember($f3)
  {
    $f3->get("log")->write("Calling /project/settings/member/add");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));

    if(!Helper::getPermission('proj_manageMembers'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $projectHash = $f3->get('SESSION.project');
    $userHash = $f3->get('POST.member');
    $roleHash = $f3->get('POST.role');

    $role = new DB\SQL\Mapper($this->db, 'Role');
    $role->load(array('hash = :hash', array(':hash' => $roleHash)));

    if($role->dry())
      return $this->tpfail($f3->get("lng.cantGetRole"));

    $user = new DB\SQL\Mapper($this->db, 'User');
    $user->load(array('hash = :hash', array(':hash' => $userHash)));

    if($user->dry())
      return $this->tpfail($f3->get("lng.cantGetUser"));

    $projPerms = new DB\SQL\Mapper($this->db, 'ProjectPermission');
    $projPerms->load(array('user = :user AND project = :project', array(':user' => $user->hash, ':project' => $projectHash)));

    if (!$projPerms->dry())
      return $this->tpfail($f3->get('lng.userExistsInProj'));

    $projPerms->user = $user->hash;
    $projPerms->role = $role->hash;
    $projPerms->project = $projectHash;
    $projPerms->save();

    $f3->reroute('/project/settings#members');
  }

  /**
   *
   */
  public function projectDelMember($f3)
  {
    $f3->get("log")->write("Calling /project/setttings/member/delete");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));

    if(!Helper::getPermission('proj_manageMembers'))
      return $this->tpfail($f3->get('lng.addMemberNotAllowed'));

    $userHash = $f3->get('POST.user');
    $projectHash = $f3->get('SESSION.project');

    $user = new DB\SQL\Mapper($this->db, 'User');
    $user->load(array('hash = :hash', array(':hash' => $userHash)));

    if($user->dry())
      return $this->tpfail($f3->get("lng.cantGetUser"));

    $projPerms = new DB\SQL\Mapper($this->db, 'ProjectPermission');
    $projPerms->load(array('user = :user AND project = :project', array(':user' => $user->hash, ':project' => $projectHash)));
    $projPerms->erase();

    $f3->set('SESSION.SUCCESS', $f3->get('lng.memberRemoved'));
    $f3->reroute('/project/settings#members');
  }
	
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  public function projectSetRole($f3)
  {
    $f3->get("log")->write("Calling /project/settings/member/setrole");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));

    if(!Helper::getPermission('proj_manageMembers'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $projectHash = $f3->get('SESSION.project');

    $user = new DB\SQL\Mapper($this->db, 'User');
    $user->load(array('hash = :hash', array(':hash' => $f3->get('POST.user'))));

    if(!$user->hash)
      return $this->tpfail($f3->get('lng.cantGetUser'));

    $role = new DB\SQL\Mapper($this->db, 'Role');
    $role->load(array('hash = :hash', 
    array(':hash' => $f3->get('POST.role'))));

    if(!$role->hash)
      return $this->tpfail($f3->get('lng.gettingRoleFail'));

    if($role->project != $projectHash)
      return $this->tpfail($f3->get('lng.roleDoesNotBelong'), "role->project = " . $role->project . ", projectHash = " . $projectHash);

    $perms = new DB\SQL\Mapper($this->db, 'ProjectPermission');
    $perms->load( array('project = :proj AND user = :user',
                  array(':proj' => $projectHash, ':user' => $user->hash)));
    $perms->role = $role->hash;
    $perms->save();

    $f3->reroute('/project/settings#members');
  }

	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  public function deleteProjectSettingsMilestone($f3)
  {
    $f3->get("log")->write("Calling deleteProjectSettingsMilestone with @hash = " . $f3->get("PARAMS.hash"));

    if(!Helper::getPermission('proj_manageMilestones'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $msHash = $f3->get('PARAMS.hash');

    $milestone = new DB\SQL\Mapper($this->db, 'Milestone');
    $milestone->load(array('hash = :hash', array(':hash' => $msHash)));

    if(!$milestone->hash)
      return $this->tpfail($f3->get('lng.gettingMSFail'));

    $tickets = new DB\SQL\Mapper($this->db, 'Ticket');
    $count = $tickets->found(array('milestone = :ms', array(':ms' => $milestone->hash)));

    if($count > 0)
      return $this->tpfail($f3->get('lng.removeMSFail'));

    $milestone->erase();
    $f3->reroute('/project/settings#milestones');
  }
	
	/**
	 * 
	 * @param type $f3
	 * @param type $projHash
	 * @return type
	 */
  public function addEditRole($f3 = false, $url = false, $projHash = false)
  {
    $f3->get("log")->write("Calling /project/settings/role/edit");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));

   	if(!$f3)
      $f3 = Base::instance();
		
    $roleHash = $f3->get('POST.hash') ? $f3->get('POST.hash') : Helper::getFreeHash('Role');
    $role = new DB\SQL\Mapper($this->db, 'Role');
		
    if($f3->exists('POST.hash')) {
      $role->load(array('hash = :hash', array(':hash' => $roleHash)));

      if ($role->dry())
        return $this->tpfail($f3->get('lng.editRoleFail'));
    }
		
    $role->project = $projHash ? $projHash : $f3->get('SESSION.project');
    $role->hash = $roleHash;
    $role->name = $projHash ? 'Admin' : $f3->get('POST.name');
    $role->issuesAssigneable = $projHash ? 1 : $f3->get('POST.issuesAssigneable') == "on";        
    $role->iss_addIssues = $projHash ? 1 : $f3->get('POST.iss_addIssues') == "on";
    $role->proj_editProject = $projHash ? 1 : $f3->get('POST.proj_editProject') == "on";
    $role->proj_manageMembers = $projHash ? 1 : $f3->get('POST.proj_manageMembers') == "on";
    $role->proj_manageMilestones = $projHash ? 1 : $f3->get('POST.proj_manageMilestones') == "on";
    $role->proj_manageRoles = $projHash ? 1 : $f3->get('POST.proj_manageRoles') == "on";
    $role->iss_editIssues = $projHash ? 1 : $f3->get('POST.iss_editIssues') == "on";
    $role->iss_addIssues = $projHash ? 1 : $f3->get('POST.iss_addIssues') == "on";
    $role->iss_deleteIssues = $projHash ? 1 : $f3->get('POST.iss_deleteIssues') == "on";
    $role->iss_moveIssue = $projHash ? 1 : $f3->get('POST.iss_moveIssue') == "on";
    $role->iss_editWatchers = $projHash ? 1 : $f3->get('POST.iss_editWatchers') == "on";
    $role->iss_addWatchers = $projHash ? 1 : $f3->get('POST.iss_addWatchers') == "on";
    $role->iss_viewWatchers = $projHash ? 1 : $f3->get('POST.iss_viewWatchers') == "on";
    $role->wiki_editWiki = $projHash ? 1 : $f3->get('POST.wiki_editWiki') == "on";
    $role->proj_manageCategories = $projHash ? 1 : $f3->get('POST.proj_manageCategories') == "on";
    $role->save();

    if($f3->exists('POST.hash'))
      $f3->set('SESSION.SUCCESS', $f3->get('lng.roleEdited'));
    else
	    $f3->set('SESSION.SUCCESS', $f3->get('lng.roleAdded'));
		
    if($projHash)
      return $roleHash;
    else
      $f3->reroute('/project/settings#roles');
  }

  /**
   *
   */  
  public function deleteRole($f3)
  {
    $f3->get("log")->write("Calling /project/settings/role/delete/@hash with hash = " . $f3->get("PARAMS.hash"));

    $hash = $f3->get('PARAMS.hash');

    if (!Helper::getPermission('proj_manageRoles'))
      return $this->tpfail($f3->get("lng.insuffPermissions"));
        
    $ax = new DB\SQL\Mapper($this->db, 'Role');
    $ax->load(array('hash = :hash', array(':hash' => $hash)));

    $ax2 = new DB\SQL\Mapper($this->db, 'ProjectPermission');

    if($ax2->count('role = :role', array(':role' => $ax->hash)))
      return $this->tpfail($f3->get('lng.deleteRoleFail'));

    $ax->erase();
    $f3->set('SESSION.SUCCESS', $f3->get('lng.roleDeleted'));
    $f3->reroute('/project/settings#roles');     
  }
	
	/**
	 * 
	 * @param type $f3
	 * @param type $params
	 * @param type $projHash
	 * @param type $name
	 * @return type
	 */
  public function addEditCategory($f3 = false, $params = false, $projHash = false, $name = false)
  {
    if(!$f3)
      $f3 = Base::instance();

    $f3->get("log")->write("Calling /project/settings/category/add");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));
		
    if(!Helper::getPermission('proj_editProject'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $category = new DB\SQL\Mapper($this->db, 'Category');

    if($f3->get('POST.hash') != "")
    {
      $category->load(array('hash = :hash', array(':hash' => $f3->get('POST.hash'))));
      if ($category->dry())
        return $this->tpfail($f3->get("lng.cantGetCategory"));
    }
    else
    {
      $category->project = ($projHash) ? $projHash : $f3->get('SESSION.projectHash');
      $category->hash = Helper::getFreeHash('Category');
    }

    $category->name = ($name) ? $name : $f3->get('POST.name');
    $category->save();

    if($f3->get('POST.hash') != "")
      $f3->set('SESSION.SUCCESS', $f3->get('lng.categoryEdited'));
    else
      if(!$projHash)
        $f3->set('SESSION.SUCCESS', $f3->get('lng.categoryAdded'));

    if(!$projHash)
      $f3->reroute('/project/settings#categories');
  }
	
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  public function deleteCategory($f3)
  {
    $f3->get("log")->write("Calling /project/settings/category/delete/@hash with @hash = " . $f3->get("PARAMS.hash"));

    if(!Helper::getPermission('proj_editProject'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $hash = $f3->get('PARAMS.hash');

    $category = new DB\SQL\Mapper($this->db, 'Category');
    $category->load(array('hash = :hash', array(':hash' => $hash)));

    $category->erase();
    $this->set('SESSION.SUCCESS', $f3->get('lng.categoryDeleted'));
    $this->reroute('/project/settings#categories');
	}
	
	/**
	 * 
	 * @param type $f3
	 */
  public function projectAdd($f3)
  {
		/* TODO: Fehlgedanke
		if (!$this->get('SESSION.user.admin'))
			return $this->tpfail($this->get('lng.insuffPermissions'));			
     */

    $f3->get("log")->write("Calling /project/add");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));
		
    $hash = Helper::getFreeHash('Project');
      
    $ax = new DB\SQL\Mapper($this->db, 'Project');
    $ax->name = $f3->get('POST.name');
    $ax->description = $f3->get('POST.description');
    $ax->public = ($f3->get('POST.public') == 'on') ? 1 : 0;
    $ax->hash = $hash;
    $ax->save();

    $cmain = new main;
    $cmain->selectProject(false, false, $hash, false);
    
    $perms = new DB\SQL\Mapper($this->db, 'ProjectPermission');
    $perms->user = $f3->get('SESSION.user.hash');
    $perms->project = $hash;
    $perms->role = $this->addEditRole(false, false, $hash);
    $perms->save();
         
    $milestone = new Milestone;
    $milestone->addEditMilestone(false, false, $hash);
        
    $this->addEditCategory(false, false, $hash, $f3->get('lng.uncategorized'));
       
    Helper::addActivity($f3->get('lng.projCreated'), 0, '', '', $hash);
      
    $f3->reroute('/'); 
  }
	
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  public function projectEditMain($f3)
  {
    $f3->get("log")->write("Calling /project/settings/main/edit");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));

    if (!Helper::getPermission('proj_editProject'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $project = new DB\SQL\Mapper($this->db, 'Project');
    $project->load(array('hash = :hash', array(':hash' => $f3->get('SESSION.project'))));
    $project->name = $f3->get('POST.name');
    $project->public = $f3->get('POST.public')=='on';
    $project->description = $f3->get('POST.description');
    $project->save();

    if (!$project->hash) 
      return $this->tpfail($this->get('lng.saveProjectFail'));
		
    $f3->set('SESSION.SUCCESS', $f3->get('lng.projectEdited'));

    $f3->reroute('/project/settings');
  }
	
  /**
   * 
   */
  public function showProjectSettings($f3)
  {
    $f3->get("log")->write("Calling /project/settings");
    
    $projectHash = $f3->get('SESSION.project');

    if($projectHash != "")
    {
      $project = new DB\SQL\Mapper($this->db, 'Project');
      $project->load(array('hash = :hash', array(':hash' => $projectHash)));

      if (!$project->hash)
        return $this->tpfail($f3->get('lng.openProjectFail'));

      $role = new DB\SQL\Mapper($this->db, 'Role');
      $roles = $role->find(array('project = :hash', array(':hash' => $projectHash)));

      $projPerms = new DB\SQL\Mapper($this->db, 'user_perms');
      $projPerms = $projPerms->find(array('project = :hash', array(':hash' => $projectHash)));

      $milestone = new DB\SQL\Mapper($this->db, 'Milestone');
      $milestones = $milestone->find(array('project = :hash', array(':hash' => $projectHash)));

			// TODO: don't load EVERY user
			// - Idea: Ajax'd input field to auto-complete user names
      $user = new DB\SQL\Mapper($this->db, 'User');
      $users = $user->find();

      $categories = new DB\SQL\Mapper($this->db, 'Category');
      $categories = $categories->find();

      $f3->set('users', $users);
      $f3->set('projMilestones', $milestones);
      $f3->set('projRoles', $roles);
      $f3->set('projMembers', $projPerms);
      $f3->set('projDetails', $project);
      $f3->set('projCategories', $categories);
      $f3->set('template', 'projectSettings.tpl.php');
      $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.settings'));
      $f3->set('onpage', 'settings');
    }
    else
    {
      $f3->set('SESSION.FAILURE', $f3->get('lng.noProject'));
      $f3->set('template', 'projectSettings.tpl.php');
      $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.settings'));
    }
  }
	
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  public function showProjectSettingsRole($f3)
  {
    $f3->get("log")->write("Calling /project/settings/role/@hash with @hash = " . $f3->get("PARAMS.hash"));

    $roleHash = $f3->get('PARAMS.hash');

    $role = new DB\SQL\Mapper($this->db, 'Role');
    $role->load(array('hash = :hash', array(':hash' => $roleHash)));

    if (!$role->hash)
       return $this->tpfail($f3->get("lng.cantGetRole"));

    $f3->set('roleData', $role);
    $f3->set('template', 'projectSettingsRole.tpl.php');
    $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.settings') . ' › ' . $f3->get('lng.role') . ' › ' . $f3->get('roleData')->name);
    $f3->set('onpage', 'settings');
  }
	
	/**
	 * 
	 * @param type $f3
	 * @return type
   */
  public function showProjectSettingsMilestone($f3)
  {
    $f3->get("log")->write("Calling /project/settings/milestone/@hash with @hash = " . $f3->get("PARAMS.hash"));

    $msHash = $f3->get('PARAMS.hash');

    $milestone = new DB\SQL\Mapper($this->db, 'Milestone');
    $milestone->load(array('hash = :hash', array(':hash' => $msHash)));

    if(!$milestone->hash)
       return $this->tpfail($f3->get('lng.gettingMSFail'));

    $f3->set('msData', $milestone);
    $f3->set('template', 'projectSettingsMilestone.tpl.php');
    $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.settings') . ' › ' . $f3->get('lng.milestone') . ' › ' . $f3->get('msData')->name);
    $f3->set('onpage', 'settings');
  }
	
	/**
	 * 
	 * @param type $f3
	 */
  public function showAddRole($f3)
  {
    $f3->get("log")->write("Calling /project/settings/role/add");

    $f3->set('template', 'projectSettingsRoleAdd.tpl.php');
    $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.settings') . ' › ' . $f3->get('lng.addrole'));
    $f3->set('onpage', 'settings');
  }
	
	/**
	 * 
	 * @param type $f3
	 */
  public function showAddMilestone($f3)
  {
    $f3->get("log")->write("Calling /project/settings/milestone/add");

    $f3->set('today', date('Y-m-d', time()));
    $f3->set('template', 'projectSettingsMilestoneAdd.tpl.php');
    $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.settings') . ' › ' . $f3->get('lng.addmilestone'));
    $f3->set('onpage', 'settings');
  }
	
	/**
	 * 
	 * @param type $f3
	 */
  public function showAddCategory($f3)
  {
    $f3->get("log")->write("Calling /project/settings/category/add");

    $f3->set('template', 'projectSettingsCategoryAdd.tpl.php');
    $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.settings') . ' › ' . $f3->get('lng.addcategory'));
    $f3->set('onpage', 'settings');
  }
	
	/**
	 * 
	 * @param type $f3
	 */
  public function showEditCategory($f3)
  {
    $f3->get("log")->write("Calling /project/settings/category/edit/@hash with @hash = " . $f3->get("PARAMS.hash"));

    $hash = $f3->get('PARAMS.hash');

    $category = new DB\SQL\Mapper($this->db, 'Category');
    $category->load(array('hash = :hash', array(':hash' => $hash)));

    $f3->set('category', $category);
    $f3->set('template', 'projectSettingsCategoryEdit.tpl.php');
    $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.settings') . ' › ' . $f3->get('lng.editcategory'));
    $f3->set('onpage', 'settings');
  }
	
	/**
	 * 
	 * @param type $f3
	 */
  public function showAddProject($f3)
  {
    $f3->get("log")->write("Calling /project/add");

    $f3->set('template', 'projectAdd.tpl.php');
    $f3->set('pageTitle', $f3->get('lng.project') . ' › ' . $f3->get('lng.add'));
    $f3->set('onpage', 'settings');
  } 
}
