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

$app->set('getPermission', function($permission) {
    $helper = new Helper;
    return $helper->getPermission($permission);
});

$app->run();

$app->clear('SESSION.SUCCESS');
$app->clear('SESSION.FAILURE');
