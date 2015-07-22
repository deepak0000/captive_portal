<?php
/*
 * ---------------
 * 
 * Copyright (c) 2008-2015 PhoneFactor, Inc.
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

/* 
 * pf_auth.php: An SDK for authenticating with PhoneFactor.
 * version: 2.20
 */

$license_key	= '<Enter key here>';
$group_key	= '<Enter key here>';

$pfd_host	= 'pfd.phonefactor.net';
$backup_hosts	= array('pfd2.phonefactor.net');

$elementNames	= array();
$elements	= array();

// 
// pf_authenticate: authenticates using PhoneFactor.
// 
// Arguments:
//	 1) $username: The username to be authenticated.
//	 2) $phone: The phone number to PhoneFactor authenticate.
//	 3) $country_code: The country code to use for the call. Defaults to 1.
//	 4) $allow_int_calls: A boolean value that determines whether international
//		calls should be allowed. Defaults to false. Note that this only needs to 
//		be set to true if the call you are making is international, and thus could
//		cost you money. See www.phonefactor.net for the PhoneFactor rate table
//		that shows which calling zones will cost money and which are free.
//	 5) $hostname: The hostname this authentication is being sent from.
//				   defaults to 'pfsdk-hostname'
//	 6) $ip: The IP address this authentication is being sent from.
//			 defaults to '255.255.255.255'
//	 7) $ca_path: A string representing the path on disk to the folder
//		containing ca certs to validate the PhoneFactor backend against.
//		If you don't use this, the PhoneFactor backend's certificate will not
//		be validated.
//	 8) $ca_file: Similar to the ca_path parameter, except that this should
//		be the path on disk to a file containing one or more ca certificates
//		to use for validation of server certificates.
//	 9) $user_can_change_phone: If this is set to true, the users will be able to
//		change their phone number from the phone menu. If this is set to false they will
//		not be able to change the phone number.
//	10) $language: The two character code for localization.
//				  Defaults to 'en'
//	11) $mode: Specify whether to use "standard", "pin", "voiceprint",
//		"sms_two_way_otp", "sms_two_way_otp_plus_pin", "sms_one_way_otp",
//		"sms_one_way_otp_plus_pin", "phone_app_standard", or "phone_app_pin" mode
//		standard: user presses #
//		pin: user enters their pin and #
//		voiceprint: user says their passphrase and their voice is matched
//		sms_two_way_otp: user responds to text with OTP
//		sms_two_way_otp_plus_pin: user responds to text with OTP + PIN
//		sms_one_way_otp: user enters OTP in application
//		sms_one_way_otp_plus_pin: user enters OTP + PIN in application
//		phone_app_standard: user selects Authenticate in phone app
//		phone_app_pin: user enters their pin and selects Authenticate in the phone app
//				   Defaults to standard phone authentication.
//	12) $pin: The user's PIN.
//	13) $sms_text: Specify the text of the SMS message.  Use <$otp$> as
//		substitution variable for One-Time Passcode.
//	14) $extension: The extension to dial after the call is answered.
//	15) $device_token: Specify the device token of the device to send the
//		phone app notification to.
//	16) $account_name: Specify the account name to display in the phone app.
//	17) $application_name: Specify the appliation name to display on reports in the
//          PhoneFactor Management Portal.
// 18) notification_type: Specify the push notification type.
//    "apns", "c2dm", "gcm", "mpns", or "bps"
//    apns - iOS
//    c2dm - Android
//    gcm - Android
//    mpns - Windows
//    bps - Blackberry
// 
// Return value:
//	 An array containing 4 elements: A boolean value representing whether the auth
//	 was successful or not, a string representing the status of the phonecall, 
//	 a string containing an error id if the connection to the PhoneFactor backend
//	 failed and a string containing the one time password if sent.
//	 If the authentication element is a true value, then the other three
//	 elements can safely be ignored.
// 
function pf_authenticate ($username, $phone, $country_code = '1', $allow_int_calls = false,
		$hostname = 'pfsdk-hostname', $ip = '255.255.255.255', 
		$ca_path = '/etc/ssl/certs', $ca_file = '/etc/ssl/certs/cacert.pem',
		$user_can_change_phone = false, $language = 'en', $mode = 'standard',
		$pin = '', $sms_text = '', $extension = '', $device_token = '',
		$account_name = '', $application_name = '', $notification_type = '')
{

	$message = create_authenticate_message(
		$username, 
		$phone, 
		$country_code, 
		$allow_int_calls, 
		$hostname, 
		$ip,
		$user_can_change_phone,
		$language,
		$mode,
		$pin,
		$sms_text,
		$extension,
		$device_token,
		$account_name,
		$application_name,
		$notification_type);
	
	$response = send_message($message, $ca_path, $ca_file);

	return get_response_status($response);
}

// 
// reset_voiceprint: Resets a user's voiceprint
// 
// Arguments:
//	 1) $username: The username whose voiceprint will be reset.
//	 2) $hostname: The hostname this request is being sent from.
//				   defaults to 'pfsdk-hostname'
//	 3) $ip: The IP address this request is being sent from.
//			 defaults to '255.255.255.255'
// 
// Return value:
//	 An array containing 3 elements:
//		A boolean value representing whether the voiceprint reset was
//			successful or not.
//		A string containing the result
//			0 - Undefined
//			1 - Success
//			2 - Voiceprint Reset Already Set
//			3 - Server Error
//		A string containing errorId if the connection to the PhoneFactor
//			backend failed.
// 
function reset_voiceprint($username, $hostname = 'pfsdk-hostname', $ip = '255.255.255.255', 
		$ca_path = '/etc/ssl/certs', $ca_file = '/etc/ssl/certs/cacert.pem')
{
	$message = create_voiceprint_reset_message(
		$username, 
		$hostname, 
		$ip);
	
	$response = send_message($message, $ca_path, $ca_file);

	return get_voiceprint_reset_response_status($response);
}

// 
// validate_device_token: Validate a user's device token
// 
// Arguments:
//	 1) $username: The username whose device token is being validated.
//	 2) $device_token: The token of the device to validate.
//	 3) $notification_type: Specify the push notification type.
//             "apns", "c2dm", "gcm", "mpns", or "bps"
//             apns - iOS
//             c2dm - Android
//             gcm - Android
//             mpns - Windows
//             bps - Blackberry
//	 4) $account_name: Specify the account name to display in the phone app.
//	 5) $hostname: The hostname this request is being sent from.
//				   defaults to 'pfsdk-hostname'
//	 6) $ip: The IP address this request is being sent from.
//			 defaults to '255.255.255.255'
// 
// Return value:
//	 An array containing 3 elements:
//		A boolean value representing whether the token is valid. 
//		A string containing the result
//			0 - Undefined
//			1 - Device Valid
//			2 - Device Invalid
//			3 - Time Out
//			4 - User Blocked
//		A string containing errorId if the connection to the PhoneFactor
//			backend failed.
// 
function validate_device_token($username, $device_token, $notification_type = '', $account_name,
		$hostname = 'pfsdk-hostname', $ip = '255.255.255.255', 
		$ca_path = '/etc/ssl/certs', $ca_file = '/etc/ssl/certs/cacert.pem')
{
	$message = create_validate_device_token_message(
		$username, 
		$device_token, 
		$notification_type, 
		$account_name, 
		$hostname, 
		$ip);
	
	$response = send_message($message, $ca_path, $ca_file);

	return get_validate_device_token_response_status($response);
}

// 
// create_authenticate_message: generates an authenticate message to be sent
// 	to the PhoneFactor backend.
//  
// Arguments:
//	 1) $username: The username to be auth'd.
//	 2) $phone: The phone number to PhoneFactor authenticate.
//	 3) $country_code: The country code to use for the call.  defaults to 1.
//	 4) $allow_int_calls: A boolean value that determines whether international 
//		calls should be allowed. 
//	 5) $hostname: The hostname this authentication is being sent from.
//	 6) $ip: The IP address this authentication is being sent from.
//	 7) $user_can_change_phone: Can user change their phonenumber.
//	 8) $language: The two character localization code.
//	 9) $mode: Specify whether to use "standard", "pin", "voiceprint",
//		"sms_two_way_otp", "sms_two_way_otp_plus_pin", "sms_one_way_otp",
//		"sms_one_way_otp_plus_pin", "phone_app_standard", or "phone_app_pin" mode
//		standard: user presses #
//		pin: user enters their pin and #
//		voiceprint: user says their passphrase and their voice is matched
//		sms_two_way_otp: user responds to text with OTP
//		sms_two_way_otp_plus_pin: user responds to text with OTP + PIN
//		sms_one_way_otp: user enters OTP in application
//		sms_one_way_otp_plus_pin: user enters OTP + PIN in application
//		phone_app_standard: user selects Authenticate in phone app
//		phone_app_pin: user enters their pin and selects Authenticate in the phone app
//	10) $pin: The users PIN.
//	11) $sms_text: Specify the text of the SMS message.  Use <$otp$> as
//		substitution variable for One-Time Passcode.
//	12) $extension: The extension to dial after the call is answered.
//	13) $device_token: Specify the device token of the device to send the
//		phone app notification to.
//	14) $account_name: Specify the account name to display in the phone app.
//	15) $application_name: Specify the appliation name to display on reports in the
//          PhoneFactor Management Portal.
// 16) notification_type: Specify the push notification type.
//    "apns", "c2dm", "gcm", "mpns", or "bps"
//    apns - iOS
//    c2dm - Android
//    gcm - Android
//    mpns - Windows
//    bps - Blackberry
// 
// Return value:
//	 a complete authentication xml message ready to be sent to the PhoneFactor backend
// 
function create_authenticate_message ($username, $phone, $country_code, 
					$allow_int_calls, $hostname, $ip,
					$user_can_change_phone, $language,
					$mode, $pin, $sms_text, $extension,
					$device_token, $account_name,
					$application_name, $notification_type)
{
	global $license_key, $group_key;

	# SMS is the only type that requires 'mode' to be set in 'authenticationRequest'.
	$request_type = '';
	if($mode == 'sms_two_way_otp' || $mode == 'sms_two_way_otp_plus_pin'
	|| $mode == 'sms_one_way_otp' || $mode == 'sms_one_way_otp_plus_pin')
		$request_type = 'sms';
	if($mode == 'phone_app_standard' || $mode == 'phone_app_pin')
		$request_type = 'phoneApp';

	$xml = "
		<pfpMessage version='1.5'>
			<header>
				<source>
					<component type='pfsdk'>
						<host ip='$ip' hostname='$hostname'/>
					</component>
				</source>
			</header>
			<request request-id='" . rand(0, 10000) . "' language='$language'>
				<authenticationRequest " . ($request_type ? "mode='$request_type'" : "") . ">
					<customer>
						<licenseKey>
							$license_key
						</licenseKey>
						<groupKey>
							$group_key
						</groupKey>
					</customer>
					<countryCode>
						$country_code
					</countryCode>
					<authenticationType>
						pfsdk
					</authenticationType>
					<username>
						$username
					</username>
					<phonenumber userCanChangePhone='" . ($user_can_change_phone ? 'yes' : 'no') . "' extension='$extension'>
						$phone
					</phonenumber>
					<allowInternationalCalls>
						" . ($allow_int_calls ? 'yes' : 'no') . "
					</allowInternationalCalls>
					<applicationName>
						$application_name
					</applicationName>\n";

	# SMS Two-Way OTP 
	if ($mode == 'sms_two_way_otp')
	{
		$xml = $xml . "
					<smsInfo direction='two-way' mode='otp'>
					   <message><![CDATA[$sms_text]]></message>
					</smsInfo>
					<pinInfo pinMode='standard'/>\n";
	}
	# SMS Two-Way OTP+PIN 
	elseif ($mode == 'sms_two_way_otp_plus_pin')
	{
		$xml = $xml . "
					<smsInfo direction='two-way' mode='otp-pin'>
					   <message><![CDATA[$sms_text]]></message>
						<pin pinFormat='plainText'>$pin</pin>
					</smsInfo>
					<pinInfo pinMode='standard'/>\n";
	}
	# SMS One-Way OTP 
	elseif ($mode == 'sms_one_way_otp')
	{
		$xml = $xml . "
					<smsInfo direction='one-way' mode='otp'>
					   <message><![CDATA[$sms_text]]></message>
					</smsInfo>
					<pinInfo pinMode='standard'/>\n";
	}
	# SMS One-Way OTP+PIN
	elseif ($mode == 'sms_one_way_otp_plus_pin')
	{
		$xml = $xml . "
					<smsInfo direction='one-way' mode='otp-pin'>
					   <message><![CDATA[$sms_text]]></message>
						<pin pinFormat='plainText'>$pin</pin>
					</smsInfo>
					<pinInfo pinMode='standard'/>\n";
	}
	# Voiceprint
	elseif ($mode == 'voiceprint')
	{
		$xml = $xml . "
					<pinInfo pinMode='voiceprint'/>\n";
	}
	elseif ($mode == 'pin')
	{
		$xml = $xml . "
					<pinInfo pinMode='pin'>
						<pin pinFormat='plainText'>$pin</pin>
						<userCanChangePin>no</userCanChangePin>
					</pinInfo>\n";
	}
	elseif ($mode == 'phone_app_standard' || $mode == 'phone_app_pin')
	{
		if ($mode == 'phone_app_standard')
		{
			$xml = $xml . "<phoneAppAuthInfo mode='standard'>";
		}
		else
		{
			$xml = $xml . "<phoneAppAuthInfo mode='pin'>";
		}
		$xml = $xml . "
			<deviceTokens>
				<deviceToken notificationType='$notification_type'>$device_token</deviceToken> 
			</deviceTokens>
			<phoneAppAccountName>$account_name</phoneAppAccountName> 
		";
		if ($mode == 'phone_app_pin')
		{
			$xml = $xml . "
				<pin pinChangeRequired='0' pinFormat='plainText'>$pin</pin> 
				<userCanChangePin>1</userCanChangePin> 
			";
		}
		$xml = $xml . "
			<phoneAppMessages>
				<message type='authenticateButton'>Authenticate</message> 
				<message type='authenticationDenied'>PhoneFactor authentication denied.</message> 
				<message type='authenticationSuccessful'>You have successfully authenticated using PhoneFactor.</message> 
				<message type='cancelButton'>Cancel</message> 
				<message type='closeButton'>Close</message> 
				<message type='denyAndReportFraudButton'>Deny and Report Fraud</message> 
				<message type='denyButton'>Deny</message> 
				<message type='fraudConfirmationNoBlock'>Your company's fraud response team will be notified.</message> 
				<message type='fraudConfirmationWithBlock'>Your account will be blocked preventing further authentications and the company's fraud response team will be notified.</message> 
				<message type='fraudReportedNoBlock'>Fraud reported.</message> 
				<message type='fraudReportedWithBlock'>Fraud reported and account blocked.</message> 
				<message type='notification'>You have received a PhoneFactor authentication request.</message> 
				<message type='reportFraudButton'>Report Fraud</message> 
		";
		if ($mode == 'phone_app_standard')
		{
			$xml = $xml . "
				<message type='standard'>Tap Authenticate to complete your authentication.</message> 
			";
		}
		else
		{
			$xml = $xml . "
				<message type='confirmPinField'>Confirm PIN</message> 
				<message type='newPinField'>New PIN</message> 
				<message type='pin'>Enter your PIN and tap Authenticate to complete your authentication.</message> 
				<message type='pinAllSameDigits'>Your PIN cannot contain 3 or more repeating digits.</message> 
				<message type='pinExpired'>Your PIN has expired. Please enter a new PIN to complete your authentication.</message> 
				<message type='pinField'>PIN</message> 
				<message type='pinHistoryDuplicate'>Your PIN cannot be the same as one of your recently used PINs. Please choose a different PIN.</message> 
				<message type='pinLength'>Your PIN must be a minimum of 4 digits.</message> 
				<message type='pinMismatch'>New PIN and Confirm PIN must match.</message> 
				<message type='pinRetry'>Incorrect PIN. Please try again.</message> 
				<message type='pinSequentialDigits'>Your PIN cannot contain 3 or more sequential digits ascending or descending.</message> 
				<message type='pinSubsetOfPhone'>Your PIN cannot contain a 4 digit subset of your phone number or backup phone number.</message> 
				<message type='saveButton'>Save</message> 
			";
		}
		$xml = $xml . "
			</phoneAppMessages>
			</phoneAppAuthInfo>
		";
	}
	else
	{
		$xml = $xml . "
					<pinInfo pinMode='standard'/>\n";
	}

	$xml = $xml . "
				</authenticationRequest>
			</request>
		</pfpMessage>";

	return $xml;
}

// 
// create_voiceprint_reset_message: generates a voiceprint reset message to be sent
// 	to the PhoneFactor backend.
//  
// Arguments:
//	 1) $username: The username whose voiceprint will be reset.
//	 2) $hostname: The hostname this request is being sent from.
//	 3) $ip: The IP address this request is being sent from.
// 
// Return value:
//	 a complete voiceprint reset xml message ready to be sent to the PhoneFactor backend
// 
function create_voiceprint_reset_message ($username, $hostname, $ip)
{
	global $license_key, $group_key;

	$xml = "
		<pfpMessage version='1.5'>
			<header>
				<source>
					<component type='pfsdk'>
						<host ip='$ip' hostname='$hostname'/>
					</component>
				</source>
			</header>
			<request request-id='" . rand(0, 10000) . "'>
				<setPinResetRequest>
					<customer>
						<licenseKey>
							$license_key
						</licenseKey>
						<groupKey>
							$group_key
						</groupKey>
					</customer>
					<username>
						$username
					</username>
				</setPinResetRequest>
			</request>
		</pfpMessage>";

	return $xml;
}

// 
// create_validate_device_token_message: generates a validate device token message to be sent
// 	to the PhoneFactor backend.
//  
// Arguments:
//	 1) $username: The username whose device token is being validated.
//	 2) $device_token: The token of the device to validate.
//	 3) $notification_type: Specify the push notification type.
//             "apns", "c2dm", "gcm", "mpns", or "bps"
//             apns - iOS
//             c2dm - Android
//             gcm - Android
//             mpns - Windows
//             bps - Blackberry
//	 4) $account_name: Specify the account name to display in the phone app.
//	 5) $hostname: The hostname this request is being sent from.
//	 6) $ip: The IP address this request is being sent from.
// 
// Return value:
//	 a complete validate device token xml message ready to be sent to the PhoneFactor backend
// 
function create_validate_device_token_message ($username, $device_token, $notification_type, $account_name, $hostname, $ip)
{
	global $license_key, $group_key;

	$xml = "
		<pfpMessage version='1.5'>
			<header>
				<source>
					<component type='pfsdk'>
						<host ip='$ip' hostname='$hostname'/>
					</component>
				</source>
			</header>
			<request request-id='" . rand(0, 10000) . "'>
				<validateDeviceTokenRequest>
					<customer>
						<licenseKey>
							$license_key
						</licenseKey>
						<groupKey>
							$group_key
						</groupKey>
					</customer>
					<username>
						$username
					</username>
					<deviceToken notificationType='$notification_type'>
						$device_token
					</deviceToken>
					<phoneAppAccountName>
						$account_name
					</phoneAppAccountName>
				</validateDeviceTokenRequest>
			</request>
		</pfpMessage>";

	return $xml;
}

// 
// send_message: sends a message to the PhoneFactor backend
// 
// Arguments:
//	 1) $message: the message to be sent
//	 2) $ca_path: a string representing the path on disk to the folder
//		containing ca certs to validate the PhoneFactor backend against
//	 3) $ca_file: similar to the ca_path parameter, except that this should
//		be the path on disk to a file containing one or more ca certificates
//		to use for validation of server certificates
// 
// Return value:
//	 The response text from the PhoneFactor backend.  This will
//	 likely be an XML message ready to be parsed.  Note that the 
//	 return value could be NULL if the communication with the 
//	 backend was not possible.
// 
function send_message($message, $ca_path, $ca_file)
{
	global $pfd_host, $backup_hosts;

	$tries = count($backup_hosts);
	$i	 = 0;

	do
	{
		$curl = setup_curl_connection($message, $ca_path, $ca_file);

		$doc = curl_exec($curl);

		if (curl_errno($curl))
			print curl_error($curl) . "\n";
		
		if ($doc == FALSE)
		{
			array_push($backup_hosts, $pfd_host);
			$pfd_host = array_shift($backup_hosts);
			$i++;
		}
		else
			break;
	} while($i <= $tries);

	curl_close($curl);

	return $doc;
}

function setup_curl_connection($message, $ca_path, $ca_file)
{
	global $pfd_host;

	$validate = (strlen($ca_path) > 0 || strlen($ca_file) > 0 ? TRUE : FALSE);
	$curl	 = curl_init("https://$pfd_host/pfd/pfd.pl");

	$curl_options = array(
		CURLOPT_PORT		=> '443',
		CURLOPT_POST		=> true,
		CURLOPT_POSTFIELDS	=> $message,
		CURLOPT_RETURNTRANSFER 	=> TRUE,
		CURLOPT_SSL_VERIFYHOST 	=> 2,
		CURLOPT_SSL_VERIFYPEER 	=> $validate,
		CURLOPT_SSLCERT		=> dirname(__FILE__) . '/certs/cert.pem',
		CURLOPT_SSLKEY		=> dirname(__FILE__) . '/certs/pkey.pem',
	);

	if (strlen($ca_path))
		$curl_options[CURLOPT_CAPATH] = $ca_path;
	if (strlen($ca_file))
		$curl_options[CURLOPT_CAINFO] = $ca_file;

	foreach ($curl_options as $option => $value)
		curl_setopt($curl, $option, $value);
	
	return $curl;
}

// 
// startElement: handler for the beginning of an XML element
// 
// Arguments:
//	 1) $parser: a reference to the XML parser
//	 2) $name: the name of the XML element being parsed
//	 3) $attrs: the attributes found in this element
// 
// Return value:
//	 none
// 
function startElement ($parser, $name, $attrs)
{
	global $elementNames, $elements;
	$elementNames[] = "$name";

	$elements[$name]['attrs'] = array();

	foreach ($attrs as $key => $value)
	{
		$elements[$name]['attrs'][$key] = $value;
	}
}

// 
// endElement: handler for the end of an XML element
// 
// Arguments:
//	 1) $parser: a reference to the XML parser
//	 2) $name: the name of the XML element being parsed
// 
// Return value:
//	 none
// 
function endElement ($parser, $name)
{
}

// 
// characterData: handler for character data
// 
// Arguments:
//	 1) $parser: a reference to the XML parser
//	 2) $data: the character data between element tags
// 
// Return value:
//	 none
// 
function characterData ($parser, $data)
{
	global $elementNames, $elements;
	$name = array_pop($elementNames);

	$elements[$name]['data'] = trim($data);
}

// 
// get_response_status: parses the response from the PhoneFactor backend
// 
// Arguments:
//	 1) $response: the XML response string to be parsed
// 
// Return value:
//	 Same as the return value for pf_authenticate
// 
function get_response_status ($response)
{
	global $elements;

	if (!$response)
		return array(false, 0, 0);

	$disposition = false;
	$authenticated = false;
	$call_status = 0;
	$error_id = 0;
	$ret = false;
	$otp = 0;

	$xml_parser = xml_parser_create();

	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	xml_set_character_data_handler($xml_parser, 'characterData');

	xml_parse($xml_parser, $response);
	xml_parser_free($xml_parser);

	if (isset($elements['STATUS']['attrs']['DISPOSITION'])
	&& $elements['STATUS']['attrs']['DISPOSITION'] == 'success')
		$disposition = true;
	else
		$ret = false;

	if (isset($elements['AUTHENTICATED']['data'])
	&& $elements['AUTHENTICATED']['data'] == 'yes')
	{
		$authenticated = true;
		$ret = true;
	}
	else
		$ret = false;

	if (isset($elements['CALLSTATUS']['data']))
		$call_status = $elements['CALLSTATUS']['data'];

	if (isset($elements['ERROR-ID']['data']))
		$error_id = $elements['ERROR-ID']['data'];

	if (isset($elements['OTP']['data']))
		$otp = $elements['OTP']['data'];

	return array($ret, $call_status, $error_id, $otp);
}

// 
// get_voiceprint_reset_response_status: parses the response from the PhoneFactor backend
// 
// Arguments:
//	 1) $response: the XML response string to be parsed
// 
// Return value:
//	 Same as the return value for reset_voiceprint
// 
function get_voiceprint_reset_response_status ($response)
{
	global $elements;

	if (!$response)
		return array(false, 0, 0);

	$disposition = false;
	$error_id = 0;
	$ret = false;
	$otp = 0;

	$xml_parser = xml_parser_create();

	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	xml_set_character_data_handler($xml_parser, 'characterData');

	xml_parse($xml_parser, $response);
	xml_parser_free($xml_parser);

	if (isset($elements['STATUS']['attrs']['DISPOSITION'])
	&& $elements['STATUS']['attrs']['DISPOSITION'] == 'success')
		$disposition = true;
	else
		$ret = false;

	if (isset($elements['RESULT']['data']))
	{
		$result = $elements['RESULT']['data'];
		if ($result  == '1')
		{
			$ret = true;
		}
		else
		{
			$ret = false;
		}
	}

	if (isset($elements['ERROR-ID']['data']))
		$error_id = $elements['ERROR-ID']['data'];

	return array($ret, $result, $error_id);
}

// 
// get_validate_device_token_response_status: parses the response from the PhoneFactor backend
// 
// Arguments:
//	 1) $response: the XML response string to be parsed
// 
// Return value:
//	 Same as the return value for validate_device_token
// 
function get_validate_device_token_response_status ($response)
{
	global $elements;

	if (!$response)
		return array(false, 0, 0);

	$disposition = false;
	$result = 0;
	$error_id = 0;
	$ret = false;
	$otp = 0;

	$xml_parser = xml_parser_create();

	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	xml_set_character_data_handler($xml_parser, 'characterData');

	xml_parse($xml_parser, $response);
	xml_parser_free($xml_parser);

	if (isset($elements['STATUS']['attrs']['DISPOSITION'])
	&& $elements['STATUS']['attrs']['DISPOSITION'] == 'success')
		$disposition = true;
	else
		$ret = false;

	if (isset($elements['VALIDATIONRESULT']['data']))
	{
		$result = $elements['VALIDATIONRESULT']['data'];
		if ($result  == '1')
		{
			$ret = true;
		}
		else
		{
			$ret = false;
		}
	}

	if (isset($elements['ERROR-ID']['data']))
		$error_id = $elements['ERROR-ID']['data'];

	return array($ret, $result, $error_id);
}
?>
