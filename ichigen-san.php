<?php
/*
Plugin Name: Ichigen San
Plugin URI: http://plugins.webnist.jp
Description:
Author: Webnist
Version: 0.2
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

	private $version = '0.2';
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
		add_settings_field( 'ichigen_san_page', __( 'Page Setting', ICHIGEN_SAN_DOMAIN ), array( &$this, 'page_field' ), $this->menu_slug, 'default' );
		add_settings_field( 'ichigen_san_user', __( 'Basic User', ICHIGEN_SAN_DOMAIN ), array( &$this, 'user_field' ), $this->menu_slug, 'default' );
		add_settings_field( 'ichigen_san_pass', __( 'Basic Password', ICHIGEN_SAN_DOMAIN ), array( &$this, 'pass_field' ), $this->menu_slug, 'default' );
	}

	public function enabling_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_enabling' );
?>
		<label>
			<select name="ichigen_san_enabling" id="ichigen_san_enabling">
				<option value="0"<?php selected( $value, 0, true ); ?>><?php _e( 'Disabled', ICHIGEN_SAN_DOMAIN ); ?></option>
				<option value="1"<?php selected( $value, 1, true ); ?>><?php _e( 'Login screen', ICHIGEN_SAN_DOMAIN ); ?></option>
				<option value="2"<?php selected( $value, 2, true ); ?>><?php _e( 'Basic authentication', ICHIGEN_SAN_DOMAIN ); ?></option>
			</select>
		</label>
	<?php
	}

	public function page_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_page' );
?>
		<label>
			<textarea name="ichigen_san_page" id="ichigen_san_page"><?php echo $value; ?></textarea>
		</label>
	<?php
	}

	public function user_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_user' );
?>
		<label>
			<input type="text" name="ichigen_san_user" id="ichigen_san_user" value="<?php echo $value; ?>"></input>
		</label>
	<?php
	}
	public function pass_field( $args ) {
		extract( $args );
		$value = get_option( 'ichigen_san_pass' );
?>
		<label>
			<input type="password" name="ichigen_san_pass" id="ichigen_san_pass"></input>
		</label>
	<?php
	}


	public function add_custom_whitelist_options_fields() {
		register_setting( $this->menu_slug, 'ichigen_san_enabling', 'intval' );
		register_setting( $this->menu_slug, 'ichigen_san_page' );
		register_setting( $this->menu_slug, 'ichigen_san_user' );
		register_setting( $this->menu_slug, 'ichigen_san_pass', 'wp_hash_password' );
	}

	public function admin_styles() {
		wp_enqueue_style( 'admin_ichigen_san_style', $this->plugin_url . '/css/admin-style.css' );
	}

	public function check_ichigen_san_page() {
		$values = get_option( 'ichigen_san_page' );
		if ( $values ) {
			$values = explode("\n", $values);
			$values = array_map('trim', $values);
			$values = array_filter($values, 'strlen');
			$values = array_values($values);
			$page_url_get = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$search_url = array('index.php');
			$url = str_replace($search_url,'',$page_url_get);
			$count = 0;
			foreach( $values as $value ) {
				if( stristr( $url, $value) ) {
					return TRUE;
				}
				$count++;
			}
		} else {
			return TRUE;
		}
	}
	public function redirect_ichigen_san() {
		if ( !is_user_logged_in() && get_option( 'ichigen_san_enabling' ) == 1 && $this->check_ichigen_san_page() ) {
			auth_redirect();
		} elseif ( !is_user_logged_in() && get_option( 'ichigen_san_enabling' ) == 2 && $this->check_ichigen_san_page() ) {
			nocache_headers();
			// WordPress のユーザー認証で BASIC 認証ユーザー/パスワードをチェック
			$user = isset($_SERVER["PHP_AUTH_USER"]) ? $_SERVER["PHP_AUTH_USER"] : '';
			$pwd  = isset($_SERVER["PHP_AUTH_PW"]) ? $_SERVER["PHP_AUTH_PW"] : '';
			if ( get_option( 'ichigen_san_user' ) && get_option( 'ichigen_san_pass' ) ) {
				if ( $user == get_option( 'ichigen_san_user' ) && wp_check_password( $pwd, get_option( 'ichigen_san_pass' ) ) ) {
					return;
				}
			}
			if ( !is_wp_error(wp_authenticate($user, $pwd)) ) {
				return;
			}

			// BASIC 認証が必要
			header('WWW-Authenticate: Basic realm="Please Enter Your Password"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Authorization Required';
			die();
		} else {
			return;
		}
	}
}
