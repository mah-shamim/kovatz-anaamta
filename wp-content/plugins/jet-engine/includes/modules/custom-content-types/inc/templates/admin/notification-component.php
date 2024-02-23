<div class="jet-cct-notification">
	<div class="jet-cct-notification__empty" v-if="! contentTypes || ! contentTypes.length"><?php
		_e( 'There is no content types found on your website', 'jet-engine' );
	?></div>
	<div class="jet-cct-notification__fields" v-else>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Content Type:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<select @input="setField( $event, 'type' )" :value="result.type">
					<option value="">--</option>
					<option v-for="type in contentTypes" :value="type.value">{{ type.label }}</option>
				</select>
			</div>
		</div>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php
				_e( 'Item Status:', 'jet-engine' );
			?></div>
			<div class="jet-form-editor__row-control">
				<select @input="setField( $event, 'status' )" :value="result.status">
					<option value="">--</option>
					<option v-for="( label, value ) in statuses" :value="value">{{ label }}</option>
				</select>
			</div>
		</div>
		<div :class="{ 'jet-form-editor__row': true, 'jet-cct-loading': isLoading }">
			<div class="jet-form-editor__row-label"><?php _e( 'Fields Map:', 'jet-engine' ); ?></div>
			<div class="jet-form-editor__row-control">
				<div class="jet-form-editor__row-notice"><?php
					_e( 'Select content type fields to save apropriate form fields into', 'jet-engine' );
				?></div>
				<div class="jet-form-editor__row-fields">
					<div class="jet-form-editor__row-map" v-for="( field, index ) in formFields" :key="field + index">
						<span>{{ field }}</span>
						<select @input="setFieldsMap( $event, field )" :value="result.fields_map[ field ]">
							<option value="">--</option>
							<option v-for="typeField in typeFields" :value="typeField.value" :selected="typeField.value === result.fields_map[ field ]">
								{{ typeField.label }}
							</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div :class="{ 'jet-form-editor__row': true, 'jet-cct-loading': isLoading }">
			<div class="jet-form-editor__row-label"><?php _e( 'Default Fields:', 'jet-engine' ); ?></div>
			<div class="jet-form-editor__row-control">
				<div class="jet-form-editor__row-notice"><?php
					_e( 'Define default fields values which should be set on the CCT item creation', 'jet-engine' );
				?></div>
				<div class="jet-form-editor__row-fields">
					<jet-cct-defaults-editor @input="setFieldValue( $event, 'default_fields' )" :value="result.default_fields" :fields="typeFields"/>
				</div>
			</div>
		</div>
	</div>
</div>