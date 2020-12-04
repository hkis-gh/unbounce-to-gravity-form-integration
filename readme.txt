=== Unbounce Gravity Forms ===
Contributors: hkispl
Tags: Unbounce Gravity Forms, Unbounce To Gravity Forms, Gravity Form, Unbounce
Requires at least: 5.0
Tested up to: 5.5.3
Requires PHP: 5.6
Stable tag: 1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Unbounce To Gravity Form Integration, fetch leads from Unbounce landing pages to Gravity Forms.

== Description ==

* Unbounce Gravity Forms ('U2GF') plugin is commonly used to connect Gravity Form(s) with Unbounce Landing Page(s) in order to manage your leads from WordPress site admin.

* You can connect Unbounce Landing Page(s) to 'U2GF' plugin using WebHook URL. WebHook provides 'POST to URL' feature which passes the form data over specified WebHook URL.

* For more information please visit [Webhook Documentation](https://documentation.unbounce.com/hc/en-us/articles/203510044-Using-a-Webhook)

* Please note that 'Gravity Form' plugin should be installed and activated first in order to use 'U2GF' plugin. You've to create then all corresponding Gravity Forms manually from your WP admin. Please make sure that the Gravity Form name must be matched with Unbounce Page name including ALL fields' name.

* Also please note that the page name will be reflected on plugin dashboard ONLY after very first lead is generated/posted successfully from your respective Unbounce Page. You can place one test entry/lead in order to reflect the page on plugin dashboard at first time.

* On a separate note those who are on dedicated or VPS with some firewall/security enabled on the server have to whitelist Unbounce IPs: 54.241.34.25 and 50.19.99.184 to prevent looking out into 404-Forbidden error.

= Premium Version is available now =

* Here's [Pro Version](https://www.hkinfosoft.com/unbounce-to-gravity-form-integration) which help you to get rid of the manual form creation and other management.

= Steps =

1. Copy WebHook URL from plugin settings page and set it as WebHook for Unbounce Page(s).
   Example of WebHook URL: http://{YOUR_DOMAIN_URL}/lead-unbounce-to-gravity/

2. Manage the page status as Active/Inactive to Start/Stop fetching leads.

== Installation ==

This section describes how to install the plugin and get it started using with WordPress.

1. Upload `unbounce-gravity-integration` to the `/wp-content/plugins/` directory.
   OR visit 'Plugins > Add New', search for 'Unbounce Gravity Forms'.
2. Activate the plugin from 'Plugins' page.

== Frequently Asked Questions ==
 
= Where to find WebHook URL? =

You should find it on U2GF plugin settings page. For ex: http://{YOUR_DOMAIN_URL}/lead-unbounce-to-gravity/

= How to set WebHook for any Unbounce Landing Page? =

Copy WebHook URL from plugin settings page and set it as 'WebHook' for particular Unbounce page for which you want to Sync the leads.
You can set single WebHook URL for multiple Unbounce pages.
For more information please visit [Webhook Documentation](https://documentation.unbounce.com/hc/en-us/articles/203510044-Using-a-Webhook)

== Screenshots ==

1. Dashboard: Plugin settings page.
2. Unbounce Dashboard: WebHook URL setup.
3. WebHook URL Integration: Example of WebHook URL setup to specific Unbounce Landing page.
4. Page Status: Active/Inactive.
5. Page Leads: Entries Vs Leads.

== Changelog ==
= 1.6 =
* Fix minor bugs.
= 1.5 =
* Add log file to record information about leads migration related issue.
* Update info about PRO version release.
= 1.4 =
* Fix htaccess rule rewrite issue.
= 1.3 =
* Use absolute path considering some of WP accounts are installed on subdomains.
* Update readme to include more clarification about plugin usage.
= 1.2 =
* Fix issue to support Unbounce page name containing extra slashes.
* Display message as in response when any UB field name doesn't match with respective GF field name.
= 1.1 =
* Minor release contains fields name validation.
= 1.0 =
* This is an initial lite version release.