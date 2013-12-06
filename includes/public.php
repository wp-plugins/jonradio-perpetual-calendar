<?php

//	Add plugin's "public" Function

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
	global $jr_pc_plugin_data;
	/*	All error messages returned by this function will be prefixed by this string.
		Though not used that way in this plugin, programmers have the ability to
		use it to determine if this function failed.
	*/
	$error_message_prefix = __( 'Error:  ', $jr_pc_plugin_data['TextDomain'] );
	if ( !isset( $year ) || !isset( $month ) || !isset( $day ) ) {
		return $error_message_prefix
			. __( 'one or more of the three jr_weekday parameters (year, month, day) were either not specified or the variable specified is unassigned.', 
				$jr_pc_plugin_data['TextDomain'] );
	}
	if ( !jr_pc_signed_integer( $year ) || !jr_pc_signed_integer( $month ) || !jr_pc_signed_integer( $day ) ) {
		return $error_message_prefix 
			. __( 'one or more of the three jr_weekday parameters (year, month, day) is not an integer.', 
				$jr_pc_plugin_data['TextDomain'] );
	}		
	$jd = gregoriantojd( $month, $day, $year );
	if ( $jd == 0 ) {
		return $error_message_prefix 
			. __( 'earliest allowed date is November 25, 4714 B.C.', 
				$jr_pc_plugin_data['TextDomain'] );
	} else {
		if ( jdtogregorian( $jd ) == "$month/$day/$year" ) {
			return sprintf(
				__( '%1$s %2$s, %3$s %4$s is a %5$s', 
					$jr_pc_plugin_data['TextDomain'] ),
				jr_pc_month_name( $month ), 
				$day, 
				abs( $year ), 
				jr_pc_century( $year ), 
				jr_pc_day_name( jddayofweek( $jd, CAL_DOW_DAYNO ) ) 
			);
		} else {
			return $error_message_prefix 
			. __( 'date specified does not exist.', 
				$jr_pc_plugin_data['TextDomain'] );
		}
	}
}

add_shortcode( 'pcal', 'jr_pc_pcal' );

/**
 * [pcal] Shortcode
 * 
 * Returns an HTML Form in which a user may enter a date.
 * If a Date was just entered, displays the Day of Week or an error message.
 *
 * @return   string              HTML to display Date entry form and the Day of Week for a previous inquiry
 */
function jr_pc_pcal() {
	$nonce_name = 'jrpcform';
	
	global $jr_pc_plugin_data;
	
	global $jr_pc_dup;
	//	Only display the Perpetual Calendar once on a web page
	if ( isset( $jr_pc_dup ) ) {
		return sprintf(
					__( '%1$s (duplicate)', 
						$jr_pc_plugin_data['TextDomain'] ),
					jr_pc_display_shortcode( '[pcal]' )
					);
	} else {
		$jr_pc_dup = TRUE;
	}
	
	$settings = get_option( 'jr_pc_settings' );
	if ( $settings['negative_year_handling'] == 'NONE' ) {
		$min = 1;
	} else {
		$min = -4714;
	}
	$max = 9999;
	
	//	Default Date:
	$year = 1962;
	$month = 2;
	$day = 9;
	
	$output = '<hr />';
	if ( isset( $_POST['jrsubmit'] ) || isset( $_POST['jrhelp'] ) || isset( $_POST['jrnohelp'] ) ) {
		check_admin_referer( $nonce_name );
		$jr_pc_help = $_POST['jrsavehelp'];
		$century = $_POST['jrcentury'];
		if ( $century < 0 ) {
			$sign = -1;
		} else {
			$sign = 1;
		}
		$year = $sign * ( ( floor( abs( $century ) / 100 ) * 100 ) + $_POST['jrten'] + $_POST['jryear'] );
		$month = $_POST['jrmonth'];
		$day = $_POST['jrday'];
		if ( isset( $_POST['jrsubmit'] ) ) {
			$output .= '<p>' . jr_weekday( $year, $month, $day ) . '</p>';
			$output .= '<hr />';
		}
	}
	
	$output .= '<form method=post> <button type="submit" name="jrsubmit" value="jrsubmit">';
	$output .= __( 'Display Day of Week', $jr_pc_plugin_data['TextDomain'] );
	$output .= '</button>';
	$output .= wp_nonce_field( $nonce_name, '_wpnonce', TRUE, FALSE );
	$output .= ' <select name="jrmonth" style="width: 100px">';
	for ( $i = 1; $i <= 12; $i++ ) {
		if ( $i == $month ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$output .= "<option $selected value=$i>" . jr_pc_month_name( $i ) . '</option>';
	}
	$output .= '</select> <select name="jrday" style="width: 45px">';
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
	
	//	Check if you need room for the A.D./B.C. or other prefix
	if ( $settings['negative_year_handling'] == 'NONE' ) {
		$century_width = 45;
	} else {
		$century_width = 75;
	}	

	$output .= '</select>, <select name="jrcentury" style="width: ' . $century_width . 'px">';
	for ( $i = $min; $i <= $max; $i += 100 ) {
		$century = floor( abs( $i ) / 100 );
		if ( intval( $i / 100 ) == intval( $year / 100 ) ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$output .= "<option $selected value=$i>" . jr_pc_century( $i ) . "$century</option>";
	}
	$output .= '</select> <select name="jrten" style="width: 37px">';
	for ( $i = 0; $i <= 90; $i += 10 ) {
		if ( $i / 10 == floor( abs( $year ) / 10 ) % 10 ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$output .= "<option $selected value=$i>" . $i / 10 . '</option>';
	}
	$output .= '</select> <select name="jryear" style="width: 37px">';
	for ( $i = 0; $i <= 9; $i++ ) {
		if ( $i == abs( $year ) % 10 ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$output .= "<option $selected>$i</option>";
	}
	$output .= '</select> '
		. __( '(year in 3 parts)', $jr_pc_plugin_data['TextDomain'] );
	if ( isset( $_POST['jrhelp'] ) ) {
		$jr_pc_help = TRUE;
	} else {
		if ( isset( $_POST['jrnohelp'] ) ) {
			$jr_pc_help = FALSE;
		}
	}
	if ( !isset( $jr_pc_help ) ) {
		$jr_pc_help = FALSE;
	}
	$output .= '<input type="hidden" name="jrsavehelp" value="' . $jr_pc_help . '">';
	$output .= '<div style="text-align: right;">';
	if ( $jr_pc_help ) {
		$output .= '<button type="submit" name="jrnohelp" value="jrnohelp">'
			.  __( 'No Help and Info', $jr_pc_plugin_data['TextDomain'] );
			. '</button></div></form><hr />';
		require_once( jr_pc_path() . 'includes/help.php' );
		$output .= jr_pc_help();
		$output .= '<hr />';
	} else {
		$output .= '<button type="submit" name="jrhelp" value="jrhelp">'
			.  __( 'Help and Info', $jr_pc_plugin_data['TextDomain'] );
			. '</button></div></form><hr />';
	}
	return $output;
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
	global $jr_pc_plugin_data;
	$settings = get_option( 'jr_pc_settings' );
	if ( $settings['negative_year_handling'] == 'NONE' ) {
		$output = '';
	} else {
		if ( $settings['negative_year_handling'] == 'BCE' ) {
			if ( $century < 0 ) {
				$output = __( 'BCE', $jr_pc_plugin_data['TextDomain'] );
			} else {
				$output = __( 'CE', $jr_pc_plugin_data['TextDomain'] );
			}
		} else {
			if ( $century < 0 ) {
				$output = __( 'B.C.', $jr_pc_plugin_data['TextDomain'] );
			} else {
				$output = __( 'A.D.', $jr_pc_plugin_data['TextDomain'] );
			}
		}
		/*	Add blank to end of all but empty string
		*/
		$output .= ' ';
	}
	return $output;
}

/**
 * Name of Day of Week
 * 
 * Given day of week number from php, return text name of day of week.
 *
 * @param    int     $day		value returned from jddayofweek( date, CAL_DOW_DAYNO )
 * @return   string				Day of Week name 
 */
function jr_pc_day_name( $day ) {
	global $jr_pc_plugin_data;
	/*	Zero is Sunday
	*/
	$day_name = explode( '/', 
		__( 'Sunday/Monday/Tuesday/Wednesday/Thursday/Friday/Saturday', 
			$jr_pc_plugin_data['TextDomain'] ) );
	return $day_name[ $day ];	
}

/**
 * Month of the Year
 * 
 * Given month number, return full text name of month.
 *
 * @param    int     $month		month number
 * @return   string				Month name 
 */
function jr_pc_month_name( $month ) {
	global $jr_pc_plugin_data;
	/*	One is January
	*/
	$month_name = explode( '/', 
		__( 'January/February/March/April/May/June/July/August/September/October/November/December', 
			$jr_pc_plugin_data['TextDomain'] ) );
	return $month_name[ $month + 1 ];
}

?>