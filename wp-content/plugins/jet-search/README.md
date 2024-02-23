# JetSearch For Elementor

The best tool for adding complex search functionality to pages built with Elementor.

# ChangeLog

# 3.2.3
* FIX: Blocks editor `Divider` and `Enable Scrolling` styles.

# 3.2.2
* FIX: Resolved Ajax Search widget issue within Jet Popup during ajax loading;
* FIX: Resolved the issue with the `Highlight Searched Text` option in the block editor when the option was turned off;
* FIX: Minor bug fixes.

# 3.2.1
* FIX: accessibility in the widgets.

# 3.2.0.1
* ADD: Check for the presence of sessions table in the database;
* ADD: Disable token clearing when the session usage option is turned off.

# 3.2.0
* ADD: Bricks Builder сompatibility;
* ADD: Added a new validation mechanism for adding new suggestions via the Search Suggestions widget;
* FIX: rest api urls;
* FIX: Search in taxonomy terms issue;
* FIX: minor issues.

# 3.1.3.1
* FIX: js issue.

# 3.1.3
* FIX: compatibility with Polylang/WPML;
* FIX: Fixed the issue for searching by category and terms;
* FIX: security issue;
* FIX: minor issues.

# 3.1.2.1
* FIX: security issue.

# 3.1.2
* FIX: Ajax Search blocks issue with custom fields;
* ADD: `Session usage settings` setting for Suggestions to resolve caching issues
* ADD: `jet-ajax-search/form/post-types` filter hook
* FIX: Ajax Search incorrect notifications issue
* UPD: JetDashboard Module to v2.1.4

# 3.1.1
* FIX: [Crocoblock/suggestions#6933](https://github.com/Crocoblock/suggestions/issues/6933);
* FIX: Include / Exclude terms issue
* ADD: `Is Products Search` option in the Search Suggestions Widget
* FIX: minor issues

# 3.1.0
* ADD: Search suggestions widget;
* ADD: Search suggestions admin UI;
* FIX: Better sanitizeing custom callbacks before execute;
* FIX: Showing results by post type;
* FIX: Markup issue with enabled highlight;
* FIX: Search with products archive.

# 3.0.3
* ADD: `jet-search/ajax-search/query-args` filter hook
* ADD: `jet-search/template/pre-get-content` filter hook
* ADD: `jet-search/template/pre-get-meta-field` filter hook
* ADD: `Minimal Quantity of Symbols for Search` option
* ADD: `jet-ajax-search/show-results` trigger on search AJAX request success
* FIX: minor issues

# 3.0.2
* ADD: [Crocoblock/suggestions#5712](https://github.com/Crocoblock/suggestions/issues/5712);
* ADD: [Crocoblock/suggestions#5742](https://github.com/Crocoblock/suggestions/issues/5742);
* FIX: issues with the `Search in taxonomy terms` option;
* FIX: compatibility with Elementor 3.7.
* FIX: minor issues

# 3.0.1
* UPD: Allow to disable submitting the search form on Enter click.

## 3.0.0
* ADD: Blocks Editor integration;
* ADD: Allow to search in taxonomy terms (include into results posts wich has terms with search query);
* ADD: Crocoblock/suggestions#4631;
* ADD: Allow to highlight search query in the search results;
* FIX: Navigation Arrows in Ajax Search withg Blocksy theme;
* FIX: Deprecated notice for Elementor editor;
* FIX: Items are duplicated in listing grid on search result page.

## 2.2.0 - 14.06.2022
* ADD: Blocks Editor integration;
* ADD: Allow to search in taxonomy terms (include into results posts wich has terms with search query);
* ADD: [Crocoblock/suggestions#4631](https://github.com/Crocoblock/suggestions/issues/4631);
* ADD: Allow to highlight search query in the search results;
* FIX: Navigation Arrows in Ajax Search withg Blocksy theme;
* FIX: Deprecated notice for Elementor editor;
* FIX: Items are duplicated in listing grid on search result page.

## [2.1.17](https://github.com/ZemezLab/jet-search/releases/tag/2.1.17) - 14.04.2022
* Added: [Crocoblock/suggestions#5090](https://github.com/Crocoblock/suggestions/issues/5090)
* Added: [Crocoblock/suggestions#4886](https://github.com/Crocoblock/suggestions/issues/4886)

## [2.1.16](https://github.com/ZemezLab/jet-search/releases/tag/2.1.16) - 23.03.2022
* Fixed: elementor 3.6 compatibility

## [2.1.15](https://github.com/ZemezLab/jet-search/releases/tag/2.1.15) - 24.12.2021
* Added: [Crocoblock/suggestions#3034](https://github.com/Crocoblock/suggestions/issues/3034)
* Fixed: minor issues

## [2.1.14](https://github.com/ZemezLab/jet-search/releases/tag/2.1.14) - 30.07.2021
* Fixed: compatibility with JetMenu on search result page

## [2.1.13](https://github.com/ZemezLab/jet-search/releases/tag/2.1.13) - 27.07.2021
* Added: better compatibility with JetSmartFilters
* Added: better compatibility with JetEngine
* Added: better compatibility with Polylang
* Fixed: showing search result on products search result page

## [2.1.12](https://github.com/ZemezLab/jet-search/releases/tag/2.1.12) - 17.06.2021
* Fixed: prevent php notice

## [2.1.11](https://github.com/ZemezLab/jet-search/releases/tag/2.1.11) - 28.04.2021
* Fixed: prevent php notice

## [2.1.10](https://github.com/ZemezLab/jet-search/releases/tag/2.1.10) - 22.04.2021
* Added: better compatibility with JetEngine
* Added: Elementor compatibility tag
* Added: [Crocoblock/suggestions#1611](https://github.com/Crocoblock/suggestions/issues/1611)
* Added: multiple improvements
* Updated: JetDashboard Module to v2.0.8
* Fixed: Various issue

## [2.1.9](https://github.com/ZemezLab/jet-search/releases/tag/2.1.9) - 13.11.2020
* Added: multiple improvements
* Updated: JetDashboard Module to v2.0.4
* Fixed: init session

## [2.1.8](https://github.com/ZemezLab/jet-search/releases/tag/2.1.8) - 01.09.2020
* Added: better compatibility with JetSmartFilters on the search result page

## [2.1.7](https://github.com/ZemezLab/jet-search/releases/tag/2.1.7) - 27.07.2020
* Added: multiple improvements
* Update: JetDashboard Module to v1.1.0
* Fixed: search by the current query

## [2.1.6](https://github.com/ZemezLab/jet-search/releases/tag/2.1.6) - 13.05.2020
* Added: `jet-search/get-locate-template` filter hook
* Added: multiple improvements and bug fixes

## [2.1.5](https://github.com/ZemezLab/jet-search/releases/tag/2.1.5) - 19.03.2020
* Added: `Serach by the current query` option
* Added: `Sentence Search` option
* Added: `Thumbnail Placeholder` option
* Added: multiple improvements and bug fixes
* Updated: optimized script dependencies

## [2.1.4](https://github.com/ZemezLab/jet-search/releases/tag/2.1.4) - 12.03.2020
* Added: support for Font Awesome 5 and SVG icons
* Added: multiple improvements

## [2.1.3](https://github.com/ZemezLab/jet-search/releases/tag/2.1.3) - 24.02.2020
* Added: better compatibility with WooCommerce Multilingual plugin
* Added: multiple improvements

## [2.1.2](https://github.com/ZemezLab/jet-search/releases/tag/2.1.2) - 21.02.2020
* Update: Jet-Dashboard Module to v1.0.10
* Added: multiple improvements
* Fixed: compatibility with Elementor 2.9

## [2.1.1](https://github.com/ZemezLab/jet-search/releases/tag/2.1.1) - 15.01.2020
* Update: Jet-Dashboard Module to v1.0.9
* Added: multiple improvements

## [2.1.0](https://github.com/ZemezLab/jet-search/releases/tag/2.1.0) - 02.12.2019
* Added: Jet Dashboard

## [2.0.2](https://github.com/ZemezLab/jet-search/releases/tag/2.0.2) - 21.11.2019
* Added: FA5 compatibility
* Fixed: Various issue

## [2.0.1](https://github.com/ZemezLab/jet-search/releases/tag/2.0.1) - 16.10.2019
* Added: filter hook 'jet-search/ajax-search/meta_callbacks' to the Custom fields meta callbacks
* Added: `get_the_title` callback to the Custom fields meta callbacks

## [2.0.0](https://github.com/ZemezLab/jet-search/releases/tag/2.0.0) - 01.08.2019
* Added: include/exclude controls for terms and posts
* Added: the ability to display custom fields in the result area
* Added: the ability to search in custom fields
* Added: `Post Content Source` control
* Added: responsive control to the `Number of posts on one search page` control
* Added: dummy data
* Added: multiple performance improvements and bug fixes

## [1.1.4](https://github.com/ZemezLab/jet-search/releases/tag/1.1.4) - 04.06.2019
* Update: categories select arguments ( add 'orderby' => 'name' )
* Fixed: compatibility with Product Search Page created with Elementor Pro
* Fixed: minor css issue

## [1.1.3](https://github.com/ZemezLab/jet-search/releases/tag/1.1.3) - 26.04.2019
* Added: `Custom Width` and `Custom Position` controls for the Result Area Panel
* Added: `Vertical Align` control for the Submit Button
* Added: filter `jet-search/ajax-search/custom-post-data`

## [1.1.2](https://github.com/ZemezLab/jet-search/releases/tag/1.1.2) - 02.04.2019
* Added: `Placeholder Typography` control in the Ajax Search Widget
* Fixed: ajax error

## [1.1.1](https://github.com/ZemezLab/jet-search/releases/tag/1.1.1) - 27.03.2019
* Fixed: minor issues

## [1.1.0](https://github.com/ZemezLab/jet-search/releases/tag/1.1.0) - 20.03.2019
* Added: `Product Price` and `Product Rating` settings in the Ajax Search Widget
* Added: compatibility with Woo Search Result Page
* Added: better compatibility with Polylang
* Added: filter `jet-search/ajax-search/categories-select/args` for passed arguments to `wp_dropdown_categories`
* Added: Brazilian translations
* Added: multiple performance improvements and bug fixes

## [1.0.1](https://github.com/ZemezLab/jet-search/releases/tag/1.0.1)
* Fixed: minor issue bugs.

## [1.0.0](https://github.com/ZemezLab/jet-search/releases/tag/1.0.0)
* Init
