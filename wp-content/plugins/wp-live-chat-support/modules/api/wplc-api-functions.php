<?php 

/* Handles all functions related to the WP Live Chat Support API */

/*
 * Accepts a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Agent ID 
*/
function wplc_api_accept_chat(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['agent_id'])){
						if(wplc_change_chat_status(intval($request['chat_id']), 3, intval($request['agent_id']))){
							$return_array['response'] = "Chat accepted successfully";
							$return_array['code'] = "200";
							$return_array['data'] = array("chat_id" => intval($request['chat_id']),
														  "agent_id" => intval($request['agent_id']));
						} else {
							$return_array['response'] = "Status could not be changed";
							$return_array['code'] = "404";
						}
				 	} else {
						$return_array['response'] = "No 'agent_id' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
													      "chat_id"   => "Chat ID",
													      "agent_id"   => "Agent ID");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      "chat_id"   => "Chat ID",
												      "agent_id"   => "Agent ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      "chat_id"   => "Chat ID",
										      "agent_id"   => "Agent ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										  "chat_id"   => "Chat ID",
										  "agent_id"   => "Agent ID");
	}
	
	return $return_array;
}

/*
 * Ends a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Agent ID 
*/
function wplc_api_end_chat(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['agent_id'])){
						if(wplc_change_chat_status(intval($request['chat_id']), 1, intval($request['agent_id']))){
							$return_array['response'] = "Chat ended successfully";
							$return_array['code'] = "200";
							$return_array['data'] = array("chat_id" => intval($request['chat_id']),
														  "agent_id" => intval($request['agent_id']));
						} else {
							$return_array['response'] = "Status could not be changed";
							$return_array['code'] = "404";
						}
				 	} else {
						$return_array['response'] = "No 'agent_id' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
													      "chat_id"   => "Chat ID",
													      "agent_id"   => "Agent ID");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      "chat_id"   => "Chat ID",
												      "agent_id"   => "Agent ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      "chat_id"   => "Chat ID",
										      "agent_id"   => "Agent ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										  "chat_id"   => "Chat ID",
										  "agent_id"   => "Agent ID");
	}
	
	return $return_array;
}

/*
 * Send a message to a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Message
*/
function wplc_api_send_message(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['message'])){
			            $chat_msg = sanitize_text_field($request['message']);
			            $wplc_rec_msg = wplc_api_record_admin_message(intval($request['chat_id']),$chat_msg);
			            if ($wplc_rec_msg) {
			                $return_array['response'] = "Message sent successfully";
							$return_array['code'] = "200";
							$return_array['data'] = array("chat_id" => intval($request['chat_id']),
														  "agent_id" => intval($request['agent_id']));
			            } else {
			                $return_array['response'] = "Message not sent";
							$return_array['code'] = "404";
			            }
					} else {
						$return_array['response'] = "No 'message' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
													      	  "chat_id"   => "Chat ID",
													      	  "message" => "Message");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID",
												      	  "message" => "Message");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID",
										      	  "message" => "Message");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID",
									      	  "message" => "Message");
	}
	
	return $return_array;
}

/*
 * Fetch a chat status within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
*/
function wplc_api_get_status(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					$status = wplc_return_chat_status(intval($request['chat_id']));
					if($status){
						$return_array['response'] = "Chat status found";
						$return_array['code'] = "200";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']),
													  "status" => $status);
					} else {
						$return_array['response'] = "Chat status not found";
						$return_array['code'] = "404";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']));
					}
					

			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}

/*
 * Fetch a chat status within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * Optional:
 * - Limit (Defaults to 50/Max Limit of 50)
 * - Offset (Defaults to 0)
*/
function wplc_api_get_messages(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					$limit = 50;
					$offset = 0;
					if(isset($request['limit'])){
						$limit = intval($request['limit']);
					}
					if(isset($request['offset'])){
						$offset = intval($request['offset']);
					}

					$message_data = wplc_api_return_messages($request['chat_id'], $limit, $offset);
					
					if($message_data){
						$return_array['response'] = "Message data returned";
						$return_array['code'] = "200";
						$return_array['data'] = array("messages" => $message_data);
					} else {
						$return_array['response'] = "Messages not found";
						$return_array['code'] = "404";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']));
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}

/*
 * Fetch a chat sessions within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
*/
function wplc_api_get_sessions(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				$session_data = wplc_api_return_sessions();
				if($session_data){
					$return_array['response'] = "Sessions found";
					$return_array['code'] = "200";
					$return_array['data'] = array("sessions" => $session_data);
				} else {
					$return_array['response'] = "No sessions found";
					$return_array['code'] = "404";
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}

/*
 * Records an admin message via the API
*/
function wplc_api_record_admin_message($cid, $msg){
    global $wpdb;
    global $wplc_tblname_msgs;

    $fromname = apply_filters("wplc_filter_admin_name","Admin");
    $orig = '1';
    
    $msg = apply_filters("wplc_filter_message_control",$msg);

    $wpdb->insert( 
	$wplc_tblname_msgs, 
	array( 
            'chat_sess_id' => $cid, 
            'timestamp' => current_time('mysql'),
            'msgfrom' => $fromname,
            'msg' => $msg,
            'status' => 0,
            'originates' => $orig
	), 
	array( 
            '%s', 
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
	) 
    );
    
    wplc_update_active_timestamp(sanitize_text_field($cid));
    wplc_change_chat_status(sanitize_text_field($cid),3);
    
    return true;
}

/*
 * Returns messages from server
*/
function wplc_api_return_messages($cid, $limit, $offset){
	global $wpdb;
    global $wplc_tblname_msgs;
    
    $cid = intval($cid);
    $limit = intval($limit);
    $offset = intval($offset);

    $cdata = wplc_get_chat_data($cid);
    $wplc_settings = get_option("WPLC_SETTINGS");


    if($limit > 50){
    	$limit = 50;
    }

	$results = $wpdb->get_results(
        "
        SELECT * 
        FROM $wplc_tblname_msgs 
        WHERE `chat_sess_id` = '$cid' 
        ORDER BY `timestamp` ASC 
        LIMIT $limit OFFSET $offset 
        "
    );

    $messages = array(); //The message array
    if ($results) {
	    foreach ($results as $result) {
	    	$messages[$result->id] = array(); //Message object
			
			$messages[$result->id]['from'] = $result->msgfrom;
	        $messages[$result->id]['message'] = stripslashes($result->msg);
	        $messages[$result->id]['timestamp'] = strtotime($result->timestamp);
	        $messages[$result->id]['is_admin'] = $result->originates == 1 ? true : false;
	        
	        $gravatar = "";        
	        if($messages[$result->id]['is_admin']){
	            if (isset($cdata->other)) {
	                $other = maybe_unserialize($cdata->other);
	                if (isset($other['aid'])) {
	                    $user_info = get_userdata(intval($other['aid']));
	                    $gravatar = "//www.gravatar.com/avatar/".md5($user_info->user_email)."?s=30";    
	                }
	            } 
	        } else {
		    	if (isset($cdata->email)) {
		                $gravatar = "//www.gravatar.com/avatar/".md5($cdata->email)."?s=30";    
		        } 
	        }
	        
	        if(function_exists('wplc_decrypt_msg')){
	            $messages[$result->id]['message'] = wplc_decrypt_msg($messages[$result->id]['message']);
	        }

			$messages[$result->id]['message'] = stripslashes($messages[$result->id]['message']);
	    }
	}
    return $messages;
}


function wplc_api_return_sessions() {
    global $wpdb;
    global $wplc_tblname_chats;
    
    $results = $wpdb->get_results("SELECT * FROM $wplc_tblname_chats WHERE `status` = 3 OR `status` = 2 OR `status` = 10 OR `status` = 5 or `status` = 8 or `status` = 9 ORDER BY `timestamp` ASC");
    
    $session_array = array();
            
    if ($results) {
        foreach ($results as $result) {
            global $wplc_basic_plugin_url;
            $ip_info = maybe_unserialize($result->ip);
            $user_ip = $ip_info['ip'];
            if($user_ip == ""){
                $user_ip = __('IP Address not recorded', 'wplivechat');
            }

            $browser = wplc_return_browser_string($user_data['user_agent']);
            $browser_image = wplc_return_browser_image($browser,"16");
         
            
           	$session_array[$result->id] = array();
           
           	$session_array[$result->id]['name'] = $result->name;
           	$session_array[$result->id]['email'] = $result->email;
           
           	$session_array[$result->id]['status'] = $result->status;
           	$session_array[$result->id]['timestamp'] = wplc_time_ago($result->timestamp);

           if ((current_time('timestamp') - strtotime($result->timestamp)) < 3600) {
               $session_array[$result->id]['type'] = __("New","wplivechat");
           } else {
               $session_array[$result->id]['type'] = __("Returning","wplivechat");
           }
           
           $session_array[$result->id]['image'] = "//www.gravatar.com/avatar/".md5($result->email)."?s=30";
           $session_array[$result->id]['data']['browsing'] = $result->url;
           $path = parse_url($result->url, PHP_URL_PATH);
           
           if (strlen($path) > 20) {
                $session_array[$result->id]['data']['browsing_nice_url'] = substr($path,0,20).'...';
           } else { 
               $session_array[$result->id]['data']['browsing_nice_url'] = $path;
           }
           
           $session_array[$result->id]['data']['browser'] = $wplc_basic_plugin_url . "/images/$browser_image";
           $session_array[$result->id]['data']['ip'] = $user_ip;
        }
    }
    
    return $session_array;
}