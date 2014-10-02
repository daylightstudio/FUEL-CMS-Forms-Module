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

// Table configurations
$config['tables']['forms'] = 'forms';
$config['tables']['form_entries'] = 'form_entries';