<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['forms'] = array(
		'forms' => 'Forms', // <--FEEL FREE TO REMOVE THIS IF YOU ARE NOT USING THE CMS TO STORE YOUR FORM INFORMATION
		'form_entries' => 'Entries', // <--FEEL FREE TO REMOVE THIS IF YOU ARE NOT SAVING FORM ENTRIES
	);


/*
|--------------------------------------------------------------------------
| ADDITIONAL SETTINGS:
|--------------------------------------------------------------------------
*/

// you can add form configurations here which can then be referenced simply by one of the following methods form('test'), $this->fuel->forms->get('test')
$config['forms']['forms'] = array(
	/*'test' => array('javascript_validate' => FALSE, 'javascript_submit' => FALSE, 
		'fields' => array(
			'name' => array('required' => TRUE),
			'email' => array('required' => TRUE),
		),
	)*/
);

// Custom fields you want to use with forms (http://docs.getfuelcms.com/general/forms#association_parameters)
$config['forms']['custom_fields'] = array();

// The default testing email address for when then application is not in production
$config['forms']['test_email'] = array();

// The default from address to use when sending email notifications
$config['forms']['email_from'] = 'Website Form Submission <website@'.$_SERVER['SERVER_NAME'].'>';

// The testing email address for when then application is not in production
$config['forms']['email_subject'] = 'Website Form';

// A list of IP addresses to block
$config['forms']['blacklist'] = array();

// Javascript files to include with each form
$config['forms']['js'] = array();

// Akismet API key if AKISMET is set for the antispam method
$config['forms']['akismet_api_key'] = '';

// Stopforumspam settings
$config['forms']['stopforumspam'] = array(
	'ip_threshold_flag'      => 5,
	'email_threshold_flag'   => 20,
	'ip_threshold_ignore'    => 20,
	'email_threshold_ignore' => 50,
);

// The fields used for SPAM checking
$config['forms']['spam_fields'] = array(
	'email_post_field'       => 'email',
	'name_post_field'        => 'name',
	'comment_post_field'     => 'comment',
);

// Save Spam to form_entries table?
$config['forms']['save_spam'] = TRUE;

// Send messages flagged as Spam to the form recipients?
$config['forms']['send_spam'] = FALSE;

// Will automatically attach any uploaded files to the email
$config['forms']['attach_files'] = TRUE;

// Attached file upload parameters
$config['forms']['attach_file_params'] = array(
	'upload_path'            => APPPATH.'cache/',
	'allowed_types'          => 'pdf|doc|docx',
	'max_size'               => '1000',
);

// Will remove attached files from the file system after being attached
$config['forms']['cleanup_attached'] = TRUE;

// Table configurations
$config['tables']['forms'] = 'forms';
$config['tables']['form_entries'] = 'form_entries';