$(window).load(function(){

	//**** VALIDATION ****
	$.validator.addMethod(
		"dateFormat",
		function(value, elem) {
			if ($(elem).is('.date')){
				var format = $(elem).data('date_format');
				console.log(format)
				switch(format){

					case 'mm/dd/yyyy':
						return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
						break;
					case 'yy-mm-dd':
						return value.match(/^\d\d?\-\d\d?\-\d\d$/);					
						break;
					case 'yy-mm-dd':
						return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
						break;
					default:
						return true;

				}
			}
			return true;
		},
		"Please enter a date in the format dd/mm/yyyy."
	);

	$('form .date').each(function(){

		if (typeof $.fn.rules !== 'undefined' && $(this).is('.date')){
			$(this).rules('add', {date: true});
			//$(this).rules('add', {dateFormat: true});	
		}

	});

})