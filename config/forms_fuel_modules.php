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
	'default_col' => 'date_added',
	'default_order' => 'desc',
	'filters' => array(
					'form_name' => array('type' => 'select', 'label' => 'Forms', 'model' => array(FORMS_FOLDER => array('forms_model' => 'forms')), 'first_option' => lang('label_select_one')),
					'is_spam' => array('type' => 'select', 'options' => array('no' => 'no', 'yes' => 'yes'), 'first_option' => lang('label_select_one'))

				)

);
