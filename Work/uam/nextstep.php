<script src="/etc/chilli/www/chilliController.js" type="text/javascript"></script>
<?php
/*
 * ---------------
 * 
 * Copyright (c) 2008-2014 PhoneFactor, Inc.
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining  a copy of this software and associated documentation
 * files (the "Software"),  to deal in the Software without
 * restriction, including without limitation the  rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT  SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,  ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER  DEALINGS IN THE SOFTWARE.
 * 
 * ---------------
*/
	$val=0;
	$val2=1;
	setcookie("valid",$val);
	setcookie("inval",$val2);
	require('pf_auth.php');
	// note that the phone number contains no dashes, spaces, or any other
	// special characters.
	$res = pf_authenticate(
		$_COOKIE['name'],        // username
		$_COOKIE['mob'],        // phone
		'91',                 // country code (optional)
		false,               // allow international calls (optional)
		$_COOKIE['company'],      // hostname (optional)
		'255.255.255.255',   // ip (optional)
		'',    // ca path (optional) 
		'', // ca file (optional)
		false,               // user can change phone (optional) 
		'en',                // language (optional)
		'standard',          // mode (optional) -- defaults to standard mode -- see pf/pf_auth.php for a list of valid modes
		'',                  // user's pin (optional) -- required for pin, sms_two_way_otp_plus_pin, sms_one_way_otp_plus_pin, and phone_app_pin modes
		"",                  // sms text message (optional) -- required for sms modes -- must include <$otp$>, for example "<\$otp\$>\nReply with this verification code to complete your sign in verification."
		'',                  // extension (optional)
		'',                  // device token (optional) -- required for phone_app_standard and phone_app_pin modes
		'',                  // account name (optional) -- used for phone_app_standard and phone_app_pin modes
		'',                  // application name (optional)
		'');                 // notification type (optional) -- required for phone_app_standard and phone_app_pin modes -- see pf/pf_auth.php for a list of valid notification types
	
	// the return value from the above function is an array with four elements,
	// the result of the authentication (boolean), the result of the phonecall
	// itself, the result of the connection with the PhoneFactor backed, and an
	// OTP respectively.  See call_results.txt for a list of call results and
	// descriptions that correspond to the second value in the array.
	if ($res[0])
        {
           
	    $val =1;
	    $val2=0;
	    setcookie("valid",$val);
	    setcookie("inval",$val2);	
	return;	 
        }

         else
         {
	return;              
	 }
?>

