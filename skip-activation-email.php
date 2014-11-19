<?php
/*
Plugin Name: Skip Activation Email
Description: Skips sending activation email link when site administrators add new users to their sites
Plugin URI: http://wp.nyu.edu
Author: Neel Shah <shah.neel@nyu.edu>
Author URI: http://neelshah.info
License: GPL2
Version: 0.3
*/

if( !class_exists( 'Skip_Activation_Email_NSD' ) ) {
    class Skip_Activation_Email_NSD {

        /* Construct the plugin object  */
        public function __construct() {
            add_filter( 'wpmu_signup_user_notification', '__return_false', 1 ); // Disable confirmation email
            
            /* Add filter to remove sending activation email link when site administrators add new users to their sites */
            add_filter( 'wpmu_signup_user', array( $this, 'wpmu_signup_user_and_skip_activation_email' ), 10, 3 );
        }
        public function wpmu_signup_user_and_skip_activation_email( $user, $user_email, $meta = array() ) {
            global $wpdb;

            // Format data
            $user = preg_replace( '/\s+/', '', sanitize_user( $user, true ) );
            $user_email = sanitize_email( $user_email );
            $key = substr( md5( time() . rand() . $user_email ), 0, 16 );
            $meta = serialize($meta);

            $wpdb->insert( $wpdb->signups, array(
                'domain' => '',
                'path' => '',
                'title' => '',
                'user_login' => $user,
                'user_email' => $user_email,
                'registered' => current_time('mysql', true),
                'activation_key' => $key,
                'meta' => $meta
            ) );
            
            wpmu_activate_signup( $key );
            $destination = network_home_url();
            wp_redirect( $destination );
        }
    }
}

if( class_exists( 'Skip_Activation_Email_NSD' ) ) {
    // instantiate the plugin class
    $skip_activation_email_nsd = new Skip_Activation_Email_NSD();
}

?>