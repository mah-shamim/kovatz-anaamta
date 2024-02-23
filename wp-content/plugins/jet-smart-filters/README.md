# JetSmartFilters

# ChangeLog

## 3.3.2
* ADD: valid url params filter
* FIX: ePro Products / ePro Archive Products for search result page
* FIX: Is Checkbox Meta Field option for checkbox meta field created created using the ACF plugin
* FIX: function maybe_parse_repeater_options adding an array check
* FIX: prevent PHP notices
* FIX: if filter option "Exclude Or Include Items" is empty
* FIX: Ajax Request Type -> Referrer / Self plus mixed type pagination
* FIX: correctly parse meta parameters with colon
* FIX: additional providers of duplicate filters
* FIX(Bricks): override main WP query when 'Is main query' option is true
* FIX(Bricks): the method for deleting query cache has been added
* FIX(Bricks): disabling cache option during AJAX.

## 3.3.1
* ADD: datepicker calendar horizontal offset style option
* ADD: compat with Elementor Lazy Load Background Images feature
* FIX: Auto-index with checkboxes
* FIX: JSF loadmore compatibility for the query loop provider has added
* FIX: Archive Products added Automatically align buttons option when filtering
* FIX: ePro Products Source -> Current Query
* FIX: prevent php notice on calendar ajax action
* FIX: Range filter input type

## 3.3.0
* ADD: Added provider preloader
* ADD: Admin UI. Added a list of warnings why the update button is blocked
* ADD: All option and ability to deselect in visual filter when radio behavior
* ADD: Add possibility to use filter values in JetEngine query
* ADD: tabindex with Date Period
* UPD: Hierarchical select
* UPD: redesigned pagination and active elements templates to support unsafe-eval
* FIX: sync search filter with the same
* FIX: Range filter max value input
* FIX: Indexer not working correctly

## 3.2.6
* FIX: Indexer with _tax_query key in Query Variable
* FIX: redirect for Product grid with Apply type -> Ajax
* FIX: re-init nested Bricks widgets after filtering; the subscription method has been changed
* FIX: Mixed type URL on page reload if 'hc' occurs
* FIX: Active Filters / Active tags if number value
* FIX: indexer with Data Source -> Custom Fields filter

## 3.2.5
* FIX: fatal error when WPML CMS is disable but WPML string translation is enable
* FIX: Select filter Get Choices From Field Data from WooCommerce Product Data meta box
* FIX: sorting from customizer is reset when use filter and Listing Grid
* FIX: JetEngin listing grid custom styles on load more
* FIX: Bricks. filtration on search result page
* FIX: Bricks. Accordion doesn't work after filtering

## 3.2.4
* ADD: sorting filter WPML support
* ADD: .gitattributes
* UPD: optimization for product indexing
* FIX: range filter input decimal numbers
* FIX: additional filters with URL params
* FIX: ePro loop load more with styles
* FIX: Active filters value Cross-Site Scripting (XSS)
* FIX: booking listing & indexer

## 3.2.3
* UPD: cherry-x-vue-ui
* FIX: eProo loop custom post types & draft posts with current query
* FIX: ePro Posts excerpt + filters
* FIX: rewrite permalink rule for custom structure post_id

## 3.2.2.1
* FIX: Security issue.

## 3.2.2
* FIX: ePro Loop indexer
* FIX: rating filter on page reload
* FIX: added filter bricks/query/force_run
* FIX: moved the location of the filter `bricks/query/no_results_content` before rendering

## 3.2.1
* ADD: block editor additional providers
* ADD: range filter Inputs thousands and decimal separators
* ADD: allow disabling apply button in the Search filter
* ADD: ePro loop "No Result Text" option
* UPD: allow to register custom query variables for different request types
* UPD: allow to rewrite default query for provider and query ID pair
* UPD: ePro loop default query
* UPD: jetDashboard framework
* FIX: changing duplicated filters on change
* FIX: ePro loop + predefined filters
* FIX: date range/period RTL datepicker arrows
* FIX: comparison operator with decimal numbers
* FIX: visual filter img alt
* FIX: date filter if date 1970-1-1
* FIX: ePro loop alternate template static item position
* FIX: admin multilingual custom flag
* FIX: hierarchical select filter shows empty options
* FIX: bricks showing and hiding the load more button after filtering

## 3.2.0
* ADD: Elementor Pro Loop Grid provider
* ADD: admin multilingual support
* ADD: additional settings dropdown N selected
* ADD: date period filter Min/Max Dates operations
* ADD: 'Comparison type' option for 'Comparison operator'
* ADD: process shortcodes in 'URL with filtered value' dynamic tag
* UPD: checkbox filter with dropdown update selected items on input change, not on filter change
* UPD: not include children for 'Intersection' relational operator
* UPD: filter each query type key after indexing
* UPD: JetDashboard module
* FIX: admin dropdown outside click
* FIX: custom fields JetEngine WPML string translation
* FIX: current WP Query & Indexer compatibility
* FIX: hierarchical filter with additional providers
* FIX: ePro Posts returns "0.66" value instead blank list for 0 results
* FIX: elementor popup with filters
* FIX: elementor popup with "Improved Asset Loading" option
* FIX: click Back button after applying filters with a redirect
* FIX: visual filter when dragging item changes image
* FIX: fatal error in dynamic tag when filter is deleted
* FIX: ePro Portfolio masonry

## 3.1.2
* ADD: process shortcodes in 'URL with filtered value' dynamic tag
* FIX: apply filter 'jet-smart-filters/render_filter_template/filter_id' for all filters
* FIX: current WP Query & Indexer compatibility
* FIX: date period filter period type "DAY"
* FIX: prevent php notices on php 8.2
* FIX: add filter Id to filter uniqueKey
* FIX: update duplicated hierarchical filter on reload
* FIX: fixed radio filter direction control in Bricks
* UPD: prevent from registering DOING_AJAX constant on non-admin-ajax referrers

## 3.1.1
* UPD: redesigned initialization of filters on the frontend
* ADD: pagination load more
* ADD: fieldset legends & aria-labels
* ADD: don't send ajax request if page hasn't provider
* ADD: filter 'jet-smart-filters/service/filter/serialized-keys'
* ADD: reinitFilters global method
* FIX: sitepath for url aliases
* FIX: URL aliases settings RTL
* FIX: fatal when Bricks query loop ("Is filterable" checked) in listing grid item

## 3.1.0
* ADD: Allow to replace selected parts of the filtered URLs with any alias words you want;
* ADD: Bricks Query Loop provider;
* ADD: PHP 8.2 compatibility;
* UPD: Improve security checks for edit filters settings requests;
* UPD: Visual filter dropdown select for taxonomies and posts data source;
* FIX: Exclude/include data source posts list;
* FIX: Admin filters list pagination;
* FIX: Keep third party URL params on filters clear;
* FIX: JetEngine Calendar and filters compatibility;
* FIX: Fatal error for when accessing admin area for non-admins users.

## 3.0.4
* ADD: Bricks builder compatibility;
* UPD: Filters builder icons;
* UPD: jet dashboard to 2.0.4;
* FIX: Ensure correct provider set from request;
* FIX: compatibility with JetWooBuilder 2.1.2;
* FIX: Visibility of classic admin editor fields in some cases.

## 3.0.3
* ADD: Filter Date Period dates limit
* ADD: indexer counter prefix/suffix & position style
* FIX: refactoring Active filter & Active tag filters
* FIX: elementor pro v3.9.0 popup
* FIX: ignore disabled filters on set data
* FIX: additional providers
* UPD: renamed url prefix from 'jet-smart-filters' to 'jsf'

## 3.0.2
* ADD: admin ability to open a filter from the list in a new tab
* ADD: pagination filter autoscroll option
* FIX: apply all hierarchical selects on redirect
* FIX: checkbox, radio & visual filter RTL
* FIX: active filters, active tag filters duplicate results after mixed url opening
* FIX: ePro Archive Posts taxonomy with multiple post types
* FIX: elementor responsive with url parameters
* FIX: additional settings placeholders translation
* FIX: adding tabindex attr

## 3.0.1
* ADD: admin RTL
* ADD: admin select search field for options
* ADD: admin advanced input for custom query var
* UPD: admin color-image icon
* FIX: admin exclude or include items on options changing
* FIX: admin media control SVG
* ADD: accessibility tabindex
* ADD: 'jet-smart-filters/inited' document event
* ADD: JS trigger before filters initialization
* ADD: allow to use tax query with different sources
* UPD: change icons
* UPD: tax query and new dynamic min/max callbacks
* FIX: prevent notices when Color Image options generated dynamically
* FIX: compatibility with custom options

## 3.0.0
Admin interface changes. Redesigned into single page application.

* FIX: prevent php notices after installation template by wizard
* FIX: prevent php notices on calendar request
* FIX: allow to correctly extend Jet_Smart_Filters_Hierarchy class
* FIX: woocommerce-archive hide out of stock items from the catalog on page reload
* FIX: date period editor block error (air-datepicker script)

## 2.3.14
* ADD: Query ID setting for blocks
* ADD: 'jet-smart-filters/query/request' to filter request before parsing query arguments
* FIX: Compatibility with Elementor 3.7
* FIX: Blocks editor and Listing Grid 'is_archive_template' option compatibility
* FIX: Merge default with current query args on ajax indexing
* FIX: Correctly pull dynamic min/max from meta values for range filter on terms archive pages
* FIX: Select filter. Don't add select_disabled_color control if the indexer is disabled
* FIX: JetEngine Calendar compatibility
* UPD: For indexer SQL query removed space between parenthesis and value. This causes an error for some clients
* UPD: Unchecked group items for intersection relational operator

## 2.3.13
* ADD: JetWooBuilder 2.0.0 version compatibility
* FIX: Blocks styles
* FIX: multi language without multi currency
* FIX: filter name Check Range > Check Range Filter

## 2.3.12
* ADD: reindex indexer DB table on plugin activate and update
* UPD: template parses special characters
* FIX: Permalink rewrite rules
* FIX: range filter with popup
* FIX: WPML WooCommerce multi currency price
* FIX: Date Range Filter datepicker current day
* FIX: Search filter RTL
* FIX: filter date period rtl scroll
* FIX: gutenberg console error
* FIX: indexer with current query args
* FIX: maps listing for Borlabs Cookies plugin
* FIX: additional filter settings input clears the 'X'
* FIX: show widget icon in elementor editor if filter not selected
* FIX: additional filter style search remove horizontal offset RTL
* FIX: additional filter style search remove horizontal offset RTL
* FIX: Checkbox styles for block editor

## 2.3.11
* UPD: replaced deprecated method _register_controls to register_controls
* FIX: CheckBoxes additional settings dropdown
* FIX: Search filter spinner spins infinitely on submission with 'AJAX on typing'
* FIX: ePro widgets after filtration
* FIX: duplication of sublevels of a hierarchical select
* FIX: woocommerce shortcode attribute on page reload
* FIX: check hierarchy current page
* FIX: Radio filter with motion effects sticky
* FIX: Date range filter query & placeholder on redirect
* FIX: Date period filter for popup
* FIX: Select filter alignment style
* FIX: EPro Posts skin 'Full Content' settings

## 2.3.10
* ADD: elementor pro popup support
* FIX: jet-woo-products-grid/list Use Current Query option on archive page
* FIX: air-datepicker conflict
* FIX: taxanomies parent terms indexer
* FIX: compatibility with Elementor Pro 3.6
* UPD: jet-elementor-extension framework

## 2.3.9
* ADD: Custom Query Variable option for taxonomies source
* ADD: `URL with filtered value` dynamic tag
* UPD: Better JetEngine compatibility
* FIX: Select filter style options
* FIX: WPML tax sub terms indexer
* FIX: Filter label notice

## 2.3.8
* ADD: allow to filter indexer data before writing into DB
* UPD: setIndexedData updating result manually
* FIX: grammatical error correction from HoriSontal to HoriZontal
* FIX: clear range filter input
* FIX: hierarchical chain
* FIX: sanitize widgets settings before passing for rendering
* FIX: indexer with duplicates

## 2.3.7
* ADD: indexer on get filters data request sql SET SESSION group_concat_max_len
* ADD: check is indexer enabled on 'index_filters' method

## 2.3.6
* SYS: renamed indexer method

## 2.3.5
* FIX: JetEngine with Use Custom Query on AJAX compatibility
* FIX: JetEngine lazy load compatibility

## 2.3.4
* FIX: Indexer for custom database table prefix

## 2.3.3
* UPD: Indexer refactoring
* ADD: Auto re-indexing option
* FIX: Alphabet filter
* FIX: Duplicate labels in the filter widget when displaying multiple filters
* FIX: Date Range with one blank field
* FIX: Date Period day type
* FIX: Rating filter clear
* FIX: Check Range filter if item max value 0
* FIX: Range filter if item max value 0
* FIX: Range filter with negatives values
* FIX: elementor editor icons from fa to eicon
* FIX: guten blocks in widgets areas error on refresh
* FIX: remove console.log

## 2.3.2
* ADD: multi sorting
* ADD: Sorting filter Reset Field Appearance control
* FIX: url with additional filters
* FIX: apply button filter for gutenberg
* FIX: Alphabet filter
* FIX: Date period filter events duplication
* FIX: Active tag filter visibility for Hello Elementor theme
* FIX: guten get_editor_script_depends
* FIX: Radio All option label when Group terms by parents
* FIX: Date Range with page reload in Safari
* FIX: hierarchical chaining for identical taxonomies
* FIX: Range filter WooCommerce min/max prices with gets params
* FIX: Hierarchical label
* FIX: jet-engine-calendar current request query

## 2.3.1
* ADD: Query Builder settings to store for JetWooBuilder Product Grid/List providers;
* FIX: Custom query arguments for Product List provider.

## 2.3.0
* ADD: Alphabet filter
* ADD: Multiple query variable separated by comma
* ADD: Radio, Visual, CheckRange filters add additional settings
* ADD: CCT Data Source
* FIX: Additional filter settings dropdown without search
* FIX: range input slider
* FIX: relation AND between filters with the same taxonomy
* FIX: elementor pro Archive Products customizer default product sorting options

## 2.2.3
* ADD: compatibility with new jetEngine features
* UPD: pagination filter provider top offset change max to 999
* UPD: pagination filter items gap
* UPD: checkbox decorator offsets
* FIX: Products cat & tag default taxonomy
* FIX: elementor Scheme_Typography

## 2.2.2
* UPD: Range Filter
* FIX: Grouped Filters styles
* FIX: Minor bugs

## 2.2.1
* UPD: Allow to rewrite indexer query args
* UPD: Rolled back hide elementor widget container if all items are hidden by indexer
* FIX: JetEngine glossaries compatibility
* FIX: Avoid letter-casing related errors when checking if DB table is exists
* FIX: ePro archive products default query
* FIX: ePro Archive Products sorting on page reload if sorting presets are set in the customizer
* FIX: Products loop

## 2.2.0
* ADD: URL Structure Settings (Plain/Permalink)
* ADD: JetTabs ajax load template compatibility
* ADD: Hamburger Panel ajax load template compatibility
* ADD: Hide elementor widget container if all items are hidden by indexer
* ADD: Date period datepicker button text
* ADD: ePro Posts skin full content support
* FIX: Visual filter options list value
* FIX: Checkbox filter MORE/LESS ignore the item if it was hidden by the indexer as empty
* FIX: remove strip slashes on searching
* FIX: check current control on ajax redirect
* FIX: avoid PHP notices
* FIX: bugs fixing


## 2.1.1
* ADD: Hide filter label if all items is hidden
* ADD: Localized data extra_props
* FIX: Filter select grouped filters styles
* FIX: Date period format placeholder
* FIX: Hierarchy filter with single tax
* FIX: Visual filter image empty error
* FIX: EPro Archive Products add tax_query to store query

## 2.1.0
* ADD: New filter Date Period
* ADD: Checkboxes Additional Settings:
	* Search
	* More/Less
	* Dropdown
	* Scroll
* ADD: Radio
	* Ability to add options all
	* Ability to deselect radio buttons
* ADD: Added the ability to change styles in Gutenberg ( **required plugin Jet Style Manager** )
<br/>Widgets that support styles:
	* Active Filters
	* Active Tags
	* Apply Button
	* Checkboxes
	* Check Range
	* Date Period
	* Date Range
	* Pagination
	* Radio
	* Range
	* Rating
	* Remove Filters
	* Search
	* Select
	* Sorting
	* Visual

## 2.0.6
* FIX: WordPress 5.6 compatibility

## 2.0.5
* UPD: jet dashboard to 2.0.4
* FIX: bugs fixing

## 2.0.4
* ADD: hide Elementor widgets: active filters, active tags and remove filters if not active
* ADD: hierarchical filter preloader class
* UPD: change indexer DB columns format
* UPD: jet dashboard to 2.0.0
* FIX: minor bugs

## 2.0.3
* ADD: JetWooBuilder 1.7.0 compatibility
* ADD: compatibility with upcoming jet-engine listing
* FIX: epro-archive widget for products posts

## 2.0.2
* ADD: 'Get from query meta key' callback for range filter
* UPD: wrapper action for jet-engine provider
* FIX: hierarchy filter with single taxonomy
* FIX: process listing grid with nested listing grid
* FIX: epro-archive widget default query tags and custom taxonomy

## 2.0.1
* ADD: jet-dashboard
* ADD: date format for date-range filter
* ADD: ajax content hooks for epro-products widget
* FIX: clearing select filter when returning to the filter page
* FIX: minor bugs

## 2.0.0
* ADD: added filter blocks for gutenberg
* FIX: ignoring a hidden filter in a general query
* FIX: range active items prefix and suffix
* FIX: hide active filter styles while there are no active filters
* FIX: indexer hide/disable items with disabled counter
* FIX: minor bugs

## 1.8.4
* ADD: additional providers repeater with provider and queryID
* ADD: ability to set negative values for range filter
* ADD: merge same query keys for filters with Exclude/Include option
* FIX: ePro Posts 'Open in new window' option
* FIX: clearing meta_query date on redirect
* FIX: term_taxonomy_id from term_id for hierarchy filter
* FIX: don't show the counter when the option is turned off while the indexer is on
* FIX: fix for duplicate pagination filters

## 1.8.3
* FIX: hierarchical select;
* FIX: indexer data key for manual input data source;
* FIX: pagination for Pro Product with query_id;

## 1.8.2
* ADD: allow using numbers in "query id" fields;
* FIX: hierarchical filters workflow with additional providers;
* FIX: filters workflow with the products loop widget;
* FIX: hide filters items in the Safari browser;
* FIX: minor bugs;

## 1.8.1
* FIX: redirect path url;
* FIX: provider widget query ID;
* FIX: reset field appearance;

## 1.8.0
* UPD: front-end code refactoring;
* ADD: allow to choose additional provider for filters;
* ADD: show empty terms for checkboxes, select, radio and visual filters;

## 1.7.2
* ADD: compatibility the Indexer with WPML plugin;
* FIX: applying Indexer functionality for page reload filters;
* FIX: compatibility the Indexer with JetPopup plugin;
* FIX: Checkbox, Check Range, Radio filters horizontal layout style controls;
* FIX: hierarchy levels options list on redirect;
* FIX: various minor fixes.

## 1.7.1
* ADD: Allow to get options for select, radio and checkboxes from custom field data (for JetEngine or ACF);
* FIX: Various fixes.

## 1.7.0
* ADD: Sorting widget;
* ADD: Support for Elementor Pro Portfolio widget;
* ADD: comparison operator for select and radio filters;
* ADD: Search Filter widget add apply on typing option;
* ADD: Relational operator for checkbox filter;
* ADD: Active Tags filter;
* ADD: New aply type for filters;
* UPD: Style options for checkbox, check range, radio, visual filters;
* FIX: Minor bugs.

## 1.6.2
* FIX: grouped filters styles
* FIX: better JetEngine compatibility
* FIX: hide grouped filters when indexer empty

## 1.6.1
* UPD: grouped filters styles
* FIX: various fixes

## 1.6.0
* ADD: allow to make redirect from filters to results page
* ADD: Hiearachical filters
* FIX: Various fixes

## 1.5.1
* FIX: Default query args in jet woo products grid widget

## 1.5.0
* ADD: Indexer functionality for checkboxes, check range, select, visual and radio filter types
* UPD: Hide remove all filters button if no active filters
* UPD: Filters Icons
* FIX: Various fixes

## 1.4.2
* FIX: Hot Fixes

## 1.4.1
* ADD: Need helps links to widgets
* ADD: Placeholders for inputs in Date Range Filter

## 1.4.0
* ADD: Visual filter
* ADD: Include/Exclude functionality
* ADD: Remove all filters button widget
* ADD: Inline layout options for radio, checkboxes, check-range filters
* ADD: Better compatibility with WPML and WooCommerce Multilingual plugins
* ADD: %woocommerce_currency_symbol%  macros for range filter prefix and suffix options;
* FIX: Various fixes.
* ADD: Changelog;

## 1.3.2
* ADD: Compatibility with checkbox meta field created with Jet Engine - https://github.com/CrocoBlock/suggestions/issues/163;
* FIX: Merge default query args with current query args;

## 1.3.1
* ADD: Compatibility with WooCommerce Multilingual plugin;
* FIX: Bug with woocommerce archive provider in astra theme;
* FIX: Issue CrocoBlock/suggestions#186;
* FIX: Merging query args with default query args;
* UPD: Compatibility with JetEngine 1.4.0;
* FIX: Various fixes.

## 1.3.0
* ADD: Rating widget;
* ADD: Support for Elementor Pro Products widget;
* ADD: Support for Elementor Pro Archive Products widget;
* ADD: Apply search filter on enter press action
* FIX: Various fixes.

## 1.2.1
* ADD: Allow to filter query before filters applied;
* UPD: Better Compatibility with Elementor Pro;
* FIX: Templates select for JetWooBuilder widgets;
* FIX: Various fixes.


## 1.2.0
* ADD: Separate widget for Apply button;
* ADD: Support for Elementor Pro Posts widget;
* ADD: Support for Elementor Pro Archive widget;
* UPD: New options for Pagination widget;
* FIX: Various fixes.

## 1.1.0.1
* FIX: Large numbers comparing

## 1.1.0
* ADD: RU localization;
* ADD: allow to edit or disable prev/next controls in Pagination widget;
* ADD: allow to set step, number format and suffix for range and check range filters;
* UPD: allow to search by meta field in search filter;
* UPD: run Elementor ready triggers after apply filters;
* UPD: allow to filter same query variable by multiple filters.

## 1.0.0
* Initial release
