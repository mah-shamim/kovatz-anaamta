# ChangeLog

## [2.1.10](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.10.zip)
* Fixed: JetEngine Query Builder empty post type issue;
* Fixed: JetEngine Query Builder functionality in widgets when JetEngine missing;
* Fixed: Widget sale badge visibility with Astra theme;
* Fixed: Single Image widget view in quick view popup;
* Fixed: Add to Cart AJAX RTL issue; 

## [2.1.9](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.9.zip)
* Updated: Single Add to Cart quantity margin control selector;
* Fixed: Remove icons display in Cart Table widget;
* Fixed: Custom templates view in Elementor editor with Astra theme.

## [2.1.8](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.8.zip)
* Added: `'jet-woo-builder/template-functions/stock-status'` filter hook to control stock status output;
* Added: Ability to use shop and single product widgets in elementor pro templates ([#6881](https://github.com/Crocoblock/suggestions/issues/6881));
* Updated: WooCommerce related templates;
* Updated: Vue UI module to 1.4.10;
* Fixed: PHP 8.1+ deprecations;
* Fixed: documents type rewrite & styles enqueue;  
* Fixed: Price opacity styles issues in loop widgets.

## [2.1.7.3](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.7.2.zip)
* Fixed: Minor fixes.

## [2.1.7.2](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.7.2.zip)
* Fixed: Security issue.

## [2.1.7.1](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.7.1.zip)
* Updated: JetDashboard to 2.1.4.

## [2.1.7](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.7.zip)
* Fixed: Wishlist, Compare, Quick view buttons click with clickable item functionality ([#6887](https://github.com/Crocoblock/suggestions/issues/6887));
* Fixed: Single Add to Cart Button widget issues;
* Fixed: Single Sale Badge widget default styles;
* Fixed: Styles enqueue.

## [2.1.6.1](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.6.1.zip)
Fixed: Change archive items render method for Elementor to avoid errors in the editor ([#7003](https://github.com/Crocoblock/suggestions/issues/7003)).

## [2.1.6](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.6.zip)
* Added: Vertical position for Categories Grid widget count element in Preset 1 ([#6347](https://github.com/Crocoblock/suggestions/issues/6347));
* Added: Custom size controls for Single Sale Badge widget ([#6696](https://github.com/Crocoblock/suggestions/issues/6696));
* Added: Additional price style controls for Product Grid/List widgets ([#6786](https://github.com/Crocoblock/suggestions/issues/6786));
* Tweak: Custom taxonomy template settings display for different object types;
* Tweak: Styles enqueue;
* Tweak: Admin notices;
* Fixed: Fixed: Display empty cart page without Empty Cart Message widget ([#6669](https://github.com/Crocoblock/suggestions/issues/6669));
* Fixed: Single Add to Cart quantity selector view for last product ([#6949](https://github.com/Crocoblock/suggestions/issues/6949));
* Fixed: Dynamic tags in Archive Category item template;
* Fixed: some Blocksy theme compatibility;
* Fixed: PHP 8.1+ compatibility issues.

## [2.1.5](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.5.zip)
* Added: `Exclude by ID` control for Show All query in Categories Grid widget;
* Added: My Account Details widget Fieldset element margin control;
* Updated: WooCommerce templates;
* Tweak: Better Kava theme compatibility;
* Fixed: Exclude taxonomies functionality in Taxonomy Tiles widget;
* Fixed: PHP 8.2 deprecation errors.

## [2.1.4](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.4.zip)
* Added: Integration with Woocommerce High-Performance Order Storage;
* Added: Linked products query type for Elementor Single Product template;
* Added: Elementor 3.10 compatibility with custom size unit;
* Fixed: Disabled carousel arrow appearance [#6496](https://github.com/Crocoblock/suggestions/issues/6496);

## [2.1.3](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.3.zip)
* Added: `jet-woo-builder/post-type/args` hook for JetWooBuilder post types `$args` manipulation;
* Updated: Speed of automatic AJAX cart update;
* Updated: Carousel dynamic bullets transition;
* Updated: Better Astra theme compatibility;
* Tweak: Better WPML compatibility;
* Fixed: Carousel dynamic bullets appearance;
* Fixed: Wrong grid display when use archive items template on products archive page;
* Fixed: Products widgets display inside JetEngine listing item.

## [2.1.2.1](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.2.zip)
* Fixed: Products widgets permalink issue;
* Fixed: Minor styles issue;

## [2.1.2](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.2.zip)
* Added: Product Variation query for Products Grid/List widgets;
* Added: Controls for vertical position for badges in Products Grid/List widgets;
* Updated: Shortcode functionality refactor and restructuring;
* Updated: Editor control UX;
* Updated: JetDashboard to 2.0.10;
* Fixed: Checkout Order Review widget spacing issues;
* Fixed: Blocksy theme compatibility;

## [2.1.1](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.1.zip)
* Added: Better compatibility with JetProductGallery;
* Added: `jet-woo-builder/woocommerce/billing-fields` and `jet-woo-builder/woocommerce/shipping-fields` for checkout fields editor compatibility with third-party plugins;
* Added: box-shadow control for form fields in some widgets;
* Updated: WC rewritten templates;
* Updated: Billing Form widget selectors;
* Fixed: JetSmartFilter pagination functionality with widgets that use JetEngine custom query;
* Fixed: Some UI editor issues;
* Fixed: Empty sale flash on the grouped product when macros used;

## [2.1.0](https://github.com/ZemezLab/jet-woo-builder/archive/2.1.0.zip)
* Added: Product not found message templates in Products Grid/List widgets;
* Added: Dynamic tags functionality for not found message and Query ID controls in Products Grid/List widgets;
* Added: Line Wrap control for some widgets titles;
* Added: JS trigger `jet-woo-builder-swiper-initialized` after Swiper slider init;
* Added: Archive items clickable item functionality;
* Added: Badges to Products List widget;
* Added: Dynamic bullets functionality for the carousel;
* Added: Single Add to Cart widget view cart link;
* Updated: WPML widgets translatable nodes;
* Updated: Single Rating widget;
* Updated: Categories Grid widget;
* Updated: WC templates;
* Fixed: WC notice after single ajax add to cart;
* Fixed: Cart table widget actions row display;
* Fixed: Slides per group bug in the carousel;
* Fixed: Single form stars rating display in the editor;
* Fixed: Price styles in Products Grid widget;
* Fixed: Wishlist custom template option wrong index;
* Fixed: WC query after template switching;
* Fixed: Template type check with active Elementor Pro;
* Fixed: Elementor Pro Custom fonts with Archive items templates;
* Fixed: JetEngine compatibility error;

## [2.0.5](https://github.com/ZemezLab/jet-woo-builder/archive/2.0.5.zip)
* Updated: Allow accessing JetWooBuilder JS object from the global scope;
* Tweak: Better WPML compatibility;
* Tweak: Better Astra theme compatibility;
* Fixed: Empty favourites query;
* Fixed: My account controls ID conflict;
* Fixed: Product variation metadata output in Cart Table widget;

## [2.0.4](https://github.com/ZemezLab/jet-woo-builder/archive/2.0.4.zip)
* Added: Favourites query type for Product Grid/List widgets;
* Added: New query type Stock Status for Products Grid/List widgets;
* Tweak: Custom stock status functionality in Products Grid/List widgets;
* Fixed: Custom taxonomy template functionality works separately from shop template functionality;
* Fixed: Ajax add to cart spinner styles;
* Fixed: Carousel render with overflow option;

## [2.0.3](https://github.com/ZemezLab/jet-woo-builder/archive/2.0.3.zip)
* Fixed: Products Grid badges align;
* Fixed: Archive Rating widget missing global `$product` variable;
* Fixed: Compatibility with Elementor 3.7;
* Fixed: Additional minor bugs;

## [2.0.2](https://github.com/ZemezLab/jet-woo-builder/archive/2.0.2.zip)
* Fixed: Products Grid widget box-shadow controls visibility;

## [2.0.1](https://github.com/ZemezLab/jet-woo-builder/archive/2.0.1.zip)
* Added: `jet-woo-builder/widgets/archive/pre-product-price` hook to rewrite archive product price output;
* Fixed: Themes compatibilities issues;
* Fixed: Variation product single AJAX add to cart;
* Fixed: Carousels pagination styles section visibility;
* Fixed: Display Archive widgets in JetEngine listing;

## [2.0.0](https://github.com/ZemezLab/jet-woo-builder/archive/2.0.0.zip)
* Added: Woo Page Builder template tabs;
* Added: WooCommerce placeholder image handling in widgets;
* Added: Single Rating widget reviews link captions;
* Added: Style & Advanced tabs to custom templates in Elementor editor;
* Added: Single Template Elementor editor settings;
* Added:  Style controls for Single Tabs widget;
* Added: Empty rating functionality in Products Grid/List widgets;
* Added: Products List & Categories Grid widgets clickable item functionality;
* Added: Products List widget content elements align controls;
* Added: Manual selection of categories in Categories Grid widget;
* Added: Archive items templates editor canvas view;
* Added: Responsiveness in checkout forms;
* Added: WooCommerce action widget;
* Added: Dynamic & macros functionalities for IDs fields in Products List/Grid widgets;
* Updated: Better JetEngine compatibility;
* Updated: Better Elementor 3.6 compatibility;
* Updated: Widgets controls and templates;
* Updated: Better Blocksy theme compatibility;
* Updated: JetDashboard to 2.0.9;
* Tweak: Widget accessibility for different template types;
* Tweak: Single Add to Cart widget styles;
* Tweak: Single Images widget;
* Tweak: Single Meta widget;
* Tweak: Refactor and restructure compatibility, integration, components and predesigned files and folders;
* Fixed: Cart Table widget thumbnail width;
* Fixed: Quick view popup with OceanWP theme.

## [1.12.4](https://github.com/ZemezLab/jet-woo-builder/archive/1.12.4.zip)
* Added: Products Grid&List compatibility with `woocommerce_product_is_visible` hook;
* Tweak: Remove button color styles in Cart Table widget;
* Fixed: Archive items equal column height.

## [1.12.3](https://github.com/ZemezLab/jet-woo-builder/archive/1.12.3.zip)
* Dev: `jet-woo-builder/documents/is-document-type` hook for checking document type;
* Dev: `jet-woo-builder/integration/register-widgets` hook for widget registration;
* Fixed: Editor widgets rendering.
* Fixed: Astra theme checkout page compatibility.

## [1.12.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.12.2.zip)
* Tweak: WooCommerce templates versions;
* Fixed: Frontend carousel rendering with slide overflow;
* Fixed: Minor bugs.

## [1.12.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.12.1.zip)
* Added: Cart Table widget auto update functionality;
* Tweak: Products List/Grid & Single Product AJAX add to cart styles;
* Tweak: Purchase Popup functionality;
* Fixed: Cart & Checkout elements float styles;
* Fixed: Close quick view popup after adding product to cart;
* Fixed: Carousel rtl issue;
* Fixed: Elementor templates export compatibility with new Export/Import functionality;
* Fixed: Update cart button mobile visibility.

## [1.12.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.12.0.zip)
* Added: Cart Table widget auto update functionality;
* Added: Separate box sizes controls for Carousel Dots;
* Added: Minimum touchmove velocity control for Carousels;
* Added: Simulation switching control for Carousels;
* Added: Carousels slide overflow and space between functionalities;
* Added: Export/Import templates;
* Updated: Cart, Checkout, My Account and Thank You templates widgets;
* Updated: WooCommerce templates;
* Updated: Body classes and wrappers for custom templates in Elementor editor;
* Updated: Assets and Templates folders structure;
* Updated: Admin templates;
* Updated: Carousels styles & styles controls;
* Tweak: Templates styles;
* Tweak: Quantity input script handling;
* Tweak: Templates switcher script handling;
* Tweak: Product thumbnails attributes implementation;
* Fixed: Checkout forms rtl compatibility;
* Fixed: Thumbnail effect in Wishlist archive product card;
* Fixed: Integrated themes compatibility;
* Fixed: Elementor db update critical error;
* Fixed: Thumbnails tablet & mobile visibility in Cart Table widget;
* Fixed: Billing and Shipping Forms widgets address fields require property;
* Fixed: SVG icons colors in carousels controls.

## [1.11.4](https://github.com/ZemezLab/jet-woo-builder/archive/1.11.4.zip)
* Updated: Better Blocksy theme compatibility;
* Fixed: Carousel issue;
* Fixed: Custom Query widgets compatibility with JetSmartFilters indexer;
* Fixed: Some additional bugs.

## [1.11.3](https://github.com/ZemezLab/jet-woo-builder/archive/1.11.3.zip)
* Fixed: Minor bugs.

## [1.11.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.11.2.zip)
* Fixed: Archive Template inside wishlist widget;
* Fixed: Reset Archive Template object;
* Fixed: Some additional bugs.

## [1.11.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.11.1.zip)
* Fixed: Single Images widget width control units.

## [1.11.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.11.0.zip)
* Added: Custom labels visibility controls for Shipping Form and Empty Cart Message widgets;
* Added: Proper WooCommerce classes to editor templates;
* Added: Possibility to choose the icon for Cart Table remove button;
* Added: Bestsellers query to Products Grid/List widgets;
* Updated: Single Images widget;
* Updated: Default WooCommerce templates styles;
* Updated: Better WPML compatibility;
* Tweak: `get_product_thumbnail()`;
* Tweak: Carousels pagination styles;
* Tweak: Cart Totals widget;
* Fixed: Conflict between Shipping Form widget heading styles and Elementor Global styles;
* Fixed: Carousel columns styles;
* Fixed: `$wp_query->queried_object` in archive card templates;
* Fixed: Product Grid clickable item compatibility with JetEngine Query Builder;
* Fixed: Checkout forms custom address fields classes.

## [1.10.5](https://github.com/ZemezLab/jet-woo-builder/archive/1.10.5.zip)
* Added: Blocksy theme integration;
* Updated: Single Product widgets templates;
* Updated: Quantity input form;
* Updated: JetAdminBar 1.0.2;
* Tweak: Output on not found message in Products Grid/List widgets;
* Tweak: Cart table coupon form;
* Fixed: Cart tables width;
* Fixed: Custom query functionality without JetSmartFilters;
* Fixed: Columns for the carousel in the editor;
* Fixed: Elementor Motion Effect with archive card templates.

## [1.10.4](https://github.com/ZemezLab/jet-woo-builder/archive/1.10.4.zip)
* Added: Alignment for Single Add to Cart Widget & hidden input styles;
* Tweak: Products Grid/List widgets button template Archive/Single Add to Cart widgets open popup after add to cart functionality;
* Tweak: Cart Totals & Table widgets;
* Tweak: Price styling;
* Tweak: Cart Cross Sell widgets styling controls;
* Fixed: Editor document type;
* Fixed: Product content template;
* Fixed: Grid styles.

## [1.10.3](https://github.com/ZemezLab/jet-woo-builder/archive/1.10.3.zip)
* Added: Compatibility with Elementor Custom Breakpoint;
* Added: Link colors controls for Checkout Order Review widget cells;
* Added: Template layout applied in Elementor editor;
* Tweak: Checkout Additional Form widget label field handling;
* Tweak: Single Images widget thumbnail padding controls;
* Tweak: Cart Totals & Checkout order Review widgets border styles;
* Fixed: Variation select display in Safari browser;
* Fixed: Checkout field manager classes.

## [1.10.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.10.2.zip)
* Fixed: Product Grid/List Query conditions.

## [1.10.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.10.1.zip)
* Updated: Cross-sell query in Products Grid/List widgets in Cart template;
* Fixed: Default addresses in Checkout templates forms;
* Fixed: Archive Product image & title permalinks in JetEngine listing with WC Product Query;
* Fixed: Cart Totals Widget mobile heading styles;
* Fixed: Empty cart template container width.

## [1.10.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.10.0.zip)
* Added: Custom field functionality to Cart Table widget;
* Added: Fields manager for Checkout Billing & Shipping forms;
* Added: JetAdminBar module;
* Updated: Billing & Shipping forms widgets templates;
* Updated: JetElementorExtension module to 1.0.5;
* Fixed: Grid widgets breakpoints styles;
* Fixed: Cart table template part;
* Fixed: Quick view popup closing after a product added to cart.

## [1.9.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.9.2.zip)
* Added: Stock Status order to Products Grid/List widgets;
* Added: Currency sign separate style controls in Checkout Order Review widget;
* Tweak: Products Grid widget sale badge styles;
* Tweak: Products Grid widget clickable item functionality;
* Fixed: WooCommerce default product sorting after template switching;
* Fixed: Dynamic tags bg for builder pages;
* Fixed: Single Tab widget hover & active colors;
* Fixed: Default thank you page template functionality;
* Fixed: `the_content()` not found when product edited with elementor;
* Fixed: Single Add to Cart widget quantity input positioning.

## [1.9.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.9.1.zip)
* Fixed: elementor-preview template;
* Fixed: Astra theme woocommerce container width;

## [1.9.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.9.0.zip)
* Added: Compatibility with JetEngine Query Builder query in Products List/Grid widgets;
* Added: Controls for templates layout;
* Added: Products List widget compatibility with `woocommerce_hide_out_of_stock_items` option;
* Added: Cells width controls for Single Attribute widget;
* Updated: Register widgets category;
* Updated: Theme integration styles;
* Updated: Optimize theme integration files;
* Updated: Refactor WooCommerce part templates code;
* Tweak: Single Product template type;
* Tweak: Layout switcher ajax request;
* Fixed: WooCommerce pages container styles;
* Fixed: Cart popup opening;
* Fixed: JetEngine dynamic tags for archive cards;
* Fixed: Archive Products widgets compatibility with listing grid that uses WC_Product_Query.

## [1.8.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.8.2.zip)
* Updated: Better rtl compatibility;
* Fixed: Single AJAX add to cart;
* Fixed: Single Image widget display on different themes;
* Fixed: Global styles with Categories Grid widget title;
* Fixed: Products archive pages dynamic tags functionality.

## [1.8.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.8.1.zip)
* Added: Hooks for handling products grid/list widgets not found messages (`jet-woo-builder/shortcodes/jet-woo-products-list/not-found-message`, `jet-woo-builder/shortcodes/jet-woo-products/not-found-message`);
* Added: Hooks for handling Categories grid widget output `jet-woo-builder/shortcodes/jet-woo-categories/categories-list`;
* Added: Controls for Open links in the new window for some widgets;
* Added: Product Grid widget item clickable;
* Added: Separate style controls for currency sign in Cart Table and Cart Totals widgets;
* Tweak: Single Add to Cart widget;
* Tweak: Single Tabs widget;
* Tweak: Cart Return to Shop widget;
* Tweak: Single AJAX add to cart;
* Tweak: Checkout Coupon Form widget;
* Fixed: Global link styles compatibility with products widgets titles;
* Fixed: SVG icon size in carousel arrows;
* Fixed: Background dynamic tags functionality in quick view popup.

## [1.8.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.8.0.zip)
* Added: Advanced icon controls for carousel functionality;
* Added: Options to control categories and tags count in products widgets;
* Added: Free Mode for carousels;
* Added: Trim title tooltip;
* Added: Trim title functionality to Categories Grid and Archive Category Title widgets;
* Added: Carousel disabled arrow style controls;
* Added: Admin notice about required elementor version;
* Added: Specific prev/next carousel arrows style controls;
* Added: Align controls for Single Image widget thumbnails;
* Added: Button width control for Checkout Payment widget button;
* Added: WooCommerce style warning for some widgets;
* Updated: WooCommerce templates;
* Tweak: Quick view popup close after the product added to cart;
* Tweak: Checkout Additional Form widget;
* Tweak: Lazy load compatibility;
* Tweak: Single Ajax add to cart;
* Fixed: Variations of products as individual products on shopping page;
* Fixed: Deprecated Elementor\Scheme_Color and Elementor\Scheme_Typography;
* Fixed: `woocommerce_thankyou` action;
* Fixed: Grouped product input quantity can't be 0;
* Fixed: Templates preview redirection for non login users;
* Fixed: Product Pagination widget styles;
* Fixed: Behaviour when archive items template has compare or wishlist widgets with disabled functionality.

## [1.7.12](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.12.zip)
* Added: Custom Template Inline CSS option;
* Added: My Account edit-addresses & view-order endpoints style controls;
* Added: Additional output validation for some widgets;
* Updated: JetDashboard 2.0.8;
* Updated: WooCommerce templates;
* Tweak: Categories Grid widget title hover control;
* Fixed: display of Archive Products Item templates;
* Fixed: Categories Grid widget equal column height;
* Fixed: Cart popup in product grid carousel;
* Fixed: Generatepress Theme input quantity buttons;
* Fixed: Sale badge macros preview error;

## [1.7.11](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.11.zip)
* Fixed: Template switcher functionality.

## [1.7.10](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.10.zip)
* Tweak: Single review widget template;
* Tweak: Individual custom template form products custom taxonomies;
* Tweak: Astra Pro quantity buttons;
* Fixed: Waring in listing template if not connected with WooCommerce;
* Fixed: Slider column padding;
* Fixed: Application of different templates archive cards on one page;
* Fixed: Listing grid product thumbnail effect;
* Fixed: Cart table widget in elementor when cart is empty;
* Fixed: Behaviour when show last order with all user information while preview template.

## [1.7.9](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.9.zip)
* Tweak: Listing source;
* Fixed: Compatibility with Kava theme woocommerce module;
* Fixed: External/Affiliate product functionality with Single Ajax add to cart.

## [1.7.8](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.8.zip)
* Fixed: Elementor 3.1.2 compatibility issue.

## [1.7.7](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.7.zip)
* Added: Equal column height in category and product archive templates;
* Added: Compatibility for Swiper JS Library with Elementor Improved Asset Loading;
* Added: Custom Archive item template selection in Products Loop widget;
* Added: Getting a list of templates in controls through ajax in Products Loop widget;
* Added: Controls for enabling links on title and thumbnails;
* Tweak: Overridden woocommerce templates versions;
* Tweak: Template Switcher on products loop display mode;
* Tweak: Quick view popup styles and scripts;
* Fixed: Astra Pro quantity buttons;
* Fixed: Swiper slider fade effect.

## [1.7.6](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.6.zip)
* Added: JupiterX theme integration;
* Added: Mobile hover on touch controller;
* Tweak: Cart Table widget cell width responsive control;
* Fixed: Single Image widget vertical layout navigation thumbnails;
* Fixed: Notice duplication on checkout page;
* Fixed: Cart Popup in Safari browser.

## [1.7.5](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.5.zip)
* Added: Archive Product & Category items widgets to JetEngine Listing Items;
* Added: ID and modified orderby in Products Grid/List widgets;
* Added: Helps url to new templates widgets;
* Updated: Thank You page template logic;
* Updated: Number of Products Grid widget columns;
* Updated: String translation;
* Tweak: Global widgets stock status;
* Tweak: Single Rating widget showing star section;
* Tweak: Text trim controls;
* Tweak: Replaces deprecated jQuery.fn.click( handler ) with jQuery.fn.on( 'click', handler ) and jQuery.fn.click() with jQuery.fn.trigger( 'click' );
* Fixed: Products Ordering widget dropdown icon;
* Fixed: `%numeric_sale%` macros for variable products;
* Fixed: Applying quantity input on adjacent widgets;
* Fixed: Cart template notice duplication.

## [1.7.4](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.4.zip)
* Added: My Account page endpoint templates;
* Added: Kava Theme compatibility with default ajax add to cart styles in Products Grid/List;
* Added: `update_db_1_7_4` callback in the DB_Upgrader;
* Added: My Account Content widget;
* Updated: JetDashboard 2.0.6;
* Updated: String translation;
* Tweak: Theme integration styles;
* Fixed: Carousels rtl;
* Fixed: Default Woocommerce products sorting query after template switching in Products Loop widget;
* Fixed: Default ajax add to cart styles in Products Grid/List Widgets;
* Fixed: Public received order page.

## [1.7.3](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.3.zip)
* Added: Product title trim functionality;
* Added: Query id control in Products Grid/List widgets;
* Added: Product Thumb Effect additional settings;
* Added: JetCompareWishlist integration for wishlist product card;
* Added: Product quantity input to Products Add to Cart buttons;
* Added: Mobile hover on touch Products Grid & Categories Grid widgets;
* Tweak: Product SKU check;
* Updated: String translation;
* Updated: JetDashboard 2.0.5;
* Fixed: Dynamic tags functionality;
* Fixed: Product quick view with Yoast SEO;
* Fixed: Variation product add to card when options not selected;
* Fixed: `%numeric_sale%` macros output;
* Fixed: Widgets functionality after template switching in Products Loop Widget;
* Fixed: Display correct template after switching in settings;
* Fixed: Cart table widget styles.

## [1.7.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.2.zip)
* Added: Archive product card SKU widget;
* Added: Categories grid queryby current subcategories;
* Added: Ability to show products filtered by custom taxonomy in products grid/list widgets;
* Added: My account page notifications;
* Updated: Quick view scripts and styles enqueue;
* Fixed: Quick view compatibility with JetEngine meta fields;
* Fixed: Archiver card title styles;
* Fixed: Dynamic styles for archives card template.

## [1.7.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.1.zip)
* Added: Product visibility control to Products Grid/List widgets;
* Added: Products Grid/List widgets orderby title;
* Added: Polylang compatibility;
* Added: Content controls for Woocommerce default pages builder widgets;
* Added: Getting a list of templates in controls through ajax;
* Added: `update_db_1_7_1` callback in the DB_Upgrader;
* Updated: Widgets translation;
* Updated: Jet-Dashboard module to 2.0.2;
* Updated: ajax single add to cart functionality;
* Updated: Naming;
* Fixed: Editor icons;
* Fixed: Archive product card saving template error;
* Fixed: displaying the metafield after product loop;
* Fixed: Endpoints for edit-address/billing and edit-address/shipping;

## [1.7.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.7.0.zip)
* Added: Woocommerce default pages builder - https://github.com/Crocoblock/suggestions/issues/498;
* Added: Layout switcher - https://github.com/Crocoblock/suggestions/issues/565;
* Added: Jet Woo Templates Active condition and Type admin columns;
* Added: Single ajax add to cart quick view popup compatibility;
* Added: Quick view btn compatibility;
* Added: Better lazy load compatibility;
* Added: SKU orderby option to Products Grid/List widgets;
* Added: Execute products by category to Products Grid/List widgets;
* Updated: Rename single ajax add to cart action & move handler to ajax-handler class;
* Updated: Editor icons;
* Updated: Elementor 3.0+ global styles compatibility;
* Updated: .pot file;
* Updated: Jet-Dashboard module to 1.1.0;
* Fixed: JetPopup error in Single Add To Cart widget;
* Fixed: Single product ajax add to cart when variations of product not selected;
* Fixed: Swiper carousel display after ajax load.

## [1.6.6](https://github.com/ZemezLab/jet-woo-builder/archive/1.6.6.zip)
* Fixed: Critical error while create Elementor templates.

## [1.6.5](https://github.com/ZemezLab/jet-woo-builder/archive/1.6.5.zip)
* Added: Better RTL compatibility;
* Added: Full width single product Template Type;
* Added: Exclude products by id control for Query by All option in the Products Grid / List widgets;
* Added: Single Image widget navigation vertical layout;
* Added: Menu order to the Categories Grid widget;
* Updated: Jet-Dashboard module to 1.0.14;
* Updated: Products Grid / List widgets query control;
* Updated: Global widgets controls consistent naming;
* Updated: Moved the Jet Woo Templates to the JetPlugins menu page;
* Fixed: Hidden products showed in Products Grid / List widgets;
* Fixed: Admin notice missing Woocommerce plugin;

## [1.6.4](https://github.com/ZemezLab/jet-woo-builder/archive/1.6.4.zip)
* Added: Single product AJAX add to cart functionality;
* Added: Option to display custom popup after adding product to cart for Single Add to Cart Button widget;
* Added: Products Grid/List widgets compatibility with default woocommerce filters;
* Updated: The number of Category grid widget columns;
* Fixed: Product excerpt function critical error;
* Fixed: Multiple photoswipe galleries open in quick view popup;
* Fixed: Single Image widget styles in quick view popup;
* Fixed: Carousel Vertical layout dots navigation position;
* Fixed: Display vertical carousel layout when count of items less than slides.

## [1.6.3](https://github.com/ZemezLab/jet-woo-builder/archive/1.6.3.zip)
* Added: Box Shadow hover mode for all global widgets with smooth transition;
* Added: Product Thumbnail box shadow hover mode to the global products widget;
* Added: Add to cart variation functionality in quick view popup on the Single Product Page;
* Added: Global Products widgets Sort Order;
* Fixed: Preview Single product template;
* Fixed: Arrow visibility when overflow container;
* Fixed: Prevent Shop Template infinite content duplication on Elementor preview page;
* Fixed: Dots box margin control styles.

## [1.6.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.6.2.zip)
* Added: Compatibility with new Elementor Swiper wrapper parameter;
* Added: Center mode for carousels;
* Update: Tested with the 4.0.0 version of WooCommerce;
* Fixed: Single Image widget transparency in editor;
* Fixed: Products Navigation widget default styles;
* Fixed: Products Loop widget column width in Safari browser.

## [1.6.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.6.1.zip)
* Added: Vertical direction for carousel;
* Added: Sale badge functionality description;
* Added: Carousels rtl compatibility;
* Update: Replaced slick slider to swiper slider;
* Update: Jet-Dashboard module to 1.0.10
* Fixed: compatibility with Elementor 2.9
* Fixed: Minor fixes.

## [1.6.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.6.0.zip)
* Added: Compatibility with Font Awesome 5 and new Icons control;
* Added: Control for customizing Heading Tags widgets;
* Added: Control switcher for display empty rating in widgets;
* Fixed: Minor fixes.

## [1.5.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.5.1.zip)
* Added: Jet Dashboard;

## [1.5.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.5.0.zip)
* Added: Stock status label for Products Grid widget;
* Added: Option to order products by Menu Order;
* Added: Custom templates for individual Woocommerce taxonomy;
* Added: Single Image widget Main Image Controls section description;
* Updated: Templates setting page titles and descriptions;
* Fixed: Archive & Category Templates widgets Help Links url;
* Fixed: Display Single Rating widget when zero reviews is set;
* Fixed: Archive Products template generating JSON;
* Fixed: Template creation form elements styles.

## [1.4.3](https://github.com/ZemezLab/jet-woo-builder/archive/1.4.3.zip)
* Added: Option to display custom popup after adding product to cart - https://github.com/Crocoblock/suggestions/issues/548;
* Added: Use Current Query option for the Products List widget;
* Added: Link for Products List widget Featured image - https://github.com/Crocoblock/suggestions/issues/661;
* Updated : Single Image widget styles - https://github.com/Crocoblock/suggestions/issues/583;
* Fixed: Products shortcode query;
* Fixed: Prevent php error on update DB;
* Fixed: Product Thumbnails Effect compatibility with third-party lazy-loading plugins - https://github.com/Crocoblock/suggestions/issues/641.

## [1.4.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.4.2.zip)
* Added: Need helps links to widgets;
* Added: Better wpml translation compatibility;
* Fixed: WooCommerce catalog ordering for Products Grid widget;
* Fixed: Quick View popup styles;
* Fixed: Minor fixes;
* Fixed: Elementor 2.7.0 compatibility.

## [1.4.1.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.4.1.1.zip)
* Fixed: Prevent php error.

## [1.4.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.4.1.zip)
* Updated: Widgets registration conditions;
* Fixed: QuickView functionality for products list;
* Fixed: QuickView on single product page;
* Fixed: Columns number control for archive product and category templates;
* Fixed: Catalog ordering for products grid widget.

## [1.4.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.4.0.zip)
* Added: Better Astra theme compatibility;
* Added: %percentage_sale% , %numeric_sale% macros for sale badges - https://github.com/CrocoBlock/suggestions/issues/47;
* Added: Sku product output in product list and product grid widgets - https://github.com/CrocoBlock/suggestions/issues/46;
* Added: Option that allow trim title by characters count in products title - https://github.com/CrocoBlock/suggestions/issues/62;
* Updated: Widgets registration conditions;
* Updated: Optimize and refactor code;
* Updated: Compatibility with Jet Popups;
* Fixed: Archive thumbnails width in Internet Explorer ;
* Fixed: Minor fixes.

## [1.3.9](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.9.zip)
* Added: Compatibility archive widgets with WooCommerce 3.6.0;
* Added: Better compatibility with Jet Popups;
* Fixed: Minor fixes.

## [1.3.8](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.8.zip)
* Updated: Optimize widget registration;
* Fixed: Issue with loading empty archive template;
* Fixed: Problems with preview products and categories when elementor render method is set.

## [1.3.7](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.7.zip)
* Added: better smart filters compatibility;
* Added: Add better Jet Woo widgets Ocean WP theme compatibility;
* Added: Global query on shop page for products grid widget;
* Fixed: Minor fixes.

## [1.3.6](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.6.zip)
* Added: Jet Compare Wishlist compatibility;
* Added: Query options for archive page in products grid widget;
* Fixed: Minor fixes.

## [1.3.5](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.5.zip)
* Added: Products Grid widget presets;
* Added: Compatibility with WPML String Translation plugin;
* Updated: allow to render archive with default Elementor callbacks;
* Fixed: processing categories;
* Fixed: compatibility with WooCommerce Booking plugin;
* Fixed: compatibility with WooCommerce Product Bundles plugin;
* Fixed: Minor fixes.

## [1.3.4](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.4.zip)
* Updated: increased the possible number of displayed elements in the products grid, categories grid, products list widgets;
* Fixed: equal column height in slick slider;
* Fixed: Minor fixes.

## [1.3.3](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.3.zip)
* Updated: Better compatibility with upcoming JetSmartFilters plugin;
* Fixed: Minor fixes.

## [1.3.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.2.zip)
* Added: Compatibility with upcoming JetSmartFilters plugin;
* Fixed: Minor fixes.

## [1.3.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.1.zip)
* Fixed: Minor fixes;

## [1.3.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.3.0.zip)
* Added: Category products page templates functionality;
* Added: Shop page templates functionality;
* Added: Rating icon option in single product rating and archive product rating widgets;
* Fixed: Query for categories and tags in products grid and products list widgets;
* Fixed: Compatibility with Astra and OceanWp themes.

## [1.2.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.2.0.zip)
* Added: Archive products page templates functionality;
* Added: Products query by related, up sells and cross sells products option in products grid and products list widgets;
* Added: Products add to cart default loader styles option;
* Added: Related, up sells, cross sells products per page option.

## [1.1.1](https://github.com/ZemezLab/jet-woo-builder/archive/1.1.1.zip)
* Added: Words count option in products grid and product list widgets;
* Added: Products query by recently viewed products option in products grid and products list widgets.

## [1.1.0](https://github.com/ZemezLab/jet-woo-builder/archive/1.1.0.zip)
* Added: Products Grid Widget;
* Added: Products List Widget;
* Added: Categories Grid Widget;
* Added: Taxonomy Tiles.

## [1.0.2](https://github.com/ZemezLab/jet-woo-builder/archive/1.0.2.zip)
* Added: Ability to create templates based on pre-designed layouts.
