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

// Implement user authentication as middleware using Slim's 'slim.before.router' hook
$app->add(new \Slimapi\Access\Authenticate(array(
			"/"     => array(
				"admin"   => "password",
				"manager" => "password",
				"user"    => "password"
			),
			"/user" => array(
				"GET"  => array(
					"admin"   => "password",
					"manager" => "password"
				),
				"POST" => array(
					"admin" => "password"
				)
			)
		)
	)
);

/**
 * Application routes
 */
$app->get('/', function () use ($app)
{

});

$app->get('/guest', function () use ($app)
{
	$app->response->write('Welcome guest!');
});

$app->get('/user/', function () use ($app)
{

});

$app->post('/user/', function () use ($app)
{

});

// Execute the application
$app->run();