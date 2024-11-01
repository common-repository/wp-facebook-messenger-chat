<?php

if ( !class_exists( 'WPFM_Settings' ) ) {
	
	/**
	 * Handles plugin settings and user profile meta fields
	 */
	class WPFM_Settings extends WPFM_Module
	{
		protected $settings;
		protected static $default_settings;
		protected static $readable_properties = ['settings'];
		protected static $writeable_properties = ['settings'];
		
		const REQUIRED_CAPABILITY = 'administrator';
		
		
		/*
		 * General methods
		 */
		
		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		protected function __construct()
		{
			$this->register_hook_callbacks();
		}
		
		/**
		 * Public setter for protected variables
		 *
		 * Updates settings outside of the Settings API or other subsystems
		 *
		 * @mvc Controller
		 *
		 * @param string $variable
		 * @param array  $value This will be merged with WPFM_Settings->settings, so it should mimic the structure of the WPFM_Settings::$default_settings. It only needs the contain the values that will change, though. See WordPress_Facebook_Messenger->upgrade() for an example.
		 */
		public function __set( $variable, $value )
		{
			// Note: WPFM_Module::__set() is automatically called before this
			
			if ( $variable != 'settings' ) {
				return;
			}
			
			$this->settings = self::validate_settings( $value );
			update_option( 'wpfm_settings', $this->settings );
		}
		
		/**
		 * Register callbacks for actions and filters
		 *
		 * @mvc Controller
		 */
		public function register_hook_callbacks()
		{
			add_action( 'admin_menu', __CLASS__ . '::register_settings_pages' );
			add_action( 'show_user_profile', __CLASS__ . '::add_user_fields' );
			add_action( 'edit_user_profile', __CLASS__ . '::add_user_fields' );
			add_action( 'personal_options_update', __CLASS__ . '::save_user_fields' );
			add_action( 'edit_user_profile_update', __CLASS__ . '::save_user_fields' );
			
			add_action( 'init', [$this, 'init'] );
			add_action( 'admin_init', [$this, 'register_settings'] );
			
			add_filter(
				'plugin_action_links_' . plugin_basename( dirname( __DIR__ ) ) . '/bootstrap.php',
				__CLASS__ . '::add_plugin_action_links'
			);
		}
		
		/**
		 * Prepares site to use the plugin during activation
		 *
		 * @mvc Controller
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide )
		{
		}
		
		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @mvc Controller
		 */
		public function deactivate()
		{
		}
		
		/**
		 * Initializes variables
		 *
		 * @mvc Controller
		 */
		public function init()
		{
			self::$default_settings = self::get_default_settings();
			$this->settings = self::get_settings();
		}
		
		/**
		 * Executes the logic of upgrading from specific older versions of the plugin to the current version
		 *
		 * @mvc Model
		 *
		 * @param string $db_version
		 */
		public function upgrade( $db_version = 0 )
		{
			/*
			if( version_compare( $db_version, 'x.y.z', '<' ) )
			{
				// Do stuff
			}
			*/
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
			// Note: __set() calls validate_settings(), so settings are never invalid
			
			return true;
		}
		
		
		/*
		 * Plugin Settings
		 */
		
		/**
		 * Establishes initial values for all settings
		 *
		 * @mvc Model
		 *
		 * @return array
		 */
		protected static function get_default_settings()
		{
			$general = [
				'wpfm_fb_app_id_field' => '',
				'wpfm_page_id_field' => '',
				'wpfm_minimise_field' => ''
			];
			
			return [
				'db-version' => '0',
				'general' => $general
			];
		}
		
		/**
		 * Retrieves all of the settings from the database
		 *
		 * @mvc Model
		 *
		 * @return array
		 */
		protected static function get_settings()
		{
			$settings = shortcode_atts(
				self::$default_settings,
				get_option( 'wpfm_settings', [] )
			);
			
			return $settings;
		}
		
		/**
		 * Adds links to the plugin's action link section on the Plugins page
		 *
		 * @mvc Model
		 *
		 * @param array $links The links currently mapped to the plugin
		 *
		 * @return array
		 */
		public static function add_plugin_action_links( $links )
		{
			array_unshift( $links, '<a href="options-general.php?page=' . 'wpfm_settings">Settings</a>' );
			
			return $links;
		}
		
		/**
		 * Adds pages to the Admin Panel menu
		 *
		 * @mvc Controller
		 */
		public static function register_settings_pages()
		{
			add_submenu_page(
				'options-general.php',
				WPFM_NAME . ' Settings',
				WPFM_NAME,
				self::REQUIRED_CAPABILITY,
				'wpfm_settings',
				__CLASS__ . '::markup_settings_page'
			);
		}
		
		/**
		 * Creates the markup for the Settings page
		 *
		 * @mvc Controller
		 */
		public static function markup_settings_page()
		{
			if ( current_user_can( self::REQUIRED_CAPABILITY ) ) {
				echo self::render_template( 'wpfm-settings/page-settings.php' );
			}else {
				wp_die( 'Access denied.' );
			}
		}
		
		/**
		 * Registers settings sections, fields and settings
		 *
		 * @mvc Controller
		 */
		public function register_settings()
		{
			add_settings_section(
				'wpfm_section-general',
				'Plugin Settings',
				__CLASS__ . '::markup_section_headers',
				'wpfm_settings'
			);
			
			add_settings_field(
				'wpfm_fb_app_id_field',
				'Facebook App ID',
				[$this, 'markup_fields'],
				'wpfm_settings',
				'wpfm_section-general',
				[
					'label_for' => 'wpfm_fb_app_id_field',
					'type' => 'text',
					'description' => 'Enter the ID for the Facebook App created for this website.',
					'link' => '<a href="https://developers.facebook.com/apps/">Create App and Obtain ID</a>'
				]
			);
			
			add_settings_field(
				'wpfm_page_id_field',
				'Facebook Page ID',
				[$this, 'markup_fields'],
				'wpfm_settings',
				'wpfm_section-general',
				[
					'label_for' => 'wpfm_page_id_field',
					'type' => 'text',
					'description' => 'Enter the ID of the Facebook Page you wish users to communicate with.',
					'link' => '<a href="https://findmyfbid.com/">Obtain ID</a>'
				]
			);
			
			add_settings_field(
				'wpfm_minimise_field',
				'Minimise Messenger Window',
				[$this, 'markup_fields'],
				'wpfm_settings',
				'wpfm_section-general',
				[
					'label_for' => 'wpfm_minimise_field',
					'type' => 'checkbox',
					'options' => [
						'1' => ''
					],
					'description' => 'Selecting this option will not show a welcome message to the clients.',
				]
			);
			
			register_setting(
				'wpfm_settings',
				'wpfm_settings',
				[$this, 'validate_settings']
			);
		}
		
		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @mvc Controller
		 *
		 * @param array $section
		 */
		public static function markup_section_headers( $section )
		{
			echo self::render_template( 'wpfm-settings/page-settings-section-headers.php', ['section' => $section], 'always' );
		}
		
		/**
		 * Delivers the markup for settings fields
		 *
		 * @mvc Controller
		 *
		 * @param array $field
		 */
		public function markup_fields( $field )
		{
			echo self::render_template( 'wpfm-settings/page-settings-fields.php', ['settings' => $this->settings, 'field' => $field], 'always' );
		}
		
		/**
		 * Validates submitted setting values before they get saved to the database. Invalid data will be overwritten with defaults.
		 *
		 * @mvc Model
		 *
		 * @param array $new_settings
		 *
		 * @return array
		 */
		public function validate_settings( $new_settings )
		{
			$new_settings = shortcode_atts( $this->settings, $new_settings );
			
			if ( !is_string( $new_settings['db-version'] ) ) {
				$new_settings['db-version'] = WP_Facebook_Messenger_Chat::VERSION;
			}
			
			return $new_settings;
		}
	} // end WPFM_Settings
}
