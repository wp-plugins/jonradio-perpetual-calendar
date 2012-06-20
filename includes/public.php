<?php

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
	global $jr_pc_dup;
	//	Only display the Perpetual Calendar once on a web page
	if ( isset( $jr_pc_dup ) ) {
		return '';
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
	
	$output .= '<form method=post> <button type="submit" name="jrsubmit" value="jrsubmit">Display Day of Week</button>';
	$output .= ' <select name="jrmonth" style="width: 100px">';
	for ( $i = 1; $i <= 12; $i++ ) {
		if ( $i == $month ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$output .= "<option $selected value=$i>" . date( 'F', mktime( 0, 0, 0, $i, 1, 2001 ) ) . '</option>';
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
	$output .= '</select> (year in 3 parts)';
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
		$output .= '<button type="submit" name="jrnohelp" value="jrnohelp">No Help and Info</button></div></form><hr />';
		require_once( jr_pc_dir() . 'includes/help.php' );
		$output .= jr_pc_help();
		$output .= '<hr />';
	} else {
		$output .= '<button type="submit" name="jrhelp" value="jrhelp">Help and Info</button></div></form><hr />';
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
?>