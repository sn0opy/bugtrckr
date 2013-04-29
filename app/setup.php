<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Setup {
    /**
     * 
     * @param type $f3
     */
    public function start() {
		$f3 = Base::instance();
        
		$f3->set('reqs', $this->_doChecks());        
        $this->tpserve();
    }    
    
    /**
     * 
     * @param type $f3
     * @return type
     */
	public function install() {
		$f3 = Base::instance();

        $admname = $f3->get('POST.name');
        $admpw = $f3->get('POST.pw');
        $admemail = $f3->get('POST.email');
        
        if($f3->get('POST.dbtype') == 'mysqldb') {
            $host = $f3->get('POST.sqlhost');
            $user = $f3->get('POST.sqluser');
            $pass = $f3->get('POST.sqlpw');
            $db = $f3->get('POST.sqldb');

            $f3->set('DB', new \DB\SQL('mysql:host=' .$host. ';dbname=' .$db, $user, $pass));
            
            require_once '../install/mysql.php';
            
            $usr = new User;
            $usr->registerUser(false, false, $admname, $admpw, $admemail, true);
            
            file_put_contents('../data/config.inc.php', "<?php F3::set('DB', new \DB('mysql:host=".$host.";dbname=".$db."', '".$user."', '".$pass."')); ?>");
            
            $f3->set('INSTALLED', true);            
            self::tpserve();            
        } else {
            $db = $f3->get('POST.dbname');
            
            if(file_exists('../data/'.$db)) {
                $f3->set('dbexists', true);
                $this->tpserve();
                return;
            }

            $f3->set('DB', new \DB\SQL('sqlite:../data/'.$db));
            require_once '../install/sqlite.php';
            
            $user = new User;
            if(!$user->registerUser(false, false, $admname, $admpw, $admemail, true)) {
                $f3->set('usererror', true);
                self::tpserve();
                return;
            }
            
            // TODO: file has changed
            file_put_contents('../data/config.inc.php', "<?php F3::set('DB', new \DB('sqlite:../data/".$db."')); ?>");
            
            $f3->set('INSTALLED', true);            
            self::tpserve();
        }
    }           
    

    /**
     * 
     * @param type $f3
     * @return boolean
     */
    private static function _doChecks() {
		$f3 = Base::instance();	
		$error = false;
		$check = null;
		
		$check['sqlite'] = !extension_loaded('pdo_sqlite') ? false : true;
		$check['mysql'] = !extension_loaded('pdo_mysql') ? false: true;

		if(!$check['sqlite'] && !$check['mysql'])
			$error = true;

		if(is_writeable('../app/')) {
			$check['config1'] = true;
		} else {
			$check['config1'] = false;
			$error = true;
		}

		if(!file_exists('../app/config.ini.php')) {
			$check['config2'] = true;
		} else {
			$error = true;
			$check['config2'] = false;
		}

        return array('error' => $error, 'checks' => $check);
    }
	
	
    /**
     * 
     */
    private static function tpserve() {
        echo Template::instance()->render('setup.tpl.php');
    }
}
