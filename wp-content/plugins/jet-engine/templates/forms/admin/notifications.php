<div id="notifications_builder">
	<div class="jet-form-list">
		<slick-list lock-axis="y" :use-drag-handle="true" v-model="items">
			<slick-item v-for="( item, index ) in items" :index="index" :key="index">
				<div class="notifications-builder jet-form-list__item">
				<div class="jet-form-list__item-handle" v-handle>
					<svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
						<line y1="0.5" x2="12" y2="0.5" stroke="#DDDDDD"/>
						<line y1="6.5" x2="12" y2="6.5" stroke="#DDDDDD"/>
						<line y1="3.5" x2="12" y2="3.5" stroke="#DDDDDD"/>
					</svg>
				</div>
				<div class="jet-form-canvas__field-content">
					<div class="jet-form-canvas__field-start">
						<div class="jet-form-canvas__field-remove" @click="removeItem( item, index )"></div>
						<div class="jet-form-canvas__field-label">
							<span class="jet-form-canvas__field-name">
								<span v-html="availableTypes[ item.type ]"></span>
							</span>
							<span class="jet-form-canvas__field-notice" v-if="showRedirectNotice( item, index )" v-html="redirectNotice"></span>
						</div>
					</div>
					<div class="jet-form-canvas__field-end">
						<div class="jet-form-canvas__field-edit" @click="editItem( item, index )">
							<span class="dashicons dashicons-edit"></span>
						</div>
					</div>
				</div>
				<div class="jet-form-editor" v-if="showEditor && index === currentIndex">
					<div class="jet-form-editor__content">
						<div class="jet-form-editor__row">
							<div class="jet-form-editor__row-label"><?php _e( 'Type:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.type">
									<option v-for="( typeLabel, typeValue ) in availableTypes" :value="typeValue">
										{{ typeLabel }}
									</option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'hook' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Hook Name:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.hook_name">
								<div class="jet-form-editor__row-note">
									<?php _e( 'Called hook names:', 'jet-engine' ); ?>
									<ul>
										<li><code>jet-engine-booking/{{ currentItem.hook_name }}</code> - <?php _e( 'WP action. Perform a hook without an ability to validate form,', 'jet-engine' ); ?></li>
										<li><code>jet-engine-booking/filter/{{ currentItem.hook_name }}</code> - <?php _e( 'WP filter. Perform a hook with an ability to validate form. Allows to return error message.', 'jet-engine' ); ?></li>
									</ul>
									<?php _e( 'Hook arguments:', 'jet-engine' ); ?>
									<ul>
										<li><code>$result</code> - <?php _e( 'only for WP filter. Hook execution result,', 'jet-engine' ); ?></li>
										<li><code>$data</code> - <?php _e( 'array with submitted form data,', 'jet-engine' ); ?></li>
										<li><code>$form</code> - <?php _e( 'current form ID,', 'jet-engine' ); ?></li>
										<li><code>$notifications</code> - <?php _e( 'notifications object, allows to returns error status by returning <code>$notifications->set_specific_status( "Status message" )</code> method from the hook callback.', 'jet-engine' ); ?></li>
									</ul>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Mail to:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.mail_to">
									<option value="admin"><?php _e( 'Admin email', 'jet-engine' ); ?></option>
									<option value="form"><?php _e( 'Email from submitted form field', 'jet-engine' ); ?></option>
									<option value="custom"><?php _e( 'Custom email', 'jet-engine' ); ?></option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type && 'custom' === currentItem.mail_to">
							<div class="jet-form-editor__row-label"><?php _e( 'Email Address:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.custom_email">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type && 'form' === currentItem.mail_to">
							<div class="jet-form-editor__row-label"><?php _e( 'From Field:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.from_field">
									<option v-for="field in availableFields" :value="field" >{{ field }}</option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Reply to:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.reply_to">
									<option value=""><?php _e( 'Not selected', 'jet-engine' ); ?></option>
									<option value="form"><?php _e( 'Email from submitted form field', 'jet-engine' ); ?></option>
									<option value="custom"><?php _e( 'Custom email', 'jet-engine' ); ?></option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type && 'custom' === currentItem.reply_to">
							<div class="jet-form-editor__row-label"><?php _e( 'Reply to Email Address:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.reply_to_email">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type && 'form' === currentItem.reply_to">
							<div class="jet-form-editor__row-label"><?php _e( 'Reply To Email From Field:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.reply_from_field">
									<option v-for="field in availableFields" :value="field" >{{ field }}</option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'insert_post' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Post Type:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.post_type">
									<option v-for="( typeLabel, typeValue ) in postTypes" :value="typeValue">
										{{ typeLabel }}
									</option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'insert_post' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Post Status:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.post_status">
									<option value=""><?php _e( 'Select status...', 'jet-engine' ); ?></option>
									<option v-for="( statusLabel, statusValue ) in postStatuses" :value="statusValue" >
										{{ statusLabel }}
									</option>
									<option value="keep-current"><?php _e( 'Keep current status (when updating post)', 'jet-engine' ); ?></option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'insert_post' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Fields Map:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set meta fields names or post properties to save appropriate form fields into', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-editor__row-map" v-for="( field, index ) in availableFields" :key="field + index" v-if="'inserted_post_id' !== field">
										<span>{{ field }}</span>
										<jet-post-field-control :fields="postProps" meta-prop="post_meta" terms-prop="post_terms" v-model="currentItem.fields_map[ field ]"></jet-post-field-control>
									</div>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'insert_post' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Default Fields:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set default meta values which should be set on post creation', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-repeater">
										<div class="jet-form-repeater__items">
											<div class="jet-form-repeater__item" v-for="( field, index ) in currentItem.default_meta" :key="'default-meta-' + index">
												<div class="jet-form-repeater__item-input" style="width: 43.5%;">
													<div class="jet-form-repeater__item-input-label"><?php
														_e( 'Meta Key:', 'jet-engine' );
													?></div>
													<input type="text" v-model="currentItem.default_meta[ index ].key">
												</div>
												<div class="jet-form-repeater__item-input" style="width: 43.5%;">
													<div class="jet-form-repeater__item-input-label"><?php
														_e( 'Meta Value:', 'jet-engine' );
													?></div>
													<div class="jet-form-repeater__item-input-control">
														<input type="text" v-model="currentItem.default_meta[ index ].value">
													</div>
												</div>
												<div class="jet-form-repeater__item-delete">
													<span class="dashicons dashicons-dismiss" @click="deleteRepeterItem( index, currentItem.default_meta )"
													></span>
												</div>
											</div>
										</div>
										<button type="button" class="button" @click="addRepeaterItem( currentItem.default_meta, { key: '', value: '' } )"><?php
											_e( 'Add Option', 'jet-engine' );
										?></button>
									</div>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'update_user' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Fields Map:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set user properties or meta fields to save appropriate form fields into', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-editor__row-map" v-for="field in availableFields" v-if="'inserted_post_id' !== field">
										<span>{{ field }}</span>
										<jet-post-field-control :fields="userProps" meta-prop="user_meta" :terms-prop="false" v-model="currentItem.fields_map[ field ]"></jet-post-field-control>
									</div>
								</div>
							</div>
						</div>
                        <div class="jet-form-editor__row" v-if="'register_user' === currentItem.type">
                            <div class="jet-form-editor__row-label"><?php _e( 'Allow creating new users by existing users', 'jet-engine' ); ?></div>
                            <div class="jet-form-editor__row-control">
                                <input type="checkbox" value="yes" v-model="currentItem.allow_register">
                            </div>
                        </div>
                        <div class="jet-form-editor__row" v-if="'register_user' === currentItem.type && currentItem.allow_register">
                            <div class="jet-form-editor__row-label"><?php _e( 'Who can add new user?', 'jet-engine' ); ?></div>
                            <div class="jet-form-editor__row-control">
                                <select v-model="currentItem.role_can_register">
                                    <option value="">--</option>
                                    <option v-for="({ value, label }) in userRoles" :value="value">{{ label }}</option>
                                </select>
                            </div>
                        </div>
						<div class="jet-form-editor__row" v-if="'register_user' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Fields Map:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set form fields names to to get user data from', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-error" v-if="! currentItem.fields_map.login || ! currentItem.fields_map.email || ! currentItem.fields_map.password"><?php
									_e( 'User Login, Email and Password fields can\'t be empty', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-editor__row-map" v-for="( uFieldLabel, uField ) in userFields">
										<span>{{ uFieldLabel }}</span>
										<select v-model="currentItem.fields_map[ uField ]">
											<option value="">--</option>
											<option v-for="field in availableFields" :value="field">{{ field }}</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'register_user' === currentItem.type || 'update_user' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'User Role:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.user_role">
									<option value=""><?php _e( 'Select role for the user...', 'jet-engine' ); ?></option>
									<option v-for="role in userRoles" :value="role.value" v-if="'administrator' !== role.value">
										{{ role.label }}
									</option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'register_user' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'User Meta:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set user meta fields to save appropriate form fields into', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-editor__row-map" v-for="field in availableFields" v-if="'inserted_post_id' !== field">
										<span>{{ field }}</span>
										<input type="text" v-model="currentItem.meta_fields_map[ field ]">
									</div>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'register_user' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Log In User after Register:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="checkbox" value="yes" v-model="currentItem.log_in">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'register_user' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Add User ID to form data:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="checkbox" value="yes" v-model="currentItem.add_user_id">
								<div class="jet-form-editor__row-control-desc">
									<?php _e( 'Registered user ID will be added to form data. If form is filled by logged in user - current user ID will be added to form data.', 'jet-engine' ); ?>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Subject:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.email.subject">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'From Name:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.email.from_name">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php
								_e( 'From Email Address:', 'jet-engine' );
							?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.email.from_address">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Content type:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.email.content_type">
									<option value="text/html"><?php _e( 'HTML', 'jet-engine' ); ?></option>
									<option value="text/plain"><?php _e( 'Plain text', 'jet-engine' ); ?></option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'webhook' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Webhook URL:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.webhook_url">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'email' === currentItem.type">
							<div class="jet-form-editor__row-label">
								<?php _e( 'Content:', 'jet-engine' ); ?>
								<div class="jet-form-editor__row-notice">
									<?php _e( 'Available macros:', 'jet-engine' ); ?>
									<div v-for="field in availableFields">
										- <i>%{{ field }}%</i>
									</div>
									<?php do_action( 'jet-engine/forms/booking/notifications/available_macros_after' ); ?>
								</div>
							</div>
							<div class="jet-form-editor__row-control">
								<textarea v-model="currentItem.email.content"></textarea>
							</div>
							<div class="jet-form-editor__row-control-desc">
								<?php do_action( 'jet-engine/forms/notifications/control_description' ); ?>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'redirect' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Redirect to:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.redirect_type">
									<option value="static_page"><?php _e( 'Static page', 'jet-engine' ) ?></option>
									<option value="custom_url"><?php _e( 'Custom URL', 'jet-engine' ) ?></option>
									<option value="current_page"><?php _e( 'Current page', 'jet-engine' ) ?></option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'redirect' === currentItem.type && 'static_page' === currentItem.redirect_type">
							<div class="jet-form-editor__row-label"><?php _e( 'Select page:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.redirect_page">
									<option v-for="(pageTitle, pageID) in allPages" :value="pageID">{{ pageTitle }}</option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'redirect' === currentItem.type && 'custom_url' === currentItem.redirect_type">
							<div class="jet-form-editor__row-label"><?php _e( 'Redirect URL:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.redirect_url">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'redirect' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Add query arguments to the redirect URL:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div v-for="field in availableFields">
									<label>
										<input type="checkbox" :value="field" v-model="currentItem.redirect_args" style="margin: 4px 0 3px 0;">
										&nbsp;&nbsp;
										{{ field }}
									</label>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'redirect' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Add hash to the redirect URL:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.redirect_hash">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Enter hash string without # symbol', 'jet-engine' );
								?></div>
							</div>
						</div>

						<!--ActiveCampaign-->
                        <div class="jet-form-editor__row" v-if="'activecampaign' === currentItem.type">
                            <div class="jet-form-editor__row-label"><?php _e( 'Use Global Settings:', 'jet-engine' ); ?></div>
                            <div class="jet-form-editor__row-control">
                                <input type="checkbox" v-model="currentItem.activecampaign.use_global">
                            </div>
                        </div>
						<div class="jet-form-editor__row" v-if="'activecampaign' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'API Data:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__input-group">
									<label>
										<?php _e( 'API URL:', 'jet-engine' ); ?><br>
										<input
                                                type="text"
                                                @input="setCurrentVal( 'api_url', $event.target.value )"
                                                :value="getCurrentValOrGlobal( 'api_url' )"
                                                :disabled="isCurrentUseGlobal()"
                                        >
									</label>
									<label>
										<?php _e( 'API Key:', 'jet-engine' ); ?><br>
										<input
                                                type="text"
                                                @input="setCurrentVal( 'api_key', $event.target.value )"
                                                :value="getCurrentValOrGlobal( 'api_key' )"
                                                :disabled="isCurrentUseGlobal()"
                                        >
									</label>
								</div>
								<div class="jet-form-editor__row-notice"><?php
									printf(
										__( 'How to obtain your ActiveCampaign API URL and Key? More info %s.', 'jet-engine' ),
										'<a href="https://help.activecampaign.com/hc/en-us/articles/207317590-Getting-started-with-the-API" target="_blank">' . __( 'here', 'jet-engine' ) . '</a>'
									);
								?></div>
								<button type="button"
									class="button button-default button-large jet-form-validate-button"
									:class="{
										'loading': 'validateActiveCampAPI' === requestProcessing,
										'is-valid': true === currentItem.activecampaign.isValidAPI && 'validateActiveCampAPI' !== requestProcessing,
										'is-invalid': false === currentItem.activecampaign.isValidAPI && 'validateActiveCampAPI' !== requestProcessing
									}"
									v-if="getCurrentValOrGlobal( 'api_url' ) && getCurrentValOrGlobal( 'api_key' )"
									@click="validateActiveCampaignAPI"
								>
									<i class="dashicons"></i>
									<?php _e('Validate API Data', 'jet-engine' ); ?>
								</button>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'activecampaign' === currentItem.type && currentItem.activecampaign.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'List Id:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__input-group">
									<select v-model="currentItem.activecampaign.list_id">
										<option value="">--</option>
										<option v-for="(listName, listId) in currentItem.activecampaign.lists" :value="listId">{{listName}}</option>
									</select>
									<button type="button"
										class="button button-default button-large jet-form-load-button"
										:class="{'loading': 'loadingActiveCampLists' === requestProcessing}"
										@click="getActiveCampaignLists"
									>
										<i class="dashicons dashicons-update"></i>
										<?php _e('Update List', 'jet-engine' ); ?>
									</button>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'activecampaign' === currentItem.type && currentItem.activecampaign.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'Fields Map:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set form fields names to to get user data from', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-editor__row-map" v-for="( acFieldLabel, acField ) in activecampFields">
										<span>{{ acFieldLabel }} <span class="jet-form-editor-required" v-if="'email' === acField">*</span></span>
										<select v-model="currentItem.activecampaign.fields_map[ acField ]">
											<option value="">--</option>
											<option v-for="field in availableFields" :value="field">{{ field }}</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'activecampaign' === currentItem.type && currentItem.activecampaign.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'Tags:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.activecampaign.tags">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Add as many tags as you want, comma separated.', 'jet-engine' );
								?></div>
							</div>
						</div>
						<!--End ActiveCampaign-->

						<!--MailChimp-->
                        <div class="jet-form-editor__row" v-if="'mailchimp' === currentItem.type">
                            <div class="jet-form-editor__row-label"><?php _e( 'Use Global Settings:', 'jet-engine' ); ?></div>
                            <div class="jet-form-editor__row-control">
                                <input type="checkbox" v-model="currentItem.mailchimp.use_global">
                            </div>
                        </div>
						<div class="jet-form-editor__row" v-if="'mailchimp' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'API Key:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__input-group">
									<input
                                            type="text"
                                            @input="setCurrentVal( 'api_key', $event.target.value )"
                                            :value="getCurrentValOrGlobal( 'api_key' )"
                                            :disabled="isCurrentUseGlobal()"
                                    >
									<button type="button"
										class="button button-default button-large jet-form-validate-button"
										:class="{
											'loading': 'validateMailChimpAPI' === requestProcessing,
											'is-valid': true === currentItem.mailchimp.isValidAPI && 'validateMailChimpAPI' !== requestProcessing,
											'is-invalid': false === currentItem.mailchimp.isValidAPI && 'validateMailChimpAPI' !== requestProcessing
										}"
										@click="validateMailChimpAPI"
									>
										<i class="dashicons"></i>
										<?php _e('Validate API Key', 'jet-engine' ); ?>
									</button>
								</div>
								<div class="jet-form-editor__row-notice"><?php
									printf(
										__( 'How to obtain your MailChimp API Key? More info %s.', 'jet-engine' ),
										'<a href="https://mailchimp.com/help/about-api-keys/" target="_blank">' . __( 'here', 'jet-engine' ) . '</a>'
									);
								?></div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'mailchimp' === currentItem.type && currentItem.mailchimp.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'Audience:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__input-group">
									<select v-model="currentItem.mailchimp.list_id"
										@change="currentItem.mailchimp.groups_ids = []"
									>
										<option value="">--</option>
										<option v-for="(listName, listId) in currentItem.mailchimp.data.lists" :value="listId">{{listName}}</option>
									</select>
									<button type="button"
										class="button button-default button-large jet-form-load-button"
										:class="{'loading': 'loadingAMailChimpData' === requestProcessing}"
										@click="getMailChimpData"
									>
										<i class="dashicons dashicons-update"></i>
										<?php _e('Update Audience List', 'jet-engine' ); ?>
									</button>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'mailchimp' === currentItem.type && currentItem.mailchimp.isValidAPI && currentItem.mailchimp.list_id">
							<div class="jet-form-editor__row-label"><?php _e( 'Groups:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<cx-vui-f-select v-model="currentItem.mailchimp.groups_ids"
									:multiple="true"
									:options-list="currentItem.mailchimp.data.groups[ currentItem.mailchimp.list_id ]"
									:prevent-wrap="true"
								></cx-vui-f-select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'mailchimp' === currentItem.type && currentItem.mailchimp.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'Tags:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="text" v-model="currentItem.mailchimp.tags">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Add as many tags as you want, comma separated.', 'jet-engine' );
								?></div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'mailchimp' === currentItem.type && currentItem.mailchimp.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'Double Opt-In:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="checkbox" v-model="currentItem.mailchimp.double_opt_in">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'mailchimp' === currentItem.type && currentItem.mailchimp.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'Fields Map:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set form fields names to to get user data from', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-editor__row-map" v-for="( mcFieldData, mcFieldId ) in currentItem.mailchimp.data.fields[ currentItem.mailchimp.list_id ]">
										<span>{{ mcFieldData.label }} <span class="jet-form-editor-required" v-if="mcFieldData.required">*</span></span>
										<select v-model="currentItem.mailchimp.fields_map[ mcFieldId ]">
											<option value="">--</option>
											<option v-for="field in availableFields" :value="field">{{ field }}</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<!--End MailChimp-->

						<!--GetResponse-->
                        <div class="jet-form-editor__row" v-if="'getresponse' === currentItem.type">
                            <div class="jet-form-editor__row-label"><?php _e( 'Use Global Settings:', 'jet-engine' ); ?></div>
                            <div class="jet-form-editor__row-control">
                                <input type="checkbox" v-model="currentItem.getresponse.use_global">
                            </div>
                        </div>
						<div class="jet-form-editor__row" v-if="'getresponse' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'API Key:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__input-group">
									<input
                                            type="text"
                                            @input="setCurrentVal( 'api_key', $event.target.value )"
                                            :value="getCurrentValOrGlobal( 'api_key' )"
                                            :disabled="isCurrentUseGlobal()"
                                    >
									<button type="button"
										class="button button-default button-large jet-form-validate-button"
										:class="{
											'loading': 'validateGetResponseAPI' === requestProcessing,
											'is-valid': true === currentItem.getresponse.isValidAPI && 'validateGetResponseAPI' !== requestProcessing,
											'is-invalid': false === currentItem.getresponse.isValidAPI && 'validateGetResponseAPI' !== requestProcessing
										}"
										@click="validateGetResponseAPI"
									>
										<i class="dashicons"></i>
										<?php _e('Validate API Key', 'jet-engine' ); ?>
									</button>
								</div>
								<div class="jet-form-editor__row-notice"><?php
									printf(
										__( 'How to obtain your GetResponse API Key? More info %s.', 'jet-engine' ),
										'<a href="https://app.getresponse.com/api" target="_blank">' . __( 'here', 'jet-engine' ) . '</a>'
									);
								?></div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'getresponse' === currentItem.type && currentItem.getresponse.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'List Id:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__input-group">
									<select v-model="currentItem.getresponse.list_id">
										<option value="">--</option>
										<option v-for="(listName, listId) in currentItem.getresponse.data.lists" :value="listId">{{listName}}</option>
									</select>
									<button type="button"
										class="button button-default button-large jet-form-load-button"
										:class="{'loading': 'loadingAGetResponseData' === requestProcessing}"
										@click="getGetResponseData"
									>
										<i class="dashicons dashicons-update"></i>
										<?php _e('Update List', 'jet-engine' ); ?>
									</button>
								</div>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'getresponse' === currentItem.type && currentItem.getresponse.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'Day Of Cycle:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<input type="number" min="0" v-model="currentItem.getresponse.day_of_cycle">
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'getresponse' === currentItem.type && currentItem.getresponse.isValidAPI">
							<div class="jet-form-editor__row-label"><?php _e( 'Fields Map:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set form fields names to to get user data from', 'jet-engine' );
								?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-editor__row-map" v-for="( grFieldData, grFieldId ) in currentItem.getresponse.data.fields">
										<span>{{ grFieldData.label }} <span class="jet-form-editor-required" v-if="grFieldData.required">*</span></span>
										<select v-model="currentItem.getresponse.fields_map[ grFieldId ]">
											<option value="">--</option>
											<option v-for="field in availableFields" :value="field">{{ field }}</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<!--End GetResponse-->

						<!--Update Options-->
						<div class="jet-form-editor__row" v-if="'update_options' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Options Page:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<select type="text" v-model="currentItem.options_page">
									<option v-for="( optionLabel, optionSlug ) in optionsPages" :value="optionSlug">
										{{ optionLabel }}
									</option>
								</select>
							</div>
						</div>
						<div class="jet-form-editor__row" v-if="'update_options' === currentItem.type">
							<div class="jet-form-editor__row-label"><?php _e( 'Options Map:', 'jet-engine' ); ?></div>
							<div class="jet-form-editor__row-control">
								<div class="jet-form-editor__row-notice"><?php
									_e( 'Set options fields to save appropriate form fields into', 'jet-engine' );
									?></div>
								<div class="jet-form-editor__row-fields">
									<div class="jet-form-editor__row-map" v-for="field in availableFields" v-if="'inserted_post_id' !== field">
										<span>{{ field }}</span>
										<input type="text" v-model="currentItem.meta_fields_map[ field ]">
									</div>
								</div>
							</div>
						</div>
						<!--End Update Options-->

						<?php do_action( 'jet-engine/forms/booking/notifications/fields-after' ); ?>
					</div>
					<div class="jet-form-editor__actions">
						<button type="button" class="button button-primary button-large" @click="applyItemChanges"><?php
							_e( 'Apply Changes', 'jet-engine' );
						?></button>
						<button type="button" class="button button-default button-large" @click="cancelItemChanges"><?php
							_e( 'Cancel', 'jet-engine' );
						?></button>
					</div>
				</div>
				</div>
			</slick-item>
		</slick-list>
	</div>
	<div class="jet-form-canvas__actions">
		<span></span>
		<button type="button" class="jet-form-canvas__add" @click="addField()"><?php
			_e( 'Add Notification', 'jet-engine' );
		?></button>
	</div>
	<div class="jet-form-canvas__result">
		<textarea name="_notifications_data">{{ resultJSON }}</textarea>
	</div>
</div>
