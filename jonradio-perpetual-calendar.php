<?php
/*
Plugin Name: jonradio Perpetual Calendar
Plugin URI: http://jonradio.com/plugins/jonradio-perpetual-calendar/
Description: Your choice of Shortcode or php function to return a message indicating the full name of the day of the week for any given date, the typical usage of a so-called Perpetual Calendar. 
Version: 2.1
Author: jonradio
Author URI: http://jonradio.com/plugins
License: GPLv2
*/

/*  Copyright 2012  jonradio  (email : info@jonradio.com)

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
DEFINE( 'JR_PC_PLUGIN_NAME', 'jonradio Perpetual Calendar');
global $jr_pc_dir;
$jr_pc_dir = plugin_dir_path( __FILE__ );
function jr_pc_dir() {
	global $jr_pc_dir;
	return $jr_pc_dir;
}
global $jr_pc_basename;
$jr_pc_basename = plugin_basename( __FILE__ );
global $jr_pc_dir_url;
//	URL of this plugin's directory with trailing slash ("/")
$jr_pc_dir_url = plugin_dir_url( __FILE__ );

register_activation_hook( __FILE__, 'jr_pc_activate' );
/**
 * Activation Time Activities
 * 
 * Be sure required php Functions are available.
 * Create Settings with default values, but only if they don't already exist.
 *
 */
function jr_pc_activate() {
	global $jr_pc_activated;
	//	Don't Activate twice, though it probably wouldn't hurt anything
	if ( isset( $jr_pc_activated ) ) {
		return;
	}
	if ( function_exists('is_multisite') && is_multisite() && isset( $_GET['networkwide'] ) && ( $_GET['networkwide'] == 1 ) ) {
		global $wpdb, $site_id;
		$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = $site_id" );
		foreach ( $blogs as $blog_obj ) {
			if ( switch_to_blog( $blog_obj->blog_id ) ) {
				//	We know the Site actually exists
				jr_pc_activate1();
			}
		}
		restore_current_blog();
	} else {
		jr_pc_activate1();
	}
	$jr_pc_activated = TRUE;
}

function jr_pc_activate1() {
	$settings = array(
		'negative_year_handling'   => 'BC'
		//	BC = B.C./A.D.
		//	BCE = BCE/CE
		//	NONE = negative years not allowed
	);
	//	Nothing happens if Settings already exist
	add_option( 'jr_pc_settings', $settings );
	
	$plugin_data = get_plugin_data(  __FILE__ );
	$version = $plugin_data['Version'];
	$internal_settings = array(
		'version' => $version
	);
	//	Nothing happens if Settings already exist
	add_option( 'jr_pc_internal_settings', $internal_settings );
}

add_action( 'wpmu_new_blog', 'jr_pc_new_site', 10, 6 );

function jr_pc_new_site( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	global $jr_pc_basename;
	if ( is_plugin_active_for_network( $jr_pc_basename ) ) {
		switch_to_blog( $blog_id );
		jr_pc_activate1();
		restore_current_blog();
	}
}

register_deactivation_hook( __FILE__, 'jr_pc_deactivate' );
/**
 * Deactivation Time Activities
 * 
 * Settings are removed at Uninstall because they need to survive a Deactivate/Activate.
 *
 */
function jr_pc_deactivate() {
	global $jr_pc_activated;
	//	Don't Deactivate if already Activated earlier in this Page view
	if ( isset( $jr_pc_activated ) ) {
		return;
	}
	
	//	Nothing to do at Deactivation time
	return;
}

add_action( 'init', 'jr_pc_init' );

function jr_pc_init() {
	//	Check for Plugin Version update (Deactivate and Activate Hooks not fired)
	if ( !function_exists( 'get_plugin_data' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}	
	$internal_settings = get_option( 'jr_pc_internal_settings' );
	if ( !$internal_settings ) {
		jr_pc_deactivate();
		jr_pc_activate();
	} else {
		$plugin_data = get_plugin_data(  __FILE__ );
		if ( version_compare( $internal_settings['version'], $plugin_data['Version'], '<' ) ) {
			jr_pc_deactivate();
			jr_pc_activate();
			$internal_settings['version'] = $plugin_data['Version'];
			update_option( 'jr_pc_internal_settings', $internal_settings );
		}
	}
	
	//	Add plugin's "public" Function
	if ( !function_exists( 'jr_weekday' ) ) {
		/**
		* Weekday message function
		* 
		* Returns a message listing the date given and the full name of the weekday for that given date, or an error message.
		*
		* @param    int     $year       Year portion of date
		* @param    int     $month      Month portion of date
		* @param    int     $day        Day of Month portion of date
		* @return   string              Message listing date and weekday, or error message.
		*/
		function jr_weekday( $year, $month, $day ) {
			if ( !isset( $year ) || !isset( $month ) || !isset( $day ) ) {
				return 'Error:  one or more of the three jr_weekday parameters (year, month, day) were either not specified or the variable specified is unassigned.';
			}
			if ( !jr_pc_signed_integer( $year ) || !jr_pc_signed_integer( $month ) || !jr_pc_signed_integer( $day ) ) {
				return 'Error:  one or more of the three jr_weekday parameters (year, month, day) is not an integer.';
			}		
			$jd = gregoriantojd( $month, $day, $year );
			if ( $jd == 0 ) {
				return 'Error:  earliest allowed date is November 25, 4714 B.C.';
			} else {
				if ( jdtogregorian( $jd ) == "$month/$day/$year" ) {
					return jdmonthname( $jd, 1 ) . " $day, " . abs( $year ) . ' ' . jr_pc_century( $year ) . ' is a ' . jddayofweek( $jd, 1 );
				} else {
					return 'Error:  date specified does not exist.';
				}
			}
		}
	}
	
	if ( is_admin() ) {
		//	Admin panel
		require_once( jr_pc_dir() . 'includes/admin.php' );
	} else {
		//  Public Page
		require_once( jr_pc_dir() . 'includes/public.php' );
	}
}

/**
 * A.D./CE or B.C./BCE
 * 
 * Returns B.C. or BCE for negative years and A.D., CE or blank otherwise,
 * depending on the negative_year_handling Setting.
 *
 * @param    int     $century    Year
 * @return   string              A.D., CE, B.C., BCE or blank, 
 */
function jr_pc_century( $century ) {
	$settings = get_option( 'jr_pc_settings' );
	if ( $settings['negative_year_handling'] == 'NONE' ) {
		$output = '';
	} else {
		if ( $settings['negative_year_handling'] == 'BCE' ) {
			if ( $century < 0 ) {
				$output = 'BCE ';
			} else {
				$output = 'CE ';
			}
		} else {
			if ( $century < 0 ) {
				$output = 'B.C. ';
			} else {
				$output = 'A.D. ';
			}
		}
	}
	return $output;
}

function jr_pc_display_shortcode( $shortcode ) {
	return str_replace( array( '[', ']' ), array( '&#091;', '&#093;' ), $shortcode );
}

?>