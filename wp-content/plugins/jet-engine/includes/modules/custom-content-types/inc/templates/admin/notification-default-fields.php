<div class="jet-cct-defaults">
	<div class="jet-cct-defaults__overlay"></div>
	<div class="jet-cct-defaults__content">
		<div class="jet-cct-defaults__fields">
			<div class="jet-cct-defaults__field" v-for="field in fields" v-if="'_ID' !== field.value">
				<label>
					<input type="checkbox" @input="swithFieldStatus( $event, field.value )" :checked="isEnabled( field.value )">
					<span>{{ field.label }}</span>
				</label>
				<input type="text" :disabled="! isEnabled( field.value )" :value="getCurrentVal( field.value )" @input="setFieldValue( $event.target.value, field.value )">
			</div>
		</div>
	</div>
</div>