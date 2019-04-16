
// SCROLL TO ANCHOR - Scrolls to target.

	function scrollToAnchor ( target ) {
		var offset = -20;
		var speed = 1500;
		var final_target = '\'' + target + '\'';
		$('html, body').animate({scrollTop: $(target).offset().top + offset }, speed );
		/*$('html, body').animate({scrollTop: $('#contact').offset().top -100 }, 'slow');*/
	}

// LOAD TRAILER - Load trailer into trailer target element.

	function loadTrailer ( target_element , trailer_key ) {
		$(target_element).html("<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/" + trailer_key + "?rel=0&autoplay=1\" frameborder=\"0\" allowfullscreen></iframe>");
	}
	
// LOAD AMAZON - Load amazon html.

	function loadAmazon ( amazon_asin ) {
		$( ".amazon_" + amazon_asin + " .ajax" ).load( "/js/ajax-pulls/amazon_affiliate.php?amazon_asin=" + amazon_asin );
	}
	
// MOVIE FOCUS - Change focus from current movie, to selected movie.

	function movieFocus ( selected_movie_id , trailer_key , amazon_asin ) {
		// Remove Previous Focus
			removeFocus( selected_movie_id , trailer_key );
		// Expand Movie
			// Remove Trailer Image Element
				$( "." + selected_movie_id + " .trailer_image_container" ).switchClass("not_hidden","hidden",100,"swing");
			// Inject trailer html
				loadTrailer ( "." + selected_movie_id + " .extra .video" , trailer_key );
			// Expand
				$( "." + selected_movie_id + " .extra" ).animate({height: "520px"});
			// Show Previously Hidden Elements
				$( "." + selected_movie_id + " .extra .hidden" ).switchClass("hidden","not_hidden",100,"swing");
			// Change "More..." Button to "Less..."
				// $( "." + selected_movie_id + " .more_less .more > span > span").html("Less...");
			// Change "basic" class into "full".
				$( "." + selected_movie_id ).switchClass("basic","full",100,"swing");
			// Remove onclick event on More_Less Button
				$( "." + selected_movie_id + " .more" ).attr("onclick","removeFocus('" + selected_movie_id + "' , '" + trailer_key + "' )");
			// Amazon - Load affiliate html.
				loadAmazon ( amazon_asin );
	}
	
// COLLAPSE FOCUSED MOVIE - Collapse movie currently in focus with loaded trailer.

	function removeFocus ( selected_movie_id , trailer_key ) {
		// Current Movie
			// Show Trailer Image Element
				$( ".trailer_image_container" ).switchClass("hidden","not_hidden",100,"swing");
			// Delete trailer html
				$(".full .extra .video").html("");
			// Collapse
				$(".full .extra").animate({height: "0px"});
			// Hide Other Elements Again
				$( ".full .extra .not_hidden" ).switchClass("not_hidden","hidden",100,"swing");
			// Change "Less..." Button to "More..."
				$(".full .more_less .more > span > span").html("More...");
			// Change removeFocua back into movieFocus
				$(".full .more").attr("onclick","movieFocus('" + selected_movie_id + "','" + trailer_key + "')");
			// Change "full" class into "basic".
				$(".full").switchClass("full","basic",100,"swing");
	}
