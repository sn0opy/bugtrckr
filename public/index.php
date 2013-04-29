<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2013, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

$app = include('../lib/base.php');

$app->config('../app/config.ini');
$app->config('../app/routes.ini');

$app->run();
