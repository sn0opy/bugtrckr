<?php

/**
 * project\view.php
 * 
 * Project view
 * 
 * @package Main
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
namespace project;

class view extends \misc\controller
{
    /**
     * 
     */
    function showProjectSettings()
    {
        $projectHash = $this->get('SESSION.project');

        if($projectHash != "") {      
            $project = new \project\model();
            $project->load(array('hash = :hash', array(':hash' => $projectHash)));

            $role = new \role\model();
            $roles = $role->find(array('project = :hash', array(':hash' => $projectHash)));

            $projPerms = new \userPerms\model();
            $projPerms = $projPerms->find(array('project = :hash', array(':hash' => $projectHash)));


            $milestone = new \milestone\model();
            $milestones = $milestone->find(array('project = :hash', array(':hash' => $projectHash)));

            // TODO: this here is wrong!
            $user = new \user\model();
            $users = $user->find();

            $categories = new \category\model();
            $categories = $categories->find();

            if (!$project->hash)
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
    
    /**
     * 
     */
    function showProjectSettingsRole()
    {
        $roleHash = $this->get('PARAMS.hash');

        $role = new \role\model();
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
}


?>
