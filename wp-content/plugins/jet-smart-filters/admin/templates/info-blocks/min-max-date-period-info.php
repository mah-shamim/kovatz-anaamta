<?php
/**
 * Min/Max date period info template
 */
?>
<div class="min-max-date-period-info">
<?php
	printf(
		'%s - <strong>"2020-05-25"</strong>. ',
		__( 'To set the limit by date, fill in the date in the following format', 'jet-smart-filters' )
	);
	printf(
		__('You can also use the keyword "%1$s" instead of the year, month, or day. Example: %2$s - the limit will be the 15th of the current year and month', 'jet-smart-filters'),
		'<strong>current</strong>',
		'<strong>current - current - 15</strong>'
	);
	echo '.<br>';
	printf(
		'%s - <strong>"today"</strong>. ',
		__( 'To set the limit by the current date, fill in', 'jet-smart-filters' )
	);
	echo __( 'Plus date operations are available with the following structure: + or - N number and unit of the date such as day(s), week(s), month(s) or year(s)', 'jet-smart-filters' );
	echo '. ';
	printf(
		__('Example: %s', 'jet-smart-filters'),
		'<strong>today + 3 days</strong>'
	);
	echo '.<br>';
	echo __( 'Leave the field empty if there is no limitation needed', 'jet-smart-filters' );
?>
</div>