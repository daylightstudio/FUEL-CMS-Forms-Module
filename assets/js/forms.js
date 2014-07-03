$(function(){

	//**** SUBMIT ACTION ****
	var submitAction = function(form) {
		var $formMessages = $('.messages', form);
		var $form = $(form);
		var formData = $form.serialize();
		$('input[type=submit]', form).prop('disabled', true);
		var ajaxMessage = unescape($form.data('ajax_message'));
		if (!ajaxMessage) ajaxMessage = '';
   		$formMessages.html(ajaxMessage).show();

	   	$.ajax({
			type: 'POST',
			url: $form.attr('action'),
			data: formData
		}).done(function(response) {
			$formMessages.removeClass('error');
			$formMessages.addClass('success');
			$formMessages.fadeIn();

			$formMessages.html(response);

			$checkInputs = $('[type="checkbox"], [type="radio"]', form);
			$('input, textarea, select', form).not($checkInputs).not('[type="submit"],[type="button"],[type="reset"]').val('');
			$checkInputs.attr('checked', false)

			$('input[type=submit]', form).prop('disabled', false);

		}).fail(function(data) {
			$formMessages.removeClass('success');
			$formMessages.addClass('error');

			if (data.responseText !== '') {
				$formMessages.html(data.responseText);
			} else {
				$formMessages.text('Oops! An error occurred.');
			}
			$('input[type=submit]', form).prop('disabled', false);
		});
	}

	var isTrue = function(elem, key){
		return $(elem).data(key) && $(elem).data(key) != 'false' && $(elem).data(key) != '0';
	}

	// create messages node to insert messages back from the server if it doesn't exist
	$("form.form").each(function(){
		if (!$(this).find('.messages').length){
			$(this).find('input[type=submit]').parent().append('<div class="messages"></div>');
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

		var extraValidators = {date: 'date', phone: 'phoneUs'};
			
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