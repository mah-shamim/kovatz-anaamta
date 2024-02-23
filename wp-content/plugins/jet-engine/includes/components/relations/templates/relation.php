<div>
	<cx-vui-collapse
		:collapsed="false"
	>
		<h3 class="cx-vui-subtitle" slot="title"><?php _e( 'General Settings', 'jet-engine' ); ?></h3>
		<div class="cx-vui-panel" slot="content">
			<cx-vui-input
				label="<?php _e( 'Name', 'jet-engine' ); ?>"
				description="<?php _e( 'Set unique name for your current relation', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.name"
				@input="( newValue ) => { setLabel( newValue, 'name' ) }"
			></cx-vui-input>
			<cx-vui-select
				label="<?php _e( 'Parent object', 'jet-engine' ); ?>"
				description="<?php _e( 'Select main object (post type, taxonomy, user or CCT) for current relation', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:groups-list="objectTypes"
				size="fullwidth"
				:multiple="false"
				:value="args.parent_object"
				@input="( newValue ) => { setArg( newValue, 'parent_object' ) }"
			></cx-vui-select>
			<cx-vui-select
				label="<?php _e( 'Child object', 'jet-engine' ); ?>"
				description="<?php _e( 'Select child object for current relation', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:groups-list="objectTypes"
				size="fullwidth"
				:multiple="false"
				:value="args.child_object"
				@input="( newValue ) => { setArg( newValue, 'child_object' ) }"
			></cx-vui-select>
			<cx-vui-select
				label="<?php _e( 'Relation type', 'jet-engine' ); ?>"
				description="<?php _e( 'Select type of current relation. Read more about available relation types <a href=\'https://crocoblock.com/knowledge-base/articles/how-to-choose-the-needed-post-relations-and-set-them-with-jetengine-plugin/\'>here</a>', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="relationsTypes"
				size="fullwidth"
				:multiple="false"
				:value="args.type"
				@input="( newValue ) => { setArg( newValue, 'type' ) }"
			></cx-vui-select>
			<cx-vui-select
				label="<?php _e( 'Parent relation', 'jet-engine' ); ?>"
				description="<?php _e( 'Select parent relation for current one. The relation could be selected as parent only if its \'Child object\' is equal to \'Parent object\' of current relation', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:options-list="parentRelations"
				size="fullwidth"
				:multiple="false"
				:value="args.parent_rel"
				@input="( newValue ) => { setArg( newValue, 'parent_rel' ) }"
			></cx-vui-select>
			<cx-vui-switcher
				name="parent_control"
				label="<?php _e( 'Register controls for parent object', 'jet-engine' ); ?>"
				description="<?php _e( 'Adds controls to manage related children items to the edit page of the parent object', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.parent_control"
				@input="( newValue ) => { setArg( newValue, 'parent_control' ) }"
			></cx-vui-switcher>
			<cx-vui-switcher
				name="parent_manager"
				label="<?php _e( 'Allow to create new children from parent', 'jet-engine' ); ?>"
				description="<?php _e( 'If enabled, allows to create new children items from the related items control for parent object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.parent_manager"
				:conditions="[ {
					input: args.parent_control,
					compare: 'equal',
					value: true,
				} ]"
				@input="( newValue ) => { setArg( newValue, 'parent_manager' ) }"
			></cx-vui-switcher>
			<cx-vui-switcher
				name="parent_allow_delete"
				label="<?php _e( 'Allow to delete children from parent', 'jet-engine' ); ?>"
				description="<?php _e( 'If enabled, allows to delete children items from the related items control for parent object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.parent_allow_delete"
				:conditions="[ {
					input: args.parent_control,
					compare: 'equal',
					value: true,
				} ]"
				@input="( newValue ) => { setArg( newValue, 'parent_allow_delete' ) }"
			></cx-vui-switcher>
			<cx-vui-switcher
				name="child_control"
				label="<?php _e( 'Register controls for child object', 'jet-engine' ); ?>"
				description="<?php _e( 'Adds controls to manage related parent items to the edit page of the child object', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.child_control"
				@input="( newValue ) => { setArg( newValue, 'child_control' ) }"
			></cx-vui-switcher>
			<cx-vui-switcher
				name="child_manager"
				label="<?php _e( 'Allow to create new parents from children', 'jet-engine' ); ?>"
				description="<?php _e( 'If enabled, allows to create new parent items from the related items control for child object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.child_manager"
				:conditions="[ {
					input: args.child_control,
					compare: 'equal',
					value: true,
				} ]"
				@input="( newValue ) => { setArg( newValue, 'child_manager' ) }"
			></cx-vui-switcher>
			<cx-vui-switcher
				name="child_allow_delete"
				label="<?php _e( 'Allow to delete parents from children', 'jet-engine' ); ?>"
				description="<?php _e( 'If enabled, allows to delete parent items from the related items control for child object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.child_allow_delete"
				:conditions="[ {
					input: args.child_control,
					compare: 'equal',
					value: true,
				} ]"
				@input="( newValue ) => { setArg( newValue, 'child_allow_delete' ) }"
			></cx-vui-switcher>
			<cx-vui-switcher
				name="db_table"
				label="<?php _e( 'Register separate DB table', 'jet-engine' ); ?>"
				description="<?php _e( 'Register separate DB tables to store current relation items and meta data. If you plan to create multiple relations with a big amount of items, this option will help optimize performance', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.db_table"
				@input="( newValue ) => { setArg( newValue, 'db_table' ) }"
			></cx-vui-switcher>
			<cx-vui-switcher
				label="<?php _e( 'Register get items/item REST API Endpoint', 'jet-engine' ); ?>"
				description="<?php _e( 'Register Rest API endpoint to get content type items.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.rest_get_enabled"
				@input="( newValue ) => { setArg( newValue, 'rest_get_enabled' ) }"
			></cx-vui-switcher>
			<cx-vui-component-wrapper
				label="<?php _e( 'Endpoint URL', 'jet-engine' ); ?>"
				description="<?php _e( 'Get endpoint URL', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:conditions="[
					{
						'input':   args.rest_get_enabled,
						'compare': 'equal',
						'value':   true,
					}
				]"
			>
				<div>
					<code>GET {{ restBase }}{{ relID }}</code> - <?php _e( 'All relation data list', 'jet-engine' ) ?><br>
					<code>GET {{ restBase }}{{ relID }}/children/{_ID}</code> - <?php _e( 'Children items for given parent {_ID}', 'jet-engine' ) ?>
					<code>GET {{ restBase }}{{ relID }}/parents/{_ID}</code> - <?php _e( 'Parent items for given child {_ID}', 'jet-engine' ) ?>
				</div>
			</cx-vui-component-wrapper>
			<cx-vui-input
				:label="'<?php _e( 'Access Capability', 'jet-engine' ); ?>'"
				description="<?php _e( 'Leave empty to make public or set user access capability. More about capabilities <a href=\'https://wordpress.org/support/article/roles-and-capabilities/\' target=\'_blank\'>here</a>.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:size="'fullwidth'"
				:value="args.rest_get_access"
				@input="( newValue ) => { setArg( newValue, 'rest_get_access' ) }"
				:conditions="[
					{
						'input':   args.rest_get_enabled,
						'compare': 'equal',
						'value':   true,
					}
				]"
			></cx-vui-input>
			<cx-vui-switcher
				label="<?php _e( 'Register update REST API Endpoint', 'jet-engine' ); ?>"
				description="<?php _e( 'Register Rest API endpoint to update related items.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:value="args.rest_post_enabled"
				@input="( newValue ) => { setArg( newValue, 'rest_post_enabled' ) }"
			></cx-vui-switcher>
			<cx-vui-component-wrapper
				label="<?php _e( 'Endpoint URL', 'jet-engine' ); ?>"
				description="<?php _e( 'Update endpoint URL', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:conditions="[
					{
						'input':   args.rest_post_enabled,
						'compare': 'equal',
						'value':   true,
					}
				]"
			>
				<div>
					<code>POST {{ restBase }}{{ relID }}</code> - <?php _e( 'to insert/update new data you need to send a POST request to given URL with next body:', 'jet-engine' ); ?>
					<pre>
{
	parent_id: object ID,
	child_id: object ID,
	context: child/parent,
	store_items_type: replace/update,
	meta: object with meta key/meta value pairs
}
					</pre>
					<?php _e( 'Where', 'jet-engine' ); ?>:<br>
					- <code>context</code> - 'child' <?php _e( 'means we updating children items for static parent object', 'jet-engine' ); ?>, 'parent' <?php _e( 'means we updating parent items for static child object', 'jet-engine' ); ?><br>
					- <code>store_items_type</code> - 'repalce' <?php _e( 'means if related object for given context already exists, it will be replaced with new one', 'jet-engine' ); ?>, 'update' <?php _e( 'means if related object for given context already exists, new related items will be added to already existsing (if allowed by relation settings)', 'jet-engine' ); ?>.
				</div>
			</cx-vui-component-wrapper>
			<cx-vui-input
				:label="'<?php _e( 'Access Capability', 'jet-engine' ); ?>'"
				description="<?php _e( 'Leave empty to make public or set user access capability. More about capabilities <a href=\'https://wordpress.org/support/article/roles-and-capabilities/\' target=\'_blank\'>here</a>.', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				:size="'fullwidth'"
				:value="args.rest_post_access"
				@input="( newValue ) => { setArg( newValue, 'rest_post_access' ) }"
				:conditions="[
					{
						'input':   args.rest_post_enabled,
						'compare': 'equal',
						'value':   true,
					}
				]"
			></cx-vui-input>
			<cx-vui-component-wrapper
				label="<?php _e( 'Note!', 'jet-engine' ); ?>"
				description="<?php _e( 'If you leave this endpoint as public anyone will can to insert new content type items into the your database', 'jet-engine' ); ?>"
				:wrapper-css="[ 'fullwidth' ]"
				v-if="args.rest_post_enabled && ! args.rest_post_access"
			></cx-vui-component-wrapper>
		</div>
	</cx-vui-collapse>
	<?php do_action( 'jet-engine/relations/edit/custom-controls' ); ?>
	<cx-vui-collapse :collapsed="true">
		<h3 class="cx-vui-subtitle" slot="title"><?php _e( 'Labels', 'jet-engine' ); ?></h3>
		<div class="cx-vui-panel" slot="content">
			<cx-vui-input
				label="<?php _e( 'Parent Object: label of relation box', 'jet-engine' ); ?>"
				description="<?php _e( 'Label of box for manage related items on parent object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.parent_page_control_title"
				@input="( newValue ) => { setLabel( newValue, 'parent_page_control_title' ) }"
				:conditions="[ {
					input: args.parent_control,
					compare: 'equal',
					value: true,
				} ]"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Parent Object: label of connect button', 'jet-engine' ); ?>"
				description="<?php _e( 'Label of button to connect existing related items on parent object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.parent_page_control_connect"
				@input="( newValue ) => { setLabel( newValue, 'parent_page_control_connect' ) }"
				:conditions="[ {
					input: args.parent_control,
					compare: 'equal',
					value: true,
				} ]"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Parent Object: label of select item control', 'jet-engine' ); ?>"
				description="<?php _e( 'Label of control to select related items in the connect items modal on parent object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.parent_page_control_select"
				@input="( newValue ) => { setLabel( newValue, 'parent_page_control_select' ) }"
				:conditions="[ {
					input: args.parent_control,
					compare: 'equal',
					value: true,
				} ]"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Parent Object: label of create button', 'jet-engine' ); ?>"
				description="<?php _e( 'Label of button to create new related item on parent object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.parent_page_control_create"
				@input="( newValue ) => { setLabel( newValue, 'parent_page_control_create' ) }"
				:conditions="[ {
					input: args.parent_control,
					compare: 'equal',
					value: true,
				}, {
					input: args.parent_manager,
					compare: 'equal',
					value: true,
				} ]"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Child Object: label of relation box', 'jet-engine' ); ?>"
				description="<?php _e( 'Label of box for manage related items on child object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.child_page_control_title"
				@input="( newValue ) => { setLabel( newValue, 'child_page_control_title' ) }"
				:conditions="[ {
					input: args.child_control,
					compare: 'equal',
					value: true,
				} ]"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Child Object: label of connect button', 'jet-engine' ); ?>"
				description="<?php _e( 'Label of button to connect existing related items on child object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.child_page_control_connect"
				@input="( newValue ) => { setLabel( newValue, 'child_page_control_connect' ) }"
				:conditions="[ {
					input: args.child_control,
					compare: 'equal',
					value: true,
				} ]"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Child Object: label of select item control', 'jet-engine' ); ?>"
				description="<?php _e( 'Label of control to select related items in the connect items modal on child object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.child_page_control_select"
				@input="( newValue ) => { setLabel( newValue, 'child_page_control_select' ) }"
				:conditions="[ {
					input: args.child_control,
					compare: 'equal',
					value: true,
				} ]"
			></cx-vui-input>
			<cx-vui-input
				label="<?php _e( 'Child Object: label of create button', 'jet-engine' ); ?>"
				description="<?php _e( 'Label of button to create new related item on child object page', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				:value="args.labels.child_page_control_create"
				@input="( newValue ) => { setLabel( newValue, 'child_page_control_create' ) }"
				:conditions="[ {
					input: args.child_control,
					compare: 'equal',
					value: true,
				}, {
					input: args.child_manager,
					compare: 'equal',
					value: true,
				} ]"
			></cx-vui-input>
		</div>
	</cx-vui-collapse>
	<jet-meta-fields v-model="args.meta_fields" :hide-options="[ 'allow_custom', 'save_custom', 'show_in_rest' ]" slug-delimiter="_"></jet-meta-fields>
</div>
