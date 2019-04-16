<?

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/globals.php';

class MySQLDatabase {
  
	private $connection;
	
	private $show_backtrace = TRUE;
	
	function __construct ( $server, $user, $password , $database_name , $port = NULL , $socket = NULL ) {
		$this->open_connection ( $server, $user, $password , $database_name , $port , $socket );
	}
	
	function __destruct ( ) {
		$this->close_connection();
	}
	
	public function open_connection( $server, $user, $password , $database_name , $port , $socket ) {
		$this->connection = mysqli_connect( $server, $user, $password , $database_name , $port , $socket );
		if(mysqli_connect_errno()) {
			die("Database connection failed: " . 
				mysqli_connect_error() . 
				" (" . mysqli_connect_errno() . ")"
			);
		}
	}
	
	public function close_connection() {
		if(isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}
	
	public function query ($sql) {
		$result_build = array();
		$final_result = array();
		$result = mysqli_query($this->connection, $sql);
		$this->confirm_query($result, $sql);
		if ( gettype ( $result ) == 'boolean' ) {
			if ( $result === TRUE ) {						// Query successfully updated database with no return values.
				$final_result = TRUE;
			} elseif ( $result === FALSE ) {				// Query failed.
				$final_result = FALSE;
			} 
		} else {
			while ( $row = mysqli_fetch_assoc ( $result ) ) {  // Collect mysqli_query result object into array.
				$result_build[] = $row;
			}
			if ( count ( $result_build ) == 0 ) {		// Create resulting array of no rows.
				$final_result = NULL;
			} elseif ( count ( $result_build ) == 1 ) {		// Create resulting array of only one row.
				//$final_result = $result_build[0];
				$final_result = $result_build;
			} else {										// Create resulting array of mutltiple rows.
				$final_result = $result_build;
			}
		}
		return $final_result;
	}
	
	private function confirm_query($result, $sql) {
		if (!$result) {
			$die_message  = "<div style='
				background-color:yellow; 
				padding-top: 11px; 
				padding-bottom: 1px; 
				padding-left: 14px; 
				border:solid black 1px; 
				border-radius: 10px; 
			'>";
			$die_message .= "Database query failed.<br/>";
			$die_message .= "Query:<br/>";
			$die_message .= "<pre>" . $sql . "</pre>";
			$die_message .= "</div>";
			if ( $this->show_backtrace ) {
				$backtrace = debug_backtrace();
				d($backtrace);
			}
			die($die_message);
		}
	}
	
	public function escape_value($string) {
		$escaped_string = mysqli_real_escape_string($this->connection, $string);
		return $escaped_string;
	}
	
	public function escape_array_values($non_escaped_array) {
		$escaped_array = array();
		foreach ( $non_escaped_array as $key => $value ) {
			$escaped_array[$key] = $this->escape_value($value);
		}
		return $escaped_array;
	}
	
	// "database neutral" functions
	
	public function fetch_array($result_set) {
		return mysqli_fetch_array($result_set);
	}
	
	public function num_rows($result_set) {
		return mysqli_num_rows($result_set);
	}
	
	public function insert_id() {
		// get the last id inserted over the current db connection
		return mysqli_insert_id($this->connection);
	}
	
	public function affected_rows() {
		return mysqli_affected_rows($this->connection);
	}
  
}

?>
