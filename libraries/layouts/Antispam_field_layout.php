<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Base_field_layout.php');

class Antispam_field_layout extends Base_field_layout {

	public $group = 'Forms';

	// --------------------------------------------------------------------
	
	/**
	 * Returns the layout's fields
	 *
	 * @access	public
	 * @return	array
	 */
	public function fields()
	{
		$fields['method'] = array('type' => 'select', 'class' => 'toggler', 'label' => 'Anti SPAM method', 'first_option' => lang('label_select_one'),
			'options' => array('stopforumspam' => 'stopforumspam', 'honeypot' => 'honeypot', 'equation' => 'equation', 'recaptcha' => 'recaptcha', 'akismet' => 'akismet'),
			'js' => '<script>
			$(function(){
				var toggler = function(elem){
					var context = $(elem).closest(".form");
					$(".recaptcha_public_key, .recaptcha_private_key, .recaptcha_theme, .akismet_api_key", context).closest("tr").hide();
					switch($(elem).val()){
						case "recaptcha":
							$(".recaptcha_public_key, .recaptcha_private_key, .recaptcha_theme", context).closest("tr").show();
							break;
						case "akismet":
							$(".akismet_api_key", context).closest("tr").show();
							break;
					}
				}
				$(".toggler").change(function(e){
					toggler(this)
				})

				toggler(".toggler");
			});
			</script>'

			);
		$fields['akismet_api_key'] = array('class' => 'akismet_api_key', 'label' => 'Akismet API key');
		$fields['recaptcha_public_key'] = array('class' => 'recaptcha_public_key', 'label' => 'reCAPTCHA public key', 'size' => 60);
		$fields['recaptcha_private_key'] = array('class' => 'recaptcha_private_key', 'label' => 'reCAPTCHA private key', 'size' => 60);
		$fields['recaptcha_theme'] = array('type' => 'select',  'label' => 'reCAPTCHA theme', 'options' => array('clean' => 'clean', 'blackglass' => 'blackglass', 'white' => 'white', 'red' => 'red'), 'class' => 'recaptcha_theme');
		$fields = $this->process_fields($fields);
		return $fields;
	}


	// --------------------------------------------------------------------

	/**
	 * Returns an array for Form_builder to use in rendering\
	 *
	 * @access	public
	 * @param	array 	Parameters for rendering
	 * @return	array
	 */	
	// function frontend_render($field_model)
	// {
	// 	$field = parent::frontend_render($field_model);
	// 	return $field;
	// }


}