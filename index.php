<?php

$app=require(__DIR__.'/lib/base.php');

require 'inc/config.inc.php';
require 'inc/main.php';

$app->set('CACHE', true);
$app->set('DEBUG', 2);
$app->set('EXTEND', true);
$app->set('GUI','gui/');
$app->set('AUTOLOAD', 'inc/');
$app->set('LOCALES','lang/');
$app->set('LANGUAGE', 'de'); // substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
$app->set('DB', array('dsn'=>'sqlite:' .$dbFile));

require 'inc/mapping.inc.php';


$app->route('GET /', 'main->start');
$app->route('GET /roadmap', 'main->showRoadmap');
$app->route('GET /timeline', 'main->showTimeline');
$app->route('GET /tickets', 'main->showTickets');
$app->route('GET /tickets/@order', 'main->showTickets');
$app->route('GET /ticket/@hash', 'main->showTicket');
$app->route('GET /user/@hash', 'main->showUser');
$app->route('GET /user/new', 'main->showUserRegister');
$app->route('GET /user/login', 'main->showUserLogin');

$app->route('POST /user/login', 'main->loginUser');
$app->route('POST /user/new', 'main->registerUser');
$app->route('POST /ticket', 'main->addTicket');
$app->route('POST /milestone', 'main->addMilestone');
$app->route('POST /project/select', 'main->selectProject');

$app->run();
