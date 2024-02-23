<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'REST API Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-select
			label="<?php _e( 'From Endpoint', 'jet-engine' ); ?>"
			description="<?php _e( 'Select REST API endpoint to get data from', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="endpoints"
			size="fullwidth"
			v-model="query.endpoint"
		></cx-vui-select>

		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth-control' ]"
		>
			<div class="cx-vui-inner-panel query-panel">
				<div class="cx-vui-component__label"><?php _e( 'Query', 'jet-engine' ); ?></div>
				<cx-vui-repeater
					button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
					button-style="accent"
					button-size="mini"
					v-model="query.args"
					@add-new-item="addNewField( $event, [], query.args, newDynamicArgs )"
				>
					<cx-vui-repeater-item
						v-for="( clause, index ) in query.args"
						:collapsed="isCollapsed( clause )"
						:index="index"
						@clone-item="cloneField( $event, clause._id, query.args, newDynamicArgs )"
						@delete-item="deleteField( $event, clause._id, query.args, deleteDynamicArgs )"
						:key="clause._id"
					>
						<cx-vui-input
							label="<?php _e( 'Key', 'jet-engine' ); ?>"
							description="<?php _e( 'Set query key to get data by', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							size="fullwidth"
							:value="query.args[ index ].field"
							@input="setFieldProp( clause._id, 'field', $event, query.args )"
						></cx-vui-input>
						<cx-vui-textarea
							label="<?php _e( 'Value', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth', 'has-macros' ]"
							size="fullwidth"
							:value="query.args[ index ].value"
							@input="setFieldProp( clause._id, 'value', $event, query.args )"
						><jet-query-dynamic-args v-model="dynamicQuery.args[ clause._id ].value"></jet-query-dynamic-args></cx-vui-textarea>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
	</div>
</div>
