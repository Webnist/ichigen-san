<?php
/*
Plugin Name: Ichigen San
Plugin URI: http://plugins.webnist.jp
Description:
Author: Webnist
Version: 0.1
Author URI: http://webni.st
*/

add_action( 'template_redirect', 'redirect_ichigen_san' );
function redirect_ichigen_san() {
	if ( !is_user_logged_in() ) {
		auth_redirect();
	}
}
