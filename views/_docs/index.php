<h1>Forms Module Documentation</h1>
<p>This Forms module documentation is for version <?=FORMS_VERSION?>.</p>

<p>The Forms module gives users the ability to create common forms. It has the following features:</p>
<ul>
	<li>Create forms directly in the CMS or by using a block view file</li>
	<li>Provides several additional <a href="<?=user_guide_url('general/forms')?>">custom field types</a> that can be used for combatting SPAM including a <a href="http://www.dexmedia.com/blog/honeypot-technique/" target="_blank">honeypot</a>, a simple equation, <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">reCAPTCHA</a>, or <a href="http://akismet.com/" target="blank">Akismet</a></li>
	<li>Emails specified recipients upon form submission</li>
	<li>Automatically saves entries into the database which can be exported in a CSV format later</li>
	<li>Automatic validation of common fields and provides ways to add your own custom validation</li>
	<li>Both javascript submit and validation as well as server side</li>
	<li>Specify a return URL</li>
</ul>

<h2>Examples</h2>
<p>There are several ways you can create forms. One is by using the CMS. The other is by specifying the parameters for the form in the forms configuration file under the "forms" configuration are.</p>

<p>Below are some examples of how to use the module:</p>

<h3>Using the 'form' helper function</h3>
<pre class="brush:php">
// load the helper ... $CI is CI object obtained from the get_instance(); and automatically passed to blocks
$CI->load->module_helper(FORMS_FOLDER, 'forms');

echo forms('myform', array('fields' => array('name' => array('required' => TRUE)));
</pre>

<h3>Using the 'forms' fuel object</h3>
<pre class="brush:php">
$form = $this->fuel->forms->create('myform', array('fields' => array('name' => array('required' => TRUE))));
echo $form->render();
</pre>

<h3>Adding additional validation</h3>
<pre class="brush:php">
$validation = array('name', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2}'));

$fields['name'] = array();
$fields['password'] = array('type' => 'password');
$fields['password2'] = array('type' => 'password', 'label' => 'Password verfied');

$form = $this->fuel->forms->create('myform', array('fields' => $fields, 'validation' => $validation));
echo $form->render();
</pre>

<h3>Kitchen Sink</h3>
<pre class="brush:php">

$fields['name'] = array();
$fields['password'] = array('type' => 'password');
$fields['password2'] = array('type' => 'password', 'label' => 'Password verfied');

$params['fields'] = $fields;
$params['validation'] = array('name', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2}')); // validation rules
$params['slug'] = 'myform'; // used for form action if none is provided to submit to forms/{slug}
$params['save_entries'] = FALSE; // saves to the form_entries table
$params['form_action'] = 'http://mysite.com/signup'; // if left blank it will be submitted automatically to forms/{slug} to be processed
$params['anti_spam_method'] = array('method' => 'recaptcha', 'recaptcha_public_key' => 'xxx', 'recaptcha_private_key' => 'xxxxx', 'theme' => 'white');
$params['submit_button_text'] = 'Submit Form';
$params['reset_button_text'] = 'Reset Form';
$params['form_display'] = 'auto'; // can be 'auto', 'block', 'html'
$params['block_view'] = 'form'; // fuel/application/views/_blocks/form.php
$params['block_view_module'] = 'application'; // the name of the module the block exists (default is the main application folder)
$params['javascript_submit'] = FALSE;
$params['javascript_validate'] = TRUE;
$params['javascript_waiting_message'] = 'Submitting Information...';
$params['email_recipients'] = 'superman@krypton';
$params['email_subject'] = 'Website Submission';
$params['email_message'] = '{name} Just signed up!';
$params['after_submit_text'] = 'You have successfully signed up.';
$params['return_url'] = 'http://mysite.com/signup'; // only works if javascript_submit = FALSE
$params['js'] = 'myvalidation.js'; // extra javascript validation can be included

$form = $this->fuel->forms->create('myform', $params);
echo $form->render();

</pre>
<?=generate_config_info()?>


<?=generate_toc()?>


