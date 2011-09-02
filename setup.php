<?php

/**
 * setup.php
 * 
 * setup file
 * 
 * @package setup
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

$phpver = explode('.', phpversion());
if($phpver[0] < 5 && $phpver[1] < 3)
    die('Minimum PHP Version: 5.3');

$app = require_once(__DIR__.'/lib/base.php');
$app->set('AUTOLOAD', __DIR__.'/inc/|'.__DIR__.'/inc/models/');
$app->set('GUI','install/');
$app->set('DEBUG', 3);
$app->set('LOCALES','install/lang/');
$app->set('LANGUAGE', 'de');
$app->route('GET /setup.php', 'main->start');
$app->route('POST /setup.php', 'main->install');

class main extends F3instance {
    function start() {
        $this->set('NEEDED', array(
            'sqlite' => extension_loaded('pdo_sqlite'),
            'mysql' => extension_loaded('pdo_mysql'),
            'writepermission' => is_writable('data/'),
            'configexists' => file_exists('data/config.inc.php')
            ));                
        
        $this->set('BERROR', $this->doChecks());        
        $this->tpserve();
    }
    
    
    function install() {
        $admname = $this->get('POST.name');
        $admpw = $this->get('POST.pw');
        $admemail = $this->get('POST.email');
        
        if($this->get('POST.dbtype') == 'mysqldb') {
            $host = $this->get('POST.sqlhost');
            $user = $this->get('POST.sqluser');
            $pass = $this->get('POST.sqlpw');
            $db = $this->get('POST.sqldb');

            function errhandler() {
                F3::set('mysqldata', array('host' => F3::get('POST.sqlhost'), 'user' => F3::get('POST.sqluser'), 'db' => F3::get('POST.sqldb')));
                $main = new main();
                $main->start();
                return;
            }
            
            $this->set('ONERROR', 'errhandler');
            $this->set('DB', new DB('mysql:host=' .$host. ';dbname=' .$db, $user, $pass));
            $this->get('DB')->sql('SET NAMES utf8'); // just to check whether connection works
            
            require_once 'install/mysql.php';
            
        } else {
            $db = $this->get('POST.dbname');
            
            if(file_exists('data/'.$db)) {
                $this->set('dbexists', true);
                $this->tpserve();
                return;
            }
            
            $this->set('DB', new DB('sqlite:data/'.$db));
            require_once 'install/sqlite.php';
            
            $user = new cuser;
            $user->registerUser($admname, $admpw, $admemail, true);
            
            file_put_contents('data/config.inc.php', '<?php $dbFile = "'.$db.'"; ?>');
            
            $this->set('INSTALLED', true);            
            $this->tpserve();
        }
    }           
    
    function doChecks() {
        $error = false;
        
        if($this->get('NEEDED.sqlite') != true && $this->get('NEEDED.mysql') != true)
            $error = true;
        
        if($this->get('NEEDED.writepermission') != true)
            $error = true;
        
        if($this->get('NEEDED.configexists') == true)
            $error = true;
        
        return $error;
    }
    
    function tpserve() {
        echo Template::serve('setup.tpl.php');
    }
}

$app->run();

?>
