<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="g-recaptcha" data-sitekey="<?php echo $public_key; ?>"<?php echo HTML::attributes($options)?>></div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>