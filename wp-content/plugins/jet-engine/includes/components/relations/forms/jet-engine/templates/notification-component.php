<div class="jet-notification">
	<div class="jet-notification__empty" v-if="! relations || ! relations.length"><?php
		_e( 'There is no relations found on your website', 'jet-engine' );
	?></div>
	<div class="jet-notification__fields" v-else>
		<div class="jet-form-editor__row" style="border-top: 1px solid #ddd;">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Relation:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<select @input="setField( $event, 'relation' )" :value="result.relation">
					<option value="">--</option>
					<option v-for="relation in relations" :key="relation.value" :value="relation.value">{{ relation.label }}</option>
				</select>
			</div>
		</div>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Parent Item ID:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<select @input="setField( $event, 'parent_id' )" :value="result.parent_id">
					<option value="">--</option>
					<option v-for="field in formFields" :key="'parent_' + field" :value="field">{{ field }}</option>
				</select>
			</div>
		</div>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Child Item ID:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<select @input="setField( $event, 'child_id' )" :value="result.child_id">
					<option value="">--</option>
					<option v-for="field in formFields" :key="'child_' + field" :value="field">{{ field }}</option>
				</select>
			</div>
		</div>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Update Context:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<select @input="setField( $event, 'context' )" :value="result.context">
					<option value="child"><?php _e( 'We updating children items for the parent object', 'jet-engine' ); ?></option>
					<option value="parent"><?php _e( 'We updating parent items for the child object', 'jet-engine' ); ?></option>
				</select>
			</div>
		</div>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'How to Store New Items:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<select @input="setField( $event, 'store_items_type' )" :value="result.store_items_type">
					<option value="replace"><?php _e( 'Replace existing related items with items from the form (default)', 'jet-engine' ); ?></option>
					<option value="append"><?php _e( 'Append new items to already existing related items', 'jet-engine' ); ?></option>
					<option value="disconnect"><?php _e( 'Disconnect selected items', 'jet-engine' ); ?></option>
				</select>
			</div>
		</div>
	</div>
</div>
