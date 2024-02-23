<?php
/**
 * Calendar header template
 */

global $wp_locale;

?>
<caption class="jet-calendar-caption">
	<div class="jet-calendar-caption__wrap wrap-<?php echo $settings['caption_layout']; ?>">
		<div class="jet-calendar-caption__name"><?php echo date_i18n( 'F Y', $current_month ); ?></div>
		<div class="jet-calendar-nav__link nav-link-prev" data-month="<?php echo $human_read_prev; ?>">
			<svg viewBox="0 0 90 179" xmlns="http://www.w3.org/2000/svg"><path transform="scale(0.1,-0.1) translate(0,-1536)" d="M627 992q0 -13 -10 -23l-393 -393l393 -393q10 -10 10 -23t-10 -23l-50 -50q-10 -10 -23 -10t-23 10l-466 466q-10 10 -10 23t10 23l466 466q10 10 23 10t23 -10l50 -50q10 -10 10 -23z" /></svg>
		</div>
		<div class="jet-calendar-nav__link nav-link-next" data-month="<?php echo $human_read_next; ?>">
			<svg viewBox="0 0 90 179" xmlns="http://www.w3.org/2000/svg"><path transform="scale(0.1,-0.1) translate(0,-1536)" d="M627 992q0 -13 -10 -23l-393 -393l393 -393q10 -10 10 -23t-10 -23l-50 -50q-10 -10 -23 -10t-23 10l-466 466q-10 10 -10 23t10 23l466 466q10 10 23 10t23 -10l50 -50q10 -10 10 -23z" /></svg>
		</div>
	</div>
</caption>
<thead class="jet-calendar-header">
	<tr class="jet-calendar-header__week"><?php

		for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
			$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
		}

		foreach ( $myweek as $wd ) {

			switch ( $days_format ) {
				case 'short':
					$day_name = $wp_locale->get_weekday_abbrev( $wd );
					break;

				case 'initial':
					$day_name = $wp_locale->get_weekday_initial( $wd );
					break;

				default:
					$day_name = $wd;
					break;
			}

			printf( '<th class="jet-calendar-header__week-day">%s</th>', $day_name );
		}

	?></tr>
</thead>