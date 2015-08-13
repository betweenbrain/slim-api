<?php

/**
 * File       Authentication.php
 * Created    8/12/15 4:04 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2015 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */
namespace Slimapi\Access;

use Slimapi\Database;

class Authenticate
{

	public function __construct($app)
	{
		$helper = new Database\Helper();

		$this->app = $app;
		$this->db  = $helper->getDb();

	}

	/**
	 * Checks if the http username and password headers contain a valid with the supplied password
	 *
	 * @param $app
	 */
	public function isUser()
	{

		try
		{
			$sql = 'SELECT *
		        FROM users
		        WHERE username = :username';

			$query = $this->db->prepare($sql);
			$query->execute(
				array(
					'username' => $this->app->request->headers->get('username')
				)
			);

			$user = $query->fetch(\PDO::FETCH_OBJ);

			if (!$user || !password_verify($this->app->request->headers->get('password'), $user->password))
			{
				$this->app->response->setStatus(401);
				$db = null;
			}
			else
			{
				$this->app->response->setStatus(200);
				return true;
			}

		} catch (\PDOException $e)
		{
			$this->app->response()->setStatus(404);
			echo '{"error":{"text":' . $e->getMessage() . '}}';
		}
	}

	public function isAdmin()
	{

		try
		{
			$sql = 'SELECT *
		        FROM users
		        WHERE username = :username
		        AND role = :role';

			$query = $this->db->prepare($sql);
			$query->execute(
				array(
					'username' => $this->app->request->headers->get('username'),
					'role'     => 'admin'
				)
			);

			$user = $query->fetch(\PDO::FETCH_OBJ);

			if (!$user || !password_verify($this->app->request->headers->get('password'), $user->password))
			{
				return false;
			}

		} catch (\PDOException $e)
		{
			$this->app->response()->setStatus(404);
			echo '{"error":{"text":' . $e->getMessage() . '}}';
		}

		return true;

	}

	/**
	 * Quick method to add a user
	 */
	public function user()
	{
		// Add user
		$admin = 'INSERT INTO users (username, role, password) '
			. "VALUES (:username, :role, :password)";

		try
		{
			$admin = $this->db->prepare($admin);
			$admin->execute(array(
				'username' => $this->app->request->params('username'),
				'role'     => $this->app->request->params('role'),
				'password' => password_hash($this->app->request->params('password'), PASSWORD_DEFAULT)
			));

			return true;

		} catch (PDOException $e)
		{
			die(sprintf('User creation error: %s', $e->getMessage()));
		}
	}

}