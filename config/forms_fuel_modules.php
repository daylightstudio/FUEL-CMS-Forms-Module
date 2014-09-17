<?php 

$config['modules']['forms'] = array(
	'preview_path' => '', // put in the preview path on the site e.g products/{slug}
	'model_location' => 'forms', // put in the advanced module name here
	'sanitize_input' => array('template','php'),
);

$config['modules']['form_entries'] = array(
	'preview_path' => '', // put in the preview path on the site e.g products/{slug}
	'model_location' => 'forms', // put in the advanced module name here
	'displayonly' => TRUE,
	'item_actions' => array(),
	'table_actions' => array(),
	'exportable' => TRUE,
	'rows_selectable' => FALSE,
	'clear_cache_on_save' => FALSE,
	'filters' => array(
					'form_id' => array('type' => 'select', 'label' => 'Forms', 'model' => array(FUEL_FOLDER => 'forms_model'), 'first_option' => 'Select a form...')

				)

);
