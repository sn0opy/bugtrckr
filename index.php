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

$app = require(__DIR__.'/lib/base.php');

if(!file_exists('data/config.inc.php')) {
    echo '<a href="/' . F3::get('BASE') . 'setup.php">Setup</a>';
    exit;
}

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
    $helper = new \misc\helper();
    return $helper->getPermission($permission);
});

$app->route('GET /', 'misc\main->start');
$app->route('GET /roadmap', '\milestone\view->showRoadmap');
$app->route('GET /timeline', '\timeline\view->showTimeline');
$app->route('GET /tickets', '\ticket\view->showTickets');
$app->route('GET /ticket/@hash', '\ticket\view->showTicket');
$app->route('GET /user/@name', '\user\view->showUser');
$app->route('GET /user/new', '\user\view->showUserRegister');
$app->route('GET /user/login', '\user\view->showUserLogin');
$app->route('GET /milestone/@hash', '\milestone\view->showMilestone');
$app->route('GET /project/add', 'project\view->showAddProject');
$app->route('GET /project/settings', 'project\view->showProjectSettings');
$app->route('GET /project/settings/role/@hash', 'project\view->showProjectSettingsRole');
$app->route('GET /project/settings/role/add', 'project\view->showAddRole');
$app->route('GET /project/settings/milestone/@hash', 'project\view->showProjectSettingsMilestone');
$app->route('GET /project/settings/milestone/add', 'project\view->showAddMilestone');
$app->route('GET /project/settings/category/add', 'project\view->showAddCategory');
$app->route('GET /project/settings/category/edit/@hash', 'project\view->showEditCategory');
$app->route('GET /project/settings/role/delete/@hash', 'project\view->deleteRole');
$app->route('GET /project/settings/milestone/delete/@hash', 'project\view->deleteProjectSettingsMilestone');
$app->route('GET /project/settings/category/delete/@hash', 'project\controller->deleteCategory');
$app->route('GET /wiki/@title', '\wiki\view->showEntry');
$app->route('GET /wiki', '\wiki\view->showEntry');
$app->route('GET /wikidiscussion/@hash', '\wiki\view->showDiscussion');

$app->route('GET /user/logout', '\user\controller->logoutUser');
$app->route('POST /search', '\ticket\view->showTickets');
$app->route('POST /project/select', 'misc\main->selectProject');
$app->route('POST /user/login', '\user\controller->loginUser');
$app->route('POST /user/new', '\user\controller->registerUser');
$app->route('POST /ticket', '\ticket\controller->addTicket');
$app->route('POST /ticket/@hash', '\ticket\controller->editTicket');
$app->route('POST /project/add', '\project\controller->projectAdd');
$app->route('POST /project/settings/member/setrole', '\project\controller->projectSetRole');
$app->route('POST /project/settings/category/add', '\project\controller->addEditCategory');
$app->route('POST /project/settings/category/edit', '\project\controller->addEditCategory');
$app->route('POST /project/settings/role/edit', '\project\controller->addEditRole');
$app->route('POST /project/settings/main/edit', '\project\controller->projectEditMain');
$app->route('POST /project/settings/milestone/edit', '\milestone\controller->addEditMilestone');
$app->route('POST /project/settings/member/add', '\project\controller->projectAddMember');
$app->route('POST /project/setttings/member/delete', '\project\controller->projectDelMember');
$app->route('POST /wiki', '\wiki\controller->editEntry');
$app->route('POST /wikidiscussion', '\wiki\controller->addDiscussion');

$app->run();

$app->clear('SESSION.SUCCESS');
$app->clear('SESSION.FAILURE');
