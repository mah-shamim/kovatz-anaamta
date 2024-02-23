=== Conditional Menus ===
Contributors: themifyme
Tags: menu, conditional-tags, context, menu-items, admin
Requires at least: 4.0
Tested up to: 6.4.2
Stable tag: 1.2.5
License: GPLv2 or later

This plugin enables you to set conditional menus per posts, pages, categories, archive pages, etc.

== Description ==

Conditional Menus is a simple yet useful WordPress plugin by <a href="https://themify.me/">Themify</a>, which allows you to swap the menus in the theme as per specific conditions. In short, you can have different menus in different posts, pages, categories, archive pages, etc. It works with any WordPress theme that uses the standard WordPress menu function.

== Installation ==

1. Login to your wp-admin > go to Plugins > Add New and upload the ‘conditional-menus.zip’
2. Activate the plugin

== How to use it ==

Once you activate the plugin, you will see the conditional menus on the Manage Locations tab located in your WP Admin > Appearance > Menus page.

1) To add conditional menu: click "Conditional Menu" and select a menu from the list (you can create these menus in the "Edit Menus" tab)
   - You can remove the menu by selecting "Disable Menu" from the list.
2) Click on “+ Conditions" to add conditions in the modal box (tick the checkboxes where you want the menu to appear)
3) To remove the conditional menus, click on the "X" button

Visit https://themify.me/conditional-menus for more details.


== Screenshots ==

1. Admin interface

== Changelog ==

= 1.2.5 (2024.01.11) =
* Reverted plugin to previous version due to major compatibility issue with Polylang from v1.2.4

= 1.2.4 (2024.01.10) =
* Fix: WPML support: auto switch conditional menus based on menu translations

= 1.2.3 (2023.09.11) =
* Fix: Edit/add conditions not showing for Taxonomy tab

= 1.2.2 (2023.07.21) =
* Fix: Setting conditions when there are lots of posts causes server error

= 1.2.1 (2023.05.23) =
* Fix: XSS issue

= 1.2.0 (2022.02.25) =
* New: Option to set condition for post type archive

= 1.1.9 (2020.12.11) =
* Fix: Compatibility with PHP 8
