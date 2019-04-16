<?php ob_start(); ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/set_session_and_cookie.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/globals.php'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--  xmlns="http://www.w3.org/1999/xhtml" -->
<html>

    <head>
    
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<!-- REFRESH PAGE TO SHOW CURRENT SESSION STATUS -->

			<?php 
                if ( !isset ( $_COOKIE["Time4Times_Cookie_ID"] ) ) {
                    //echo "<META HTTP-EQUIV='refresh' CONTENT='0'>";
                }
            ?>
			
        <title>
        	Multiplication Worksheets and Multiplication Tables Generator
        </title>
        
        <!-- <link rel="canonical" href="http://www.time4times.com" /> -->

        <!-- META DATA -->

            <meta name="description" content="Multiplication Worksheets and Multiplication Tables Maker – Fun Interactive Worksheets, Tables, Printouts, & Exercises" />

            <meta name="language" content="English" />
            
        <!-- PHP INCLUDES -->
        
        	<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/authorization/authorization_functions.php'; ?>
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions_misc.php';  ?>
            
        <!-- URL DETECT FUNCTION INCLUDE -->
            
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/url_detect.php'; ?>
            
        <!-- GLASS HOVER LINK TO BUTTON FUNCTION INCLUDE -->
        
        	<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Glass_Hover_Link_To_Button.php'; ?>
            <link rel="stylesheet" type="text/css" href="css/Glass_Hover_Link_Button.css" />

        <!--  DATABASE CONNECTIONS -->
        
        	<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/-CONNECTION.php'; ?>
            
        <!-- INTERACTIVE JS -->
        
			<script src="javascript/javascript.js" ></script>
            
        <!-- JQUERY LIBARY INCLUDE -->
        
            <script src="<?php echo JQUERY_LIBRARY; ?>" ></script>
        
        <!-- CONTENT SHOW OR HIDE GLASS HOVER BUTTON -->
        
			<?php require $_SERVER['DOCUMENT_ROOT'] . '/includes/Show_and_Hide_Button.php'; ?>
        	<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Glass_Show_or_Hide_Content_Hover_Button_Function.php'; ?>
            
        <!-- CSS STYLES -->
            
            <link rel="stylesheet" type="text/css" href="css/main_02.css" />
            <link rel="stylesheet" type="text/css" href="css/suggestions_box.css" />
            <link rel="stylesheet" type="text/css" href="css/external_sites.css" />
            <link rel="stylesheet" type="text/css" href="css/interactive_input_form.css" >
            <link rel="stylesheet" type="text/css" href="css/interactive_tally_board.css" >
            <link rel="stylesheet" type="text/css" href="css/interactive_worksheet_problems.css" >
            <link rel="stylesheet" type="text/css" href="timer/timer_01_style.css" />

		<!-- FAVICON REFERENCE -->
        
            <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
            
		<!-- PIE CHART JQUERY JAVASCRIPT INCLUDE -->
        
        	<?php require $_SERVER['DOCUMENT_ROOT'] . '/pie-chart/-pie-chart-includes.php'; ?>
			
		<!-- ANALYTICS INCLUDE -->
		
        	<?php require $_SERVER['DOCUMENT_ROOT'] . '/third-party-tags/alexa.php'; ?>

    </head>
    
    <body>
    
		<!-- USER AUTHORIZATION -->
        
        	<div>
            	
				<?php
					if( isset ( $_COOKIE["Time4Times_Cookie_ID"] ) ) {
						require_once $_SERVER['DOCUMENT_ROOT'] . '/authorization/authorization.php';
	                	Authorization_and_Redirection ( 'No_Redirect' );
					}
				?>
            
            </div>

        <!-- SET USER MEMBER_ID -->
        
            <?php
			
				require $_SERVER['DOCUMENT_ROOT'] . '/database_connections/database_connection_members.php';
				require $_SERVER['DOCUMENT_ROOT'] . '/database_connections/database_connection_grades.php';
            
	            GENERAL_Set_User_Member_ID ( $Connection_Handle_MEMBERS );
				
				mysql_close ( $Connection_Handle_GRADES );
				mysql_close ( $Connection_Handle_MEMBERS );
				
			?>
            
        <!-- HEADER -->
    
            <div class="header" >
                <?php
					if ( '_Multiplication_Worksheets_Interactive.php' == curPageName ( ) ) {
						require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/header_slim.php';
					} else {
						require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php';
					}
				?>
            </div>
            
        <!-- BOARD TOP -->
        
			<a id="generator"></a>
        
            <div class="board_top" ></div>
            
        <!-- LEFT COLUMN -->
        
            <article>
        
                <div class="left" >
                    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/left_column.php'; ?>
                </div>
            

        <!-- BOARD -->
    
                <div class="board" >

                    <p style="font-size:25px;" >
                    	<strong>Multiplication Worksheets Generator</strong>
                    </p>
                    
                    <!-- COMMENTARY -->
                    
                        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/commentary.php'; ?>
                        
                    <!-- INTERACTIVE INPUT AND OUTPUT -->

                        <!-- ==================== DISPLAY PROBLEM CLASS DEFINITION ==================== -->
                        
                            <?php require $_SERVER['DOCUMENT_ROOT'] . '/interactive_worksheet/04_display_problem_class.php'; ?>
                                
                        <!-- ==================== TALLY BOARD JAVASCRIPT VARIABLES AND FUNCTIONS ==================== -->
                        
                            <script src="interactive_worksheet/02_tally_board_variables_and_functions.js"></script>
                            
                        <!-- ==================== DISPLAY INPUT FORM ==================== -->
                        
                            <?php require $_SERVER['DOCUMENT_ROOT'] . '/interactive_worksheet/01_display_input_form.php'; ?>
                            
                        <!-- ==================== DISPLAY TALLY SCORE BOARD ==================== -->
                        
                            <?php require $_SERVER['DOCUMENT_ROOT'] . '/interactive_worksheet/03_display_tally_board_and_pie_chart.php'; ?>
                        
                        <!-- ==================== TIMER ==================== -->
                                
                            <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/timer/timer_00_complete.php'; ?>
                            
                        <!-- ==================== FIND HIGHEST WORKSHEET NUMBER MADE BY USER ==================== -->
                            
                            <?php READ_Get_Current_Worksheet_Number_of_User ( $Connection_Handle_GRADES ); ?>
                                    
                        <!-- ==================== DISPLAY INTERACTIVE WORKSHEET ==================== -->
                        
                            <?php require $_SERVER['DOCUMENT_ROOT'] . '/interactive_worksheet/05_display_worksheet.php'; ?>
    
                </div>
                
		<!-- ADD WORKSHEET MADE TO USER DATABASE -->
        
        	<?php
				if ( isset ( $_COOKIE['Time4Times_Cookie_ID'] ) ) {
					require $_SERVER['DOCUMENT_ROOT'] . '/includes/worksheets_add_one.php';
				}
			?>

        <!-- RIGHT COLUMN -->
        
                <div class="right" >
                    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/right_column.php'; ?>
                </div>
                
            </article>
            
        <!-- BOARD BOTTOM -->
        
            <div class="board_bottom" ></div>
            
        <!-- FOOTER -->
        
            <div class="footer" >
                <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
            </div>
            
    </body>
    
</html>

<?php ob_end_flush(); ?>
