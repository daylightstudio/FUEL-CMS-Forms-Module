<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Base_field_layout.php');

class Phone_field_layout extends Base_field_layout {

	public $group = 'Forms';

	// --------------------------------------------------------------------

	/**
	 * A front end validation function to run for this rendered field (not to be confused with the backend "validate method")
	 *
	 * @access	public
	 * @param	mixed 	The value to validate
	 * @return	object
	 */	
	function frontend_validation($value)
	{
		$validator = $this->get_validator();
		$validator->add_rule($name, 'valid_phone', 'Please enter in a valid phone number');
		return $validator;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an array for Form_builder to use in rendering
	 *
	 * @access	public
	 * @param	array 	Parameters for rendering
	 * @return	array
	 */	
	function frontend_render($field_model)
	{
		$field = $field_model->values();
		$field['size'] = 20;
		$field['class'] = 'phone';
		return $field;
	}
}