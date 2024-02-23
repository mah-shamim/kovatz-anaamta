<div id="gateways_data">
	<div class="jet-engine-gateways-row"><?php
		_e( 'If you want to process any payments on this form submission, please select payment gateway', 'jet-engine' );
	?></div>
	<div class="jet-engine-gateways-row">
		<div>
			<input id="gateway_none" name="_gateways[gateway]" value="none" v-model="gateways.gateway" type="radio">
			<label for="gateway_none"><?php _e( 'None', 'jet-engine' ); ?></label>
		</div>
		<?php

		foreach ( $gateways as $gateway ) {
			?>
			<div>
				<input id="gateway_<?php echo $gateway->get_id(); ?>" name="_gateways[gateway]" value="<?php echo $gateway->get_id(); ?>" v-model="gateways.gateway" type="radio">
				<label for="gateway_<?php echo $gateway->get_id(); ?>"><?php echo $gateway->get_name(); ?></label>
			</div>
			<?php
		}
		?>
	</div>
	<div class="jet-engine-gateways-row" v-if="'none' !== gateways.gateway && ! hasPostNotification()"><b><?php
		_e( 'Forms with payment gateways must contain "Insert/Update Post" notification to correctly create and process payemnt orders. Please set up "Insert/Update Post" notification to create order-related posts', 'jet-engine' );
	?></b></div>
	<div class="jet-engine-gateways-group" v-if="notificationsList && notificationsList.length">
		<div class="jet-engine-gateways-group__title"><?php
			_e( 'Notifications queue:', 'jet-engine' );
		?></div>
		<div class="jet-engine-gateways-group__item" style="flex: 0 0 100%; margin: 0 0 20px;">
			<div class="jet-engine-gateways-group__item-title"><?php
				_e( 'Create payment order notification:', 'jet-engine' );
			?></div>
			<div v-for="( notification, index ) in notificationsList" v-if="'insert_post' === notification.type" :key="'order_' + index">
				<label>
					<input type="radio" name="_gateways[notifications_order]" :value="index" v-model="gateways.notifications_order">
					{{ getNotificationLabel( notification ) }}
				</label>
			</div>
		</div>
		<div class="jet-engine-gateways-group__item">
			<div class="jet-engine-gateways-group__item-title"><?php
				_e( 'Before payment processed:', 'jet-engine' );
			?></div>
			<div v-for="( notification, index ) in notificationsList" v-if="'redirect' !== notification.type" :key="'before_' + index">
				<label>
					<input type="checkbox" name="_gateways[notifications_before][]" :value="index" v-model="gateways.notifications_before">
					{{ getNotificationLabel( notification ) }}
				</label>
			</div>
		</div>
		<div class="jet-engine-gateways-group__item">
			<div class="jet-engine-gateways-group__item-title"><?php
				_e( 'On successfull payment:', 'jet-engine' );
			?></div>
			<div v-for="( notification, index ) in notificationsList" v-if="'redirect' !== notification.type" :key="'success_' + index">
				<label>
					<input type="checkbox" name="_gateways[notifications_success][]" :value="index" v-model="gateways.notifications_success">
					{{ getNotificationLabel( notification ) }}
				</label>
			</div>
		</div>
		<div class="jet-engine-gateways-group__item">
			<div class="jet-engine-gateways-group__item-title"><?php
				_e( 'On failed payment:', 'jet-engine' );
			?></div>
			<div v-for="( notification, index ) in notificationsList" v-if="'redirect' !== notification.type" :key="'failed_' + index">
				<label>
					<input type="checkbox" name="_gateways[notifications_failed][]" :value="index" v-model="gateways.notifications_failed">
					{{ getNotificationLabel( notification ) }}
				</label>
			</div>
		</div>
	</div>
	<div class="jet-engine-gateways-row">
		<label for="gateways_price_field"  class="jet-engine-gateways-row__label"><?php
			_e( 'Price/amount field', 'jet-engine' );
		?></label>
		<select v-model="gateways.price_field" name="_gateways[price_field]" id="gateways_price_field">
			<option v-for="( field, index ) in availableFields" :value="field" :key="field + index">{{ field }}</option>
		</select>
	</div>
	<?php do_action( 'jet-engine/forms/gateways/fields' ); ?>
	<div class="jet-engine-gateways-section">
		<div class="jet-engine-gateways-section__title"><?php
			_e( 'Payment result messages:', 'jet-engine' );
		?></div>
		<div class="jet-engine-gateways-row">
			<label for="gateways_success_message" class="jet-engine-gateways-row__label"><?php
				_e( 'Payment success message', 'jet-engine' );
			?></label>
			<textarea v-model="gateways.success_message" id="gateways_success_message" name="_gateways[success_message]" rows="7"></textarea>
		</div>
		<div class="jet-engine-gateways-row">
			<label for="gateways_failed_message"  class="jet-engine-gateways-row__label"><?php
				_e( 'Payment failed message', 'jet-engine' );
			?></label>
			<textarea v-model="gateways.failed_message" id="gateways_failed_message" name="_gateways[failed_message]" rows="7"></textarea>
		</div>
		<div class="jet-engine-gateways-macros-list">
			<div class="jet-engine-gateways-macros-list__title"><?php
				_e( 'Available macros list:', 'jet-engine' );
			?></div>
			<ul>
				<li>%gateway_amount% - <?php _e( 'payment amount returned from gateway template;', 'jet-engine' ); ?></li>
				<li>%gateway_status% - <?php _e( 'payemnt status returned from payment gateway;', 'jet-engine' ); ?></li>
				<li>%field_name% - <?php _e( 'replace "field_name" with any field name from the form;', 'jet-engine' ); ?></li>
			</ul>
		</div>
		<div class="jet-engine-gateways-row" v-if="hasRedirectNotification">
			<label>
				<input type="checkbox" name="_gateways[use_success_redirect]" v-model="gateways.use_success_redirect" value="1">
				<?php _e( 'Use redirect URL from Redirect notification', 'jet-engine' ); ?>
			</label>
		</div>
	</div>
</div>