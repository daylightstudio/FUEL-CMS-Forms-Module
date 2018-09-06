<script src="<?php echo $recaptcha_script_url; ?>" async defer></script>
<div
		class="g-recaptcha"
		data-sitekey="<?php echo $recaptcha_public_key; ?>"
		data-theme="<?php echo $recaptcha_theme; ?>">
</div>
<noscript>
	<div>
		<div style="width: 302px; height: 422px; position: relative;">
			<div style="width: 302px; height: 422px; position: absolute;">
				<iframe src="<?php echo $recaptcha_fallback_url; ?>"
						frameborder="0" scrolling="no"
						style="width: 302px; height:422px; border-style: none;">
				</iframe>
			</div>
		</div>
		<div style="width: 300px; height: 60px; border-style: none;
					   bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;
					   background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
		  <textarea id="g-recaptcha-response" name="g-recaptcha-response"
					class="g-recaptcha-response"
					style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
							  margin: 10px 25px; padding: 0px; resize: none;">
		  </textarea>
		</div>
	</div>
</noscript>