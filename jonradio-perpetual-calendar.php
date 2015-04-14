<?php
/*
Plugin Name: jonradio Perpetual Calendar
Plugin URI: http://zatzlabs.com/plugins/
Description: Your choice of Shortcode or php function to return a message indicating the full name of the day of the week for any given date, the typical usage of a so-called Perpetual Calendar. 
Version: 0.9
Author: David Gewirtz
Author URI: http://zatzlabs.com/plugins/
License: GPLv2
*/

/*  Copyright 2012  jonradio  (email : info@zatz.com)

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

add_action( 'init', 'jr_pc_init' );

function jr_pc_init() {
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
	
	add_shortcode( 'pcal', 'jr_pc_pcal' );
}

/**
 * [pcal] Shortcode
 * 
 * Returns an HTML Form in which a user may enter a date.
 * If a Date was just entered, displays the Day of Week or an error message.
 *
 * @return   string              HTML to display Date entry form and the Day of Week for a previous inquiry
 */
function jr_pc_pcal() {
	$min = -4714;
	$max = 9999;
	
	//	Default Date:
	$year = 1962;
	$month = 2;
	$day = 9;
	
	$output = '<hr />';
	if ( isset( $_POST['jrsubmit'] ) ) {
		$century = $_POST['jrcentury'];
		if ( $century < 0 ) {
			$sign = -1;
		} else {
			$sign = 1;
		}
		$year = $sign * ( ( floor( abs( $century ) / 100 ) * 100 ) + $_POST['jrten'] + $_POST['jryear'] );
		$month = $_POST['jrmonth'];
		$day = $_POST['jrday'];
		$output .= '<p>' . jr_weekday( $year, $month, $day ) . '</p>';
		$output .= '<hr />';
	}
	
	$output .= '<form method=post> <button type="submit" name="jrsubmit" value="jrsubmit">Display Day of Week</button> <select name="jrmonth">';
	for ( $i = 1; $i <= 12; $i++ ) {
		if ( $i == $month ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$output .= "<option $selected value=$i>" . date( 'F', mktime( 0, 0, 0, $i, 1, 2001 ) ) . '</option>';
	}
	$output .= '</select> <select name="jrday">';
	for ( $i = 1; $i <= 31; $i++ ) {
		if ( $i == $day ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}		
		$output .= "<option $selected>$i</option>";
	}
	
	if ( $year < 0 ) {
		$sign = -1;
	} else {
		$sign = 1;
	}	
	
	$output .= '</select>, <select name="jrcentury">';
	for ( $i = $min; $i <= $max; $i += 100 ) {
		$century = floor( abs( $i ) / 100 );
		if ( intval( $i / 100 ) == intval( $year / 100 ) ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$output .= "<option $selected value=$i>" . jr_pc_century( $i ) . " $century</option>";
	}
	$output .= '</select> <select name="jrten">';
	for ( $i = 0; $i <= 90; $i += 10 ) {
		if ( $i / 10 == floor( abs( $year ) / 10 ) % 10 ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$output .= "<option $selected value=$i>" . $i / 10 . '</option>';
	}
	$output .= '</select> <select name="jryear">';
	for ( $i = 0; $i <= 9; $i++ ) {
		if ( $i == abs( $year ) % 10 ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$output .= "<option $selected>$i</option>";
	}
	$output .= '</select> (year in three parts)';
	$output .= '</form>';
	$output .= '<div style="text-align: right;"><a href="http://zatzlabs.com/plugins/help" target="_blank">Help and Info</a></div><hr />';
	return $output;
}

/**
 * A.D. or B.C.
 * 
 * Returns B.C. for negative years and A.D. otherwise
 *
 * @param    int     $century    Year
 * @return   string              A.D. or B.C.
 */
function jr_pc_century( $century ) {
	if ( $century < 0 ) {
		return 'B.C.';
	} else {
		return 'A.D.';
	}
}

/**
 * Check for signed integer
 * 
 * Determines if the parameter is a signed integer, including Strings and Floating Point ending .000
 *
 * @param    var     $val        Value to check
 * @return   bool                TRUE if signed integer; FALSE otherwise
 */
function jr_pc_signed_integer( $val ) {
	if ( is_numeric( $val ) && ( (int)$val == $val ) ) {
		return TRUE;
	} else {
		return FALSE;
	}
}

?>