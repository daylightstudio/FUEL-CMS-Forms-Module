$(function(){

	var beforeSubmit = function(formData, form, options){
		var $formMessages = $('.messages', form);
		var $form = $(form);
		$('input[type=submit]', form).prop('disabled', true);
		var ajaxMessage = unescape($form.data('ajax_message'));
		if (!ajaxMessage) ajaxMessage = '';
		$formMessages.html(ajaxMessage).show();
	}

	var submitSuccess = function(response, statusText, xhr, $form){
		var $formMessages = $('.messages', $form);
		$formMessages.removeClass('error');
		$formMessages.addClass('success');
		$formMessages.fadeIn();
		$formMessages.html(response);
		$('input[type=submit]', $form).prop('disabled', false);
	}

	var submitError = function(xhr, status, error, $form){
		var $formMessages = $('.messages', $form);
		$formMessages.removeClass('success');
		$formMessages.addClass('error');
		console.log(xhr.responseText)
		if (xhr.responseText !== '') {
			$formMessages.html(xhr.responseText);
		} else {
			$formMessages.text('Oops! An error occurred.');
		}
		$('input[type=submit]', $form).prop('disabled', false);
	}

	var formOptions = { 
		beforeSubmit: beforeSubmit, 
		success: submitSuccess, 
		error: submitError,
		clearForm: true
	}; 

	//**** SUBMIT ACTION ****
	var submitAction = function(form) {
   		$(form).ajaxSubmit(formOptions);
	}

	var isTrue = function(elem, key){
		return $(elem).data(key) && $(elem).data(key) != 'false' && $(elem).data(key) != '0';
	}

	// create messages node to insert messages back from the server if it doesn't exist
	$("form.form").each(function(){
		if (!$(this).find('.messages').length){
			$(this).find('input[type=submit]').parent().prepend('<div class="messages"></div>');
		}
	});

	// add date validation to each date type
	$("form.form").each(function(){

		$form = $(this);
		if (isTrue(this, 'validate')){

			if (!$(this).data('validateParams')){
				$(this).data('validateParams', {});
			}
			$(this).validate($(this).data('validateParams'));

		}

		if (isTrue(this, 'ajax')){
			if (isTrue(this, 'validate')){
				$(this).submit(function(){ if ($(this).valid()) submitAction(this); return false; });
			} else if (!isTrue(this, 'validate')){
				$(this).submit(function(){ submitAction(this); return false; });
			}
		}
		
	});


	if (isTrue(this, 'validate')){

		var extraValidators = {date: 'date', phone: 'phoneUS'};
			
		if (typeof $.fn.rules !== 'undefined'){

			for(var n in extraValidators){
				var func = extraValidators[n];
				$('form.form .' + n).each(function(){
					var r = {};
					r[func] = true;
					$form.rules('add', r);
				});
			}
		}
	}

})