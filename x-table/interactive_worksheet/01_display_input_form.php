    <div class="formData" >
    
        <form action="_Multiplication_Worksheets_Interactive_02.php#generator" method="post" >
        
            <!-- WHAT NUMBERS TO PRACTICE? -->
    
                <table class="groupTogether" >
                    <tr>
                        <td class="strong" colspan="1" >
                            1) Groups?
                        </td>
                        <td class="small_italics" colspan="2" >
                            What Multiplication Groups Do You Want To Practice?
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" >
                            <table style="margin: 0 auto;" >
                                <?php
                                    for ( $_i = 0 ; $_i < 12; $_i++ )
                                    {
                                        echo "<tr>";
                                        for ( $_j = 1 ; $_j <= 7 ; $_j++ )
                                        {
                                            echo "<td class='multipleSelectionGrouper' >";
                                            echo $_i . "'s" . "<br/>";
                                            echo "<input type='checkbox' name='checkbox"  . $_i . "' value=" . $_i . " />";
                                            echo "</td>";
                                            if ( $_i == 6 || $_i == 12 )
                                            {
                                                break;
                                            }
                                            $_i++;
                                        }
                                        echo "</tr>";
                                    }
                                ?>
                            </table>
                        </td>
                    </tr>
                </table>
    
            <!-- NUMBER OF PROBLEMS -->
    
                <table class="groupTogether" >
                    <tr>
                        <td class="strong">
                            2) Problems?
                        </td>
                        <td class="small_italics">
                            How Many Problems Do You Want to Make?
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' >
                        	<table style="margin: 0 auto;" >
                        		<tr>
                                	<td>
                                        <select name="numProblems" style="background-color: rgba(255,255,153,1); width: 185px;" >
                                            <option value=0 selected="selected">Select...</option>
                                            <?php
                                                for ( $_i = 1 ; $_i <=100 ; $_i++ )
                                                {
                                                    echo "<option value='" . $_i . "' >" . $_i . "</option>";
                                                }
                                            ?>
                                        </select>
	                            	</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            <!-- SUBMIT -->
    
                <table style="margin: 0 auto;" >
                    <tr>
                        <td style="text-align:center;" >
                        
                            <input type="submit" class="Make_Interactive_Worksheet_Button_ID" value="Make Worksheet!" onclick="" />
                            
                        </td>
                    </tr>
                </table>
                
            <!-- BUTTON TO PRINTER FRIENDLY VERSION -->
    
                <table style="margin: 0 auto;" >
                    <tr>
                        <td style="text-align:center;" >
                        
							<?php
                                Glass_Hover_Link_To_Button
                                (
                                    'Printer Friendly Version'								,
                                    '_Multiplication_Worksheets_Printable.php'				,
                                    250														,
                                    30														,
                                    20														,
									'target="_blank"'														,
									"onclick=\"window.open('_Multiplication_Worksheets_Printable.php')\""
                                );
                            ?>
                            
                        </td>
                    </tr>
                </table>
                    
        </form>
    
    </div>
