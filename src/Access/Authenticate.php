<?php

/**
 * File       Authenticate.php
 * Created    8/24/15 2:51 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2015 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */

namespace Slimapi\Access;

class Authenticate extends \Slim\Middleware
{
	private $username;
	private $password;

	public function __construct($options = array())
	{
		$this->options = $options;
	}

	/**
	 *
	 */
	public function call()
	{

		$isAuthorized = function ()
		{
			$route = $this->app->request->getResourceUri();

			// Authenticate only specified routes
			if (array_key_exists($route, $this->options))
			{
				// Check for and set user credentials first as we're in an authenticated route
				if ($this->setCredentials())
				{
					foreach ($this->options[$route] as $option => $value)
					{
						// HTTP request method specific authentication
						if (in_array($option, array('GET', 'PUT', 'POST', 'DELETE')) && $option == $this->app->request->getMethod())
						{
							if ($this->authenticate($value))
							{
								// User has authenticated
								break;
							}
						}

						// Non-http request specific authentication
						if ($this->authenticate($this->options[$route]))
						{
							// User has authenticated
							break;
						}

						// Default response for authenticated routes
						$this->app->halt(403, 'Invalid credentials.');
					}
				}
			}
		};

		// Execute middleware with application hook to be able call Slim instance methods like halt()
		$this->app->hook('slim.before.dispatch', $isAuthorized);

		// Non-authenticated routes aren't checked
		$this->next->call();
	}

	/**
	 * Checks the set credentials against the options array
	 *
	 * @param $option
	 *
	 * @return bool
	 */
	private function authenticate($option)
	{
		if (array_key_exists($this->username, $option) && $this->password === $option[$this->username])
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks for and sets Basic HTTP authentication credentials
	 */
	private function setCredentials()
	{

		$this->username = $this->app->request->headers->get('PHP_AUTH_USER');

		if (is_null($this->username))
		{
			$this->app->response->header('WWW-Authenticate: Basic realm="My Realm"');
			$this->app->halt(403, 'Authorization required.');

			return false;
		}
		else
		{
			$this->username = $this->app->request->headers->get('PHP_AUTH_USER');
			$this->password = $this->app->request->headers->get('PHP_AUTH_PW');

			return true;
		}
	}

}
