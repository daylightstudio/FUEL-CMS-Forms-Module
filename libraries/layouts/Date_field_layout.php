<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Base_field_layout.php');

class Date_field_layout extends Base_field_layout {

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
		$validator->add_rule($name, 'valid_date', 'Please enter in a valid date');
		return $validator;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the layout's fields
	 *
	 * @access	public
	 * @return	array
	 */
	public function fields()
	{
		$fields = parent::fields();
		$fields['format'] = array('type' => 'select', 'options' => array('m/d/Y' => 'm/d/Y', 'Y-m-d' => 'Y-m-d', 'd-mm-Y' => 'd-m-Y'));
		$fields['min_date'] = array('type' => 'date');
		$fields['max_date'] = array('type' => 'date');
		$fields = $this->process_fields($fields);
		return $fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an array for Form_builder to use in rendering
	 *
	 * @access	public
	 * @param	array 	Parameters for rendering
	 * @return	array
	 */	
	function frontend_render($field_model, $create = FALSE)
	{

		$field = $field_model->values();
		$field['type'] = 'date';
		$field['class'] = 'date';
		
		// may remove if we ever decide to add it as a configuration parameter
		$field['js'] = '<script>imgPath = "'.img_path('', FUEL_FOLDER).'";</script>';
		$field['show_on'] = 'focus';

		return $field;
	}

}