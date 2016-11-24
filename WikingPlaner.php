<?php
/*
 Plugin Name: Viking Planer 
 Plugin URI: http://www.wp-vikings.com
 Description: Plan stuf 
 Version: 1.0 
 Author: Mike Mikjaer 
 License: GPL 2.0
 Author URI: http://www.mikjaer-consulting.com
 Text Domain: Viking PLaner 
 */

/*  Copyright 2016 Mikkel Mikjaer, mikkel@mikjaer.com

Diz iz magic
*/
include("config.php"); 

include( plugin_dir_path( __FILE__ ) ."/post-type-switcher.php");

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Our custom post type function
add_action( 'init', 'create_posttype' );
function create_posttype() {
	foreach (wpw_lists_settings("Lists") as $list)
	{
		register_post_type( $list["slug"],
		// CPT Options
			array(
				'labels' => array(
					'name' => __( $list["name"] ),
					'singular_name' => __( $list["name"] )
				),
				'public' => true,
				'show_in_menu' => __FILE__, 
				'has_archive' => false,
				'exclude_from_search'   => true,
				'rewrite' => array('slug' => 'todo'),
			)
		);
		add_filter( 'views_edit-'.$list["slug"], 'wpwp_swap_status', 10, 1 );
	}
}
function wpwp_swap_status( $views ) 
{
	if ($views["draft"])
		$v["draft"] = str_replace("Kladde", "Aktuelle", $views["draft"]);
	
	if ($views["publish"])
		$v["publish"] = str_replace("Udgivet", "Afviklede", $views["publish"]);
	
	if ($views["all"])
		$v["all"] = $views["all"];
    	return $v;
}

// Making draft the default view
add_action( 'admin_menu', 'edit_admin_menus' );
function edit_admin_menus() {
	global $submenu;
	
	foreach ($submenu[plugin_basename(__FILE__)] as $k=>$v)
		$submenu[plugin_basename(__FILE__)][$k][2].="&post_status=draft";
}



# Creating menu
add_action( 'admin_menu', 'wpw_admin_menu' );
function wpw_admin_menu() {
	add_menu_page('wiking-planer', wpw_lists_settings("MenuName"), 'manage_options', __FILE__, '',plugin_dir_url( __FILE__ ) . '/arrow.png',5);;
}

# Notes: http://wordpress.stackexchange.com/questions/89351/new-post-status-for-custom-post-type
# 		https://www.smashingmagazine.com/2012/11/complete-guide-custom-post-types/
?>
