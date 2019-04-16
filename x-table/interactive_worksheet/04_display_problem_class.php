	<?php
        class DisplayProblemClass
        {
            public function displayProblem( $id , $a , $b , $c )
            {
                // WRITE PROBLEM DATA TABLE STRUCTURE
                    
					echo
					"
						<td>
							<table class='problemTableFormat' >
								<tr>
								
									<!-- PROBLEM ID NUMBER  -->
									
										<td class='problemID' >
											" . $id . ")
										</td>
										
									<!-- TOP MULTIPLIER -->
									
										<td class='colRight' >
											" . $a . "
										</td>
										
								</tr>
								<tr>
								
									<!-- X SYMBOL -->
									
										<td class='colLeft drawLine' >
											x
										</td>
										
									<!-- BOTTOM MULTIPLIER -->
									
										<td class='colRight drawLine' >
											" . $b . "
										</td>
										
								</tr>
								<tr>
								
									<!-- RED X GIF -->
								
										<td
											class=	'colLeft'
											style=	'visibility:hidden;'
											id=		'answeredID_" . $id . "_RedX'
										>
											<img
												alt='Multiplication-Worksheet-Red-X'
												src='assets/Red_X.gif'
											>
										</td>
										
									<!-- INPUT FIELD -->
										
										<td class='colRight' >
											<input
												type=		'text'
												id=			'answeredID_Believed_" . $id . "'
												name=		'answeredID_Believed_" . $id . "'
												onblur=		'checkAnswer_" . $id . "_(" . $c . ")'
												size=		'2'
												style=		'background-color:#FF9;'
												onchange=	'doTimer()'
											>
										</td>
									
								</tr>
								<tr>
								
									<!-- GREEN CHECKMARK -->
									
										<td
											class=	'colLeft'
											style=	'visibility: hidden;'
											id=		'answeredID_" . $id . "_GreenCheckmark'
										>
											<img
												alt='Multiplication-Worksheet-Green-Checkmark'
												src='assets/Green_Checkmark.gif'
											>
										</td>
										
									<!-- CORRECT ANSWER -->
										
										<td
											class='colCenter'
											style=
											'
												visibility:		hidden;
												color:			green;
												font-weight:	bold;
												font-size:		20px;
											'
											id='answeredID_" . $id . "_CorrectAnswer'
										>
											" . $c . "
										</td>
									
								</tr>
							</table>
						
                    
                <!-- CHECK ANSWER AND SHOW OR HIDE ELEMENTS -->

							<script language='javascript'>
								var answerBelieved" . $id . "
								function checkAnswer_" . $id . "_( answerActual )
								{
									var answerBelieved" . $id . " = document.forms['problamMatrixForm']['answeredID_Believed_" . $id . "'].value;
									if ( answerBelieved" . $id . " == answerActual )
									{
										document.getElementById('answeredID_" . $id . "_RedX').style.visibility = 'hidden';
										document.getElementById('answeredID_" . $id . "_GreenCheckmark').style.visibility = 'visible';
										document.getElementById('answeredID_" . $id . "_CorrectAnswer').style.visibility = 'hidden';
										Total_Right_Answers++;
										Total_Answered++;
										Update_Grade ( Total_Right_Answers , Total_Wrong_Answers , Total_Answered );
										document.getElementById('answeredID_Believed_" . $id . "').style.backgroundColor = 'Green';
									}
									else
									{
										document.getElementById('answeredID_" . $id . "_RedX').style.visibility = 'visible';
										document.getElementById('answeredID_" . $id . "_GreenCheckmark').style.visibility = 'hidden';
										document.getElementById('answeredID_" . $id . "_CorrectAnswer').style.visibility = 'visible';
										Total_Wrong_Answers++;
										Total_Answered++;
										Update_Grade ( Total_Right_Answers , Total_Wrong_Answers , Total_Answered );
										document.getElementById('answeredID_Believed_" . $id . "').style.backgroundColor = 'Red';
									}
									document.getElementById('answeredID_Believed_" . $id . "').readOnly = 'readonly';
								}
							</script>
						</td>
					";
					
				// PASS PROBLEM INSTANCE DATA VIA SESSION ARRAY
				
					$_SESSION[ 'Problem_Instance_A_' . $id ] = $a;
					$_SESSION[ 'Problem_Instance_B_' . $id ] = $b;
            }
        }
    ?>
