<?php

// FUNCTION DEFINITIONS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			function READ_Does_Cookie_Exist_In_Database ( $Connection_Handle )
			{
				$query = 
				"
					SELECT 
						Cookie_ID 
					FROM 
						members 
					WHERE 
						Cookie_ID='" . $_COOKIE["Time4Times_Cookie_ID"] . "'
				";
				
				$Query_Array = mysql_fetch_array( mysql_query( $query , $Connection_Handle ) );
				$Result = $Query_Array['Cookie_ID'];
				$Does_Cookie_Exist = !isset( $Result ) ? "COOKIE DOES NOT EXIST IN DATABASE" : "COOKIE EXISTS IN DATABASE" ;
				
				return $Does_Cookie_Exist;
			}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			function READ_Does_IP_Exist_In_Database ( $Connection_Handle )
			{
				$query = 
				"
					SELECT 
						IP_Address 
					FROM 
						members 
					WHERE 
						IP_Address='" . $_SERVER['REMOTE_ADDR'] . "'
				";
				
				$Query_Array = mysql_fetch_array ( mysql_query( $query , $Connection_Handle ) );
				$Result = $Query_Array['IP_Address'];
				$Does_IP_Exist_In_Database = !isset( $Result ) ? "IP DOES NOT EXIST IN DATABASE" : "IP EXISTS IN DATABASE" ;
				
				return $Does_IP_Exist_In_Database;
			}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			function CREATE_New_Row_Record_With_Cookie_and_IP_Combination ( $Connection_Handle )
			{
				date_default_timezone_set('America/New_York');
				
				if( isset ( $_COOKIE['Time4Times_Cookie_ID'] ) )
				{
					$query  = 
					"
						INSERT INTO 
							members 
								( 
									Cookie_ID				, 
									IP_Address				, 
									Date_Time_First_Visited	, 
									Web_Browser 
								) 
						VALUES 
								( '" . 
									$_COOKIE['Time4Times_Cookie_ID']	. "' , '" . 
									$_SERVER['REMOTE_ADDR']				. "' , '" . 
									date( "Y-m-d H:i:s" )				. "' , '" . 
									$_SERVER['HTTP_USER_AGENT']			. "' 
								)
					";
					
					mysql_query( $query , $Connection_Handle );
				}
			}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			function READ_What_Is_User_Paid_Status ( $Connection_Handle )
			{
				if ( $_SESSION['LOGGED_IN'] == 1 )
				{
					$Query =
					"
						SELECT 
							User_Paid_Status 
						FROM 
							members 
						WHERE 
							login='" . $_SESSION['SESS_LOGIN'] . "'
					";
				}
				else if ( $_SESSION['LOGGED_IN'] == 0 )
				{
					$Query  = 
					"
						SELECT 
							User_Paid_Status 
						FROM 
							members 
						WHERE 
							member_id='" . $_SESSION['SESS_MEMBER_ID'] . "'
					";
				}
				
				$Paid_Statuses_Array = mysql_fetch_array ( mysql_query ( $Query , $Connection_Handle ) );
				
				// CHECK IF ANY VALUES ARE PAID
				
					$Final_Status = 'Not Paid';
					
					foreach ( $Paid_Statuses_Array as $Current_Status )
					{
						if ( $Current_Status == 'Paid' )
						{
							$Final_Status = 'Paid';
						}
					}
					
				return $Final_Status;
			}
	
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			function READ_Worksheets_Made_by_IP ( $Connection_Handle )
			{
				$query = 
				"
					SELECT 
						Worksheets_Made 
					FROM 
						members 
					WHERE 
						Cookie_ID='" . $_COOKIE['Time4Times_Cookie_ID'] . "'
				";

				$Query_Array = mysql_fetch_array( mysql_query( $query , $Connection_Handle ) );

				return $Query_Array['Worksheets_Made'];
			}

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			function UPDATE_Add_1_to_Worksheets_Made_by_IP ( $Current_Number_of_Worksheets_Made , $Connection_Handle )
			{
				$query = 
				"
					UPDATE 
						members 
					SET 
						Worksheets_Made=" . ++$Current_Number_of_Worksheets_Made . " 
					WHERE 
						Cookie_ID='" . $_COOKIE["Time4Times_Cookie_ID"] . "'
				";

				mysql_query( $query , $Connection_Handle );
			}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			function READ_Member_ID ( $Connection_Handle_MEMBERS )
			{
				if ( isset ( $_COOKIE["Time4Times_Cookie_ID"] ) )
				{
					$Query = 
					"
						SELECT 
							member_id 
						FROM 
							members 
						WHERE 
							Cookie_ID='" . $_COOKIE["Time4Times_Cookie_ID"] .= "'
					";
					
					$Member_ID = FALSE;
					
					if ( mysql_query ( $Query , $Connection_Handle_MEMBERS ) )
					{
						$Query_Array = mysql_fetch_array ( mysql_query ( $Query , $Connection_Handle_MEMBERS ) );
						$Member_ID = $Query_Array['member_id'];
					}
					
					return $Member_ID;
				}
			}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			function READ_Does_Member_Exist_In_Database ( $Connection_Handle_MEMBERS )
			{
				$Query = 
				"
					SELECT 
						member_id 
					FROM 
						members 
					WHERE 
						Cookie_ID='" . $_COOKIE["Time4Times_Cookie_ID"] .= "'
				";
				
				return $Does_Member_Exist_In_Database;
			}
			
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			function READ_Does_Member_Have_Worksheets_In_Database ( $Connection_Handle_MEMBERS )
			{
				$Member_ID = READ_Member_ID ( $Connection_Handle_MEMBERS );
				
				$Query = 
				"
					SELECT 
						Worksheets_Made 
					FROM 
						member_id_" . $Member_ID . "
				";
				
				$Does_Member_Have_Worksheets_In_Database = FALSE;
				
				if ( mysql_query ( $Query , $Connection_Handle_MEMBERS  ) )
				{
					if( mysql_fetch_array ( mysql_query ( $Query , $Connection_Handle_MEMBERS ) ) )
					{
						$Worksheets_Made_By_Member = mysql_fetch_array ( mysql_query ( $Query , $Connection_Handle_MEMBERS ) );
						
						if( $Worksheets_Made_By_Member > 0 )
						{
							$Does_Member_Have_Worksheets_In_Database = TRUE;
						}
					}
				}
				
				return $Does_Member_Have_Worksheets_In_Database;
			}
			
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			function GENERAL_Set_User_Member_ID ( $Connection_Handle_MEMBERS )
			{
				if ( isset ( $_SESSION['LOGGED_IN'] ) )
				{
					$Member_ID = $_SESSION['SESS_MEMBER_ID'];
				}
				else if ( isset ( $_COOKIE["Time4Times_Cookie_ID"] ) )
				{
					$Member_ID = READ_Member_ID ( $Connection_Handle_MEMBERS );
				}
				else
				{
					CREATE_New_Row_Record_With_Cookie_and_IP_Combination ( $Connection_Handle_MEMBERS );
					$Member_ID = READ_Member_ID ( $Connection_Handle_MEMBERS );
				}
			}

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			function READ_Get_Current_Worksheet_Number_of_User ( $Connection_Handle_GRADES )
			{
				// OPEN DATABASES
				
					require $_SERVER['DOCUMENT_ROOT'] . '/database_connections/database_connection_members.php';
					require $_SERVER['DOCUMENT_ROOT'] . '/database_connections/database_connection_grades.php';
				
				// EXTRACT WORKSHEET IDS
			
					$Query =
					"
						SELECT 
							Worksheet_ID 
						FROM 
							member_id_" . $_SESSION['SESS_MEMBER_ID'] . "
					";
			
				// CREATE WORKSHEET ID ARRAY
				
					$_SESSION['Highest_Worksheet_ID'] = 1;

					$Query_Result = mysql_query ( $Query , $Connection_Handle_GRADES );
				
					if ( $Query_Result != FALSE )
					{
						while ( $Row = mysql_fetch_array ( $Query_Result ) )
						{
							if ( $_SESSION['Highest_Worksheet_ID'] <= $Row['Worksheet_ID'] )
							{
								$_SESSION['Highest_Worksheet_ID'] = $Row['Worksheet_ID'];
								$_SESSION['Highest_Worksheet_ID']++;
							}
						}
					}
				
				// CLOSE DATABASES
					
					mysql_close ( $Connection_Handle_GRADES );
					mysql_close ( $Connection_Handle_MEMBERS );
			}
			
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
