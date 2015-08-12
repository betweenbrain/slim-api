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
function getDB()
{
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "slim-api";

	$mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";

	try
	{
		$dbConnection = new PDO($mysql_conn_string, $dbuser, $dbpass);
		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		return $dbConnection;
	} catch (PDOException $e)
	{
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
}

getDb();