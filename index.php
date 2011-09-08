<?php

/**
 * index.php
 * 
 * Routes, settings anonymous functions
 * 
 * @package Index
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
**/

if(!file_exists('data/config.inc.php')) {
    echo '<a href="setup.php">Setup</a>';
    exit;
}

$app = require(__DIR__.'/lib/base.php');

$app->set('RELEASE', false);
$app->set('CACHE', false);
$app->set('DEBUG', 3);
$app->set('EXTEND', true);
$app->set('GUI','gui/');
$app->set('AUTOLOAD', 'inc/|inc/models/');
$app->set('LOCALES','lang/');
$app->set('PROXY', true);
$app->set('LANGUAGE', 'de'); // until we have a better idea for localizing the db stuff

require_once('data/config.inc.php');

// Template functions
$app->set('getPermission', function($permission) {
    $helper = new helper();
    return $helper->getPermission($permission);
});

$app->route('GET /', 'cmain->start');
$app->route('GET /roadmap', 'cmilestone->showRoadmap');
$app->route('GET /timeline', 'ctimeline->showTimeline');
$app->route('GET /tickets', 'cticket->showTickets');
$app->route('GET /ticket/@hash', 'cticket->showTicket');
$app->route('GET /user/@name', 'cuser->showUser');
$app->route('GET /user/new', 'cuser->showUserRegister');
$app->route('GET /user/login', 'cuser->showUserLogin');
$app->route('GET /user/logout', 'cuser->logoutUser');
$app->route('GET /milestone/@hash', 'cmilestone->showMilestone');
$app->route('GET /project/add', 'cproperties->showAddProject');
$app->route('GET /project/settings', 'cproperties->showProjectSettings');
$app->route('GET /project/settings/role/@hash', 'cproperties->showProjectSettingsRole');
$app->route('GET /project/settings/role/add', 'cproperties->showAddRole');
$app->route('GET /project/settings/milestone/@hash', 'cproperties->showProjectSettingsMilestone');
$app->route('GET /project/settings/milestone/add', 'cproperties->showAddMilestone');
$app->route('GET /project/settings/category/add', 'cproperties->showAddCategory');
$app->route('GET /project/settings/role/delete/@hash', 'cproperties->deleteRole');
$app->route('GET /project/settings/milestone/delete/@hash', 'cproperties->deleteProjectSettingsMilestone');
$app->route('GET /project/settings/category/delete/@hash', 'cproperties->deleteCategory');
$app->route('GET /wiki/@title', 'cwiki->showEntry');
$app->route('GET /wiki', 'cwiki->showEntry');


$app->route('POST /search', 'cticket->showTickets');
$app->route('POST /project/select', 'cmain->selectProject');
$app->route('POST /user/login', 'cuser->loginUser');
$app->route('POST /user/new', 'cuser->registerUser');
$app->route('POST /ticket', 'cticket->addTicket');
$app->route('POST /ticket/@hash', 'cticket->editTicket');
$app->route('POST /project/add', 'cproperties->projectAdd');
$app->route('POST /project/settings/member/setrole', 'cproperties->projectSetRole');
$app->route('POST /project/settings/category/add', 'cproperties->addEditCategory');
$app->route('POST /project/settings/category/edit', 'cproperties->addEditCategory');
$app->route('POST /project/settings/role/edit', 'cproperties->addEditRole');
$app->route('POST /project/settings/main/edit', 'cproperties->projectEditMain');
$app->route('POST /project/settings/milestone/edit', 'cmilestone->addEditMilestone');
$app->route('POST /project/settings/member/add', 'cproperties->projectAddMember');
$app->route('POST /project/setttings/member/delete', 'cproperties->projectDelMember');
$app->route('POST /wiki', 'cwiki->editEntry');

$app->run();

$app->clear('SESSION.SUCCESS');
$app->clear('SESSION.FAILURE');
