<?php
function jr_pc_help() {
	global $jr_pc_plugin_data, $jr_pc_dir_url;
	$date = getdate();
	$year = $date['year'];
	$settings = get_option( 'jr_pc_settings' );
	
	if ( $settings['negative_year_handling'] === 'NONE' ) {
		$era_text = '';
	} else {
		/*	translators: This is the text referred to later as "Era Text".
			%1$s is A.D. or CE (depending on Setting and how you have translated them) for current years,
			and %2$s is B.C. or BCE for years more than 2000 years old.
		*/
		$era_text = sprintf( 
			__( '<li>The era, %1$s for current years and %2$s for ancient years</li>', $jr_pc_plugin_data['TextDomain'] ),
			jr_pc_century( 1 ),
			jr_pc_century( -1 )
		);
	}

	$output = '<h1>'
		. __( 'What is a Perpetual Calendar?', $jr_pc_plugin_data['TextDomain'] )
		. '</h1><p>'
		. __( 'The name Perpetual Calendar refers to the quick visual determination of the day of week for any date across a large number of years into the past, present or future. This on-line version lets you enter any date up to several thousand years into the past or future, and have the day of the week displayed for you, without the need to read a traditional calendar.',
			$jr_pc_plugin_data['TextDomain'] )
		. '</p><h1>'
		. __( 'How to Display the Day of the Week for a Date?', $jr_pc_plugin_data['TextDomain'] )
		. '</h1><p>';
	/*	translators: %1$s is a small graphic of a down arrow.
	*/
	$output .= sprintf( 
			__( 
				'To the right of the <strong>Display Day of Week</strong> button, click on the %1$s down arrow in each of the six boxes and select from the lists displayed by clicking on:',
				$jr_pc_plugin_data['TextDomain'] ),
			'<img src="' . $jr_pc_dir_url . 'images/dropdown9x5.jpg" alt="" width="9" height="5" />'
			)
		. '<ol>';
	/*	translators: %1$s is the current year as a four digit number.
		%2$s is the "Era Text" referred to in a previous translators comment.
	*/
	$output .= sprintf( 
			__( 
				'<li>The Month</li><li>The Day of the Month</li><li>The first one or two digits of the year, for example, 20 for this year, %1$s</li><li>The next digit of the year, frequently called <em>tens</em></li><li>The last digit of the year</li>%2$s',
				$jr_pc_plugin_data['TextDomain'] ),
			$year,
			$era_text
			)
		. '</ol>'
		. __( 'Then click on the <strong>Display Day of Week</strong> button.', $jr_pc_plugin_data['TextDomain'] )
		. '</p><p>'
		. __( 'You should now see a message above the Perpetual Calendar similar to the following:', $jr_pc_plugin_data['TextDomain'] )
		. '<ul><li>';
	/*	translators: %1$s is A.D. or CE or blank depending on Settings, and refers to how dates are specified to differential from Ancient Dates more than 2000 years ago.
	*/
	$output .= sprintf(
			__( 
				'February 9, 1962 %1$s is a Friday', 
				$jr_pc_plugin_data['TextDomain'] ),
			jr_pc_century( 1962 )
			)
		. '</li></ul>'
		. __( 'You may also see the message "Error:  date specified does not exist", typically when specifying a Day of the Month that does not exist, such as April 31, as April only has 30 days.',
			$jr_pc_plugin_data['TextDomain'] )
		. '</p>';
		if ( $settings['negative_year_handling'] !== 'NONE' ) {
			$output .= '<h1>' 
			. sprintf( 
				__( '%1$s and %2$s', $jr_pc_plugin_data['TextDomain'] ),
				jr_pc_century( 1 ),
				jr_pc_century( -1 )
				)
			. '</h1><p>';
			/*	translators: %1$s, %2$s and %4$s are the current four digit year.
				%3$s is A.D. or CE, depending on the Settings.
			*/
			$output .= sprintf( 
				__( 'The current year, %1$s, is more correctly known as &quot;%2$s %3$s&quot; to differentiate it from the year more than 4000 years ago with the same number, %4$s',
					$jr_pc_plugin_data['TextDomain'] ),
				$year,
				$year,
				jr_pc_century( 1 ),
				$year
				)
			. ' ';
			if ( $settings['negative_year_handling'] == 'BCE' ) {
				$output .= __( 'BCE.  For more information, on CE and BCE, <a href="http://en.wikipedia.org/wiki/Common_Era" target="_blank">click here</a>	for the <a href="http://en.wikipedia.org/wiki/Common_Era" target="_blank">Wikipedia article on the subject</a>.',
					$jr_pc_plugin_data['TextDomain'] )
				. '</p><p>'
				. __( 'Because this Perpetual Calendar can go nearly 7000 years into the past or future, all dates are shown as CE or BCE.',
					$jr_pc_plugin_data['TextDomain'] )
				. '</p>';
			} else {
				$output .= __( 'B.C.  For more information, on A.D. and B.C., <a href="http://en.wikipedia.org/wiki/A.d." target="_blank">click here</a> for the <a href="http://en.wikipedia.org/wiki/A.d." target="_blank">Wikipedia article on the subject</a>.',
					$jr_pc_plugin_data['TextDomain'] )
				. '</p><p>'
				. __( 'Because this Perpetual Calendar can go nearly 7000 years into the past or future, all dates are shown as A.D. or B.C.',
					$jr_pc_plugin_data['TextDomain'] )
				. '</p>';
			}
		}
		$output .= '<h1>'
			. __( 'About this Perpetual Calendar',
				$jr_pc_plugin_data['TextDomain'] )
			. '</h1><p>'
			. __( 'This Perpetual Calendar was written by a retired computer programmer who began writing programs in 1971.	It is available for free to any web site that wishes to use it. It was designed for WordPress, a popular piece of software used to create web sites.',
				$jr_pc_plugin_data['TextDomain'] )
			. '</p>';
	return $output;
}
?>