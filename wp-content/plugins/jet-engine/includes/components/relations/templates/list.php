<div>
	<cx-vui-list-table
		:is-empty="! itemsList.length"
		empty-message="<?php _e( 'No relations found', 'jet-engine' ); ?>"
	>
		<cx-vui-list-table-heading
			:slots="[ 'name', 'post_types', 'type', 'actions' ]"
			class-name="cols-4"
			slot="heading"
		>
			<span slot="name"><?php _e( 'Name', 'jet-engine' ); ?></span>
			<span slot="post_types"><?php _e( 'Related objects', 'jet-engine' ); ?></span>
			<span slot="type"><?php _e( 'Relation type', 'jet-engine' ); ?></span>
			<span slot="actions"><?php _e( 'Actions', 'jet-engine' ); ?></span>
		</cx-vui-list-table-heading>
		<cx-vui-list-table-item
			:slots="[ 'name', 'post_types', 'type', 'actions' ]"
			class-name="cols-4"
			slot="items"
			v-for="item in itemsList"
			:key="item.id"
		>
			<span slot="name">
				<a
					:href="getEditLink( item.id )"
					class="jet-engine-title-link"
				>{{ item.name }}</a>
				<i v-if="item.is_legacy"><small><?php _e( '(Legacy)', 'jet-engine' ); ?></small></i>
			</span>
			<i slot="post_types">{{ item.related_objects }}</i>
			<i slot="type">{{ relationsTypes[ item.args.type ] }}</i>
			<div slot="actions" style="display: flex;">
				<a :href="getEditLink( item.id )"><?php _e( 'Edit', 'jet-engine' ); ?></a>&nbsp;|&nbsp;
				<a
					href="#"
					@click.prevent="copyItem( item )"
				><?php _e( 'Copy', 'jet-engine' ); ?></a>&nbsp;|&nbsp;
				<a
					class="jet-engine-delete-item"
					href="#"
					@click.prevent="deleteItem( item )"
				><?php _e( 'Delete', 'jet-engine' ); ?></a>
			</div>
		</cx-vui-list-table-item>
	</cx-vui-list-table>
	<jet-cpt-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:item-id="deletedItem.id"
		:item-name="deletedItem.name"
	></jet-cpt-delete-dialog>
</div>
