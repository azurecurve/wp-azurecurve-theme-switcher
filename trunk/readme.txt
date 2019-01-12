=== azurecurve Theme Switcher ===
Contributors: azurecurve
Donate link: http://development.azurecurve.co.uk/support-development/
Author URI: http://development.azurecurve.co.uk/
Plugin URI: http://development.azurecurve.co.uk/plugins/theme-switcher/
Tags: themes, switching, theme, switcher, WordPress, ClassicPress
Requires at least: 3.4
Tested up to: 5.0.0
Stable tag: 2.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows users to easily switch themes (ideal for allowing light/dark mode).

Forked from original Theme Switcher in order to bring code up to current standards.

== Description ==
Allows users to easily switch themes (ideal for allowing light/dark mode).

Theme switcher functionality is made available to users via a widget; settings done via widget administration.

Settings available to display available themes as a list or select drop-down; widget admin allows themes with certain prefix to be excluded and/or to include only themes containing a certain word or part of a word.

As an alternative to using the widget, the function azc_ts_theme_switcher() can be called directly; add 'dropdown' as a parameter to have the select drop-down, instead of the list, of themes returned.

== Installation ==
1. Download and extract the azurecurve-theme-switcher plugin files.
2. Upload the azurecurve-theme-switcher directory to the /wp-content/plugins/ directory.
3. Activate the plugin under the Plugins menu in the WordPress administration panel.
4. Add the azurecurve Theme Switcher widget to one of your sidebars (select list/drop down and configure other parameters if required), or call azc_ts_theme_switcher() directly.

== Frequently Asked Questions ==
= Is this plugin compatible with both WordPress and ClassicPress? =
* Yes, this plugin will work with both.

== Screenshots ==
1. The Theme Switcher widget allows you to set the title of the widget and to choose the "list" or "dropdown" option.
2. The Theme Switcher widget in action on the sidebar.

== Changelog ==
Changes and feature additions for the Theme Switcher plugin:
= 2.2.0 =
* Fix issues with format of ul and select
= 2.1.1 =
* Move menu to includes folder for easier maintenance
= 2.1.0 =
* Update azurecurve menu
* Correct error with widget update under PHP7
= 2.0.0 =
* Add azurecurve menu
= 1.0.0 =
* Add code from Daniele Scasciafratte (@mte90) to keep user on current page when switching theme
* Add optional theme prefix to ignore (useful for ignoring parent themes)
* Add optional filter to only include themes including supplied value
* Replace deprecated WordPress functions with current ones
* Fork from Theme Switcher