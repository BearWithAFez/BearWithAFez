<?php

	/**
	 * Redirects to the error handling page
	 * @param string $type
	 * @param object $dbhandler
	 * @return void
	 */
	function showDbError($type, $msg) {

		// debug activated
		if (DEBUG === true) {

			switch($type) {

				case 'connect':
				case 'query':
					echo $msg;
				break;

				default:
					echo 'There was an unknown error while communicating with the database';
				break;

			}

		}

		// debug not activated
		else {

			// Log the error
			file_put_contents(ERROR_LOG, $msg . PHP_EOL, FILE_APPEND);

			// The referrerd page will show a proper error based on the $_GET parameters
			header('location: error.php?type=db&detail=' . $type);

		}

		exit();

	}

	/**
	 * Gets the DB connection
	 * @return PDO The DB Connection
	 */
	function getDatabase() {
		try {
			$db = new PDO('mysql:host=' . DB_HOST .';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $db;
		} catch (Exception $e) {
			showDbError('connect', $e->getMessage());
		}
	}

// EOF