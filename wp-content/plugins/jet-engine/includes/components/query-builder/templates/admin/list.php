<div>
	<cx-vui-list-table
		:is-empty="! itemsList.length"
		empty-message="<?php _e( 'No queries found', 'jet-engine' ); ?>"
	>
		<cx-vui-list-table-heading
			:slots="[ 'name', 'type', 'actions' ]"
			class-name="cols-3"
			slot="heading"
		>
			<span slot="name"><?php _e( 'Name', 'jet-engine' ); ?></span>
			<span slot="type"><?php _e( 'Query Type', 'jet-engine' ); ?></span>
			<span slot="actions"><?php _e( 'Actions', 'jet-engine' ); ?></span>
		</cx-vui-list-table-heading>
		<cx-vui-list-table-item
			:slots="[ 'name', 'type', 'actions' ]"
			class-name="cols-3"
			slot="items"
			v-for="item in itemsList"
			:key="item.id"
		>
			<span slot="name">
				<a
					:href="getEditLink( item.id )"
					class="jet-engine-title-link"
				>{{ item.labels.name }}</a>
				<i
					v-if="item.args.description"
					class="jet-engine-description"
				>
					{{ item.args.description }}
				</i>
			</span>
			<i slot="type">{{ getQueryType( item.args.query_type ) }}</i>
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
	<jet-query-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:item-id="deletedItem.id"
		:item-name="deletedItem.labels.name"
	></jet-query-delete-dialog>
</div>
