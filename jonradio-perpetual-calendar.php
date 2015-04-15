<?php
/*
Plugin Name: Perpetual Calendar
Plugin URI: http://zatzlabs.com/lab-notes/
Description: Determines Day of the Week for any Date, 7000 years into the past or future, with a Shortcode allowing Web Site Visitors to enter any date and display Day of Week, and a PHP Function accepting Day, Month and Year and returning a String with the Formatted Date and Day of Week.
Version: 3.0
Author: David Gewirtz
Author URI: http://zatzlabs.com/lab-notes/
Text Domain: jonradio-perpetual-calendar
Domain Path: /languages
License: GPLv2
*/

/*  Copyright 2014  jonradio  (email : info@zatz.com)

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
		/*	BC = B.C./A.D.
			BCE = BCE/CE
			NONE = negative years not allowed
			
			'fields' are added later, at plugins_loaded
		*/
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
	$internal_settings['shortcode_dup'] = FALSE;
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
	
	global $jr_pc_date_parts, $jr_pc_form_prefix, $jr_pc_part_fields;
	/*	Prefix all fields in Forms to make them unique.
	*/
	$jr_pc_form_prefix = 'jrpc';
	
	$settings = get_option( 'jr_pc_settings' );
	if ( empty( $settings['shortcode'] ) ) {
		/*	translators: [pcal] is default Shortcode Name in English.  Feel free to change. */
		$settings['shortcode'] = __( 'pcal', $jr_pc_plugin_data['TextDomain'] );
		update_option( 'jr_pc_settings', $settings );
	}
	
	/*	Key value and Description of each Date field
	*/
	$jr_pc_date_parts = array(
		'month'      => __( 'Month Name', $jr_pc_plugin_data['TextDomain'] ),
		'day'        => __( 'Day of Month', $jr_pc_plugin_data['TextDomain'] ),
		/*	translators: Century Digits of Year refers to the year divided by 100, and would be "20" for the year 2014. */
		'century'    => __( 'Century Digits of Year', $jr_pc_plugin_data['TextDomain'] ),
		/*	translators: Tens Digit of the Year would be "1" for 2014. */
		'tens'       => __( 'Tens Digit of Year', $jr_pc_plugin_data['TextDomain'] ),
		'year'       => __( 'Last Digit of Year', $jr_pc_plugin_data['TextDomain'] ),
		/*	translators: Current or Ancient Date refers to AD and BC, also known as CE and BCE; for example, 2200 years ago would be the year 200 BC. */
		'era'        => __( 'Current or Ancient Date', $jr_pc_plugin_data['TextDomain'] ),
		'buttonday'  => __( 'Display Day of Week button', $jr_pc_plugin_data['TextDomain'] ),
		'buttonhelp' => __( 'Help and Info button', $jr_pc_plugin_data['TextDomain'] )
		);
	/*	Format of Date Fields information in jr_pc_settings:
		['fields'][0] =>
			['part']   => key from $jr_pc_date_parts, e.g. - 'day', of first field to display
			['break']  => line break before field (true or false),
			['before'] => text to display before field,
			['width']  => width, in characters, of Form Field,
			['after']  => text to display after field
		['fields'][1] =>
			['part']   => key from $jr_pc_date_parts, e.g. - 'day', of second field to display
				...
	*/
	$jr_pc_part_fields = array(
		/*	translators: Date Part is the Settings Field Name for a portion of the date,
			for example, Name of Month.
		*/
		'part'   => __( 'Date Part', $jr_pc_plugin_data['TextDomain'] ), 
		'break'  => __( 'New Line Before?', $jr_pc_plugin_data['TextDomain'] ), 
		'height' => __( 'Height of New Line (in Pixels)', $jr_pc_plugin_data['TextDomain'] ),
		'before' => __( 'Text Before', $jr_pc_plugin_data['TextDomain'] ),
		'width'  => __( 'Field Width (in Pixels)', $jr_pc_plugin_data['TextDomain'] ), 
		'after'  => __( 'Text After', $jr_pc_plugin_data['TextDomain'] ) 
		);
	
	/*	Set Default values for Form Fields
	*/
	$default_width = array(
		'month'      => 100,
		'day'        => 45,
		'century'    => 45,
		'tens'       => 37,
		'year'       => 37,
		'era'        => 50,
		'buttonday'  => 1,
		'buttonhelp' => 1
		);
	$default_after = array(
		'month'      => '',
		'day'        => ', ',
		'century'    => '',
		'tens'       => '',
		'year'       => '',
		'era'        => '',
		'buttonday'  => ' ',
		'buttonhelp' => ''
		);
	if ( empty( $settings['fields'] ) ) {
		$index = 0;
		foreach ( $jr_pc_date_parts as $part => $desc ) {
			$fields[$index]['part'] = $part;
			$fields[$index]['break'] = FALSE;
			$fields[$index]['height'] = 0;
			$fields[$index]['before'] = '';
			$fields[$index]['width'] = $default_width[$part];
			$fields[$index++]['after'] = $default_after[$part];
		}
		$settings['fields'] = $fields;
		update_option( 'jr_pc_settings', $settings );
	}
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