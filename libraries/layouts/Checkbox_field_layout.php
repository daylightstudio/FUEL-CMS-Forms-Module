<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Base_field_layout.php');

class Checkbox_field_layout extends Base_field_layout {

	public $group = 'Forms';

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
		unset($fields['required'], $fields['attributes']);
		return $fields;
	}
}