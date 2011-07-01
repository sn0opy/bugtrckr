<?php

/**
 * helper.php
 * 
 * helper functions
 * 
 * @package Helper
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
class helper extends F3instance
{

    public static function randStr($length = 5)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    public static function salting($salt, $pass)
    {
        $salt = md5($salt);
        $pw = md5($pass);
        return sha1(md5($salt . $pw) . $salt);
    }

    public static function getFreeHash($table, $length = 12)
    {
        $ax = new Axon($table);
        do
        {
            $hash = self::randStr($length);
            $ax->find('hash = "' . $hash . '"');
        } while (!$ax->dry());
        return $hash;
    }

    public static function setTitle($subTitles)
    {
        $title = '';
        $subTitles = (array) $subTitles;

        foreach ($subTitles as $sub)
        {
            $seperator = !empty($title) ? ' › ' : '';
            $title .= $seperator . $sub;
        }

        F3::set('title', $title . ' - ' . F3::get('title'));
    }

    /**
     * Checks whether the user has access to $permission
     * 
     * @param string $permission Permission is a predefined string stored in the db
     * @return bool
     * @static
     */
    public function getPermission($permission)
    {
        $userId = $this->get('SESSION.user->id');
        $projectId = $this->get('SESSION.project');
        
        if ($userId)
        {
            $user = new User();
            $user->load('id = ' . $userId);

            $projPerm = new ProjectPermission();
            $permissions = $projPerm->find('userId = ' . $userId . ' AND projectId = ' . $projectId);

            $permissions = $permissions[0]; // TODO: find a better way ...
          
            $role = new Role();
            $role->load('id = ' . $permissions->roleId);

            if ($user->admin) // admin has access to everything
                return true;

            if (in_array($permission, $permissions))
                if ($permissions[$permission] == true)
                    return true;
        }

        return false;
    }

    public function getTicketCount($milestone)
    {
        return $this->get('DB')->sql('SELECT state, COUNT(*) AS `count` FROM `Ticket` WHERE milestone = ' . $milestone . ' GROUP BY state');
    }

    public static function addActivity($description, $ticket = 0)
    {
        $activity = new Activity();
        
        $activity->hash = helper::getFreeHash('Activity');
        $activity->description = $description;
        $activity->user = $_SESSION['user']['id'];
        $activity->changed = time();
        $activity->project = $_SESSION['project'];
        $activity->ticket = $ticket;
        
        $activity->save();
    }

}

