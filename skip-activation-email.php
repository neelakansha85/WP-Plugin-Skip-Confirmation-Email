<?php
/*
Plugin Name: Skip Activation Email
Description: Skips sending activation email link when site administrators add new users to their sites
Plugin URI: http://wp.nyu.edu
Author: Neel Shah <shah.neel@nyu.edu>
Author URI: http://neelshah.info
License: GPL2
Version: 0.1
*/

if( !class_exists( 'Skip_Activation_Email_NSD' ) ) {
    class Skip_Activation_Email_NSD {

        /* Construct the plugin object  */
        public function __construct() {
            /* Add filter to remove sending activation email link when site administrators add new users to their sites */
            add_filter( 'wpmu_signup_user', array( $this, 'wpmu_signup_user_and_skip_activation_email', 1 ) );
        }
        public function wpmu_signup_user_and_skip_activation_email( $user_name, $password, $email ) {
            $user_name = preg_replace( '/\s+/', '', sanitize_user( $user_name, true ) );

            $user_id = wp_create_user( $user_name, $password, $email );
            if ( is_wp_error( $user_id ) )
                return false;

            // Newly created users have no roles or caps until they are added to a blog.
            delete_user_option( $user_id, 'capabilities' );
            delete_user_option( $user_id, 'user_level' );

            /**
             * Fires immediately after a new user is created.
             *
             * @since MU
             *
             * @param int $user_id User ID.
             */
            do_action( 'wpmu_new_user', $user_id );

            return $user_id;
        }
    }
}

if( class_exists( 'Skip_Activation_Email_NSD' ) ) {
    // instantiate the plugin class
    $skip_activation_email_nsd = new Skip_Activation_Email_NSD();
}

?>