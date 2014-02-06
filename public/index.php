<?php

/**
 * Routes, settings anonymous functions
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2012, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
**/

$start = microtime();

$f3 = require __DIR__ . '/../lib/base.php';

// really dirty, but works great
if(!file_exists('../setup.cfg'))
  exit("Create a setup.cfg first.");

$f3->config('../setup.cfg');
$f3->config('../routes.cfg');

$f3->set('getPermission', function($permission) {
    $helper = new \misc\helper();
    return $helper->getPermission($permission);
});

$f3->run();

$f3->clear('SESSION.SUCCESS');
$f3->clear('SESSION.FAILURE');

$end = microtime();

echo "Render time: ".($end - $start);
