<?php

$app=require(__DIR__.'/lib/base.php');

$app->set('CACHE', true);
$app->set('DEBUG', 2);
$app->set('EXTEND', true);
$app->set('GUI','gui/');

