<?php
function wplc_api_email_notification(WP_REST_Request $request) {
	$return_array = array();
	if(isset($request)){
		if(isset($request['security'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['security'] === $check_token){
				if(isset($request['cid'])){
					if(isset($request['wplc_extra_data'])){
						
						$data = $request['wplc_extra_data'];

						$wplc_acbc_data = get_option("WPLC_ACBC_SETTINGS");
					    if (isset($wplc_acbc_data['wplc_pro_chat_notification']) && $wplc_acbc_data['wplc_pro_chat_notification'] == "yes") {
					    	if (isset($wplc_acbc_data['wplc_pro_chat_email_address'])) { $email_address = $wplc_acbc_data['wplc_pro_chat_email_address']; } else { $email_address = ""; }
					    	if (!$email_address || $email_address == "") { $email_address = get_option('admin_email'); }

					        $subject = sprintf( __( 'Incoming chat from %s (%s) on %s', 'wplivechat' ),
					                $data['name'],
					                $data['email'],
					                get_option('blogname')
					        );

					        $msg = sprintf( __( '%s (%s) wants to chat with you. <br /><br />Log in: %s', 'wplivechat' ),
					                $data['name'],
					                $data['email'],
					                get_option('home')."/wp-login.php"
					        );

					        wplcmail($email_address,"WP Live Chat Support", $subject, $msg);
					    }
					    return true;


					} else {
						$return_array['response'] = "No 'Data' array found (base64 encoded)";
						$return_array['code'] = "401";
						$return_array['requirements'] = array(
														"security" => "YOUR_SECRET_TOKEN",
												      	"cid"   => "Chat ID",
												      	"wplc_extra_data"   => "Data array");
					}


					
			 	} else {
					$return_array['response'] = "No 'CID' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array(
														"security" => "YOUR_SECRET_TOKEN",
												      	"cid"   => "Chat ID",
												      	"wplc_extra_data"   => "Data array");
				}
		 	} else {
				$return_array['response'] = "Nonce is invalid";
				$return_array['code'] = "401";
			}
		} else{
			$return_array['response'] = "No 'security' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array(
												"security" => "YOUR_SECRET_TOKEN",
										      	"cid"   => "Chat ID",
										      	"wplc_extra_data"   => "Data array");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array(
											"security" => "YOUR_SECRET_TOKEN",
									      	"cid"   => "Chat ID",
									      	"wplc_extra_data"   => "Data array");
	}
	
	return $return_array;


}


function wplc_api_is_typing(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['security'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['security'] === $check_token){
				if(isset($request['cid'])){
					if(isset($request['user'])){
						if(isset($request['type'])){
							if (wplc_typing($request['user'],sanitize_text_field($request['cid']),sanitize_text_field($request['type']))) {
								
								$return_array['response'] = "Successful";
								$return_array['code'] = "200";
								$return_array['data'] = array("cid" => intval($request['cid']),
															  "user" => intval($request['user']),
															  "type" => intval($request['type']));
							} else {
								$return_array['response'] = "Failed to send typing indicaator";
								$return_array['code'] = "401";
								$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
															      "cid"   => "Chat ID",
															      "user"   => "User type",
															      'type' => "TYPE");

							}
						} else {

						$return_array['response'] = "No 'type' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
													      "cid"   => "Chat ID",
													      "user"   => "User type",
													      'type' => "TYPE");
						}

				 	} else {
						$return_array['response'] = "No 'user' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
													      "cid"   => "Chat ID",
													      "user"   => "User type",
													      'type' => "TYPE");
					}
			 	} else {
					$return_array['response'] = "No 'cid' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
												      "cid"   => "Chat ID",
												      "user"   => "User type",
												      'type' => "TYPE");
				}
		 	} else {
				$return_array['response'] = "Nonce is invalid";
				$return_array['code'] = "401";
			}
		} else{
			$return_array['response'] = "No 'security' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
										      "cid"   => "Chat ID",
										      "user"   => "User type",
										      'type' => "TYPE");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
									      "cid"   => "Chat ID",
									      "user"   => "User type",
									      'type' => "TYPE");
	}
	
	return $return_array;
}