theme-mediaelement
=====================

Base stylesheet for theme developers to work with the WordPress media player (mediaelement.js).

This project was created because it's pretty tough to overwrite the style rules for the media player in WordPress.  The reasons for this are:

* WordPress loads a stylesheet in the footer.
* The stylesheet has extremely specific selectors that force you to write something more specific.
* The stylesheet has `!important` in several places.

As a theme author, I didn't want to have to write something like the following:

	.mejs-container .mejs-controls .mejs-button button { width: 100px !important; }

What this project does is give you an easier way to handle this without a ton of crazy CSS and `!important` overrides.  It is a base stylesheet with fairly minimal styling (based off the original `medialement.css` skin).  You can use it to add custom styles by overwriting just the parts you need or you can completely fork the project and do what you want with it.

## Installation

To use this project, I highly recommend creating a sub-module in your theme's `/css` folder if you're using Git.  That way, it's much easier to keep up with updates.  Otherwise, do the following.

* Download a copy of ZIP file for this project.
* Extract the ZIP to `your-theme/css/mediaelement`.
* Load the `mediaelement.min.css` file (see below).
* Disable the WordPress Mediaelement.js styles (see below).

### Loading the stylesheet

There are a few ways to copy the stylesheet into your theme.

#### wp_enqueue_style()

My preferred method is to enqueue the stylesheet via my theme's `functions.php` file.

	add_action( 'wp_enqueue_scripts', 'my_enqueue_styles' );

	function my_enqueue_styles() {
		wp_enqueue_style( 
			'my-mediaelement', 
			trailingslashit( get_template_directory_uri() ) . 'css/mediaelement/mediaelement.min.css',
			null,
			'20130902'
		);
	}

#### @import

You can also import it from your theme's `style.css` file.

	@import url( 'css/mediaelement/mediaelement.min.css' );

#### Copy/Paste

Another method is to copy all of `mediaelement.css` into your theme's `style.css` and make direct changes there.

### Disable WordPress' Mediaelement.js styles

Add the following code to your theme's `functions.php` file to disable the WordPress stylesheets from loading.  This is an important step because it will allow our stylesheet to take over.

	add_action( 'wp_enqueue_scripts', 'my_deregister_styles' );

	function my_deregister_styles() {
		wp_deregister_style( 'mediaelement' );
		wp_deregister_style( 'wp-mediaelement' );
	}

## Background Images

The stylesheet has the background images commented out by default.  This is so that themes aren't unnecessarily loading extra resources.  If you wish to use the default background images, use the following CSS code (correct the paths if necessary).

	.mejs-overlay-button {
		background: url( 'images/bigplay.png' ) no-repeat;
	}

	.mejs-overlay-loading span {
		background: transparent url( 'images/loading.gif' ) 50% 50% no-repeat;
	}

	.mejs-button button {
		background: transparent url( 'images/controls.png' ) no-repeat;
	}

## Structure

Basic structure of the media player:

	<div class="mejs-container">
		<div class="mejs-inner">
			<div class="mejs-mediaelement">
				<audio class="wp-audio-shortcode">...</audio>
			</div>
			<div class="mejs-layers">
				<div class="mejs-layer">...</div>
			</div>
			<div class="mejs-controls">
				<div class="mejs-button"></div>
			</div>
			<div class="mejs-clear">...</div>
		</div>
	</div>

## Development

Follow the "/dev" branch for the latest updates.  Please make pull requests and patches against that branch rather than the master branch.

https://github.com/justintadlock/theme-mediaelement/tree/dev

## Resources

* http://mediaelementjs.com