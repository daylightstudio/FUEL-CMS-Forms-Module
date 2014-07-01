<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Forms_test extends Tester_base {
	
	public function __construct()
	{
		parent::__construct();
		$this->CI->load->module_helper(FORMS_FOLDER, 'forms');
	}
	
	public function setup()
	{


	}
	
	public function test_helper()
	{
		$html = '
		<form action="forms/myform" method="post" class="form">
		Name: {name_field}
		Email: {email_field}
		</form>
		';
		$fields['name'] = array();
		$fields['email'] = array();

		$form = form('myform', array('form_display' => 'html', 'form_html' => $html, 'fields' => $fields));
		phpQuery::newDocument($form);

		$test = pq('.form')->size() === 1;
		$expected = TRUE;
		$this->run($test, $expected, 'Test that the form tags appear with form helper function');

		
		phpQuery::newDocument($form);
		$test = pq(".form")->attr('action');
		
		$expected = 'forms/myform';
		$this->run($test, $expected, 'Test that the form tags appear with form helper function');
	}

	public function test_validation()
	{
		// test 1
		$validation_rule = array('password', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2}'));

		$fields['password'] = array('type' => 'password');
		$fields['password2'] = array('type' => 'password', 'label' => 'Password verfied');

		// manually set post values
		$_POST['password'] = 'xx';
		$_POST['password2'] = 'xxx';
		$form = $this->fuel->forms->create('myform', array('fields' => $fields, 'validation' => array($validation_rule)));
		$test = $form->validate();
		$expected = FALSE;
		$this->run($test, $expected, 'Test validation of custom validation rules passed as a parameter which should NOT be valid.');


		// test 2
		$fields['password'] = array('type' => 'password');
		$fields['password2'] = array('type' => 'password', 'label' => 'Password verfied');

		// manually set post values
		$_POST['password'] = 'xxx';
		$_POST['password2'] = 'xxx';
		$form = $this->fuel->forms->create('myform', array('fields' => $fields));
		$form->add_validation('password', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2}'));
		$test = $form->validate();
		$expected = TRUE;
		$this->run($test, $expected, 'Test validation of custom validation rules using add_validation and should be valid.');


		// test 3
		// manually set post values
		$_POST['password'] = 'xxx';
		$_POST['password2'] = 'xxx';
		$form = $this->fuel->forms->create('myform', array('fields' => $fields));
		//$form->add_validation($validation_rule);
		$form->add_validation($validation_rule);
		$test = $form->validate();
		$expected = TRUE;
		$this->run($test, $expected, 'Test validation of custom validation rules using add_validation array values and should be valid.');

	}

}
