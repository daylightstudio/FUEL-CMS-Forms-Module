<h1>Forms Module Documentation</h1>
<p>This Forms module documentation is for version <?=FORMS_VERSION?>.</p>

<p>The Forms module provides a flexible way to create simple to complex forms. This is what you can do with it:</p>
<ul>
	<li>Create forms in the CMS, as static views or a combination of the two</li>
	<li>Use one of the additional <a href="<?=user_guide_url('general/forms')?>">custom field types</a> to combat SPAM including a <a href="http://www.dexmedia.com/blog/honeypot-technique/" target="_blank">honeypot</a>, a simple equation, <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">reCAPTCHA</a>, <a href="http://www.stopforumspam" target="_blank">stopforumspam</a> or <a href="http://akismet.com/" target="blank">Akismet</a></li>
	<li>Email specified recipients upon form submission</li>
	<li>Save entries into the database which can be exported as a CSV file</li>
	<li>Validate required and common field types like email as well as setup your own custom validation</li>
	<li>Submit and validate forms via javascript as well as server side</li>
	<li>Specify a return URL</li>
	<li>Automatically will attach uploaded files to the recipient email</li>
	<li>Hook into various parts of the processing of the email that gets sent</li>
</ul>


<h2>Hooks</h2>
<p>There are a number of hooks you can use to add additional processing functionality during the form submission process. You can pass it a callable function or an array with the first index being the object and the second being the method to execute on the object.
	Additionally, you can specify an array of <a href="http://ellislab.com/codeigniter/user-guide/general/hooks.html" target="_blank">CodeIgniter hook parameters</a>. 
	The hooks must be specified in the config file to be run upon processing and not on the object during rendering unless the form is submitting to the same page in which it is rendered.
	A hook gets passed 2 parameters&mdash;the first is the form object instance, the second is the $_POST parameters as a convenience.
	The following hooks are:
</p>
<ul>
	<li><strong>pre_process</strong>: executes before anything processes and the response email is compiled which means you can alter $_POST values if necessary</li>
	<li><strong>pre_validate</strong>: excutes right before the validation of the form fields</li>
	<li><strong>post_validate</strong>: executes after the validation of the form fields</li>
	<li><strong>pre_save</strong>: executes right before saving (if saving is enabled for the form)</li>
	<li><strong>post_save</strong>: executes right after saving (if saving is enabled for the form)</li>
	<li><strong>post_process</strong>: executes after everything is processed and before the notifications are sent (if there are any)</li>
	<li><strong>pre_notify</strong>: executes right before notifying a recipient</li>
	<li><strong>success</strong>: executes upon successful submission</li>
	<li><strong>error</strong>: executes on error</li>
</ul>

<p>If the <a href="http://ellislab.com/codeigniter/user-guide/general/hooks.html" target="_blank">CodeIgniter hook parameters</a> syntax is used, the actual hook name being used is 'form_{slug}_{hook}' (e.g. form_contact_pre_validate).</p>

<p>Below is an example of adding a hook in the configuration file of a form:</p>
<pre class="brush:php">
// you can add form configurations here which can then be referenced simply by one of the following methods form('test'), $this->fuel->forms->get('test')
$config['forms']['forms'] = array(
	'contact' => array(
				'anti_spam_method' => array('method' => 'honeypot'),
				'after_submit_text' => 'Your inquiry has been successfully sent.',
				'email_recipients' => array('superman@krypton.com'),
				'javascript_submit' => TRUE,
				'javascript_validate' => TRUE,
				'form_action' => 'forms/application',
				'submit_button_text' => 'Submit',
				'reset_button_text' => 'Reset',
				'form_display' => 'block',
				'block_view' => 'application_form',
				'fields' => array(
					'fullname' => array('required' => TRUE, 'label' => 'Full Name'),
					'phone' => array('type' => 'phone', 'required' => TRUE, 'label' => 'Phone'),
					'email' => array('type' => 'email', 'required' => TRUE),
					'comments' => array('type' => 'textarea', 'rows' => 5, 'label' => 'Comments &amp; Questions'),
					),
				'hooks' => array(
					'post_validate' => 'my_post_validate_func'
					)
	)
);
</pre>

<h2>Examples</h2>
<p>There are several ways you can create forms. One is by using the CMS. The other is by specifying the parameters for the form in the forms configuration file under the "forms" configuration are.</p>

<p>Below are some examples of how to use the module:</p>

<h3>Using the 'form' helper function</h3>
<pre class="brush:php">
// load the helper ... $CI is CI object obtained from the get_instance(); and automatically passed to blocks
$CI->load->module_helper(FORMS_FOLDER, 'forms');

echo form('myform', array('fields' => array('name' => array('required' => TRUE)));
</pre>

<p>To add the form to a page in the CMS, you will need to add the "form" function to the <dfn>$config['parser_allowed_functions']</dfn> configuration in the <span class="file">fuel/application/config/MY_fuel.php</span> file. Then you can use the templating syntax like so:</p>
<pre class="brush:php">
{form('myform')}
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

<h3>Customizing the HTML</h3>
<p>There are several ways to generate the HTML for the form. The first option is to use "auto" which will use the <a href="http://docs.getfuelcms.com/libraries/form_builder" target="_blank">Form_builder</a> class to generate the form based on
the fields you've specified. Fields can be specified in the CMS or passed in under the 'fields' parameter as demonstrated in the above example. The second option is to use a block view and the third is to simply use an HTML string.
In both cases, you will automatically have several variables passed to it included a <dfn>$fields</dfn> array which contains an array of all the the rendered fields, their labels, and their "key" values. It also will pass variables of <dfn>email_field</dfn> and <dfn>email_label</dfn> where "email" is the name of the field.
You can then use the following in your block view or HTML:
</p>

<h4>Block View</h4>
<pre class="brush:php">
&lt;?php foreach($fields as $field) : ?&gt;
<div>
	&lt;?=$field['label']?&gt;
	<p>&lt;?=$field['field']?&gt;</p>
</div>
&lt;?php endforeach; ?&gt;

<!-- OR -->
<div>
	&lt;?=$name_label?&gt;
	<p>&lt;?=$name_field?&gt;</p>
</div>
<div>
	&lt;?=$email_label?&gt;
	<p>&lt;?=$email_field?&gt;</p>
</div>
</pre>

<h4>Template Syntax</h4>
<pre class="brush:php">
<div>
	{name_label}

	<p>{name_field}</p>
</div>

<div>
	{email_label}

	<p>{email_field}</p>
</div>
</pre>

<h3>Special Hidden Fields</h3>
<p>If you are using your own HTML code, you may need to include 2 additional hidden fields with values&mdash;return_url and form_url. There is also an "__antispam__" field that 
automatically gets generated and can be outputted automatically. Below is an example of what you can include in your own blocks.</p>
<pre class="brush:php">
...
<input type="hidden" name="return_url" id="return_url" value="<?=site_url('thanks')?>">
<input type="hidden" name="form_url" id="form_url" value="<?=current_url()?>">
&lt;?=$__antispam___field?&gt;
<input type="submit" value="Send">
</pre>

<h3>Kitchen Sink</h3>
<pre class="brush:php">

$fields['name'] = array();
$fields['password'] = array('type' => 'password');
$fields['password2'] = array('type' => 'password', 'label' => 'Password verfied');

$params['name'] = 'My Form';
$params['slug'] = 'my_form';
$params['fields'] = $fields;
$params['validation'] = array('name', 'is_equal_to', 'Please make sure the passwords match', array('{password}', '{password2}')); // validation rules
$params['slug'] = 'myform'; // used for form action if none is provided to submit to forms/{slug}
$params['save_entries'] = FALSE; // saves to the form_entries table
$params['form_action'] = 'http:&#47;&#47;mysite.com/signup'; // if left blank it will be submitted automatically to forms/{slug} to be processed
$params['anti_spam_method'] = array('method' => 'recaptcha', 'recaptcha_public_key' => 'xxx', 'recaptcha_private_key' => 'xxxxx', 'theme' => 'white');
$params['submit_button_text'] = 'Submit Form';
$params['submit_button_value'] = 'Submit'; // used to determine that the form was actually submitted
$params['reset_button_text'] = 'Reset Form';
$params['form_display'] = 'auto'; // can be 'auto', 'block', 'html'
$params['block_view'] = 'form'; // fuel/application/views/_blocks/form.php
$params['block_view_module'] = 'application'; // the name of the module the block exists (default is the main application folder)
$params['javascript_submit'] = FALSE;
$params['javascript_validate'] = TRUE;
$params['javascript_waiting_message'] = 'Submitting Information...';
$params['email_recipients'] = 'superman@krypton.com';
$params['email_cc'] = 'batman@gotham.com';
$params['email_bcc'] = 'wonderwoman@paradiseisland.com';
$params['email_subject'] = 'Website Submission';
$params['email_message'] = '{name} Just signed up!';
$params['after_submit_text'] = 'You have successfully signed up.';
$params['attach_files'] = TRUE; // Will automatically attach files to the email sent out
$params['attach_file_params'] = array('upload_path' => APPPATH.'cache/', 'allowed_types' => 'pdf|doc|docx',	'max_size' => '1000'); 
$params['cleanup_attached'] = TRUE;
$params['return_url'] = 'http:&#47;&#47;mysite.com/signup'; // only works if javascript_submit = FALSE
$params['js'] = 'myvalidation.js'; // extra javascript validation can be included
$params['form_builder'] = array(); // Initialization parameters for the Form_builder class used if a form is being auto-generated
$params['hooks'] = array(); // An array of different callable functions associated with one of the predefined hooks "pre_validate", "post_validate", "pre_save", "post_save", "pre_notify", "success", "error" (e.g. 'pre_validate' => 'My_func')


$form = $this->fuel->forms->create('myform', $params);
echo $form->render();

</pre>

<h3>Use Without Database Tables</h3>
<p>If you don't want to create your forms in the CMS and/or want to capture the entries in the CMS database, you can use configure the module to work without using the tables that are automaticaly installed by the installer. To do this, add the following to your <span class="file">fuel/application/MY_fuel_modules.php</span> file:</p>
<pre class="brush:php">
$config['module_overwrites']['forms']['disabled'] = TRUE;
$config['module_overwrites']['form_entries']['disabled'] = TRUE;
</pre>

<?=generate_config_info()?>


<?=generate_toc()?>


