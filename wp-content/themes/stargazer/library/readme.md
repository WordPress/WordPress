# Hybrid Core: WordPress Theme Framework

Hybrid Core is a framework for developing WordPress themes.  It allows theme developers to build themes without having to code much of the complex "logic" or other complicated functionality often needed in themes.  The framework takes care of a lot of these things so theme authors can get back to doing what matter the most:  developing and designing cool themes.  

The framework was built to make it easy for developers to include (or not include) specific, pre-coded features.  Themes handle all the markup, style, and scripts while the framework handles the logic.

## FAQ ##

### Why was this framework built? ###

In 2008, I started work on Hybrid Core.  At the time, it was used in only one of my themes on my newly-launched site, [Theme Hybrid](http://themehybrid.com).  The idea was that I'd have a base of reusable scripts that I could use along with any theme I built in the future.  Eventually, others wanted to use it too, so I officially released it to the public.  Basically, I decided to share the framework that had gotten my own business started with the entire WordPress community.

### Who is Hybrid Core for? ###

Primarily, Hybrid Core is for me, Justin Tadlock.  I use it to build all of my [WordPress themes](http://themehybrid.com/themes) at Theme Hybrid, which is my plugin and theme site.

However, I also maintain it for other theme authors who need a solid framework behind their themes that doesn't force them into certain markup and gives them ultimate flexibility.

### How do I install Hybrid Core? ###

The most basic method is to add the framework folder to your theme folder.  Assuming a folder name of `hybrid-core` (you can name the folder anything), you'd add this code to your theme's `functions.php` file:

	/* Launch the Hybrid Core framework. */
	require_once( trailingslashit( get_template_directory() ) . 'hybrid-core/hybrid.php' );
	new Hybrid();

That will load and initialize the framework.  You'll have to learn the ins-and-outs of the framework though to actually make use of it.  The code itself is very well documented, but I also have [documentation](http://themehybrid.com/docs/for/hybrid-core) available to club members at Theme Hybrid.

### Can I install Hybrid Core as a theme? ###

No, Hybrid Core is not a theme.  It is a framework that you drop into your theme folder to help you build themes.

### Wait. Aren't frameworks parent themes? ###

No, not really, they're not.  Unfortunately, many theme authors have co-opted the term "framework" and applied it to themes intended to be used solely as parent themes.  I suppose you could stretch the term framework to pretty much apply to any base code that you can build from, but this has meant for some confusing terminology in the WordPress community.  We already have a term for what these "framework" themes are &mdash; they're called "parent themes".  If you're interested in reading more on this topic, I recommend checking out "[Frameworks? Parent, child, and  grandchild themes?](http://justintadlock.com/archives/2010/08/16/frameworks-parent-child-and-grandchild-themes)" for an in-depth discussion on the subject.

### So, I can't have child themes? ###

You can't create child themes for Hybrid Core because it's not a theme.  However, Hybrid Core was built so that theme authors could create awesome parent (or standalone) themes.  You, your users, or other theme authors can build child themes for your themes.

I was one of the original theme authors to help pioneer the parent/child theme movement.  I'm a big believer in child themes being the absolute best way to make theme customizations.  Therefore, Hybrid Core is built with this idea in mind.  As you learn more about the framework, you'll understand how it makes child theme development even better.

### Can I contribute to Hybrid Core? ###

Certainly.  The code for the framework is handled via its [GitHub Repository](https://github.com/justintadlock/hybrid-core).  You can open tickets, create patches, and send pull requests there.

Please don't make pull requests against the `master` branch.  This is the latest, stable code.  You can make a pull request against one of the point branches or the `dev` (future release) branch.

## Copyright and License ##

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

2008&thinsp;&ndash;&thinsp;2014 &copy; [Justin Tadlock](http://justintadlock.com).

## Changelog ##

You can see the changes made via the [commit log](https://github.com/justintadlock/hybrid-core/commits/master) for the latest release.

### Changelogs for each release ###

* [2.0.0](https://github.com/justintadlock/hybrid-core/commits/2.0)
* [1.6.2](https://github.com/justintadlock/hybrid-core/tree/1.6.2)
* [1.6.1](https://github.com/justintadlock/hybrid-core/tree/1.6.1)
* [1.6.0](https://github.com/justintadlock/hybrid-core/tree/1.6.0)
* [1.5.5](https://github.com/justintadlock/hybrid-core/tree/1.5.5)
* [1.5.4](https://github.com/justintadlock/hybrid-core/tree/1.5.4)
* [1.5.3](https://github.com/justintadlock/hybrid-core/tree/1.5.3)
* [1.5.2](https://github.com/justintadlock/hybrid-core/tree/1.5.2)
* [1.5.1](https://github.com/justintadlock/hybrid-core/tree/1.5.1)
* [1.5.0](https://github.com/justintadlock/hybrid-core/tree/1.5)
* [1.4.3](https://github.com/justintadlock/hybrid-core/tree/1.4.3)
* [1.4.2](https://github.com/justintadlock/hybrid-core/tree/1.4.2)
* [1.4.1](https://github.com/justintadlock/hybrid-core/tree/1.4.1)
* [1.4.0](https://github.com/justintadlock/hybrid-core/tree/1.4)
* [1.3.1](https://github.com/justintadlock/hybrid-core/tree/1.3.1)
* [1.3.0](https://github.com/justintadlock/hybrid-core/tree/1.3)
* [1.2.1](https://github.com/justintadlock/hybrid-core/tree/1.2.1)
* [1.2.0](https://github.com/justintadlock/hybrid-core/tree/1.2)
* [1.1.1](https://github.com/justintadlock/hybrid-core/tree/1.1.1)
* [1.1.0](https://github.com/justintadlock/hybrid-core/tree/1.1)