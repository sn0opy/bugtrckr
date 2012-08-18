<?php

/**
 * Setup
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

namespace misc;

class Setup extends \F3instance 
{
    public function start() 
	{
        $this->set('NEEDED', array(
            'sqlite' => extension_loaded('pdo_sqlite'),
            'mysql' => extension_loaded('pdo_mysql'),
            'writepermission' => is_writable('../data/'),
            'configexists' => file_exists('../data/config.inc.php')
            ));                
        
        $this->set('BERROR', $this->doChecks());        
        $this->tpserve();
    }    
    
    public function install() 
	{
        $admname = $this->get('POST.name');
        $admpw = $this->get('POST.pw');
        $admemail = $this->get('POST.email');
        
        if($this->get('POST.dbtype') == 'mysqldb') 
		{
            $host = $this->get('POST.sqlhost');
            $user = $this->get('POST.sqluser');
            $pass = $this->get('POST.sqlpw');
            $db = $this->get('POST.sqldb');

            $this->set('DB', new \DB('mysql:host=' .$host. ';dbname=' .$db, $user, $pass));
            $this->get('DB')->sql('SET NAMES utf8'); // just to check whether connection works
            
            require_once '../install/mysql.php';
            
            $usr = new \controllers\User();
            $usr->registerUser($admname, $admpw, $admemail, true);
            
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
            if(!$user->registerUser($admname, $admpw, $admemail, true)) 
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
    
    private function doChecks() {
        $error = false;
        
        if($this->get('NEEDED.sqlite') != true && $this->get('NEEDED.mysql') != true)
            $error = true;
        
        if($this->get('NEEDED.writepermission') != true)
            $error = true;
        
        if($this->get('NEEDED.configexists') == true)
            $error = true;
        
        return $error;
    }
    
    private function tpserve() {
        echo \Template::serve('setup.tpl.php');
    }
}
