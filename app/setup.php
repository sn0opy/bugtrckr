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
     * @return type
     */
    public function install() {
		$f3 = Base::instance();
		$err = '';
		$success = false;
		
		foreach($f3->get('POST') as $elem => $val)
			$f3->set('SESSION.'.$elem, $val);
		
		if($f3->get('POST.username') && $f3->get('POST.email') && $f3->get('POST.password') && $f3->get('POST.passwordrepeat')) {
			if($f3->get('POST.password') == $f3->get('POST.passwordrepeat')) {
				if(!$f3->get('POST.sqlusername') && !$f3->get('POST.sqlpassword') && !$f3->get('POST.sqlserver') && !$f3->get('POST.sqldb')) {
					// SQLite installation					
					$rnd = Helper::randStr();
					$db = new DB\SQL('sqlite:../app/' . $rnd . '.db');
					$db->exec(explode(';', $f3->read('../app/setup/sqlite.sql')));
					
					$f3->write('../app/sql.ini', "[global]\nDB_DBNAME=../app/".$rnd.'.db');
					
					// create new user
					$u = new User;
					$u->registerUser(false, false, $f3->get('POST.username'), $f3->get('POST.password'), $f3->get('POST.email'), true);
					
					$f3->set('success', 'Installation complete');
					
					
				} elseif(!$f3->get('POST.sqlusername') || !$f3->get('POST.sqlpassword') || !$f3->get('POST.sqlserver') || !$f3->get('POST.sqldb')) {
					$err['sqlmissingfields'] = true;
				} else {
					try {
						$db = new DB\SQL('mysql:host=' . $f3->get('POST.sqlserver') . ';dbname=' . $f3->get('POST.sqldb'), $f3->get('POST.sqlusername'), $f3->get('POST.sqlpassword'), array(\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION));
					} catch(PDOException $e) {
						$err['mysqlconnfail'] = true;
					}
					
					if(!isset($err['mysqlconnfail'])) {
						// MySQL installation
					}
				}
			} else {
				$err['pwmatch'] = true;
			}
		} else {
			$err['missingfields'] = true;
		}
		
		$f3->set('err', $err);
		
		if($err != '') 
			$f3->reroute('/setup');
		else
			$f3->reroute('/');
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

		if(!file_exists('../app/sql.ini')) {
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
