<?php

/**
 * Routes, settings anonymous functions
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2013, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
**/

$app = include('../lib/base.php');

$app->config('../app/config.ini');
$app->config('../app/sql.ini');
$app->config('../app/routes.ini');

$app->set('DB', new DB\SQL('mysql:host=localhost;dbname=' . 
		$app->get('DB_DBNAME'), 
		$app->get('DB_USER'), 
		$app->get('DB_PASSWORD')));

$app->set('getPermission', function($permission) {
    $helper = new Helper;
    return $helper->getPermission($permission);
});

$app->run();

$app->clear('SESSION.SUCCESS');
$app->clear('SESSION.FAILURE');
