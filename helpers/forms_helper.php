<?php 
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2014, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * {module_name} Helper
 *
 * Contains functions for the {module_name} module
 *
 * @package		User Guide
 * @subpackage	Helpers
 * @category	Helpers
 */

// --------------------------------------------------------------------

/**
 * Convenience function to easily create forms and is an alias to $this->fuel->forms->get()->render();
 * 
 * <code>
 * echo form('contact', array('javascript_validate' => FALSE, 'javascript_submit' => FALSE, 
		'fields' => array(
			'name' => array('required' => TRUE),
			'email' => array('required' => TRUE),
		)));
 * </code>
 *
 * @access	public
 * @param	string	The name of the form
 * @param	array	An array of configuration parameters
 * @return	string
 */
function form($name, $params = array())
{
	$CI =& get_instance();
	$form = $CI->fuel->forms->get($name);
	if (!$form)
	{
		$form = $CI->fuel->forms->create($name, $params);
	}
	return $form->render();
}

// --------------------------------------------------------------------

/**
 * Returns TRUE/FALSE if the current Remote IP address is blacklisted. Used during validation.
 * 
 * @access	public
 * @param	array	An array of IP addresses (optional)
 * @return	boolean
 */
function blacklisted($ips = array())
{
	$CI =& get_instance();
	if (empty($ips))
	{
		$ips = $CI->fuel->forms->config('blacklist');
	}
	return !$CI->fuel->auth->check_valid_ip($ips);
}

// --------------------------------------------------------------------

/**
 * Returns TRUE/FALSE as to whether the passed parameters get through Akismet. Used during validation.
 * 
 * @access	public
 * @param	string	The API key
 * @param	string	The name of the person submitting the form. Will pull from post
 * @param	string	The email address of the person submitting the form
 * @param	string	The message being submitted
 * @param	boolean	Determines whether to log errors or not
 * @return	boolean
 */
function validate_akismet($api_key, $name, $email, $msg, $log = TRUE)
{
	$CI =& get_instance();

	if ($api_key AND $name AND $email AND $msg)
	{
		$CI->load->module_library(FORMS_FOLDER, 'antispam/akismet');
		$akismet =& $CI->akismet;

		$akisment_comment = array(
			'author'	=> $name, //  viagra-test-123 to trigger spam
			'email'		=> $email,
			'body'		=> $msg
		);

		$config = array(
			'blog_url' => site_url(uri_string()),
			'api_key' => $api_key,
			'comment' => $akisment_comment
		);

		$akismet->init($config);
		if ( $akismet->errors_exist() AND $log)
		{				
			if ( $akismet->is_error('AKISMET_INVALID_KEY') )
			{
				log_message('error', 'AKISMET :: Theres a problem with the api key');
			}
			elseif ( $akismet->is_error('AKISMET_RESPONSE_FAILED') )
			{
				log_message('error', 'AKISMET :: Looks like the servers not responding');
			}
			elseif ( $akismet->is_error('AKISMET_SERVER_NOT_FOUND') )
			{
				log_message('error', 'AKISMET :: Wheres the server gone?');
			}
		}
		else
		{
			return !$akismet->is_spam();
		}
	}

	// if no name or email, we just return FALSE
	return FALSE;		
}

// --------------------------------------------------------------------

/**
 * Returns TRUE/FALSE as to whether the reCAPTCHA value is correct. Used during validation.
 * 
 * @access	public
 * @param	string	The private API key.
 * @return	boolean
 */
function validate_recaptcha($private_key)
{
	$resp = recaptcha_check_answer ($private_key,
									$_SERVER["REMOTE_ADDR"],
									$_POST["recaptcha_challenge_field"],
									$_POST["recaptcha_response_field"]);
	return $resp->is_valid;
}


// --------------------------------------------------------------------

/**
 * Returns TRUE/FALSE as to whether the passed parameters get through Stopforumspam.com's API. 
 * Used during validation as well as to determin entries is_spam value.
 * 
 * @access	public
 * @param	string	The username/name of the person submitting the form. Will pull from post
 * @param	string	The email address of the person submitting the form 
 * @param	string	The IP being submitted (optional)
 * @param	boolean	Determines whether to log errors or not (optional)
 * @return	boolean
 */
function validate_stopforumspam($name, $email, $ip = NULL, $thresholds = array(), $log = TRUE)
{
	$CI =& get_instance();
	if (is_array($name))
	{
		extract($name);
	}
	if (empty($ip))
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	$to_check = array(
		'username'	=> $name,
		'email'		=> $email,
		'ip'		=> $ip
	);

	// known spammer for testing...
	// $to_check['username'] = 'JHannam';
	// $to_check['email'] = 'cooneyursula4916@yahoo.com';
	// $to_check['ip'] = '23.95.105.75';

	$CI->load->module_library(FORMS_FOLDER, 'antispam/stopforumspam');
	$CI->stopforumspam->set_config($thresholds);
	$is_spam = $CI->stopforumspam->check($to_check);

	if ($CI->stopforumspam->has_errors())
	{
		if ($log)
		{
			log_message('error', 'STOPFORUMSPAM :: '.$CI->stopforumspam->last_error());	
		}
		return FALSE;
	}
	else
	{
		return !$is_spam;
	}
}