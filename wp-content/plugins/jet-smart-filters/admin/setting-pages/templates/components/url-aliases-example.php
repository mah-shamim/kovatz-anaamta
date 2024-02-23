<div class="url-aliases-example">
	<div class="url-aliases-example-string">
		<div class="url-aliases-example-string-prefix">{{urlPrefix}}</div>
		<div class="url-aliases-example-string-editor"
			 :class="{ editing: isUrlEdite }">
			<input class="url-aliases-example-string-editor-input"
				   type="text"
				   :value="url"
				   @input="onUrlInput"
				   :disabled="isUrlEdite ? false : true"
				   ref="urlInput" >
			<div class="url-aliases-example-string-editor-tools">
				<div class="editing-tools">
					<div v-if="!isUrlEdite"
						 class="editing-tools-actions">
						<div class="editing-tools-actions-edit"
							 @click="onUrlEditClick" />
						<div v-if="isNotDefaultUrl"
							 class="editing-tools-actions-restore-default"
							 @click="onUrlEditRestoreClick" />
					</div>
					<div v-else
						 class="editing-tools-confirmation">
						<div class="editing-tools-confirmation-confirm"
							 @click="onUrlEditConfirmClick" />
						<div class="editing-tools-confirmation-cancel"
							 @click="onUrlEditCancelClick" />
					</div>
				</div>
			</div>
		</div>
	</div>
	<div v-if="isUrl && isMatches"
		 class="url-aliases-example-changes">
		<div class="url-aliases-example-changes-transformation direct-transformation"
			:class="{ opened: directOpened }">
			<div class="url-aliases-example-changes-transformation-resalt">
				<div class="url-aliases-example-changes-transformation-resalt-label"
					@click="onDirectClick">
					<?php _e( 'Direct transformation', 'jet-smart-filters' ); ?>
				</div>
				{{urlPrefix + directUrl}}
			</div>
			<div v-if="directTransformations.length"
				 class="url-aliases-example-changes-transformation-chain">
				<div v-for="( alias, index ) in directTransformations"
					 class="url-aliases-example-changes-transformation-chain-iteration">
					<div class="index">{{alias.index}}:</div>
					<div class="resalt">
						<div class="replacement">
							<span class="replacement-from">{{alias.replacement.from}}</span>
							-> 
							<span class="replacement-to">{{alias.replacement.to}}</span>
						</div>
						<div class="url"
							 v-html="urlPrefix + alias.html" />
					</div>
				</div>
			</div>
			<div v-else
				 class="url-aliases-example-changes-transformation-chain">
				<div class="url-aliases-example-message">
					<p><?php _e( 'No matches found', 'jet-smart-filters' ); ?></p>
				</div>
			</div>
		</div>
		<div class="url-aliases-example-changes-transformation reverse-transformation"
			:class="{ opened: reverseOpened }">
			<div class="url-aliases-example-changes-transformation-resalt">
				<div class="url-aliases-example-changes-transformation-resalt-label"
					@click="onReverseClick">
					<?php _e( 'Reverse transformation', 'jet-smart-filters' ); ?>
				</div>
				{{urlPrefix + reverseUrl}}
			</div>
			<div v-if="reverseTransformations.length"
				 class="url-aliases-example-changes-transformation-chain">
				<div v-for="( alias, index ) in reverseTransformations"
					 class="url-aliases-example-changes-transformation-chain-iteration">
					<div class="index">{{alias.index}}:</div>
					<div class="resalt">
						<div class="replacement">
							<span class="replacement-from">{{alias.replacement.from}}</span>
							-> 
							<span class="replacement-to">{{alias.replacement.to}}</span>
						</div>
						<div class="url"
							 v-html="urlPrefix + alias.html" />
					</div>
				</div>
			</div>
			<div v-else
				 class="url-aliases-example-changes-transformation-chain">
				<div class="url-aliases-example-message">
					<p><?php _e( 'No matches found', 'jet-smart-filters' ); ?></p>
				</div>
			</div>
		</div>
		<div v-if="url !== reverseUrl"
			 class="url-aliases-example-error">
			<p><?php _e( 'After reverse transformation, the URL does not match the incoming value', 'jet-smart-filters' ); ?></p>
			<p><?php _e( 'Check uniqueness of keys', 'jet-smart-filters' ); ?></p>
		</div>
	</div>
	<div v-else
		 class="url-aliases-example-message">
		<p v-if="!url">
			<?php _e( 'Enter URL with JetSmartFilters parameters to check aliases', 'jet-smart-filters' ); ?>
		</p>
		<p v-else-if="!isUrl">
			<?php _e( 'URL does not contain JetSmartFilters parameters', 'jet-smart-filters' ); ?>
		</p>
		<p v-else>
			<?php _e( 'No matches found', 'jet-smart-filters' ); ?>
		</p>
	</div>
</div>