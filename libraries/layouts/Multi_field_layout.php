<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Abstract_select_field_layout.php');

class Multi_field_layout extends Abstract_select_field_layout {

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
		$fields['mode'] = array('type' => 'enum', 'options' => array('checkbox' => 'checkbox', 'select' => 'select'));
		$fields = $this->process_fields($fields);
		return $fields;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an array for Form_builder to use in rendering\
	 *
	 * @access	public
	 * @param	array 	Parameters for rendering
	 * @return	array
	 */	
	function frontend_render($field_model)
	{
		$field = parent::frontend_render($field_model);
		$field['type'] = 'multi';
		return $field;
	}

}