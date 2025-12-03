=== Twenty8teen ===
Contributors: joyously
Tags: blog, custom-background, custom-colors, custom-header, custom-logo, custom-menu, editor-style, featured-images, footer-widgets, microformats, rtl-language-support, theme-options, threaded-comments, translation-ready, two-columns
Tested up to: 6.8
Stable tag: 20250303
License: GNU General Public License v2 or later
License URI: LICENSE

A lightweight, block-oriented theme.

== Description ==
* Block oriented by using widgets
* easily add user styles to checkbox grid
* save and load subsets of theme options (presets)
* apply a different preset to each page
* works with any font or screen size
* option for balanced display of posts regardless of number
* any Google fonts, with suggested fonts
* CSS variables for easy user changes and dynamic CSS
* template function to generate unique gradients for text
* Child theme friendly (load style sheet, lots of filters, template files are widgets too, child demo)
* Microformats done right
* script to add CSS vendor prefixes so CSS is smaller, and works on user CSS

The theme defines classes for you to use in content or just for
styling the site. These classes, along with user-defined classes, are
available on the Formats selector in the Classic Editor and Classic
Block, and also on all widgets.
All of the Area Styles can be used in content or changed, and it is
simple to add your own style to the checkbox grid by entering the
class name in the Theme Advanced section of the Customizer.

Each page/post has a body class of name-{slug}, so you can style for
instance
 .name-hello-world { font-size: 110% }
These body classes, as well as the WordPress standard classes, can be
used to identify when to apply presets per page. See the Theme
Advanced section of the Customizer, where you choose which body class
to associate with a preset, making each one unique.
The body font has a class of body-font, and the titles have titles-
font. These can be used in content.
Each image size has a class for the default featured images to size
called wrapped-media-size-{size}  (e.g. wrapped-media-size-
thumbnail).

There are many CSS Custom Properties, which can be set in a
hierarchy, but be careful, as this won't work correctly in old
browsers. The theme CSS tries to only set Custom Properties on :root.
The mouse position is tracked as `--mouse-x` and `--mouse-y`,
so that can be used for interactive effects. The theme defines many
of the Area Styles as Custom Properties so that they can easily be
modified and mixed (like in multiple backgrounds), due to the
checkboxes being independent of each other.
As of 20210511, the widths are custom properties using a calculation.
If you use a very narrow font, you might need to adjust this ammount.

The taxonomy root page template uses the page slug for the
taxonomy slug to list the terms (with counts) below the content of
the page.

The presets are saved only when the Create Preset button is used.
Nothing happens with the presets when the Customizer Publish
button is used.

The default entry footer uses `the_taxonomies()` function to show all
taxonomies for the post type. To reduce the ones shown or to show
custom fields also, copy the loop-parts/entry-footer.php template
part to your child theme and change it there.

As of 20200911, the post format and/or the post type can be used on the
loop-parts. To use, turn on the option in Advanced, and copy the template
part you want to change to your child theme and name it like
entry-content-slug.php (for a post type of slug or a post format of slug).
Don't choose it in the widget, however, unless that's the only type you have
on your site. Choose the generic one in the widget and the specific one
will be used when it matches.

This theme uses core functionality for header text color, which was
apparently only concerned with the site title and description. If you want
text in the header to be a different color, and also hide the site title,
use Additional CSS in the Customizer to set the color, like
:root { --header_textcolor: #c0ffee; }

== Frequently Asked Questions ==
**Why aren't the widgets stored in the presets?**
Widgets are handled by WordPress, not the theme. The data is stored
separately from the theme options in the database.

**How do I show different widgets on different pages if they aren't in the presets?**
The best way is to use a plugin such as Dynamic Widgets or If So or Widget
Context, so the settings are not lost on theme switch.

**Why can't I put styles on core widgets?**
Those core widgets don't have any filters for the theme to insert the classes!
But you can select styles to apply to 'widgets' in the checkbox grid. The
'widget' class is applied to widgets in the sidebar and footer only.
As of version 20200614, the same list of classes is available for all widgets.
This includes the User Classes entered in the Advanced section of Customizer.
This was originally omitted from the change log; sorry about that.

**How do I change how something is output?**
Make a child theme! All you need is a style.css file that shows the
parent as twenty8teen, and then put your copy of the template file in
the child theme template-parts folder or the loop-parts folder. (See
the included child-demo, which does a little more.)
Version 20200911 makes it easier for a template to match the loop widget, by
including the post type as a template part name suffix from the widget when
you choose that option in Advanced.

**How do I move the theme options from parent to child or to my other site?**
You can move theme options using the Customizer Import/Export plugin.
But be wary of presets, since they are part of the theme options of
both the source and destination. Version 20190510 added code to
merge the preset options when an import calls the
`customize_save_{name}` action for the preset option (instead of it
overwriting).

**Does this theme work on ClassicPress?**
Yes
Both editors are styled.

== Changelog ==
= 20250303 =
* fix block editor loading of styles for use with iframe (since WP 6.3)
* fix accordion in Customizer preset list (broken by WP 6.7 change)
* modernize editor styles to use :is()

= 20250201 =
* specify good default for new view-transition CSS feature

= 20221101 =
* add check for subscript for widgets

= 20221027 =
* use sanitize_key for filtered attribute names
* tweak print style to account for text on top of color
* remove extra :is() in summary style
* add option to control use of prefixfree script (no UI so it's not saved to database)

= 20220913 =
* fix print style min-height

See changelog.txt for history

== Credits ==
Twenty8teen WordPress Theme, Copyright 2018 Joy Reynolds
Twenty8teen is distributed under the terms of the GNU GPLv2 or later.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see
https://www.gnu.org/licenses/gpl-2.0.txt.

Twenty8teen WordPress Theme is derived from Underscores
WordPress Theme, http://underscores.me/, Copyright 2012-2017
Automattic, Inc. Underscores WordPress Theme is distributed under
the terms of the GPLv2 or later (https://www.gnu.org/licenses/gpl-2.0.html)

Twenty8teen WordPress Theme incorporates code from
Justin Tadlock, described at http://justintadlock.com/archives/2015/05/26/multiple-checkbox-customizer-control

Twenty8teen WordPress Theme bundles the following third-party
resources:

normalize.css, (C) 2012-2016 Nicolas Gallagher and Jonathan Neal
MIT license (http://opensource.org/licenses/MIT)
Source: http://necolas.github.io/normalize.css/

prefixfree javascript library,
MIT license (http://opensource.org/licenses/MIT)
Source: http://leaverou.github.io/prefixfree/

conic-gradient javascript library,
MIT license (http://opensource.org/licenses/MIT)
Source: http://leaverou.github.io/conic-gradient/

auticons icon font, Copyright 2013, Heydon Pickering
WTFPL license - http://sam.zoy.org/wtfpl/
Source: http://www.heydonworks.com

Twenty8teen WordPress Theme uses these two fonts from
Google Fonts by default: Amarante font, Copyright (c) 2012 by
Sorkin Type Co (www.sorkintype.com),
with Reserved Font Name "Amarante".
SIL Open Font License, Version 1.1 - http://scripts.sil.org/OFL

Convergence font,
Copyright (c) 2010, 2011 José Nicolás Silva Schwarzenberg <info@nsilva.com>,
Copyright (c) 2010, 2011 John Vargas Beltrán
(john.vargasbeltran@gmail.com),
with Reserved Font Names "Convergence".
SIL Open Font License, Version 1.1 - http://scripts.sil.org/OFL
