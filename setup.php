<?php

$phpver = explode('.', phpversion());

if($phpver[0] < 5 && $phpver[1] < 3)
    die('Minimum PHP Version: 5.3');

$app = require_once(__DIR__.'/lib/base.php');
$app->set('GUI','install/');
$app->set('DEBUG', 3);

$app->route('GET /setup.php', 'main->start');
$app->route('POST /setup.php', 'main->install');

class main extends F3instance {
    function start() {
        $this->set('NEEDED', array(
            'sqlite' => extension_loaded('pdo_sqlite'),
            'writepermission' => is_writable('inc/'),
            'configexists' => file_exists('inc/config.inc.php')
            ));                
        
        $this->set('ERROR', $this->doChecks());        
        $this->tpserve();
    }
    
    function install() {
        $admname = $this->get('POST.name');
        $admpw = $this->get('POST.pw');
        $admpwre = $this->get();
        
        if($this->get('POST.dbtype') == 'mysqldb') {
            $host = $this->get('POST.sqlhost');
            $user = $this->get('POST.sqluser');
            $pass = $this->get('POST.sqlpw');
            $db = $this->get('POST.sqldb');
                    
            $this->set('DB', new DB('mysql:host=' .$host. ';dbname=' .$db, $user, $pass));
            require_once 'install/mysql.php';
        } else {
            $db = $this->get('POST.dbname');
            
            $this->set('DB', new DB('sqlite:'.$db));
            require_once 'install/sqlite.php';
        }
    }
    
    function doChecks() {
        $error = false;
        
        if($this->get('NEEDED.sqlite') != true)
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
