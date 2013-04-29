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
	
        $f3->set('NEEDED', array(
            'sqlite' => extension_loaded('pdo_sqlite'),
            'mysql' => extension_loaded('pdo_mysql'),
            'writepermission' => is_writable('../data/'),
            'configexists' => file_exists('../data/config.inc.php')
            ));                
        
        $f3->set('BERROR', $this->_doChecks());        
        $this->tpserve();
    }    
    
    /**
     * 
     * @param type $f3
     * @return type
     */
    public function install($f3) {
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
        $error = false;
        
        if($f3->get('NEEDED.sqlite') != true && $f3->get('NEEDED.mysql') != true)
            $error = true;
        
        if($f3->get('NEEDED.writepermission') != true)
            $error = true;
        
        if($f3->get('NEEDED.configexists') == true)
            $error = true;
        
        return $error;
    }
	
	
    /**
     * 
     */
    private static function tpserve() {
        echo Template::instance()->serve('setup.tpl.php');
    }
}
