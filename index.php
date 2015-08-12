<?php
require 'vendor/autoload.php';
require 'src/autoload.php';

/**
 * File       index.php
 * Created    8/12/15 10:42 AM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2015 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */

$app    = new \Slim\Slim();
$helper = new \Slimapi\Database\Helper();
$auth   = new \Slimapi\Access\Authenticate($app);
$db     = $helper->getDb();

$app->get('/', function () use ($auth)
{
	$auth->isUser();
});

$app->post('/user/', function () use ($app, $auth)
{

	if ($auth->isAdmin())
	{
		if($auth->user())
		{
			$app->response->setStatus(201);
		}
	}

	if (!$auth->isAdmin())
	{
		$app->response->setStatus(400);
	}

});

$app->run();