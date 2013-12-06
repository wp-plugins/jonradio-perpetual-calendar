<?php
/*
Plugin Name: jonradio Perpetual Calendar
Plugin URI: http://jonradio.com/plugins/jonradio-perpetual-calendar/
Description: Your choice of Shortcode or php function to return a message indicating the full name of the day of the week for any given date, the typical usage of a so-called Perpetual Calendar. 
Version: 3.0
Author: jonradio
Author URI: http://jonradio.com/plugins
Text Domain: jonradio-perpetual-calendar
License: GPLv2
*/

/*  Copyright 2014  jonradio  (email : info@jonradio.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) exit;

global $jr_pc_plugin_basename;
$jr_pc_plugin_basename = plugin_basename( __FILE__ );
/**
 * Return Plugin's Basename
 * 
 * For this plugin, it would be:
 *	jonradio-multiple-themes/jonradio-multiple-themes.php
 *
 */
function jr_pc_plugin_basename() {
	global $jr_pc_plugin_basename;
	return $jr_pc_plugin_basename;
}

global $jr_pc_path;
$jr_pc_path = plugin_dir_path( __FILE__ );
/**
* Return Plugin's full directory path with trailing slash
* 
* Local XAMPP install might return:
*	C:\xampp\htdocs\wpbeta\wp-content\plugins\jonradio-perpetual-calendar/
*
*/
function jr_pc_path() {
	global $jr_pc_path;
	return $jr_pc_path;
}

global $jr_pc_dir_url;
//	URL of this plugin's directory with trailing slash ("/")
$jr_pc_dir_url = plugin_dir_url( __FILE__ );
/**
* Return Plugin's full directory URL with trailing slash
* 
* Local XAMPP install might return:
*	http://localhost/wpbeta/wp-content/plugins/jonradio-perpetual-calendar/
*
*/
function jr_pc_dir_url() {
	global $jr_pc_dir_url;
	return $jr_pc_dir_url;
}

if ( !function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

global $jr_pc_plugin_data;
$jr_pc_plugin_data = get_plugin_data( __FILE__ );
$jr_pc_plugin_data['slug'] = basename( dirname( __FILE__ ) );

/*	Detect initial activation or a change in plugin's Version number

	Sometimes special processing is required when the plugin is updated to a new version of the plugin.
	Also used in place of standard activation and new site creation exits provided by WordPress.
	Once that is complete, update the Version number in the plugin's Network-wide settings.
*/

if ( ( FALSE === ( $internal_settings = get_option( 'jr_pc_internal_settings' ) ) ) 
	|| empty( $internal_settings['version'] ) )
	{
	/*	Plugin is either:
		- updated from a version so old that Version was not yet stored in the plugin's settings, or
		- first use after install:
			- first time ever installed, or
			- installed previously and properly uninstalled (data deleted)
	*/

	$old_version = '0.1';
} else {
	$old_version = $internal_settings['version'];
}

$settings = get_option( 'jr_pc_settings' );
if ( empty( $settings ) ) {
	$settings = array(
		'negative_year_handling'   => 'BC'
		//	BC = B.C./A.D.
		//	BCE = BCE/CE
		//	NONE = negative years not allowed
	);
	/*	Add if Settings don't exist, re-initialize if they were empty.
	*/
	update_option( 'jr_pc_settings', $settings );
	/*	New install on this site, old version or corrupt settings
	*/
	$old_version = $jr_pc_plugin_data['Version'];
}

if ( version_compare( $old_version, $jr_pc_plugin_data['Version'], '!=' ) ) {
	/*	Create, if internal settings do not exist; update if they do exist
	*/
	$internal_settings['version'] = $jr_pc_plugin_data['Version'];
	update_option( 'jr_pc_internal_settings', $internal_settings );

	/*	Handle all Settings changes made in old plugin versions
	*/
	/*	None yet.
	update_option( 'jr_pc_settings', $settings );
	*/
}

add_action( 'plugins_loaded', 'jr_pc_textdomain' );
function jr_pc_textdomain() {
	global $jr_pc_plugin_data;
	load_plugin_textdomain( $jr_pc_plugin_data['TextDomain'], FALSE, dirname( jr_pc_plugin_basename() ) . '/languages' );
}

function jr_pc_display_shortcode( $shortcode ) {
	return str_replace( array( '[', ']' ), array( '&#091;', '&#093;' ), $shortcode );
}

if ( is_admin() ) {
	//	Admin panel
	require_once( jr_pc_path() . 'includes/admin.php' );
} else {
	//  Public Page
	require_once( jr_pc_path() . 'includes/public.php' );
}

?>