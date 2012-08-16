<?php

/**
 * index.php
 * 
 * Routes, settings anonymous functions
 * 
 * @package Index
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2012, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
**/

$app = require '../lib/base.php';

if(!file_exists('../data/config.inc.php')) {
    echo '<a href="' . $app->get('BASE') . '/setup">Setup</a>';
    exit;
}

$app->set('CACHE', false);
$app->set('DEBUG', 3);
$app->set('GUI','../gui/'); 
$app->set('AUTOLOAD', '../inc/|../inc/models/');
$app->set('TEMP', '../temp/');
$app->set('LOCALES','../lang/');
$app->set('LANGUAGE', 'de'); // currently forcing to German

include '../data/config.inc.php';

$app->set('getPermission', function($permission) {
    $helper = new \misc\helper();
    return $helper->getPermission($permission);
});


$app->route('GET /', 'misc\main->start');
$app->route('GET /roadmap', '\controllers\milestone->showRoadmap');
$app->route('GET /timeline', '\controllers\timeline->showTimeline');
$app->route('GET /tickets', '\controllers\ticket->showTickets');
$app->route('GET /ticket/@hash', '\controllers\ticket->showTicket');
$app->route('GET /user/@name', '\controllers\user->showUser');
$app->route('GET /user/new', '\controllers\user->showUserRegister');
$app->route('GET /user/login', '\controllers\user->showUserLogin');
$app->route('GET /milestone/@hash', '\controllers\milestone->showMilestone');
$app->route('GET /project/add', '\controllers\project->showAddProject');
$app->route('GET /project/settings', '\controllers\project->showProjectSettings');
$app->route('GET /project/settings/role/@hash', '\controllers\project->showProjectSettingsRole');
$app->route('GET /project/settings/role/add', '\controllers\project->showAddRole');
$app->route('GET /project/settings/milestone/@hash', '\controllers\project->showProjectSettingsMilestone');
$app->route('GET /project/settings/milestone/add', '\controllers\project->showAddMilestone');
$app->route('GET /project/settings/category/add', '\controllers\project->showAddCategory');
$app->route('GET /project/settings/category/edit/@hash', '\controllers\project->showEditCategory');
$app->route('GET /project/settings/role/delete/@hash', '\controllers\project->deleteRole');
$app->route('GET /project/settings/milestone/delete/@hash', '\controllers\milestone->deleteMilestone');
$app->route('GET /project/settings/category/delete/@hash', '\controllers\project->deleteCategory');
$app->route('GET /wiki/@title', '\controllers\wiki->showEntry');
$app->route('GET /wiki', '\controllers\wiki->showEntry');
$app->route('GET /wiki/discussion/@hash', '\controllers\wiki->showDiscussion');

$app->route('GET /user/logout', '\controllers\user->logoutUser');
$app->route('POST /search', '\controllers\ticket->showTickets');
$app->route('POST /project/select', 'misc\main->selectProject');
$app->route('POST /user/login', '\controllers\user->loginUser');
$app->route('POST /user/new', '\controllers\user->registerUser');
$app->route('POST /ticket', '\controllers\ticket->addTicket');
$app->route('POST /ticket/@hash', '\controllers\ticket->editTicket');
$app->route('POST /project/add', '\controllers\project->projectAdd');
$app->route('POST /project/settings/member/setrole', '\controllers\project->projectSetRole');
$app->route('POST /project/settings/category/add', '\controllers\project->addEditCategory');
$app->route('POST /project/settings/category/edit', '\controllers\project->addEditCategory');
$app->route('POST /project/settings/role/edit', '\controllers\project->addEditRole');
$app->route('POST /project/settings/main/edit', '\controllers\project->projectEditMain');
$app->route('POST /project/settings/milestone/edit', '\controllers\milestone->addEditMilestone');
$app->route('POST /project/settings/member/add', '\controllers\project->projectAddMember');
$app->route('POST /project/setttings/member/delete', '\controllers\project->projectDelMember');
$app->route('POST /wiki', '\controllers\wiki->editEntry');
$app->route('POST /wikidiscussion', '\controllers\wiki->addDiscussion');

$app->run();

$app->clear('SESSION.SUCCESS');
$app->clear('SESSION.FAILURE');
