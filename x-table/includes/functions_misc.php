<?php

	function Display_Free_Limited_Time_Message ( )
	{
		if ( isset ( $_SESSION['User_Paid_Status'] ) )
		{
			if ( ( $_SESSION['User_Paid_Status'] != 'Paid' ) && ( ( curPageName ( ) == 'index.php' ) || ( curPageName ( ) == '_Multiplication_Worksheets_Interactive.php' ) ) )
			{
				// IF NOT PAID AND HOME SCREEN
	
					echo "<img src='assets/Free_Stamp.gif' style='margin-top: 10px;' width='180' height='181' alt='FREE SAMPLE BELOW for 0's, 1's, 2's, 3's, 4's, and 5's for a Limited Time!' />";
					echo
					"
						<p 
							style=
							'
								color: yellow;
								font-weight: bold;
								font-size: 25px;
							' 
						>
							FREE SAMPLE BELOW for 0's, 1's, 2's, 3's, 4's, and 5's for a Limited Time!
						</p>
					";
			}
			else if ( ( $_SESSION['User_Paid_Status'] != 'Paid' ) && ( curPageName ( ) == '_Multiplication_Worksheets_Interactive_02.php' ) )
			{
				// IF NOT PAID AND CUSTOM SCREEN

					echo "<img src='assets/Free_Stamp.gif' style='margin-top: 10px;' width='180' height='181' alt='FREE SAMPLE BELOW for a Limited Time!' />";
					echo
					"
						<p 
							style=
							'
								color: yellow;
								font-weight: bold;
								font-size: 25px;
							' 
						>
							FREE SAMPLE BELOW for a Limited Time!
						</p>
					";
			}
		}
		else if ( !isset ( $_SESSION['User_Paid_Status'] ) )
		{
			if ( ( curPageName ( ) == 'index.php' ) || ( curPageName ( ) == '_Multiplication_Worksheets_Interactive.php' ) )
			{
				// IF NOT PAID AND HOME SCREEN
	
					echo "<img src='assets/Free_Stamp.gif' style='margin-top: 10px;' width='180' height='181' alt='FREE SAMPLE BELOW for 0's, 1's, 2's, 3's, 4's, and 5's for a Limited Time!' />";
					echo
					"
						<p 
							style=
							'
								color: yellow;
								font-weight: bold;
								font-size: 25px;
							' 
						>
							FREE SAMPLE BELOW for 0's, 1's, 2's, 3's, 4's, and 5's for a Limited Time!
						</p>
						";
			}
			else if ( curPageName ( ) == '_Multiplication_Worksheets_Interactive_02.php' )
			{
				// IF NOT PAID AND CUSTOM SCREEN

					echo "<img src='assets/Free_Stamp.gif' style='margin-top: 10px;' width='180' height='181' alt='FREE SAMPLE BELOW for a Limited Time!' />";
					echo
					"
						<p 
							style=
							'
								color: yellow;
								font-weight: bold;
								font-size: 25px;
							' 
						>
							FREE SAMPLE BELOW for a Limited Time!
						</p>
					";
			}
		}
	}

?>
