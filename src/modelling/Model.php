<?php

class Model {

	protected $connection;

	protected function __construct() {
        require_once("../app/config/database_config.php");

		try {
  			$this->connection =  new PDO("mysql:host=$database_host;dbname=$database_name", $database_username, $database_password);
  			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			Logger::error("Failed to connect. $e->getMessage()");
            View::redirect("error");
        }
	}

	protected function return_query($query, $values = ""){
		try {
			$statement = $this->connection->prepare($query);

	        if (is_array($values)) {
	            $statement->execute($values);
	        }else {
	            $statement->execute();
	        }

	        $results = $statement->fetchAll();

	        $statement->closeCursor();

	        return $results;

	    }catch (Exception $e) {
			Logger::error($e->getMessage());
	    	return null;
	    }
	}
	
	protected function binary_query($query, $values = "") {
		$success = false;

		try {
	        $statement = $this->connection->prepare($query);

	        if (is_array($values)) {
	            $success = $statement->execute($values);
	        } else {
	            $success = $statement->execute();
	        }

	        return $success;

        }catch (Exception $e) {
			Logger::error($e->getMessage());
	    	return null;
	    }
	}

	protected function last_insert() {
		try {
			$query = "SELECT LAST_INSERT_ID()";

			$statement = $this->connection->prepare($query);

	        $statement->execute();

	        $id = $statement->fetchAll();

	        return $id;

		}catch (PDOException $e) {
			Logger::error($e->getMessage());
	    	return false;
	    }
	}
}