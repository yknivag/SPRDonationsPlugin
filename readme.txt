=== Simple PayPal Recurring Donations ===
Contributors: yknivag 
Tags: subscription, donate, donation, paypal, recurring, payment, donations, paypal donation, button, shortcode, sidebar, widget, monthly
Requires at least: 3.0
Tested up to: 5.2.2
Stable tag: 1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Accept PayPal recurring donations from your WordPress site easily.

== Description ==

This plugin allows you to accept recurring or one-off donations via PayPal from your website in a simple and elegant manner.

It has a user-friendly and simple shortcode that lets you place a donate form anywhere on your WordPress site. You can add the subscription donation widget to your sidebar, posts, pages etc.

The plugin creates a clean cusomisable form laid out using Flexbox which can be styled in your theme or with the Wordpress Additional CSS functionality.  The plugin is also "translation ready" for all strings used in the front end display.

The front end display consists of three sections, a collection of "presets" or buttons which trigger pre-defined weekly/monthly/yearly donations which recur indefinitely, a "besoke" section where users can choose their own amount, frequency and duration, and a section where users can make a one-off donation in their choice of amount.
Each of these sections can be turned on and off in the shortcode.  The number of "presets", their amount and frequency can also be specified and the "bespoke" section takes two forms: simple and advanced.

This plugin allows you to accept one time donations also, should you choose to enable it.

* Quick installation and setup.
* Easily take recurring donations via PayPal.
* Accept ongoing subscription donations on your site.
* Simple, elegant plugin to create PayPal recurring donations buttons.
* Ability to add multiple recurring donation widgets on your site.
* Allow your users to specify a donation amount that they wish to pay. 
* Ability to accept recurring payment in any PayPal supported currency.
* Send your users to a custom thank you page after the payment.
* Option to send your users to a custom cancel return page from PayPal.
* Fully GDPR compliant, as no user data is captured or stored in any part of your site, only within PayPal.
* Stylable in your theme using the `sprdntplgn_` classes.
* Translation ready.
* User Interface controls feature appropriate `aria-*` labelling.

The setup is very simple and easy. Once you have installed the plugin, all you need to do is enter your PayPal Email address in the plugin settings and your site will be ready to accept recurring donations from users.

= Shortcode =

In it's most basic form the shortcode, `[sprdntplgn]`, will give you a functioning recurring donations form.  By default there are no preset amounts, no one-off donation box and the bespoke section displays in advanced mode.  You can use the options below within the shortcode to customise the form to your liking.

* `item_name`
** This will show as the description at PayPal, make it something people will recognise.
** e.g. [sprdntplgn item_name="Donations to My Charity"]
** Specifying this in the shortcode overides any default description set below.
* `bespoke`
** `[none|simple|advanced]`
** `none`
*** No option will be given for the customer to provide their own amount, only preset amounts will be shown. If no presets are specified, no form will be shown.
** `simple`
*** The customer may specify only an amount. That amount will be taken every month.
** `advanced`
*** The default. This option allows the customer to specify how much, how often and for how long they will be billed.
* `presets`
** These create buttons which offer shortcuts to dontaing preset amounts over a preset time.
** e.g. `[sprdtnplgn presets="5.00:M|7.50:M|10.00:M"]` would create 3 buttons, one for 5.00 per month, one for 7.50 per month and one for 10,00 per month.
** The letter may be "W", "M" or "Y" for "per week", "per month" or "per year". If you wish to use only preset buttons remember to set bespoke=false
* `one-off`
** `[1|0]` Defaults to "0".
** When set to "1" offers the customer a chance to make a one-off payment of an amount of their choice.

= Widget =

In order to place a widget on the sidebar, go to "Appearance -> Widgets" and add a new text widget. Now add the following shortcode to the text widget.

`[sprdntplgn]`

The shortcode takes all the same parameters in a Widget as it does on a page, though not all option combinations work will in a widge due to the space required.

== Installation ==

1. Upload plugin `simple-paypal-recurring-donations` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin using the 'Plugins' menu in your WordPress admin panel.
3. You can adjust the necessary settings using your WordPress admin panel in "SPR Donation Plugin".
4. Create a page or a post, customize button settings and insert generated shortcode into the text.

== Frequently Asked Questions ==

= How can I add Donate Plugin form to my website? =

Use the following shortcode to add a recurring donation button to your website:

In it's simplest form, add the shortcode `[sprdntplgn]` to your post, page or widget.  For more detailed instructions see the section above on how to format the shortcode.

= Can I add more then one Donate Plagin form on the same page or post? =

Yes, you can add multiple Donate Plugin forms on your page or post or text widget.

= Can I create multiple recurring donation widgets using different paypal accounts? =

No, all donations will go to the address you specify in the plugin settings.

== Screenshots ==

1. Basic Implementation with no options ([sprdntplgn])
2. Full form with everything enabled ([sprdntplgn presets="5.00:M|7.50:M|10.00:M" one-off=1])
3. Simple "bespoke" section ([sprdntplgn bespoke="simple"])

== Changelog ==

= 0.1 =
* Forked from ["The Recurring Donations plugin" by WP eCommerce](https://wp-ecommerce.net/wordpress-recurring-donation-plugin)
* Options added to format the user display.

== Upgrade Notice ==
none

