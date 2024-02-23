=== User Role Editor ===
Contributors: shinephp
Tags: user, role, editor, security, access
Requires at least: 4.4
Tested up to: 6.4.3
Stable tag: 4.64.2
Requires PHP: 7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

User Role Editor WordPress plugin makes user roles and capabilities changing easy. Edit/add/delete WordPress user roles and capabilities.

== Description ==

User Role Editor WordPress plugin allows you to change user roles and capabilities easy.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

Try it out on your free TasteWP [test site](https://demo.tastewp.com/user-role-editor).

To read more about 'User Role Editor' visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/) at [shinephp.com](http://shinephp.com)


Do you need more functionality with quality support in a real time? Do you wish to remove advertisements from User Role Editor pages? 
[Buy Pro version](https://www.role-editor.com). 
[User Role Editor Pro](https://www.role-editor.com) includes extra modules:
<ul>
<li>Block selected admin menu items for role.</li>
<li>Hide selected front-end menu items for no logged-in visitors, logged-in users, roles.</li>
<li>Block selected widgets under "Appearance" menu for role.</li>
<li>Show widgets at front-end for selected roles.</li>
<li>Block selected meta boxes (dashboard, posts, pages, custom post types) for role.</li>
<li>"Export/Import" module. You can export user role to the local file and import it to any WordPress site or other sites of the multi-site WordPress network.</li> 
<li>Roles and Users permissions management via Network Admin  for multisite configuration. One click Synchronization to the whole network.</li>
<li>"Other roles access" module allows to define which other roles user with current role may see at WordPress: dropdown menus, e.g assign role to user editing user profile, etc.</li>
<li>Manage user access to editing posts/pages/custom post type using posts/pages, authors, taxonomies ID list.</li>
<li>Per plugin users access management for plugins activate/deactivate operations.</li>
<li>Per form users access management for Gravity Forms plugin.</li>
<li>Shortcode to show enclosed content to the users with selected roles only.</li>
<li>Posts and pages view restrictions for selected roles.</li>
<li>Admin back-end pages permissions viewer</li>
</ul>
Pro version is advertisement free. Premium support is included.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor.zip" archive content to the "/wp-content/plugins/user-role-editor" directory.
3. Activate "User Role Editor" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Users"-"User Role Editor" menu item and change your WordPress standard roles capabilities according to your needs.

== Frequently Asked Questions ==
- Does it work with WordPress in multi-site environment?
Yes, it works with WordPress multi-site. By default plugin works for every blog from your multi-site network as for locally installed blog.
To update selected role globally for the Network you should turn on the "Apply to All Sites" checkbox. You should have superadmin privileges to use User Role Editor under WordPress multi-site.
Pro version allows to manage roles of the whole network from the Netwok Admin.

To read full FAQ section visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/#faq) at [shinephp.com](shinephp.com).

== Screenshots ==
1. screenshot-1.png User Role Editor main form
2. screenshot-2.png Add/Remove roles or capabilities
3. screenshot-3.png User Capabilities link
4. screenshot-4.png User Capabilities Editor
5. screenshot-5.png Bulk change role for users without roles
6. screenshot-6.png Assign multiple roles to the selected users

To read more about 'User Role Editor' visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/) at [shinephp.com](shinephp.com).

= Translations =

If you wish to check available translations or help with plugin translation to your language visit this link
https://translate.wordpress.org/projects/wp-plugins/user-role-editor/


== Changelog =

= [4.64.2] 19.02.2024 =
* Update: Marked as compatible with WordPress 6.4.3
* Update: URE_Advertisement: rand() is replaced with wp_rand().
* Update: URE_Ajax_Proccessor: json_encode() is replaced with wp_json_encode().
* Update: User_Role_Editor::load_translation(): load_plugin_textdomain() is called with the 2nd parameter value false, instead of deprecated ''.
* Update: URE_Lib::is_right_admin_path(): parse_url() is replaced with wp_parse_url().
* Update: URE_Lib::user_is_admin() does not call WP_User::has_cap() to enhance performance.
* Update: Plugin version was added to CSS loaded to the "Users", "Users->User Role Editor", "Settings->User Role Editor" pages.
* Update: All JavaScript files are loaded in footer now.
* Fix: "Users->Add New Users". Unneeded extra '<table></table>' HTML tags was removed (thanks to Alejandro A. for this bug report). 

= [4.64.1] 24.10.2023 =
* Update: Marked as compatible with WordPress 6.4
* Fix: Notice shown by PHP 8.3 is removed: PHP Deprecated: Creation of dynamic property URE_Editor::$hide_pro_banner is deprecated in /wp-content/plugins/user-role-editor/includes/classes/editor.php on line 166
* Fix: Notice shown by PHP 8.3 is removed: PHP Deprecated: Creation of dynamic property URE_Role_View::$caps_to_remove is deprecated in /wp-content/plugins/user-role-editor/includes/classes/role-view.php on line 23
* Fix: Notice shown by PHP 8.3 is removed: PHP Deprecated: Function utf8_decode() is deprecated in /wp-content/plugins/user-role-editor-pro/includes/classes/editor.php on line 984


File changelog.txt contains the full list of changes.

== Additional Documentation ==

You can find more information about "User Role Editor" plugin at [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/)

I am ready to answer on your questions about plugin usage. Use [plugin page comments](http://www.shinephp.com/user-role-editor-wordpress-plugin/) for that.

== Upgrade Notice ==

= [4.64.2] 19.02.2023 =
* Update: URE_Advertisement: rand() is replaced with wp_rand().
* Update: URE_Ajax_Proccessor: json_encode() is replaced with wp_json_encode().
* Update: User_Role_Editor::load_translation(): load_plugin_textdomain() is called with the 2nd parameter value false, instead of deprecated ''.
* Update: URE_Lib::is_right_admin_path(): parse_url() is replaced with wp_parse_url().
* Update: URE_Lib::user_is_admin() does not call WP_User::has_cap() to enhance performance.
* Update: Plugin version was added to CSS loaded to the "Users", "Users->User Role Editor", "Settings->User Role Editor" pages.
* Update: All JavaScript files are loaded in footer now.
* Fix: "Users->Add New Users". Unneeded extra '<table></table>' HTML tags was removed (thanks to Alejandro A. for this bug report). 
