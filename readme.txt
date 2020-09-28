=== The SEO Machine ===

Tags: wordpress, seo
Requires at least: 5.0
Tested up to: 5.5
Requires PHP: 7.2
Stable tag: 0.1
License: GPLv2 or later
Repository URL: https://github.com/jaimenj/the-seo-machine
Plugin URI: https://jnjsite.com/the-seo-machine-for-wordpress/
Contributors: jaimenj

A SEO machine to study and improve your WordPress website.

== Description ==

A very simple plugin for WordPress that allows you to study the SEO of your website. It will show you how many URLs you have, number of h1/h2/h3/.. tags, number of links/CSS/JavaScripts/.., study of the text of every page, etc.. It will show you a lot of your SEO insights info that will allow you to focus on the next improves that your website needs.

Features:

* Feel free to contribute in GitHub to improve the project.
* It’s free, completely free.
* It keeps data into your website.
* Unlimited studies of your site.
* Filter and show only the relevant data that you need to improve in your website.
* Export data of the SEO study in CSV, PDF or Excel format.
* Etc..

== Installation ==

Install uploading manually the files to the server:

1. Copy the files in the directory /wp-content/plugins/the-seo-machine/ like others plugins.
2. Activate the plugin in the WordPress backend menu of plugins.
3. Got to the admin section of The SEO Machine.
4. See how it works and play with it configs, fully personalizable.
5. Enjoy.. 🙂

Alternative install with SSH:

1. Goto the plugins directory doing: cd /wp-content/plugins/
2. Clone the GitHub repository doing: git clone git@github.com:jaimenj/the-seo-machine.git

With SSH you can stay up to date using the normal git pull command.

== Uninstall ==

1. Deactivate the plugin into the Plugins menu in the admin panel of WordPress.
2. Delete into the Plugins menu.

All the options configured into the plugin are removed when plugin is deleted, not when plugin is deactivated. All the database tables are removed when plugin is deactivated. So if you want to remove the plugin and all data stored, first deactivate the plugin and then remove it from the plugin admin zone into the WordPress backend.

== Frequently Asked Questions ==

= I've changed my website pages, what can I do to make a new study? =

You can do two things. The one is to remove al data with the button that says 'Remove All Data' and start a new study. The second way is to use the button that says 'Reset Queue' and start a new study, al the data of your website will be updated without removing the old URLs.

= What is the Quantity per Batch config? =

The study is done in batches of URLs studied. Every bacth must be done by the server in a maximum time of 30 seconds to avoid reaching this default timeout server (usually 30 seconds). So you can increase or decrease it for adjusting it acordingly to the size of your server.

= What is the Time beetween Batches config? =

The study is done in batches of URLs studied. When a batch is done, it will sleep to avoid blocking your server with too much processing. This amount of time of sleep is the Time beetween Batches.

== Screenshots ==

1. The main view into the admin panel.
2. Configuration of columns to show in the admin panel.

== Changelog ==

= v0.1 =
* Initial version.
