<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Base_field_layout extends Fuel_block_layout {

	public $group = 'Forms';

	protected $validator = NULL; // the validator object
	protected $submitted_values = array(); // submitted values

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
		$fields['required'] = array('type' => 'checkbox', 'value' => 1);
		$fields['attributes'] = array();
		$fields = $this->process_fields($fields);
		return $fields;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
	{
		parent::__construct();
		$this->CI->load->library('validator');
		$this->CI->load->library('form_builder');

		$this->set_validator($this->CI->validator);
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the validator object used for validating the front end
	 *
	 * @access	public
	 * @param	mixed 	The validator object
	 * @return	boolean
	 */	
	public function set_validator($validator)
	{
		$this->validator =& $validator;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the validator object used for validating the front end
	 *
	 * @access	public
	 * @return	object
	 */	
	public function &get_validator()
	{
		return $this->validator;
	}
	
	// --------------------------------------------------------------------

	/**
	 * A front end validation function to run for this rendered field (not to be confused with the backend "validate method")
	 *
	 * @access	public
	 * @param	mixed 	The value to validate
	 * @return	object
	 */	
	function frontend_validation($name)
	{
		return $this->validator;
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
		return $field;
	}
}