<?php
/*
Plugin Name: Ichigen San
Plugin URI: http://plugins.webnist.jp
Description:
Author: Webnist
Version: 0.1.7
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

	private $version = '0.1.7';
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
		add_menu_page( __( 'Set Ichigen San', ICHIGEN_SAN_DOMAIN ), __( 'Set Ichigen San', ICHIGEN_SAN_DOMAIN ), 'add_users', $this->menu_slug, array( &$this, 'add_admin_edit_page' ), $this->plugin_url . '/images/icon/menu.png' );
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
		add_settings_field( 'feed', __( 'To allow the feed', ICHIGEN_SAN_DOMAIN ), array( &$this, 'allow_feed_field' ), $this->menu_slug, 'default' );
		add_settings_field( 'maintenance_enabling', __( 'To set up maintenance page', ICHIGEN_SAN_DOMAIN ), array( &$this, 'maintenance_enabling_field' ), $this->menu_slug, 'default' );
		add_settings_field( 'maintenance_title', __( 'Maintenance page title', ICHIGEN_SAN_DOMAIN ), array( &$this, 'maintenance_title_field' ), $this->menu_slug, 'default' );
		add_settings_field( 'maintenance_text', __( 'Maintenance page text', ICHIGEN_SAN_DOMAIN ), array( &$this, 'maintenance_text_field' ), $this->menu_slug, 'default' );
		//add_settings_field( 'maintenance_date', __( 'Maintenance page date', ICHIGEN_SAN_DOMAIN ), array( &$this, 'maintenance_date_field' ), $this->menu_slug, 'default' );
	}

	public function enabling_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_enabling' );
?>
		<label><input type="checkbox" name="ichigen_san_enabling" value="1" id="ichigen_san_enabling"<?php checked( 1, $value ); ?> /><?php _e( 'Enabling', ICHIGEN_SAN_DOMAIN ); ?></label>
	<?php
	}

	public function allow_feed_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_allow_feed' );
?>
		<label><input type="checkbox" name="ichigen_san_allow_feed" value="1" id="ichigen_san_allow_feed"<?php checked( 1, $value ); ?> /><?php _e( 'Allow feed', ICHIGEN_SAN_DOMAIN ); ?></label>
	<?php
	}
	public function maintenance_enabling_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_maintenance_enabling' );
?>
		<label><input type="checkbox" name="ichigen_san_maintenance_enabling" value="1" id="ichigen_san_maintenance_enabling"<?php checked( 1, $value ); ?> /><?php _e( 'Enabling', ICHIGEN_SAN_DOMAIN ); ?></label>
	<?php
	}
	public function maintenance_title_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_maintenance_title' ) ? get_option( 'ichigen_san_maintenance_title' ) : __( 'Maintenance', 'ichigen_san' ); ?>
		<label><input type="text" name="ichigen_san_maintenance_title" value="<?php echo $value; ?>" id="ichigen_san_maintenance_title" /></label>
	<?php
	}
	public function maintenance_text_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_maintenance_text' ) ? get_option( 'ichigen_san_maintenance_text' ) : __( 'It is currently undergoing maintenance', 'ichigen_san' );
?>
		<label><?php wp_editor( $value, 'ichigen_san_maintenance_text' ); ?></label>
	<?php
	}
	public function maintenance_date_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_maintenance_date' ) ? get_option( 'ichigen_san_maintenance_date' ) : date_i18n( 'Y/m/d' ) ;
?>
		<label><input type="text" name="ichigen_san_maintenance_date" value="<?php echo $value; ?>" id="ichigen_san_maintenance_date" /></label>
	<?php
	}

	public function add_custom_whitelist_options_fields() {
		register_setting( $this->menu_slug, 'ichigen_san_enabling' );
		register_setting( $this->menu_slug, 'ichigen_san_allow_feed' );
		register_setting( $this->menu_slug, 'ichigen_san_maintenance_enabling', array( &$this, 'add_maintenance' ) );
		register_setting( $this->menu_slug, 'ichigen_san_maintenance_title' );
		register_setting( $this->menu_slug, 'ichigen_san_maintenance_text' );
		//register_setting( $this->menu_slug, 'ichigen_san_maintenance_date' );
	}

	public function add_maintenance() {
		$value = isset( $_POST['ichigen_san_maintenance_enabling'] ) ? $_POST['ichigen_san_maintenance_enabling'] : null;
		if ( $value ) {
			$title = isset( $_POST['ichigen_san_maintenance_title'] ) ? $_POST['ichigen_san_maintenance_title'] : null;
			$text = isset( $_POST['ichigen_san_maintenance_text'] ) ? $_POST['ichigen_san_maintenance_text'] : null;
			$date = isset( $_POST['ichigen_san_maintenance_date'] ) ? $_POST['ichigen_san_maintenance_date'] : null;
			$id = get_option( 'ichigen_san_maintenance_id' ) ? get_option( 'ichigen_san_maintenance_id' ) : null;
			$args = array(
				'ID' => $id,
				'post_content' => esc_html( stripslashes( $text ) ),
				'post_title' => esc_html( stripslashes( $title ) ),
				'post_status' => 'publish', 
				'post_name' => 'maintenance',
				'menu_order' => 9999,
				'post_type' => 'page',
			);
			if ( $id ) {
				wp_update_post( $args );
			} else {
				$id = wp_insert_post( $args );
				update_option( 'ichigen_san_maintenance_id', $id );
			}
		} else {
			$id = get_option( 'ichigen_san_maintenance_id' ) ? get_option( 'ichigen_san_maintenance_id' ) : null;
			$args = array(
				'ID' => $id,
				'post_status' => 'draft', 
			);
			wp_update_post( $args );
		}
		return $value;
	}

	public function admin_styles() {
		wp_enqueue_style( 'admin_ichigen_san_style', $this->plugin_url . '/css/admin-style.css' );
	}

	public function redirect_ichigen_san() {
		if ( get_option( 'ichigen_san_allow_feed' ) && is_feed() )
			return;

		if ( !is_user_logged_in() && get_option( 'ichigen_san_maintenance_enabling' ) ) {
			$maintenance_id = get_option( 'ichigen_san_maintenance_id' );
			if ( !is_page( $maintenance_id ) ) {
				$maintenance_id = get_option( 'ichigen_san_maintenance_id' );
				$redirect_url = get_permalink( $maintenance_id );
				wp_redirect( $redirect_url );
				exit();
			}
		} elseif ( !is_user_logged_in() && get_option( 'ichigen_san_enabling' ) ) {
			auth_redirect();
		}
	}

}
