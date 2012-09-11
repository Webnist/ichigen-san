<?php
/*
Plugin Name: Ichigen San
Plugin URI: http://plugins.webnist.jp
Description:
Author: Webnist
Version: 0.1
Author URI: http://webni.st
*/

if ( !defined( 'ICHIGEN_SAN_DOMAIN' ) )
	define( 'ICHIGEN_SAN_DOMAIN', 'ichigen-san' );

if ( !defined( 'ICHIGEN_SAN_PLUGIN_URL' ) )
	define( 'ICHIGEN_SAN_PLUGIN_URL', plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ) );

if ( !defined( 'ICHIGEN_SAN_PLUGIN_DIR' ) )
	define( 'ICHIGEN_SAN_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );

new Ichigen_San();

class Ichigen_San {

	private $version = '0.1';
	private $base_dir;
	private $plugin_dir;
	private $plugin_url;
	private $menu_slug = 'ichigen-san';

	public function __construct() {
		$this->base_dir = dirname( plugin_basename( __FILE__ ) );
		$this->plugin_dir = WP_PLUGIN_DIR . '/' . $this->base_dir;
		$this->plugin_url = WP_PLUGIN_URL . '/' . $this->base_dir;
		$this->menu_slug = 'ichigen-san';

		load_plugin_textdomain( ICHIGEN_SAN_DOMAIN, false, $this->base_dir . '/languages/' );
		add_action( 'template_redirect', array( &$this, 'redirect_ichigen_san' ) );
		if ( is_admin() ) {
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'admin_print_styles', array( &$this, 'admin_styles' ) );
			//add_action( 'admin_enqueue_scripts', array( &$this, 'admin_javascript' ) );
			add_action( 'admin_init', array( &$this, 'add_general_custom_fields' ) );
			add_filter( 'admin_init', array( &$this, 'add_custom_whitelist_options_fields' ) );
		}
	}

	public function admin_menu() {
		add_menu_page( __( 'Set Ichigen San', ICHIGEN_SAN_DOMAIN ), __( 'Set Ichigen San', ICHIGEN_SAN_DOMAIN ), 'add_users', $this->menu_slug, array( &$this, 'add_admin_edit_page' ) );
	}

	public function add_admin_edit_page() {
		$title = __( 'Set Ichigen San', ICHIGEN_SAN_DOMAIN ); ?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php echo esc_html( $title ); ?></h2>
		<form method="post" action="options.php">
		<?php settings_fields( $this->menu_slug ); ?>
		<?php do_settings_sections( $this->menu_slug ); ?>
		<table class="form-table">
		<?php do_settings_fields( $this->menu_slug, 'default' ); ?>
		</table>
		<?php submit_button(); ?>
		</form>
		</div>
	<?php }

	public function add_general_custom_fields() {
		add_settings_field( 'enabling', __( 'Enabling Ichogen San', ICHIGEN_SAN_DOMAIN ), array( &$this, 'enabling_field' ), $this->menu_slug, 'default' );
	}

	public function enabling_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_enabling' );
?>
		<label><input type="checkbox" name="ichigen_san_enabling" value="1" id="ichigen_san_enabling"<?php checked( 1, $value ); ?> /><?php _e( 'Enabling', ICHIGEN_SAN_DOMAIN ); ?></label>
	<?php
	}

	public function add_custom_whitelist_options_fields() {
		register_setting( $this->menu_slug, 'ichigen_san_enabling' );
	}

	public function admin_styles() {
		wp_enqueue_style( 'admin_ichigen_san_style', $this->plugin_url . '/css/admin-style.css' );
	}

	public function redirect_ichigen_san() {
		if ( !is_user_logged_in() && get_option( 'ichigen_san_enabling' ) ) {
			auth_redirect();
		}
	}

}
