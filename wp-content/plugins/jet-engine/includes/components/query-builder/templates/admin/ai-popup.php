<div
	class="jet-ai-query"
	@keydown.esc="onClickOutside"
	tabindex="-1"
	v-if="isAllowed"
>
	<div class="jet-ai-query__trigger" @click="switchIsActive()">
		<svg width="16" height="16" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M7.5 3.6L10 5L8.6 2.5L10 0L7.5 1.4L5 0L6.4 2.5L5 5L7.5 3.6ZM19.5 13.4L17 12L18.4 14.5L17 17L19.5 15.6L22 17L20.6 14.5L22 12L19.5 13.4ZM22 0L19.5 1.4L17 0L18.4 2.5L17 5L19.5 3.6L22 5L20.6 2.5L22 0ZM14.37 5.29C13.98 4.9 13.35 4.9 12.96 5.29L1.29 16.96C0.899998 17.35 0.899998 17.98 1.29 18.37L3.63 20.71C4.02 21.1 4.65 21.1 5.04 20.71L16.7 9.05C17.09 8.66 17.09 8.03 16.7 7.64L14.37 5.29ZM13.34 10.78L11.22 8.66L13.66 6.22L15.78 8.34L13.34 10.78Z" fill="#0F172A"/>
		</svg>
		<div class="jet-ai-query__trigger-tip"><?php
			_e( 'Generate with AI', 'jet-engine' );
		?></div>
	</div>
	<div class="jet-ai-query__popup" v-if="isActive">
		<div class="jet-ai-query__content">
			<div class="jet-ai-query__header">
				<h3 class="cx-vui-subtitle">Generate query with AI</h3>
				<div class="jet-ai-query__header-notice">
					<b>
						Beta. Limited {{ limit }} requests per month.
					</b>
				</div>
				<span class="jet-ai-query__close" @click="switchIsActive()">
					<svg width="20" height="20" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.00671L8.00671 7L12 10.9933L10.9933 12L7 8.00671L3.00671 12L2 10.9933L5.99329 7L2 3.00671L3.00671 2L7 5.99329L10.9933 2L12 3.00671Z"></path></svg>
				</span>
			</div>
			<div v-if="'request' === this.mode">
				<cx-vui-textarea
					label="<?php _e( 'Describe your query:', 'jet-engine' ); ?>"
					size="fullwidth"
					rows="3"
					name="query_prompt"
					placeholder="<?php _e( 'Describe the data you want to retrieve', 'jet-engine' ); ?>"
					v-model="prompt"
					:disabled="loading"
				></cx-vui-textarea>
				<div class="jet-ai-query__submit" v-if="prompt">
					<cx-vui-button
						@click="generateQuery()"
						button-style="accent-border"
						size="mini"
						:disabled="loading"
					><span slot="label">Generate query</span></cx-vui-button>
					<div class="jet-ai-query__load" v-if="loading">
						<?php _e( 'Please wait a bit...', 'jet-engine' ); ?>
					</div>
				</div>
				<div class="jet-ai-query__error" v-if="error">
					<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm1.13 9.38l.35-6.46H8.52l.35 6.46h2.26zm-.09 3.36c.24-.23.37-.55.37-.96 0-.42-.12-.74-.36-.97s-.59-.35-1.06-.35-.82.12-1.07.35-.37.55-.37.97c0 .41.13.73.38.96.26.23.61.34 1.06.34s.8-.11 1.05-.34z"/></g></svg>
					<span>{{ error }}</span>
				</div>
			</div>
			<div v-if="'completion' === this.mode">
				<cx-vui-textarea
					label="<?php _e( 'Generated query:', 'jet-engine' ); ?>"
					size="fullwidth"
					:rows="rows"
					name="query_completion"
					v-model="completion"
				></cx-vui-textarea>
				<div class="jet-ai-query__generated-notice"><?php _e( '* Please note: The SQL query generated below was created by AI system to best match your description. Please review the query carefully as it might not be 100% accurate.', 'jet-engine' ); ?></div>
				<div class="jet-ai-query__submit">
					<cx-vui-button
						@click="useQuery()"
						button-style="accent-border"
						size="mini"
						:disabled="loading"
					><span slot="label">Use this query</span></cx-vui-button>
					<cx-vui-button
						@click="switchToRequest()"
						button-style="link-accent"
						size="mini"
					><span slot="label">Generate new query</span></cx-vui-button>
					<div class="jet-ai-query__requests">
						<b><?php _e( 'Requests used:', 'jet-engine' ); ?> {{ this.usage }}</b>
					</div>
				</div>
			</div>
			<div class="jet-ai-query__info">
				<div class="jet-ai-query__info-header"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M9 15h2V9H9v6zm1-10c-.5 0-1 .5-1 1s.5 1 1 1 1-.5 1-1-.5-1-1-1zm0-4c-5 0-9 4-9 9s4 9 9 9 9-4 9-9-4-9-9-9zm0 16c-3.9 0-7-3.1-7-7s3.1-7 7-7 7 3.1 7 7-3.1 7-7 7z"/></g></svg><?php _e( 'Tips how to write a good prompt:' ) ?></div>
				<ul class="jet-ai-query__info-list">
					<li><?php _e( 'Make sure your prompt clearly describes what information you\'re trying to retrieve from the database. The clearer your prompt is, the better the AI will be able to generate a useful SQL query', 'jet-engine' ); ?></li>
					<li><?php _e( 'Use the same naming like WordPress DB tables itself - users, posts, comments etc. This might help the AI understand the context better', 'jet-engine' ); ?></li>
					<li><?php _e( 'If possible, structure your prompt in a way that mirrors the structure of a SQL query. For example, start with what you want to select, then from which tables, followed by any conditions or ordering you want to apply', 'jet-engine' ); ?></li>
					<li><?php _e( 'Use keywords that are specific to SQL syntax such as \'SELECT\', \'FROM\', \'WHERE\', \'ORDER BY\', \'LIMIT\' etc., in your prompt if possible', 'jet-engine' ); ?></li>
				</ul>
			</div>
			<div class="jet-ai-query__info">
				<div class="jet-ai-query__info-header"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm-.615 12.66h-1.34l-3.24-4.54 1.34-1.25 2.57 2.4 5.14-5.93 1.34.94-5.81 8.38z"/></g></svg><?php _e( 'Prompt examples to use as starting point:' ) ?></div>
				<ul class="jet-ai-query__info-list">
					<li v-for="snippet in snippets"><span class="jet-ai-query__info-prompt" @click="prompt = snippet">{{ snippet }}</span></li>
				</ul>
			</div>
			<div class="jet-ai-query__license-warning" v-if="!hasLicense">
				<h3 class="cx-vui-subtitle"><?php _e( 'Please note:', 'jet-engine' ); ?></h3>
				<div class="jet-ai-query__license-warning-text">
					<p><?php printf( 
						__( 'You need to %s your license to use AI functionality.', 'jet-engine' ),
						'<a href="' . admin_url( 'admin.php?page=jet-dashboard-license-page&subpage=license-manager' ) . '" traget="_blank">' . __( 'activate', 'jet-engine' ) . '</a>'
					); ?></p>
					<p><?php _e( 'At the moment AI functionality works in beta mode and has restricted requests number.', 'jet-engine' ); ?></p>
					<p><?php _e( '<b>All-inclusive Lifetime</b> and <b>Freelance Lifetime</b> Crocoblock subscription plans allows <b>30</b> AI requests per month.', 'jet-engine' ); ?></p>
					<p><?php _e( '<b>JetEngine</b> and <b>Yearly</b> subscriptions allows <b>5</b> AI requests per month.', 'jet-engine' ); ?></p>
					<cx-vui-button
						@click="switchIsActive()"
						button-style="accent-border"
						size="mini"
					><span slot="label">Close</span></cx-vui-button>
				</div>
			</div>
		</div>
	</div>
</div>
