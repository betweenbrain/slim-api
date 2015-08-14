<?php
namespace Slimapi\Access;

use Slimapi\Database;

class Authenticate extends \Slim\Middleware
{

	public function __construct()
	{
		$helper   = new Database\Helper();
		$this->db = $helper->getDb();
	}

	/**
	 * Uses Slim's 'slim.before.router' hook to check for user authorization.
	 * Will redirect to named login route if user is unauthorized
	 *
	 * @throws \RuntimeException if there isn't a named 'login' route
	 */
	public function call()
	{

		$isAuthorized = function ()
		{
			try
			{
				$sql   = 'SELECT *
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
					$this->app->halt(403, 'You shall not pass!');
					$this->db = null;
				}
			} catch (\PDOException $e)
			{
				$this->app->halt(404, sprintf('Authentication error: %s', $e->getMessage()));
			}
		};

		$this->app->hook('slim.before.dispatch', $isAuthorized);
		$this->next->call();
	}
}
