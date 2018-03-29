<?php 

$config['modules']['forms'] = array(
	'preview_path' => '',
	'model_location' => 'forms',
	'sanitize_input' => array('template','php'),
	'js_controller' => 'FormsController',
	'js_controller_path' => js_path('', FORMS_FOLDER),
);

$config['modules']['form_entries'] = array(
	'preview_path' => '',
	'model_location' => 'forms',
	'item_actions' => array('save', 'view', 'delete'),
	'exportable' => TRUE,
	'clear_cache_on_save' => FALSE,
	'default_col' => 'date_added',
	'default_order' => 'desc',
	'filters' => array(
					'form_name' => array('type' => 'select', 'label' => 'Forms', 'model' => array(FORMS_FOLDER => array('forms_model' => 'forms')), 'first_option' => lang('label_select_one')),
					'is_spam' => array('type' => 'select', 'options' => array('no' => 'no', 'yes' => 'yes'), 'first_option' => lang('label_select_one'))

				)

);
