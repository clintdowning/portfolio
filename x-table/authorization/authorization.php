<?php

// AUTHORIZATION AND DIRECTION TRAFFICKER ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function Authorization_and_Redirection ( $Redirect )
	{
	
		// INCLUDES
		
			require_once $_SERVER['DOCUMENT_ROOT'] . '/authorization/authorization_functions.php';	// AUTHORZATION FUNCTIONS
			require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/url_detect.php';		// DETECT CURRENT URL
	
		// OPEN MEMBER DATABASE CONNECTION
		
			//  DATABASE CONNECTIONS
			
				require_once $_SERVER['DOCUMENT_ROOT'] . '/-CONNECTION.php';
			
			// GLOBAL VARIABLES
			
				require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/globals.php';
				
			// DATABASE CONNECTION SETUP ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
				// 01 - CREATE DATABASE CONNECTION
				
					// CREATE DATABASE CONNECTION HANDLE
					
						$Connection_Handle = mysql_connect ( DB_HOST , DB_USER , DB_PASSWORD );
						
					// TEST DATABASE CONNECTION HANDLE
					
						if ( !$Connection_Handle )
						{
							die ( "Database Connection Failed: " . mysql_error ( ) );
						}
				
				// 02 - SELECT DATABASE
			
					// CREATE DATABASE SELECTION HANDLE
				
						$Database_Selected_Handle = mysql_select_db ( DB_DATABASE , $Connection_Handle );
						
					// TEST DATABASE SELECTION HANDLE
					
						if(!$Database_Selected_Handle)
						{
							die ( "Database Selection failed: " . mysql_error ( ) );
						}
	
		// DECISION STRUCTURE LOGIC //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			// IDEA HERE IS TO EITHER LOCATE A UNIQUE RECORD ROW OR CREATE A UNIQUE RECORD ROW
			// A UNIQUE RECORD ROW IS ONE WITH A UNIQUE COOKIE AND IP COMBINATION
			
			// IF LOGGED IN VALUE IS NOT SET, SET IT TO FALSE
			
				if ( ! isset ( $_SESSION['LOGGED_IN'] ) )
				{
					$_SESSION['LOGGED_IN'] = 0;
				}
			
			// DOES COOKIE EXIST IN DB?
			
				$Does_Cookie_ID_Exist_In_Database = READ_Does_Cookie_Exist_In_Database ( $Connection_Handle );
				
				// if ( DEBUG == TRUE ) echo "- Does_Cookie_ID_Exist_In_Database: [" . $Does_Cookie_ID_Exist_In_Database . "]<br/>";			
			
			// DOES IP EXIST IN DB?
			
				$Does_IP_Exist_In_Database = READ_Does_IP_Exist_In_Database ( $Connection_Handle );
				
				// if ( DEBUG == TRUE ) echo "- Does_IP_Exist_In_Database: [" . $Does_IP_Exist_In_Database . "]<br/>";			
				
			// CREATE NEW RECORD ROW WITH COOKIE AND IP COMBINATION
				
				if ( $Does_Cookie_ID_Exist_In_Database == 'COOKIE DOES NOT EXIST IN DATABASE' || $Does_IP_Exist_In_Database == 'IP DOES NOT EXIST IN DATABASE' )
				{
					CREATE_New_Row_Record_With_Cookie_and_IP_Combination ( $Connection_Handle );
					
					// if ( DEBUG == TRUE ) echo "- Record Created: [" . "" . "]<br/>";				
					
				}
				
			// IF USER IS NOT LOGGED IN, SET MEMBER_ID RELATED TO COOKIE
			
				if ( $_SESSION['LOGGED_IN'] == 0 )
				{
					$Query = 
					"
						SELECT 
							member_id 
						FROM 
							members 
						WHERE 
							Cookie_ID='" . $_COOKIE["Time4Times_Cookie_ID"] . "'
					";
					
					$Member_ID_Array = mysql_fetch_array ( mysql_query( $Query , $Connection_Handle ) );
					$_SESSION['SESS_MEMBER_ID'] = $Member_ID_Array['member_id'];
				}
				
				// if ( DEBUG == TRUE ) echo "- Session Member_ID: [" . $_SESSION['SESS_MEMBER_ID'] . "]<br/>";
				
			// WHAT IS USER PAID STATUS?
			
				$User_Paid_Status = READ_What_Is_User_Paid_Status ( $Connection_Handle );
				
				// if ( DEBUG == TRUE ) echo "- User_Paid_Status: [" . $User_Paid_Status . "]<br/>";			
				
			// HOW MANY WORKSHEETS MADE?
			
				$Worksheets_Made = READ_Worksheets_Made_by_IP ( $Connection_Handle );
				
				// if ( DEBUG == TRUE ) echo "- Worksheets_Made: [" . $Worksheets_Made . "]<br/>";
				
			// CLOSE DATABASE
			
				mysql_close( $Connection_Handle );
				
			// DETERMINE WHERE TO REDIRECT USERS
		
				if ( !headers_sent() )
				{
					// if ( DEBUG == TRUE ) echo "- IF Executed: [" . 1 . "]<br/>";
					if ( !isset ( $_SESSION['Redirect_Flag'] ) )
					{
						// if ( DEBUG == TRUE ) echo "- IF Executed: [" . 2 . "]<br/>";
						$_SESSION['Redirect_Flag'] = 'FALSE';
					}
	
					if ( $_SESSION['Redirect_Flag'] == 'FALSE' )
					{
						// if ( DEBUG == TRUE ) echo "- IF Executed: [" . 3 . "]<br/>";
						// if ( DEBUG == TRUE ) echo "- Session Pages_Viewed: [" . $_SESSION['Current_Session_Pages_Viewed'] . "]<br/>";
						// if ( DEBUG == TRUE ) echo "- Session Redirect Flag: [" . $_SESSION['Redirect_Flag'] . "]<br/>";
			
						if ( $_SESSION['Current_Session_Pages_Viewed'] > PER_SESSION_FREE_PASSES )
						{
							// if ( DEBUG == TRUE ) echo "- IF Executed: [" . 4 . "]<br/>";
							
							$_SESSION['Current_Session_Pages_Viewed']--;
							
							if ( $User_Paid_Status == 'Paid' && $_SESSION['LOGGED_IN'] == 0 )
							{
								// if ( DEBUG == TRUE ) echo "- IF Executed: [" . "5 PAID" . "]<br/>";
								$_SESSION['User_Paid_Status'] = 'Paid';
								$_SESSION['Redirect_Flag'] = 'FALSE';
								// if ( DEBUG == TRUE ) echo "- Session Redirect Flag: [" . $_SESSION['Redirect_Flag'] . "]<br/>";
								if ( $Redirect == 'Redirect' )
								{
									header('location: index_08_login_form.php');
									exit();
								}
							}
							else if ( ( $User_Paid_Status == 'Not Paid' ) && ( $Worksheets_Made <= FREE_WORKSHEETS_UNTIL_ASKED_TO_PAY ) )
							{
								// if ( DEBUG == TRUE ) echo "- IF Executed: [" . "6 FREE" . "]<br/>";
								$_SESSION['User_Paid_Status'] = 'Free';
								$_SESSION['Redirect_Flag'] = 'FALSE';
								// if ( DEBUG == TRUE ) echo "- Session Redirect Flag: [" . $_SESSION['Redirect_Flag'] . "]<br/>";
								if ( $Redirect == 'Redirect'  && $Worksheets_Made > FREE_WORKSHEETS_UNTIL_ASKED_TO_PAY )
								{
									header('location: index.php');
									exit();
								}
							}
							else if ( ( $User_Paid_Status == 'Not Paid' ) && ( $Worksheets_Made > FREE_WORKSHEETS_UNTIL_ASKED_TO_PAY ) )
							{
								// if ( DEBUG == TRUE ) echo "- IF Executed: [" . "7 PAY" . "]<br/>";
								$_SESSION['User_Paid_Status'] = 'Pay';
								$_SESSION['Redirect_Flag'] = 'FALSE';
								// if ( DEBUG == TRUE ) echo "- Session Redirect Flag: [" . $_SESSION['Redirect_Flag'] . "]<br/>";
								if ( $Redirect == 'Redirect' )
								{
									header('location: index_14_please_invest.php');
									exit();
								}
							}
							else if ( $User_Paid_Status == 'Paid' && $_SESSION['LOGGED_IN'] == 1 )
							{
								$_SESSION['User_Paid_Status'] = 'Paid';
								$_SESSION['Redirect_Flag'] = 'FALSE';
							}
							else
							{
								// if ( DEBUG == TRUE ) echo "- IF Executed: [" . 8 . "]<br/>";
								$_SESSION['User_Paid_Status'] = 'Undefined_A';
							}
						}
					}
					else if ( $_SESSION['Redirect_Flag'] == 'TRUE' )
					{
						// if ( DEBUG == TRUE ) echo "- IF Executed: [" . 9 . "]<br/>";
						$_SESSION['Redirect_Flag'] = 'FALSE';
					}
				}
	}
			
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>
