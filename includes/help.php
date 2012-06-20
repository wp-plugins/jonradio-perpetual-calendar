<?php
function jr_pc_help() {
		global $jr_pc_dir_url;
		$date = getdate();
		$year = $date['year'];
		$output = '
		<h1>What is a Perpetual Calendar?</h1>
		<p>The name <em>Perpetual Calendar</em> refers to the ability to see a calendar for <em>any</em> date in the past, present or future. 
		Because they are almost always used to figure out the day of the week for a particular date, 
		this one lets you enter a date and have the day of the week displayed for you, 
		without the need to read a traditional calendar.
		</p>
		<h1>How to Display the Day of the Week for a Date?</h1>
		<p>To the right of the <strong>Display Day of Week</strong> button, click on the <img src="' . $jr_pc_dir_url . 'images/dropdown9x5.jpg"
		alt="" width="9" height="5" /> down arrow in each of the five boxes and select from the lists displayed by clicking on:
		<ol>
			<li>the Month</li>
			<li>the Day of the Month</li>
			<li>the first one or two digits of the year, for example, <strong> ' . jr_pc_century( $year ) .
		"20</strong> for this year, $year" . '
		</li>
			<li>the next digit of the year, frequently called <em>tens</em></li>
			<li>the last digit of the year</li>
		</ol>
		Then click on the <strong>Display Day of Week</strong> button.
		</p>
		
		<p>You should now see a message above the Perpetual Calendar similar to the following:
		<ul>
			<li>February 9, 1962 ' . jr_pc_century( 1962 ) .	'is a Friday</li>
		</ul>
		You may also see the message "Error:  date specified does not exist", typically when specifying a Day of the Month that does not exist, such as April 31, as April only has 30 days.
		</p>';
		$settings = get_option( 'jr_pc_settings' );
		if ( $settings['negative_year_handling'] != 'NONE' ) {
			$output .= '<h1>' . jr_pc_century( 1 ) . ' and ' . jr_pc_century( -1 ) .
				"</h1><p>The current year, $year, is more correctly known as &quot;$year " .
				jr_pc_century( 1 ) . "&quot; to differentiate it from the year more than 4000 years ago with the same number, $year ";
			if ( $settings['negative_year_handling'] == 'BCE' ) {
				$output .= 'BCE.  For more information, on CE and BCE, 
					<a href="http://en.wikipedia.org/wiki/Common_Era" target="_blank">click here</a>
					for the <a href="http://en.wikipedia.org/wiki/Common_Era" target="_blank">Wikipedia article on the subject</a>.
				</p>
				
				<p>Because this Perpetual Calendar can go nearly 7000 years into the past or future, all dates are shown as CE or BCE.
				</p>';
			} else {
				$output .= 'B.C.  For more information, on A.D. and B.C., 
					<a href="http://en.wikipedia.org/wiki/A.d." target="_blank">click here</a> 
					for the <a href="http://en.wikipedia.org/wiki/A.d." target="_blank">Wikipedia article on the subject</a>.
				</p>
				
				<p>Because this Perpetual Calendar can go nearly 7000 years into the past or future, all dates are shown as A.D. or B.C.
				</p>';
			}
		}
		$output .= '<h1>About this Perpetual Calendar</h1>
			<p>This Perpetual Calendar was written by a retired computer programmer who began writing programs in 1971.
			It is available for free to any web site that wishes to use it.  
			It was designed for WordPress, a popular piece of software used to create web sites.
			</p>';
	return $output;
}
?>