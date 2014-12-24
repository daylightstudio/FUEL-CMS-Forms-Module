<?php
/*
|--------------------------------------------------------------------------
| MY Custom Layouts
|--------------------------------------------------------------------------
|
| specify the name of the layouts and their fields associated with them
*/


/*
|--------------------------------------------------------------------------
| BLOCK LAYOUTS
|--------------------------------------------------------------------------
*/

// SECTIONS
$config['blocks']['text'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['email'] = array(
	'group' => 'Forms',
	'class'    => 'Email_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['textarea'] = array(
	'group' => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['select'] = array(
	'group'    => 'Forms',
	'class'    => 'Select_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['checkbox'] = array(
	'group'    => 'Forms',
	'class'    => 'Checkbox_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
);

$config['blocks']['enum'] = array(
	'group'    => 'Forms',
	'class'    => 'Enum_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
	'label'    => 'enum',
	// 'fields'   => array(
	// 					'options' => array('type' => 'keyval', 'after_html'=> "<br>To add select options, put in values separated by a colon like so 'value:label'", 'allow_numeric_indexes' => TRUE))
);
$config['blocks']['multi'] = array(
	'group'    => 'Forms',
	'class'    => 'Multi_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
	// 'fields'   => array(
	// 					'options' => array('type' => 'keyval', 'after_html'=> "<br>To add select options, put in values separated by a colon like so 'value:label'", 'allow_numeric_indexes' => TRUE))
);

$config['blocks']['number'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['password'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['phone'] = array(
	'group'    => 'Forms',
	'class'    => 'Phone_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['date'] = array(
	'group'    => 'Forms',
	'class'    => 'Date_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['antispam'] = array(
	'group'    => 'Forms',
	'class'    => 'Antispam_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['hidden'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['file'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['section'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['fieldset'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['copy'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

$config['blocks']['phone'] = array(
	'group'    => 'Forms',
	'class'    => 'Base_field_layout',
	'filepath' => 'libraries/layouts',
	'module'   => 'forms',
	'model'    => array('forms' => 'forms_model'),
);

/* End of file MY_fuellayouts.php */
/* Location: ./application/config/MY_fuellayouts.php */