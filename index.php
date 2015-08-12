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

$app = new \Slim\Slim();

$helper = new Slimapi\Database\Helper();
$db     = $helper->getDb();

$app->get('/', function () use ($app, $db)
{
	try
	{
		$sql = 'SELECT *
	        FROM users
	        WHERE username = :username';

		$query = $db->prepare($sql);
		$query->execute(
			array(
				'username' => $app->request->headers->get('username')
			)
		);

		$user = $query->fetch(PDO::FETCH_OBJ);

		if (!$user || !password_verify($app->request->headers->get('password'), $user->password))
		{
			$app->response->setStatus(401);
			$db = null;
		}
		else
		{
			$app->response->setStatus(200);
			echo "Welcome to the Slim API.";
		}

	} catch (PDOException $e)
	{
		$app->response()->setStatus(404);
		echo '{"error":{"text":' . $e->getMessage() . '}}';
	}
});

$app->run();