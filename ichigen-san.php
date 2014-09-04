<?php
/*
Plugin Name: Ichigen San
Plugin URI: http://wordpress.org/plugins/ichigen-san/
Description: Because you can not be viewed unless you log When you activate this plug-in, You can start a private blog. Furthermore, you can also be used for communication between the acquaintance.
Author: Webnist
Version: 0.3
Author URI: http://profiles.wordpress.org/webnist
License: GPLv2 or later
Text Domain: ichigen-san
Domain Path: /languages/
*/

if ( !defined( 'ICHIGEN_SAN_PLUGIN_URL' ) )
	define( 'ICHIGEN_SAN_PLUGIN_URL', plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ) );

if ( !defined( 'ICHIGEN_SAN_PLUGIN_DIR' ) )
	define( 'ICHIGEN_SAN_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );

class IchigenSan {

	public function __construct() {
		$data = get_file_data(
			__FILE__,
			array(
				'ver'    => 'Version',
				'domain' => 'Text Domain',
				'langs'  => 'Domain Path'
			)
		);

		$this->plugin_basename = dirname( plugin_basename(__FILE__) );
		$this->version         = $data['ver'];
		$this->domain          = $data['domain'];
		$this->langs           = $data['langs'];
		$this->enabling        = get_option( 'ichigen_san_enabling', 0 );
		$this->basic_user      = get_option( 'ichigen_san_basic_user' );
		$this->basic_pass      = get_option( 'ichigen_san_basic_pass' );

		load_plugin_textdomain( $this->domain, false, $this->plugin_basename . $this->langs );
		add_action( 'template_redirect', array( &$this, 'template_redirect' ) );
		if ( is_admin() ) {
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'admin_init', array( &$this, 'add_general_custom_fields' ) );
			add_filter( 'admin_init', array( &$this, 'add_custom_whitelist_options_fields' ) );
			add_action( 'admin_print_scripts-settings_page_' . $this->plugin_basename, array( &$this, 'admin_print_scripts' ) );
		}
	}

	public function admin_print_scripts() {
		wp_enqueue_script( 'admin_ichigen_san', ICHIGEN_SAN_PLUGIN_URL . '/js/ichigen-san.js', array( 'jquery' ), $this->version, true );
	}

	public function admin_menu() {
		add_options_page( __( 'Set Ichigen San', $this->domain ), __( 'Set Ichigen San', $this->domain ), 'add_users', $this->plugin_basename, array( &$this, 'add_admin_edit_page' ), ICHIGEN_SAN_PLUGIN_URL . '/images/icon/menu.png' );
	}

	public function add_admin_edit_page() {
		$title = __( 'Set Ichigen San', $this->domain );
		echo '<div class="wrap">' . "\n";
		screen_icon();
		echo '<h2>' . esc_html( $title ) . '</h2>' . "\n";
		echo '<form method="post" action="options.php">' . "\n";
		settings_fields( $this->plugin_basename  );
		do_settings_sections( $this->plugin_basename  );
		submit_button();
		echo '<h2>' . esc_html__( 'Setting initialization', $this->domain ) . '</h2>' . "\n";
		submit_button( __( 'Initialization', $this->domain ), 'primary', 'ichigen-san-initialization' );
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}

	public function add_general_custom_fields() {
		global $wp_version;

		add_settings_section(
			'general',
			__( 'General', $this->domain ),
			'',
			$this->domain
		);
		add_settings_field(
			'enabling',
			__( 'Enabling Ichogen San', $this->domain ),
			array( &$this, 'select_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'   => 'ichigen_san_enabling',
				'value'  => $this->enabling,
				'option' => array(
					__( 'Disabled', $this->domain ) => 0,
					__( 'Login screen', $this->domain ) => 1,
					__( 'Basic authentication', $this->domain ) => 2,
				),
			)
		);
		add_settings_field(
			'basic-user',
			__( 'Basic User', $this->domain ),
			array( &$this, 'text_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'ichigen_san_basic_user',
				'value' => $this->basic_user,
			)
		);
		add_settings_field(
			'basic-pass',
			__( 'Basic Password', $this->domain ),
			array( &$this, 'text_field' ),
			$this->plugin_basename ,
			'general',
			array(
				'name'  => 'ichigen_san_basic_pass',
				'value' => $this->basic_pass,
				'type'  => 'password',
			)
		);
	}
	public function text_field( $args ) {
		extract( $args );
		$desc   = ! empty( $desc ) ? $desc : '';
		$type   = empty( $type ) ? 'text' : esc_attr( $type );
		$value  = ( 'password' == $type ) ? '' : $value;
		$output = '<input type="' . $type . '" name="' . $name .'" id="' . $name .'" value="' . $value .'" />' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";

		echo $output;
	}

	public function textarea_field( $args ) {
		extract( $args );
		$desc   = ! empty( $desc ) ? $desc : '';
		$output = '<textarea name="' . $name .'" rows="10" cols="50" id="' . $name .'" class="large-text code">' . $value . '</textarea>' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";
		echo $output;
	}

	public function check_field( $args ) {
		extract( $args );
		$desc   = ! empty( $desc ) ? $desc : '';
		$output = '<label for="' . $name . '">' . "\n";
		$output = '<input name="' . $name . '" type="checkbox" id="' . $name . '" value="1"' . checked( $value, 1, false ) . '>' . "\n";
		if ( $desc )
			$output .= $desc . "\n";
		$output  .= '</label>' . "\n";

		echo $output;
	}

	public function select_field( $args ) {
		extract( $args );
		$desc   = ! empty( $desc ) ? $desc : '';
		$output = '<select name="' . $name . '" id="' . $name . '">' . "\n";
			foreach ( $option as $key => $val ) {
				$output .= '<option value="' . $val . '"' . selected( $value, $val, false ) . '>' . $key . '</option>' . "\n";
			}
		$output .= '</select>' . "\n";
			if ( $desc )
			$output .= $desc . "\n";

		echo $output;
	}

	public function add_custom_whitelist_options_fields() {
		register_setting( $this->plugin_basename , 'ichigen_san_enabling', 'intval' );
		register_setting( $this->plugin_basename , 'ichigen_san_basic_user', 'esc_attr' );
		register_setting( $this->plugin_basename , 'ichigen_san_basic_pass', array( &$this, 'register_setting_basic_pass_check' ) );
		register_setting( $this->plugin_basename , 'ichigen-san-convert', array( &$this, 'register_setting_convert' ) );
		register_setting( $this->plugin_basename , 'ichigen-san-initialization', array( &$this, 'register_setting_initialization' ) );
	}

	public function register_setting_basic_pass_check( $value ) {
		if ( empty( $value ) && ! empty( $this->basic_pass ) ) {
			$value = $this->basic_pass;
		} else {
			$value = wp_hash_password( $value );
		}

		return $value;
	}

	public function register_setting_initialization( $value ) {
		if ( __( 'Initialization', $this->domain ) != $value )
			return $value;

		delete_option( 'ichigen_san_enabling' );
		delete_option( 'ichigen_san_basic_user' );
		delete_option( 'ichigen_san_basic_pass' );
		return $value;
	}

	public function template_redirect() {
		if ( !is_user_logged_in() && $this->enabling == 1 ) {
			auth_redirect();
		} elseif ( !is_user_logged_in() && $this->enabling == 2 ) {
			nocache_headers();
			// Check the BASIC authentication user and password
			$user = isset($_SERVER["PHP_AUTH_USER"]) ? $_SERVER["PHP_AUTH_USER"] : '';
			$pwd  = isset($_SERVER["PHP_AUTH_PW"]) ? $_SERVER["PHP_AUTH_PW"] : '';
			if ( $this->basic_user && $this->basic_pass ) {
				if ( $user == $this->basic_user && wp_check_password( $pwd, $this->basic_pass ) ) {
					return;
				}
			}
			// Check the user/password for WordPress
			if ( !is_wp_error(wp_authenticate($user, $pwd)) ) {
				return;
			}

			// BASIC authentication is required
			header('WWW-Authenticate: Basic realm="Please Enter Your Password"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Authorization Required';
			die();
		} else {
			return;
		}
	}
}
new IchigenSan();
