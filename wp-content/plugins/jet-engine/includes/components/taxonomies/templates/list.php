<div>
	<cx-vui-list-table
		:is-empty="! itemsList.length"
		empty-message="<?php _e( 'No taxonomies found', 'jet-engine' ); ?>"
	>
		<cx-vui-list-table-heading
			:slots="[ 'name', 'slug', 'actions' ]"
			class-name="cols-3"
			slot="heading"
		>
			<span slot="name"><?php _e( 'Taxonomy Name', 'jet-engine' ); ?></span>
			<span slot="slug"><?php _e( 'Taxonomy Slug', 'jet-engine' ); ?></span>
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
					v-if="'built-in' !== showTypes"
					@click.prevent="copyItem( item )"><?php _e( 'Copy', 'jet-engine' ); ?></a>
				<span v-if="'built-in' !== showTypes">&nbsp;|&nbsp;</span>
				<a
					class="jet-engine-delete-item"
					href="#"
					v-if="'built-in' !== showTypes"
					@click.prevent="deleteItem( item )"
				><?php _e( 'Delete', 'jet-engine' ); ?></a>
			</div>
		</cx-vui-list-table-item>
	</cx-vui-list-table>
	<jet-cpt-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:tax-id="parseInt( deletedItem.id, 10 )"
		:tax-slug="deletedItem.slug"
		:tax-name="deletedItem.labels.name"
	></jet-cpt-delete-dialog>
</div>
