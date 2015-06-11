<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FORMS_PATH.'libraries/third_party/recaptchalib.php');

class Forms_custom_fields {

	protected $CI;
	protected $fuel;
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->fuel =& $this->CI->fuel;
		$this->CI->load->module_helper(FORMS_FOLDER, 'forms');
		$this->CI->load->library('session');
		$this->CI->load->library('validator');
		$this->CI->load->library('form_builder');
		$this->CI->form_builder->set_validator($this->CI->validator);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Creates a reCAPTCHA to combat SPAM as well as the necessary post processing validation rules.
	 * Additional parameters include "recaptcha_public_key", "recaptcha_private_key" and "<a href="https://developers.google.com/recaptcha/docs/customization" target="_blank">theme</a>":
	 *
	 * <a href="https://developers.google.com/recaptcha/" target="_blank">https://developers.google.com/recaptcha/</a>
	 * 
	 * @access	public
	 * @param 	array  	An array of parameters to pass to the field type
	 * @return	string
	 */	
	public function recaptcha($params = array())
	{
		$form_builder =& $params['instance'];

		if (empty($params['recaptcha_public_key']))
		{
			$params['recaptcha_public_key'] = $this->fuel->forms->config('recaptcha_public_key');
		}

		if (empty($params['recaptcha_private_key']))
		{
			$params['recaptcha_private_key'] = $this->fuel->forms->config('recaptcha_private_key');
		}

		$defaults = array('theme' => 'clean', 'error_message' => 'Please enter in a valid captcha value');
		$params = $this->set_defaults($defaults, $params);

		if (isset($_POST["recaptcha_response_field"]))
		{
			$_POST[$params['key']] = $_POST["recaptcha_response_field"];
		}

        $params['type'] = 'none';
        $func_str = '$CI =& get_instance();
        	$validator =& $CI->form_builder->get_validator();
        	$validator->add_rule("recaptcha_response_field", "required", "'.$params['error_message'].'", array("'.$this->CI->input->post('recaptcha_response_field').'"));
			$validator->add_rule("recaptcha_response_field", "validate_recaptcha", "'.$params['error_message'].'", array("'.$params['recaptcha_private_key'].'"));
			';
		$func = create_function('$value', $func_str);
		$form_builder->set_post_process($params['key'], $func);

		$str = '<script>
             var RecaptchaOptions = {
                theme : \''.$params['theme'].'\'
             };
        </script>
        ';
		$str .= recaptcha_get_html($params['recaptcha_public_key']);
		return $str;
	}


	// --------------------------------------------------------------------
	
	/**
	 * Adds Akismet validation to your form as well as the necessary post processing validation rules.
	 * akismet_api_key
	 * recaptcha_private_key
	 *
	 * http://akismet.com
	 *
	 * @access	public
	 * @param 	array  	An array of parameters to pass to the field type
	 * @return	string
	 */	
	public function akismet($params = array())
	{
		$form_builder =& $params['instance'];

		if (empty($params['akismet_api_key']))
		{
			$params['akismet_api_key'] = $this->fuel->forms->config('akismet_api_key');
		}

		$defaults = array('name_field' => 'name', 'email_field' => 'email', 'message_field' => '__email_message__', 'error_message' => 'Your message has been flagged as SPAM and cannot be submitted.');
		$params = $this->set_defaults($defaults, $params);

		if (!empty($_POST))
		{
			$func_str = '$CI =& get_instance();
				$validator =& $CI->form_builder->get_validator();
				$validation_params = array("'.$params['akismet_api_key'].'", "'.$this->CI->input->post($params['name_field']).'", "'.$this->CI->input->post($params['email_field']).'", "'.$this->CI->input->post($params['message_field']).'");
				$validator->add_rule("recaptcha_response_field", "validate_akismet", "'.$params['error_message'].'", $validation_params);
				';
			$func = create_function('$value', $func_str);
			$form_builder->set_post_process($params['key'], $func);
		}

		// must return a space or else a default field will appear
		return ' ';
	}

	
	// --------------------------------------------------------------------
	
	/**
	 * Creates a honeypot to combat SPAM as well as the necessary post processing validation rules.
	 * http://www.dexmedia.com/blog/honeypot-technique/
	 * 
	 * @access	public
	 * @param 	array  	An array of parameters to pass to the field type
	 * @return	array
	 */	
	public function honeypot($params = array())
	{
		$form_builder =& $params['instance'];

		$defaults = array('name_field' => '', 'error_message' => 'Invalid submission.');
		$params = $this->set_defaults($defaults, $params);
		
		if (!empty($_POST))
		{
			$func_str = '$CI =& get_instance();
				$validator =& $CI->form_builder->get_validator();
				$validator->add_rule("donotfillthisout", "is_equal_to", "'.$params['error_message'].'", array("'.$this->CI->input->post('donotfillthisout').'", ""));
				';
			$func = create_function('$value', $func_str);
			$form_builder->set_post_process($params['key'], $func);
		}

		$field = array('type' => 'hidden', 'name' => 'donotfillthisout');
		return $form_builder->create_field($field);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Creates an equation to combat SPAM as well as the necessary post processing validation rules.
	 * http://www.dexmedia.com/blog/honeypot-technique/
	 * 
	 * @access	public
	 * @param 	array  	An array of parameters to pass to the field type
	 * @return	string
	 */	
	public function equation($params = array())
	{
		// check if session is already started and if not, start one
		$session_id = session_id();
		if (empty($session_id))
		{
			session_start();
		}

		$form_builder =& $params['instance'];

		$defaults = array('name_field' => '', 'error_message' => 'Please enter in a valid answer to the spam check.');
		$params = $this->set_defaults($defaults, $params);

		if (!empty($_POST))
		{
			$func_str = '$CI =& get_instance();
				$validator =& $CI->form_builder->get_validator();
				$validator->add_rule("antispam", "required", "'.$params['error_message'].'", array("'.$this->CI->input->post('antispam').'"));
				$validator->add_rule("antispam", "is_equal_to", "'.$params['error_message'].'", array("'.$this->CI->input->post('antispam').'", "'.$_SESSION['check_spam'].'"));
				';
			$func = create_function('$value', $func_str);
			$form_builder->set_post_process($params['key'], $func);
		}

		$spam_check1 = rand(0, 9);
		$spam_check2 = rand(0, 100);
		$answer = $spam_check1 + $spam_check2;
		if (!$form_builder->is_post_processing)
		{
			$_SESSION['check_spam'] = $answer;
			// using same session is probablematic with FUEL when dealing with Flash data
			//$this->CI->session->set_flashdata('check_spam', $answer);
		}

		$field = array('type' => 'text', 'name' => 'antispam', 'placeholder' => '?', 'size' => 3, 'display_label' => FALSE, 'before_html' => $spam_check1." + ".$spam_check2." = ");
		return $form_builder->create_field($field);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds Stopforumspam validation to your form as well as the necessary post processing validation rules.
	 *
	 * @access	public
	 * @param 	array  	An array of parameters to pass to the field type
	 * @return	string
	 */	
	public function stopforumspam($params = array())
	{
		$form_builder =& $params['instance'];

		if (empty($params['thresholds']))
		{
			$params['thresholds'] = $this->fuel->forms->config('stopforumspam');
		}
		else
		{
			$params['thresholds'] = array_merge($this->fuel->forms->config('stopforumspam'), $params['thresholds']);
		}
		
		$defaults = array('name_field' => 'name', 'email_field' => 'email', 'message_field' => '__email_message__', 'error_message' => 'Your message has been flagged as SPAM and cannot be submitted.');
		$params = $this->set_defaults($defaults, $params);

		if (!empty($_POST))
		{
			$func_str = '$CI =& get_instance();
			$thresholds = array();
			';

			if (!empty($params['thresholds']['ip_threshold_flag']))	$func_str .= '$thresholds["ip_threshold_flag"] = '.$params['thresholds']['ip_threshold_flag'].';';
			if (!empty($params['thresholds']['email_threshold_flag'])) $func_str .= '$thresholds["email_threshold_flag"] = '.$params['thresholds']['email_threshold_flag'].';';
			if (!empty($params['thresholds']['ip_threshold_ignore'])) $func_str .= '$thresholds["ip_threshold_ignore"] = '.$params['thresholds']['ip_threshold_ignore'].';';
			if (!empty($params['thresholds']['email_threshold_ignore'])) $func_str .= '$thresholds["email_threshold_ignore"] = '.$params['thresholds']['email_threshold_ignore'].';';

			$func_str .= '$validator =& $CI->form_builder->get_validator();
				$validation_params = array("'.$this->CI->input->post($params['name_field']).'", "'.$this->CI->input->post($params['email_field']).'", "'.$_SERVER['REMOTE_ADDR'].'", $thresholds);
				$validator->add_rule("'.$params['key'].'", "validate_stopforumspam", "'.$params['error_message'].'", $validation_params);
				';
			$func = create_function('$value', $func_str);
			$form_builder->set_post_process($params['key'], $func);
		}

		// must return a space or else a default field will appear
		return ' ';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Creates one of the 4 SPAM fields types. You specify the "method" parameter to determine which one to use.
	 * 
	 * @access	public
	 * @param 	array  	An array of parameters to pass to the field type
	 * @return	string
	 */	
	public function antispam($params = array())
	{
		$form_builder =& $params['instance'];

		$defaults = array('method' => 'honeypot');
		$params = $this->set_defaults($defaults, $params);

		$valid_types = array('akismet', 'recaptcha', 'equation', 'honeypot', 'stopforumspam');
		if (!in_array($params['method'], $valid_types))
		{
			return 'Invalid spam method specified';
		}
		$method = $params['method'];

		return $this->$method($params);
	}


	// --------------------------------------------------------------------
	
	/**
	 * Creates a default set of parameters for the field type.
	 * 
	 * @access	protected
	 * @param 	array  	An array of default parameters to pass to the field type
	 * @param 	array  	An array of parameters to pass to the field type
	 * @return	array
	 */	
	protected function set_defaults($defaults, $params = array())
	{

		foreach($defaults as $key => $default)
		{
			if (!isset($params[$key]))
			{
				$params[$key] = $defaults[$key];
			}
		}

		if (!empty($_POST) AND !isset($_POST[$params['key']]))
		{
			$_POST[$params['key']] = '';
		}

		return $params;
	}
}