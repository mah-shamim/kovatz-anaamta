# ChangeLog

## 3.3.5
* ADD: Cluster Max Zoom and Cluster Radius settings for Map Listing;
* ADD: An ability to show dynamic popup for repeater query in Calendar;
* FIX: Only products in paid order display for purchised products macros;
* FIX: Positioning added Options pages in the admin menu;
* FIX: Removal of related items doesn't synch in different languages;
* FIX: Option page for CCT gives 404 error;
* FIX: Empty columns dropdown in SQL query;
* FIX: 504 error in some cases;
* FIX: Issue with OpenStreetMap coordinates in Map meta field;
* FIX: Repeater items duplicates in Dynamic Calendar;
* FIX: Problem with Bricks condition in Dynamic Calendar;
* FIX: Display Dynamic Field content in the Bricks template;
* FIX: Reset radius in Location and Distance filter when address is reset.

## 3.3.4
* FIX: Prevent error if Elementor is not installed;
* FIX: Query result count for Dynamic Visibility in gutenberg does not work;
* FIX: Prevent deprecated notic on PHP 8+;
* FIX: Sorting filters not working with CCT Dynamic Table/CCT listing custom query;
* FIX: Filters with Maps listing and CCT Query;
* FIX: PHP error when using a Query Results Macro in Blocks editor;
* FIX: Error when use Location&Distance in some cases;
* FIX: Brick Builder: Query loop conflict;
* FIX: Related items table UI;
* FIX: Bricks: setting background color dynamically from the colorpicker meta field does not work;
* FIX: Advanced date field is not displayed properly on large number of iterations;
* FIX: Can't update CCT posts meta field via REST API if is multiple is enabled.

## 3.3.3
* ADD: Dynamic Terms widget for Bricks editor;
* ADD: Default value to the color picker field;
* UPD: Dynamic CSS  of Listing Item refactoring;
* UPD: Cache Related items query during the single request to avoid SQL queries duplicating;
* FIX: Listing Grid. When lazy load is enabled, the listing disappears;
* FIX: Placeholder for relation meta based on qurey builder does not work;
* FIX: Profile Builder. Profile Menu - Single User Page in Blocks editor;
* FIX: Get users from store item macro;
* FIX: Conditions of meta field does not work for bulk inputted values;
* FIX: Custom meta field for Pages causes PHP error;
* FIX: Order by not working for a Query Builder of type SQL/AI Query.

## 3.3.2.1
* FIX: Orderby param in SQL and CCT queries;

## 3.3.2
* ADD: An ability to set anchor text in Listing Grid item link;
* ADD: Allow to use SQL and REST API queries with Map Listing;
* ADD: Allow to sse pagination with repeater query;
* FIX: Issue with injections on mobile;
* FIX: Issue with slider in Listing Grid;

## 3.3.1
* FIX: Dynamic Link: with hide if value is empty still appears if there is mailto: prefix;
* FIX: Dynamic Link: prefix display issue;
* FIX: Images gallery and slider style in Bricks builder;
* FIX: WPML. Map listing marker color;
* FIX: Geo distance not displaying correctly after filtration;
* FIX: Maps Listing add to data store;
* FIX: CCT Query. Order by Type Numeric;
* FIX: Repeater Query. Issue with OR relation;
* FIX: Geo Query in CCT resetting after making a change in CCT fields;
* FIX: Encoding in redirect url for CCT listings;
* FIX: Fallback render issue;

## 3.3.0.1
* FIX: Fatal error when Dynamic Visibility module is enabled with WooCOmmerce but without Elementor

## 3.3.0
* ADD: Timber/Twig views type for the Listing items;
* ADD: Calendar module: Advanced Date field type and recurring events in the Calendar listing;
* ADD: Location & Distance filter type;
* ADD: New options sources for meta fields - Bulk Manual Input & Query Builder;
* ADD: Query Builder: Add an option to disable query caching;
* UPD: Listing Grid slider improvements.

## 3.2.7
* ADD: an ability to use macros in Map Center setting;
* ADD: WooCommerce dynamic visibility condition Product is Purchased;
* ADD: `jet-engine/options-pages/updated`, `jet-engine/options-pages/updated/{$page-slug}` hooks for after saving options pages;
* ADD: Support of dynamic popups for JetFormBuilder Records Query;
* FIX: Listing grid breaks layout of template in Bricks;
* FIX: OpenStreet Map API;
* FIX: CCT SQL query lisitng after filtration shows wrong ID;
* FIX: Issue with Listing and Lazy Load;
* FIX: WooCommerce Product Meta Box fields;
* FIX: Dynamic Link - redirect after delete;
* FIX: Warning with MetaBox pluign;
* FIX: Update Vue.js version for admin framework;
* FIX: Static item not injected if the number of posts is low;
* FIX: Add compatibility the JetFormBuilder Records Query with Pagination filter;
* FIX: Various fixes with AJAX loading for listings.

## 3.2.6
* UPD: Support of Repeater Query for CCT Repeaters;
* FIX: Bricks Builder. Listing Grid loses styles after loading wiht AJAX;
* FIX: Bricks Builder. Nested listing breaks the Footer;
* FIX: Map Listings. Prevent opening default map info box if it's point match with marker;
* FIX: Saving product if the Product Data metabox contains required fields;
* FIX: Dynamic CSS for injected listing items;
* FIX: `Related Items List` callback resulting in critical error;
* FIX: Saving the order of repeater fields and manual options in Meta Fields editor;
* FIX: Printing dynamic CSS on mobile devices.

## 3.2.5.2
* FIX: security issue.

## 3.2.5.1
* FIX: Non-posts JetEngine Queries with Bricks Query Loop.

## 3.2.5
* ADD: An ability centering and increasing map when click on marker Crocoblock/suggestions#5926;
* ADD: An ability to display a dynamic image from Advanced SQL Query Crocoblock/suggestions#6492;
* ADD: Min Zoom option for Map Listing Crocoblock/suggestions#6864;
* ADD: Support for repeater field in Map Listing;
* ADD: Language query arg to the Google map API URL;
* ADD: Query Builder: allow to order terms by meta_value and meta_value_num Crocoblock/suggestions#1225;
* ADD: `prepare_date_query_args` method for queries;
* ADD: Current Object Image Field dynamic tag;
* UPD: Prevent some user meta fields from updating with Legacy Forms;
* FIX: Error in query type when deactivating the WooCommerce
* FIX: Dynamic link with url_prefix in cct listing;
* FIX: Dynamic link/Query arguments + shortcode result macro;
* FIX: Repeater Query & Checkbox filter;
* FIX: the delete link is not working properly with Elementor Page Transition;
* FIX: User Geolocation filter & Load More;
* FIX: Nested listing query with query switcher filter + load more;
* FIX: Context setting in the Data Store button;
* FIX: Clone CCT with Single post;
* FIX: Macro fallback if macro result is not-found;
* FIX: Dynamic term and Dynamic field for ACF with WC Query;
* FIX: Better capability checking on Legacy Forms settings saving;
* FIX: Prevent Legacy Forms access for non-admin users.

## 3.2.4
* ADD: Zoom option for Map Listing popup;
* FIX: Prevent PHP warnings on Option Page;
* FIX: Clear a repeater query cache if a repeater listing is inside another listing;
* FIX: https://github.com/Crocoblock/suggestions/issues/6960;
* FIX: Dynamic visibility. Days of week condition for Blocks editor;
* FIX: Reset `is_last_static_hooked` injected prop after listing;
* FIX: Meta Boxes for Products Data. Tab priority option;
* FIX: Bricks. {post_content} issue when dynamic elements was used.

## 3.2.3.2
* FIX: Change listing grid items render method for Elementor to avoid errors in the editor.

## 3.2.3.1
* FIX: error loading the Listing Grid block.

## 3.2.3
* ADD: Query description in queries list screen;
* ADD: An ability to set 0 as fallback in macro;
* FIX: `replace_tax_query_row` method;
* FIX: Listing Grid. Block attributes validation;
* FIX: Setup macros context for label in Dynamic Link;
* FIX: prevent fatal error in filters in some cases;
* FIX: Dynamic popups and advanced SQL queries;
* FIX: Dynamic object field in Repeater Query;
* FIX: GenerateBlocks. Missing styles when lazy loading is enabled.

## 3.2.2
* FIX: Better sanitize user callbacks before execute;
* FIX: Query results fallback doesn't work with dymanic tag;
* FIX: CPT Fields UI Bug;
* FIX: Make listing item clickable option for map listing;
* FIX: Current user instead of queried in listing popup of map listing;
* FIX: Dynamic Image block and Site Editor compatibility.

## 3.2.1
* FIX: Icon position for Dynamic Link;

## 3.2.0
* ADD: Allow to generate SQL queries with AI;
* ADD: Perfomance section in JetEngine dashboard with optimization tweaks;
* ADD: New query type for Query Builder - `Current WP Query`;
* ADD: Listing Grid. Columns Number: `Auto` option;
* ADD: Allow to output JetEngine meta fields in WooCommerce native tabs and variations;
* ADD: JetEngine meta fields now supports Posts Revisions;
* ADD: Alpha setting for Colorpicker meta fields;
* ADD: Relational operator AND/OR to SQL query;
* ADD: Fallback for CCT image dynamic tag;
* UPD: Map Listing: allow to open map popup on marker hover;
* UPD: Meta fields conditional rules;
* UPD: Partial frontend JS refactoring;
* UPD: Elementor widgets registration refactoring;
* FIX: Calendar Listing Widget Caption style options;
* FIX: Map Listings: custom styles for MapBox provider;
* FIX: Images gallery slider callback doen't work in shortcode
* FIX: PHP 8.2.6 compatibility;
* FIX: Prevent PHP notice in customizer in some cases;
* FIX: Data store button doesn't work properly with cast result in SQL query;
* FIX: Dynamic background from CCT field;
* FIX: Skin Import fails if skin contains Unicode characters;
* FIX: Fatal error in Bricks builder 1.8+;
* FIX: CCT REST API Unable to update multiple Select field;
* FIX: Prevent errors when using Format number callback.

## 3.1.6.1
* FIX: Prevent PHP warnings on PHP < 8.0.

## 3.1.6
* ADD: Allow to output "Show N of M" for Listing query with dynamically changed numbers when using Load More functionality;
* ADD: `jet-engine/query-builder/types/posts-query/args` filter to modify Post query args before use;
* ADD: Support of dynamic popups for SQL queries;
* ADD: Option to stay on current global page after delete post with Dynamic Link;
* ADD: Allow to trigger popup by click on listing item;
* UPD: Glossaries support for Admin Columns;
* UPD: Better compatibility Rank Math & Profile Builder User Page;
* UPD: Better error messages for CCT edit page;
* FIX: Prevent JS notices in Map listing;
* FIX: Allow to set 0 as fallback value in dynamic tags;
* FIX: Bricks: Listing grid - Not Found message control;
* FIX: Dynamic Repeater throws critical error on PHP 8.2;
* FIX: "Back to items" label not saved when creating new taxonomy;
* FIX: Lazy load with tax query and tax filter;
* FIX: Images gallery grid/slider does not work in repeater listing;
* FIX: Glossary value 0 is not saved;
* FIX: Shortcode result with filter field output;
* FIX: Minor bug in admin filters functionality.

## 3.1.5
* ADD: Query Builder: Before and After options for Advanced settings section of dynamic arguments;
* ADD: Query Builder: Description field for Query. Crocoblock/suggestions#6194;
* ADD: Glossaries: Allow to convert options from the file into manual source;
* UPD: Delete CCT items of user (author of CCT items) when user account is deleted;
* UPD: Allow to make listing Item clickable in Blocks editor;
* UPD: Minor Map Listing improvements;
* FIX: Listing Grid: Compatibility with new Elementor Mega Menu;
* FIX: Listing Grid: Slides to Scroll on mobile;
* FIX: `%current_terms%` macro fatal error when no object received;
* FIX: Error after deleting CCT which was used in Query Builder;
* FIX: Bricks builder listings: Post ID after listing;
* FIX: Translate default value of CPT labels;
* FIX: Prevent errors in some cases when query was deleted from Query Builder;
* FIX: Better PHP 8.1+ compatibility.

## 3.1.4
* ADD: Relevanssi search and Query Builder compatibility;
* ADD: Allow to set aria-labels for Dynamic Link widget and for clickable listing grid item;
* ADD: Is post by queried user dynamic visibility condition;
* ADD: Shortcode Result macro;
* UPD: Show deleted instance name in dialog box for JetEngine instances;
* FIX: Better WPML compatibility;
* FIX: Media-related dynamic tags processing;
* FIX: ACF repeater settings for Dynamic Repeater widget;
* FIX: Grid/Slider Gallery Dynamic Field;
* FIX: Listing grid in Bricks builder - Not Found message control;
* FIX: Dynamic Meta widget inside listing grid with Custom Query.

## 3.1.3.1
* ADD: 'jet-engine/query-builder/set-props', 'jet-engine/listing/current-object-title', 'jet-engine/query-builder/filters/before-set-props', 'jet-engine/query-builder/filters/after-set-props' hooks;
* FIX: Additional security checks on export, import and duplicate legacy forms.

## 3.1.3
* ADD: Dynamic Field: Allow to specify object property to get manually;
* ADD: Allow enabling hierarchical structure of term link;
* ADD: Current Date macro;
* UPD: Profile Builder: User Page Title to add First Name, Last Name & Nickname;
* FIX: Fatal error when listing is not exists;
* FIX: Layout option for relations meta;
* FIX: Unable to fetch image in Lisitng item in Buicks Builder;
* FIX: ACF get_field() function not working properly in Listing Item;
* FIX: Form displayed with a shortcode not working properly;
* FIX: Shortcode generator: Missing Format Number callback parameter;
* FIX: Custom Query with filter by related post;
* FIX: Repeater query issue with dynamic fields below;
* FIX: CCT Query IN and NOT IN operators do not get an array as a value;
* FIX: PHP errors on save build-in taxonomy;
* FIX: Terms Query: Order by meta clause does not work if the clause name contains capital letters;
* FIX: Update elementor database critical error.

## 3.1.2
* ADD: Bricks builder: native Bricks conditional visibility inside JetEngine listing items;
* ADD: Dynamic field: get user data callback;
* UPD: CCT date field: automatically convert saved values when change 'Save as timestamp' field option;
* FIX: CCT number field: can't change the step;
* FIX: Bricks builder: global styles and JetEngine elements;
* FIX: Dynamic Link: context option;
* FIX: Deprecated: elementor\Core\DynamicTags\Manager::register_tag;
* FIX: Bricks builder: custom CSS class and style;
* FIX: Profile builder: profile page links options for Dynamic Image;
* FIX: PHP 8.2 compatibility;
* FIX: Scroll Slider with Additional Custom Breakpoints;
* FIX: Dynamic Image: Image Size doesn't work for URL value format;
* FIX: Gallery Grid Callback: a warning with URL and Array image value;
* FIX: Profile builder: profile page link + Bricks;
* FIX: Calendar filtering issue;
* FIX: Autocomplete Google map request if not setup Separate Geocoding API key;
* FIX: JS error: Loading the Google Maps JavaScript API without a callback is not supported;
* FIX: Query Builder: repeater query with checkbox field;
* FIX: Query Builder: results Counts skips CCT items status.

## 3.1.1
* ADD: Unified Date format callback;
* ADD: Support for custom units;
* ADD: Native Bricks loop builder and Query builder compatibility;
* ADD: Elementor Dynamic Tag for relation meta data;
* ADD: Query Results macros;
* FIX: Calendar bug with multiday events;
* FIX: Saving Quick Edit fields in some cases;
* FIX: Update Repeater field in Post through REST API;
* FIX: Bricks Listing grid Columns span option for injections;
* FIX: Padding option for nested Listings;
* FIX: Saving dates in options pages;
* FIX: Saving metabox for WC products when has required fields;
* FIX: Additional security checks in some edge cases.

## 3.1.0
* ADD: Bricks Builder сompatibility;
* ADD: Custom control for connecting query to any widget containing a repeater;
* ADD: Repeater Query Type;
* ADD: New conditions for Dynamic Visibility;
* ADD: Ability to choose the options storage type;
* ADD: Quick search in Troubleshooting and Knowledge Base from the admin panel;
* ADD: Macros Generator;
* UPD: New JS init for non-Elemenotr views;
* UPD: Shortocde Generator;
* UPD: Hide Show in Rest API option for HTML filed;
* FIX: Prevent errors if WooCommerce is not installed;
* FIX: Avoid errors in the map listing in some cases;
* FIX: Prevent errors in some cases if Elementor not installed.

## 3.0.9.1
* FIX: profile page url in the Dynamic Link;
* FIX: providers issue in the Map Listing module.

## 3.0.9
* ADD: Output SQL request in preview for SQL query;
* ADD: Allow to style Not found Message in Listing Grid;
* ADD: Infinite scroll offset option for Listing Grid;
* UPD: Allow to control zoom on scrool behavior for Leaflet and Map Box providers;
* FIX: Better check conditional rules for Profile Builder pages;
* FIX: Correctly save root object on JetEngine AJAX requests;
* FIX: Query builder, Terms query. Hide empty terms option behavior;
* FIX: Filter field output Callbacks for blocks editor;
* FIX: PHP warning in some cases.

## 3.0.8
* ADD: Save as timestamp setting for date fields inside repeaters;
* ADD: Quantity input for Add to cart source in Dynamic link widget;
* ADD: Context for Dynamic repeater block;
* ADD: An ability to copy filed name to clipboard;
* ADD: Allow order terms in the Dynamic Terms;
* FIX: Dynamic function thousands separator bug;
* FIX: Z-Index in Listing Grid Slider;
* FIX: Map listing popup in tabs with ajax loading;
* FIX: Post URL dynamic tag with nested listings;
* FIX: Reset order by meta clause with filters;
* FIX: Map Listing and Glossary get label callback;
* FIX: Options page saving in some cases;
* FIX: Calendar with macros queried user;
* FIX: Dynamic visibility on blocks editor if WooCommerce activated.

## 3.0.7
* ADD: Dynamic Image for Elementor - allow to open image in Lightbox;
* ADD: New Dynamic Visibility conditions (Greater or equal, Less or equal, Between, Not Between, Regexp, Not Regexp);
* UPD: Dynamic Feilds for Elementor - add dynamic data support for Fallback option;
* UPD: Allow to select context for Dynamic Terms;
* FIX: Profile builder, URL for deleted user;
* FIX: Taxonomies, allow to add more then 32 symbols to slug;
* FIX: Listing Item + Kadence Blocks (PRO) background image;
* FIX: Repeater Field delimiter Trims intentionall whitespaces;
* FIX: PHP error with JSF version less 3.0.0;
* FIX: Elementor 3.8.0 compatibility;
* FIX: Map listing with Elementor sticky option.

## 3.0.6.1
* FIX: Calendar navigation for some cases.

## 3.0.6
* ADD: Macro Query Results Count;
* ADD: JSF compatibility - Query Builder source for filter options;
* ADD: Crocoblock/suggestions#2288;
* UPD: Show pop-up Jet Engine Map Listing;
* FIX: Dynamic meta block error in the posts content;
* FIX: Load more doesnt work for listing with query builder after filtering by checkbox field;
* FIX: Lazy load listing is not filtered with redirect;
* FIX: Query builder: Random order + Load More + Filters;
* FIX: Listing Grid with WC Query inside Listing Grid;
* FIX: Map Settings issue with the listing;
* FIX: Borlabs Cookies and Maps Listing;
* FIX: Geolocation filter initizliation;
* FIX: Load more and nested listings;
* FIX: Use local timestamp instead of universal time in the Dynamic calendar;
* FIX: Fatal error if received post terms is WP_ERROR;
* FIX: WPML issue;
* FIX: Dynamic visibility is_in_stock error.

## 3.0.5
* ADD: `Current Object Field` Dynamic Tag;
* ADD: An ability to sort glossary list;
* ADD: Show glossary ID in UI;
* FIX: Meta field conditional logic & required attribute;
* FIX: Advanced SQL query and Include Columns option conflict;
* FIX: Dynamic Calendar days check;
* FIX: Query builder dynamic Date query parameters;
* FIX: Related Items callback error if in listing item has nested listing;
* FIX: Dynamic Visibility: Datetime Data type issue;
* FIX: Reset Query Builder query for some edge cases;
* FIX: Calendar error if there is no custom queries;
* FIX: Dynamic function in blocks editor.

## 3.0.4
* ADD: Query Builder + Dynamic Calendar compatibility;
* ADD: allow to cast SQL query results into specific object;
* ADD: Crocoblock/suggestions#5682;
* ADD: Custom image Alt setting for Dynamic Image Crocoblock/suggestions#5551;
* ADD: `Lazy Load` setting for Dynamic Image Crocoblock/suggestions#5522;
* ADD: `Data type` setting for `Equal` condition;
* ADD: dynamic css for inner templates in listing items;
* ADD: sync the relations with translated post (WPML);
* UPD: WC Products properties handling in dynamic image and link widgets;
* FIX: Gutenberg listing breaks layout after filtering;
* FIX: Listing + load more in Gutenberg;
* FIX: Relations - Add New CCT item from related post;
* FIX: Search Filter and users query compatibility;
* FIX: Getting comment meta;
* FIX: Glossary long labels + WPML;
* FIX: Description of the taxonomy doesn't save;
* FIX: CCT listing with filters critical error in some cases;
* FIX: WC Product Query tax_query relation issue.

## 3.0.3.1
* FIX: Elementor Dynamic Visibility conditions processing for some cases;

## 3.0.3
* ADD: New Dynamic Visibility conditions for posts;
* ADD: `CSS ID` setting for listings blocks in the Block Editor;
* ADD: Compatibility with Search Exclude plugin;
* UPD: Ability to hide "Name:" for meta fields in the CPT interface;
* UPD: Some performance optimization for Dynamic Widgets;
* UPD: `jet-engine/query-builder/query/items` hook, allow to filter query results before return items;
* UPD: Hide Empty Terms option is disabled by default for Terms Query;
* UPD: `permission_callback` for maps api;
* FIX: Dynamic Meta widget doesn't display author on single post;
* FIX: %queried_term% macros behavior;
* FIX: JetEngine SQL advanced query gets cached when not supposed to;
* FIX: Compatibility with Elementor 3.7;
* FIX: Minor bug fixes and improvements.

## 3.0.2
* ADD: Allow to create listing for non-public post type;
* ADD: Add to cart source for dynamic link widget;
* ADD: WooCommerce specific dynamic visibility conditions;
* ADD: WC Products In Cart macros;
* UPD: Ability to hide Delete Item button in Relations UI;
* FIX: Better relations and WPML compatibility;
* FIX: Conditions for macros dynamic tags;
* FIX: Load more doesn't work for CCT listing after filtering;
* FIX: Dynamic settings for Order By query arguments;
* FIX: Load more with the Listing Grid on the products page;
* FIX: WC Product Query dynamic data.

## 3.0.1
* ADD: Has terms, Post Type and Term Taxonomy conditions for listing injections;
* ADD: JetFormBuilder Records Query type;
* ADD: Allow to disconnect related items with front-end forms;
* UPD: Better compat the ACF with dynamic blocks;
* UPD: Handle WC product duplication with custom JetEngine taxonomies;
* FIX: Get Store macros and Local Storage store compatibility;
* FIX: JetEngine Proportional callback missing argument;
* FIX: Field width in metabox for users;
* FIX: Prevent error on frontend if table was deleted;
* FIX: Flush object cache to update any post to ensure query builder and object cache compatibility;
* FIX: Process html tags in textarea metafield with quick edit support.

## 3.0.0
* ADD: Allow to select map and geocoding providers for Map Listings;
* ADD: Map meta field type;
* ADD: Allow to output JetEngine Meta Fields in the default WordPress Rest API endpoints;
* ADD: Allow to create new listing item directly from Listing Grid widget in Elementor editor;
* ADD: Geo search query;
* ADD: Allow to register Rest API endpoints to manage relations;
* ADD: Dynamic Visibility for Blocks editor (similar to Elementor);
* ADD: Allow to set context and fallback for macros in Query Builder;
* ADD: Allow to disable legacy Query settings in favor of Query Builder;
* ADD: Allow to edit built-in WordPress taxonomies;
* UPD: various minor bug fixes and improvements.

## 2.11.11
* ADD: An ability to use loading indication for load more;
* ADD: An ability to set custom image size in the Dynamic Image widget;
* ADD: Width, Max Width, Height, Object Fit style options in the Dynamic Image;
* ADD: Post Slug and Term Slug into the object field list;
* ADD: WC Product Field, WC Product Image and WC Product Gallery Dynamic Tags;
* ADD: Allow to print shop messages before WC Query listing;
* UPD: Allow to use macros in Field format option. Macros parsed before do_shortcode calling;
* UPD: Crocoblock/suggestions#5246;
* UPD: Allow to trigger props setup from 3rd party by calling hook 'jet-engine/profile-builder/query/maybe-setup-props';
* UPD: 'jet-engine/rest-api-listings/request/type' filter to change API request type - post or get;
* FIX: Compatibility multiple relation filters with a listing;
* FIX: Flexbox containers compatiblity with listing grid slider;
* FIX: Crocoblock/suggestions#3901;
* FIX: Compat the cct map listings with filters.

## 2.11.10
* ADD: Crocoblock/suggestions#3964;
* ADD: Crocoblock/suggestions#5389;
* ADD: Crocoblock/suggestions#5356;
* ADD: Crocoblock/suggestions#5404;
* ADD: Profile builder content and WPML compatibility;
* ADD: an ability to use dynamic tags for URL Anchor in the Dynamic Link widget;
* ADD: an ability to order terms in the Admin Filters;
* ADD: REGEXP & NOT REGEXP compare operators in the Query Builder;
* FIX: prevent error while updating elementor database;
* FIX: prevent fatal error in Term Field Dynamic Tag;
* FIX: If Query ID is set for Query Builder, than "is_filters_request" match only filters with the same ID;
* FIX: prevent error while updating elementor database;
* FIX: various minor fixes.

## 2.11.9
* ADD: User Page Title to Profile Builder;
* ADD: compat the User Single Page with SEO plugins;
* ADD: webp support for media control;
* ADD: query variable source in the Dynamic Field;
* UPD: Post author ID macro callback to better work with filters;
* UPD: remove empty array for the Relations Dynamic Field props;
* FIX: Crocoblock/suggestions#5261;
* FIX: setup single user page props on filters request;
* FIX: Edit post type/meta box link style in Blocks Editor;
* FIX: gallery field bug;
* FIX: better CCT and Maps Listings compatibility.

## 2.11.8
* ADD: allow to export & import JetEngine CPT & CCT Meta Fields;
* UPD: Display CCT Repeater fields;
* FIX: Dynamic Visibility AND relation;
* FIX: Forms html rendering bug;
* FIX: Update Options form hook - allow to clear data;
* FIX: Human readable time difference callback;
* FIX: various minor fixes and improvements.

## 2.11.7
* ADD: compat with Elementor 3.6 (Listing equal columns height);
* ADD: horizontal layout for checkbox & radio fields in meta boxes;
* UPD: better relations and JSF compatibility;
* UPD: allow to use 3rd party data as source for the calendar widget;
* UPD: better compatibility the lazy load listing with the filters;
* FIX: better WooCommerce compatibility;
* FIX: Query Builder and JSF redirect compatibility;
* FIX: the Load More listing after redirect to prefiltered page (mixed apply type);
* FIX:Edit post type/meta box link style in Blocks Editor;
* FIX: RTL compatibility masonry listing;
* FIX: Apply relations controls labels.

## 2.11.6
* ADD: Crocoblock/suggestions#2599;
* ADD: Query ID option for the Query Builder to better JSF compatibility;
* ADD: escaping a double quote on export cct items to CSV;
* UPD: allow unfiltered output for Dynamic Field with appropriate filter;
* UPD: getting more precise object ID on Dynamic Terms render;
* UPD: allow to update or replace related items from the front-end forms;
* FIX: Replace slider arrows with SVG;
* FIX: Better Calendar listing and Query Builder compatibility;
* FIX: Elementor 3.6 compatibility;
* FIX: prevent PHP errors on posts search;

## 2.11.5
* ADD: allow to duplicate JetEngine instances like CPT, CCT, Taxonomies, Meta Boxes and others;
* UPD: better compat the Forms with WPML and Polylang;
* FIX: better required meta fields processing;
* FIX: correctly get post ID when displaying post_content property;
* FIX: ensure global $product object is set for WC queries and listing;
* FIX: manual set time in the time field;
* FIX: slidesToScroll option for slider listing on tablet devices.

## 2.11.4
* ADD: WC Product properties handling in product listing;
* UPD: refactoring map listing sources;
* UPD: macros registration method in Data Stores module;
* UPD: macros registration method in CCT module;
* UPD: call macros options only when need it;
* FIX: better JSF indexer compatibility;
* FIX: prevent PHP notices on is_valid_timestamp checking;
* FIX: WC object props with params;
* FIX: calendar widget with a few post types;
* FIX: `Exclude Posts` condition for Meta Boxes.

## 2.11.3
* ADD: an ability to get object link by ID in the QR Code callback;
* UPD: allow to create relations for the same objects;
* FIX: compatibility the Dynamic Visibility with Elementor Improved CSS Loading Experiment;
* FIX: prevent jump to top of listing after load more of masonry listing;
* FIX: compat with Elementor Pro 3.6 (registration controls);
* FIX: lazy load listing by queried_user_id on single user page;
* FIX: displaying preview of profile menu widget in Elementor Editor.

## 2.11.2
* ADD: related grandchildren macros;
* ADD: an ability to use Store Count dynamic tag in the Dynamic Visibility;
* UPD: better related items context processing;
* UPD: change AJAX URL for AJAX listing requests;
* FIX: setting custom context for label in the Dynamic Link;
* FIX: inline font icons color;
* FIX: render fallback image for the post thumbnail if post not found;
* FIX: select2 dropdown position on woo pages;
* FIX: prevent php error on change date field format;
* FIX: correct saving listing settings in the Blocks Editor;
* FIX: prevent php notice in the Profile Builder module.

## 2.11.1
* UPD: Better timestamp query arguments processing;
* FIX: Required date meta fields;
* FIX: 3rd party legacy relations registration;
* FIX: Admin filters by related items.

## 2.11.0
* ADD: New Relationships funcitonality;
* ADD: Flexible Container block for the blocks editor;
* ADD: Section block for the blocks editor;
* ADD: URL Anchor setting in the Dynamic Link;
* ADD: an ability to use the Macros tag in the Dynamic Visibility module;
* UPD: allow to select context for the Dynamic Field block in the block editor;
* UPD: Legacy notice into the Froms module info popup;
* UPD: Profile builder and Gutenberg compatibility;
* UPD: mark tabs in use inside the Query Builder;
* FIX: post_titles_by_ids filter if value is string post id;

## 2.10.3
* ADD: better compatibility a query builder listing with taxonomy filters;
* ADD: compatibility with Elementor 3.5;
* ADD: compatibility the Map Listings with JetPopup;
* ADD: Rest API listing settings in Blocks editor;
* ADD: verify nonce in the Form;
* FIX: condition meta field by glossary field;
* FIX: prevent php error if a store name has quotes;
* FIX: prevent php error if a store name or slug has slashes;
* FIX: `update_user` notification in the Form;
* FIX: better data escaping before output in the listing grid;
* FIX: Repeater listing inside WC_Product_Query listing.

## 2.10.2
* ADD: Title Field option for the Repeater Meta Field;
* ADD: an ability to copy repeater item in meta boxes;
* ADD: an ability to collapse repeater items in meta boxes on page load;
* ADD: Author ID parameter to WC_Product_Query;
* ADD: warning tooltip on removing repeater item in meta boxes;
* ADD: base macros class to more refined custom macros registration - https://gist.github.com/MjHead/d1715c7e14547d2d9994a8d8b8913d0e
* FIX: WC_Product_Query id in Data Stores;
* FIX: %current_terms% and %queried_term% macros in WC_Products_Query Tax Query;
* FIX: select2 styles in interface builder module;
* FIX: prevent js error in Elementor Editor;
* FIX: print inner templates styles in listing item on ajax;
* FIX: hierarchy relations.

## 2.10.1
* ADD: support Save as array checkbox fields for terms meta;
* ADD: Post Has Terms condition in the Dynamic Visibility module;
* ADD: updating the user display_name property on update user action;
* ADD: an ability to use shortcodes in email notifications content;
* ADD: an ability to use multiple post types in the Calendar Listing;
* FIX: applying Is checked setting from glossary option;
* FIX: init Data Store Button script;
* FIX: Images gallery slider callback in Blocks Editor;
* FIX: JetSmartFilters Indexer compatibility lazy load with Use as Archive Template;
* FIX: process meta box conditions for non-hiearachical terms.

## 2.10.0
* ADD: New admin conditions for meta boxes;
* ADD: Admin filters for CPT;
* ADD: Quick Edit support for CPT and CCT;
* ADD: Dynamic data funcitonality for blocks editor;
* UPD: compatibility between JetReviews and WC_Product_Query;
* UPD: notice if custom format don't have %s or %1$s in the Dynamic Field;
* FIX: better custom query check for Listing Grid widget;
* FIX: calendar widget with nested listings;
* FIX: ensure indexer correctly works with CCT and filters with multiple keys;
* FIX: better Query Builder and JetSmartFilters compatibility;
* FIX: custom query check for Listing Grid widget;
* FIX: avoid CCTs REST API Create endpoints from updating existing items if `_ID` is presented in the request.

## 2.9.4
* FIX: gallery dynamic tag if value is empty;
* FIX: export CCT items to CSV for available users;
* FIX: prevent js error on init captcha in popup if popup don't have form;
* FIX: calendar widget with nested listings;
* FIX: additional output validation in the Dynamic Field;
* FIX: ensure select, radio and checkboxes fields are processed correctly when registered programmatically;
* FIX: pin view if using global colors in the Map Listing;
* FIX: inline svg icon color in the Map Listing;
* FIX: prevent remove term metadata on quick edit term;
* UPD: compatibility with upcoming JetSmartFilters indexer update;
* UPD: better compatibility glossaries with filters;
* ADD: an ability to use Query Count dynamic tag in the Dynamic Visibility;
* ADD: Icon Size control for the Data Store Button widget;
* ADD: compatibility with SEOPress plugin.

## 2.9.3
* FIX: meta condition with glossary;
* FIX: PHP error in Map Listing if Elementor deactivated;
* FIX: better compatibility with the Hello theme;
* FIX: prevent PHP errors on Elementor data update;
* FIX: init listing slider in Blocks editor;
* FIX: open map marker popup by click on the CCT listing item;
* FIX: Crocoblock/suggestions#3852;
* FIX: current-id macros for WC_Product_Query;
* ADD: Center Mode setting in the Listing Grid Slider;
* ADD: hooks for adding custom arrow icon in the Listing Grid slider;
* ADD: allow to export/import queries and glossaries.

## 2.9.2
* ADD: `Tax Query` to `WC_Product_Query`;
* FIX: preview scripts loading for the Listing grid widget.

## 2.9.1
* ADD: WC_Product_Query meta value sorting;
* ADD: Data Store Button block and widget;
* ADD: Allow to enqueue JetEngine CSS in footer only when JetEngine widgets are used on the page. Controls by `jet-engine/listings/styles-in-footer` hook;
* UPD: Allow to select context for the dynamic values in visibility conditions;
* UPD: Install external modules from api.crocoblock.com instead of account.crocoblock.com;
* FIX: Better PHP 8 compatibility;
* FIX: enqueue JetEngine frontend JS only when listing grid widget used to improve performance;
* FIX: Better form data sanitization and validation.

## 2.9.0
* ADD: Import glossaries from the file;
* ADD: Calculated Columns and Grouping Results for SQL Queries;
* ADD: Advanced mode for the SQL queries to write Query manually;
* ADD: `Current user meta` macros;
* ADD: Query Results for Dynamic Functions;
* ADD: External Modules;
* ADD: Dynamic Tables Module;
* ADD: Dynamic Charts Module;
* ADD: Post Expiration Period Module;
* ADD: Attachment file link by ID Module;
* ADD: Custom visibility conditions Module;
* ADD: Trim string callback Module;
* FIX: various fixes and improvements.

## 2.8.10
* FIX: compatibility with Elementor 3.4;
* FIX: accessibility in the Listing Grid Slider;
* FIX: compatibility a Custom Query Listing with filters on reload.

## 2.8.9
* ADD: an ability to display Date Modified in the Dynamic Field;
* ADD: allow to filter CCT REST API response;
* FIX: better WP 5.8 compatibility;
* FIX: initialize input mask for new repeater items;
* FIX: compatibility with Elementor 3.4;
* FIX: PHP 8 compatibility;
* FIX: compatibility with Events Calendar;
* FIX: unslash glossaries fields before output;
* FIX: Dynamic Field widget Meta Data source with WC Product Query;
* FIX: load more for Custom Query listing.

## 2.8.8
* ADD: `Option value` macros;
* FIX: Dynamic query options in Query builder;
* FIX: Compatibility with multiple sorting filters;
* FIX: Prevent error in form editor if don't have the cct items;
* FIX: Gallery Grid and Gallery Slider with galleries contained image ID;
* FIX: Sorting repeater items in the Data Stores settings;
* FIX: Query Builder and SmartFilters compatibility.

## 2.8.7
* ADD: Edit with JetPlugins admin bar section;
* UPD: Insert/Update CCT and REST API request actions and JetFormBuilder compatibility;
* UPD: Better compatibility with JetSearch.

## 2.8.6
* ADD: Bearer Token authorization type for REST API;
* ADD: an ability to query CCT by random order;
* ADD: an ability to change size of delimiter in the Dynamic Repeater;
* ADD: an ability to change size an icon marker in the Map Listing;
* UPD: Make Dynamic blocks compatible with new Query Loop feature;
* UPD: avoid PHP notices when accessing CCT REST API endpoints;
* FIX: prevent php error on Elementor Data Updater action;
* FIX: `Hide past events` in the Calendar;
* FIX: compatibility the Dynamic image widget with Hello theme;
* FIX: avoid Glossaries options glitch on delete;
* FIX: WC_Product_Query items per page;
* FIX: `Exclude children` tax query option in the Query builder;
* FIX: init forms block on document.ready to ensure WYSIWIG field worked correctly;
* FIX: avoid recursion in the Dynamic field.

## 2.8.5
* ADD: CCT and map listing compatibility;
* ADD: WC Query for Query Builder.

## 2.8.4
* FIX: compatibility the Map Listing with the JetSmartFilters

## 2.8.3
* ADD: allow to calculate Dynamic Functions values by related posts;
* UPD: API method to get meta fields by context;
* FIX: Crocoblock/suggestions#3310;
* FIX: Crocoblock/suggestions#3068;
* FIX: Crocoblock/suggestions#3423.

## 2.8.2
* FIX: PHP 7.0.x compatibility.

## 2.8.1
* ADD: Global settings for actions, additional sidebar plugins, for example `reCAPTCHA v3`;
* ADD: The ability to use global settings in `Mailchimp`, `GetResponse` & `ActiveCampaign` actions;
* ADD: Compatibility with JetForms Add-оns;
* UPD: Allow to show total count and current page count in the Query Count dynamic tag;
* FIX: Prevent accidental deletion of CCT item;
* FIX: Remove mask from empty input on submit the Form;
* FIX: Ensure only CCT item author can edit CCT item with front-end form;
* FIX: Prevent php error in cct preset form.

## 2.8.0
* ADD: Query Builder functionality;
* UPD: Better compatibility CCT and SmartFilters;
* FIX: various fixes.

## 2.7.8
* ADD: `Column Width` control for Checkbox & Radio Fields in the Form;
* FIX: correctly setup the current object.

## 2.7.7
* FIX: additional output validation for some widgets.

## 2.7.6
* ADD: an ability to choose value format for media and gallery meta fields;
* ADD: compatibility with Rank Math and Yoast;
* FIX: additional output validation for some widgets;
* FIX: wisywig field on Options Page;
* FIX: save terms meta;
* FIX: various minor fixes.

## 2.7.5
* ADD: `assign_terms` capability in the Taxonomies component;
* UPD: allow to filter SQL query before dynamic function is applied;
* FIX: lazyload listing on search result page;
* FIX: conditional logic in the form with URL field;
* FIX: saving the date value in timestamp format on Option Page;
* FIX: correct display the value in date control on Option Page;
* FIX: display an icon of widget in elementor context menu;
* FIX: JetSmartFilters pagination + CCT.

## 2.7.4
* ADD: Glossaries functionality;
* ADD: an ability use `REGEX` and `NOT REGEX` options for the Operator control in the Meta Query;
* ADD: convert cyrillic slug of CPT, Taxonomies, Option Page, CCT to latin;
* UPD: help_links in components;
* FIX: prevent php notice in the injections module;
* FIX: avoid letter-casing related errors when checking if DB table is exists;
* FIX: index value in `verbose_repeater` method;
* FIX: default items indents in masonry layout;
* FIX: export/import the Listing items of blocks type;
* FIX: php error on some servers;
* FIX: duplicate print elementor-frontend css on lazyload listing;

## 2.7.3
* ADD: `Include` option to the `Terms Order By` control
* ADD: JS trigger on add/remove items into store
* FIX: Elementor 3.1.2 compatibility issue
* FIX: submitting a form if required media field is hidden

## 2.7.2
* ADD: `Max Zoom` control in the Map Listing
* FIX: compatibility the Map Listing with the JetSmartFilters

## 2.7.1
* FIX: prevent error on adding Listing Grid block without any additional modules activated.

## 2.7.0
* ADD: Dynamic Tag to insert macros in visual way;
* ADD: Query and layout controls fro the Listing Grid block for the Gutenberg editor;
* ADD: Listing Calendar block for the Gutenberg editor;
* ADD: Map Listing block for the Gutenberg editor;
* ADD: Forms block for the Gutenberg editor;
* ADD: Allow to style JetEngine Gutenberg blocks with JetStyleManager plugin;
* FIX: various bug fixes and improvements.

## 2.6.4
* FIX: compatibility the load more listing with filter by checkboxes field
* FIX: remove mask from empty input on submit the Form
* FIX: PHP 8 compatibility
* FIX: wysiwyg field in WP 5.6
* FIX: CCT forms preset processing
* FIX: setup `_listing_type` on create a listing item

## 2.6.3
* ADD: copy to clipboard for shortcode generator;
* ADD: meta viewport for page canvas template;
* ADD: an ability display the Post ID in Admin Columns;
* FIX: User listing and SmartFilters pagination compatibility;
* FIX: wysiwyg field in WP 5.6;
* FIX: work of macros in the Calendar widget;
* FIX: Rest API endpoints and listing creation;
* FIX: compatibility the dynamic visibility module with the map listing module;
* FIX: Getting CCT items from data store;
* FIX: checked radio/checkbox field with custom template.

## 2.6.2
* UPD: JetDashboard Module to v2.0.6;
* UPD: better CCT forms preset processing;
* FIX: saving a date field of term as a timestamp;
* FIX: conditional logic in the Form

## 2.6.1
* ADD: conditional logic to inside repeater in the Form;
* ADD: an ability to disable the marker clustering in the Map Listing widget;
* ADD: RapidAPI auth for REST API listings;
* UPD: better `human_time_diff` callback handling;
* UPD: better compat with WPML;
* UPD: better Gutenberg compatibility;
* FIX: duplicate init elements handlers;
* FIX: save checkbox value inside repeater in the Option Page;
* FIX: prevent error on empty rest API query result.

## 2.6.0
* ADD: Possibility to Differentiate Account Pages by Roles in User Profile Builder;
* ADD: REST API Endpoint Management for CCT;
* ADD: REST API Listings;
* ADD: REST API Request Form Notification Type;
* ADD: Possibility to Add Posts to Data Stores Upon the Post Review;
* ADD: Possibility to "Inject alternative listing items" in last position;
* ADD: ССТ Items Duplication;
* ADD: Allow to automatically generate forms for the CCT items;
* UPD: New UI for Module Management;
* FIX: various fixes and improvements.

## 2.5.6
* ADD: `datetime` field type in the Form module;
* ADD: `post_date`, `post_date_gmt` post properties to save appropriate form fields;
* ADD: `Decimals Point` and `Thousands separator` settings in the Dynamic Functions Tag;
* ADD: `ignore_sticky_posts` in the Listing Grid widget;
* FIX: Getting map popup data with plain permalinks enabled;
* FIX: dynamic value for form fields if user not logged;
* FIX: lazy load listing inside another lazy load listing;
* FIX: prevent CCT DB tables creation error on some MySQL versions;
* FIX: an ability to change size and hover color of svg icon in dynamic widgets;
* FIX: Crocoblock/suggestions#2054;
* FIX: CCT listing with lazy load.

## 2.5.5
* ADD: an ability to use dynamic value for conditions in the Form;
* ADD: 'Is mobile device' condition for Dynamic Visibility module;
* ADD: user listings and Data Stores compatibility;
* UPD: allow to get data in the dynaic widgets depends on selected context (post, current user, current author etc);
* UPD: alt text for images in the Dynamic Image widget;
* FIX: hide the load more button if listing empty;
* FIX: no selection option in dynamic user value in the Form.

## 2.5.4
* ADD: `Hide past events` option to the Calendar widget;
* ADD: Search Query parameter for the Listing Grid widget;
* ADD: CCT and sorting widget compatibility;
* FIX: injecting posts listing settings if enable CCT module;
* FIX: admin area access redirect on AJAX requests.

## 2.5.3
* ADD: Forms - allow to select user role for Register and Update user notifications;
* ADD: Profile Builder – Hide Admin area option;
* FIX: unnecessary slashes and html entities in admin interfaces;
* FIX: macros regex;
* FIX: Lazyload listing grid and a popup inside listing item.

## 2.5.2
* ADD: User Can Add Posts conditions for Profile Builder and Dynamic Visibility modules;
* ADD: Current User Meta option for hidden field value in the Form;
* ADD: allow to process macros in the CCT query;
* ADD: term field and term image dynamic tags;
* UPD: new control to select forms and listings in the appropriate widgets;
* UPD: improve CCT admin columns functionality;
* FIX: forms page break if page has wysiwyg field;
* FIX: duplicate index in the Form builder;
* FIX: macros with listing lazy load.

## 2.5.1
* ADD: Allow to set menu position for CCT;
* FIX: Dynamic Link and Profile Builder pages bug.

## 2.5.0
* ADD: Custom content types module;
* ADD: Lazy load option for the Listing Grid widget;
* ADD: Inifinite scroll options for the Listing Grid widget;
* ADD: Posts restriction rules per CPT;
* ADD: JetEngine Form Builder – Duplicate Fields;
* ADD: Dynamic Visibility Column resize;
* ADD: HTML fields for the options pages;
* UPD: minor improvements and bug fixes.

## 2.4.13
* ADD: dynamic tags support for Dynamic Link widget;
* ADD: allow to add a offset to map coordinates to avoid markers overlapping;
* ADD: an ability disable map controls in the Map Listing widget;
* ADD: content type option for email notification;
* ADD: Get Store dynamic tag;
* ADD: allow process date field as timestamp;
* UPD: allow to add preloader box for the map listing widget;
* FIX: compatibility with Elementor 3.0 (Optimized DOM issue);
* FIX: REST API and session error.

## 2.4.12
* ADD: validate page fields on switching page in the Form;
* ADD: Crocoblock/suggestions#1191;
* ADD: new conditions for Dynamic Visibility module;
* ADD: an ability to set date format to the current date hidden field;
* ADD: allow to use meta values as form values for the select, checkbox and radio fields;
* UPD: allow to preset form fields from options pages;
* FIX: Safari select;
* FIX: compatibility with Elementor 3.0 Crocoblock/suggestions#1823;
* FIX: the yoast meta panel overlapped the datepicker

## 2.4.11
* ADD: better compatibility with WPML (translate admin labels);
* FIX: compatibility with Elementor 3.0 (Repeater issue);
* FIX: profile pages rewrites.

## 2.4.10
* ADD: `Update Options` notification in the Form module;
* ADD: support `Save as array` checkbox fields for options pages;
* ADD: compatibility with upcoming Elementor 3.0 ( Dynamic CSS issue );
* FIX: inserting form repeater field data to user repeater meta;
* FIX: hide the form row if it has visible elements;
* FIX: injection user listing by user meta;
* FIX: better process conditions in combination with calculated fields;
* FIX: forms import and duplicating.

## 2.4.9
* ADD: allow to preset field values on per field basis;
* ADD: allow to open map marker popup by hover on the listing item;
* ADD: indication of active conditional rules for JetEngine forms;
* ADD: rollback to the previous version functionality;
* FIX: maps listing and JetSmartFilters compatibility;
* FIX: error validation form if the hidden specific fields (date, time, email);
* FIX: process PayPal gateway with AJAX forms;
* FIX: prevent attach media file with zero id;
* FIX: `update_user` notification if empty form fields.

## 2.4.8
* FIX: tax query processing.

## 2.4.7
* ADD: inline editing the listing items;
* ADD: allow to output Repeater fields from options as listing grid;
* UPD: allow to change fields label HTML tag for the JetEngine Form fields;
* UPD: better compatibility the Calendar widget with JetPopup;
* UPD: meta_fields column datatype in db.php;
* UPD: JetDashboard Module to v1.0.16;
* FIX: display post content built with elementor in the Dynamic Field widget;
* FIX: open map marker popup by click on the listing item;
* FIX: EXISTS and NO EXISTS options behavior for listing grid query;
* FIX: display Prev Page button on the first page;
* FIX: compatibility with ACF Dynamic Tags(Elementor Pro);
* FIX: Crocoblock/suggestions#1541;
* FIX: get marker coordinates by lat and lng fields combined with "+";

## 2.4.6
* UPD: allow to filter calculation formula.

## 2.4.5
* ADD: export-import the Options Pages;
* UPD: calculate values logic in the forms module;
* FIX: prevent form submitting error if it has only redirect notification;
* FIX: with front settings for post types and taxonomies.

## 2.4.4
* FIX: Dates comparing in the Dynamic Visibility widget;
* FIX: Dynamic capabilities;

## 2.4.3
* FIX: revert breaking changes.

## 2.4.2
* UPD: allow export, import and duplicate JetEngine forms;
* UPD: add dynamic capabilities for some posts query controls for the Listing Grid widget;
* FIX: media field value encoding.

## 2.4.1
* FIX: better JetPopup compatibility;
* FIX: setup postdata for nested listings;
* FIX: handle form fields after repeater;
* FIX: remove/restore required attributes when fields is hide/show by coinditions.

## 2.4.0
* ADD: Data Stores module;
* ADD: Dynamic Visibility module for Sections/Columns/Widgets;
* ADD: Ability to create repeater groups in the Forms;
* ADD: Conditional logic for the meta fields;
* ADD: Conditional logic for the fields in the Forms module;
* ADD: Ability to delete posts from the front end;
* ADD: Ability to set the Input mask for the text fields in the Forms module;
* ADD: Ability to set the maximum number of user-generated posts depending on a user (a Profile Builder setting);
* ADD: Support of the Select, Radio, and Checkbox fields in the Repeater meta field;
* ADD: Support of the Number field type for the meta fields;
* ADD: Random number of posts in the Listing Grid;
* ADD: Automatic conversion of Cyrillic to the Latin alphabet in the Name meta field;
* FIX: Prevent duplication of the meta field names;
* FIX: Support of SVG images preview in the Media meta field;
* FIX: Various bug fixes.

## 2.3.7
* ADD: compatibility the related posts with WPML;
* FIX: checking map markers conditions;
* FIX: wysiwyg editor in the forms and popup compatibility;
* FIX: JetEngine listings and JetPopup compatibility;
* FIX: compatibility Dynamic CSS with the User listing;
* FIX: Layout option for Checkbox & Radio Fields in the Form widget;
* FIX: the repeater listing inside the Grid listing;
* FIX: unregister meta boxes for deleted post types and taxonomies;
* FIX: Crocoblock/suggestions#1332.

## 2.3.6
* ADD: allow to open map marker popup by click on the listing item;
* UPD: allow to correctly get data from Posts relations in the form preset;
* UPD: new datetimepicker control for date time field;
* UPD: add separate controls for Geocoding API key in the Maps Listings module;
* FIX: ensure injection listing grid CSS rendered on AJAX requests;
* FIX: Crocoblock/suggestions#1143 on the frontend;
* FIX: an ability to edit another user's post through a Form with the `edit_others_posts` capability.

## 2.3.5
* FIX: new map markers options and JetSmartFilters compatibility.

## 2.3.4
* ADD: allow to set multiple map marker types by conditions;
* UPD: aligments controls in the Dynamic field widget;
* UPD: alignments controls in the Dynamic field widget;
* FIX: Crocoblock/suggestions#1149;
* FIX: Crocoblock/suggestions#1155;
* FIX: creating the Blocks listing item without active Elementor;
* FIX: prevetn PHP notice in the Custom Field Tag;
* FIX: Crocoblock/suggestions#1143;
* FIX: if all checkboxes unchecked, this meta value was not empty;
* FIX: prevent form layout break on form quick editing;
* FIX: injection listings conflict;
* FIX: prevent PHP notices from taxonomies manager.

## 2.3.3
* ADD: %queried_user_id% macros to get user ID at the author pages, profile builder user pages;
* UPD: allow to get address from multiple meta fields for Maps module;
* UPD: allow to set custom capability type for new taxonomies;
* FIX: correctly get data for queried user context in the User Field and User Image dynamic tags;
* FIX: capabilities check on term meta saving.

## 2.3.2
* UPD: allow to set post ID for meta fields in the shortcodes generator;
* FIX: allow to set empty value for select option in the meta fields;
* FIX: missing line breaks in meta after update from form WYSIWYG field;
* FIX: Crocoblock/suggestions#1123.

## 2.3.1
* UPD: allow to query posts by types and status in the dynamic function tag;
* UPD: ensure HTML attributes is correctly reset on form elements render;
* FIX: form styles;
* FIX: prevent PHP notices when saving built-in post types.

## 2.3.0
* ADD: Dynamic Function Tag for Elementor;
* ADD: Maps Listings;
* ADD: Repeater type meta fields display in the Listing Grid;
* ADD: Custom template creation for Checkbox and Radio meta fields within the Form’s functionality;
* ADD: An opportunity to add custom values to Radio and Checkbox meta fields types from the editing page of Post/ Taxonomy/ Options Page;
* ADD: New notification types for Forms: MailChimp and GetResponse;
* ADD: Crocoblock/suggestions#914;
* ADD: License check for updates;
* UPD: Forms module. Current Post Author Name & Current User Name values for hidden fields;
* UPD: Allow to change form pages on field value change;
* UPD: Media field type in the Forms functionality refined;
* UPD: Add with_front option for Post Type and Taxonomy edit screens;
* UPD: Allow to add custom query arguments to redirect URL in the form redirect notification;
* FIX: Crocoblock/suggestions#604;
* FIX: Dynamic bg for listing items;
* FIX: Prevent PHP notices in some cases;
* FIX: Various minor fixes.

## 2.2.7
* ADD: compatibility a Term Grid listing with JetPopup;
* UPD: allow to set top and bottom positions for dynamic link icon;
* UPD: allow to reset field appearance;
* UPD: checked values list callback re-factoring;
* UPD: parse the options list from meta field for the Form module;
* FIX: minor form style;
* FIX: Safari select compatibility;
* FIX: ensure listing grid CSS rendered on AJAX requests;
* FIX: missing line breaks in the meta field of the textarea after saving;
* FIX: calculating process if a checkbox has only one field;
* FIX: prevent fatal errors if maybe_set_current_object is called without some of required arguments.

## 2.2.6
* ADD: support for color dynamic tags category;

## 2.2.5
* ADD: support `Save as array` checkbox fields for user meta;
* UPD: allow to correctly setup relations with front-end forms;
* UPD: better RTL compatibility;
* UPD: better compatibility with Dynamic CSS;
* FIX: saving settings on the options page with specific access capability;
* FIX: https://github.com/Crocoblock/suggestions/issues/1008;
* FIX: layout option for checkbox and radio fields in the Form.

## 2.2.4
* FIX: prevent PHP error in the forms;
* FIX: missing dynamic BG properties for a elements inside a listings.

## 2.2.3
* FIX: Elementor 2.9 compatibility;
* FIX: prevent PHP error on archive templates;
* FIX: add User URL to allowed user fields;
* FIX: minor fixes.

## 2.2.2
* UPD: Show form when preset is configured but no source was found;
* FIX: Some issues related to nested listings;
* FIX: Required value for wysiwyg field;
* FIX: JetPopup compatibility;
* FIX: Profile menu on mobile devices;
* FIX: Dynamic bg for listing items.

## 2.2.1
* UPD: Form Module. Prevent form from display if current user haven't access to configured preset data;
* UPD: Form Module. Minor improvements for insert/update post notification;
* FIX: Form Module. Set post terms for checkbox field;
* FIX: Listings. Current object set up and reset for listing items.

## 2.2.0
* ADD: Profile Builder module;
* ADD: Allow to preset form values from post data, user data or query varaibles;
* ADD: Update User notification;
* ADD: Presets logic;
* ADD: Media field for from module;
* ADD: Range field for from module;
* ADD: Heading field for from module;
* ADD: Page breaks for form module;
* ADD: ActiveCampaign for form module;
* ADD: Relations hierarchy;
* ADD: Listing Grid block for Gutenberg editor;
* UPD: Allow to update posts for Insert/Update Posts notification;
* UPD: Minor updates of Meta Fields editor UI;
* UPD: Object properties selector now contains list of all avaialble properties of all sources;
* FIX: Various bug fixes and minor enhancements.

## 2.1.4
* FIX: parent/child options pages registration.

## 2.1.3
* FIX: re-initialize widgets scripts on Listing Grid load more click.

## 2.1.2
* FIX: multiday events display in Calendar widget;
* ADD: %current_meta_string% macros to correctly get stringified arrays from meta fields.

## 2.1.1
* FIX: equal columns height for Listing Grid widget;
* FIX: allow to keep hover effects with click-able listing grid items.

## 2.1.0
* ADD: Allow to make whole listing item clickable;
* ADD: Load More for Listing Grid widget;
* ADD: Allow to inject alternative listing items into listing grid;
* ADD: Allow to create meta fields for users;
* ADD: Allow to set default values for meta fields;
* ADD: Allow to create Listing items in Blocks (Gutenberg) editor;
* ADD: Allow to use multiple orderby;
* UPD: Allow to query posts by multiple pot types in Listing Grid;
* UPD: Allow to order posts by multiple meta clauses;
* UPD: Allow to set fallback values for Dynamic Field widget;
* UPD: Allow to add counter for items in Dynamic Repeater widget;
* FIX: Various fixes.

## 2.0.3
* ADD: Do Shortcode callback for Dynamic Field filters;
* FIX: Rewriting document type on listing item autosave in Elementor editor;

## 2.0.2
* FIX: Taxonomies meta fields registration;

## 2.0.1
* FIX: better WooCommerce + JetWooBuilder compatibility;
* FIX: Post types and taxonomies labels saving;
* FIX: Meta fields visibility in Elementor widgets controls.

## 2.0.0
* ADD: Admin UI/UX: new admin UI;
* ADD: Admin UI/UX: allow to edit built-in post types;
* ADD: Admin UI/UX: allow to delete/reattach created posts (terms) on post type (taxonomy deletion);
* ADD: Admin UI/UX: allow to change type of already created posts/terms on post type/taxonomy slug update;
* ADD: Admin UI/UX: allow to make admin columns sortable;
* ADD: Admin UI/UX: allow to select from predefined callbacks for admin columns;
* ADD: Admin UI/UX: allow to group created fields into tabs/accordions;
* ADD: Admin UI/UX: allow to set max length and required for created fields where is possible;
* ADD: New feature: global options pages;
* ADD: Listing Calendar: showcase multiday events;
* UPD: Admin UI/UX: allow to sort notifiactions in forms;
* UPD: Listing Calendar: allow to set default month;
* UPD: Elementor widgets: compatibility with new Elemntor icons control;
* UPD: Elementor widgets: show all existing fields in Elemntor widgets controls;
* UPD: Elementor widgets: added callback for filtering switcher meta fields;
* FIX: Prevent errors if some of dates in calendar posts is not timestamp;
* FIX: Posts control in repeater;
* FIX: Various fixes and performance improvements.

## 1.4.8
* FIX: Elementor Pro 2.6+ compatibility.

## 1.4.7
* FIX: Better ACF compatibility;
* ADD: JS trigger on From wdget init;
* ADD: PHP hooks to add own fields into forms builder.

## 1.4.6.1
* UPD: allow to use P tag in dynamic field widget;
* FIX: Elementor 2.6.x compatibility.

## 1.4.6
* FIX: Booking form fields render.

## 1.4.5
* ADD: Masonry layout for listing grid widget;
* FIX: Better ACF compaibility.

## 1.4.4
* ADD: `jet-engine/forms/booking/notifications/fields-after` hook;
* UPD: Allow to filter form field template path;
* UPD: Store inserted post ID into form data to allow process it by other notifications.

## 1.4.3
* FIX: Prevent errors in Listing grid widget;
* FIX: Macros regex.

## 1.4.2
* ADD: macros for related posts between two post types;
* ADD: reCAPTCHA v3 validation for booking forms;
* ADD: JetEngine gallery dynamic tag - https://github.com/CrocoBlock/suggestions/issues/120;
* ADD: Allow to add descriptions for meta fields - https://github.com/CrocoBlock/suggestions/issues/134;
* ADD: render_acf_checkbox filter for Dynamic Repeater widget items
* UPD: https://github.com/CrocoBlock/suggestions/issues/250;
* UPD: https://github.com/CrocoBlock/suggestions/issues/213;
* FIX: Fade animation for listing grid slider;
* FIX: htps://github.com/CrocoBlock/suggestions/issues/183;
* FIX: Prevent shrink `post` control;
* FIX: Prevent php error when `$elementor_data` is array;
* FIX: `grid-items` container width;
* FIX: prevent errors on posts with relations saving;
* FIX: Dynamic Link widget, Icon Gap option visibility.

## 1.4.1
* FIX: Date- and timepicker styles;
* FIX: Listing Grid slider behavior when found posts < number slides to show;
* FIX: Start of month and prev month dates calculations in Calendar widget;
* FIX: Better check for an empty results in Dynamic Field widget.

## 1.4.0
* ADD: `Register user` notification for booking form;
* ADD: `Call a Webhook` notification for booking form;
* ADD: `Field Visibility` option for Booking form fields;
* ADD: `Post status` option for Insert post notification in Booking form;
* ADD: Allow to use dynamic popups from JetPopup inside listing and calendar;
* ADD: Mobile settings for image gallery slider in Dynamic Field widget;
* ADD: Allow to set meta field width;
* UPD: Allow to showcase listing grid as slider;
* UPD: Allow to set equal columns height for listing grid widget;
* UPD: Cross-browser Date- and Timepicker for meta fields;
* FIX: Allow to use shortcode in Not Found Message control;
* FIX: Mobile columns in Listing Grid widget.

## 1.3.2
* ADD: Post status control for Listing Grid Widget;
* UPD: Gallery slider init for Dynamic Field widget;
* UPD: Move meta boxes registration to admin_init hook
* FIX: Better ACF compatibility;
* FIX: Timestamp validation for Format Date callback in Dynamic Field widget;
* FIX: Jet Smart Filters compatibility for Listing Grid Widget when archive query option enabled;
* FIX: Custom meta fields list in Dynamic Repeater Widget;

## 1.3.1
* ADD: 'Get post/page link (linked post title)' callback for Dynamic Field widget;

## 1.3.0
* ADD: Booking Forms functionality;
* FIX: %current_terms% macros processing for terms query;

## 1.2.6
* UPD: Responsive controls for gaps in Listing Grid widget;
* FIX: Better compatibility with JetSmartFilters;
* FIX: Processing datetime meta;

## 1.2.5
* ADD: Better WPML compatibility;
* UPD: Listing returns not found when using related posts macros and no related posts were found;
* FIX: Show in nav menu control for Post Types and Taxonomies;
* FIX: Make relations compatible with PODs;
* FIX: Prevent errors with Elementor 2.4.0;
* FIX: Customize field output in combination with Related posts list callback;
* FIX: Delimiter for checkboxes values

## 1.2.4
* ADD: Admin columns management;
* ADD: Text before and Text after controls for Dynamic terms widget;
* ADD: Allow to enable/disable link on term in Dynamic terms widget;
* ADD: 'queried_term' macros;
* FIX: months translation in Calendar grid widget

## 1.2.3
* ADD: RU localization

## 1.2.2
* ADD: rel and target controls to Dynamic Image link;
* ADD: allow to add meta boxes only for specific posts/pages;
* FIX: Checkbox meta fields processing.

## 1.2.1
* FIX: Allow to use HTML tags in textarea meta field;

## 1.2.0
* ADD: Calendar widget;
* ADD: Posts relationships;
* ADD: Columns gap for Listing Grid widget;
* ADD: 'QR Code' callback to filter Dynamic Field output;
* ADD: 'Format number' callback to filter Dynamic Field output;
* ADD: Date query controls for Posts query settings in Listing Grid widget;
* FIX: Dynamic image tag.

## 1.1.3

* ADD: 'Embed icon' callback for Filter field output option in Dynamic field widget;
* ADD: %current_terms% and %current_meta% macros;
* UPD: Allow to use shortcodes inside Field format control in Dynamic field widget;
* FIX: Prevent JavaScript errors in some cases;
* FIX: Correctly enqueue assets for multiple meta boxes on page.

## 1.1.2

* ADD: Allow to filter posts query arguments;
* ADD: 'Get value from query variable' control for Meta Query in Listing Grid Widget;
* ADD: Allow to use macros in Get terms of posts control in Listing grid widget;
* UPD: Prepare for JetSmartFilters plugin;
* FIX: Current object reset after listing.

## 1.1.1

* ADD: Listing Grid visibility options;
* FIX: Hook name for filtering macros and filters lists;
* FIX: Applying macros in Meta Query values.

## 1.1.0

* ADD: Allow to filter values in repeater items;
* ADD: Gallery meta field;
* ADD: %current_id%, %current_tags% and %current_categories% macros;
* ADD: WYSIWYG meta field;
* ADD: Slider and grid output for gallery meta field;
* ADD: New options for Dynamic Repeater widget;
* ADD: New opitons for Dynamic Link widget;
* ADD: Embed URL callback;
* ADD: Allow to use dynamic terms images on taxonomy archives;
* UPD: Allow to set and output multiple values for select field;
* FIX: Prevent errors on archive pages;
* FIX: Meta boxes registration;
* FIX: Meta fields parsing for dynamic widgets options.

## 1.0.0

* Initial release;

