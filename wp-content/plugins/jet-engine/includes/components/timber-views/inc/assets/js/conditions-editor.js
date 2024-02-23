(function( $ ) {

	'use strict';

	Vue.component( 'jet-engine-timber-editor-conditions', {
		template: `<div 
			class="jet-engine-timber-editor-conditions jet-engine-timber-dynamic-data"
			v-click-outside.capture="closePopup"
			v-click-outside:mousedown.capture="closePopup"
			v-click-outside:touchstart.capture="closePopup"
			@keydown.esc="closePopup"
		>
			<cx-vui-button
				button-style="accent-border"
				@click="switchPopup"
				size="mini"
			>
				<svg 
					slot="label"
					viewBox="0 0 64 64" 
					xmlns="http://www.w3.org/2000/svg" 
					width="16"
					height="16"
					fill-rule="evenodd" 
					clip-rule="evenodd" 
					stroke-linejoin="round" 
					stroke-miterlimit="1.414"
				><path d="M11.375 20.844c-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c3.75 0 7.5 1.5 10.125 4.125.75.75 1.875.75 2.625 0s.75-1.875 0-2.625c-3.375-3.375-8.063-5.25-12.75-5.25z" fill="#0071a1" fill-rule="nonzero"></path> <path d="M53.938 21.219l-5.25-5.25c-.376-.375-.938-.563-1.313-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625l2.062 2.062h-4.313c-4.875 0-9.375 1.875-12.75 5.25l-9.375 9.375c-2.625 2.625-6.375 4.125-10.125 4.125-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c4.688 0 9.375-1.875 12.75-5.25l9.375-9.375c2.813-2.625 6.375-4.125 10.125-4.125h4.313l-2.062 2.063c-.75.75-.75 1.875 0 2.625s1.875.75 2.625 0l5.25-5.25c.75-.563.75-1.875 0-2.625z" fill="#0071a1" fill-rule="nonzero"></path> <path d="M53.938 40.156l-5.25-5.25c-.376-.375-.938-.562-1.313-.562-.563 0-.938.187-1.312.562-.75.75-.75 1.875 0 2.625l2.062 2.063h-4.313c-3.75 0-7.5-1.5-10.125-4.125-.374-.375-.937-.563-1.312-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625 3.374 3.375 7.874 5.25 12.75 5.25h4.312l-2.063 2.062c-.75.75-.75 1.875 0 2.625s1.876.75 2.625 0l5.25-5.25c.75-.562.75-1.875 0-2.625z" fill="#0071a1" fill-rule="nonzero"></path></svg>
				<span slot="label">Conditional Tags</span>
			</cx-vui-button>
			<div
				class="jet-engine-timber-dynamic-data__popup jet-engine-timber-editor-popup"
				v-if="showPopup"
				tabindex="-1"
			>
				<template v-if="isEnabled">
					<div class="jet-engine-timber-dynamic-data__notice">
						<span>*</span>
						<span v-html="macrosNotice"></span>
					</div>
					<div
						class="jet-engine-timber-dynamic-data__single-item-control"
						v-for="control in getPreparedControls( controls )"
					>
						<component
							:is="control.type"
							:options-list="control.optionsList"
							:groups-list="control.groupsList"
							:label="control.label"
							:wrapper-css="[ 'mini-label' ]"
							:multiple="control.multiple"
							size="fullwidth"
							v-if="checkCondition( control.condition, result )"
							v-model="result[ control.name ]"
						><small v-if="control.description" v-html="control.description"></small></component>
					</div>
				</template>
				<template v-else>
					<div class="jet-engine-timber-dynamic-data__notice">
						<span>*</span>
						<span v-html="disabledNotice"></span>
					</div>
				</template>
				<div class="jet-engine-timber-dynamic-data__single-actions">
					<cx-vui-button
						button-style="accent"
						size="mini"
						@click="insertConditionalTag()"
					><span slot="label">{{ insertButtonLabel() }}</span></cx-vui-button>
				</div>
				<br>
				<div class="jet-engine-timber-dynamic-data__notice">
					<span>*</span>
					<span v-html="twigNotice"></span>
				</div>
			</div>
		</div>`,
		directives: { clickOutside: window.JetVueUIClickOutside },
		mixins: [ window.popupHelper, window.controlsHelper ],
		data() {
			return {
				controls: window.JetEngineDynamicVisibilityData.controls,
				functionName: window.JetEngineDynamicVisibilityData.function_name,
				macrosNotice: window.JetEngineDynamicVisibilityData.macros_notice,
				twigNotice: window.JetEngineDynamicVisibilityData.twig_notice,
				isEnabled: window.JetEngineDynamicVisibilityData.is_enabled,
				disabledNotice: window.JetEngineDynamicVisibilityData.disabled_notice,
				result: {},
			};
		},
		methods: {
			onPopupClose() {
				this.result = {};
			},
			insertConditionalTag() {

				this.$emit( 'insert', this.getConditionalTagToInsert() );
				this.closePopup();

			},
			insertButtonLabel() {
				if ( this.isEmpty() ) {
					return 'Insert empty tag';
				} else {
					return 'Insert';
				}
			},
			isEmpty() {
				return ! this.result.jedv_condition ? true : false;
			},
			getConditionalTagToInsert() {

				let args = {};

				for ( const arg in this.result ) {

					let allowed = this.controls[ arg ].condition ? false : true;

					if ( ! allowed && this.checkCondition( this.controls[ arg ].condition, this.result ) ) {
						allowed = true;
					}

					if ( allowed ) {
						args[ arg ] = this.result[ arg ];
					}

				}

				let result = '{% if ';

				if ( this.isEmpty() ) {
					result += ' ';
				} else {
					result += this.functionName + '(args=';
					result += JSON.stringify( args );
					result += ')';
				}
				
				result += ' %}';
				result += "\n<!-- Paste your HTML here -->\n";
				result += '{% endif %}';

				return result;
			}
		}

	} );

})( jQuery );
