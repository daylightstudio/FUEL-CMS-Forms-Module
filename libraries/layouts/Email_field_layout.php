<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Base_field_layout.php');

class Email_field_layout extends Base_field_layout {

	public $group = 'Forms';

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
		$validator = $this->get_validator();
		$validator->add_rule($name, 'valid_email', 'Please enter in a valid email address');
		return $validator;
	}

}