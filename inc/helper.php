<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Helper {
	/**
	 * 
	 * @param type $length
	 * @return type
	 */
	public static function randStr($len = 10) { 
		$pw = '';
		for($i = 0; $i < $len; $i++) {
			switch(mt_rand(1, 7)) { 
				case 1: $pw .= chr(rand(48, 57)); break;
				case 2: $pw .= chr(rand(65, 72)); break;
				case 3: $pw .= chr(rand(73, 81)); break;
				case 4: $pw .= chr(rand(82, 90)); break; 
				case 5: $pw .= chr(rand(97, 104)); break; 
				case 6: $pw .= chr(rand(105, 113)); break;
				case 7: $pw .= chr(rand(114, 122)); break;
			} 
		} 
		return $pw; 
	}
	
	public static function dbconnection() {
		$f3 = Base::instance();

		if($f3->exists('DB')) {
			return;
		} elseif($f3->exists('DB_USER')) {
			$f3->set('DB', new DB\SQL('mysql:host=localhost;dbname=' . 
						$f3->get('DB_DBNAME'), 
						$f3->get('DB_USER'), 
						$f3->get('DB_PASSWORD')));
		} else {
			$f3->set('DB', new DB\SQL('sqlite:'.$f3->get('DB_DBNAME')));
		}
	}

	
	/**
	 * 
	 * @param type $table
	 * @param type $length
	 * @return type
	 */
    public static function getFreeHash($table, $length = 12) {
		$db = Base::instance()->get('DB');
		
        $ax = new DB\SQL\Mapper($db, $table);
        do {
            $hash = self::randStr($length);
            $ax->find('hash = "' . $hash . '"');
        } while (!$ax->dry());
        return $hash;
    }

	
	/**
	 * 
	 * @param type $subTitles
	 */
    public static function setTitle($subTitles) {
		$f3 = Base::instance();
		
        $title = '';
        $subTitles = (array) $subTitles;

        foreach ($subTitles as $sub) {
            $seperator = !empty($title) ? ' › ' : '';
            $title .= $seperator . $sub;
        }

        $f3->set('title', $title . ' - ' . $f3->get('title'));
    }

	
	/**
	 * 
	 * @param type $permission
	 * @return boolean
	 */
    public static function getPermission($permission) {
		$f3 = Base::instance();
		$db = $f3->get('DB');
		
        $userHash = $f3->get('SESSION.user.hash');
        $projectHash = $f3->get('SESSION.project');
        
        if ($userHash) {
            $user = new DB\SQL\Mapper($db, 'User');
            $user->load(array('hash = :hash', array(':hash' => $userHash)));

            if($user->admin) // admin has access to everything
                return true;
            
            $projPerm = new DB\SQL\Mapper($db, 'ProjectPermission');
            $permissions = $projPerm->findone(array('user = :user AND project = :project', array(':user' => $userHash, ':project' => $projectHash)));
            
			if($permissions == null)
				return false;

            $role = new DB\SQL\Mapper($db, 'Role');
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
	 * @param type $hash
	 * @return boolean
	 */
	public static function canRead($hash){
		$f3 = Base::instance();
		$db = $f3->get('DB');
	
		$project = new DB\SQL\Mapper($db, 'project');
		$project->load(array('hash = :hash', array(':hash' => $hash)));

		if ($project->public)
			return true;

		$perm = new DB\SQL\Mapper(self::$db, 'ProjectPermission');
		return $perm->found(array('user = :user AND project = :project', array(':user' => $f3->get('SESSION.user.hash'), ':project' => $f3->get('SESSION.project'))));
	}

	
	/**
	 * 
	 * @param type $milestone
	 * @return type
	 */
    public static function getTicketCount($milestone) {
		// TODO: dirty? dirty!
        return Base::instance()->get('DB')->exec('SELECT state, COUNT(*) AS `count` FROM `Ticket` WHERE milestone = \'' . $milestone . '\' GROUP BY state');
    }

	
	/**
	 * 
	 * @param type $description
	 * @param type $ticket
	 * @param type $comment
	 * @param type $fields
	 * @param type $projHash
	 */
    public static function addActivity($description, $ticket = 0, $comment = '', $fields = '', $projHash = false) {
		$f3 = Base::instance();
		$db = $f3->get('DB');
        $activity = new DB\SQL\Mapper($db, 'Activity');
        
        $activity->hash = self::getFreeHash('Activity');
        $activity->description = $description;
        $activity->comment = $comment;
        $activity->user = $f3->get('SESSION.user.hash');
        $activity->changed = time();
        $activity->project = ($projHash) ? $projHash : $f3->get('SESSION.project');
        $activity->ticket = $ticket;
        $activity->changedFields = $fields;
        $activity->save();
    }

	
	/**
	 * 
	 * @param type $hash
	 * @return type
	 */
	public static function getUsername($hash) {
		$f3 = Base::instance();
		$db = $f3->get('DB');
		
		$user = new DB\SQL\Mapper($db, 'User');
		$user->load(array('hash = :hash', array(':hash' => $hash)));

        if($user->dry())
            return $f3->get('lng.nobody');
            
		return $user->name;
	}

	
	/**
	 * 
	 * @param type $type
	 * @param type $id
	 * @return string
	 */
	public static function getName($type, $id) {
		$arr = Base::instance()->get('lng.' . $type);
        
        foreach($arr as $elem)
            if($elem['id'] == $id)
                return $elem['name'];
        
        return '';
	}
    
	
	/**
	 * 
	 * @param type $hash
	 * @return type
	 */
    public static function getMsName($hash) {
		$f3 = Base::instance();
		$db = $f3->get('DB');
		
        $ms = new DB\SQL\Mapper($db, 'Model');
        $ms->load(array('hash = :hash', array(':hash' => $hash)));
        return $ms->name;
    }
	

	/**
	 * 
	 * @param type $string
	 * @return type
	 */
	public static function translateBBCode($string) {
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
