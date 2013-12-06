<?php
function jr_pc_help() {
	global $jr_pc_plugin_data;
	$date = getdate();
	$year = $date['year'];
	$output = '<h1>'
		. __( 'What is a Perpetual Calendar?', $jr_pc_plugin_data['TextDomain'] )
		. '</h1><p>'
		. __( 'The name <em>Perpetual Calendar</em> refers to the ability to see a calendar for <em>any</em> date in the past, present or future. Because they are almost always used to figure out the day of the week for a particular date, this one lets you enter a date and have the day of the week displayed for you, without the need to read a traditional calendar.',
			$jr_pc_plugin_data['TextDomain'] )
		. '</p><h1>'
		. __( 'How to Display the Day of the Week for a Date?', $jr_pc_plugin_data['TextDomain'] )
		. '</h1><p>'
		. sprintf( 
			__( 
				'To the right of the <strong>Display Day of Week</strong> button, click on the %1$s down arrow in each of the five boxes and select from the lists displayed by clicking on:',
				$jr_pc_plugin_data['TextDomain'] ),
			'<img src="' . $jr_pc_dir_url . 'images/dropdown9x5.jpg" alt="" width="9" height="5" />'
			)
		. '<ol><li>'
		. __( 'the Month', $jr_pc_plugin_data['TextDomain'] )
		. '</li><li>'
		. __( 'the Day of the Month', $jr_pc_plugin_data['TextDomain'] )
		. '</li><li>'
		. sprintf( 
			__( 
				'the first one or two digits of the year, for example, <strong>%1$s 20</strong> for this year, %2$s',
				$jr_pc_plugin_data['TextDomain'] ),
			jr_pc_century( $year ), 
			$year
			)
		. '</li><li>'
		. __( 'the next digit of the year, frequently called <em>tens</em>', $jr_pc_plugin_data['TextDomain'] )
		. '</li><li>'
		. __( 'the last digit of the year', $jr_pc_plugin_data['TextDomain'] )
		. '</li></ol>'
		. __( 'Then click on the <strong>Display Day of Week</strong> button.', $jr_pc_plugin_data['TextDomain'] )
		. '</p><p>'
		. __( 'You should now see a message above the Perpetual Calendar similar to the following:', $jr_pc_plugin_data['TextDomain'] )
		. '<ul><li>'
		. sprintf( 
			__( 
				'February 9, 1962 %1$s is a Friday', 
				$jr_pc_plugin_data['TextDomain'] ),
			jr_pc_century( 1962 )
			)
		. '</li></ul>'
		. __( 'You may also see the message "Error:  date specified does not exist", typically when specifying a Day of the Month that does not exist, such as April 31, as April only has 30 days.',
			$jr_pc_plugin_data['TextDomain'] )
		. '</p>';
		$settings = get_option( 'jr_pc_settings' );
		if ( $settings['negative_year_handling'] != 'NONE' ) {
			$output .= '<h1>' 
			. sprintf( 
				__( '%1$s and %2$s', $jr_pc_plugin_data['TextDomain'] ),
				jr_pc_century( 1 ),
				jr_pc_century( -1 )
				)
			. '</h1><p>'
			. sprintf( 
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