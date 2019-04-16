	<!-- SENT FINISHED FORM INTO GRADE SHEET -->
    
    	<script>
		
			function Submit_For_Grading_Form_Processing()
			{
				document.getElementById("Worksheet_Form").action											=
					"interactive_worksheet/06_Save_Interactive_Worksheet_Results.php?Total_Right_Answers="	+
						Total_Right_Answers																	+
					"&Total_Answered="																		+
						Total_Answered																		+
					"&Elapsed_Seconds_Total="																+
						--Elapsed_Seconds_Total																
				;
				document.getElementById("Worksheet_Form").submit();
			}
		
		</script>
   
    <?php
    
		// 01 - OPEN CONNECTION /////////////////////////////////////////////////////////////////////////////////////////////////////// 01
		
				$connectionHandle = mysql_connect( DB_HOST_A , DB_USER_A , DB_PASSWORD_A );
		
				if (!$connectionHandle)
				{
					die("Could Not Connect: " . mysql_error());
				}
	
		// 02 - SELECT DATABASE /////////////////////////////////////////////////////////////////////////////////////////////////////// 02
		
				$databaseSelectedHandle = mysql_select_db(DB_DATABASE_A,$connectionHandle);
				
				if(!$databaseSelectedHandle)
				{
					die("Database Selection failed: " . mysql_error());
				}
	
		// CREATE UNIQUE TABLE NAME
		
			// FIND IP OF USER AND REPLACE DOTS WITH UNDERSCORES
			
				$ipAddress = str_replace( "." , "_" , $_SERVER['REMOTE_ADDR'] );
				
			// CREATE UNIQUE TABLE NAME FROM TIMESTAMP AND IP
		
				$uniqueTableName = date( "Y_m_d_Hi_s_" ) . "ip_" . $ipAddress . "_Multiplication";
			
		// INITIALIZE MULTIPLICATION TABLE
				
			$table =
			
				"
					CREATE TABLE $uniqueTableName
					(
						problemID	int NOT NULL AUTO_INCREMENT,
						
						PRIMARY KEY ( problemID ),
						aValue		int,
						bValue		int,
						cValue		int
					)
				";
				
			mysql_query( $table , $connectionHandle );
			
		// CLEAN CHECKBOX VALUES TO BE "-" IF NULL OR THEIR ACTUAL VALUE IF SELECTED BY USER
		
			$_SESSION['checkbox0Val'] 	= 	isset($_POST["checkbox0"]) 	? 	$_POST["checkbox0"] 	: 	"-" ;
			$_SESSION['checkbox1Val'] 	= 	isset($_POST["checkbox1"]) 	? 	$_POST["checkbox1"] 	: 	"-" ;
			$_SESSION['checkbox2Val'] 	= 	isset($_POST["checkbox2"]) 	? 	$_POST["checkbox2"] 	: 	"-" ;
			$_SESSION['checkbox3Val'] 	= 	isset($_POST["checkbox3"]) 	? 	$_POST["checkbox3"] 	: 	"-" ;
			$_SESSION['checkbox4Val'] 	= 	isset($_POST["checkbox4"]) 	? 	$_POST["checkbox4"] 	: 	"-" ;
			$_SESSION['checkbox5Val'] 	= 	isset($_POST["checkbox5"]) 	? 	$_POST["checkbox5"] 	: 	"-" ;
			$_SESSION['checkbox6Val'] 	= 	isset($_POST["checkbox6"]) 	? 	$_POST["checkbox6"] 	: 	"-" ;
			$_SESSION['checkbox7Val'] 	= 	isset($_POST["checkbox7"]) 	? 	$_POST["checkbox7"] 	: 	"-" ;
			$_SESSION['checkbox8Val'] 	= 	isset($_POST["checkbox8"]) 	? 	$_POST["checkbox8"] 	: 	"-" ;
			$_SESSION['checkbox9Val'] 	= 	isset($_POST["checkbox9"]) 	? 	$_POST["checkbox9"] 	: 	"-" ;
			$_SESSION['checkbox10Val'] 	= 	isset($_POST["checkbox10"])	? 	$_POST["checkbox10"] 	: 	"-" ;
			$_SESSION['checkbox11Val'] 	= 	isset($_POST["checkbox11"])	? 	$_POST["checkbox11"] 	: 	"-" ;
			$_SESSION['checkbox12Val'] 	= 	isset($_POST["checkbox12"])	? 	$_POST["checkbox12"] 	: 	"-" ;
			
		// LOAD PRACTICE ARRAY WITH USER WANTED VALUES OR "-" IF NOT WANTED
		
			$_practiceAArrayUserWants = 
				array
				(
					$_SESSION['checkbox0Val'],
					$_SESSION['checkbox1Val'],
					$_SESSION['checkbox2Val'],
					$_SESSION['checkbox3Val'],
					$_SESSION['checkbox4Val'],
					$_SESSION['checkbox5Val'],
					$_SESSION['checkbox6Val'],
					$_SESSION['checkbox7Val'],
					$_SESSION['checkbox8Val'],
					$_SESSION['checkbox9Val'],
					$_SESSION['checkbox10Val'],
					$_SESSION['checkbox11Val'],
					$_SESSION['checkbox12Val']
				);
				
		// DETERMINE IF MULTIPLIER ARRRAY IS EMPTY
		
			$Practice_Array_Is_Empty = TRUE;
			
			$i = 1;
		
			foreach ( $_practiceAArrayUserWants as $Row )
			{
				if( $Row != '-' )
				{
					$Practice_Array_Is_Empty = FALSE;
					break;
				}
				$i++;
			}
			
		// DETERMINE MAX MULTIPLIER

			if ( ( max ( $_practiceAArrayUserWants ) == '-' ) && isset ( $_GET['Factor'] ) )	// PRE-BUILT BUTTON FUNCTIONS
			{
				$_SESSION['maxMultiplier'] = $_GET['Factor'];
			}
			else if ( max ( $_practiceAArrayUserWants ) == '-' )							// DEFAULT INDEX PAGE WORKSHEET
			{
				$_SESSION['maxMultiplier'] = rand ( 4 , 5 );
			}
			else if ( max ( $_practiceAArrayUserWants ) != '-'  )					// CUSTOM FORM ENTERED WORKSHEET
			{
				$_SESSION['maxMultiplier'] = max ( $_practiceAArrayUserWants );
			}
			
		// SET NUMBER OF PROBLEMS
		
			if ( $Practice_Array_Is_Empty == TRUE && isset ( $_GET['Factor'] ) )	// PRE-BUILT BUTTON FUNCTIONS
			{
				$_SESSION['numProblems'] = 15;
			}
			else if ( $Practice_Array_Is_Empty == TRUE )							// DEFAULT INDEX PAGE WORKSHEET
			{
				$_SESSION['numProblems'] = rand ( 15 , 15 );
			}
			else if( $Practice_Array_Is_Empty == FALSE )							// CUSTOM FORM ENTERED WORKSHEET
			{
				$_SESSION['numProblems'] = $_POST["numProblems"];
			}
			
		// LOAD TABLE
		
			// LOAD TABLE PROBLEMS 	starting with row 1
		
				for ( $i = 1 ; $i <= $_SESSION['numProblems'] ; $i++ )
				{
					// DETERMINE "A" & "B" VALUES
					
						$randNumA = rand ( 0 , $_SESSION['maxMultiplier'] );
						$randNumB = rand ( 0 , $_SESSION['maxMultiplier'] );
						
					// RANDOMLY DETERMINE POSITION OF A OR B TO BE TOP OR BOTTOM
					
						if ( rand( 0 , 1 ) == 0 )
						{
							$finalA = $randNumA;
							$finalB = $randNumB;
						}
						else
						{
							$finalA = $randNumB;
							$finalB = $randNumA;
						}
	
					// DETMINE "C" VALUE
					
						$resultC  = $finalA * $finalB;
							
					// LOAD ONE PROBLEM USING VALUES
								
						mysql_query
						(
							"
								INSERT INTO
									$uniqueTableName
									( 
										problemID 	, 
										aValue 		, 
										bValue 		, 
										cValue 
									)
								VALUES
									( 
										$i 			, 
										$finalA 	, 
										$finalB 	, 
										$resultC 
									)
							"
						);
				}
				
		// ==================== NEW WORKSHEET BUTTON ==================== 
		
				Glass_Hover_Link_To_Button
				(
					'Make New Worksheet'							,
					'_Multiplication_Worksheets_Interactive.php'	,
					200												,
					25												,
					15												,
					''												,
					'onclick="location.href=\'_Multiplication_Worksheets_Interactive.php\'")'
				);
				
		// DISPLAY TABLE
		
			// SELECT ALL DATA FROM DATABASE
			
				$allTableData = mysql_query("SELECT * FROM $uniqueTableName");
				
			// DISPLAY PROBLEMS ONE BY ONE
			
				// DECLARE CLASS INSTANCES
				
					$displayProblemInstance = new DisplayProblemClass();
					
				// DISPLAY ANSWER GRID
	
					echo
					"
						<form
							id='Worksheet_Form'
							name='problamMatrixForm'
							method='post'
						>
					";
					for ($j = 1 ; $j <= $_SESSION['numProblems'] ; )
					{
						echo "<table style='margin: 0 auto;' >";
						echo "    <tr>";
						for ( $k = 1 ; $k <= 3 ; $k++ )
						{
							$row = mysql_fetch_array( $allTableData );
							$displayProblemInstance->displayProblem( $row['problemID'] , $row['aValue'] , $row['bValue'] , $row['cValue'] );
							$j++;
							if ( $j > $_SESSION['numProblems'] ) { break; }
						}
						echo "    </tr>";
					}
					echo
					"
						</table>
							<table style='margin: 0 auto;' >
								<tr>
									<td>
										<input
											class=	'Submit_for_Grading_Button_ID'
											type=	'button'
											onclick='Submit_For_Grading_Form_Processing()'
											value=	'Add Grade to Report Card...'
										>
									</td>
								</tr>
							</table>
						</form>
					";
	
		// 05 - CLOSE CONNECTION
		
			mysql_close( $connectionHandle );
			
		// ==================== NEW WORKSHEET BUTTON ==================== 
		
				Glass_Hover_Link_To_Button
				(
					'Make New Worksheet'			,
					'_Multiplication_Worksheets_Interactive.php'	,
					200								,
					25								,
					15								,
					''								,
					'onclick="location.href=\'_Multiplication_Worksheets_Interactive.php\'"'
				);
    ?>
