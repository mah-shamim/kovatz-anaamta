=== ACF: Better Search ===
Contributors: mateuszgbiorczyk
Donate link: https://ko-fi.com/gbiorczyk/?utm_source=acf-better-search&utm_medium=readme-donate
Tags: acf search, advanced custom fields, better search, extended search, search
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 4.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin adds to default WordPress search engine the ability to search by content from selected fields of Advanced Custom Fields plugin.

== Description ==

This plugin adds to default WordPress search engine the ability to search by content from selected fields of Advanced Custom Fields plugin.

Everything works automatically, no need to add any additional code. The plugin does not create a search results page, but modifies the SQL database query to make your search engine work better.

Additionally you can search for whole phrases instead of each single word of phrase. As a result, search will be more accurate than before.

#### New search core

We modified the code of search engine. Content search is now faster by about 75% *(depending on the level of complexity of searched phrase)*!

#### Support to the development of plugin

We spend hours working on the development of this plugin. Technical support also requires a lot of time, but we do it because we want to offer you the best plugin. We enjoy every new plugin installation.

If you would like to appreciate it, you can [provide us a coffee](https://ko-fi.com/gbiorczyk/?utm_source=acf-better-search&utm_medium=readme-content). **If every user bought at least one, we could work on the plugin 24 hours a day!**

#### Please also read the FAQ below. Thank you for being with us!

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/acf-better-search` directory, or install plugin through the WordPress plugins screen directly.
2. Activate plugin through the `Plugins` screen in WordPress Admin Panel.
3. Use the `Settings -> ACF: Better Search` screen to configure the plugin.

== Frequently Asked Questions ==

= What version of Advanced Custom Fields is supported? =

Advanced Custom Fields in version 5 *(also free)*. ACF below version 5 has a different data structure in database and is not supported.

= In what fields does the plugin search? =

Our plugin supports the following fields: Text, Text Area, Number, Email, Url, File, Wysiwyg Editor, Select, Checkbox and Radio Button.

All these fields may be located in both the Repeater or Flexible Content field.

= How does this work? =

Plugin changes all SQL queries by extending the standard search to selected fields of Advanced Custom Fields.

The plugin in admin panel works same as for the search page.

It works for `WP_Query` class.

= How to activate advanced search? =

Everythings works automatically. For custom `WP_Query` loop and `get_posts()` function also if you add [Search Parameter](https://codex.wordpress.org/Class_Reference/WP_Query#Search_Parameter).

= What to do when not searching for posts? =

Sometimes it happens that the data in your database is incorrectly arranged. This happens when you import or duplicate posts.

You can use `Incorrect Mode`. This is a slower search, but it does not take into account the order of records in the `_postmeta` table. This solution should help in this situation. Use of this mode is allowed without restrictions. This does not mean any problems with your website.

= How does searching for whole phrases? =

The default search in WordPress is to search for each of words listed. An additional option in the plugin settings allows you to search for occurrences of the whole phrase entered in the search field without word division.

You can enable it at any time.

= How does Lite mode work? =

In this mode, the plugin does not check the field types. Phrases are searched in all ACF fields. Thanks to this, the query to the database is smaller and faster by about 25%. However, we do not have control over which fields are taken into account when searching.

= Is the plugin completely free? =

Yes. The plugin is completely free.

However, working on plugins and technical support requires many hours of work. If you want to appreciate it, you can [provide us a coffee](https://ko-fi.com/gbiorczyk/?utm_source=acf-better-search&utm_medium=readme-faq). Thanks everyone!

Thank you for all the ratings and reviews.

If you are satisfied with this plugin, please recommend it to your friends. Every new person using our plugin is valuable to us.

This is all very important to us and allows us to do even better things for you!

== Screenshots ==

1. Screenshot of the options panel

== Changelog ==

= 4.2.0 (2023-09-11) =
* `[Fixed]` Duplicated search results for modified SQL query

= 4.1.1 (2023-06-29) =
* `[Added]` Filter `acfbs_field_types` to add new supported field types

= 4.1.0 (2023-03-02) =
* `[Changed]` Appearance of plugin settings page
* `[Added]` Support for Table field type generated by Advanced Custom Fields: Table Field plugin
* `[Added]` Support for WordPress 6.2

See [changelog.txt](https://plugins.svn.wordpress.org/acf-better-search/trunk/changelog.txt) for previous versions.

== Upgrade Notice ==

None.
