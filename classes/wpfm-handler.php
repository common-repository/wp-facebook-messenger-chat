<?php

if ( !class_exists( 'WPFM_Handler' ) ) {
	/**
	 * Handles all logic for the plugin
	 */
	class WPFM_Handler extends WPFM_Module
	{
		/*
		 * Magic Methods
		 */
		protected function __construct()
		{
			$this->register_hook_callbacks();
		}
		
		/*
		 * Static Methods
		 */
		
		/**
		 * Populates the Facebook Script and HTML with data from Settings and
		 * loads it on the website when plugin is activated and footer is called.
		 */
		public static function add_facebook_messenger_capability()
		{
			$fb_app_id = get_option( 'wpfm_settings' )['general']['wpfm_fb_app_id_field'];
			$fb_page_id = get_option( 'wpfm_settings' )['general']['wpfm_page_id_field'];
			$minimise = get_option( 'wpfm_settings' )['general']['wpfm_minimise_field'][0];
			$minimise = $minimise == '1' ? "true" : "false";
			if ( $fb_app_id != '' && $fb_page_id != '' && $minimise != '' ) {
				echo '<div class="fb-customerchat" page_id="' . $fb_page_id . '" minimized="' . $minimise . '"></div>
						<script>
	                    window.fbAsyncInit = function() {
	                        FB.init({
					            appId            : \'' . $fb_app_id . '\',
					            autoLogAppEvents : true,
					            xfbml            : true,
					            version          : \'v2.11\'
	                        });
	                     };
	
					    (function(d, s, id){
					        var js, fjs = d.getElementsByTagName(s)[0];
					        if (d.getElementById(id)) {return;}
					        js = d.createElement(s); js.id = id;
					        js.src = "https://connect.facebook.net/en_US/sdk.js";
					        fjs.parentNode.insertBefore(js, fjs);
					    }(document, \'script\', \'facebook-jssdk\'));
					</script>';
			}
		}
		
		/*
		 * Instance Methods
		 */
		
		public function register_hook_callbacks()
		{
			add_action( 'init', [$this, 'init'] );
			add_action('wp_footer', __CLASS__ . '::add_facebook_messenger_capability');
		}
		
		public function activate( $network_wide )
		{
		}
		
		public function deactivate()
		{
		}
		
		public function init()
		{
		}
		
		public function upgrade( $db_version = 0 )
		{
		}
		
		/**
		 * Checks that the object is in a correct state
		 *
		 * @mvc Model
		 *
		 * @param string $property An individual property to check, or 'all' to check all of them
		 *
		 * @return bool
		 */
		protected function is_valid( $property = 'all' )
		{
			return true;
		}
	} //End WPFM_Handler
}