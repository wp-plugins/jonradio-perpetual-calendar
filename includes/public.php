<?php

add_action('wp', 'jr_pc_add_shortcode' );

/**
* Add plugin's Shortcode
* 
* This is performed as late as possible (wp Action hook),
* to maximize the chances of detecting a duplicate shortcode definition.
* Because WordPress gives no warning when this occurs.
* Theoretically, you could wait until the_content Action hook,
* when do_shortcode is executed by WordPress, but there are other plugins
* that look for shortcodes in Widgets, so there might be some that look in
* Headers.  So, why risk it?
*/
function jr_pc_add_shortcode() {
	$internal_settings = get_option( 'jr_pc_internal_settings' );
	$settings = get_option( 'jr_pc_settings' );
	if ( shortcode_exists( $settings['shortcode'] ) ) {
		/*	A WordPress global that provides the Function Name associated with each Shortcode Name.
		*/
		global $shortcode_tags;
		$internal_settings['shortcode_dup'] = $shortcode_tags[ $settings['shortcode'] ];
		update_option( 'jr_pc_internal_settings', $internal_settings );
	} else {
		add_shortcode( $settings['shortcode'], 'jr_pc_pcal' );
		if ( FALSE !== $internal_settings['shortcode_dup'] ) {
			$internal_settings['shortcode_dup'] = FALSE;
			/*	Avoid Database Write on every WordPress public page or post
			*/
			update_option( 'jr_pc_internal_settings', $internal_settings );
		}
	}

}

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
	function jr_weekday_spacing( $after_previous, $before ) {
		if ( FALSE === $after_previous ) {
			return $before;
		} else {
			if ( ( '' === $before ) && ( '' === $after_previous ) ) {
				return ' ';
			} else {
				return $after_previous . $before;
			}
		}
	}
	global $jr_pc_plugin_data;
	/*	All error messages returned by this function will be prefixed by this string.
		Though not used that way in this plugin, programmers have the ability to
		use it to determine if this function failed.
	*/
	$error_message_prefix = __( 'Error jr_weekday function:  ', $jr_pc_plugin_data['TextDomain'] );
	if ( !isset( $year ) || !isset( $month ) || !isset( $day ) ) {
		return $error_message_prefix
			. __( 'one or more of the three function parameters (year, month, day) were either not specified or the variable specified is unassigned.', 
				$jr_pc_plugin_data['TextDomain'] );
	}
	if ( !jr_pc_signed_integer( $year ) || !jr_pc_signed_integer( $month ) || !jr_pc_signed_integer( $day ) ) {
		return $error_message_prefix 
			. __( 'one or more of the three function parameters (year, month, day) is not an integer.', 
				$jr_pc_plugin_data['TextDomain'] );
	}		
	$jd = gregoriantojd( $month, $day, $year );
	if ( $jd == 0 ) {
		return $error_message_prefix 
			. __( 'earliest allowed date is November 25, 4714 B.C.', 
				$jr_pc_plugin_data['TextDomain'] );
	} else {
		if ( jdtogregorian( $jd ) == "$month/$day/$year" ) {
			$weekday = jr_pc_day_name( jddayofweek( $jd, CAL_DOW_DAYNO ) );
			if ( ( FALSE !== ( $settings = get_option( 'jr_pc_settings' ) ) ) && isset( $settings['fields'] ) ) {
				/*	Rules:
					Output ['before'] of current entry, current entry, and ['after'] of current entry.
					Exception for first year field:  output ['before'] of Century part of year, entire year, and ['after'] of last digit of year.
					Exception for other than first year field:  do nothing.
					Exception when nothing output between fields:  output a space.
				*/
				foreach ( $settings['fields'] as $index => $attributes ) {
					switch ( $attributes['part'] ) {
						case 'century':
							$before_year = $attributes['before'];
							break;
						case 'year':
							$after_year = $attributes['after'];
							break;
					}
				}
				$date = '';
				$first_year = TRUE;
				$after_previous = FALSE;
				foreach ( $settings['fields'] as $index => $attributes ) {
					switch ( $attributes['part'] ) {
						case 'month':
							$date .= jr_weekday_spacing( $after_previous, $attributes['before'] ) . jr_pc_month_name( $month );
							$after_previous = $attributes['after'];
							break;
						case 'day':
							$date .= jr_weekday_spacing( $after_previous, $attributes['before'] ) . $day;
							$after_previous = $attributes['after'];
							break;
						case 'century':
						case 'tens':
						case 'year':
							if ( $first_year ) {
								$date .= jr_weekday_spacing( $after_previous, $before_year ) . abs( $year );
								$after_previous = $after_year;
								$first_year = FALSE;
							}
							break;
						case 'era':
							if ( 'NONE' !== $settings['negative_year_handling'] ) {
								$date .= jr_weekday_spacing( $after_previous, $attributes['before'] ) . jr_pc_century( $year );
								$after_previous = $attributes['after'];
							}
							break;
					}
				}
				$date .= $after_previous;
				/*	translators: %1$s is the full Date formatted according to the Settings
					and %2$s is the Name of the Day of the Week.
				*/
				return sprintf(
					__( '%1$s is a %2$s', 
						$jr_pc_plugin_data['TextDomain'] ),
					$date,
					$weekday 
				);
			} else {
				/*	translators: %1$s is the Name of the Month;
					%2$s is the Day of the Month;
					%3$s is the Year as a positive integer;
					%4$s is either a zero length string,
					or indicates the time period when the year occurs
					as B.C., A.D., BC or BCE;
					and %5$s is the Name of the Day of the Week.
					Used only when Settings not available.
				*/
				return sprintf(
					__( '%1$s %2$s, %3$s %4$s is a %5$s', 
						$jr_pc_plugin_data['TextDomain'] ),
					jr_pc_month_name( $month ), 
					$day, 
					abs( $year ), 
					jr_pc_century( $year ), 
					$weekday 
				);
			}
		} else {
			return $error_message_prefix 
			. __( 'date specified does not exist.', 
				$jr_pc_plugin_data['TextDomain'] );
		}
	}
}

/**
 * [pcal] Shortcode (or whatever name is Set by Administrator)
 * 
 * Returns an HTML Form in which a user may enter a date.
 * If a Date was just entered, displays the Day of Week or an error message.
 *
 * @return   string              HTML to display Date entry form and the Day of Week for a previous inquiry
 */
function jr_pc_pcal() {
	$nonce_name = 'jrpcform';
	
	global $jr_pc_plugin_data, $jr_pc_date_parts;

	$settings = get_option( 'jr_pc_settings' );
	
	global $jr_pc_dup;
	/*	Only display the Perpetual Calendar once on a web page,
		as the code has not been adapted to multiple uses per page.
	*/
	if ( isset( $jr_pc_dup ) ) {
		/*	translators: %1$s is the name of the shortcode surrounded by square brackets,
			by default, [pcal]
			It is displayed whenever the shortcode is used twice in the same page, post,
			widget, etc.
			This message is displayed the second, third and subsequent times the shortcode is
			used in the same page, etc.
			At present, the plugin would not work properly if the shortcode were allowed to
			actually process more than once.
		*/
		return sprintf(
					__( '%1$s (duplicate)', 
						$jr_pc_plugin_data['TextDomain'] ),
					jr_pc_display_shortcode( '[' . $settings['shortcode'] . ']' )
					);
	} else {
		$jr_pc_dup = TRUE;
	}
	
	/*	Default Date:
		Years prior to 1 AD are specified as negative $year value
		(remember, there is no year zero).
	*/
	$year = 1962;
	$month = 2;
	$day = 9;
	
	$output = '<hr />';
	if ( isset( $_POST['jrsubmit'] ) || isset( $_POST['jrhelp'] ) || isset( $_POST['jrnohelp'] ) ) {
		check_admin_referer( $nonce_name );
		$jr_pc_help = $_POST['jrsavehelp'];
		$year = $_POST['jrera'] * ( ( $_POST['jrcentury'] * 100 ) + ( $_POST['jrten'] * 10 ) + $_POST['jryear'] );
		$month = $_POST['jrmonth'];
		$day = $_POST['jrday'];
		if ( isset( $_POST['jrsubmit'] ) ) {
			$output .= '<p>' . jr_weekday( $year, $month, $day ) . '</p>';
			$output .= '<hr />';
		}
	}
	
	$output .= '<form method=post>';
	$output .= wp_nonce_field( $nonce_name, '_wpnonce', TRUE, FALSE );
	
	$p = FALSE;
	for ( $index = 0; $index < count( $jr_pc_date_parts ); $index++ ) {
		if ( $settings['fields'][$index]['break'] ) {
			if ( $p ) {
				$output .= '</p>';
			}
			$output .= '<p style="line-height: ' . $settings['fields'][$index]['height'] . 'px">';
			$p = TRUE;
		}
		$output .= $settings['fields'][$index]['before'];
		switch ( $settings['fields'][$index]['part'] ) {
			case 'month':
				$output .= ' <select name="jrmonth" style="width: '
					. $settings['fields'][$index]['width'] 
					. 'px">';
				for ( $i = 1; $i <= 12; $i++ ) {
					$output .= '<option '
						. selected( $i, $month, FALSE )
						. " value=$i>" 
						. jr_pc_month_name( $i ) 
						. '</option>';
				}
				$output .= '</select>';
				break;
			case 'day':   
				$output .= '<select name="jrday" style="width: '
					. $settings['fields'][$index]['width']
					. 'px">';
					for ( $i = 1; $i <= 31; $i++ ) {
						$output .= '<option '
							. selected( $i, $day, FALSE )
							. ">$i</option>";
					}
				$output .= '</select>';
				break;
			case 'century':
				$output .= '<select name="jrcentury" style="width: ' 
					. $settings['fields'][$index]['width'] 
					. 'px">';
				for ( $i = 0; $i <= 99; $i++ ) {
					$output .= '<option '
						. selected( $i, intval( abs( $year ) / 100 ), FALSE )
						. ">$i</option>";
				}
				$output .= '</select>';
				break;
			case 'tens':
				$output .= '<select name="jrten" style="width: '
					. $settings['fields'][$index]['width']
					. 'px">';
				for ( $i = 0; $i <= 9; $i++ ) {	
					$output .= '<option '
						. selected( $i, floor( abs( $year ) / 10 ) % 10, FALSE )
						. ">$i</option>";
				}
				$output .= '</select>';
				break;
			case 'year':
				$output .= '<select name="jryear" style="width: '
					. $settings['fields'][$index]['width']
					. 'px">';
				for ( $i = 0; $i <= 9; $i++ ) {
					$output .= '<option '
						. selected( $i, abs( $year ) % 10, FALSE )
						. ">$i</option>";
				}
				$output .= '</select>';
				break;
			case 'era':
				if ( 'NONE' === $settings['negative_year_handling'] ) {
					$output .= '<input name="jrera" type="hidden" value="1" />';
				} else {
					$output .= '<select name="jrera" style="width: '
						. $settings['fields'][$index]['width']
						. 'px;">';
					foreach ( array( 1, -1 ) as $era ) {
						$output .= "<option value='$era' "
							. selected( $year >= 0, $era >= 0, FALSE )
							. ' >'
							. jr_pc_century( $era )
							. '</option>';
					}
					$output .= '</select>';
				}
				break;
			case 'buttonday':
				$output .= '<button type="submit" name="jrsubmit" value="jrsubmit">';
				$output .= _x( 'Display Day of Week', 'Button', $jr_pc_plugin_data['TextDomain'] );
				$output .= '</button>';
				break;
			case 'buttonhelp':
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
				if ( $jr_pc_help ) {
					$output .= '<button type="submit" name="jrnohelp" value="jrnohelp">'
						.  _x( 'No Help and Info', 'Button', $jr_pc_plugin_data['TextDomain'] )
						. '</button>';
				} else {
					$output .= '<button type="submit" name="jrhelp" value="jrhelp">'
						.  _x( 'Help and Info', 'Button', $jr_pc_plugin_data['TextDomain'] )
						. '</button>';
				}
				break;
		}
		$output .= $settings['fields'][$index]['after'];		
	}
	if ( $p ) {
		$output .= '</p>';
	}

	$output .= '</form><hr />';
	if ( $jr_pc_help ) {
		require_once( jr_pc_path() . 'includes/help.php' );
		$output .= jr_pc_help();
		$output .= '<hr />';		
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
	if ( ( FALSE === ( $settings = get_option( 'jr_pc_settings' ) ) )
		|| empty( $settings['negative_year_handling'] ) )
	{
		$neg = 'BC';
	} else {
		$neg = $settings['negative_year_handling'];
	}
	switch ( $neg ) {
		case 'NONE':
			$output = '';
			break;
		case 'BCE':
			if ( $century < 0 ) {
				$output = _x( 'BCE', 'Date Era', $jr_pc_plugin_data['TextDomain'] );
			} else {
				$output = _x( 'CE', 'Date Era', $jr_pc_plugin_data['TextDomain'] );
			}
			break;
		case 'BC':
		default:
			if ( $century < 0 ) {
				$output = _x( 'B.C.', 'Date Era', $jr_pc_plugin_data['TextDomain'] );
			} else {
				$output = _x( 'A.D.', 'Date Era', $jr_pc_plugin_data['TextDomain'] );
			}
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
	return $month_name[ $month - 1 ];
}

?>