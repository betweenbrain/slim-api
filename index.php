<?php
require 'vendor/autoload.php';

/**
 * File       index.php
 * Created    8/12/15 10:42 AM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2015 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */

/**
 * @return PDO
 */
function getDb()
{
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "slim-api";

	$mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";

	// Try database connection and die if it fails
	try
	{
		$db = new PDO($mysql_conn_string, $dbuser, $dbpass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	} catch (PDOException $e)
	{
		die(sprintf('DB connection error: %s', $e->getMessage()));
	}

	$create = 'CREATE TABLE IF NOT EXISTS `users` ( '
		. '`id` INTEGER  NOT NULL PRIMARY KEY, '
		. '`username` VARCHAR(50) NOT NULL, '
		. '`role` VARCHAR(50) NOT NULL, '
		. '`password` VARCHAR(255) NULL)';

	try
	{
		$db->exec($create);
	} catch (PDOException $e)
	{
		die(sprintf('DB setup error: %s', $e->getMessage()));
	}

	return $db;

}

$app = new \Slim\Slim();

$app->get('/', function () use ($app)
{
	$app->response->setStatus(200);
	echo "Welcome to the Slim API.";
});

$app->run();