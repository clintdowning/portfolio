// Provider List - JS for provider list search results page.

function uncheckAll (  ) {  // Uncheck all provider checkboxes.
	console.log("uncheckAll");
	$('input[type=checkbox]').removeAttr('checked');
	updateProviderCountMessage();
}

function updateProviderCountMessage (  ) {  // Update number of providers checked massage.
	console.log("updateProviderCountMessage");
	var count = countCheckedProviders();
	if ( count === 1 ) {
		$('.items_selected').html( count + ' Provider Selected' );
	} else {
		$('.items_selected').html( count + ' Providers Selected' );
	}
}

function countCheckedProviders (  ) {  // Count the number of providers that are currently checked.
	console.log("countCheckedProviders");
	var count = $('input:checked').length;
	if ( ! count ) {
		count = 0;
		changeAllButtons( 'disable' );
	}
	if ( count > 0 ) {
		changeAllButtons( 'enable' );
	}
	return count;
}

function changeAllButtons ( change_type ) {  // Change all button statuses to enabled or disabled.
	console.log("changeAllButtons");
	changeButton( change_type , '#uncheck_button' );
	changeButton( change_type , '#submit,#pre_submit' );
}

function changeButton ( change_type , button_id ) {  // Change button status to enabled or disabled.
	console.log("changeButton");
	if ( change_type === 'enable' ) {
		$(button_id).removeClass('inactive');
		$(button_id).addClass('active');
		$(button_id).prop('disabled', false);
	} else if ( change_type === 'disable' ) {
		$(button_id).removeClass('active');
		$(button_id).addClass('inactive');
		$(button_id).prop('disabled', true);
	}
}

function changeForEmail (  ) {
	console.log("changeForEmail");
	$('#email').removeClass("hidden");
	$('#email').animate({
		width: "300px"
	});
	if ( $('#email').val().length === 0 ) {  // Only red alert color if field is not prefilled.
		$('#email').addClass("alert"); // Red color email field to be filled in.
	}
	$('#pre_submit').removeClass("visible_inline").addClass("hidden"); // Hide original 'Get Quotes' button.
	$('#submit').removeClass("hidden").addClass("visible_inline"); // Show 'Get Quotes' submit button.
	$('.privacy_policy').css("display","block"); // Show 'Privacy Policy' Statement.
}

function clearAlert (  ) {
	console.log("clearAlert");
	var email_text_len = $('#email').val().length;
	if ( email_text_len > 0 ) {
		$('#email').removeClass("alert");
	} else {
		$('#email').addClass("alert");
	}
}

function updateFormPostZip ( text_field_object , page_region ) {
	var form_id = '#search_zip_top';
	if ( page_region === 'bottom' ) {
		form_id = '#search_zip_bottom';
	}
	var current_action_value = $(form_id).attr("action");  // Current form action url.
	var url_zip_position = current_action_value.search("zip=")+4;  // Zip data position in current form action url.
	var reseted_action_value = current_action_value.substring(0,url_zip_position);  // Form action url reset with no zip.
	var new_action_value = reseted_action_value + $(text_field_object).val();  // Form action url with zip data.
	$(form_id).attr("action",new_action_value);  // Forma action attribute value replaced with updated zip data.
}

// Listeners

$(document).ready(function() {
	
	$('input[type=checkbox]').click(function(){  // Click on checkbox.
		updateProviderCountMessage();
	});
	
	$('#uncheck_button').click(function(){  // Click on "Uncheck All" button.
		uncheckAll();
	});
	
	$('#client_zip_top,#provider_zip_top').keyup(function(){  // Inject searched zip into form post value - On Page Top.
		updateFormPostZip ( this , 'top' );
	});
	
	$('#client_zip_bottom').keyup(function(){  // Inject searched zip into form post value - On Page Bottom.
		updateFormPostZip ( this , 'bottom' );
	});
	
	$('#email').keyup(clearAlert);  // Change format of email field if empty or not empty.
	
});

