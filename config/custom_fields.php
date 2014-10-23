<?php 
/*
|--------------------------------------------------------------------------
| Form builder 
|--------------------------------------------------------------------------
|
| Specify field types and other Form_builder configuration properties
| This file is included by the fuel/modules/fuel/config/form_builder.php file
*/

// date field
$fields['date'] = array(
	'css_class' => 'datepicker',
	'js_function' => 'fuel.fields.datetime_field',
	'js' => array(FORMS_FOLDER => array(
							'jquery-ui-1.8.17.custom.min.js',
							//'date_field'
							)
				),
	'css' => array(FORMS_FOLDER => array(
							'jquery-ui-1.8.17.custom.css',
						)
				)
);

$fields['antispam'] = array(
	'class'		=> array(FORMS_FOLDER => 'Forms_custom_fields'),
	'function'	=> 'antispam',
	'filepath'	=> '',
);

$fields['recaptcha'] = array(
	'class'		=> array(FORMS_FOLDER => 'Forms_custom_fields'),
	'function'	=> 'recaptcha',
	'filepath'	=> '',
);

$fields['equation'] = array(
	'class'		=> array(FORMS_FOLDER => 'Forms_custom_fields'),
	'function'	=> 'equation',
	'filepath'	=> '',
);

$fields['honeypot'] = array(
	'class'		=> array(FORMS_FOLDER => 'Forms_custom_fields'),
	'function'	=> 'honeypot',
	'filepath'	=> '',
);

$fields['akismet'] = array(
	'class'		=> array(FORMS_FOLDER => 'Forms_custom_fields'),
	'function'	=> 'akismet',
	'filepath'	=> '',
);

$fields['stopforumspam'] = array(
	'class'		=> array(FORMS_FOLDER => 'Forms_custom_fields'),
	'function'	=> 'stopforumspam',
	'filepath'	=> '',
);


/* End of file form_builder.php */
/* Location: ./application/config/form_builder.php */