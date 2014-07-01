<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Abstract_select_field_layout.php');

class Enum_field_layout extends Abstract_select_field_layout {

	public $group = 'Forms';

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
		$field['type'] = 'enum';
		return $field;
	}

}