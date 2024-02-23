<div>
	<cx-vui-list-table
		:is-empty="! itemsList.length"
		empty-message="<?php _e( 'No post types found', 'jet-engine' ); ?>"
	>
		<cx-vui-list-table-heading
			:slots="[ 'name', 'slug', 'actions' ]"
			class-name="cols-3"
			slot="heading"
		>
			<div slot="name"><?php _e( 'Post Type Name', 'jet-engine' ); ?></div>
			<div slot="slug"><?php _e( 'Post Type Slug', 'jet-engine' ); ?></div>
			<div slot="actions" class="jet-engine-type-switcher-wrap">
				<span><?php _e( 'Actions', 'jet-engine' ); ?></span>
				<div class="jet-engine-type-switcher">
					<div
						:class="{
							'jet-engine-type-switcher__item': true,
							'is-active': 'jet-engine' === showTypes,
						}"
						@click="showTypes = 'jet-engine'"
					>
						<?php _e( 'JetEngine', 'jet-engine' ); ?>
					</div>
					<div
						:class="[
							'cx-vui-switcher',
							'cx-vui-switcher--off',
							'cx-vui-switcher--at-' + showTypes
						]"
						@click="switchType"
					>
						<div class="cx-vui-switcher__panel"></div>
						<div class="cx-vui-switcher__trigger"></div>
					</div>
					<div
						:class="{
							'jet-engine-type-switcher__item': true,
							'is-active': 'built-in' === showTypes,
						}"
						@click="showTypes = 'built-in'"
					>
						<?php _e( 'Built-in', 'jet-engine' ); ?>
					</div>
				</div>
			</div>
		</cx-vui-list-table-heading>
		<cx-vui-list-table-item
			:slots="[ 'name', 'slug', 'actions' ]"
			class-name="cols-3"
			slot="items"
			v-for="item in itemsList"
			:key="item.slug + item.id"
		>
			<span slot="name">
				<a
					:href="getEditLink( item.id, item.slug )"
					class="jet-engine-title-link"
				>{{ item.labels.name }}</a>
			</span>
			<i slot="slug">{{ item.slug }}</i>
			<i slot="slug" v-if="item.rewrite_slug" title="<?php _e( 'Rewrite slug', 'jet-engine' ); ?>"> ( {{ item.rewrite_slug }} )</i>
			<div slot="actions" style="display: flex;">
				<a :href="getEditLink( item.id, item.slug )"><?php _e( 'Edit', 'jet-engine' ); ?></a>
				<span v-if="'built-in' !== showTypes">&nbsp;|&nbsp;</span>
				<a
					href="#"
					@click.prevent="copyItem( item )"
					v-if="'built-in' !== showTypes"
				><?php _e( 'Copy', 'jet-engine' ); ?></a>
				<span v-if="'built-in' !== showTypes">&nbsp;|&nbsp;</span>
				<a
					class="jet-engine-delete-item"
					href="#"
					@click.prevent="deleteItem( item )"
					v-if="'built-in' !== showTypes"
				><?php _e( 'Delete', 'jet-engine' ); ?></a>
			</div>
		</cx-vui-list-table-item>
	</cx-vui-list-table>
	<jet-cpt-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:post-type-id="parseInt( deletedItem.id, 10 )"
		:post-type-slug="deletedItem.slug"
		:post-type-name="deletedItem.labels.name"
	></jet-cpt-delete-dialog>
</div>
