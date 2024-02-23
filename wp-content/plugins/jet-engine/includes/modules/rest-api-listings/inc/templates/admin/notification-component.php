<div class="jet-rest-notification">
	<div class="jet-rest-notification__fields">
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'REST API URL:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<textarea @input="setField( $event, 'url' )" :value="result.url" rows="2" style="height: 50px;"></textarea>
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<div class="jet-form-editor__row-notice"><?php _e( 'You can use these macros as dynamic part of the URL: ', 'jet-engine' ); ?>{{ fieldsString }}</div>
		</div>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Custom Body:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<textarea @input="setField( $event, 'body' )" :value="result.body"></textarea>
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<div class="jet-form-editor__row-notice"><?php _e( 'By default API request will use all form data as body. Here you can set custom body of your API request in the JSON format. <a href="https://www.w3dnetwork.com/json-formatter.html" target="_blank">Online editing tool</a> - swith to the <b><i>Tree View</i></b>, edit object as you need, than swith to <b><i>Plain Text</i></b> and copy/paste result here. You can use the same macros as for the URL.', 'jet-engine' ); ?></div>
		</div>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Authorization:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<input type="checkbox" @change="setField( $event, 'authorization' )" :checked="result.authorization">
			</div>
		</div>
		<div class="jet-form-editor__row" v-if="result.authorization">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Authorization type:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<select @change="setField( $event, 'auth_type' )" :value="result.auth_type">
					<option v-for="type in authTypes" :value="type.value" :selected="type.value === result.auth_type">{{ type.label }}</option>
				</select>
			</div>
		</div>
		<?php do_action( 'jet-engine/rest-api-listings/form/auth-controls' ); ?>
	</div>
</div>