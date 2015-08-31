<?php
/*
Plugin Name: W3 Total Cache
Description: The highest rated and most complete WordPress performance plugin. Dramatically improve the speed and user experience of your site. Add browser, page, object and database caching as well as minify and content delivery network (CDN) to WordPress.
Version: 0.9.4.1
Plugin URI: http://www.w3-edge.com/wordpress-plugins/w3-total-cache/
Author: Frederick Townes
Author URI: http://www.linkedin.com/in/w3edge
Network: True
*/

/*  Copyright (c) 2009 Frederick Townes <ftownes@w3-edge.com>
    Portions of this distribution are copyrighted by:
		Copyright (c) 2008 Ryan Grove <ryan@wonko.com>
		Copyright (c) 2008 Steve Clay <steve@mrclay.org>
                Copyright (c) 2007 Matt Mullenweg
                Copyright (c) 2007 Andy Skelton
                Copyright (c) 2007 Iliya Polihronov
                Copyright (c) 2007 Michael Adams
                Copyright (c) 2007 Automattic Inc.
                Ryan Boren
	All rights reserved.

	W3 Total Cache is distributed under the GNU General Public License, Version 2,
	June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
	St, Fifth Floor, Boston, MA 02110, USA

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

if (!defined('ABSPATH')) {
    die();
}

/**
 * Abort W3TC loading if WordPress is upgrading
 */
if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;

include dirname(__FILE__) . '/lib/EDD/integration.php';

if (!defined('W3TC_IN_MINIFY')) {
    /**
     * Require plugin configuration
     */
    require_once dirname(__FILE__) . '/inc/define.php';
    
    // Load the wp cli command - if run from wp-cli
    if (defined('WP_CLI') && WP_CLI)
      w3_require_once(W3TC_LIB_W3_DIR . '/Cli.php');

    /**
     * Run
     */
    $root = w3_instance('W3_Root');
    $root->run();
}
