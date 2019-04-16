<?php
	// Custom Functions
	
		require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/pre-processing.php';

	//Start session

		if ( session_id() == '' )
		{
			session_start();
		}
	
	//Include database connection details
	
		require_once('config.php');
	
	//Array to store validation errors
	
		$errmsg_arr = array();
	
	//Validation error flag
	
		$errflag = false;
	
	//Connect to mysql server
	
		$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if(!$link) {
			die('Failed to connect to server: ' . mysql_error());
		}
	
	//Select database
	
		$db = mysql_select_db(DB_DATABASE);
		if(!$db) {
			die("Unable to select database");
		}
	
	//Function to sanitize values received from the form. Prevents SQL injection
	
		function clean($str) {
			$str = @trim($str);
			if(get_magic_quotes_gpc()) {
				$str = stripslashes($str);
			}
			return mysql_real_escape_string($str);
		}
		
	//Sanitize the POST values
	
		$fname = clean($_POST['fname']);
		$lname = clean($_POST['lname']);
		$login = clean($_POST['login']);
		$password = clean($_POST['password']);
		$cpassword = clean($_POST['cpassword']);
	
	//Input Validations
	
		if($fname == '') {
			$errmsg_arr[] = 'First name missing';
			$errflag = true;
		}
		if($lname == '') {
			$errmsg_arr[] = 'Last name missing';
			$errflag = true;
		}
		if($login == '') {
			$errmsg_arr[] = 'Login ID missing';
			$errflag = true;
		}
		if($password == '') {
			$errmsg_arr[] = 'Password missing';
			$errflag = true;
		}
		if($cpassword == '') {
			$errmsg_arr[] = 'Confirm password missing';
			$errflag = true;
		}
		if( strcmp($password, $cpassword) != 0 ) {
			$errmsg_arr[] = 'Passwords do not match';
			$errflag = true;
		}
	
	// CREATE MEMBERS TABLE IF NOT EXISTS
	
		$Create_Members_Table_Query = 
		"
			CREATE TABLE 
			`members` 
			( 
			  `member_id` 	int(11) 		unsigned NOT NULL auto_increment,
			  `firstname` 	varchar(100) 	default NULL,
			  `lastname` 	varchar(100) 	default NULL,
			  `login` 		varchar(100) 	NOT NULL default '',
			  `passwd` 		varchar(32) 	NOT NULL default '',
			  
			  PRIMARY KEY  (`member_id`)
			)
		";
		
		if ( mysql_query ( $Create_Members_Table_Query , $link ) )
		{
			echo "Table \"members\" Created Successfully" . ".<br/>";
		}
		else
		{
			echo "Error Creating \"members\" Table: " . mysql_error ( $link ) . ".<br/>";
		}
		
	// ALTER INITIAL PRIMARY KEY NUMBER
	
		$Members_Table_Primary_Key_Initial_Value = 100000001;
		$Change_Members_Table_Initial_Primary_Key_Query = "ALTER TABLE members AUTO_INCREMENT=" . $Members_Table_Primary_Key_Initial_Value . "";
		
		if ( mysql_query ( $Change_Members_Table_Initial_Primary_Key_Query , $link ) )
		{
			echo "Table \"photos_reference\" ID Field Initialzed to " . $Members_Table_Primary_Key_Initial_Value . ".<br/>";
		}
		else
		{
			echo "Error Initializing \"photos_reference\" ID Field to " . $Members_Table_Primary_Key_Initial_Value . ": " . mysql_error ( $link ) . ".<br/>";
		}
	
	//Check for duplicate login ID
	
		if($login != '') {
			$qry = "SELECT * FROM members WHERE login='$login'";
			$result = mysql_query($qry);
			if($result) {
				if(mysql_num_rows($result) > 0) {
					$errmsg_arr[] = 'Login ID is already in use.';
					$errflag = true;
				}
				@mysql_free_result($result);
			}
			else {
				die("Query failed");
			}
		}
	
	//If there are input validations, redirect back to the registration form
	
		if($errflag) {
			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			session_write_close();
			header("location: ../index-1-login.php?content=register-form");
			exit();
		}

	//Create INSERT query
	
		$qry = 
		"
			INSERT INTO 
				members
					(
						firstname, 
						lastname, 
						login, 
						passwd,
						gender,
						height,
						hair,
						eyes,
						birth_month,
						birth_day_of_month,
						birth_year,
						ethnicity,
						body,
						weight,
						email,
						zip
					) 
			VALUES
					(
						'$fname',
						'$lname',
						'$login',
						'".md5($_POST['password'])."',
						'" . $_SESSION['gender'] . "',
						'" . $_SESSION['height'] . "',
						'" . $_SESSION['hair'] . "',
						'" . $_SESSION['eyes'] . "',
						'" . $_SESSION['birth_month'] . "',
						'" . $_SESSION['birth_day_of_month'] . "',
						'" . $_SESSION['birth_year'] . "',
						'" . $_SESSION['ethnicity'] . "',
						'" . $_SESSION['body'] . "',
						'" . $_SESSION['weight'] . "',
						'" . $_SESSION['email'] . "',
						'" . $_SESSION['zip'] . "'
					)
		";
		$result = @mysql_query($qry);
	
	//Check whether the query was successful or not
	
		if($result) {
			header("location: ../index-1-login.php?content=register-success");
			exit();
		}else {
			die("Query failed");
		}
?>
