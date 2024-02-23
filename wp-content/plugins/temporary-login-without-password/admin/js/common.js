(function ($) {
	'use strict';

	$(document).ready(function () {

		if (typeof tempData !== 'undefined') {

			if (tempData.hasOwnProperty('is_temporary_login')) {

				var isTemporaryLogin = tempData.is_temporary_login;

				// Disable deactivation checkbox of Temporary Login Without Password plugin
				// For Temporary Logged in user
				if (isTemporaryLogin === "yes") {
					var elem = 'table.plugins tbody#the-list tr[data-slug=temporary-login-without-password] th.check-column input';
					if ($(elem).get(0)) {
						$(elem).attr('disabled', true);
						$(elem).hide();
					}
				}
			}
		}
	});

})(jQuery);
