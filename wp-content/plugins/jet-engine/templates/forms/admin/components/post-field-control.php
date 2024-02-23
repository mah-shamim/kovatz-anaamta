<div class="jet-post-field-control">
	<select :value="fieldType" @input="setField( $event, 'field_type' )" style="width: 160px;">
		<option v-for="( fieldLabel, fieldKey ) in fields" :value="fieldKey">{{ fieldLabel }}</option>
	</select>
	<input
		type="text"
		v-if="metaProp === fieldType"
		:value="fieldName"
		@input="setField( $event, 'field_name' )"
		style="width: 200px;"
	>
	<select
		v-if="termsProp === fieldType"
		:value="fieldName"
		@input="setField( $event, 'field_name' )"
		style="width: 160px;"
	>
		<option
			v-for="( taxName, taxSlug ) in taxonomies"
			:value="'jet_tax__' + taxSlug"
		>{{ taxName }}</option>
	</select>
</div>