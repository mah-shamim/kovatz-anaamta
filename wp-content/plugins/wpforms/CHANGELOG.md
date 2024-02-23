# Change Log
All notable changes to this project will be documented in this file, formatted via [this recommendation](https://keepachangelog.com/).

## [1.5.4.2] - 2019-08-06
### Changed
- Renamed certain actions with typos in their names, backwards-compatible. Added a deprecation text using `do_action_deprecated()`.
- Geolocation API endpoint (used for "smart" phone field).

### Fixed
- About Us page behaviour when WP Mail SMTP Pro is installed.
- Elite licenses could not install addons from inside the form builder.
- Rating field icon color not changing on frontend with some themes.
- reCAPTCHA settings could be saved without providing reCAPTCHA type.
- Entry database tables not created for some users upgrading from WPForms Lite.

## [1.5.4.1] - 2019-07-31
### Fixed
- Plugin Settings > Misc > 'View Email Summary Example' link errors.

## [1.5.4] - 2019-07-30
### Added
- Email Summaries.
- Form builder hotkey to save changes, CTRL + S.

### Changed
- Team photo under WPForms > About Us. :)

### Fixed
- Dynamic field population populates checkbox and radio fields values but not adding 'wpforms-selected' class to its containers.
- Dropdown and Dropdown Items field attributes are now accessible with `wpforms_field_properties` filter.
- Form builder field buttons overflowing when translated.
- Dashboard widget PHP error.
- Form can be submitted multiple times if "Submit button processing text" form setting empty.
- "Error loading block" in Gutenberg if Additional CSS form settings are provided.
- Incorrect payment amount displayed in some cases.

## [1.5.3.1] - 2019-06-18
### Fixed
- Checkbox field validation issue when field is not required.

## [1.5.3] - 2019-06-17
### Added
- AJAX form submissions.
- Google reCAPTCHA v3.

### Changed
- WPForms uninstall script for better cleanup process.
- Email field mailcheck feature to offer additional controls. New filters: `wpforms_mailcheck_enabled`, `wpforms_mailcheck_domains`, and `wpforms_mailcheck_toplevel_domains`.

### Fixed
- File Upload fields issue in Microsoft Edge.
- Special characters aren't encoded when Smart Tags are processed in query string.
- Fields with Image choices are not working with some Android and older desktop browsers.
- Payment Total field value includes conditionally hidden Single item fields.
- Frontend and notification emails incorrect payment amount for some currencies if the value is greater than 1000.
- Conditional Logic: Payment Checkbox Items multiple selection issue.
- Form Builder: Several alert modals are displayed in batch if multiple providers have configuration issues.
- WP_Post object is returned from `wpforms()->form->get()` if form data is requested with a non-WPForms post ID.
- Inconsistent Enter key behaviour in multi-page forms.
- Unable to get a specific entry with `wpforms()->entry->get_entries()` without giving the form id.

## [1.5.2.3] - 2019-04-23
### Fixed
- PHP error if checkbox field is empty when form is submitted.
- Validate all :input fields (not only required) when navigating multi-page forms.
- Conditional logic conflicts using checkboxes/dropdowns with options "false" or "0".
- Use of JavaScript Array Prototype Constructor breaks conditional logic.

## [1.5.2.2] - 2019-04-15
### Fixed
- PHP notice/warnings from undefined constant (typo).
- Addons screen not populating for all license levels.

## [1.5.2.1] - 2019-04-11
### Fixed
- Entry print preview page not supporting non-UTF8 charsets.
- Entry print preview page not displaying entry notes.
- Required Checkbox fields asking for all inputs to be checked to pass validation.

## [1.5.2] - 2019-04-10
### Added
- Smart format for Phone fields.
- Choice Limit advanced option for Checkbox fields.
- Smart domain name typo detection for Email fields.
- New Gutenberg block keywords to help with discovery.
- Link to "How to Properly Test Your WordPress Forms Before Launching" doc inside Gutenberg block.
- Filter `wpforms_upload_root` to change uploads location.

### Changed
- Form builder field delete icon, now a trash can.
- Removed legacy check for conditional logic.
- Improved Entries list table on small devices.
- User IP detection method, now filterable.
- Updated flatpickr JS library to v4.5.5.
- Updated jQuery inputmask library to v4.0.6.
- Updated jQuery validation plugin to v1.19.0.
- Clear Dashboard widget cache when form is created/deleted/updated.

### Fixed
- Blank form if using form template containing `target="_blank"`.
- Honeypot field not using unique IDs.
- Duplicating forms creating another duplicate if afterwards the table was sorted.
- Minor issues with Gutenberg editor.
- Browser autocomplete conflict with US address zipcode input mask.
- Form Builder embed modal showing Classic Editor instructions for Gutenberg users.
- No detection or errors if combined multiple file uploads size is greater than `post_max_size`.
- Number field allowing non-numerical characters on iOS devices.
- Incorrect data in CSV entry exports if fields have been deleted.
- Field Dynamic Choices not showing in form preview when using "Post Type".

## [1.5.1.3] - 2019-03-14
### Fixed
- Styling issue with single entry previous/next buttons.
- Importing forms that containing `target="_blank"`.
- Issues with duplicating Form Notifications and conditional logic rules inside Form Notifications.
- Quote support/display inside query param Smart Tags.
- Addon cache not clearing when license key is switched or deactivated.
- Other minor fixes.

## [1.5.1.2] - 2019-02-28
### Fixed
- Conditional logic issue with Checkbox/Multiple choice fields when default values are set.

## [1.5.1.1] - 2019-02-26
### Fixed
- Conflict with WordPress 5.1 if form contained target="_blank".
- Long field labels cut off when viewed in Entry Print page compact view.
- PHP notices on Entry Print page.
- PHP notices on Entries page.
- Unable to uncheck default Multiple Choice value in form builder after being set initially.
- PHP error when entries are exported after a field has been deleted.
- Form builder Email notification conditional logic settings display issue after new notification is added.
- Conflict with some themes preventing Multiple Choice fields from being selectable.

## [1.5.1] - 2019-02-06
### Added
- Checkbox Items field (payment checkboxes).
- Complete translations for Spanish, Italian, Japanese, and German.
- Improved form builder education and workflows: install and activate any addon without ever leaving the form builder!
- Smart Tag for referencing user meta data, `{user_meta key=""}`.

### Changed
- Removed limit on Entry Columns when customizing.
- Improved support with LocoTranslate plugin.
- Refactored Form Preview functionality, no longer requiring hidden private page to be created.
- Always load full WPForms styling inside Gutenberg so forms render correctly.

### Fixed
- Entry counts getting off sync with entry heartbeat detection.
- Typos, grammar, and other i18n related issues.
- Created alias class for `WPForms` to prevent issue with namespacing introduced in 1.5.0.
- Dynamic population issue when using Image Choices field.

## [1.5.0.4] - 2018-12-20
### Changed
- Dashboard widget improvements.

### Fixed
- Various typos.

## [1.5.0.3] - 2018-12-06
### Changed
- Minor improvements to Gutenberg block for WordPress 5.0.

### Fixed
- Error when activating WPForms Pro if WPForms Lite is still activated.

## [1.5.0.2] - 2018-12-03
### Fixed
- File Upload validation issue if max file size was defined.
- Dashboard widget appearance on Windows.

## [1.5.0.1] - 2018-11-28
### Fixed
- Required validation enforcement on Date Time fields.

## [1.5.0] - 2018-11-28
### IMPORTANT
- Support for PHP 5.2 has been discontinued. If you are running PHP 5.2, you MUST upgrade PHP before installing WPForms 1.5. Failure to do that will disable WPForms core functionality.

### Added
- Dashboard widget with basic reporting.
- WPForms Challenge: an interactive step-by-step guide to creating a form for new users.
- Dynamic field population, available to enable from form settings.
- New entries "heartbeat" notification on entries list screen.
- "About Us" admin page (WPForms > About Us).
- {user_first_name} and {user_last_name} Smart Tags.

### Changed
- Improved randomizing if field is configured to randomize items.
- Improved file size validations with multiple uploads.
- Improved i18n support.

### Fixed
- Form builder errors if user had Visual Editor disabled in profile.
- Form builder Windows styling issues.
- Form builder dynamic choices warning not always removing.
- Form builder "Show Layout" CSS formatting.
- reCAPTCHA compatibility when form is inside OptinMonster popup.
- PHP errors if form does not contain entries.
- Validation and formatting issues on some fields if submitted value is zero.
- File upload javascript validation conflicting with multi-page forms.
- Gutenberg block returning error if no forms have been created.

## [1.4.9] - 2018-09-18
### Added
- Pirate Forms importer.

### Changed
- Some form builder tooltips to contain documentation links.

### Fixed
- Form builder javascript conflict with jQuery non-conflict mode.
- RTL issue with Phone field when using input masks.
- PHP Notice from WPForms widget.
- Incorrect markup around Addons submenu item.

## [1.4.8.1] - 2018-08-21
### Fixed
- Certain confirmation settings, before 1.4.8, not displaying correctly in the form builder.
- Compatibility issue with MySQL `Strict_Trans_Tables` mode (again).

## [1.4.8] - 2018-08-28
### Added
- Gutenberg block.
- Conditional form confirmations - forms can now have multiple confirmations with conditional logic!
- WP Mail SMTP detection and hints in the form builder notification settings.
- Alt and title tags to image choices images on frontend display.

### Changed
- Improved Website URL field frontend validation - now automatically adds protocol if omitted.
- i18n improvements.

### Fixed
- Compatiblity issue with MySQL `Strict_Trans_Tables` mode.
- Incorrect param used with `shortcode_atts`.
- NPS and Rating fields not having access to all conditional logic comparisons.
- Accessing `wpforms_setting` in frontend javascript before checking if it exists.
- Escaping method in HTML field mangling code on save.
- PHP error toggling form builder notifications in some use cases.
- GDPR field Agreement text not updating in real time.
- Marketing provider connections containing an escaped apostrophe. 
- Pressing "Enter" in the form builder resulting in unexpected behavior.
- Incorrect pagination when searching entries.
- Security enhancements and other misc. bug fixes.

## [1.4.7.2] - 2018-06-21
### Changed
- Adding new choice to Multiple Items field now defaults price to $0.

### Fixed
- Entry ID always displaying 0 when viewing single entry details.
- Honeypot field using a none unique CSS ID.
- Form builder Bulk Add display issues in certain use cases.
- Checkbox field values not saving if Show Values field option is enabled.
- Date Time field date dropdown placeholder text not accessible.

## [1.4.7.1] - 2018-06-07
### Added
- Greater Than and Less Than conditional logic rules.
- Conditional logic support for Net Promoter Score field (Surveys and Polls addon v1.1.0).

### Changed
- Updated Russian translation.

### Fixed
- Various i18n issues.

## [1.4.7] - 2018-06-04
### Added
- New Providers class and functionality. The Drip addon is the first to leverage the new class and existing provider addons will be updated over time.

### Changed
- CSV export columns are now filterable (`wpforms_export_get_csv_cols`).
- Old PHP version (5.2 and 5.3) admin warning adjusted to reflect new August 2018 time line.

### Fixed
- Multiple Choice fields showing as Radio fields in the builder preview when first created.
- Duplicating fields in the form builder causing issues with certain field types.
- Entry ID becomes 0 when resending notifications.
- Escaping issue with provider connection names contained an apostrophe.
- Alignment issues with the Addons page display.
- Incorrect text on the Welcome activation page.

## [1.4.6] - 2018-05-14
### Added
- GDPR Enhancements plugin setting [doc](https://wpforms.com/how-to-create-gdpr-compliant-forms/).
- GDPR Enhancement: Disable User Cookies plugin setting.
- GDPR Enhancement: Disable User Details (IP and User Agent) plugin setting.
- GDPR Enhancement: Disable Storing User Details form setting.
- GDPR Enhancement: User Agreement form field.
- Page break, section divider, and HTML fields can now be enabled in email notifications with a filter [doc](https://developers.wpforms.com/docs/filter-reference-wpforms_email_display_other_fields).

### Changed
- Hide credit card field unless enabled by a payment addon or with a filter [doc](https://developers.wpforms.com/docs/enable-credit-card-field-without-stripe-addon/).
- PHP warning that alerts users support for PHP 5.4 and below will be dropped this summer.
- Spam logging, to improve performance.

### Fixed
- Rating and Likert Scale not included in CSV exports.
- Typo in base form CSS.
- Stripping HTML from the checkbox, mulitple choice, and multiple payment choice labels in the form builder.
- Unreadable errors if 1-click addon install fails.
- Date and Time field time interval labels not translatable.
- Form builder icon visibility when field labels are hidden.

## [1.4.5.3] - 2018-04-03
### Changed
- Use minified admin assets when appropriate.
- Show helpful doc link in form embed modal.
- Minor improvements with complex conditional logic rule processing.

### Fixed
- Rating and Likert fields missing from CSV exports.
- reCAPTCHA v2 showing in form builder when using Invisible reCAPTCHA.
- Conditional logic rules inception.
- Conditional logic rules with Radio and Checkbox choices not updating until save.
- Remove jQuery shorthand references in `admin-utils` to prevent conflicts.
- Issue with form return hash not processing correctly in some scenarios.

## [1.4.5.2] - 2018-03-20
### Fixed
- Checkbox and Multiple choice fields not validating when inside pagebreaks.
- Incorrect documentation link for Input Mask.
- Input Mask value disappearing when form builder is refreshed.

## [1.4.5.1] - 2018-03-20
### Fixed
- Dynamic choices not displaying correctly for Multiple Choice and Checkbox fields.

## [1.4.5] - 2018-03-15
### Added
- Image choices feature with Checkbox, Multiple Choice and Multiple Payments fields; Images can now be uploaded and displayed with your choices!
- Custom input masks for Single Line Text fields (Advanced Options).
- No-Conflict Mode for Google reCAPTCHA (Settings > reCAPTCHA). Removes other reCAPTCHA occurrences, to prevent conflicts.
- SSL Connection Test (Tools > System Info). Quicky verify that your web host correct supports SSL connections.
- `{user_full_name}` Smart Tag, displays users first and last name.
- Discalimer / Terms of Service Display formatting option for Checkbox fields (Advanced Options).
- Basic CSS styling for `disabled` fields.
- Uninstall routine, available from Settings > Misc.
- Form builder performance improvements. Editing a form with hundreds of fields is now 500%+ faster!
- Search field on Addons page to quickly search available Addons.

### Changed
- New Settings tab: Misc, moved Hide Annoucements option to new tab.
- "Total" entries column only displays if the form has a gateway configured and enabled.
- `{user_display}` Smart Tag diplays user's display name (in most cases, this is the user's name).
- All `<form>` attributes can now be changed via `wpforms_frontend_form_atts` filter.

### Fixed
- Processing and validation of return hashes (primarily used with PayPal Standard addon).
- Smart Tag usage in confirmation messages displayed from return hashes (primarily used with PayPal Standard addon).
- Form builder tab icon alignment conflicts with third party plugin CSS.
- Smart Tag dropdown display issues in the form builder.
- Form builder drag and drop area disappearing if all fields are removed from a form.

## [1.4.4.1] - 2018-02-13
### Changed
- Textdomain loading to a later priority.
- Provide entry ID if logging entries to improve performance.
- Allow the `WPForms_Builder` class to be accessible.
- Move the confirmation message `wpautop` to an earlier priority to not conflict with content added using filters.

### Fixed
- Form builder templates area not aligning correctly in some browsers.
- Payment transaction IDs not displaying on entry details page.
- Incorrect permissions check for annoucements feed.

## [1.4.4] - 2018-01-30
### Added
- Form entries searching; search by specific field or across all fields, multiple conditionals available (is, is not, contains, does not contain).
- Form entries filtering by date; e.g. show form entries from Dec 1 - Dec 31 2017.
- Rating field.
- Advanced setting for Multiple Choice and Checkbox fields to randomize choices.
- Filter for Date Time date dropdown select inputs, to customize ranges (`wpforms_datetime_date_dropdowns`).

### Changed
- Lists (both ordered and unordered) used in the HTML field now have basic styling if using full form theme setting.
- Admin menu icons now uses SVG instead of custom font icon.
- Reviewed all translatable strings, improved escaping and formatting .
- External links have `rel="noopener noreferrer"` improve security.
- Permission check centralized into a single function (`wpforms_current_user_can()`).
- Required label field text centralized into a single function (`wpforms_get_required_label()`).
- Improved list of Countries.

### Fixed
- Conditional logic mismatches due to sanitizing values.
- Typo in German translation.
- Improved i18n for countries.
- Required email provider connection fields not highlighting when left empty.
- Inside form builder, notification name area breaking into multiple lines on smaller screens.
- Total field not updating correctly when multiple forms are on the same page.

## [1.4.3] - 2017-12-04
### Added
- Form entry field values are now stored (additionally) in a new database, `wpforms_entry_fields`, to be used with exciting new features in the near future.
- Upgrade routine for the above mentioned new database.
- Early filter for form data before form output, `wpforms_frontend_form_data`.
- Setting to hide Announcement feed.
- Announcement feed data.

### Changed
- Standardize and tweak modal window button styles.
- Default mail notification settings are now sent "from" the site administrator email; user email is used in Reply-To where applicable (to hopefully improve email deliverability).
- Removed "Hide form name and description" form setting as it was a common source or confusion.
- Provide base styling for `hr` elements inside HTML fields.

### Fixed
- Site cache being flushed when it shouldn't have been, affecting performance in some scenarios.
- Country, state, months and days not properly exposed to i18n.
- CSV export dates not properly using i18n.
- Incorrect usage of `esc_sql` with `wpdb->prepare`.
- Styling preventing the entries column picker from displaying correctly.
- WPForms custom post types omitting labels.
- Smart Tag value encoding issues with email notifications.
- Infinite recursion issue when using Dynamic Values option.
- PHP notice in form builder.

### Changed

## [1.4.2] - 2017-10-25
### Added
- Import your old Ninja Forms or Contact Form 7 forms! (WPForms > Tools > Import).

### Changed
- Date i18n improvements
- Dropdown/Checkbox/Multiple Choice "Show Values" setting has been hidden by default to avoid confusion, can be re-enabled using the `wpforms_fields_show_options_setting` filter.
- Date Time field inputs break into separate lines on mobile to prevent Date picker from going off screen in some scenarioes.

### Fixed
- reCAPTCHA now showing in the Form Builder preview when enabled.
- Encoded/escaped entities in email notifications.
- German translation issue.

## [1.4.1.2] - 2017-10-03
### Fixed
- New CSV separator filter introduced 1.4.1 not correctly running.

## [1.4.1.1] - 2017-09-29
### Changed
- Improved the loading order of javascript files for forms builder.
- Update some strings for Russian translation.

### Fixed
- Entries export functionality was broken.
- Multipage indicators behavior when several multipage forms present on the same page.

## [1.4.1] - 2017-09-28
### Added
- Ability to rename Form >Settings>Notifications>Single notification panels.
- Define a minimum PHP version support in plugin readme.txt file.
- Display a friendly link to a full page version, when form is previewed on AMP pages.
- Ability to collapse Form>Settings>Notifications>Single notification panels.
- Russian translation.
- Allow more than 1 default selection for checkboxes fields.
- Announcement feed.

### Changed
- Bump minimum WordPress version to 4.6.
- Improved localization support of the plugin.
- Improved texts in various places.
- Code style improvements throughout the plugin.
- Combine WPFORMS_DEBUG and WPFORMS_DEVELOPMENT into one, use `wpforms_debug()` to check.
- All HTTP requests now validate target sites SSL certificates with WP bundled certificates (since 3.7).

### Fixed
- Payments and providers classes version visibility.
- Postal field (part of Address field) now supports the {query_var} smart tag.
- Form's Entries page unread/read and starred/unstarred counters.
- Incomplete selection of Date dropdown fields causes entries to be recorded as 'Array'.
- Notification email is empty if submitted form has no user values (displaying user friendly message instead).
- Pressing enter in "Enter a notification name" popup does nothing.
- Removed Screen Options on single entry screen.
- Allow postal code to be hidden/removed, fix Country issues.
- Country names don't have redundant `)` or spaces anymore.
- Do not display 2400 option in TimePicker in Date / Time field for 24h format.
- Deprecate a misspelled `wpforms_csv_export_seperator` filter, introduced a proper name for it.
- Conditional logic comparison issues if rule contained special characters.

## [1.4.0.1] - 2017-08-24
### Added
- Non-dismissible Dashboard page admin only notice about PHP 5.2.

### Changed
- Updated FontAwesome library.

### Fixed
- Fatal error with PHP 5.2 due to an anonymous function.
- Required Credit Card fields incorrectly passing JS validation if empty.
- CSV exports missing line breaks.
- Entries dropdown menu being cut off under the WordPress menu.

## [1.4.0] - 2017-08-21
### Added
- Entries table columns can now be customized; personalize what fields you want to see!
- All entries can be deleted for a form from the Entries page.
- Announcement feed.

### Changed
- Phone number field switched to `tel` input for improved mobile experience.
- Core form templates are now displayed separate in the form builder from other custom templates.
- Refactored CSV exporting for better support.

### Fixed
- Dynamic Choices large items modal render issue.
- Certain characters (such as comma) breaking CSV export format.
- Cursor issues inside the form builder.
- CSS Layout Generator class name typo.
- Dynamic choices with nesting sometimes causing form builder to time out.
- Settings page typos.
- Deleting a form in some cases did not remove entry meta for its entries.
- File Uploads stored in the media library not storing the correct URL when offloaded to other services such as S3.
- Tools page export description text typo.
- Widget state not displayed correctly when adding via Customizer, without forcing user to select a form.

## [1.3.9.2] - 2017-08-03
### Fixed
- Currency setting for new users saving to an incorrect option key.

## [1.3.9.1] - 2017-08-02
### Changed
- Template Export excludes array items with empty strings.

### Fixed
- Admin notices displaying on plugin Welcome/activation screen.
- WPForms admin pages displaying blank due to conflicts with a few other plugins.
- License related notices not removed immediately after key is activated.
- Addons page items not displaying with uniform height.
- Addons page installing returned JS object instead of message.

## [1.3.9] - 2017-08-01
### Added
- Complete redesign and refactor of admin area.
- New Settings API.
- Entry print preview compact mode.
- Entry print preview view entry notes.
- Dynamic field choices nest hierarchical items.

### Changed
- Moved Import/Export and System Info content to new Tools sub-page.
- Shortcode provided in form builder now includes title/description arguments.
- Don't show CSS layout selector helper in Pagebreak fields.

### Fixed
- Form builder URL redirect issue on the Marketing tab with some configurations.
- Password field item mislabeled.
- PHP notices on Entries page if form contained no fields.
- PHP notices when using HTML field with conditional logic.

## [1.3.8] - 2017-06-13
### Added
- Conditional logic functionality is now in the core plugin - the Conditional Logic addon can be removed.
- New conditional logic rules: empty and not empty.
- Conditional logic can now be applied to fields that are marked as required.

### Changed
- Available conditional logic rules/functionality with Providers have been updated.
- Updated form builder modals (jquery-confirm.js).
- Many Form Builder performance enhancements.

### Fixed
- Allowing Storing entries form setting to be enabled when form is connected to payments.
- Number field validation message not saving.
- Email/Password confirmation setting not displaying correctly with Small field size.

## [1.3.7.3] - 2017-05-12
### Fixed
- Required setting checkbox getting out of sync when duplicating fields.
- CSS class name typo in the form builder layout selector.
- Excel mangling non-english characters when opening CSV export files.
- Smart Tag `field_id` stripping line breaks.
- Multiple Items field choices not updating correctly in form builder preview.
- Form JS settings `wpforms_settings` missing due to some caching plugins.
- Empty classes causing `array` string to be printed in some use cases.

### Changed
- Updated credit card, page break, password, and phone fields to improved field class.

## [1.3.7.2] - 2017-04-26
### Fixed
- PHP warning when displaying page break indicator at the top of a form.
- Error for some users with PHP 5.4 and below.

## [1.3.7.1] - 2017-04-26
### Fixed
- Issue sending form notifications using email fields that had confirmation enabled.

## [1.3.7] - 2017-04-26
### Added
- Google Invisible reCAPTCHA support.
- Custom field validation messages (see WPForms Settings page).
- Bulk add choices for Checkbox, Multiple Choice, and Dropdown fields.
- Filter to allow email notifications to include empty fields, `wpforms_email_display_empty_fields`.
- Custom form template exporting.
- Field CSS layout selector.
- Total payment fields can now be marked as required, preventing the field from submitting unless it contains a payment.

### Changed
- HTML fields now allow and run WordPress shortcodes.
- Leverage `wp_json_encode` instead of native PHP function.
- Various WordPress coding standard improvements (work in progress).
- Refactored form front-end code to allow for more customizations.
- Refactored text, textarea, email, number, name, divider, file upload, hidden, html, payment total, and URL fields to allow for more customizations (more coming next release).

### Fixed
- Welcome page typo.
- Address field options getting off sync inside form builder.
- Bug adding new notifications and element IDs not updating.
- Page indicator (navigation) overflowing in some use cases.
- SmartTag selectors getting off sync inside form builder.
- File upload routine using `pathinfo` which is not reliable with some locales.

## [1.3.6] - 2017-03-09
### Added
- Constant Contact integration.

### Changed
- Don't strip tags from plain text emails.

### Fixed
- Address field variable name typo.
- Form builder javascript conflict with Clef plugin.
- Form builder logo URL double slash.
- Form builder embed code field not being selectable.

## [1.3.5] - 2017-02-22
### Fixed
- Some browers allowing unexpected characters inside number input fields.
- Error when resending email notifications through Single Entry page.
- Issue with Dropdown field placeholder text.
- Select few plugins loading conflicting scripts in form builder.

## [1.3.4] - 2017-02-09
### Added
- reCAPTCHA improvements; reCAPTCHA now required if turned on.

### Fixed
- Date/Time Smart Tag not using WordPress time zone settings.
- Name field defaults not processing Smart Tags.

## [1.3.3] - 2017-02-01
### Added
- Default value support in the email field.
- Related Entries metabox on single entry page.
- Various new hooks and filters for improved extendibility.

### Changed
- Payment status is now displayed in status column, indicated with money icon.
- Multi-page scroll can be customized via JS overrides, `wpform_pageScroll`.

### Fixed
- Possible errors if web host had `set_time_limit()` disabled.
- File upload failing in edge cases due to library not being loaded.
- PHP 7.1 warning message inside the form builder when using payments.

## [1.3.2] - 2017-01-17
### Added
- CSS class support for hidden fields, for easier targeting.
- New form class, `.inline-fields`, to apply single line form layout.
- Allow date and time pickers properties to be specified on a per form/field basis.

### Changed
- All Smart Tags now available for Email Subject field in form notifications.
- License checks rely on options, instead of transients, for more reliability.
- Enable date picker on mobile devices.

### Fixed
- Email addresses reporting as invalid of the domain contained capitalization.
- Error uploading MP3 files when File upload was using the media library.
- Author related Smart Tags not working in form notification fields.
- Typo on settings page related to Carbon Copy.
- Incorrect messaging/layout on plugins addon page for Basic license users.
- Date Time field date picker causing validation issues for mobile users.
- PHP 7.1 warning messages inside the form builder.

## [1.3.1.2] - 2016-12-12
### Fixed
- Plugin name to correctly indicate Lite for Lite release.

## [1.3.1.1] - 2016-12-12
### Fixed
- Error with 1.3.1 Lite release.

## [1.3.1] - 2016-12-08
### Added
- Dropdown Items payment field.
- Smart Tags for author ID, email, and name.
- Carbon Copy (CC) support for form notifications; enable in WPForms Settings.

### Changed
- Form data and fields publicly accessible in email class.

### Fixed
- Field duplication issues
- Total payment field error when only using Multiple Items payment field.
- TinyMCE "Add Form" button not opening modal with dynamic TinyMCE instances.
- Email formatting issues when using plain text formatting.
- Number field validation tripping when number submitted is zero.
- reCAPTCHA validation passing when reCAPTCHA left blank.
- Dropdown field size not reflecting in builder.
- File Upload field offering Size option but not supported (option removed).
- File uploads configured to go to the media library not working.
- Server-side file upload errors not displaying correct due to a type.

## [1.3.0.1] - 2016-11-10
### Added
- Context usage param to `wpforms_html_field_value` filter.
- New filter, `wpforms_plaintext_field_value`, for plaintext email values.

### Fixed
- Bug with date picker limiting date selection to current year.
- PHP notice when uploading non-media library files.
- Issue with form title/description being toggled with shortcode.
- Secured `target=_blank` usage.

## [1.3.0] - 2016-10-24
### Added
- Email field confirmantion.
- Password field confirmation.
- Support for Visual Composer.
- Additional date field type, dropdowns.
- Field class to force elements to full-width on mobile devices, `wpforms-mobile-full`.

### Changed
- Datepicker library.
- Timepicker library.
- Placeholders are added/updated in real-time for Dropdown fields in the form builder.
- Add empty value to select element placeholders when displaying form for better markup validation.

### Fixed
- Multiple instances of reCAPTCHA on a page not correctly loading.
- Field choice defaults not restoring in form builder.
- Field alignment issues in the form builder when dragging field more than once.
- PHP fatal erroring if form notification email address provided is not valid upon sending.
- Date field Datepicker allows empty submit when marked as required.
- Compatibility issuses when network activated on a Multisite install.

## [1.2.9.1] - 2016-10-07
### Fixed
- Compatibility issue with Stripe addon.

## [1.2.9] - 2016-10-04
### Added
- Individual fields can be duplicated in the form builder.

### Changed
- How data is stored for fields using Dynanic Choices.
- File Upload contents can (optionally) be stored in the WordPress media library.

### Fixed
- CSV exports not handling new lines well.
- Global assets setting causing errors in some cases.
- Writing setting ("correct invalidly nested XHTML") breaking forms containing HTML.
- Forms being displayed/included on the native WordPress Export page.
- Dynamic Choices erroring when used with Post Types.
- Form labels including blank IDs.

## [1.2.8.1] - 2016-09-19
### Fixed
- Form javascript email validation being too strict (introducted in 1.2.8).
- Provider sub-group IDs not correctly stored with connection information.

## [1.2.8] - 2016-09-15
### Added
- Dynamic choice feature for Dropdown, Multiple Choice, and Checkbox fields.

### Changed
- Loading order of templates and field classes - moved to `init`.
- Form javascript email validation requires domain TLD to pass.
- File Upload file size setting now allows non-whole numbers, eg 0.5.

### Fixed
- HTML email notification templates uses site locale text-direction.
- Javascript in the form builder conflicting with certain locales.
- Datepicker overflowing off screen on small devices.

## [1.2.7] - 2016-08-31
### Added
- Store intial plugin activation date.
- Input mask for US zip code within Address field, supports both 5 and 9 digit formats.
- Duplicate form submit protection.

### Changed
- Entry dates includes GMT offset defined in WordPress settings.
- Entry export now includes both local and GMT dates.
- Improved Address field to allow for new schemes/formats to be create and better customizations.

### Fixed
- Provider conditonal logic processing when using checkbox field.
- Strip slashes from entry data before processing.
- Single Item field price not live updating inside form builder.

## [1.2.6] - 2016-08-24
### Added
- Expanded support for additional currencies.
- Display payment status and total column on entry list screen as allow sorting with these new columns.
- Display payment details on single entry screen.
- Miscellaneous internal improvements.

### Changed
- Added month/year selector to date picker for better accessibility.
- Payment validation methods.

### Fixed
- Incorrectly named variables in the front-end javascript preventing features from properly being extendable.

## [1.2.5] - 2016-08-03
### Added
- Setting for Email template background color.
- Form setting for form wrapper CSS class.

### Changed
- Multiple Payment field stores Choice label text.
- reCAPTCHA tweaks and added filter.
- Improved IP detection.

### Fixed
- Mapped select fields in builder triggering JS error.

## [1.2.4] - 2016-07-07
### Added
- Form import and exporting.
- Additional logging and error reporting.

### Changed
- Footer asset detection priority, for improved capatibility with other services.
- Refactored and refined front-end javascript.

### Fixed
- Restored form notification defaults for Blank template.
- Default field validation considered 0 value as empty.
- Rogue PHP notices.

## [1.2.3] - 2016-06-23
### Added
- Multiple form notifications capability.
- Form notification message setting.
- Form notification conditional logic (via add-on).
- Additional Smart Tags available inside Form Settings panels.
- Process Smart Tags inside form confirmation messages and URLs.
- Hide WPForms Preview page from WordPress dashboard.
- System Details tab to WPForms Settings, to display debug information, etc.

### Changed
- Center align text inside page break navigation buttons.
- Scroll to top most validation error when using form pagination.
- Many form builder javascript improvements.
- Improved internal logging and debugging tools.
- Don't show Page Break fields in Entry Tables.

### Fixed
- Form select inside modal window overflowing when a form exists with a long title.
- Large forms not always saving because of max_input_vars PHP setting.
- Entry Read/Unread count incorrect after AJAX toggle.
- Single Payment field failed validation if configured for user input and amount contained a comma.

## [1.2.2.1] - 2016-06-13
### Fixed
- Entry ID not always correctly passing to hooks.

## [1.2.2] - 2016-06-03
### Added
- Page Break navigation buttons now have an alignment setting.
- Page Break previous navigation button is togglable and defaults to off.

### Changed
- Improved styling of Page Break fields in the builder.
- Choice Layouts now use flexbox instead of CSS columns for better rendering.

### Fixed
- Class name typo in a CSS column class introduced with 1.2.1.
- PHP notice on Entries page when there are no forms.

## [1.2.1] - 2016-05-30
### Added
- Drag and drop field buttons - simply drag the desired field to the form!
- Page Break progress indicator themes, with optional page titles.
- Choice Layout option for Checkboxes and Multiple Choice fields (under Advanced Options).
- Full and expanded column class/grid support.

### Changed
- Refactored Page Break field, fully backwards compatible with previous version.
- Page Break navigation buttons with without a label do not display.
- Refactored CSS column classes, previous classes are deprecated.
- Improved field and column gutter consistency.

### Fixed
- Form ending with column classes not closing correctly.
- reCAPTCHA button overlaying submit button preventing it from being clicked.

## [1.2] - 2016-05-19
### Added
- Column classes for Checkbox and Multiple choice inputs.

### Changed
- Improved file upload text format inside entry tables.

### Fixed
- Removed nonce verification.
- Issue with Address fields not processing correctly when using international format.

## [1.1.9.1] - 2016-05-06
### Fixed
- Payment calculations incorrect with large values.

## [1.1.9] - 2016-05-06
### Added
- Form preview.
- Other small misc. updates.

### Changed
- reCAPTCHA settings description to include link to how-to article.
- Some fields did not have the correct (unique) CSS ID, this has been corrected, which means custom styling may need to be adjusted.
- Form notification settings hide if set to Off.

### Fixed
- Issue with submit button position when form ends with columns classes.
- PHP warnings inside the form builder.

## [1.1.8] - 2016-04-29
### Added
- "WPForm" to new-content admin bar menu item.

### Changed
- Removed "New" field name prefix.
- Moved email related settings into email settings group.

### Fixed
- Incorrect i18n strings.
- Load order causing add-on update conflicts.

## [1.1.7] - 2016-04-26
### Added
- Smart Tag for Dropdown/Multiple choice raw values, allowing for conditional email addres notifications ([link].(https://wpforms.com/docs/how-to-create-conditional-form-notifications-in-wpforms/)).
- HTML/Code field Conditional Logic support.
- HTML/Code field CSS class support.
- Three column CSS field classes ([link](https://wpforms.com/docs/how-to-create-multi-column-form-layouts-in-wpforms/)).
- Support for WordPress Zero Spam plugin (https://wordpress.org/plugins/zero-spam/).

### Changed
- Checkbox/Multiple Choice fields allow certain HTML to display in choice labels.

### Fixed
- Issue when stacking fields with 2 column classes.

## [1.1.6] - 2016-04-22
### Added
- Entry starring.
- Entry read/unread tracking.
- Entry filtering by stars/read state.
- Entry notes.
- Entry exports (csv) for all entries in a form.

### Changed
- Improved entries table overview page.
- Email Header Image setting description to include recommended sizing.

### Fixed
- reCAPTCHA cutting off with full form theme.
- Debug output from wpforms.js.
- Conflict between confirmation action and filter.

## [1.1.5] - 2016-04-15
### Added
- Print entry for single entries.
- Export (CSV) for single entries.
- Resend notifications for single entries.
- Store user ID, IP address, and user agent for entries.

### Changed
- Improved single entry page (more improvements soon!).
- HTML Email template footer text appearance.

### Fixed
- Form builder textareas not displaying full width.
- HTML emails not displaying correctly in Thunderbird.

## [1.1.4] - 2016-04-12
### Added
- Form general setting for "Submit Button CSS Class".
- Duplicate forms from the Forms Overview page (All Forms).
- Suggestion form template.

### Changed
- Improved error logging for providers, now writes to CPT error log.
- Adjusted field display inside the Form Builder to better resemble full theme.

### Fixed
- Firefox CSS issue in form base theme.
- Don't allow inserting shortcode via modal if there are no forms.
- Issue limiting Total field display amount.

## [1.1.3] - 2016-04-06
### Added
- New class that handles sending/processing emails.
- Form notification setting for "From Address", defaults to site administrator's email address.
- HTML email template for sleek emails (enabled by default, see more below).
- General setting to configure email notification format.
- General setting to optionally configure email notification header image.

### Changed
- Default email notification format is now HTML, can go back to plain text format via option on WPForms > Settings page.
- File Upload field now saves original file name.
- Empty fields are no longer included in email notifications.

### Fixed
- Various issues with File Upload field in different configurations.
- Address field saving select values when empty.
- Issue with Checkbox field when empty.

## [1.1.2] - 2016-04-01
### Added
- Form option to scroll page to form after submit, defaults on for new forms.

### Changed
- Revamped "Full" form theme to be more consistent across different themes, browsers, and devices.
- Full theme and bare theme separated.

### Fixed
- File upload required message when not set to required.

## [1.1.1] - 2016-03-29
### Fixed
- Settings page typo
- Providers issue causing AJAX to fail.

## [1.1] - 2016-03-28
### Added
- Credit Card payment field.

### Changed
- CSS updates to improve compatibility.

### Fixed
- PHP notices when saving plugin Settings.

## [1.0.9] - 2016-03-25
### Changed
- Email field defaulting to Required.

## [1.0.8] - 2016-03-24
### Fixed
- Name field setting always showing Required.
- Debug function incorrectly requiring WP_DEBUG.

## [1.0.7] - 2016-03-22
### Changed
- CSS tweaks.

### Fixed
- Issue with File Upload field returning incorrect file URL.
- Filter (wpforms_manage_cap) incorrectly named in some instances.

## [1.0.6] - 2016-03-21
### Added
- Embed button inside the Form Builder.
- Basic two column CSS class support.
- French translation.

### Changed
- Form names are no longer required, if no form name is provided the template name is used.
- Inputmask script, for better broad device support.
- Field specific assets are now conditionally loaded.
- CSS tweaks for form display.

### Fixed
- Issue with Date/Time field.
- Issue Address field preventing Country select from hiding in some configurations.
- Localization string errors.

## [1.0.5] - 2016-03-18
### Added
- Pagination for Entries table.

### Changed
- Checkboxes/Dropdown/Multiple Choice fields always show choice label value in e-mail notifications.

### Fixed
- PHP notices inside the Form Builder.
- Typo inside Form Builder tooltip.

## [1.0.4.1] - 2016-03-17
### Added
- Check for TinyMCE in the builder before triggering TinyMCE save.

### Fixed
- Sub labels showing when configured to hide.
- Forms pagination number screen setting not saving.
- Email notification setting always displaying "On".

## [1.0.4] - 2016-03-16
### Changed
- Improved marketing provider conditional logic.
- Addons page [Lite].

### Fixed
- Variable assignment in the builder.

## [1.0.3] - 2016-03-15
### Added
- Basic TinyMCE editor for form confirmation messages.

### Changed
- Removed form ID from form overview table, ID still visible in shortcode column.

### Fixed
- Checkbox/radio form elements alignment.
- Quotation slashes in email notification text.
- SSL verification preventing proper API calls on some servers.

## [1.0.2] - 2016-03-13
### Added
- Widget to display form.
- Function to display form, `wpforms_display( $form_id )`.

### Changed
- Default notification settings for Contact form template.
- Success message styling for full form theme.

## [1.0.1] - 2016-03-12
### Added
- "From Name" and "Reply To" Setting>Notification fields.
- Smart Tags feature to all Setting>Notification fields.

## [1.0.0] - 2016-03-11
- Initial release.
