
/* Sign-Up - Sign-In Toggle */

	$(".login_modal .login-form h2 .sign_up").click(function(){  // Show Sign-Up Form
		$(this).addClass("live");  // Button - Sign-Up - Show
		$(".login_modal .login-form h2 .sign_in").removeClass("live");  // Button - Sign-In - Hide
		$(".login_modal .forms .sign_up").addClass("live");  // Form - Sign-Up - Show
		$(".login_modal .forms .sign_in").removeClass("live");  // Form - Sign-In - Hide
	});

	$(".login_modal .login-form h2 .sign_in").click(function(){  // Show Sign-In Form
		$(this).addClass("live");  // Button - Sign-In
		$(".login_modal .login-form h2 .sign_up").removeClass("live");  // Button - Sign-Up - Hide
		$(".login_modal .forms .sign_in").addClass("live");  // Form - Sign-In - Show
		$(".login_modal .forms .sign_up").removeClass("live");  // Form - Sign-Up - Hide
	});

/* Hide (Close) Modal */

	$('#playlist_user_top_nav.logged_out').click(function(){
		displayModal();
	});

	function displayModal (  ) {
		$(".login_modal").css("display","block");
	}

	function hideModal (  ) {
		$(".login_modal").css("display","none");
	}

	$("#modal_close").click(function(){  // Hide (Close) modal - On "X" click.
		hideModal();
	});

	$('*').click(function (event) {  // Hide (Close) modal - On "Outside" click.
		var not_inside_modal = ! $(event.target).closest('.login-form-wrap').length ? true : false ;		// Clicked spot is not inside modal.
		var not_modal = ! $(event.target).is('.login-form-wrap') ? true : false ;							// Clicked spot is not modal.
		var not_inside_add_to_playlist = ! $(event.target).closest('.oval.saved').length ? true : false ;	// Clicked spot is not inside "Add to Playlist".
		var not_add_to_playlist = ! $(event.target).is('.oval.saved') ? true : false ;						// Clicked spot is not "Add to Playlist".
		var login_nav_clicked = $(event.target).is('#playlist_user_top_nav.logged_out') ? true : false ;	// Clicked spot is "Nav Login".
		if ( not_inside_modal && not_modal && not_inside_add_to_playlist && not_add_to_playlist && ! login_nav_clicked ) {
			hideModal();
		}
	});

/* Validation */

	function hasValidPatternError ( input_target ) {console.log("");
		var pattern = $(input_target).attr("pattern");
		var is_valid_pattern = $(input_target).val().search(pattern) >= 0;
		var has_valid_pattern_error = ( ! is_valid_pattern ) ? true : false ;
		return has_valid_pattern_error;
	}
	
	function checkBlurError ( event , target_selector ) {
		if ( hasValidPatternError(event) ) {
			$( target_selector + " .invalid_pattern" ).css("display", "block");
			$(target_selector).fadeIn();
		} else {
			$(target_selector).fadeOut();
		}
	}

	function checkEmailUsableError ( email ) {
		var get_request = "/_js/ajax/email-exists.php?email=" + email;
		$.get ( get_request , function ( data ) {
			if ( data ) {
				if ( data.search('true') > -1 ) {  // Email exists.
					$('.sign_up .exists').css("display","block");
					$('.sign_up .email_error').fadeIn();
				}
			}
		});
	}

	function checkEmailExistsError ( email ) {
		var get_request = "/_js/ajax/email-exists.php?email=" + email;
		$.get ( get_request , function ( data ) {
			if ( data ) {
				if ( data.search('true') === -1 ) {  // Email does not exist.
					$('.sign_in .not_exist').css("display","block");
					$('.sign_in .email_error').fadeIn();
					submitButtonState( 'sign_in' , 'disable' );
				}
			}
		});
	}

	function hideAllErrors (  ) {
		$(".error").css("display","none");
		$(".error p").css("display","none");
	}

	function submitButtonState ( button , state ) {
		if ( button === 'sign_up' ) {
			if ( state === 'enable' ) {

			} else if ( state === 'disable' ) {
				
			}
		} else if ( button === 'sign_in' ) {
			if ( state === 'enable' ) {

			} else if ( state === 'disable' ) {
				$('#sign_in_submit').addClass('disabled');
			}
		}
	}
	
	$("#sign_up_email").blur(function(){
		hideAllErrors();
		checkBlurError( this , ".sign_up .email_error" );
		checkEmailUsableError( $(this).val() );
	});

	$("#sign_up_password").blur(function(){
		checkBlurError( this , ".sign_up .password_error" );
	});

	$("#sign_in_email").blur(function(){
		checkBlurError( this , ".sign_in .email_error" );
		checkEmailExistsError( $(this).val() );
	});
	
/* Submit */

	$("form.sign_up").submit(function(){
		return ( ! hasValidPatternError("#sign_up_email") && ! hasValidPatternError("#sign_up_password") ) ? true : false ;
	});

	$("form.sign_in").submit(function(){
		return ( ! hasValidPatternError("#sign_in_email") ) ? true : false ;
	});

