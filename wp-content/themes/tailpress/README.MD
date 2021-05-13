<p><img src="https://tailpress.io/images/logo.svg" width="200" alt="Laravel Mix"></p>

# Introduction
TailPress is a minimal boilerplate theme for WordPress using [TailwindCSS](https://tailwindcss.com/), with [PostCSS](https://postcss.org) and [Laravel Mix](https://laravel-mix.com/).

## Getting started
* Clone repo `git clone https://github.com/jeffreyvr/tailpress.git && cd tailpress`
* Run `rm -rf .git` to remove git
* Run `npm install`
* Run `npm run development`
* Run `npm run watch` to start developing

You will find the editable CSS and Javascript files within the `/resources` folder.

Before you use your theme in production, make sure you run `npm run production`.

## Block editor support
TailPress comes with basic support for the [block editor](https://wordpress.org/support/article/wordpress-editor/).

CSS-classes for alignment, background and text colors will be generated automatically. You can modify this within the `tailwind.config.js` file.

To make the editing experience within the block editor more in line with the front end styling, a `editor-style.css` is generated.

### Define theme colors
Four colors (primary, secondary, dark and light) are defined from the beginning. You can modify the colors in `tailpress.json`.

### Define theme font sizes
You can modify the font sizes within `tailpress.json`.

## JIT
[Tailwind CSS JIT](https://tailwindcss.com/docs/just-in-time-mode#enabling-jit-mode) is used to allow for fast compiling.

## PurgeCSS
By default, PurgeCSS is enabled. You can modify or disable it by changing the settings in the `tailwind.config.js` file. There are several [PurgeCSS options](https://tailwindcss.com/docs/optimizing-for-production#purge-css-options).

## Links
* [TailPress website](https://tailpress.io)
* [Screencasts](https://www.youtube.com/playlist?list=PL6GBdOp044SHIOSCZejodwr1HcYsC43wG)
* [TailwindCSS Documentation](https://tailwindcss.com/docs)
* [Laravel Mix Documentation](https://laravel-mix.com/docs)

## Contributors
* [Jeffrey van Rossum](https://github.com/jeffreyvr)
* [All contributors](https://github.com/jeffreyvr/tailpress/graphs/contributors)

## License
GNU GPL version 2. Please see the [License File](/LICENSE) for more information.