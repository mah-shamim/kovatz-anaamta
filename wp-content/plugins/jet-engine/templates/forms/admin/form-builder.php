<div id="form_builder">
    <grid-layout class="jet-form-canvas" v-if="!showEditor && !showLogicEditor"
                 :layout="layout"
                 :col-num="12"
                 :row-height="48"
                 :margin="[5, 5]"
                 :is-draggable="true"
                 :is-resizable="true"
                 :vertical-compact="true"
                 :use-css-transforms="true"
                 :style="{ margin: '0 -5px' }"
                 @layout-updated="updateLayout"
    >
        <grid-item class="jet-form-canvas__field"
                   v-for="( item, index ) in layout"
                   :key="item.i"
                   :x="item.x"
                   :y="item.y"
                   :w="item.w"
                   :h="item.h"
                   :i="item.i"
                   :max-h="1"
        >
            <div class="jet-form-canvas__field-content">
                <div class="jet-form-canvas__field-start">
                    <div class="jet-form-canvas__field-remove" @click="removeField( item, index )"></div>
                    <div class="jet-form-canvas__field-label">
						<span class="jet-form-canvas__field-name">
							<span v-html="itemInstance( item )"></span>:&nbsp;
							<span v-if="'submit' === item.settings.type">{{ item.settings.label }}</span>
							<span v-else-if="'repeater_end' === item.settings.type">repeater_end</span>
							<span v-else>{{ item.settings.name }}</span>
						</span>
                        <span class="jet-form-canvas__field-type">Type: {{ item.settings.type }}</span>
                    </div>
                </div>
                <div class="jet-form-canvas__field-end">
                    <span>{{ currentWidth( item.w ) }}</span>
                    <div class="jet-form-canvas__field-edit" @click="editField( item, index )">
                        <span class="dashicons dashicons-edit"></span>
                    </div>
                    <div class="jet-form-canvas__field-copy" @click="copyField( item, index )">
                        <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.401 14.5362V4.35745H5.47721V14.5362H13.401ZM13.401 2.92766C13.7848 2.92766 14.1234 3.07518 14.4169 3.37021C14.7104 3.64255 14.8571 3.97163 14.8571 4.35745V14.5362C14.8571 14.922 14.7104 15.2624 14.4169 15.5574C14.1234 15.8525 13.7848 16 13.401 16H5.47721C5.09344 16 4.75482 15.8525 4.46134 15.5574C4.16787 15.2624 4.02113 14.922 4.02113 14.5362V4.35745C4.02113 3.97163 4.16787 3.64255 4.46134 3.37021C4.75482 3.07518 5.09344 2.92766 5.47721 2.92766H13.401ZM11.2338 0V1.46383H2.56504V11.6426H1.14282V1.46383C1.14282 1.07801 1.27827 0.737589 1.54917 0.442553C1.84265 0.147518 2.18127 0 2.56504 0H11.2338Z"/>
                        </svg>
                    </div>
                    <div class="jet-form-canvas__field-conditional-logic" @click="editFieldLogic( item, index )">
                        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                             clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414">
                            <path d="M11.375 20.844c-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c3.75 0 7.5 1.5 10.125 4.125.75.75 1.875.75 2.625 0s.75-1.875 0-2.625c-3.375-3.375-8.063-5.25-12.75-5.25z"
                                  fill="#0071a1" fill-rule="nonzero"/>
                            <path d="M53.938 21.219l-5.25-5.25c-.376-.375-.938-.563-1.313-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625l2.062 2.062h-4.313c-4.875 0-9.375 1.875-12.75 5.25l-9.375 9.375c-2.625 2.625-6.375 4.125-10.125 4.125-1.125 0-1.875.75-1.875 1.875s.75 1.875 1.875 1.875c4.688 0 9.375-1.875 12.75-5.25l9.375-9.375c2.813-2.625 6.375-4.125 10.125-4.125h4.313l-2.062 2.063c-.75.75-.75 1.875 0 2.625s1.875.75 2.625 0l5.25-5.25c.75-.563.75-1.875 0-2.625z"
                                  fill="#0071a1" fill-rule="nonzero"/>
                            <path d="M53.938 40.156l-5.25-5.25c-.376-.375-.938-.562-1.313-.562-.563 0-.938.187-1.312.562-.75.75-.75 1.875 0 2.625l2.062 2.063h-4.313c-3.75 0-7.5-1.5-10.125-4.125-.374-.375-.937-.563-1.312-.563-.563 0-.938.188-1.312.563-.75.75-.75 1.875 0 2.625 3.374 3.375 7.874 5.25 12.75 5.25h4.312l-2.063 2.062c-.75.75-.75 1.875 0 2.625s1.876.75 2.625 0l5.25-5.25c.75-.562.75-1.875 0-2.625z"
                                  fill="#0071a1" fill-rule="nonzero"/>
                        </svg>
                        <span class="jet-form-canvas__field-tooltip"><?php _e( 'Set up conditional rules', 'jet-engine' ); ?></span>
                        <span class="jet-form-canvas__field-has-conditions"
                              v-if="item.conditionals && item.conditionals.length"></span>
                    </div>
                </div>
            </div>
        </grid-item>
    </grid-layout>
    <div class="jet-form-canvas__actions" v-if="!showEditor && !showLogicEditor">
        <div :class="{
        'jet-form-canvas__captcha': true,
        'jet-form-canvas__captcha--enabled': captcha.enabled
        }">
            <label>
                <input type="checkbox" name="_captcha[enabled]" v-model="captcha.enabled">
				<?php _e( 'Enable reCAPTCHA v3 form verification', 'jet-enegine' ); ?>
            </label>
            <label v-if="captcha.enabled">
                <input type="checkbox" name="_captcha[use_global]" v-model="captcha.use_global">
				<?php _e( 'Use Global Settings', 'jet-enegine' ); ?>
            </label>
        </div>
        <div class="jet-form-canvas__buttons">
            <button type="button" class="jet-form-canvas__add" @click="addField( false, false )"><?php
				_e( 'Add Field', 'jet-engine' );
				?></button>
            <button type="button" class="jet-form-canvas__add add-default" @click="addField( true, false )"><?php
				_e( 'Add Submit Button', 'jet-engine' );
				?></button>
            <button type="button" class="jet-form-canvas__add add-default" @click="addField( false, false, true )"><?php
				_e( 'Add Page Break', 'jet-engine' );
				?></button>
        </div>
        <div class="jet-form-canvas__captcha-fields" v-if="captcha.enabled">
            <label>
				<?php _e( 'Site Key:', 'jet-engine' ); ?><br>
                <input
                        type="text"
                        name="_captcha[key]"
                        :disabled="captcha.use_global"
                        :value="getCaptchaVal( 'key' )"
                        @input="$set( captcha, 'key', $event.target.value )"
                >
                <input
                        v-if="captcha.use_global"
                        type="hidden"
                        name="_captcha[key]"
                        :value="captcha.key"
                >
            </label>
            <label>
				<?php _e( 'Secret Key:', 'jet-engine' ); ?><br>
                <input
                        type="text"
                        name="_captcha[secret]"
                        :disabled="captcha.use_global"
                        :value="getCaptchaVal( 'secret' )"
                        @input="$set( captcha, 'secret', $event.target.value )"
                >
                <input
                        v-if="captcha.use_global"
                        type="hidden"
                        name="_captcha[secret]"
                        :value="captcha.secret"
                >
            </label>
            <div class="jet-form-canvas__captcha-info">
                <i><?php
					printf(
						__( 'Register reCAPTCHA v3 keys %s.', 'jet-engine' ),
						'<a href="https://www.google.com/recaptcha/admin/create">' . __( 'here', 'jet-engine' ) . '</a>'
					);
					?></i>
            </div>
        </div>
    </div>
    <div class="jet-form-canvas__result">
        <textarea name="_form_data">{{ resultJSON }}</textarea>
    </div>
    <div class="jet-form-editor" v-if="showLogicEditor">
        <div class="jet-form-editor__header">
            <span><?php _e( 'Edit conditional logic for', 'jet-engine' ) ?></span>: {{ currentItem.settings.name }}
        </div>
        <div class="jet-form-editor__content">
            <div class="jet-form-editor__cl-rules">
                <div class="jet-form-editor__cl-rule" v-for="( rule, index ) in currentItem.conditionals">
					<?php
					/**
					 * Add custom fields on this hook
					 */
					do_action( 'jet-engine/forms/edit-conditional-logic/before' );
					?>
                    <div class="jet-form-editor__cl-rule--title">
						<?php _e( 'Conditional rule', 'jet-engine' ); ?> #{{ index + 1 }}
                        <span class="jet-form-editor__cl-rule--remove"
                              @click="condRuleDelete = index"><?php _e( 'Delete', 'jet-engine' ); ?></span>
                        <span class="jet-form-editor__cl-rule--remove-confirm" v-if="index === condRuleDelete">
							<?php _e( 'Are you sure?', 'jet-engine' ); ?>
							<span class="jet-form-editor__cl-rule--remove-confirm-yes"
                                  @click="confirmCondRuleDel"><?php _e( 'Yes', 'jet-engine' ); ?></span>
							<span class="jet-form-editor__cl-rule--remove-confirm-no"
                                  @click="cancelCondRuleDel"><?php _e( 'No', 'jet-engine' ); ?></span>
						</span>
                    </div>
                    <div class="jet-form-editor__row">
                        <div class="jet-form-editor__row-label"><?php _e( 'Type:', 'jet-engine' ); ?></div>
                        <div class="jet-form-editor__row-control">
                            <select type="text" v-model="currentItem.conditionals[ index ].type">
                                <option value="show"><?php _e( 'Show this field if...', 'jet-engine' ); ?></option>
                                <option value="hide"><?php _e( 'Hide this field if...', 'jet-engine' ); ?></option>
                                <option value="set_value"><?php _e( 'Set value for this field if...', 'jet-engine' ); ?></option>
								<?php /*<option value="set_calculated_value"><?php _e( 'Set calculated value for this field...', 'jet-engine' ); ?></option> */ ?>
                            </select>
                        </div>
                    </div>
                    <div class="jet-form-editor__row">
                        <div class="jet-form-editor__row-label"><?php _e( 'Field:', 'jet-engine' ); ?></div>
                        <div class="jet-form-editor__row-control">
                            <select type="text" v-model="currentItem.conditionals[ index ].field">
                                <option value=""><?php _e( 'Select field...', 'jet-engine' ); ?></option>
                                <option v-for="field in getAvailableConditionalsFields( currentItem )" :value="field">{{
                                    field }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="jet-form-editor__row">
                        <div class="jet-form-editor__row-label"><?php _e( 'Operator:', 'jet-engine' ); ?></div>
                        <div class="jet-form-editor__row-control">
                            <select type="text" v-model="currentItem.conditionals[ index ].operator">
                                <option value=""><?php _e( 'Select operator...', 'jet-engine' ); ?></option>
                                <option value="equal"><?php _e( 'Equal', 'jet-engine' ); ?></option>
                                <option value="greater"><?php _e( 'Greater than', 'jet-engine' ); ?></option>
                                <option value="less"><?php _e( 'Less than', 'jet-engine' ); ?></option>
                                <option value="between"><?php _e( 'Between', 'jet-engine' ); ?></option>
                                <option value="one_of"><?php _e( 'In the list', 'jet-engine' ); ?></option>
                                <option value="contain"><?php _e( 'Contain text', 'jet-engine' ); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="jet-form-editor__row">
                        <div class="jet-form-editor__row-label"><?php _e( 'Value to compare:', 'jet-engine' ); ?></div>
                        <div class="jet-form-editor__row-control">
                            <div class="jet-form-editor__dynamic-control">
                                <textarea v-model="currentItem.conditionals[ index ].value" rows="4"
                                          style="height: 100px;"></textarea>
                                <div class="jet-form-editor__dynamic-trigger"
                                     @click="condRuleDynamicIndex = index; showDynamicSettings()">
                                    <svg viewBox="0 0 54 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M42.6396 26.4347C37.8682 27.3436 32.5666 28.0252 27.1894 28.0252C21.8121 28.0252 16.4348 27.3436 11.7391 26.4347C6.96774 25.4502 3.18093 23.8597 0.37868 21.9663L0.37868 28.0252C0.37868 29.5399 1.59046 31.1304 3.78682 32.4179C5.98317 33.7054 9.46704 34.9172 13.6325 35.5988C17.798 36.2805 22.115 36.8106 27.1894 36.8106C32.2637 36.8106 36.6564 36.5077 40.7462 35.5988C44.8359 34.69 48.3198 33.7054 50.5162 32.4179C52.7125 31.1304 54 29.5399 54 28.0252L54 21.9663C51.122 23.8597 47.3352 25.4502 42.6396 26.4347ZM42.6396 53.5484C37.8682 54.5329 32.5666 55.1388 27.1894 55.1388C21.8121 55.1388 16.4348 54.5329 11.7391 53.5484C7.04348 52.5638 3.18093 51.0491 0.378682 49.1556L0.378682 55.1388C0.378683 56.7293 1.59046 58.3197 3.78682 59.5315C6.36186 60.819 9.46705 62.1066 13.6325 62.7125C17.7223 63.697 22.115 64 27.1894 64C32.2637 64 36.6564 63.697 40.7462 62.7125C44.8359 61.8036 48.3198 60.819 50.5162 59.5315C52.7125 57.9411 54 56.7293 54 54.8359L54 48.8527C51.122 51.0491 47.3352 52.2608 42.6396 53.5484ZM42.6396 39.9915C37.8682 40.9004 32.5666 41.582 27.1894 41.582C21.8121 41.582 16.4348 40.9004 11.7391 39.9915C6.96774 39.007 3.18093 37.4922 0.378681 35.5988L0.378681 41.582C0.378681 43.1725 1.59046 44.6872 3.78682 45.9747C6.36185 47.2622 9.46705 48.474 13.6325 49.1556C17.7223 50.0645 22.115 50.3674 27.1894 50.3674C32.2637 50.3674 36.6564 50.0645 40.7462 49.1556C44.8359 48.1711 48.3198 47.2622 50.5162 45.9747C52.7125 44.3843 54 43.1725 54 41.582L54 35.5988C51.122 37.4922 47.3352 39.007 42.6396 39.9915ZM40.4432 2.12337C36.3535 1.13879 31.885 0.835848 26.8864 0.835849C21.8878 0.835849 17.4194 1.13879 13.2539 2.12337C9.08836 3.10794 5.68022 4.01678 3.48387 5.3043C1.28751 6.59181 -3.4782e-06 8.10654 -3.33916e-06 9.697L-2.95513e-06 14.0897C-2.81609e-06 15.6802 1.28752 17.2706 3.48387 18.5582C6.05891 19.7699 9.1641 21.0575 13.2539 21.6633C17.3436 22.2692 21.8121 22.9509 26.8864 22.9509C31.9607 22.9509 36.3535 22.9509 40.4432 22.345C44.533 21.7391 48.0169 20.4516 50.2132 19.164C52.7125 17.5736 54 15.9831 54 14.3927L54 9.99995C54 8.40948 52.7125 6.81902 50.5162 5.60724C48.3198 4.39546 44.533 2.72926 40.4432 2.12337Z"
                                              fill="#7E8993"/>
                                    </svg>
                                </div>
                            </div>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="jet-form-editor__row-notice"><?php
							_e( 'Separate values with commas', 'jet-engine' );
							?></div>
                    </div>
                    <div class="jet-form-editor__row">
                        <div class="jet-form-editor__row-label"><?php _e( 'Value to set:', 'jet-engine' ); ?></div>
                        <div class="jet-form-editor__row-control">
                            <textarea v-model="currentItem.conditionals[ index ].set_value" rows="4"
                                      style="height: 100px;"></textarea>
                        </div>
                    </div>
					<?php
					/**
					 * Add custom fields on this hook
					 */
					do_action( 'jet-engine/forms/edit-conditional-logic/after' );
					?>
                </div>
            </div>
            <div class="jet-form-editor__cl-actions">
                <button type="button" class="button button-secondary" @click="newRule">
                    + <?php _e( 'New Rule', 'jet-engine' ); ?></button>
            </div>
        </div>
        <div class="jet-form-editor__actions">
            <div class="jet-form-editor__buttons">
                <button type="button" class="button button-primary button-large" @click="applyFieldChanges"><?php
					_e( 'Apply Changes', 'jet-engine' );
					?></button>
                &nbsp;&nbsp;
                <button type="button" class="button button-default button-large" @click="cancelFieldChanges"><?php
					_e( 'Cancel', 'jet-engine' );
					?></button>
            </div>
        </div>
    </div>
    <div class="jet-form-editor" v-if="showEditor">
        <div class="jet-form-editor__header">
            <span v-html="itemInstance( currentItem )"></span>: {{ currentItem.settings.name }}
        </div>
        <div class="jet-form-editor__content"
             v-if="true === currentItem.settings.is_submit || true === currentItem.settings.is_page_break"
        >
            <div class="jet-form-editor__row">
                <div class="jet-form-editor__row-label"><?php _e( 'Label:', 'jet-engine' ); ?></div>
                <div class="jet-form-editor__row-control">
                    <input type="text" v-model="currentItem.settings.label">
                </div>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <div class="jet-form-editor__row-notice"><?php
					_e( 'Leave empty to hide Next button', 'jet-engine' );
					?></div>
            </div>
            <div class="jet-form-editor__row" v-if="true === currentItem.settings.is_page_break">
                <div class="jet-form-editor__row-label"><?php _e( 'Disabled message:', 'jet-engine' ); ?></div>
                <div class="jet-form-editor__row-control">
                    <textarea v-model="currentItem.settings.page_break_disabled" rows="4"
                              style="height: 100px;"></textarea>
                </div>&nbsp;&nbsp;&nbsp;&nbsp;
                <div class="jet-form-editor__row-notice"><?php
					_e( 'Text to show if next page button is disabled. For example - "Fill required fields" etc.', 'jet-engine' );
					?></div>
            </div>
            <div class="jet-form-editor__row">
                <div class="jet-form-editor__row-label"><?php _e( 'Custom CSS Class:', 'jet-engine' ); ?></div>
                <div class="jet-form-editor__row-control">
                    <input type="text" v-model="currentItem.settings.class_name">
                </div>
            </div>
            <div class="jet-form-editor__row">
                <div class="jet-form-editor__row-label"><?php _e( 'Add Prev Page Button:', 'jet-engine' ); ?></div>
                <div class="jet-form-editor__row-control">
                    <input type="checkbox" value="required" v-model="currentItem.settings.add_prev">
                </div>&nbsp;&nbsp;&nbsp;&nbsp;
                <div class="jet-form-editor__row-notice"><?php
					_e( 'Check this to add prev page button before this button. Will work only if previous page exists', 'jet-engine' );
					?></div>
            </div>
            <div class="jet-form-editor__row" v-if="currentItem.settings.add_prev">
                <div class="jet-form-editor__row-label"><?php _e( 'Prev Page Button Label:', 'jet-engine' ); ?></div>
                <div class="jet-form-editor__row-control">
                    <input type="text" v-model="currentItem.settings.prev_label">
                </div>
            </div>
        </div>
        <div class="jet-form-editor__content"
             v-if="false === currentItem.settings.is_message && false === currentItem.settings.is_submit && ! currentItem.settings.is_page_break"
        >
            <div class="jet-form-editor__row">
                <div class="jet-form-editor__row-label"><?php _e( 'Type:', 'jet-engine' ); ?></div>
                <div class="jet-form-editor__row-control">
                    <select type="text" v-model="currentItem.settings.type" @change="setHeadingName()">
                        <option v-for="( typeLabel, typeVal ) in fieldTypes" :value="typeVal">{{ typeLabel }}</option>
                    </select>
                </div>
            </div>
			<?php
			/**
			 * Add custom fields on this hook
			 */
			do_action( 'jet-engine/forms/edit-field/before' );
			?>
            <div class="jet-form-editor__row" v-if="'text' === currentItem.settings.type">
                <div class="jet-form-editor__row-label"><?php _e( 'Field Type:', 'jet-engine' ); ?></div>
                <div class="jet-form-editor__row-control">
                    <select type="text" v-model="currentItem.settings.field_type">
                        <option v-for="( typeLabel, typeName ) in inputTypes" :value="typeName">{{ typeLabel }}</option>
                    </select>
                </div>
            </div>
            <template v-if="'repeater_end' !== currentItem.settings.type">
                <div class="jet-form-editor__row"
                     v-if="'heading' !== currentItem.settings.type && 'group_break' !== currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Name:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.name">
                    </div>
                </div>
                <div class="jet-form-editor__row" v-if="'group_break' !== currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Label:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.label">
                    </div>
                </div>
                <div class="jet-form-editor__row" v-if="'group_break' !== currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Description:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.desc">
                    </div>
                </div>
                <div class="jet-form-editor__row" v-if="'repeater_start' === currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Manage repeater items count:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.manage_items_count">
                            <option value="manually"><?php _e( 'Manually', 'jet-engine' ); ?></option>
                            <option value="dynamically"><?php _e( 'Dynamically (get count from form field)', 'jet-engine' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'repeater_start' === currentItem.settings.type && 'dynamically' === currentItem.settings.manage_items_count">
                    <div class="jet-form-editor__row-label"><?php _e( 'Manage repeater items count:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.manage_items_count_field">
                            <option v-for="field in availableFields" v-if="field !== currentItem.settings.name"
                                    :value="field">{{ field }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'repeater_start' === currentItem.settings.type && 'dynamically' !== currentItem.settings.manage_items_count">
                    <div class="jet-form-editor__row-label"><?php _e( 'Add New Item Label:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.new_item_label">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="!inArray( currentItem.settings.type, [ 'calculated', 'wysiwyg', 'range', 'heading', 'group_break' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Required:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="checkbox" value="required" v-model="currentItem.settings.required">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'hidden' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Field Value:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.hidden_value">
                            <option v-for="( label, value ) in hiddenValues" :value="value">{{ label }}</option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'hidden' === currentItem.settings.type && 'current_date' === currentItem.settings.hidden_value"
                >
                    <div class="jet-form-editor__row-label">
						<?php _e( 'Format:', 'jet-engine' ); ?>
                        <div class="jet-form-editor__row-notice">
							<?php _e( 'Leave empty to use format by default.', 'jet-engine' ); ?>&nbsp;
							<?php printf( '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">%s</a>', __( 'Documentation on date and time formatting', 'jet-engine' ) ); ?>
                        </div>
                    </div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.date_format">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice">
                        <b><?php _e( 'Example:', 'jet-engine' ); ?></b><br>
                        <i>Y-m-d\TH:i - </i><?php _e( 'datetime format', 'jet-engine' ); ?><br>
                        <i>U - </i><?php _e( 'timestamp format', 'jet-engine' ); ?>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'hidden' === currentItem.settings.type && 'manual_input' === currentItem.settings.hidden_value"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Value:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <div class="jet-form-editor__dynamic-control">
                            <input type="text" v-model="currentItem.settings.default">
                            <div class="jet-form-editor__dynamic-trigger" @click="showDynamicSettings">
                                <svg viewBox="0 0 54 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M42.6396 26.4347C37.8682 27.3436 32.5666 28.0252 27.1894 28.0252C21.8121 28.0252 16.4348 27.3436 11.7391 26.4347C6.96774 25.4502 3.18093 23.8597 0.37868 21.9663L0.37868 28.0252C0.37868 29.5399 1.59046 31.1304 3.78682 32.4179C5.98317 33.7054 9.46704 34.9172 13.6325 35.5988C17.798 36.2805 22.115 36.8106 27.1894 36.8106C32.2637 36.8106 36.6564 36.5077 40.7462 35.5988C44.8359 34.69 48.3198 33.7054 50.5162 32.4179C52.7125 31.1304 54 29.5399 54 28.0252L54 21.9663C51.122 23.8597 47.3352 25.4502 42.6396 26.4347ZM42.6396 53.5484C37.8682 54.5329 32.5666 55.1388 27.1894 55.1388C21.8121 55.1388 16.4348 54.5329 11.7391 53.5484C7.04348 52.5638 3.18093 51.0491 0.378682 49.1556L0.378682 55.1388C0.378683 56.7293 1.59046 58.3197 3.78682 59.5315C6.36186 60.819 9.46705 62.1066 13.6325 62.7125C17.7223 63.697 22.115 64 27.1894 64C32.2637 64 36.6564 63.697 40.7462 62.7125C44.8359 61.8036 48.3198 60.819 50.5162 59.5315C52.7125 57.9411 54 56.7293 54 54.8359L54 48.8527C51.122 51.0491 47.3352 52.2608 42.6396 53.5484ZM42.6396 39.9915C37.8682 40.9004 32.5666 41.582 27.1894 41.582C21.8121 41.582 16.4348 40.9004 11.7391 39.9915C6.96774 39.007 3.18093 37.4922 0.378681 35.5988L0.378681 41.582C0.378681 43.1725 1.59046 44.6872 3.78682 45.9747C6.36185 47.2622 9.46705 48.474 13.6325 49.1556C17.7223 50.0645 22.115 50.3674 27.1894 50.3674C32.2637 50.3674 36.6564 50.0645 40.7462 49.1556C44.8359 48.1711 48.3198 47.2622 50.5162 45.9747C52.7125 44.3843 54 43.1725 54 41.582L54 35.5988C51.122 37.4922 47.3352 39.007 42.6396 39.9915ZM40.4432 2.12337C36.3535 1.13879 31.885 0.835848 26.8864 0.835849C21.8878 0.835849 17.4194 1.13879 13.2539 2.12337C9.08836 3.10794 5.68022 4.01678 3.48387 5.3043C1.28751 6.59181 -3.4782e-06 8.10654 -3.33916e-06 9.697L-2.95513e-06 14.0897C-2.81609e-06 15.6802 1.28752 17.2706 3.48387 18.5582C6.05891 19.7699 9.1641 21.0575 13.2539 21.6633C17.3436 22.2692 21.8121 22.9509 26.8864 22.9509C31.9607 22.9509 36.3535 22.9509 40.4432 22.345C44.533 21.7391 48.0169 20.4516 50.2132 19.164C52.7125 17.5736 54 15.9831 54 14.3927L54 9.99995C54 8.40948 52.7125 6.81902 50.5162 5.60724C48.3198 4.39546 44.533 2.72926 40.4432 2.12337Z"
                                          fill="#7E8993"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'hidden' === currentItem.settings.type && inArray( currentItem.settings.hidden_value, [ 'post_meta', 'user_meta' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Meta field to get value from:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.hidden_value_field">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'hidden' === currentItem.settings.type && 'query_var' === currentItem.settings.hidden_value"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Query variable key:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.query_var_key">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'Query parameter name', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Fill Options From:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select v-model="currentItem.settings.field_options_from">
                            <option :value="value" v-for="( label, value ) in fieldOptionsSources">{{ label }}</option>
                        </select>
                    </div>
                </div>
				<?php do_action( 'jet-engine/forms/editor/field-options-controls' ); ?>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && 'generate' === currentItem.settings.field_options_from"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Generator Function:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select v-model="currentItem.settings.generator_function">
                            <option v-for="( generatorName, generatoID ) in generatorsList" :value="generatoID">{{
                                generatorName }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && 'generate' === currentItem.settings.field_options_from"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Field Name:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.generator_field">
                    </div>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"
                         v-if="inArray(currentItem.settings.generator_function, [ 'get_from_field', 'get_from_option' ] )"><?php
						_e( 'For fields inside a repeater need to set the repeater name and the field name through a separator |. <b>Example:</b> <i>repeater_name|field_name</i>', 'jet-engine' );
						?></div>
                    <div class="jet-form-editor__row-notice"
                         v-if="'num_range' === currentItem.settings.generator_function"><?php
						_e( 'For <b>Numbers range</b> generator set field with max range value', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && 'posts' === currentItem.settings.field_options_from"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Post type:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.field_options_post_type">
                            <option v-for="( typeLabel, typeValue ) in postTypes" :value="typeValue">
                                {{ typeLabel }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && 'terms' === currentItem.settings.field_options_from"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Taxonomy:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.field_options_tax">
                            <option v-for="( taxLabel, taxValue ) in taxonomies" :value="taxValue">
                                {{ taxLabel }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && inArray( currentItem.settings.field_options_from, [ 'terms', 'posts', 'generate' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Value from meta field:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.value_from_key">
                    </div>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'By default post/term ID is used as value. Here you can set meta field name to use its value as form field value', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && inArray( currentItem.settings.field_options_from, [ 'terms', 'posts', 'generate' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Calculated value from meta field:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.calculated_value_from_key">
                    </div>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'Here you can set meta field name to use its value as calculated value for current form field', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'checkboxes', 'radio' ] ) && inArray( currentItem.settings.field_options_from, [ 'terms', 'posts', 'generate' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Custom item template:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="checkbox" value="1" v-model="currentItem.settings.custom_item_template">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'Check this to use custom listing template to show field items.', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'checkboxes', 'radio' ] ) && inArray( currentItem.settings.field_options_from, [ 'terms', 'posts', 'generate' ] ) && currentItem.settings.custom_item_template"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Custom item template:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.custom_item_template_id">
                            <option v-for="( listingItemName, listingItemID ) in listingItems" :value="listingItemID">
                                {{ listingItemName }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && 'meta_field' === currentItem.settings.field_options_from"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Meta field to get value from:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.field_options_key">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && 'manual_input' === currentItem.settings.field_options_from"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Options List:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <div class="jet-form-repeater">
                            <div class="jet-form-repeater__items">
                                <div class="jet-form-repeater__item"
                                     v-for="( option, index ) in currentItem.settings.field_options"
                                >
                                    <div class="jet-form-repeater__item-input">
                                        <div class="jet-form-repeater__item-input-label"><?php
											_e( 'Value:', 'jet-engine' );
											?></div>
                                        <input type="text" v-model="currentItem.settings.field_options[ index ].value">
                                    </div>
                                    <div class="jet-form-repeater__item-input">
                                        <div class="jet-form-repeater__item-input-label"><?php
											_e( 'Label:', 'jet-engine' );
											?></div>
                                        <div class="jet-form-repeater__item-input-control">
                                            <input type="text"
                                                   v-model="currentItem.settings.field_options[ index ].label">
                                        </div>
                                    </div>
                                    <div class="jet-form-repeater__item-input">
                                        <div class="jet-form-repeater__item-input-label"><?php
											_e( 'Calculate:', 'jet-engine' );
											?></div>
                                        <div class="jet-form-repeater__item-input-control">
                                            <input type="text"
                                                   v-model="currentItem.settings.field_options[ index ].calculate">
                                        </div>
                                    </div>
                                    <div class="jet-form-repeater__item-delete">
										<span class="dashicons dashicons-dismiss"
                                              @click="deleteRepeterItem( index, currentItem.settings.field_options )"
                                        ></span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="button"
                                    @click="addRepeaterItem( currentItem.settings.field_options, { value: '', label: '' } )"
                            ><?php
								_e( 'Add Option', 'jet-engine' );
								?></button>
                        </div>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'select', 'radio' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Switch page on change:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="checkbox" value="1" v-model="currentItem.settings.switch_on_change">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'Check this to switch page to next on current value change', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'repeater_start' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Calculate repeater row value:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.repeater_calc_type">
                            <option value="default"><?php _e( 'Default (returns rows count)', 'jet-engine' ); ?></option>
                            <option value="custom"><?php _e( 'Custom (calculate custom value for each row)', 'jet-engine' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row" v-if="'text' === currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Set input mask:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="checkbox" value="1" v-model="currentItem.settings.enable_input_mask">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'Check this to setup specific input format for current field', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'text' === currentItem.settings.type && currentItem.settings.enable_input_mask">
                    <div class="jet-form-editor__row-label"><?php _e( 'Mask type:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select v-model="currentItem.settings.mask_type">
                            <option value=""><?php _e( 'Default', 'jet-engine' ); ?></option>
                            <option value="datetime"><?php _e( 'Datetime', 'jet-engine' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'text' === currentItem.settings.type && currentItem.settings.enable_input_mask">
                    <div class="jet-form-editor__row-label"><?php _e( 'Input mask:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.input_mask">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice" v-if="'datetime' === currentItem.settings.mask_type">
						<?php _e( 'Examples: dd/mm/yyyy, mm/dd/yyyy', 'jet-engine' ); ?><br>
						<?php _e( 'More info - ', 'jet-engine' ); ?><a
                                href="https://github.com/RobinHerbots/Inputmask/blob/5.x/README_date.md"
                                target="_blank"><?php
							_e( 'here', 'jet-engine' );
							?></a>
                    </div>
                    <div class="jet-form-editor__row-notice" v-else>
						<?php _e( 'Examples: (999) 999-9999 - static mask, 9-a{1,3}9{1,3} - mask with dynamic syntax', 'jet-engine' ); ?>
                        <br>
						<?php _e( 'Default masking definitions: 9 - numeric, a - alphabetical, * - alphanumeric', 'jet-engine' ); ?>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'text' === currentItem.settings.type && currentItem.settings.enable_input_mask">
                    <div class="jet-form-editor__row-label"><?php _e( 'Mask visibility:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select v-model="currentItem.settings.mask_visibility">
                            <option value="always"><?php _e( 'Always', 'jet-engine' ); ?></option>
                            <option value="hover"><?php _e( 'On hover', 'jet-engine' ); ?></option>
                            <option value="focus"><?php _e( 'On focus', 'jet-engine' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'text' === currentItem.settings.type && currentItem.settings.enable_input_mask">
                    <div class="jet-form-editor__row-label"><?php _e( 'Mask placeholder:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select v-model="currentItem.settings.mask_placeholder">
                            <option value="_">_</option>
                            <option value="-">-</option>
                            <option value="*">*</option>
                            <option value="•">•</option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'date', 'datetime-local' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Is Timestamp:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="checkbox" value="is_timestamp" v-model="currentItem.settings.is_timestamp">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'Check this if you want to send value of this field as timestamp instead of plain date', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="showCalculatedFormulaField( currentItem.settings )"
                >
                    <div class="jet-form-editor__row-label">
						<?php _e( 'Calculation Formula:', 'jet-engine' ); ?>
                        <div class="jet-form-editor__row-notice">
							<?php _e( 'Set math formula to calculate field value.', 'jet-engine' ); ?><br>
							<?php _e( 'For example:', 'jet-engine' ); ?><br><br>
                            %FIELD::quantity%*%META::price%<br><br>
							<?php _e( 'Where:', 'jet-engine' ); ?><br>
                            - <?php _e( '%FIELD::quantity% - macros for form field value. "quantity" - is a field name to get value from', 'jet-engine' ); ?>
                            <br>
                            - <?php _e( '%META::price% - macros for current post meta value. "quantity" - is a meta key to get value from', 'jet-engine' ); ?>
                            <br><br>
                        </div>
                    </div>
                    <div class="jet-form-editor__row-control">
                        <textarea v-model="currentItem.settings.calc_formula"></textarea>
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice">
                        <div><b><?php _e( 'Available fields:', 'jet-engine' ); ?></b></div>
                        <div v-for="field in availableFields" v-if="field !== currentItem.settings.name"><i>%FIELD::{{
                                field }}%</i></div>
						<?php do_action( 'jet-engine/forms/editor/macros-list' ); ?>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'calculated' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Decimal Places Number:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="number" min="0" max="20" v-model="currentItem.settings.precision">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'calculated' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Calculated Value Prefix:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.calc_prefix">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'calculated' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Calculated Value Suffix:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.calc_suffix">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'calculated' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Hidden:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="checkbox" value="required" v-model="currentItem.settings.calc_hidden">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'Check this to hide calculated field', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'text' === currentItem.settings.type || 'select' === currentItem.settings.type || 'textarea' === currentItem.settings.type || 'number' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Placeholder:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.placeholder">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'hidden' !== currentItem.settings.type && 'media' !== currentItem.settings.type && 'heading' !== currentItem.settings.type && 'group_break' !== currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Default:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <div class="jet-form-editor__dynamic-control">
                            <input type="text" v-model="currentItem.settings.default">
                            <div class="jet-form-editor__dynamic-trigger" @click="showDynamicSettings">
                                <svg viewBox="0 0 54 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M42.6396 26.4347C37.8682 27.3436 32.5666 28.0252 27.1894 28.0252C21.8121 28.0252 16.4348 27.3436 11.7391 26.4347C6.96774 25.4502 3.18093 23.8597 0.37868 21.9663L0.37868 28.0252C0.37868 29.5399 1.59046 31.1304 3.78682 32.4179C5.98317 33.7054 9.46704 34.9172 13.6325 35.5988C17.798 36.2805 22.115 36.8106 27.1894 36.8106C32.2637 36.8106 36.6564 36.5077 40.7462 35.5988C44.8359 34.69 48.3198 33.7054 50.5162 32.4179C52.7125 31.1304 54 29.5399 54 28.0252L54 21.9663C51.122 23.8597 47.3352 25.4502 42.6396 26.4347ZM42.6396 53.5484C37.8682 54.5329 32.5666 55.1388 27.1894 55.1388C21.8121 55.1388 16.4348 54.5329 11.7391 53.5484C7.04348 52.5638 3.18093 51.0491 0.378682 49.1556L0.378682 55.1388C0.378683 56.7293 1.59046 58.3197 3.78682 59.5315C6.36186 60.819 9.46705 62.1066 13.6325 62.7125C17.7223 63.697 22.115 64 27.1894 64C32.2637 64 36.6564 63.697 40.7462 62.7125C44.8359 61.8036 48.3198 60.819 50.5162 59.5315C52.7125 57.9411 54 56.7293 54 54.8359L54 48.8527C51.122 51.0491 47.3352 52.2608 42.6396 53.5484ZM42.6396 39.9915C37.8682 40.9004 32.5666 41.582 27.1894 41.582C21.8121 41.582 16.4348 40.9004 11.7391 39.9915C6.96774 39.007 3.18093 37.4922 0.378681 35.5988L0.378681 41.582C0.378681 43.1725 1.59046 44.6872 3.78682 45.9747C6.36185 47.2622 9.46705 48.474 13.6325 49.1556C17.7223 50.0645 22.115 50.3674 27.1894 50.3674C32.2637 50.3674 36.6564 50.0645 40.7462 49.1556C44.8359 48.1711 48.3198 47.2622 50.5162 45.9747C52.7125 44.3843 54 43.1725 54 41.582L54 35.5988C51.122 37.4922 47.3352 39.007 42.6396 39.9915ZM40.4432 2.12337C36.3535 1.13879 31.885 0.835848 26.8864 0.835849C21.8878 0.835849 17.4194 1.13879 13.2539 2.12337C9.08836 3.10794 5.68022 4.01678 3.48387 5.3043C1.28751 6.59181 -3.4782e-06 8.10654 -3.33916e-06 9.697L-2.95513e-06 14.0897C-2.81609e-06 15.6802 1.28752 17.2706 3.48387 18.5582C6.05891 19.7699 9.1641 21.0575 13.2539 21.6633C17.3436 22.2692 21.8121 22.9509 26.8864 22.9509C31.9607 22.9509 36.3535 22.9509 40.4432 22.345C44.533 21.7391 48.0169 20.4516 50.2132 19.164C52.7125 17.5736 54 15.9831 54 14.3927L54 9.99995C54 8.40948 52.7125 6.81902 50.5162 5.60724C48.3198 4.39546 44.533 2.72926 40.4432 2.12337Z"
                                          fill="#7E8993"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'text', 'textarea' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Minlength:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="number" v-model="currentItem.settings.minlength">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'text', 'textarea' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Maxlength:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="number" v-model="currentItem.settings.maxlength">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'number', 'range' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Min Value:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="number" v-model="currentItem.settings.min">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'number', 'range' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Max Value:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="number" v-model="currentItem.settings.max">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="inArray( currentItem.settings.type, [ 'number', 'range' ] )"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Step:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="number" v-model="currentItem.settings.step">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'range' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Value prefix:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.prefix">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'range' === currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Value suffix:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.suffix">
                    </div>
                </div>
                <div class="jet-form-editor__row" v-if="'media' === currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'User access:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.allowed_user_cap">
                            <option value="all"><?php _e( 'Any registered user', 'jet-engine' ); ?></option>
                            <option value="upload_files"><?php _e( 'Any user, who allowed to upload files', 'jet-engine' ); ?></option>
                            <option value="edit_posts"><?php _e( 'Any user, who allowed to edit posts', 'jet-engine' ); ?></option>
                            <option value="any_user"><?php _e( 'Any user ( incl. Guest )', 'jet-engine' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row" v-if="'media' === currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Insert attachment:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="checkbox" value="1" v-model="currentItem.settings.insert_attachment">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'If checked new attachment will be inserted for uploaded file. <b>Note: work only for logged-in users!</b>', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'media' === currentItem.settings.type && currentItem.settings.insert_attachment">
                    <div class="jet-form-editor__row-label"><?php _e( 'Field value:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select v-model="currentItem.settings.value_format">
                            <option value="id"><?php _e( 'Attachment ID', 'jet-engine' ); ?></option>
                            <option value="url"><?php _e( 'Attachment URL', 'jet-engine' ); ?></option>
                            <option value="both"><?php _e( 'Array with attachment ID and URL', 'jet-engine' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row" v-if="'media' === currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Maximum allowed files to upload:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="number" v-model="currentItem.settings.max_files" min="1">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'If not set allow to upload 1 file', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row" v-if="'media' === currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Maximum size in Mb:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="number" v-model="currentItem.settings.max_size" step="0.1" min="0.1">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice">Mb</div>
                </div>
                <div class="jet-form-editor__row" v-if="'media' === currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Allow MIME types:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select v-model="currentItem.settings.allowed_mimes" multiple size="10">
                            <option v-for="mime in mimes" :value="mime">{{ mime }}</option>
                        </select>
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'If no MIME type selected will allow all types.', 'jet-engine' );
						?><br><?php
						_e( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row" v-if="'hidden' !== currentItem.settings.type">
                    <div class="jet-form-editor__row-label"><?php _e( 'Add Prev Page Button:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="checkbox" value="required" v-model="currentItem.settings.add_prev">
                    </div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="jet-form-editor__row-notice"><?php
						_e( 'Check this to add prev page button before this button. Will work only if previous page exists', 'jet-engine' );
						?></div>
                </div>
                <div class="jet-form-editor__row" v-if="currentItem.settings.add_prev">
                    <div class="jet-form-editor__row-label"><?php _e( 'Prev Page Button Label:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.prev_label">
                    </div>
                </div>
                <div class="jet-form-editor__row"
                     v-if="'hidden' !== currentItem.settings.type"
                >
                    <div class="jet-form-editor__row-label"><?php _e( 'Field Visibility:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <select type="text" v-model="currentItem.settings.visibility">
                            <option value="all"><?php _e( 'For all', 'jet-engine' ); ?></option>
                            <option value="logged_id"><?php _e( 'Only for logged in users', 'jet-engine' ); ?></option>
                            <option value="not_logged_in"><?php _e( 'Only for NOT-logged in users', 'jet-engine' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="jet-form-editor__row">
                    <div class="jet-form-editor__row-label"><?php _e( 'Custom CSS Class:', 'jet-engine' ); ?></div>
                    <div class="jet-form-editor__row-control">
                        <input type="text" v-model="currentItem.settings.class_name">
                    </div>
                </div>
            </template>
			<?php
			/**
			 * Add custom fields on this hook
			 */
			do_action( 'jet-engine/forms/edit-field/after' );
			?>
        </div>
        <div class="jet-form-editor__actions">
            <div class="jet-form-editor__buttons">
                <button type="button" class="button button-primary button-large" @click="applyFieldChanges"><?php
					_e( 'Apply Changes', 'jet-engine' );
					?></button>
                &nbsp;&nbsp;
                <button type="button" class="button button-default button-large" @click="cancelFieldChanges"><?php
					_e( 'Cancel', 'jet-engine' );
					?></button>
            </div>
        </div>
    </div>
    <div class="jet-form-canvas__preset">
        <div class="jet-form-canvas__preset-enable">
            <label class="jet-form-canvas__preset-heading">
                <input type="checkbox" name="_preset[enabled]" v-model="preset.enabled">
				<?php _e( 'Pre-set form field values', 'jet-enegine' ); ?>
            </label>
            <div class="jet-form-canvas__preset-controls" v-if="preset.enabled">
                <jet-form-preset-editor v-model="preset" :decode="false" :encode="false"
                                        :available-fields="availableFields"></jet-form-preset-editor>
            </div>
        </div>
    </div>
    <div :class="{ 'jet-form-dynamic-settings':true }" v-if="editDynamicSettings && currentItem">
        <div :class="{ 'jet-form-dynamic-settings__overlay':true }" @click="cancelDynamicSettings"></div>
        <div class="jet-form-dynamic-settings__content">
            <jet-form-preset-editor v-if="showLogicEditor"
                                    v-model="currentItem.conditionals[ condRuleDynamicIndex ].value" :decode="true"
                                    :encode="true" :available-fields="false"></jet-form-preset-editor>
            <jet-form-preset-editor v-else v-model="currentItem.settings.default" :decode="true" :encode="true"
                                    :available-fields="false"></jet-form-preset-editor>
            <div class="jet-form-dynamic-settings__actions">
                <button type="button" class="button button-primary button-large"
                        @click="setDynamicSettings"><?php _e( 'Done', 'jet-engine' ); ?></button>
                <button type="button" class="button button-secondary button-large"
                        @click="cancelDynamicSettings"><?php _e( 'Cancel', 'jet-engine' ); ?></button>
            </div>
        </div>
    </div>
</div>
