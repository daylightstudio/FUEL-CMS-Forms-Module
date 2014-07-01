<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Base_field_layout.php');

abstract class Abstract_select_field_layout extends Base_field_layout {

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
		$fields['options'] = array('type' => 'keyval', 'after_html'=> "<br>To add select options, put in values separated by a colon like so 'value:label'", 'allow_numeric_indexes' => TRUE);
		$fields = $this->process_fields($fields);
		return $fields;
	}

}