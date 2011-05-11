<?php

/**
 * dao.php
 * 
 * Data Access Object to recieve globally different data from the DB
 * 
 * @package Dao
 * @author Sascha Ohms
 * @author Phillipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
**/

class Dao extends F3instance
{  

    /**
     *
     * @param string $stmt
     * @return array 
     * @static
     */
    static function getRoles($stmt)
    {
        $result = array();
        $roles = F3::get('DB')->sql("SELECT id FROM Role WHERE $stmt");

        /* Selecting role failed */
        if ($roles == NULL)
            throw new Exception();

        /* Get Milestones data */
        foreach($roles as $i=>$role)
        {
            try {
                $result[$i] = new Role();
                $result[$i]->load("id = $role[id]");
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $result;
    }
    
    /**
     * returns all milestones by a given WHERE-statement
     * 
     * @param type $stmt
     * @return array 
     * @static
     */
    static function getMilestones($stmt)
    {
        $result = array();
        $milestones = F3::get('DB')->sql("SELECT id FROM Milestone WHERE $stmt");

        /* Selecting Milestones failed */
        if ($milestones == NULL)
            throw new Exception();

        /* Get Milestones data */
        foreach($milestones as $i=>$milestone)
        {
            try {
                $result[$i] = new Milestone();
                $result[$i]->load("id = $milestone[id]");
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $result;
    }

    /**
     * returns all tickets by a given WHERE-statement
     * 
     * @param string $stmt
     * @return array 
     * @static
     */
    static function getTickets($stmt)
    {
        $result = array();

        $tickets = F3::get('DB')->sql("SELECT id FROM Ticket WHERE $stmt");

        /* Selecting Tickets failed */
        if ($tickets == NULL)
            throw new Exception();

        /* Get Tickets data */
        foreach($tickets as $i=>$ticket)
        {
            try {
                $result[$i] = new Ticket();
                $result[$i]->load("id = $ticket[id]");
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $result;
    }

    /**
     * returns all activities by a given WHERE-statement
     * 
     * @param string $stmt
     * @return array
     * @static
     */
    static function getActivities($stmt)
    {
        $result = array();

        $activities = F3::get('DB')->sql("SELECT id FROM Activity WHERE $stmt");

        /* Selecting Activities failed */
        if ($activities == NULL)
            throw new Exception();

        /* Get Activities data */
        foreach($activities as $i=>$activity)
        {
            try {
                $result[$i] = new Activity();
                $result[$i]->load("id = $activity[id]");
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $result;
    }

    /**
     * returns array with projects by a given WHERE-statement
     * 
     * @param string $stmt string to change the WHERE 
     * @return array
     * @static
     */
    static function getProjects($stmt)
    {
        $result = array();

        $projects = F3::get('DB')->sql("SELECT id FROM Project WHERE $stmt");

        /* Selecting Projects failed */
        if ($projects == NULL)
            throw new Exception();

        /* Get Projects data */
        foreach($projects as $i=>$project)
        {
            try {
                $result[$i] = new Project();
                $result[$i]->load("id = $project[id]");
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $result;
    }

    /**
     * returns array of users WHERE-statement
     * 
     * @param string $stmt
     * @return array 
     * @static
     */
    static function getUsers($stmt)
    {
        $result = array();

        $users = F3::get('DB')->sql("SELECT id FROM User WHERE $stmt");

        /* Selecting Users failed */
        if ($users == NULL)
            throw new Exception();

        /* Get Users data */ 
        foreach($users as $i=>$user)
        {
            try {
                $result[$i] = new User();
                $result[$i]->load("id = $user[id]");
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $result;
    }

    /**
     * Inserts given message into activity table
     * 
     * @param string $message Ex: User1 created Ticket "Ticket1"
     * @static
     */
    static function addActivity($message)
    {
        $userId = F3::get('SESSION.userId');
        $projectId = F3::get('SESSION.project');

        try {
            $user = new User();
            $user->load("id = $userId");

            $activity = new Activity();

            $activity->setHash(helper::getFreeHash('Activity'));
            $activity->setDescription($user->getName() ." $message");
            $activity->setProject($projectId);
            $activity->setUser($userId);

            $activity->save();
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Checks whether the user has access to $permission
     * 
     * @param string $permission Permission is a predefined string stored in the db
     * @return bool
     * @static
     */
    static function getPermission($permission)
    {
        $userId = F3::get('SESSION.userId');
        $projectId = F3::get('SESSION.project');

        try {
            $user = new User();
            $user->load('id = ' .$userId);

            $projPerm = new ProjectPermission();
            $projPerm->load('userId = ' .$userId. ' AND projectId = ' .$projectId);
            
            $role = new Role();
            $role->load('id = ' .$projPerm->getRoleId());
            
        } catch (Exception $e) {
            throw $e;
        }
                
        $permissions = $role->toArray();

        if($user->getAdmin()) // admin has access to everything
            return true;

        if(in_array($permission, $permissions))
            if($permissions[$permission] == true)
                return true;

        return false;
    }

    /**
     * Returns username by given UserID
     * 
     * @param int $uid the UserID
     * @return string 
     * @static
     */
    static function getUserName($uid)
    {
        $ax = new Axon('User');
        $ax->load('id = ' .$uid);
        return $ax->name;
    }

    /**
     * Returns count of tickets for a given milestone
     * 
     * @param int $milestone the milestoneID
     * @return int 
     * @static
     */
    static function getTicketCount($milestone)
    {
        return F3::get('DB')->sql('SELECT state, COUNT(*) AS `count` FROM `Ticket` WHERE milestone = ' .$milestone. ' GROUP BY state');
    }
    
    /**
     * Returns array with all members of a given project
     * 
     * @param int $project 
     * @return array
     * @static
     */
    static function getProjectMembers($project)
    {
        
        $results = F3::get('DB')->sql('SELECT userId, roleId FROM ProjectPermission WHERE projectId = ' .$project);
     
        $user = new User();        
        foreach($results as $key=>$row) 
        {
            $user->load('id = ' .$row['userId']);
            $members[$key] = $user->toArray(true);
            $members[$key]['role'] = $row['roleId'];
        }
        
        return $members;
    }
    
    /**
     * returns a project name by given hash
     * 
     * @static
     * @param string $hash
     * @return string project name 
     */
    static function getProjectName($hash)
    {
        $ax = new Axon('project');
        $ax->load('hash = "' .$hash. '"');
        
        if(!$ax->dry())
            return $ax->name;
    }

    /**
     * returns all admins of a project
     * 
     * @param int $project 
     * @return array
     * @static
     */
    static function getProjectAdmins($project)
    {
        $result = F3::get('DB')->sql('SELECT userID FROM ProjectAdmins WHERE projectID = :project', array(':project' => $project));
        
        foreach($result as $row)
            $ret[] = $row['userID'];

        return $ret;
    }
}
