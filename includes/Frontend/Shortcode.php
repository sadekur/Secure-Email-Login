<?php

namespace SecureEmailLogin\EmailLogin\Frontend;

class SecureEmailLogin_Shortcode {

    public function __construct() {
        add_shortcode( 'secure_email_optin', [ $this, 'secure_email_optin_form' ] );
        add_action( 'wp_footer', [ $this, 'secure_email_optin_footer' ] );
    }

    public function secure_email_optin_form() {
        $form_html = '<div class="form-container">
        <form id="thrailCrmOptinForm" action="" method="post">
            <label for="secure_email_name">' . esc_html__( 'Name:', 'secure-email-login' ) . '</label>
            <input type="text" id="secure_email_name" name="name" required placeholder="' . esc_attr__( 'Enter your name', 'secure-email-login' ) . '">
            <label for="secure_email_email">' . esc_html__( 'Email:', 'secure-email-login' ) . '</label>
            <input type="email" id="secure_email_email" name="email" required placeholder="' . esc_attr__( 'Enter your email', 'secure-email-login' ) . '">
            <input type="submit" value="' . esc_attr__( 'Subscribe', 'secure-email-login' ) . '">
        </form>
        </div>';
        return $form_html;
    }

    public function thrail_crm_optin_footer() {
        if ( ! is_admin() ) {
            echo '<div class="loader-container" id="thrailCrmFormLoader" style="display: none;">' .
                 '<img src="' . esc_url( SECURE_EMAIL_LOGIN_ASSETS . '/img/loader.gif' ) . '" alt="' . esc_attr__( 'Loading...', 'secure-email-login' ) . '">' .
                 '</div>';
        }
    }   
}
