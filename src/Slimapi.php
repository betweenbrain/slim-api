<?php

/**
 * File       Slimapi.php
 * Created    8/12/15 2:51 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2015 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */

namespace Slimapi;

class Helper
{

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
			$db = new \PDO($mysql_conn_string, $dbuser, $dbpass);
			$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		} catch (PDOException $e)
		{
			die(sprintf('DB connection error: %s', $e->getMessage()));
		}

		// Create users table
		$create = 'CREATE TABLE IF NOT EXISTS `users` ( '
			. '`id` INTEGER  NOT NULL PRIMARY KEY, '
			. '`username` VARCHAR(50) NOT NULL, '
			. '`role` VARCHAR(50) NOT NULL, '
			. '`password` VARCHAR(255) NULL)';

		// Add admin user
		$admin = 'INSERT INTO users (username, role, password) '
			. "VALUES ('admin', 'admin', :pass)";

		// Returns the admin user ID
		$ifAdmin = 'SELECT id from users
		WHERE username = \'admin\'';

		try
		{
			$db->exec($create);

			$isAdmin = $db->prepare($ifAdmin);
			$isAdmin->execute();

			// Check if admin user exists before trying to create it again
			if (!$isAdmin->rowCount())
			{
				$admin = $db->prepare($admin);
				$admin->execute(array('pass' => password_hash('admin', PASSWORD_DEFAULT)));
			}

		} catch (PDOException $e)
		{
			die(sprintf('DB setup error: %s', $e->getMessage()));
		}

		return $db;

	}
}
