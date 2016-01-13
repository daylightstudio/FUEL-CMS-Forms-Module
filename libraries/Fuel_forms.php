<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 */

// ------------------------------------------------------------------------

/**
 * Fuel Forms object 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 */

// ------------------------------------------------------------------------

class Fuel_forms extends Fuel_advanced_module {
	
	public $name = "forms"; // the folder name of the module
	
	/**
	 * Constructor - Sets preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($params = array())
	{
		parent::__construct();

		$this->CI->load->library('validator');
		$this->CI->load->library('form_builder');
		$this->CI->load->module_helper(FORMS_FOLDER, 'forms');

		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the backup object
	 *
	 * Accepts an associative array as input, containing preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
		$this->set_params($this->_config);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Creates and returns a single Fuel_form object
	 *
	 <code>$form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE))));</code>
	 * 
	 * @access	public
	 * @param	string	Name of the form
	 * @param	array	Initialization parameters
	 * @return	object
	 */	
	public function create($name, $params = array())
	{
		$params['name'] = $name;

		if (empty($params['slug']))
		{
			$params['slug'] = url_title($name, '-', TRUE);	
		}

		$form = new Fuel_form();
		$form->initialize($params);
		if (isset($params['fields']))
		{
			$form->add_field($params['fields']);
		}
		return $form;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a single Fuel_form object
	 *
	 <code>$form = $this->fuel->forms->get('myform');</code>
	 *
	 * @access	public
	 * @param	string	Name of the form to retrieve. Will first look in the database and then in the config file
	 * @param	boolean	Determines whether to check the database or not
	 * @return	object
	 */	
	public function get($name, $check_db = TRUE)
	{
		$form = NULL;
		$params = array();

		// check the page mode to see if we can query the database
		if ($this->fuel->pages->mode() != 'views' AND $check_db)
		{
			if (!isset($this->CI->db))
			{
				$this->CI->load->database();
			}

			if ($this->CI->db->table_exists('forms')){
				$forms_model = $this->model('forms');

				// additional check that their are correct fields for returning the form since it's a pretty generic name for a table
				$fields = $forms_model->fields();
				if (in_array('name', $fields) AND in_array('slug', $fields))
				{
					$forms_model->db()->where(array('name' => $name));
					$forms_model->db()->or_where(array('slug' => $name));
					if (is_int($name))
					{
						$forms_model->db()->or_where(array('id' => $name));	
					}
					$form_data = $forms_model->find_one();

					if (isset($form_data->id))
					{
						// prep values for initialization
						$params = $form_data->values(TRUE);
						$params['fields'] = $form_data->get_form_fields();
					}
				}
			}
		}
		// next check the configuration to see if there are any declared
		if (empty($form_data))
		{
			$forms = $this->config('forms');
			if (isset($forms[$name]))
			{
				$params = $forms[$name];

				// set the name of the module
				if (!empty($forms[$name]['name']))
				{
					$name = $forms[$name]['name'];
				}
			}
			else
			{
				return FALSE;
			}
		}

		$form = $this->create($name, $params);
		return $form;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a Fuel_block_layout object used in the CMS for creating fields in association with different field types (e.g. text, email, select... etc)
	 *
	 <code>$block_layout = $this->fuel->forms->field_layout('email');</code>
	 *
	 * @access	public
	 * @param	string	Name of the block layout to retrieve
	 * @return	object
	 */	
	public function field_layout($layout)
	{
		return $this->fuel->layouts->get($layout, 'block');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns all the entries for a given form
	 *
	 * @access	public
	 * @param	string start date
	 * @param	string end date
	 * @return	array
	 */	
	public function all_entries_by_date($start_date, $end_date)
	{
		$this->load_model('form_entries');
		$this->form_entries_model->db()->select('COUNT(*) as num_entries, DATE(form_entries.date_added) as date_added_day');
		$this->form_entries_model->db()->group_by('form_id, DATE(form_entries.date_added)');
		$where = 'form_entries.date_added BETWEEN "'.$start_date.'" AND "'.$end_date.'"';

		$data = $this->CI->form_entries_model->find_all_array($where);
		$return = array();
		foreach($data as $key => $val)
		{
			if (!isset($return[$val['form']]))
			{
				$return[$val['form']] = array();
			}

			$ts = strtotime($val['date_added_day']);
			$year = date('Y', $ts);
			$month = date('m', $ts);
			$day = date('d', $ts);
			$utc = mktime(date('h') + 1, NULL, NULL, $month, $day, $year) * 1000;

			$return[$val['form']][] = array($utc, $val['num_entries']);
		}
		//$this->CI->form_entries_model->debug_query();
		return $return;
	}


	// --------------------------------------------------------------------
	
	/**
	 * Returns a key value list of all the forms (used for dropdwons)
	 *
	 * @access	public
	 * @param	string The type of form either "db" (found in the database) or "config" (found in the config)
	 * @return	array
	 */	
	public function options_list($type = NULL)
	{
		$options = array();
		if (strtolower($type) == 'db' OR is_null($type))
		{
			$forms_model = $this->model('forms');
			$options = $forms_model->options_list('slug', 'name');
		}
		if (strtolower($type) == 'config' OR is_null($type))
		{
			$forms = $this->config('forms');
			if (!empty($forms))
			{
				foreach($forms as $key => $val)
				{
					$name = (!empty($val['name'])) ? $val['name'] : $key;
					$k = (isset($val['name'])) ? $val['name'] : $key;
					$options[$k] = $name;
				}
			}
		}
		return $options;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method which allows you to make calls like $this->fuel->forms->render('test');
	 *
	 * @access	public
	 * @param	string	Method name
	 * @param	array	An array of arguments to pass to the method
	 * @return	mixed
	 */	
	public function __call($method, $args)
	{
		if (isset($args[0]))
		{
			$name = $args[0];
			$form = $this->get($name);
			if (!isset($form->id))
			{
				$form = $this->create($name);
			}
			array_shift($args);
			return call_user_func_array(array($form, $method), $args);
		
		}
		throw new Exception('Invalid method call '.$$method);
	}
}



// ------------------------------------------------------------------------

/**
 * Fuel Form object 
 *
 */

//  ------------------------------------------------------------------------
class Fuel_form extends Fuel_base_library {

	protected $name = ''; // Name of the form (must be unique and is required)
	protected $slug = ''; // A slug value which can be passed to forms/{slug} for processing the form
	protected $save_entries = FALSE; // Determines whether to save the entries into the database
	protected $form_action = ''; // The URL in which to submit the form. If none is provided, one will be automatically created
	protected $anti_spam_method = array('method' => 'honeypot'); // The method to use to combat SPAM. Options are 'honeypot', 'equation', 'recaptcha', 'stopforumspam' or 'akismet'.
	protected $submit_button_text = 'Submit'; // The text to display for the submit button
	protected $submit_button_value = 'Submit'; // The value used to check that the form was actually submitted. Can't rely on just $_POST not being empty
	protected $reset_button_text = ''; // The text to display for the reset button
	protected $form_display = 'auto'; // The method in which to options are 'auto', 'block', 'html'
	protected $block_view = ''; // The name of the block view file  (only necessary if form_display is set to "block")
	protected $block_view_module = ''; // The name of the module the block view belongs to (only necessary if form_display is set to "block")
	protected $form_html = ''; // The HTML to display (only necessary if form_display is set to "html")
	protected $javascript_submit = TRUE; // Determines whether to submit the form via AJAX
	protected $javascript_validate = TRUE; // Determines whether to use javascript to do front end validation before sending to the backend
	protected $javascript_waiting_message = 'Sending...'; // The message to display during the AJAX process
	protected $email_recipients = ''; // The recipients to recieve the email after form submission
	protected $email_cc = ''; // The CC recipients to recieve the email after form submission
	protected $email_bcc = ''; // The BCC recipients to recieve the email after form submission
	protected $email_subject = ''; // The subject line of the email being sent
	protected $email_message = ''; // The email message to send
	protected $after_submit_text = ''; // The text/HTML to display after the submission process
	protected $attach_files = TRUE; // Will automatically attach files to the email sent out
	protected $attach_file_params = array('upload_path' => 'cache', 'allowed_types' => 'pdf|doc|docx',	'max_size' => '1000'); // An array of parameters used for the CI File Upload class when uploading and attaching files to an email.
	protected $cleanup_attached = TRUE; // Will remove attached files from the file system after being attached
	protected $upload_data = array(); // An array of uploaded file information
	protected $attachments = array(); // An array of files to attach to the recipient email
	protected $return_url = ''; // The return URL to use after the submission process. It will default to returning back to the page that submitted the form
	protected $validation = array(); // An array of extra validation rules to run during the submission process beyond 'required' and the rules setup by default for a field type (e.g. valid_email, valid_phone)
	protected $js = array(); // Additional javascript files to include for the rendering of the form
	protected $fields = array(); // The fields for the form. This is not required if you are using your own HTML in a block or HTML form_display view
	protected $form_builder = array(); // Initialization parameters for the Form_builder class used if a form is being auto-generated
	protected $hooks = array(); // An array of different callable functions associated with one of the predefined hooks "pre_validate", "post_validate", "pre_save", "post_save", "pre_notify", "success", "error" (e.g. 'pre_validate' => 'My_func')

	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object.
	 *
	 * Accepts an associative array as input, containing preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
		$this->CI->load->library('validator');

		// need to reset the validation object upon initialization since we are simply sharing the same one from $CI->validator
		$validator =& $this->get_validator();
		$validator->reset();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders the form for the front end.
	 *
	 <code>
	 $form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE))));
	 echo $form->render();

	 // OR, pass parameters directly to the "render" method
	 $form = $this->fuel->forms->create('myform');
	 echo $form->render(array('fields' => array('name' => array('required' => TRUE))));
	 </code>
	 * 
	 * @access	public
	 * @param	array	An array to modify object properties. If none are provided, it will use the current properties on the object (optional)
	 * @return	string 	Returns the rendered form
	 */	
	public function render($params = array())
	{
		$this->initialize($params);
		
		$this->CI->load->library('session');

		// process request
		if (!empty($_POST[$this->submit_button_value]))
		{
			$this->process();
		}

		// catch any errors thrown back with flash data
		$validator =& $this->get_validator();
		if ($this->CI->session->flashdata('error'))
		{
			$validator->catch_errors($this->CI->session->flashdata('error'));
		}

		// initialize output string
		$output = $this->js_output();

		$this->CI->form_builder->load_custom_fields($this->get_custom_fields());

		$form_fields = $this->form_fields();

		// render from view or HTML
		if (strtolower($this->form_display) != 'auto' AND ($this->has_block_view() OR $this->has_form_html()))
		{
			$vars = $this->rendered_vars($form_fields);

			if ($this->form_display == 'block')
			{
				// use block view file
				$view = '_blocks/'.$this->block_view;
				$output .= $this->CI->load->module_view($this->block_view_module, $view, $vars, TRUE);
			}
			else
			{
				// use HTML from form
				$output .= $this->form_html;
			}
			$output = parse_template_syntax($output, $vars);
		}
		else
		{
			$this->CI->form_builder->set_validator($validator);
			$this->CI->form_builder->set_fields($form_fields);
			if ($this->has_submit_button_text())
			{
				$this->CI->form_builder->submit_value = $this->get_submit_button_text();
			}
			if ($this->has_reset_button_text())
			{
				$this->CI->form_builder->reset_value = $this->get_reset_button_text();
			}
			$posted = ($this->CI->session->flashdata('posted')) ? (array) $this->CI->session->flashdata('posted') : $_POST;
			$this->CI->form_builder->set_field_values($posted);

			$ajax_submit = ($this->is_javascript_submit()) ? ' data-ajax="true"' : '';
			$js_validate = ($this->is_javascript_validate()) ? ' data-validate="true"' : '';
			$js_waiting_message = ($this->is_javascript_validate()) ? ' data-ajax_message="'.rawurlencode($this->javascript_waiting_message).'"' : '';
			$this->CI->form_builder->form_attrs = 'novalidate method="post" action="'.$this->get_form_action().'" enctype="multipart/form-data" class="form" id="'.$this->slug.'"'.$ajax_submit.$js_validate.$js_waiting_message;
			$this->CI->form_builder->display_errors = TRUE;
			$this->CI->form_builder->required_text = lang('forms_required');
			$this->CI->form_builder->set_params($this->form_builder);
			$output .= $this->CI->form_builder->render();
		}
		

		if ($this->CI->session->flashdata('success'))
		{
			$output = $this->get_after_submit_text();
		}

		$output = $output;

		// create area for javascript callback
		return $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds a field to the form. This can also be done by passing the "fields" parameter when creating a form. 
	 * 
	 <code>$form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE))));</code>
	 *
	 * @access	public
	 * @param	array	The name of the field to add
	 * @param	array	Form field parameters
	 * @return	object  Returns itself for method chaining
	 */	
	public function add_field($name, $params = array('type' => 'text'))
	{
		if (is_array($name))
		{
			foreach($name as $key => $value)
			{
				if (is_array($value))
				{
					if (empty($value['name']))
					{
						$value['name'] = $key;
					}
					if (empty($value['type']))
					{
						$value['type'] = 'text';
					}

					//$this->fields[$key] = $this->fuel->forms->create($key, $value);
					$this->fields[$key] = new Form_field($value);
				}
				elseif ($value instanceof Form_field)
				{
					//$this->fields[$key] = $value;
					$this->fields[$key] = $value;
				}
			}
		}
		else
		{
			if (is_array($params))
			{
				if (empty($params['name']))
				{
					$params['name'] = $name;
				}

				//$this->fields[$name] = $this->fuel->forms->create($name, $params);
				$this->fields[$name] = new Form_field($params);

			}
			elseif ($params instanceof Form_field)
			{
				$this->fields[$name] = $params;
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds validation to a form. Note that you can pass {...} placeholders that represent $_POST values
	 * 
	 <code>
	 $validation = array('name', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2'));
	 $form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE))));
	 $form->add_validation($validation);
	 if ($form->validate())
	 {
		echo 'Is Valid!!!';
	 }
	 else
	 {
		echo 'Not Valid :-(';
	 }
	 </code>
	 *
	 * @access	public
	 * @param	mixed	Can be a string, an array like array('start_date', 'check_date', 'Please enter in a valid date.'), or an array of arrays
	 * @param	mixed	The function to validate with which must return either TRUE or FALSE. You can use the array($object, 'method') syntax for methods on object instances
	 * @param	string	The error message to display when the function returns FALSE
	 * @param	mixed	Can be a string or an array. You can use place holder to represent post data (e.g. {email});
	 * @return	object  Returns itself for method chaining
	 */	
	public function add_validation($name, $func = NULL, $msg = NULL, $params = array())
	{
		$validator =& $this->get_validator();

		if (is_array($name))
		{
			if (is_array(current($name)))
			{
				foreach($name as $key => $value)
				{
					$this->add_validation_rule($value);
				}
			}
			else
			{
				$this->add_validation_rule($name);
			}
		}
		elseif (is_string($name))
		{
			$this->add_validation_rule($name, $func, $msg, $params);

		}
		return $this;
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Adds a single validation rule to the validator object.
	 * 
	 * @access	protected
	 * @return	object 	Returns the validator object 
	 */	
	protected function add_validation_rule($name, $func = NULL, $msg = NULL, $params = array())
	{
		$validator = $this->get_validator();
		$values = $this->CI->input->post();

		if (is_array($name))
		{
			$rule = $name;
		}
		else
		{
			$rule = array($name, $func, $msg, $params);
		}

		$key = $rule[0];
		$val = $this->CI->input->post($key);

		if (empty($rule[3]))
		{
			$rule[3] = (!empty($values[$key])) ? array($values[$key]) : array();
		} 
		else if (!is_array($rule[3])) 
		{
			$rule[3] = array($rule[3]);
		}
		
		// now replace any placeholders for values
		foreach($rule[3] as $r_key => $r_val) 
		{
			if (is_array($r_val))
			{
				foreach($r_val as $rv)
				{
					if (strpos($rv, '{') === 0)
					{
						$val_key = str_replace(array('{', '}'), '', $rv);
						if (isset($values[$val_key])) $rule[3][$r_key] = $values[$val_key];
					}
				}
			}
			else
			{
				if (strpos($r_val, '{') === 0)
				{
					$val_key = str_replace(array('{', '}'), '', $r_val);
					if (isset($values[$val_key])) $rule[3][$r_key] = $values[$val_key];
				}
			}
			
		}

		call_user_func_array(array($validator, 'add_rule'), $rule);
		return $validator;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Removes a validation rule from a form.
	 * 
	 <code>
	 $_POST['password'] = 'xx';
	 $_POST['password2'] = 'xxx';

	 $validation = array('name', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2'));
 	 $fields['password'] = array('type' => 'password', 'validation' => array($validation));
	 $fields['password2'] = array('type' => 'password', 'label' => 'Password verfied');

	 $form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE))));

	 $form->add_validation($validation);
	 $validated = $form->validate(); // FALSE
	 $form->remove_validation('name');
	 $validated = $form->validate(); // TRUE
	 </code>
	 *
	 * @access	public
	 * @param 	string  Field to remove
	 * @param 	string  Key for rule (can have more then one rule for a field) (optional)
	 * @return	object  Returns itself for method chaining
	 */	
	public function remove_validation($key, $func = NULL)
	{
		$validator = $this->get_validator();
		$validator->remove_rule($key, $func);
		return $this;
	}
	// --------------------------------------------------------------------
	
	/**
	 * Processes the field which includes validation, submission to database (if submit_entries = TRUE) and emailing to recipients (if email_recipients isn't blank).
	 * 
	 <code>
	$form = $this->fuel->forms->get('myform');
	if ($form->process())
	{
		echo 'Success';
	}
	else
	{
		echo 'Failure';
	}
	 </code>
	 *
	 * @access	public
	 * @return	boolean  Returns whether the processing was successfull without any errors
	 */	
	public function process()
	{
		// pre process hook
		$this->call_hook('pre_process');

		// saved in the post so that it can be validated by post processors like AKISMET
		$_POST['__email_message__'] = $this->get_email_message();

		// pre validate hook
		$this->call_hook('pre_validate');

		if ($this->validate())
		{
			$hook_params = array('form' => $this, 'post' => $_POST);

			// post validate hook
			$this->call_hook('post_validate');

			$posted = $this->clean_posted();
			$is_spam = $this->is_spam($posted);
			if ($this->fuel->pages->mode() != 'views' && $this->get_save_entries())
			{
				if (!isset($this->CI->db))
				{
					$this->CI->load->database();
				}

				if ($this->CI->db->table_exists('form_entries'))
				{

					// pre save hook
					$this->call_hook('pre_save');

					$model =& $this->CI->fuel->forms->model('form_entries');
					$entry = $model->create();
					$entry->url = last_url();
					$entry->post = json_encode($posted);
					$entry->form_name = $this->name;
					$entry->remote_ip = $_SERVER['REMOTE_ADDR'];

					// set if it's SPAM
					$entry->is_spam = ($is_spam) ? 'yes' : 'no';
					$entry->fill($posted);

					if ($entry->is_savable())
					{
						if (!$entry->save())
						{
							$this->call_hook('error', array('errors' => $entry->errors()));
							$this->_add_error($entry->errors());
							return FALSE;
						}
						$this->call_hook('post_save'); 
					}
				}
			}

			$this->call_hook('post_process');
			if (!$is_spam OR ($is_spam AND $this->fuel->forms->config('send_spam')))
			{
				if (!$this->notify($_POST['__email_message__']))
				{
					$this->call_hook('error', array('errors' => $this->last_error()));
					$this->_add_error($entry->errors());
					return FALSE;
				}
			}
			$this->call_hook('success');
			return TRUE;
		}
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks if a form posting has SPAM characteristics
	 *
	 * @access	public
	 * @param	array	Posted form values
	 * @return	boolean Returns TRUE/FALSE based on if either stopforumspam or AKISMET returns it as SPAM
	 */	
	public function is_spam($posted)
	{
		$is_spam = FALSE;
		$anti_spam_params = $this->get_antispam_params();
		$spam_config = $this->fuel->forms->config('spam_fields');
		if (isset($posted[$spam_config['name_post_field']]) AND isset($posted[$spam_config['email_post_field']]))
		{

			// first test AKISMET if a key is provided
			$akismet_key = $this->fuel->forms->config('akismet_api_key');
			if (!empty($akismet_key))
			{
				$is_spam = (isset($posted[$spam_config['comment_post_field']]) AND !validate_akismet($akismet_key, $posted[$spam_config['name_post_field']], $posted[$spam_config['email_post_field']], $posted[$spam_config['comment_post_field']]));
			}

			// if still no spam, we'll check stopfurmspam to just be sure
			if ($is_spam === FALSE)
			{
				$is_spam = (!validate_stopforumspam($posted[$spam_config['name_post_field']], $posted[$spam_config['email_post_field']]));
			}
		}
		return $is_spam;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Validates the form before submission.
	 * 
	 <code>
	$form = $this->fuel->forms->get('myform');
	if ($form->validate())
	{
		echo 'Is valid';
	}
	else
	{
		echo 'Invalid!!!';
	}
	// ... further processing
	 </code>
	 *
	 * @access	public
	 * @return	boolean  Returns TRUE/FALSE based on if the form validates or not. Is called during the "process" method as well
	 */	
	public function validate()
	{
		$this->CI->load->module_helper(FORMS_FOLDER, 'forms');

		// run post processing to validate custom fields
		$this->CI->form_builder->load_custom_fields($this->get_custom_fields());
		$this->CI->form_builder->set_fields($this->form_fields());
		$this->CI->form_builder->set_field_values($_POST);
		$this->CI->form_builder->post_process_field_values();

		$fields = $this->fields;

		$form_layouts = $this->CI->fuel->layouts->options_list(TRUE, 'Forms');
		$form_validators = array();
		foreach($form_layouts as $key => $layout)
		{
			$form_validators[$key] = $this->field_layout($key);
		}

		$validator = $this->get_validator();

		// loop through the $form variable to grab all the form fields marked as required to add validation rules
		foreach($fields as $f)
		{
			if (empty($f->name) OR !isset($form_validators[$f->type])) continue;

			$field = $form_validators[$f->type];

			if ($f->is_required())
			{
				$validator->add_rule($f->name, 'required', lang('forms_form_required', $f->label));
			}

			if (method_exists($field, 'frontend_validation'))
			{
				 // not necessary since this is the default, but doing it for good measure
				$field->set_validator($validator);

				// set front end validation rules
				$field->frontend_validation($f->name);
			}
		}

		// add blacklist validation
		$blacklist = $this->fuel->forms->config('blacklist');
		if (!empty($blacklist))
		{
			$validator->add_rule('ip_address', 'blacklisted', lang('forms_error_blacklisted'), $blacklist);	
		}

		// add any additional validation
		$this->run_other_validation();


		// VALIDATE!!!
		$validated = $validator->validate();

		if (!$validated)
		{
			$this->_add_error($validator->get_errors());
		}
		return $validated;

	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a callback hook to be run via "pre" or "post" rendering of the page.
	 *
	 * @access	public
	 * @param	mixed	A string or an array of server file paths to attach. If no values are passed, then it will automatically look in the $_FILES array
	 * @param	array	An array of configuration parameters to pass to the CI File Upload class.
	 * @return	void
	 */
	public function upload_files($files = array(), $params = array())
	{
		if (empty($files))
		{
			if (!empty($_FILES))
			{
				foreach($_FILES as $key => $file)
				{
					if ($file['error'] == 0)
					{
						$files[$key] = $file['tmp_name'];
					}	
				}
			}
		}

		if (empty($files))
		{
			return array();
		}

		// use upload class to help a bit with security and consistency
		if (!is_array($files))
		{
			$file_name = pathinfo($files, PATHINFO_FILENAME);
			$files = array($file_name => $files);
		}

		// if parameters is empty, we grab from the config
		if (empty($params))
		{
			$params = $this->get_attach_file_params();
		}

		$this->CI->load->library('upload');
		$fields = $this->fields;
		foreach($files as $key => $path)
		{
			if (isset($fields[$key]))
			{
				if ( ! empty($params))
				{
					$this->CI->upload->initialize($params);	
				}

				if ( ! $this->CI->upload->do_upload($key))
				{
					$error = array('error' => $this->CI->upload->display_errors());
					$this->_add_error($error);
				}
				else
				{
					$this->upload_data[$key] = $this->CI->upload->data();
				}		
			}
		}
		return $this->upload_data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Cleans up attached uploaded files.
	 *
	 * @access	public
	 * @return	void
	 */
	protected function cleanup_upload_files()
	{
		if ($this->get_cleanup_attached() === TRUE)
		{
			foreach($this->attachments as $attachment)
			{
				@unlink($attachment);
			}

			foreach($this->upload_data as $upload_data)
			{
				@unlink($upload_data['full_path']);
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a callback hook to be run via "pre" or "post" rendering of the page.
	 *
	 * @access	public
	 * @param	key		The type of hook (e.g. "pre_render" or "post_render")
	 * @param	array	An array of hook information including the class/callback function. <a href="http://ellislab.com/codeigniter/user-guide/general/hooks.html" target="blank">More here</a>
	 * @return	void
	 */
	public function set_hook($type, $hook)
	{
		$this->hooks[$type] = $hook;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Calls a specified hook to be run.
	 *
	 * @access	public
	 * @param	hook	The type of hook (e.g. "pre_validate" or "pre_save")
	 * @param	array	An array of additional parameters to pass to the hook method/function
	 * @return	void
	 */
	public function call_hook($hook, $params = array())
	{
		$default_params['form'] =& $this;
		$default_params['post'] = $_POST;
		$default_params['hook'] = $hook;
		$params = array_merge($default_params, $params);

		if (!empty($this->hooks[$hook]) AND is_callable($this->hooks[$hook]))
		{
			return call_user_func_array($this->hooks[$hook], $params);
		}
		else
		{
			// call hooks set in hooks file
			$hook_name = 'form_'.$this->slug.'_'.$hook;
			
			if (!empty($this->hooks[$hook]))
			{
				if (isset($GLOBALS['EXT']->hooks[$hook_name]) AND !is_array($GLOBALS['EXT']->hooks[$hook_name]))
				{
					$GLOBALS['EXT']->hooks[$hook_name] = array($GLOBALS['EXT']->hooks[$hook_name]);
				}
				$GLOBALS['EXT']->hooks[$hook_name][] = $this->hooks[$hook];
			}

			// run any hooks set on the object
			return $GLOBALS['EXT']->_call_hook($hook_name, $params);
		}
	
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sends email notification to those specified in the email_recipients field. Is called within the process method as well.
	 * 
	 <code>
	$form = $this->fuel->forms->create('myform', array('email_recipients' => array('superman@krypton.com')));
	if ($form->notify())
	{
		echo 'Notified';
	}
	else
	{
		echo 'Failure in Notification';
	}
	 </code>
	 *
	 * @access	public
	 * @param   string   The message to send (optional)
	 * @return	boolean  Returns TRUE/FALSE based on if the form validates or not. Is called during the "process" method as well
	 */	
	public function notify($msg = NULL)
	{
		if (empty($msg))
		{
			$msg = $this->get_email_message();
		}

		if ($this->has_email_recipients() OR $this->fuel->forms->config('test_email'))
		{

			$this->call_hook('pre_notify');

			$this->CI->load->library('email');
			$email =& $this->CI->email;
			$forms =& $this->CI->fuel->forms;

			// send email
			$email->from($this->fuel->forms->config('email_from'));

			// set the email subject
			$email->subject($this->get_email_subject());

			// check config if we are in dev mode
			if (is_dev_mode())
			{
				$email->to($this->fuel->forms->config('test_email'));
				$email->cc($this->email_cc);
				$email->bcc($this->email_bcc);
			}
			else
			{
				$email->to($this->email_recipients);
				$email->cc($this->email_cc);
				$email->bcc($this->email_bcc);
			}

			// build the email content
			$email->message($msg);

			// attach any files
			if ($this->get_attach_files() === TRUE)
			{
				foreach($this->attachments as $attachment)
				{
					// add this so that it can be auto-removed
					$email->attach($attachment);
				}

				foreach($this->upload_files() as $upload_data)
				{
					$email->attach($upload_data['full_path']);
				}
			}
	
			// let her rip
			if (!$email->send())
			{
				if (is_dev_mode())
				{
					echo $email->print_debugger();
					exit();
				}
				$this->_add_error(lang('forms_error_sending_email'));
				return FALSE;
			}

			// cleaup any uploaded files
			$this->cleanup_upload_files();
			
		}
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the javascript files used for rendering the form. The render method will automatically call this method, however,
	 * it's at your disposal if you wish to render it in your own block outside of that process.
	 * 
	 <code>
	$form = $this->fuel->forms->create('myform', array('js' => array('myvalidation.js')));
	echo $form->js_output();
	 </code>
	 *
	 * @access	public
	 * @return	string  Returns the javascript script files registered with the form including any the jquery.validate plugin if javascript_validate is set to TRUE
	 */	
	public function js_output()
	{
		$output = '';
		// include js files
		if ($this->is_javascript_submit() OR $this->is_javascript_validate())
		{
			$output .= "\n".js('jquery.validate.min, additional-methods.min, jquery.forms, forms', FORMS_FOLDER);
			$config_js = $this->fuel->forms->config('js');
		}
		if (!empty($config_js))
		{
			$output .= "\n".js($config_js);
		}
		if ($this->has_js())
		{
			$output .= "\n".js($this->js);
		}
		return $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the rendered form fields as variables which can be consumed by views/blocks and includes an array of rendered field output, labels, and the form object itself.
	 * 
	 <code>
	$form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE), 'email' => array('required' => TRUE))));
	echo $form->rendered_vars();
	 </code>
	 *
	 * @access	public
	 * @return	array  Returns an array of variables that can be used in views/block files
	 */	
	public function rendered_vars($form_fields)
	{
		$rendered_fields = array();
		$vars = array();
		
		foreach($form_fields as $key => $form_field)
		{
			$rendered_fields[$key]['field'] = $this->CI->form_builder->create_field($form_field);
			$rendered_fields[$key]['label'] = $this->CI->form_builder->create_label($form_field);
			$rendered_fields[$key]['key'] = $key;
			$vars[$key.'_field'] = $rendered_fields[$key]['field'];
			$vars[$key.'_label'] = $rendered_fields[$key]['label'];
		}
		$vars['fields'] = $rendered_fields;
		$vars['form'] = $this;
		return $vars;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of form fields that can be used with the Form_builder class.
	 * 
	 <code>
	$form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE), 'email' => array('required' => TRUE))));
	foreach($form->form_fields() as $name => $field)
	{
		echo $this->form_builder->create_field($field);
	}
	 </code>
	 *
	 * @access	public
	 * @return	array  Returns an array of form fields
	 */	
	public function form_fields()
	{
		// setup fields for the form
		$form_fields = array();

		$is_block_view = $this->is_block_view();
		foreach($this->fields as $f)
		{
			$form_fields[$f->name] = $f->render($is_block_view);
		}

		// antispam
		$antispam_params = $this->get_antispam_params();
		if (!empty($antispam_params['method']))
		{
			$form_fields['__antispam__'] = array('type' => 'antispam', 'display_label' => FALSE);
			$form_fields['__antispam__'] = array_merge($form_fields['__antispam__'], $antispam_params);
		}
		$form_fields['return_url'] = array('type' => 'hidden', 'value' => $this->get_return_url());
		$form_fields['form_url'] = array('type' => 'hidden', 'value' => current_url());

		return $form_fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the validator object used for validating the front end.
	 *
	 * @access	protected
	 * @return	object
	 */	
	public function &get_validator()
	{
		return $this->CI->validator;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns custom field information to be used with rendering the form via Form_builder.
	 * 
	 * @access	protected
	 * @return	array
	 */	
	protected function get_custom_fields()
	{
		include(FORMS_PATH.'config/custom_fields.php');
		$custom_fields = $this->fuel->forms->config('custom_fields');
		$custom_fields = array_merge($fields, $custom_fields);
		return $custom_fields;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns information about the anti SPAM method to use for the form.
	 * 
	 * @access	protected
	 * @return	array 
	 */	
	protected function get_antispam_params()
	{
		if (is_json_str($this->anti_spam_method))
		{
			return json_decode($this->anti_spam_method, TRUE);
		}
		if (is_string($this->anti_spam_method))
		{
			return array('method' => $this->anti_spam_method);
		}
		return $this->anti_spam_method;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of posted form variables that can be used for capturing to a database or sent via email.
	 *
	 * @access	protected
	 * @param	array $posted
	 * @return	array
	 */
	protected function clean_posted($posted = array())
	{
		if (empty($posted))
		{
			$posted = $this->CI->input->post(NULL, TRUE);
		}
		$return = array();
		if (!empty($posted))
		{
			$fields = $this->fields;
			$has_fields = !empty($fields);
			foreach($posted as $key => $val)
			{
				if (preg_match('#^_.+#', $key) OR ($has_fields AND (empty($fields[$key]) OR ($fields[$key]->type == 'hidden'))))
				{
					continue;
				}
				if (is_array($val))
				{
					$val = implode(', ', $val);
				}
				$return[$key] = strip_tags($val);
			}
		}
		return $return;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Runs validation found within the forms configuration if a form is created within the $config['forms']['forms'].
	 * 
	 * @access	protected
	 * @return	object 	Returns the validator object 
	 */	
	protected function run_other_validation()
	{
		// grab any validation that may be set in the config file
		// $config = $this->fuel->forms->config();
		// $validation = (isset($config['forms'][$this->slug]['validation'])) ? $config['forms'][$this->slug]['validation'] : array();
		// if (!empty($validation))
		// {
		// 	$this->add_validation($validation);
		// }

		if (!empty($this->validation))
		{
			$this->add_validation($this->validation);	
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns TRUE/FALSE based on if the entries should be saved or not.
	 *
	 * @access	protected
	 * @return	boolean
	 */	
	protected function get_save_entries()
	{
		return is_true_val($this->save_entries);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns TRUE/FALSE based on if javascript submission should be used.
	 *
	 * @access	protected
	 * @return	boolean
	 */	
	protected function get_javascript_submit()
	{
		return is_true_val($this->javascript_submit);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns TRUE/FALSE based on if the javascript validation should be saved or not.
	 *
	 * @access	protected
	 * @return	boolean
	 */	
	protected function get_javascript_validate()
	{
		return is_true_val($this->javascript_validate);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the form "action" URL. If no value is specified it will be "forms/{slug}".
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_form_action()
	{
		if (empty($this->form_action))
		{
			// if ($this->fuel->forms->config('javascript_submit'))
			// {
				return site_url('forms/process/'.$this->slug);
			//}
			//return site_url(uri_path());	
		}
		return site_url($this->form_action);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the module name to be used if a block is being used to render the form.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_block_view_module()
	{
		if (empty($this->block_view_module))
		{
			return 'application';
		}
		return $this->block_view_module;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the the email's from value and will pull from the form's config file if no value is set.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_email_from()
	{
		if (empty($this->email_from))
		{
			return $this->fuel->forms->config('email_from');
		}
		return $this->email_from;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the the email's subject line and will pull from the form's config file if no value is set.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_email_subject()
	{
		if (empty($this->email_subject))
		{
			return $this->fuel->forms->config('email_subject');
		}
		return $this->email_subject;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the the email's subject line and will pull from the form's config file if no value is set.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_email_message()
	{
		$this->CI->load->helper('inflector');

		$output = '';

		$fields = $this->fields;
		$posted = $this->clean_posted();
		$posted['URL'] = site_url(uri_string());

		if (!empty($posted))
		{
			if (!empty($this->email_message))
			{
				$msg = $this->email_message;

				// if it's callable, then we execute it
				if (is_callable($this->email_message))
				{
					$msg = call_user_func($this->email_message, $this, $posted);
				}

				// used to escape the placeholder issues with for example the "name" property
				$msg = str_replace(array('{{', '}}'), array('{', '}'), $msg);
				$output = parse_template_syntax($msg, $posted, TRUE);
			}
			else
			{
				foreach($posted as $key => $val)
				{
					$output .= humanize($key).": $val\n";
				}
				$output = lang('forms_email_message', $this->name, $output);
			}
		}
		return $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Gets the return URL to be used after the submission of the form.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_return_url()
	{
		if (empty($this->return_url))
		{
			return site_url(uri_string());
		}
		return $this->return_url;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the submit button text.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_submit_button_text()
	{
		if (empty($this->submit_button_text))
		{
			return lang('forms_submit_button_default');
		}
		return $this->submit_button_text;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the reset button text.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_reset_button_text()
	{
		return $this->reset_button_text;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the text to be display after submisssion.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_after_submit_text()
	{
		if (empty($this->after_submit_text))
		{
			return lang('forms_after_submit');
		}
		return $this->after_submit_text;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a boolean TRUE/FALSE value based on whether the form should attach files automatically to the generated sent email.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_attach_files()
	{
		if (!isset($this->attach_files))
		{
			return $this->fuel->forms->config('attach_files');
		}
		return $this->attach_files;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of parameters used for the CI File Upload class when uploading and attaching files to an email.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_attach_file_params()
	{
		if (empty($this->attach_file_params))
		{
			$params = $this->fuel->forms->config('attach_file_params');
		}
		else
		{
			$params = $this->attach_file_params;
		}
		if ((isset($params['upload_path']) AND $params['upload_path'] == 'cache') OR empty($params['upload_path']))
		{
			$params['upload_path'] = APPPATH.'cache/';
		}
		return $params;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a boolean TRUE/FALSE value based on whether to cleanup any attached files from the file system.
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function get_cleanup_attached()
	{
		if (!isset($this->cleanup_attached))
		{
			return $this->fuel->forms->config('cleanup_attached');
		}
		return $this->cleanup_attached;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds an attachment for the recipient email
	 *
	 * @access	protected
	 * @return	string
	 */	
	public function add_attachment($attachment)
	{
		$this->attachments[] = $attachment;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method for capturing method calls on the record object that don't exist. Allows for "get_{field}" to map to just "{field}" as well as "is_{field}"" and "has_{field}".
	 *
	 * @access	public
	 * @param	object	method name
	 * @param	array	arguments
	 * @return	array
	 */	
	public function __call($method, $args)
	{
		if (preg_match( "/^set_(.*)/", $method, $found))
		{
			if (property_exists($this, $found[1]))
			{

				$method = $found[1];
				$this->$method = $args[0];
				return TRUE;
			}
		}
		else if (preg_match("/^get_(.*)/", $method, $found))
		{
			if (property_exists($this, $found[1]))
			{
				$method = $this->$found[1];
				return $this->$method;
			}
		}
		elseif (preg_match("/^is_(.*)/", $method, $found))
		{
			if (property_exists($this, $found[1]))
			{
				if (!empty($found[1]))
				{
					return is_true_val($this->$found[1]);
				}
			}
		}
		else if (preg_match("/^has_(.*)/", $method, $found))
		{
			if (property_exists($this, $found[1]))
			{
				return !empty($this->$found[1]);
			}
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method to set first property, method, then field values.
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	mixed	
	 * @return	void
	 */	
	public function __set($var, $val)
	{
		if (method_exists($this, 'set_'.$var))
		{
			$set_method = "set_".$var;
			$this->$set_method($val);
		}
		else if (property_exists($this, $var))
		{
			$this->$var = $val;
		}
		else
		{
			throw new Exception('property '.$var.' does not exist.');
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method to return first property, method, then field values.
	 *
	 * @access	public
	 * @param	string	field name
	 * @return	mixed
	 */	
	public function __get($var)
	{
		$output = NULL;

		// first class property has precedence
		if (method_exists($this, "get_".$var))
		{
			$get_method = "get_".$var;
			$output = $this->$get_method();
		}
		else if (property_exists($this, $var))
		{
			$output = $this->$var;
		}
		
		return $output;
	}
}

// ------------------------------------------------------------------------

/**
 * Fuel Form field object 
 *
 */

//  ------------------------------------------------------------------------
class Form_field extends Fuel_base_library {

	protected $params = array('type' => 'text');

	/**
	 * Constructor - Sets parameters
	 */
	function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */
	public function set_params($params)
	{

		if (!is_array($params) OR empty($params)) return;

		// set invalid base properties that can be set
		$invalid_props = array('CI', 'fuel');
		foreach ($params as $key => $val)
		{
			if (!in_array($key, $invalid_props) AND substr($key, 0, 1) != '_')
			{
				$this->$key = $val;
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the values of the form fields in an array.
	 *
	 * @access	protected
	 * @return	array
	 */
	public function values()
	{
		return $this->params;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns TRUE/FALSE depending on if the field is considered required.
	 *
	 * @access	protected
	 * @return	boolean
	 */
	public function is_required()
	{
		return !empty($this->required);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders the form field.
	 *
	 * @access	protected
	 * @return	string
	 */
	public function render($string = TRUE)
	{
		$field = '';
		$layout = $this->fuel->forms->field_layout($this->type);

		if (!empty($layout))
		{
			$field = $layout->frontend_render($this);
			if ($string)
			{
				return $this->CI->form_builder->create_field($field);
			}
		}
		return $field;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the label value.
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function get_label()
	{
		if (empty($this->params['label']))
		{
			return ucfirst(str_replace('_', ' ', $this->params['name']));
		}
		return $this->params['label'];
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method to set first property, method, then field values.
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	mixed	
	 * @return	void
	 */	
	public function __set($var, $val)
	{
		if (method_exists($this, 'set_'.$var))
		{
			$set_method = "set_".$var;
			$this->$set_method($val);
		}
		else
		{
			$this->params[$var] = $val;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method to return first property, method, then field values.
	 *
	 * @access	public
	 * @param	string	field name
	 * @return	mixed
	 */	
	public function __get($var)
	{
		$output = NULL;

		// first class property has precedence
		if (method_exists($this, "get_".$var))
		{
			$get_method = "get_".$var;
			$output = $this->$get_method();
		}
		else if (array_key_exists($var, $this->params))
		{
			$output = $this->params[$var];
		}
		
		return $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method that returns TRUE/FALSE depending if the parameter is set.
	 *
	 * @access	public
	 * @param	string	field name
	 * @return	boolean
	 */	
	public function __isset($var)
	{
		return isset($this->params[$var]);
	}
}