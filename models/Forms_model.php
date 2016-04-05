<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Forms_model extends Base_module_model {

	// read more about models in the user guide to get a list of all properties. Below is a subset of the most common:

	public $record_class = 'Form'; // the name of the record class (if it can't be determined)
	public $filters = array(); // filters to apply to when searching for items
	public $auto_validate_fields = array(); // fields to auto validate
	public $required = array('name'); // an array of required fields. If a key => val is provided, the key is name of the field and the value is the error message to display
	public $foreign_keys = array(); // map foreign keys to table models
	public $linked_fields = array('slug' => 'name'); // fields that are linked meaning one value helps to determine another. Key is the field, value is a function name to transform it. (e.g. array('slug' => 'title'), or array('slug' => arry('name' => 'strtolower')));
	public $boolean_fields = array(); // fields that are tinyint and should be treated as boolean
	public $unique_fields = array(); // fields that are not IDs but are unique. Can also be an array of arrays for compound keys
	public $parsed_fields = array('form_html', 'email_message'); // fields to automatically parse
	public $serialized_fields = array('inputs', 'anti_spam_method'); // fields that contain serialized data. This will automatically serialize before saving and unserialize data upon retrieving
	public $has_many = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $belongs_to = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $formatters = array(); // an array of helper formatter functions related to a specific field type (e.g. string, datetime, number), or name (e.g. title, content) that can augment field results
	public $display_unpublished_if_logged_in = FALSE;
	
	protected $friendly_name = ''; // a friendlier name of the group of objects
	protected $singular_name = ''; // a friendly singular name of the object


	public function __construct()
	{
		parent::__construct('forms', FORMS_FOLDER); // table name
		$CI =& get_instance();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Placeholder for return data that appears in the right side when editing a record (e.g. Related Navigation in pages module )
	 *
	 * @access	public
	 * @param	array View variable data (optional)
	 * @return	mixed Can be an array of items or a string value
	 */	
	public function related_items($params = array())
	{
		return lang('forms_view_user_guide');
	}

	public function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'desc', $just_count = FALSE)
	{
		$this->db->select('id, name, slug, published');
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		return $data;
	}

	public function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);


		// general
		$fields['General'] = array('type' => 'fieldset', 'order' => 1, 'class' => 'tab');
		$fields['name']['order'] = 2;
		$fields['slug']['order'] = 3;
		$fields['save_entries']['order'] = 4;
		$fields['published']['order'] = 6;


		// form
		$fields['Form'] = array('type' => 'fieldset', 'order' => 100, 'class' => 'tab');
		$fields['form_display'] = array('type' => 'toggler', 'prefix' => 'toggle_', 'order' => 102, 'options' => array('auto' => 'auto', 'block' => 'block', 'html' => 'html'), 'comment' => 'Select which method you\'d like to use to render the form');

		$block_view_module = (!empty($values['block_view_module'])) ? $values['block_view_module'] : '';
		$block_view_module_field = array('name' => 'block_view_module', 'type' => 'select', 'options' => $this->fuel->modules->options_list(TRUE), 'first_option' => 'application', 'value' => $block_view_module);
		$module_view = $this->form_builder->create_select($block_view_module_field);
		$fields['block_view']['comment'] = 'The view file used to render the form. If no value is provided, then the form will be automatically generated. More information on the templating syntax and variables that get passed to the HTML and view file can be found in the documentation.';
		$fields['block_view']['after_html'] = ' in the '.$module_view.' module';
		$fields['block_view']['order'] = 103;
		$fields['block_view']['class'] = 'toggle toggle_block';
		$fields['form_html'] = array('type' => 'textarea', 'class' => 'toggle toggle_html', 'order' => 104, 'label' => 'Form HTML', 'comment' => 'Insert HTML code for your form. More information on the templating syntax and variables that get passed to the HTML and view file can be found in the documentation.');
		$fields['anti_spam_method'] = array('type' => 'block', 'block_name' => 'antispam', 'order' => 105, 'display_label' => FALSE, 'label' => 'Anti SPAM method', 'module' => FORMS_FOLDER);
		$fields['submit_button_text']['order'] = 106;
		$fields['reset_button_text']['order'] = 107;


		// fields
		$fields['Fields'] = array('type' => 'fieldset', 'order' => 200, 'class' => 'tab');
		$fields['inputs'] = array(
					'order' => 201,
					'display_label' => FALSE,
					'type'          => 'template', 
					'title_field'   => 'label',
					'fields'        => array(
											'section' => array('type' => 'section', 'value' => '__title__'),
											'name' => array('required' => TRUE, 'comment' => 'The input field name (not to be confused with the field label).'),
											'label' => array('comment' => 'If left blank, one will be added for you based on the above name value.'),
											'field' => array('label' => 'Field type', 'type' => 'block', 'group' => 'Forms'),
											//'attributes' => array(),
											//'required' => array('type' => 'checkbox'),
											//'options' => array('type' => 'keyval'),
											),
					'class'         => 'repeatable',
					'add_extra'     => FALSE,
					'repeatable'    => TRUE,
					'value'			=> array(
									array('name' => 'name', 'label' => 'Name', 'field' => array('block_name' => 'text'), 'required' => 1),
									array('name' => 'email', 'label' => 'Email', 'field' => array('block_name' => 'email'), 'required' => 1),

						),
				);

		// fields
		$yes_no_options = array('yes' => 'yes', 'no' => 'no');
		$fields['Javascript'] = array('type' => 'fieldset', 'order' => 300, 'class' => 'tab');
		$fields['javascript_info'] = array('type' => 'copy', 'order' => 301, 'value' => 'Learn more about the jquery plugin used for javascript validation at <a href="http://jqueryvalidation.org/validate" target="blank">jqueryvalidation.org/validate</a>');
		$fields['javascript_submit'] = array('type' => 'enum', 'options' => $yes_no_options, 'order' => 302, 'comment' => 'Will submit the form via an AJAX post');
		$fields['javascript_validate'] = array('type' => 'enum', 'options' => $yes_no_options, 'order' => 303, 'comment' => 'Will run javascript validation before submission. More can be found here: http://jqueryvalidation.org/validate');
		$fields['javascript_waiting_message'] = array('order' => 305, 'value' => 'Sending...', 'comment' => 'The message to display while the form is being processed.');

		// after post
		$fields['After Submit'] = array('type' => 'fieldset', 'order' => 400, 'class' => 'tab');
		$fields['form_action'] = array('order' => 401, 'comment' => 'This field is irrevelant if the view contains the form tag and action. If no action is provided, the form will submit to itself.');
		$fields['after_submit_text']['order'] = 402;
		$fields['email_recipients']['order'] = 403;
		$fields['email_cc'] = array('label' => 'CC recipients', 'order' => 404);
		$fields['email_bcc'] = array('label' => 'BCC recipients', 'order' => 405);
		$fields['email_subject']['order'] = 406;
		$fields['email_message'] = array('type' => 'textarea', 'order' => 407, 'class' => 'no_editor', 'style' => 50);
		$fields['return_url'] = array('label' => 'Return URL', 'order' => 408);
		

		// remove unused
		unset($fields['block_view_module']);
		return $fields;
	}
	
	// added here to make it easier for the filter
	public function forms($type = null)
	{
		$CI =& get_instance();
		return $CI->fuel->forms->options_list($type);
	}

	public function on_before_save($values)
	{
		parent::on_before_save($values);

		if (!empty($values['email_recipients']))
		{
			$contacts = preg_split('#,\s#', $values['email_recipients']);

			foreach($contacts as $contact)
			{
				if (!valid_email($contact))
				{
					$this->add_error(lang('forms_invalid_email_address'));
				}
			}
		}
		return $values;
	}

	public function on_after_save($values)
	{
		parent::on_after_save($values);
		return $values;
	}

	public function _common_query($display_unpublished_if_logged_in = NULL)
	{
		parent::_common_query($display_unpublished_if_logged_in);
	}

}

class Form_model extends Base_module_record {

	public function get_form_fields($field = NULL)
	{

		$fields = array();
		if (!empty($this->inputs))
		{
			foreach($this->inputs as $key => $input)
			{
				if (is_array($input))
				{
					if (isset($input['field']['block_name']))
					{
						$input['type'] = $input['field']['block_name'];
					}
					elseif(empty($input['type']))
					{
						$input['type'] = 'text';
					}
					
					$fields[$input['name']] = new Form_field();

					// merge in extra properties
					if (!empty($input['field']))
					{
						if (is_array($input['field']))
						{
							$input = array_merge($input, $input['field']);
						}
						unset($input['field']);
					}

					// decode json strings automatically
					foreach($input as $k => $v)
					{
						if (is_json_str($v))
						{
							$input[$k] = json_decode($v, TRUE);
						}
					}
					
					$fields[$input['name']]->initialize($input);
				}
				elseif ($input instanceof Form_field)
				{
					$fields[$key] = $input;
				}
			}
		}
		
		if (!empty($field))
		{
			if (array_key_exists($field, $fields))
			{
				return $fields[$field];	
			}
			return FALSE;
		}
		return $fields;
	}

}
