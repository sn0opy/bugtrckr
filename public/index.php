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
if(file_exists('../setup.cfg'))
    include '../data/config.inc.php';
elseif($f3->exists('POST.email'))
    $f3->mock('POST /setup');
else
    $f3->mock('GET /setup');

$f3->setup('../setup.cfg');
$f3->setup('../routes.cfg');

$f3->set('getPermission', function($permission) {
    $helper = new \misc\helper();
    return $helper->getPermission($permission);
});

$f3->run();

$f3->clear('SESSION.SUCCESS');
$f3->clear('SESSION.FAILURE');

$end = microtime();

echo "Render time: ".($end - $start);
