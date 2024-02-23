<?php
/**
 * Booking PDF Template
 *
 * @var YITH_WCBK_Booking $booking    The booking.
 * @var int               $booking_id Booking ID.
 * @var bool              $is_admin   Is admin flag.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$font_family = apply_filters( 'yith_wcbk_pdf_font_family', 'Helvetica, sans-serif' );
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<style type="text/css">
		body {
			color       : #2f3742;
			font-family : <?php echo esc_html( $font_family ); ?>;
		}

		h2 {

		}

		a {
			text-decoration : none;
			color           : #5b6d96;
		}

		table {
			border : 0;
		}

		table.booking-table {
			font-size       : 12px;
			width           : 100%;
			border-collapse : collapse;
		}

		.booking-table th, .booking-table td {
			padding       : 12px 20px;
			text-align    : left;
			border-bottom : 1px solid #676f76;
		}

		.booking-table tr {
			margin-bottom : 5px;
		}

		table.booking_person_types_details {
			font-size : 10px;
		}

		table.booking_person_types_details th, table.booking_person_types_details td {
			padding : 7px 20px;
		}

		tr.person-tot {
			font-size : 12px;
		}

		tr.person-tot th, tr.person-tot td {
			border-top    : 2px solid #676f76;
			border-bottom : 0;
			font-weight   : 800;
		}

		.pdf-button {
			color           : #a8c6e4;
			text-decoration : none;
		}

		div.content {
			padding-bottom : 100px;
			border-bottom  : 1px
		}

		.footer {
			position   : fixed;
			bottom     : 0;
			text-align : center;
			font-size  : 70%
		}

		.footer {
			width      : 100%;
			text-align : center;
			position   : fixed;
			bottom     : 0;
		}

		.pagenum:before {
			content : counter(page);
		}
	</style>
	<?php

	do_action( 'yith_wcbk_booking_pdf_template_head' );
	?>
</head>

<body>
<?php
do_action( 'yith_wcbk_booking_pdf_template_header', $booking, $is_admin );
?>
<div class="content">
	<?php
	do_action( 'yith_wcbk_booking_pdf_template_content', $booking, $is_admin );
	?>
</div>
<?php
do_action( 'yith_wcbk_booking_pdf_template_footer', $booking, $is_admin );
?>
</body>
</html>
