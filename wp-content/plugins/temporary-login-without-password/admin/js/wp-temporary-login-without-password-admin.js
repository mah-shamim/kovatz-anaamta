/**
 * Admin Settings Js.
 *
 * @package Temporary Login Without Password
 */

(function ($) {
	'use strict';

	$(document).ready(
		function () {

			$('#add-new-wtlwp-form-button').click(
				function () {
					$('#new-wtlwp-form').show();
					$('#update-wtlwp-form').hide();
				}
			);

			$('#cancel-new-login-form').click(
				function () {
					$('#new-wtlwp-form').hide();
					$('#update-wtlwp-form').show();
				}
			);

			$('#cancel-update-login-form').click(
				function () {
					$('#update-wtlwp-form').hide();
				}
			);

			if ($('.wtlwp-click-to-copy-btn').get(0)) {

				var clipboard = new Clipboard('.wtlwp-click-to-copy-btn');

				clipboard.on(
					'success', function (e) {
						var elem = e.trigger;
						var className = elem.getAttribute('class');
						$('#copied-text-message-' + className).text('Copied').fadeIn();
						$('#copied-text-message-' + className).fadeOut('slow');
					}
				);
			}

			if ($('.wtlwp-copy-to-clipboard').get(0)) {
				var clipboard_link = new Clipboard('.wtlwp-copy-to-clipboard');

				clipboard_link.on(
					'success', function (e) {
						var elem = e.trigger;
						var id = elem.getAttribute('id');
						$('#copied-' + id).text('Copied').fadeIn();
						$('#copied-' + id).fadeOut('slow');
					}
				);
			}

			$('#new-user-expiry-time').change(
				function () {
					var value = $(this).val();
					showDatePicker(value, 'new');
				}
			);

			$('#update-user-expiry-time').change(
				function () {
					var value = $(this).val();
					showDatePicker(value, 'update');
				}
			);

			function showDatePicker(value, datePickerClass) {

				var customDatePickerClass = '';
				var customDatePickerID = '';
				if ('new' === datePickerClass) {
					customDatePickerClass = '.new-custom-date-picker';
					customDatePickerID = '#new-custom-date-picker';
				} else {
					customDatePickerClass = '.update-custom-date-picker';
					customDatePickerID = '#update-custom-date-picker';
				}

				if (value === 'custom_date') {
					var tomorrowDate = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
					$(customDatePickerClass).datepicker(
						{
							dateFormat: 'yy-mm-dd',
							minDate: tomorrowDate
						}
					);
					$(customDatePickerID).show();
				} else {
					$(customDatePickerID).hide();
				}
			}

			/* Add tailwind classes to language dropdown */
			$('.wtlwp-language-dropdown select').addClass('form-select font-normal text-gray-600 h-8 shadow-sm');

			// Confirm before delete
			$('.tlwp-delete').click(function () {
				// escape here if the confirm is false;
				return confirm('Do you want to delete temporary user?');
			});

		}
	);

})(jQuery);
