<?php

/**
 * role.php
 * 
 * Getter / setter class for roles
 * 
 * @package Role
 * @author Sascha Ohms
 * @author Phillipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
**/

class Role extends F3instance
{
    private $id;
    private $name;
    private $hash;
    private $projectId;
    private $issuesAssigneable;
    private $proj_editProject;
    private $proj_manageMembers;
    private $iss_editIssues;
    private $iss_addIssues;
    private $iss_deleteIssues;
    private $iss_moveIssue;
    private $iss_editWatchers;
    private $iss_addWatchers;
    private $iss_viewWatchers;

    private $ax;

    function __construct()
    {
        parent::__construct();

        $this->ax = new Axon('Role');
    }
    
    public function getHash()
    {
        return $this->hash;
    }
    
    public function setHash($hash)
    {
        $this->hash = $hash;
    }
    
    public function getProjectId()
    {
        $this->projectId;
    }
    
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getIssuesAssigneable()
    {
        return $this->issuesAssigneable;
    }

    public function setIssuesAssigneable($issuesAssigneable)
    {
        $this->issuesAssigneable = $issuesAssigneable;
    }

    public function getProj_editProject()
    {
        return $this->proj_editProject;
    }

    public function setProj_editProject($proj_editProject)
    {
        $this->proj_editProject = $proj_editProject;
    }

    public function getProj_manageMembers()
    {
        return $this->proj_manageMembers;
    }

    public function setProj_manageMembers($proj_manageMembers)
    {
        $this->proj_manageMembers = $proj_manageMembers;
    }

    public function getIss_editIssues()
    {
        return $this->iss_editIssues;
    }

    public function setIss_editIssues($iss_editIssues)
    {
        $this->iss_editIssues = $iss_editIssues;
    }

    public function getIss_addIssues()
    {
        return $this->iss_addIssues;
    }

    public function setIss_addIssues($iss_addIssues)
    {
        $this->iss_addIssues = $iss_addIssues;
    }

    public function getIss_deleteIssues()
    {
        return $this->iss_deleteIssues;
    }

    public function setIss_deleteIssues($iss_deleteIssues)
    {
        $this->iss_deleteIssues = $iss_deleteIssues;
    }

    public function getIss_moveIssue()
    {
        return $this->iss_moveIssue;
    }

    public function setIss_moveIssue($iss_moveIssue)
    {
        $this->iss_moveIssue = $iss_moveIssue;
    }

    public function getIss_editWatchers()
    {
        return $this->iss_editWatchers;
    }

    public function setIss_editWatchers($iss_editWatchers)
    {
        $this->iss_editWatchers = $iss_editWatchers;
    }

    public function getIss_addWatchers()
    {
        return $this->iss_addWatchers;
    }

    public function setIss_addWatchers($iss_addWatchers)
    {
        $this->iss_addWatchers = $iss_addWatchers;
    }

    public function getIss_viewWatchers()
    {
        return $this->iss_viewWatchers;
    }

    public function setIss_viewWatchers($iss_viewWatchers)
    {
        $this->iss_viewWatchers = $iss_viewWatchers;
    }

    public function save()
    {
        $this->ax->id = $this->id;
        $this->ax->name = $this->name;
        $this->ax->hash = $this->hash;
        $this->ax->projectId = $this->projectId;
        $this->ax->issuesAssigneable = $this->issuesAssigneable;
        $this->ax->proj_editProject = $this->proj_editProject;
        $this->ax->proj_manageMembers = $this->proj_manageMembers;
        $this->ax->iss_editIssues = $this->iss_editIssues;
        $this->ax->iss_addIssues = $this->iss_addIssues;
        $this->ax->iss_deleteIssues = $this->iss_deleteIssues;
        $this->ax->iss_moveIssue = $this->iss_moveIssue;
        $this->ax->iss_editWatchers = $this->iss_editWatchers;
        $this->ax->iss_addWatchers = $this->iss_addWatchers;
        $this->ax->iss_viewWatchers = $this->iss_viewWatchers;
        $this->ax->save();
    }

    public function load($stmt)
    {
        $this->ax->load($stmt);

        if(!$this->ax->dry())
        {
            $this->id = $this->ax->id;
            $this->name = $this->ax->name;
            $this->hash = $this->ax->hash;
            $this->projectId = $this->ax->projectId;
            $this->issuesAssigneable = $this->ax->issuesAssigneable;
            $this->proj_editProject = $this->ax->proj_editProject;
            $this->proj_manageMembers = $this->ax->proj_manageMembers;
            $this->iss_editIssues = $this->ax->iss_editIssues;
            $this->iss_addIssues = $this->ax->iss_addIssues;
            $this->iss_deleteIssues = $this->ax->iss_deleteIssues;
            $this->iss_moveIssue = $this->ax->iss_moveIssue;
            $this->iss_editWatchers = $this->ax->iss_editWatchers;
            $this->iss_addWatchers = $this->ax->iss_addWatchers;
            $this->iss_viewWatchers = $this->ax->iss_viewWatchers;
        }
    }

    public function toArray()
    {
        $permission = array();

        $permission['id'] = $this->id;
        $permission['name'] = $this->name;
        $permission['hash'] = $this->hash;
        $permission['projectId'] = $this->projectId;
        $permission['issuesAssigneable'] = $this->issuesAssigneable;
        $permission['proj_editProject'] = $this->proj_editProject;
        $permission['proj_manageMembers'] = $this->proj_manageMembers;
        $permission['iss_editIssues'] = $this->iss_editIssues;
        $permission['iss_addIssues'] = $this->iss_addIssues;
        $permission['iss_deleteIssues'] = $this->iss_deleteIssues;
        $permission['iss_moveIssue'] = $this->iss_moveIssue;
        $permission['iss_editWatchers'] = $this->iss_editWatchers;
        $permission['iss_addWatchers'] = $this->iss_addWatchers;
        $permission['iss_viewWatchers'] = $this->iss_viewWatchers;

        return $permission;

    }
}
