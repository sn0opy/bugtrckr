<?php

/**
 * Setup
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2013, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

class Setup {
    public function start() 
	{
		$f3 = Base::instance();
		
        $f3->set('NEEDED', array(
            'sqlite' => extension_loaded('pdo_sqlite'),
            'mysql' => extension_loaded('pdo_mysql'),
            'writepermission' => is_writable('../data/'),
            'configexists' => file_exists('../data/config.inc.php')
            ));                
        
        $f3->set('BERROR', $this->doChecks());        
        $this->tpserve();
    }    
    
    public function install() 
	{
		$f3 = Base::instance();
		
        $admname = $f3->get('POST.name');
        $admpw = $f3->get('POST.pw');
        $admemail = $f3->get('POST.email');
        
        if($f3->get('POST.dbtype') == 'mysqldb') 
		{
            $host = $f3->get('POST.sqlhost');
            $user = $f3->get('POST.sqluser');
            $pass = $f3->get('POST.sqlpw');
            $db = $f3->get('POST.sqldb');

            $f3->set('DB', new \DB\SQL('mysql:host=' .$host. ';dbname=' .$db, $user, $pass));
            
            require_once '../install/mysql.php';
            
            $usr = new \controllers\User();
            $usr->registerUser(false, false, $admname, $admpw, $admemail, true);
            
            file_put_contents('../data/config.inc.php', "<?php F3::set('DB', new \DB('mysql:host=".$host.";dbname=".$db."', '".$user."', '".$pass."')); ?>");
            
            $this->set('INSTALLED', true);            
            $this->tpserve();
            
        } 
		else 
		{
            $db = $this->get('POST.dbname');
            
            if(file_exists('../data/'.$db)) 
			{
                $this->set('dbexists', true);
                $this->tpserve();
                return;
            }

            $this->set('DB', new \DB('sqlite:../data/'.$db));
            require_once '../install/sqlite.php';
            
            $user = new \controllers\User();
            if(!$user->registerUser(false, false, $admname, $admpw, $admemail, true)) 
			{
                $this->set('usererror', true);
                $this->tpserve();
                return;
            }
            
            
            file_put_contents('../data/config.inc.php', "<?php F3::set('DB', new \DB('sqlite:../data/".$db."')); ?>");
            
            $this->set('INSTALLED', true);            
            $this->tpserve();
        }
    }           
    
	/**
	 * 
	 */
    private function doChecks() {
		$f3 = Base::instance();
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
    private function tpserve() {
        echo Template::instance()->serve('setup.tpl.php');
    }
}
