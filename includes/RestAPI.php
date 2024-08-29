<?php
namespace SecureEmailLogin\EmailLogin;

class RestAPI {
	public function __construct() {
		add_action( 'rest_api_init', [ $this,'register_email_login_routes' ] );
		add_action( 'rest_api_init', [ $this,'register_email_secureemaillogin' ] );
	}

	public function register_email_login_routes() {
		register_rest_route('secureemaillogin/v1', '/submit-email', array(
			'methods' => 'POST',
			'callback' => [$this, 'handle_email_submission'],
			'permission_callback' => '__return_true',
		));
	}
	public function register_email_secureemaillogin() {
		register_rest_route('secureemaillogin/v1', '/verify-otp', array(
			'methods' => 'POST',
			'callback' => [$this, 'handle_otp_verification'],
			'permission_callback' => '__return_true',
		));
	}

	public function handle_email_submission(\WP_REST_Request $request) {
		$email = $request->get_param('email');
		$user = get_user_by('email', $email);
		if ($user) {
			return new \WP_REST_Response(array('userExists' => true), 200);
		} else {
			// Generate a random OTP
			$otp = rand(100000, 999999);
	
			// Store the OTP in a transient for 10 minutes
			set_transient('otp_' . $email, $otp, 10 * MINUTE_IN_SECONDS);
	
			// Send the OTP via email
			$subject = "Your Login OTP";
			$message = "Here is your OTP for login: " . $otp;
			$headers = array('Content-Type: text/html; charset=UTF-8');
			
			wp_mail($email, $subject, $message, $headers);
	
			return new \WP_REST_Response(array('userExists' => false), 200, array('Content-Type' => 'application/json'));
		}
	}	

	public function handle_otp_verification(WP_REST_Request $request) {
		$email = $request->get_param('email');
		$name = $request->get_param('name');
		$otp = $request->get_param('otp');
	
		$stored_otp = get_transient('otp_' . $email);
	
		if ($otp == $stored_otp) {
			$user_id = wp_create_user($email, wp_generate_password(), $email);
			wp_update_user(array(
				'ID' => $user_id,
				'display_name' => $name
			));
			wp_set_current_user($user_id);
			wp_set_auth_cookie($user_id);
	
			delete_transient('otp_' . $email);
	
			return new WP_REST_Response(array('success' => true), 200);
		} else {
			return new WP_REST_Response(array('success' => false, 'message' => 'Invalid OTP'), 403);
		}
	}
	
}