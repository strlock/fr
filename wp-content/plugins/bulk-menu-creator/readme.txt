=== Bulk menu creator ===
Contributors: kubiq
Donate link: https://www.paypal.me/jakubnovaksl
Tags: menu, nav, navigation, bulk, batch, generate, remove, delete
Requires at least: 4.0
Tested up to: 6.9
Stable tag: 9.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create multiple menu items at once or quick delete menu item with or without all subitems

== Description ==

[youtube https://youtu.be/U7gQ4HmcUTs]
<br/>
[youtube https://youtu.be/e_3zLGHQouo]
<br/>
[youtube https://youtu.be/P5tFncQkRCg]
<br/>

<ul>
	<li>create multiple menu items at once</li>
	<li>menu items are created from multiline text</li>
	<li>you can provide menu items labels one per line</li>
	<li>use 2 space indent to create subitem</li>
	<li>you can provide menu items URLs one per line - they are automatically paired line by line</li>
	<li>if you don't provide URLs, then hash is automatically generated for each label</li>
	<li>quick delete menu item with or without all subitems</li>
	<li>quick copy/clone menu item</li>
</ul>

<strong>PRO version features (<a href="https://wp-speedup.eu/shop/wordpress-plugins/pro-plugins/bulk-menu-creator-pro/" target="_blank">BUY HERE</a>):</strong>

<ul>
	<li>
		<strong>generate posts directly from the Menus screen</strong>
		<ul>
			<li>no need to leave your Menus screen to create new drafts that you will need later</li>
			<li>generate multiple posts, pages or other post type posts at once</li>
			<li>use 2 space / tab indent to create subpages</li>
			<li>you can provide custom slugs for newly generated posts or let them generate automatically from the titles</li>
		</ul>
	</li>
	<li>
		<strong>duplicate menu functionality</strong>
		<ul>
			<li>works with all the menu items metas</li>
			<li>you will not lose your Polylang Languages settings</li>
			<li>also works with ACF icons or other data</li>
		</ul>
	</li>
	<li>
		<strong>taxonomy terms auto generated menu items</strong>
		<ul>
			<li>select any public taxonomy like Blog post categories or WooCommerce product categories or other custom taxonomies</li>
			<li>you can limit how many terms will be listed</li>
			<li>you can limit how many levels of subterms will be listed</li>
			<li>you can exclude some specific terms</li>
			<li>you can order terms by name, parent, count, or other parameters</li>
			<li>you can show/hide empty terms</li>
			<li>you can show/hide terms count (number of assigned posts)</li>
		</ul>
	</li>
	<li>
		<strong>post type posts auto generated menu items</strong>
		<ul>
			<li>select any public post type like Posts, Pages, Products or other custom post types</li>
			<li>you can limit how many posts will be listed</li>
			<li>you can limit how many levels of subposts will be listed</li>
			<li>you can exclude some specific posts</li>
			<li>you can filter posts by any term and taxonomy</li>
			<li>you can order posts by title, author, date, or other parameters</li>
		</ul>
	</li>
	<li>
		<strong>Profile menu item</strong>
		<ul>
			<li>show current user in menu</li>
			<li>you can use variables {display_name}, {first_name}, {last_name}, {nickname}, {user_email} to create any custom menu item, like  `Hello John (john@doe.com)` by `Hello {first_name} ({user_email})`</li>
			<li>you can link it to admin profile or author posts URL or choose from WooCommerce account endpoint URLs or to # to use it just as a parent menu item for dropdown</li>
		</ul>
	</li>
	<li>
		<strong>Login / Logout menu item</strong>
		<ul>
			<li>show login and logout links in menu</li>
			<li>you can provide your own login URL or use default WP login</li>
			<li>you can provide your own login and logout redirect URL</li>
			<li>you can provide your own login and logout menu item label and it's fully translatable with WPML, Polylang or others</li>
			<li>you can use variables {display_name}, {first_name}, {last_name}, {nickname}, {user_email} in logout menu item</li>
		</ul>
	</li>
	<li>
		<strong>special field for hash or $_GET parameters for any post menu item</strong>
		<ul>
			<li>you can write some `#hash` that will be added at the end of the post URL</li>
			<li>you can write some `?get_attribute=123` that will be added at the end of the post URL</li>
		</ul>
	</li>
	<li>
		<strong>automatically generates anchor links menu items for any post</strong>
		<ul>
			<li>click on anchor button to get all anchors from that specific post</li>
			<li>you can select title for every anchor</li>
			<li>you can decide which anchors to add</li>
		</ul>
	</li>
</ul>

== Installation ==

1. Upload `bulk-menu-creator` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In case you don't see Bulk menu in Appearance > Menus, just click on top right Screen Options and activate it

== Screenshots ==

1. Bulk menu creator
2. Quick delete

== Changelog ==

= 9.6 =
* Tested on WP 6.9
* PRO ONLY: generate posts directly from the Menus screen

= 9.5 =
* Tested on WP 6.5
* stop wrapping lines inside Bulk menu textareas
* now you can resize Bulk menu textareas horizontally and vertically
* PRO ONLY: now you can use TAB and SHIFT+TAB in Bulk menu textareas to change indent

= 9.4 =
* Tested on WP 6.4

= 9.3 =
* Tested on WP 6.3
* PRO version promo notice

= 9.2 =
* Tested on WP 6.1
* new features in PRO version

= 9.1 =
* previous fix was not updated... what the hell?

= 9.0 =
* fix missing JS localization

= 8.0 =
* new Quick copy button on every menu item
* rewrite quick delete to use WordPress core JS functions for better compatibility

= 7.0 =
* fix Firefox not working new line with Enter

= 6.0 =
* Tested on WP 5.9
* fix children detection for Quick delete
* PRO version introduced

= 5.0 =
* Tested on WP 5.7
* possibility to quick delete menu item with or without all subitems - trash icon will appear on hover

= 4.0 =
* Tested on WP 5.4
* textarea has now numbers so you can easily visually pair labels and urls
* possibility to create subitems with 2 spaces indent ( use 4 spaces to create sub-sub-item etc. )

= 3.0 =
* Tested on WP 5.2.1
* auto generate hash from title if URL is empty

= 2.0 =
* Tested on WP 5.0
* Optimized for translations
* Auto unhide metabox on plugin activation

= 1.0 =
* First version