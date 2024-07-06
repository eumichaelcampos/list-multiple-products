<?php
if ( ! class_exists( 'LMP_Settings' ) ) {
    class LMP_Settings {
        public static function init() {
            add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
        }

        public static function register_settings() {
            register_setting( 'lmp_options_group', 'lmp_options' );
        }
    }
}
