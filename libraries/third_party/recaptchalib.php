<?php

/**
 * The reCAPTCHA server URLs
 */
define("RECAPTCHA_API_SERVER", "https://www.google.com/recaptcha/api");

/**
 * Gets the challenge HTML (javascript and non-javascript version).
 * This is called from the browser, and the resulting reCAPTCHA HTML widget
 * is embedded within the HTML form it was called from.
 * @param string $public_key A public key for reCAPTCHA
 * @param string $theme Used to style the reCAPTCHA, can be 'light' or 'dark'
 * @return string - The HTML to be embedded in the user's form.
 */
function recaptcha_get_html($public_key, $theme = 'light')
{
	$recaptcha_script_url = RECAPTCHA_API_SERVER . '.js';
	$recaptcha_fallback_url = RECAPTCHA_API_SERVER . '/fallback?k=' . $public_key;

	return fuel_block(array(
		'module' => FORMS_FOLDER,
		'view' => 'recaptcha_view',
		'only_views' => TRUE,
		'vars' => array(
			'recaptcha_script_url' => $recaptcha_script_url,
			'recaptcha_fallback_url' => $recaptcha_fallback_url,
			'recaptcha_public_key' => $public_key,
			'recaptcha_theme' => $theme,
		)
	));
}
