	var Total_Right_Answers = 0;
	var Total_Wrong_Answers = 0;
	var Total_Answered      = 0;
	var Percent_Right		= 100;
	var Percent_Wrong		= 0;
	
	function Update_Grade ( Total_Right_Answers_In_Func , Total_Wrong_Answers_In_Func , Total_Answered_In_Func )
	{
		Percent_Right = Total_Right_Answers_In_Func / Total_Answered_In_Func * 100;
		Percent_Wrong = Total_Wrong_Answers_In_Func / Total_Answered_In_Func * 100;
		
		document.getElementById("Total_Right_Answers").innerHTML = Total_Right_Answers_In_Func;
		document.getElementById("Percent_Right_Upper").innerHTML = parseInt ( Percent_Right );
		
		document.getElementById("Total_Wrong_Answers").innerHTML = Total_Wrong_Answers;
		document.getElementById("Percent_Wrong").innerHTML = 100-parseInt ( Percent_Right );
		
		document.getElementById("Total_Answered").innerHTML = Total_Answered;
		document.getElementById("Percent_Right_Lower").innerHTML = parseInt ( Percent_Right );
		
		Draw_Pie_Chart ( Percent_Right );
	}
	
	function Draw_Pie_Chart ( Percent_Right )
	{
		var data = [
				['Right', parseInt ( Percent_Right )],['Wrong', 100 - parseInt ( Percent_Right )]
		];
		var plot1 = jQuery.jqplot ('Pie_Chart', [data], 
		{ 
			seriesDefaults: {
				// Make this a pie chart.
				renderer: jQuery.jqplot.PieRenderer, 
				rendererOptions: {
					// Put data labels on the pie slices.
					// By default, labels show the percentage of the slice.
					showDataLabels: true
				}
			}, 
			seriesColors: [ "lime", "red"],
			grid: { background: "transparent" },
			legend: { show:false, location: 'e' }
		}
		);
	}
	
	$(document).ready(function(){
		Draw_Pie_Chart ( Percent_Right );
	});
