<?php

/**
 * The reCAPTCHA server URL's
 */
define("WPPB_RECAPTCHA_API_SERVER", "http://www.google.com/recaptcha/api");
define("WPPB_RECAPTCHA_API_SECURE_SERVER", "https://www.google.com/recaptcha/api");
define("WPPB_RECAPTCHA_VERIFY_SERVER", "www.google.com");

/**
 * Encodes the given data into a query string format
 * @param $data - array of string elements to be encoded
 * @return string - encoded request
 */
function _wppb_recaptcha_qsencode ($data) {
        $req = "";
        foreach ( $data as $key => $value )
			$req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

        // Cut the last '&'
        $req=substr($req,0,strlen($req)-1);
        return $req;
}



/**
 * Submits an HTTP POST to a reCAPTCHA server
 * @param string $host
 * @param string $path
 * @param array $data
 * @param int port
 * @return array response
 */
function _wppb_recaptcha_http_post($host, $path, $data, $port = 80) {

        $req = _wppb_recaptcha_qsencode ($data);

        $http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($req) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;

        $response = '';
        if( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) )
			echo $errorMessage = '<span class="error">'. __('Could not open socket!', 'profilebuilder') .'</span><br/><br/>';

        fwrite($fs, $http_request);

        while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
        fclose($fs);
        $response = explode("\r\n\r\n", $response, 2);

        return $response;
}



/**
 * Gets the challenge HTML (javascript and non-javascript version).
 * This is called from the browser, and the resulting reCAPTCHA HTML widget
 * is embedded within the HTML form it was called from.
 * @param string $pubkey A public key for reCAPTCHA
 * @param string $error The error given by reCAPTCHA (optional, default is null)
 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

 * @return string - The HTML to be embedded in the user's form.
 */
function wppb_recaptcha_get_html ($pubkey, $error = null, $use_ssl = false){

	if ($pubkey == null || $pubkey == '')
		echo $errorMessage = '<span class="error">'. __("To use reCAPTCHA you must get an API key from", "profilebuilder:"). " <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a></span><br/><br/>";
	
	if ($use_ssl)
		$server = WPPB_RECAPTCHA_API_SECURE_SERVER;
	else
		$server = WPPB_RECAPTCHA_API_SERVER;

        $errorpart = "";
        if ($error) {
           $errorpart = "&amp;error=" . $error;
        }
        return '<script type="text/javascript" src="'. $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

	<noscript>
  		<iframe src="'. $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
  		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
  		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>';
}




/**
 * A wppb_ReCaptchaResponse is returned from wppb_recaptcha_check_answer()
 */
class wppb_ReCaptchaResponse {
	var $is_valid;
	var $error;
}


/**
  * Calls an HTTP POST function to verify if the user's guess was correct
  * @param string $privkey
  * @param string $remoteip
  * @param string $challenge
  * @param string $response
  * @param array $extra_params an array of extra variables to post to the server
  * @return wppb_ReCaptchaResponse
  */
function wppb_recaptcha_check_answer ($privkey, $remoteip, $challenge, $response, $extra_params = array()){

	if ($privkey == null || $privkey == '')
		//echo $errorMessage = '<span class="error">'. __("To use reCAPTCHA you must get an API key from", "profilebuilder:"). " <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a></span><br/><br/>";

	if ($remoteip == null || $remoteip == '')
		echo $errorMessage = '<span class="error">'. __("For security reasons, you must pass the remote ip to reCAPTCHA!", "profilebuilder") .'</span><br/><br/>';
	
	
        //discard spam submissions
        if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
			$recaptcha_response = new wppb_ReCaptchaResponse();
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = 'incorrect-captcha-sol';
			
			return $recaptcha_response;
        }

        $response = _wppb_recaptcha_http_post (WPPB_RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify",
                                          array (
                                                 'privatekey' => $privkey,
                                                 'remoteip' => $remoteip,
                                                 'challenge' => $challenge,
                                                 'response' => $response
                                                 ) + $extra_params
                                          );

        $answers = explode ("\n", $response [1]);
        $recaptcha_response = new wppb_ReCaptchaResponse();

        if (trim ($answers [0]) == 'true') {
                $recaptcha_response->is_valid = true;
        }
        else {
                $recaptcha_response->is_valid = false;
                $recaptcha_response->error = $answers [1];
        }
        return $recaptcha_response;

}

/**
 * gets a URL where the user can sign up for reCAPTCHA. If your application
 * has a configuration page where you enter a key, you should provide a link
 * using this function.
 * @param string $domain The domain where the page is hosted
 * @param string $appname The name of your application
 */
function wppb_recaptcha_get_signup_url ($domain = null, $appname = null) {

	return "https://www.google.com/recaptcha/admin/create?" .  _wppb_recaptcha_qsencode (array ('domains' => $domain, 'app' => $appname));
}

function _wppb_recaptcha_aes_pad($val) {
	$block_size = 16;
	$numpad = $block_size - (strlen ($val) % $block_size);
	return str_pad($val, strlen ($val) + $numpad, chr($numpad));
}

/* Mailhide related code */

function _wppb_recaptcha_aes_encrypt($val,$ky) {
	if (! function_exists ("mcrypt_encrypt"))
		echo $errorMessage = '<span class="error">'. __("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed!", "profilebuilder") .'</span><br/><br/>';
		
	$mode=MCRYPT_MODE_CBC;   
	$enc=MCRYPT_RIJNDAEL_128;
	$val=_wppb_recaptcha_aes_pad($val);
	return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
}


function _wppb_wppb_recaptcha_mailhide_urlbase64 ($x) {
	return strtr(base64_encode ($x), '+/', '-_');
}

/* gets the reCAPTCHA Mailhide url for a given email, public key and private key */
function wppb_recaptcha_mailhide_url($pubkey, $privkey, $email) {
	if ($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null)
		echo $errorMessage = '<span class="error">'. __("To use reCAPTCHA Mailhide, you have to sign up for a public and private key; you can do so at", "profilebuilder"). " <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a></span><br/><br/>";
	

	$ky = pack('H*', $privkey);
	$cryptmail = _wppb_recaptcha_aes_encrypt ($email, $ky);
	
	return "http://www.google.com/recaptcha/mailhide/d?k=" . $pubkey . "&c=" . _wppb_wppb_recaptcha_mailhide_urlbase64 ($cryptmail);
}

/**
 * gets the parts of the email to expose to the user.
 * eg, given johndoe@example,com return ["john", "example.com"].
 * the email is then displayed as john...@example.com
 */
function _wppb_recaptcha_mailhide_email_parts ($email) {
	$arr = preg_split("/@/", $email );

	if (strlen ($arr[0]) <= 4) {
		$arr[0] = substr ($arr[0], 0, 1);
	} else if (strlen ($arr[0]) <= 6) {
		$arr[0] = substr ($arr[0], 0, 3);
	} else {
		$arr[0] = substr ($arr[0], 0, 4);
	}
	return $arr;
}

/**
 * Gets html to display an email address given a public an private key.
 * to get a key, go to:
 *
 * http://www.google.com/recaptcha/mailhide/apikey
 */
function wppb_recaptcha_mailhide_html($pubkey, $privkey, $email) {
	$emailparts = _wppb_recaptcha_mailhide_email_parts ($email);
	$url = wppb_recaptcha_mailhide_url ($pubkey, $privkey, $email);
	
	return htmlentities($emailparts[0]) . "<a href='" . htmlentities ($url) .
		"' onclick=\"window.open('" . htmlentities ($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities ($emailparts [1]);

}



function wppb_reCaptcha(){
	//first thing we will have to do is create a default settings on first-time run of the addon
	$reCaptchaSettings = get_option('reCaptchaSettings','not_found');
	if ($reCaptchaSettings == 'not_found'){
		$reCaptchaSettings = array('publicKey' => '', 'privateKey' => '');
		add_option('reCaptchaSettings', $reCaptchaSettings);
	}
?>
	
	<form method="post" action="options.php#wppb_reCaptcha">
		<?php $reCaptchaSettings = get_option('reCaptchaSettings'); ?>
		<?php settings_fields('reCaptchaSettings'); ?>

		
		
		<h2><?php _e('reCAPTCHA', 'profilebuilder');?></h2>
		<h3><?php _e('reCAPTCHA', 'profilebuilder');?></h3>


		<p>
			<?php _e('Adds a reCAPTCHA form on the registration page created in the front-end (only).', 'profilebuilder');?><br/>
			<?php _e('For this you must get a public and private key from Google:', 'profilebuilder');?> <a href="http://www.google.com/recaptcha" target="new">www.google.com/recaptcha</a>
		</p>
		
		<table class="redirectTable">
			<thead class="disableLoginAndRegistrationTableHead">
				<tr>
					<th class="manage-column" scope="col"><?php _e('Key', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('Code', 'profilebuilder');?></th>
				</tr>
			</thead>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1"><?php _e('Public Key:', 'profilebuilder');?></td>
				<td class="redirectTableCell2"><input name="reCaptchaSettings[publicKey]" class="reCaptchaSettingsPubK" type="password" value="<?php echo $reCaptchaSettings['publicKey'];?>" /></td>
			</tr>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1"><?php _e('Private Key:', 'profilebuilder');?></td>
				<td class="redirectTableCell2"><input name="reCaptchaSettings[privateKey]" class="reCaptchaSettingsPriK" type="password" value="<?php echo $reCaptchaSettings['privateKey'];?>" /></td>
			</tr>
		</table>
	
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</form>
	</div>
	
<?php
}


/* the function to display error message on the registration page */
function wppb_add_captcha_error_message(){

	$reCaptchaSettings = get_option('reCaptchaSettings', 'not_found');
	if ($reCaptchaSettings == 'not_found'){
		$publickey = ""; 
		$privatekey = "";

	}else{
		$publickey = trim($reCaptchaSettings['publicKey']); 
		$privatekey = trim($reCaptchaSettings['privateKey']);
	}
	
 
	$resp = wppb_recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
 
	if (!empty($_POST)){
		if (!$resp->is_valid){  // What happens when the CAPTCHA was entered incorrectly
			if ($publickey != '' || $publickey != null || $privatekey != '' || $privatekey != null)  //make sure the user doesn't get a double error on registration
				return __("The reCAPTCHA wasn't entered correctly. Go back and try it again!", "profilebuilder");
		}else{  // Your code here to handle a successful verification
			return '';
		}
	}
 
}
$wppb_addon_settings = get_option('wppb_addon_settings');
if ($wppb_addon_settings['wppb_reCaptcha'] == 'show')
	add_filter('wppb_register_extra_error', 'wppb_add_captcha_error_message');
 
 
 
/* the function to add recaptcha to the registration form o PB */
function wppb_add_recaptcha_to_registration_form () {

	$reCaptchaSettings = get_option('reCaptchaSettings', 'not_found');
	if ($reCaptchaSettings == 'not_found'){
		$publickey = ""; 
		$privatekey = "";

	}else{
		$publickey = trim($reCaptchaSettings['publicKey']); 
		$privatekey = trim($reCaptchaSettings['privateKey']);
	}

	return wppb_recaptcha_get_html($publickey);	
}