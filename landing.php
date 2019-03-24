<?php

/**
	 * Includes
	 * ----------------------------------------------------------------
	 */

		// config & functions
		require_once 'includes/config.php';
		require_once 'includes/functions.php';
		require_once __DIR__ . '/includes/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
		$twig = new Twig_Environment($loader);

	/**
	 * Database Connection & session
	 * ----------------------------------------------------------------
	 */
	session_start();
	$sesUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;
	$db = getDatabase();

	/**
	 * Edit card #
	 * ----------------------------------------------------------------
	 */
	if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'editCopies')) {
		$newCopies = $_POST['newVal'];
		$id = $_POST['id'];

		$stmt = $db->prepare('UPDATE cards SET Copies=? WHERE Id=? AND SetCode="RNA"');
		$stmt->execute(array($newCopies,$id));

		header('location: landing.php#card-'.$id.'-RNA');
		exit();
	}

	/**
	 * Login
	 * ----------------------------------------------------------------
	 */
	if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'login')) {
		$username = isset($_POST['username']) ? trim($_POST['username']) : '';
		$password = isset($_POST['password']) ? trim($_POST['password']) : '';

		$stmt = $db->prepare('SELECT * FROM users WHERE name = ?');
			$stmt->execute(array($username));

		if ($stmt->rowCount() != 1) {
			$message = 'Invalid login...';
			echo "<script type='text/javascript'>alert('$message');</script>";
		}

		else {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

				// Password checks out
				if (password_verify($password, $user['password'])) {

					$_SESSION['user'] = $user;

					// Redirect to index
					header('location: landing.php');
					exit();
				}
				else{
					$message = 'Invalid login...';
			echo "<script type='text/javascript'>alert('$message');</script>";
				}
		}
	}

	/**
	 * No action to handle: show our page itself
	 * ----------------------------------------------------------------
	 */
	
	$stmt = $db->prepare('SELECT * FROM cards ORDER BY Id');
	$stmt->execute();

	$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

	/**
	 * Load and render template
	 * ----------------------------------------------------------------
	 */

		$tpl = $twig->loadTemplate('landing.twig');
		echo $tpl->render(array(
			'action' => $_SERVER['PHP_SELF'],
			'cards' => $cards,
			'user' => $sesUser
		));


// EOF