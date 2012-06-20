<?php
global $jr_pc_basename;
// Add Link to the plugin's entry on the Admin "Plugins" Page, for easy access
add_filter("plugin_action_links_$jr_pc_basename", 'jr_pc_plugin_action_links', 10, 1);

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
	// The "page=" query string value must be equal to the slug
	// of the Settings admin page.
	array_unshift( $links, '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=jr_pc_settings' . '">Settings</a>' );
	return $links;
}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_filter("network_admin_plugin_action_links_$jr_pc_basename", 'jr_pc_plugin_network_action_links', 10, 1);
}

function jr_pc_plugin_network_action_links( $links ) {
	array_unshift( $links, '<a href="' . get_bloginfo('wpurl') . '/wp-admin/network/settings.php?page=jr_pc_network_settings' . '">Settings</a>' );
	return $links;
}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_action('network_admin_menu', 'jr_pc_network_admin_hook' );
}

function jr_pc_network_admin_hook() {
	//  Add Network Settings Page for this Plugin
	add_network_options_page( JR_PC_PLUGIN_NAME, 'Perpetual Calendar plugin', 'manage_network_options', 'jr_pc_network_settings', 'jr_pc_network_settings_page' );
}

function jr_pc_network_settings_page() {
	echo '<div class="wrap">';
	screen_icon( 'plugins' );
	echo '<h2>' . JR_PC_PLUGIN_NAME . '</h2>';
	?>
	<p>
	This plugin has been Network Activated in a WordPress Multisite ("Network") installation.
	Since all of this plugin's Settings can be specified separately for each individual WordPress site,
	you will need to go to the relevant individual Site's Settings page for this plugin to change the Settings for that Site.
	</p>
	<p>
	Unfortunately, when WordPress network activates a plugin,
	you will not see an entry for that plugin on each individual Site's Plugins page.
	Needless to say, this can be very confusing.
	Therefore, we recommend that you Network Deactivate this plugin
	and Activate it individually on each Site where you wish to use it.
	</p>
	<p>
	Alternatively, if you would prefer to have a single set of Settings that would apply to all sites in a WordPress network,
	please contact the Plugin author and this will be added to a future version of this plugin if there is enough interest expressed by webmasters such as you.
	</p>
	<?php
}

add_action( 'admin_menu', 'jr_pc_admin_hook' );

/**
 * Add Admin Menu item for plugin
 * 
 * Plugin needs its own Page in the Settings section of the Admin menu.
 *
 */
function jr_pc_admin_hook() {
	$settings = get_option( 'jr_pc_settings' );
	//  Add Settings Page for this Plugin
	add_options_page( JR_PC_PLUGIN_NAME, 'Perpetual Calendar plugin', 'manage_options', 'jr_pc_settings', 'jr_pc_settings_page' );
}

/**
 * Settings page for plugin
 * 
 * Display and Process Settings page for this plugin,
 * and allow all captions to be updated with IPTC fields.
 *
 */
function jr_pc_settings_page() {
	echo '<div class="wrap">';
	screen_icon( 'plugins' );
	echo '<h2>' . JR_PC_PLUGIN_NAME . '</h2>';
	echo '<p>This plugin provides a Perpetual Calendar for your site visitors via the ';
	echo jr_pc_display_shortcode( '[pcal]' );
	echo ' Shortcode, and for your own use within php code via the <code>jr_pc_weekday( $year, $month, $day )</code> function.</p>';
	echo '<p>A Perpetual Calendar provides the day of the week for virtually any date in the past, present or future.</p>';

	//	Plugin Settings are displayed and entered here:
	echo '<form action="options.php" method="POST">';
	settings_fields( 'jr_pc_settings' );
	do_settings_sections( 'jr_pc_settings_page' );
	echo '<p><input name="save" type="submit" value="Save Changes" class="button-primary" /></p></form>';
}

add_action( 'admin_init', 'jr_pc_admin_init' );

/**
 * Register and define the settings
 * 
 * Everything to be stored and/or can be set by the user
 *
 */
function jr_pc_admin_init() {
	register_setting( 'jr_pc_settings', 'jr_pc_settings', 'jr_pc_validate_settings' );
	add_settings_section( 'jr_pc_display_settings_section', 
		'Perpetual Calendar Settings', 
		'jr_pc_display_settings_expl', 
		'jr_pc_settings_page' 
	);
	add_settings_field( 'negative_year_handling', 'Ancient Date Handling', 'jr_pc_echo_negative_year_handling', 'jr_pc_settings_page', 'jr_pc_display_settings_section' );
}

/**
 * Section text for Section1
 * 
 * Display an explanation of this Section
 *
 */
function jr_pc_display_settings_expl() {
	echo 'Here is where you define how dates more than 2000 years ago will be handled. And how years since then, and into the future, will be displayed.';
}

function jr_pc_echo_negative_year_handling() {
	$settings = get_option( 'jr_pc_settings' );
	$negative_year_handling = $settings['negative_year_handling'];
	$choices = 
		array(
			array(
				'value' => 'BC',
				'display' => 'Current and Future Dates as A.D., Ancient Dates as B.C.'
			),
			array(
				'value' => 'BCE',
				'display' => 'Current and Future Dates as CE, Ancient Dates as BCE'
			),
			array(
				'value' => 'NONE',
				'display' => 'Do not allow Dates more then 2000 Years in the Past'
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

function jr_pc_validate_settings( $input ) {
	$valid = array();
	//	Radio buttons do not need to be verified for odd input:
	$valid['negative_year_handling'] = $input['negative_year_handling'];
	return $valid;
}

?>