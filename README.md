# FreakPress

FreakPress is Wordpress fork that is configured to run on the [Pantheon platform](https://www.getpantheon.com). This fork is definitely for the developers and is NOT for production use. 

> NOTE: [Pantheon](https://www.getpantheon.com) is a Drupal platform and as such no questions regarding Wordpress or Freakpress will be supported. For any bugs or error reports, submit them to the Freakpress [issue queue](https://github.com/nstielau/WordPress/issues?page=1&state=open).

Pantheon is development platform optimized and configured to run high performance Drupal sites. There is built-in support for features such as Varnish, redis, Apache Solr, New Relic, Nginx, PHP-FPM, MySQL, PhantomJS and more. 

## Getting Started

The goal is to keep Freakpress as close to Wordpress as possible and avoid hacking the core. There are four steps to getting a Freakpress install up and running. 

### 1. Spin-up a site

Go to the spin-up screen and create your free development site. If you do not have an account, you can create it during the spin-up process.

![alt](http://i.imgur.com/nge9oyE.png, '')

### 2. Load up the site

When the spin-up process is complete, you will be redirected to the site's dashboard. Click on the link under the site's name to access the Dev environment.

![alt](http://i.imgur.com/2wjCj9j.png?1, '')

### 3. Run the Wordpress installer 

How about the Wordpress database config screen? No need to worry about database connection information as that is taken care of in the background. The only step that you need to complete is the site information and the installation process will be complete.

We will post more information about how this works but we recommend developers take a look at `wp-congfig.php` to get an understanding.

![alt](http://i.imgur.com/4EOcqYN.png, '')

### 4. Enjoy!

![alt](http://i.imgur.com/DwFe35s.png, '') 


## Recommended Plugins - _Unstable_

There are a number of plugins can be added to improve the performance and functionality of Freakpress. These have not been fully tested and may require some work to be 100% functional

- _[W3 Total Cache](http://wordpress.org/plugins/w3-total-cache/)_
- _[Solr for Wordpress](http://wordpress.org/plugins/solr-for-wordpress/)_ 
- _[wp-shell](http://wordpress.org/plugins/solr-for-wordpress/) - (Not available)_ 
 
## Breaking changes

There are some breaking changes that should be considered before using Freakpress.

#### Relative URLs

The Pantheon workflow includes three separate environments to work on your site by default - Dev, Test and Live. Through the development process the URLs for the site will change depending on the environment being accessed. 

Absolute URLs are standard in Wordpress which can lead to broken links and other quirks. Please be aware of this as some plugins follow this convention so functionality may break as code is moved between environments. 
 
#### Cookies

Pantheon is configured for Drupal so the cookie scheme is set to the Drupal convention of the _SESS_ prefix for all cookies. This is handled in `wp-config.php` by setting configuration variables. For any custom modules that rely on cookies, it will be important to keep this in mind.

If cookies are detected they will be stripped out and this will also prevent Varnish from caching and serving requests. 

## Troubleshooting & Useful Tools 

_@TODO_

## Contributing changes
  
This is an experimental version but if there are any bug reports, or feature enhancements then file a request on the Github [issue queue](https://github.com/nstielau/WordPress/issues?page=1&state=open). 

_@TODO_

Fork the repo and create a pull request!
 
