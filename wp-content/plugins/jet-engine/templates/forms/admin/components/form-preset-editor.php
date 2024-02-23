<div class="jet-form-canvas__preset-editor">
	<div class="jet-form-canvas__preset-row">
		<span><?php _e( 'Source:', 'jet-engine' ); ?></span>
		<select type="text" name="_preset[from]" v-model="preset.from">
			<option v-for="source in presetSources" :key="source.value" :value="source.value">{{ source.label }}</option>
		</select>
	</div>
	<div class="jet-form-canvas__preset-row" v-if="'post' === preset.from">
		<span><?php _e( 'Get post ID from:', 'jet-engine' ); ?></span>
		<select type="text" name="_preset[post_from]" v-model="preset.post_from">
			<option value="current_post"><?php _e( 'Current post', 'jet-engine' ); ?></option>
			<option value="query_var"><?php _e( 'URL Query Variable', 'jet-engine' ); ?></option>
		</select>
	</div>
	<div class="jet-form-canvas__preset-row" v-if="'user' === preset.from">
		<span><?php _e( 'Get user ID from:', 'jet-engine' ); ?></span>
		<select type="text" name="_preset[user_from]" v-model="preset.user_from">
			<option value="current_user"><?php _e( 'Current user', 'jet-engine' ); ?></option>
			<option value="query_var"><?php _e( 'URL Query Variable', 'jet-engine' ); ?></option>
		</select>
	</div>
	<div class="jet-form-canvas__preset-row" v-if="( 'post' === preset.from && 'query_var' === preset.post_from ) || ( 'user' === preset.from && 'query_var' === preset.user_from )">
		<span><?php _e( 'Query variable name:', 'jet-engine' ); ?></span>
		<input type="text" name="_preset[query_var]" v-model="preset.query_var">
	</div>
	<?php do_action( 'jet-engine/forms/preset-editor/custom-controls-source' ); ?>
	<div class="jet-form-canvas__preset-row" v-if="availableFields">
		<span><?php _e( 'Fields Map:', 'jet-engine' ); ?></span>
		<div class="jet-form-canvas__preset-fields-map">
			<div class="jet-form-editor__row-map" v-for="field in availableFields">
				<span><i>{{ field }}</i></span>
				<div class="jet-post-field-control">
					<input :name="'_preset[fields_map][' + field + '][key]'" placeholder="<?php _e( 'Query variable key' ); ?>" v-model="preset.fields_map[ field ].key" v-if="'query_vars' === preset.from" type="text">
					<div class="jet-post-field-control__inner" v-if="'post' === preset.from">
						<select :name="'_preset[fields_map][' + field + '][prop]'" v-if="'post' === preset.from" v-model="preset.fields_map[ field ].prop">
							<option value=""><?php _e( 'Select post property...', 'jet-engine' ); ?></option>
							<option value="ID"><?php _e( 'Post ID', 'jet-engine' ); ?></option>
							<option value="post_title"><?php _e( 'Post Title', 'jet-engine' ); ?></option>
							<option value="post_name"><?php _e( 'Post Slug', 'jet-engine' ); ?></option>
							<option value="post_content"><?php _e( 'Post Content', 'jet-engine' ); ?></option>
							<option value="post_date"><?php _e( 'Post Date', 'jet-engine' ); ?></option>
							<option value="post_date_gmt"><?php _e( 'Post Date GMT', 'jet-engine' ); ?></option>
							<option value="post_excerpt"><?php _e( 'Post Excerpt', 'jet-engine' ); ?></option>
							<option value="post_thumb"><?php _e( 'Post Thumbnail', 'jet-engine' ); ?></option>
							<option value="post_meta"><?php _e( 'Post Meta', 'jet-engine' ); ?></option>
							<option value="post_terms"><?php _e( 'Post Terms', 'jet-engine' ); ?></option>
						</select>
						<input :name="'_preset[fields_map][' + field + '][key]'" placeholder="<?php _e( 'Meta field key' ) ?>" v-if="'post_meta' === preset.fields_map[ field ].prop" v-model="preset.fields_map[ field ].key" type="text">
						<select :name="'_preset[fields_map][' + field + '][key]'" v-if="'post_terms' === preset.fields_map[ field ].prop" v-model="preset.fields_map[ field ].key">
							<option v-for="( taxLabel, taxValue ) in taxonomies" :value="taxValue" >
								{{ taxLabel }}
							</option>
						</select>
					</div>
					<div class="jet-post-field-control__inner" v-if="'user' === preset.from">
						<select :name="'_preset[fields_map][' + field + '][prop]'" v-if="'user' === preset.from" v-model="preset.fields_map[ field ].prop">
							<option value=""><?php _e( 'Select user property...', 'jet-engine' ); ?></option>
							<option value="ID"><?php _e( 'User ID', 'jet-engine' ); ?></option>
							<option v-for="( ufLabel, ufKey ) in userFields" :value="ufKey">
								{{ ufLabel }}
							</option>
							<option value="user_meta"><?php _e( 'User Meta', 'jet-engine' ); ?></option>
						</select>
						<input :name="'_preset[fields_map][' + field + '][key]'" placeholder="<?php _e( 'Meta field key' ) ?>" v-if="'user_meta' === preset.fields_map[ field ].prop" v-model="preset.fields_map[ field ].key" type="text">
					</div>
					<div class="jet-post-field-control__inner" v-if="'option_page' === preset.from">
						<select :name="'_preset[fields_map][' + field + '][key]'" v-model="preset.fields_map[ field ].key">
							<option value=""><?php _e( 'Select option...', 'jet-engine' ); ?></option>
							<optgroup v-for="( page, index ) in optionsPages" :label="page.label" :key="'option_page_' + index">
								<option v-for="option in page.values" :key="'option_' + option.value" :value="option.value">{{ option.label }}</option>
							</optgroup>
						</select>
					</div>
					<?php do_action( 'jet-engine/forms/preset-editor/custom-controls-global' ); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="jet-form-canvas__preset-row" v-else>
		<span><?php _e( 'Value:', 'jet-engine' ); ?></span>
		<div class="jet-post-field-control">
			<input placeholder="<?php _e( 'Query variable key' ); ?>" v-model="preset.current_field_key" v-if="'query_vars' === preset.from" type="text">
			<div class="jet-post-field-control__inner" v-if="'post' === preset.from">
				<select v-if="'post' === preset.from" v-model="preset.current_field_prop">
					<option value=""><?php _e( 'Select post property...', 'jet-engine' ); ?></option>
					<option value="ID"><?php _e( 'Post ID', 'jet-engine' ); ?></option>
					<option value="post_title"><?php _e( 'Post Title', 'jet-engine' ); ?></option>
					<option value="post_name"><?php _e( 'Post Slug', 'jet-engine' ); ?></option>
					<option value="post_content"><?php _e( 'Post Content', 'jet-engine' ); ?></option>
					<option value="post_date"><?php _e( 'Post Date', 'jet-engine' ); ?></option>
					<option value="post_date_gmt"><?php _e( 'Post Date GMT', 'jet-engine' ); ?></option>
					<option value="post_excerpt"><?php _e( 'Post Excerpt', 'jet-engine' ); ?></option>
					<option value="post_thumb"><?php _e( 'Post Thumbnail', 'jet-engine' ); ?></option>
					<option value="post_meta"><?php _e( 'Post Meta', 'jet-engine' ); ?></option>
					<option value="post_terms"><?php _e( 'Post Terms', 'jet-engine' ); ?></option>
				</select>
				<input placeholder="<?php _e( 'Meta field key' ) ?>" v-if="'post_meta' === preset.current_field_prop" v-model="preset.current_field_key" type="text">
				<select v-if="'post_terms' === preset.current_field_prop" v-model="preset.current_field_key">
					<option v-for="( taxLabel, taxValue ) in taxonomies" :value="taxValue" >
						{{ taxLabel }}
					</option>
				</select>
			</div>
			<div class="jet-post-field-control__inner" v-if="'user' === preset.from">
				<select v-if="'user' === preset.from" v-model="preset.current_field_prop">
					<option value=""><?php _e( 'Select user property...', 'jet-engine' ); ?></option>
					<option value="ID"><?php _e( 'User ID', 'jet-engine' ); ?></option>
					<option v-for="( ufLabel, ufKey ) in userFields" :value="ufKey">
						{{ ufLabel }}
					</option>
					<option value="user_meta"><?php _e( 'User Meta', 'jet-engine' ); ?></option>
				</select>
				<input placeholder="<?php _e( 'Meta field key' ) ?>" v-if="'user_meta' === preset.current_field_prop" v-model="preset.current_field_key" type="text">
			</div>
			<div class="jet-post-field-control__inner" v-if="'option_page' === preset.from">
				<select v-model="preset.current_field_key">
					<option value=""><?php _e( 'Select option...', 'jet-engine' ); ?></option>
					<optgroup v-for="( page, index ) in optionsPages" :label="page.label" :key="'option_page_' + index">
						<option v-for="option in page.values" :key="'option_' + option.value" :value="option.value">{{ option.label }}</option>
					</optgroup>
				</select>
			</div>
			<?php do_action( 'jet-engine/forms/preset-editor/custom-controls-field' ); ?>
		</div>
	</div>
</div>