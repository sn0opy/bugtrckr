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

        F3::set('title', $title . ' - ' . \F3::get('title'));
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
        $userHash = \F3::get('SESSION.user.hash');
        $projectHash = \F3::get('SESSION.project');
        
        if ($userHash)
        {
            $user = new \user\model();
            $user->load(array('hash = :hash', array(':hash' => $userHash)));

            if($user->admin) // admin has access to everything
                return true;
            
            $projPerm = new \userPerms\model();
            $permissions = $projPerm->findone(array('user = :user AND project = :project', array(':user' => $userHash, ':project' => $projectHash)));
            
			if($permissions == null)
				return false;

            $role = new \role\model();
            $role->load(array('hash = :hash', array(':hash' => $permissions->role)));
            
            if($role->dry())
				return false;

            if($role->$permission == true)
                return true;
        }

        return false;
    }

	/**
	 *	
	 */
	public static function canRead($hash)
	{
		$project = new \project\model;
		$project->load(array('hash = :hash', array(':hash' => $hash)));

		if ($project->public)
			return true;

		$perm = new \projPerms\model;
		return $perm->found(array('user = :user AND project = :project', array(':user' => \F3::get('SESSION.user.hash'), ':project' => \F3::get('SESSION.project'))));
	}

	/**
	 *
	 */
    public static function getTicketCount($milestone)
    {
        return \F3::get('DB')->sql('SELECT state, COUNT(*) AS `count` FROM `Ticket` WHERE milestone = \'' . $milestone . '\' GROUP BY state');
    }

    public static function addActivity($description, $ticket = 0, $comment = '', $fields = '', $projHash = false)
    {
        $activity = new \activity\model();
        
        $activity->hash = \misc\helper::getFreeHash('Activity');
        $activity->description = $description;
        $activity->comment = $comment;
        $activity->user = \F3::get('SESSION.user.hash');
        $activity->changed = time();
        $activity->project = ($projHash) ? $projHash : \F3::get('SESSION.project');
        $activity->ticket = $ticket;
        $activity->changedFields = $fields;

        $activity->save();
    }

	public static function getUsername($hash)
	{
		$user = new \user\model;
		$user->load(array('hash = :hash', array(':hash' => $hash)));

        if($user->dry())
            return \F3::get('lng.nobody');
            
		return $user->name;
	}

	public static function getName($type, $id)
	{
        $arr = \F3::get('lng.' . $type);
        
        foreach($arr as $elem)
            if($elem['id'] == $id)
                return $elem['name'];
        
        return '';
	}
    
    public static function getMsName($hash) {
        $ms = new \milestone\model();
        $ms->load(array('hash = :hash', array(':hash' => $hash)));
        return $ms->name;
    }

	public static function translateBBCode($string)
    {
        $string = preg_replace('/===(.+)===/', '<h3>${1}</h3>', $string);
        $string = preg_replace('/==(.+)==/', '<h2>${1}</h2>', $string);
        $string = preg_replace('/\'\'\'(.+)\'\'\'/', '<b>${1}</b>', $string);			
		$string = preg_replace('/\'\'(.+)\'\'/', '<i>${1}</i>', $string);
        $string = preg_replace('/----/', '<hr />', $string);
		$string = preg_replace('/\[\[(.+) (.+)\]\]/', '<a href="${1}">${2}</a>', $string);
        $string = preg_replace('/\[\[(.+)\]\]/', '<a href="' . \F3::get('BASE') . '/wiki/${1}">${1}</a>', $string);
        $string = preg_replace('/\~\~(.+)\~\~/', '<pre>${1}</pre>', $string);
        $string = preg_replace('/\n/', '<br />', $string);

        return $string;
    }
}
