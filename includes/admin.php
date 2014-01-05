<?php
// Add Link to the plugin's entry on the Admin "Plugins" Page, for easy access
add_filter( 'plugin_action_links_' . jr_pc_plugin_basename(), 'jr_pc_plugin_action_links', 10, 1 );

/**
 * Creates Settings entry right on the Plugins Page entry.
 *
 * Helps the user understand where to go immediately upon Activation of the Plugin
 * by creating entries on the Plugins page, right beside Deactivate and Edit.
 *
 * @param	array	$links	Existing links for our Plugin, supplied by WordPress
 * @param	string	$file	Name of Plugin currently being processed
 * @return	string	$links	Updated set of links for our Plugin
 */
function jr_pc_plugin_action_links( $links ) {
	global $jr_pc_plugin_data;
	/*	The "page=" query string value must be equal to the slug
		of the Settings admin page.
	*/
	array_push( $links, '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=jr_pc_settings' . '">'
		. __( 'Settings', $jr_pc_plugin_data['TextDomain'] )
		. '</a>' );
	return $links;
}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_filter( 'network_admin_plugin_action_links_' . jr_pc_plugin_basename(), 'jr_pc_plugin_network_action_links', 10, 1 );
}

function jr_pc_plugin_network_action_links( $links ) {
	global $jr_pc_plugin_data;
	array_push( $links, '<a href="' . get_bloginfo('wpurl') . '/wp-admin/network/settings.php?page=jr_pc_network_settings' . '">'
		. __( 'Settings', $jr_pc_plugin_data['TextDomain'] )
		. '</a>' );
	return $links;
}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_action('network_admin_menu', 'jr_pc_network_admin_hook' );
}

function jr_pc_network_admin_hook() {
	global $jr_pc_plugin_data;
	//  Add Network Settings Page for this Plugin
	add_submenu_page(
		'settings.php', 
		$jr_pc_plugin_data['Name'], 
		__( 'Perpetual Calendar plugin', $jr_pc_plugin_data['TextDomain'] ), 
		'manage_network_options', 
		'jr_pc_network_settings', 
		'jr_pc_network_settings_page' 
	);
}

function jr_pc_network_settings_page() {
	global $jr_pc_plugin_data;
	echo '<div class="wrap">';
	echo '<h2>' . $jr_pc_plugin_data['Name'] . '</h2>'
		. '<p>';
	_e( 'This plugin has been Network Activated in a WordPress Multisite ("Network") installation. Since all of this plugin&#039;s Settings can be specified separately for each individual WordPress site, you will need to go to the relevant individual Site&#039;s Settings page for this plugin to change the Settings for that Site.',
		$jr_pc_plugin_data['TextDomain'] );
	echo '</p><p>';
	_e( 'Unfortunately, when WordPress network activates a plugin, you will not see an entry for that plugin on each individual Site&#039;s Plugins page. Needless to say, this can be very confusing. If you wish to avoid this confusion, you can Network Deactivate this plugin and Activate it individually on each Site where you wish to use it.',
		$jr_pc_plugin_data['TextDomain'] );
	echo '</p><p>';
	_e( 'Alternatively, if you would prefer to have a single set of Settings that would apply to all sites in a WordPress network, please contact the Plugin author and this will be added to a future version of this plugin if there is enough interest expressed by webmasters such as you.',
		$jr_pc_plugin_data['TextDomain'] );
	echo '</p>';
}

add_action( 'admin_menu', 'jr_pc_admin_hook' );

/**
 * Add Admin Menu item for plugin
 * 
 * Plugin needs its own Page in the Settings section of the Admin menu.
 *
 */
function jr_pc_admin_hook() {
	global $jr_pc_plugin_data;
	$settings = get_option( 'jr_pc_settings' );
	//  Add Settings Page for this Plugin
	add_options_page( 
		$jr_pc_plugin_data['Name'], 
		__( 'Perpetual Calendar plugin', $jr_pc_plugin_data['TextDomain'] ), 
		'manage_options', 
		'jr_pc_settings', 
		'jr_pc_settings_page' 
	);
}

/**
 * Settings page for plugin
 * 
 * Display and Process Settings page for this plugin.
 *
 */
function jr_pc_settings_page() {
	global $jr_pc_plugin_data;
	echo '<div class="wrap">';
	echo '<h2>' . $jr_pc_plugin_data['Name'] . '</h2><p>';
	$settings = get_option( 'jr_pc_settings' );
	printf(
		__( 'This plugin provides a Perpetual Calendar for your site visitors via the <code>%1$s</code> Shortcode, and for your own use within php code via the <code>jr_pc_weekday( $year, $month, $day )</code> function. The name of the Shortcode can be changed in the Settings below, but the name of the function cannot be changed.'
			, $jr_pc_plugin_data['TextDomain'] ),
		jr_pc_display_shortcode( '[' . $settings['shortcode'] . ']' )
	);
	echo '</p><p>';
	_e( 'A Perpetual Calendar provides the day of the week for virtually any date in the past, present or future.'
		, $jr_pc_plugin_data['TextDomain'] );
	echo '</p>';
	
	//	Plugin Settings are displayed and entered here:
	echo '<form action="options.php" method="POST">';
	settings_fields( 'jr_pc_settings' );
	do_settings_sections( 'jr_pc_settings_page' );
	echo '<p><tt>&para;</tt> - ';
	_e( 'indicates the position of a blank in a Settings value.'
		, $jr_pc_plugin_data['TextDomain'] );
	echo '</p><p><input name="save" type="submit" value="';
	_e( 'Save Changes', $jr_pc_plugin_data['TextDomain'] );
	echo '" class="button-primary" /></p></form>';
}

add_action( 'admin_init', 'jr_pc_admin_init' );

/**
 * Register and define the settings
 * 
 * Everything to be stored and/or can be set by the user
 *
 */
function jr_pc_admin_init() {
	global $jr_pc_plugin_data, $jr_pc_date_parts, $jr_pc_part_fields, $jr_pc_form_prefix;
	register_setting( 'jr_pc_settings', 'jr_pc_settings', 'jr_pc_validate_settings' );
	
	add_settings_section( 
		'jr_pc_shortcode_name_section', 
		__( 
			'Name of Shortcode', 
			$jr_pc_plugin_data['TextDomain'] 
		),
		'jr_pc_shortcode_name_expl', 
		'jr_pc_settings_page' 
	);
	add_settings_field( 
		'shortcode', 
		__( 
			'Shortcode Name', 
			$jr_pc_plugin_data['TextDomain'] 
		), 
		'jr_pc_echo_shortcode_name', 
		'jr_pc_settings_page', 
		'jr_pc_shortcode_name_section' 
	);

	add_settings_section( 
		'jr_pc_ancient_handling_section', 
		__( 
			'Ancient Date Handling', 
			$jr_pc_plugin_data['TextDomain'] 
		),
		'jr_pc_ancient_handling_expl', 
		'jr_pc_settings_page' 
	);
	add_settings_field( 
		'negative_year_handling', 
		__( 
			'Ancient Date Handling', 
			$jr_pc_plugin_data['TextDomain'] 
		), 
		'jr_pc_echo_negative_year_handling', 
		'jr_pc_settings_page', 
		'jr_pc_ancient_handling_section' 
	);

	add_settings_section( 
		'jr_pc_layout_section',
		'<input name="save" type="submit" value="'
			. 	__( 'Save Changes', $jr_pc_plugin_data['TextDomain'] )
			. '" class="button-primary" /><hr />'
			.	__( 
					'Date Form Input Layout', 
					$jr_pc_plugin_data['TextDomain'] 
				),
		'jr_pc_layout_expl', 
		'jr_pc_settings_page' 
	);
	$settings = get_option( 'jr_pc_settings' );
	for ( $index = 0; $index < count( $jr_pc_date_parts ); $index++ ) {
		if ( ( 'era' === $settings['fields'][$index]['part'] ) && ( 'NONE' === $settings['negative_year_handling'] ) ) {
			$function_name = 'jr_pc_era_expl';
		} else {
			$function_name = 'jr_pc_field_expl';
		}
		add_settings_section( 
			'jr_pc_field'. ( $index + 1 ) . '_section',
			sprintf(
				__( 
					'Date Field %1$s Content and Layout', 
					$jr_pc_plugin_data['TextDomain'] 
					),
				$index + 1
				),
			$function_name, 
			'jr_pc_settings_page' 
			);
		foreach ( $jr_pc_part_fields as $field => $field_description ) {
			$description = $field_description;
			add_settings_field( 
				"$jr_pc_form_prefix$field$index",
				$description,
				'jr_pc_echo_form_field',
				'jr_pc_settings_page',
				'jr_pc_field'. ( $index + 1 ) . '_section',
				array( $index, $field )
			);
		}
	}
}

/**
 * Section text for Section1
 * 
 * Display an explanation of this Section
 *
 */
function jr_pc_shortcode_name_expl() {
	global $jr_pc_plugin_data;
	_e( 'If you wish to change the name of the Shortcode that you will use to indicate where the Perpetual Calendar is to be placed, typically in a WordPress Page or Post, please specify it here.', 
		$jr_pc_plugin_data['TextDomain'] );
	$internal_settings = get_option( 'jr_pc_internal_settings' );
	$settings = get_option( 'jr_pc_settings' );
	if ( FALSE !== $internal_settings['shortcode_dup'] ) {
		echo '<p>';
		printf(
			__( 
				'The Shortcode Name specified below, <code>%1$s</code>, appears to be already in use by another plugin or WordPress itself. It initiates function <code>%2$s</code>. Please choose another Shortcode Name.', 
				$jr_pc_plugin_data['TextDomain'] 
				),
			jr_pc_display_shortcode( '[' . $settings['shortcode'] . ']' ),
			$internal_settings['shortcode_dup']
		);
		echo '</p>';
	}
}

function jr_pc_echo_shortcode_name() {
	global $jr_pc_plugin_data;
	$settings = get_option( 'jr_pc_settings' );
	echo '<input type="text" id="shortcode" name="jr_pc_settings[shortcode]" size="16" maxlength="16" value="'
		. $settings['shortcode']
		. '" /> <small>';
	_e( 'To maintain compatibility with future versions of WordPress, please use only lower-case letters, with no blanks, hyphens or underscores, and maximum length of 16.', 
		$jr_pc_plugin_data['TextDomain'] );
	echo '</small>';
}		
		
/**
 * Section text for Section1
 * 
 * Display an explanation of this Section
 *
 */
function jr_pc_ancient_handling_expl() {
	global $jr_pc_plugin_data;
	_e( 'Here is where you define how dates more than 2000 years ago will be handled. And how years since then, and into the future, will be displayed.', 
		$jr_pc_plugin_data['TextDomain'] );
}

function jr_pc_echo_negative_year_handling() {
	global $jr_pc_plugin_data;
	$settings = get_option( 'jr_pc_settings' );
	$negative_year_handling = $settings['negative_year_handling'];
	$choices = 
		array(
			array(
				'value' => 'BC',
				'display' => __( 
					'Current and Future Dates as A.D., Ancient Dates as B.C.', 
					$jr_pc_plugin_data['TextDomain']
				),
			),
			array(
				'value' => 'BCE',
				'display' => __( 
					'Current and Future Dates as CE, Ancient Dates as BCE', 
					$jr_pc_plugin_data['TextDomain'] 
				),
			),
			array(
				'value' => 'NONE',
				'display' => __( 
					'Do not allow Dates more then 2000 Years in the Past', 
					$jr_pc_plugin_data['TextDomain'] 
				),
			),
			
	);
	foreach ( $choices as $choice ) {
		$value = $choice['value'];
		$display = $choice['display'];
		if ( $value == $negative_year_handling ) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
		echo "<input type='radio' $checked id='negative_year_handling' name='jr_pc_settings[negative_year_handling]' value='$value' /> $display<br />";
	}
}

/**
 * Section text for Section2
 * 
 * Display an explanation of this Section
 *
 */
function jr_pc_layout_expl() {
	global $jr_pc_plugin_data;
	echo '<p>';
	_e( 'Visitors enter a date using an Input Form of drop-down values for day of month, name of month, and year. The format of that Form is defined below.  You can choose the order in which day of month, name of month, and year appear, as well as the width of each field within the Form. And the text, if any, separating each field, including line breaks.', 
		$jr_pc_plugin_data['TextDomain'] );
	echo '</p><p>';
	_e( 'This also determines the format of the date in the response to the Display Date of Week button.', 
		$jr_pc_plugin_data['TextDomain'] );
	echo '</p>';
	_e( "You can also position the two buttons used in the Form, and specify text and line breaks as separators. But you cannot specify the width of the buttons, as that is determined automatically by the visitor's browser based on the button's text and the Theme's font and size that applies to the button's text."
		, $jr_pc_plugin_data['TextDomain'] );
	/*	text-align and margin do the same thing, but each only works on some browsers, so do both.
	*/
	echo '<hr style="text-align: center; margin: 0 auto; height: 25px; width: 100px; background-color: black;" />';
	_e( '<b>Pixels</b>: Form Field widths are specified in Pixels in the Settings below. If you are unfamiliar with Pixels, the large black line just above is shown for reference and is 100 pixels wide and 25 pixels in height.', 
		$jr_pc_plugin_data['TextDomain'] );
	echo '<p>';
	_e( 'Height of New Line (in Pixels) is ignored if New Line Before? is not selected.  It is also ignored if it is smaller than the height of the current Date Field and the ones that follow that are displayed on this new line.  Recommendation:  start with a value of 50.'
		, $jr_pc_plugin_data['TextDomain'] );
	echo '</p>';
	
}

/**
 * Section text for Section3
 * 
 * Display an explanation of this Section
 *
 */
function jr_pc_field_expl() {
}

function jr_pc_era_expl() {
	global $jr_pc_plugin_data;
	echo '<p>';
	_e( 'This Date Field will be ignored, and will not shown in either the public Form where the site visitor enters the Date or in the response displayed after clicking the "Display Day of Week" button. This Date Field will be ignored as long as "Ancient Date Handling" continues to be set to "Do not allow Dates more then 2000 Years in the Past".', 
		$jr_pc_plugin_data['TextDomain'] );
	echo '</p>';	
}

function jr_pc_echo_form_field( $keys ) {
	global $jr_pc_plugin_data, $jr_pc_date_parts, $jr_pc_form_prefix;
	$index = $keys[0];
	$field = $keys[1];
	$settings = get_option( 'jr_pc_settings' );
	switch ( $field ) {
		case 'part':
			echo "<select id='$jr_pc_form_prefix$field$index' name='jr_pc_settings[$jr_pc_form_prefix$field$index]' size='1'>";
			foreach ( $jr_pc_date_parts as $part => $part_description ) {
				echo "<option value='$part' ";
				selected( $settings['fields'][$index][$field], $part );
				if ( ( 'era' === $part ) && ( 'NONE' === $settings['negative_year_handling'] ) ) {
					echo ">(Placeholder, not displayed)</option>";
				} else {
					echo ">$part_description</option>";
				}
			}
			echo '</select>';
			break;
		case 'break':
			echo "<input type='checkbox' id='$jr_pc_form_prefix$field$index' name='jr_pc_settings[$jr_pc_form_prefix$field$index]' value='true' ";
			checked( $settings['fields'][$index][$field] );
			echo ' />';
			break;
		case 'height':
			echo "<input type='text' id='$jr_pc_form_prefix$field$index' name='jr_pc_settings[$jr_pc_form_prefix$field$index]' size='20' maxlength='100' value='"
				. $settings['fields'][$index][$field]
				. "' />";
			break;
		case 'width':
			if ( 'button' === substr( $settings['fields'][$index]['part'], 0, 6 ) ) {
				echo '<input type="text" disabled="disabled" name="disabled" value="(automatic)" />';
			} else {
				echo "<input type='text' id='$jr_pc_form_prefix$field$index' name='jr_pc_settings[$jr_pc_form_prefix$field$index]' size='20' maxlength='100' value='"
					. $settings['fields'][$index][$field]
					. "' />";
			}
			break;
		default:
			echo "<input type='text' id='$jr_pc_form_prefix$field$index' name='jr_pc_settings[$jr_pc_form_prefix$field$index]' size='20' maxlength='100' value='"
				. $settings['fields'][$index][$field]
				. "' />";
			if ( '' !==  $settings['fields'][$index][$field] ) {
				echo ' "<tt>' . str_replace( ' ', '&para;', $settings['fields'][$index][$field] ) . '</tt>"';
			}
	}
}

function jr_pc_validate_settings( $input ) {	
	global $jr_pc_plugin_data, $jr_pc_date_parts, $jr_pc_part_fields, $jr_pc_form_prefix;
	$valid = array();
	$settings = get_option( 'jr_pc_settings' );
	$internal_settings = get_option( 'jr_pc_internal_settings' );

	/*	Verify against the rules for Shortcode Names.
	*/
	$shortcode_name = trim( $input['shortcode'] );
	if ( function_exists( 'ctype_lower' ) && !ctype_lower( $shortcode_name ) ) {
		add_settings_error(
			'jr_pc_settings',
			'jr_mt_lcshortcodeerror',
			sprintf(
				__( 'Shortcode Name must be nothing but lower-case letters; "%1$s" is not valid',
					$jr_pc_plugin_data['TextDomain'] ),
				$input['shortcode'] ),
			'error'
		);
		$valid['shortcode'] = $settings['shortcode'];
	} else {
		/*	Reset Duplicate Shortcode flag set from Public page
		*/
		if ( ( FALSE !== $internal_settings['shortcode_dup'] )
			&& ( $shortcode_name !== $settings['shortcode'] ) ) {
			$internal_settings['shortcode_dup'] = FALSE;
			update_option( 'jr_pc_internal_settings', $internal_settings );
		}
		
		/*	Since our Shortcode is ONLY defined on Public pages,
			and we are currently on an Admin page,
			it is safe to check for duplicate definitions of Shortcodes.
		*/
		if ( shortcode_exists( $shortcode_name ) ) {
			add_settings_error(
				'jr_pc_settings',
				'jr_mt_dupshortcodeerror',
				sprintf(
					__( 'Shortcode Name "%1$s" is already in use by another plugin or WordPress itself; please choose another name',
						$jr_pc_plugin_data['TextDomain'] ),
					$shortcode_name ),
				'error'
			);
			$valid['shortcode'] = $settings['shortcode'];
		} else {
			$valid['shortcode'] = $shortcode_name;
		}
	}

	/*	Radio buttons do not need to be verified for odd input.
	*/
	$valid['negative_year_handling'] = $input['negative_year_handling'];

	$remember_part = array();
	$dup_parts = FALSE;
	for ( $index = 0; $index < count( $jr_pc_date_parts ); $index++ ) {
		foreach ( $jr_pc_part_fields as $field => $field_description ) {
			switch ( $field ) {
				case 'break':
					/*	Checkbox only returns a value when a checkmark is present
					*/
					$valid['fields'][$index][$field] = isset( $input["$jr_pc_form_prefix$field$index"] );
					break;
				case 'height':
					$height = trim( $input["$jr_pc_form_prefix$field$index"] );
					$valid_height = TRUE;
					if ( empty( $height ) ) {
						$height = '0';
					}
					if ( function_exists( 'ctype_digit' ) ) {
						if ( !ctype_digit( $height ) ) {
							$valid_height = FALSE;
						}
					} else {
						if ( !is_numeric( $height ) ) {
							$valid_height = FALSE;
						}
					}
					if ( $valid_height ) {
						if ( ( $height < 0 ) || ( $height > 100 ) ) {
							$valid_height = FALSE;
						}
					}
					if ( !$valid_height ) {
						add_settings_error(
							'jr_pc_settings',
							'jr_mt_heighterror',
							sprintf(
								__( 'Date Field %1$s New Line Height (pixels) must be an integer between 0 and 100',
									$jr_pc_plugin_data['TextDomain'] ),
								$index + 1 ),
							'error'
						);
					}
					$valid['fields'][$index][$field] = $height;
					break;
				case 'part':
					/*	Check for duplicate Date Parts
						(ignore blank Parts)
					*/
					if ( in_array( $input["$jr_pc_form_prefix$field$index"], $remember_part, TRUE ) ) {
						$dup_parts = TRUE;
						add_settings_error(
							'jr_pc_settings',
							'jr_mt_parterror',
							sprintf(
								__( 'Date Field %1$s Date Part is duplicated from Date Field %2$s',
									$jr_pc_plugin_data['TextDomain'] ),
								$index + 1, array_search( $input["$jr_pc_form_prefix$field$index"], $remember_part ) + 1, TRUE ),
							'error'
						);
					}
					$valid['fields'][$index][$field] = $input["$jr_pc_form_prefix$field$index"];
					$remember_part[$index] = $input["$jr_pc_form_prefix$field$index"];
					break;
				case 'width':
					if ( 'button' !== substr( $settings['fields'][$index]['part'], 0, 6 ) ) {
						$width = trim( $input["$jr_pc_form_prefix$field$index"] );
						$valid_width = TRUE;
						if ( empty( $width ) ) {
							$valid_width = FALSE;
						} else {
							if ( function_exists( 'ctype_digit' ) ) {
								if ( !ctype_digit( $width ) ) {
									$valid_width = FALSE;
								}
							} else {
								if ( !is_numeric( $width ) ) {
									$valid_width = FALSE;
								}
							}
							if ( $valid_width ) {
								if ( ( $width <= 0 ) || ( $width > 500 ) ) {
									$valid_width = FALSE;
								}
							}
						}
						if ( !$valid_width ) {
							add_settings_error(
								'jr_pc_settings',
								'jr_mt_widtherror',
								sprintf(
									__( 'Date Field %1$s Width (pixels) must be an integer between 1 and 500',
										$jr_pc_plugin_data['TextDomain'] ),
									$index + 1 ),
								'error'
							);
						}
					}
					/*	No break statement here as also want to do Default processing
					*/
				default:
					/*	Text fields also need to be sanitized, but blanks cannot be trimmed,
						as they are valid separators between fields.
					*/
					$valid['fields'][$index][$field] = $input["$jr_pc_form_prefix$field$index"];
			}
		}
	}
	
	$errors = get_settings_errors();
	if ( empty( $errors ) ) {
		add_settings_error(
			'jr_pc_settings',
			'jr_pc_saved',
			__( 'Settings Saved',
				$jr_pc_plugin_data['TextDomain'] ),
			'updated'
		);	
	}
	return $valid;
}

?>