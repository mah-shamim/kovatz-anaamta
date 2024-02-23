<div
	class="jet-smart-filters-settings-page jet-smart-filters-settings-page__ajax-request-type"
>
	<div class="ajax-request-types">
		<div class="ajax-request-types__header">
			<div class="cx-vui-title"><?php _e( 'Ajax Request Type', 'jet-smart-filters' ); ?></div>
			<div class="cx-vui-subtitle"><?php _e( 'This option allows you to set global WordPress variables and variables from the URL in the same way as on the page from which the request was sent. Use this option if the macros or dynamic tags in your query settings don’t work properly.', 'jet-smart-filters' ); ?></div>
			<cx-vui-radio
				name="ajax-request-types"
				v-model="settings.ajax_request_types"
				:optionsList="data.ajax_request_types_options"
			>
			</cx-vui-radio>
		</div>
	</div>
</div>
