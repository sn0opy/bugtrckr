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
namespace misc;

class helper extends \F3instance
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
        $ax = new \Axon($table);
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
    public static function getPermission($permission)
    {
        $userHash = F3::get('SESSION.user.hash');
        $projectHash = F3::get('SESSION.project');
        
        if ($userHash)
        {
            $user = new User();
            $user->load(array('hash = :hash', array(':hash' => $userHash)));

            if($user->admin) // admin has access to everything
                return true;
            
            $projPerm = new ProjectPermission();
            $permissions = $projPerm->findone(array('user = :user AND project = :project', array(':user' => $userHash, ':project' => $projectHash)));
            
			if($permissions == null)
				return false;

            $role = new Role();
            $role->load(array('hash = :hash', array(':hash' => $permissions->role)));
            
            if($role->dry())
				return false;

            if($role->$permission == true)
                return true;
        }

        return false;
    }

    public static function getTicketCount($milestone)
    {
        return \F3::get('DB')->sql('SELECT state, COUNT(*) AS `count` FROM `Ticket` WHERE milestone = \'' . $milestone . '\' GROUP BY state');
    }

    public static function addActivity($description, $ticket = 0, $comment = '')
    {
        $activity = new Activity();
        
        $activity->hash = \misc\helper::getFreeHash('Activity');
        $activity->description = $description;
        $activity->comment = $comment;
        $activity->user = F3::get('SESSION.user.id');
        $activity->changed = time();
        $activity->project = F3::get('SESSION.project');
        $activity->ticket = $ticket;

        $activity->save();
    }
}
