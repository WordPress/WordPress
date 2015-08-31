<?php

/*
 * Descriptors of configuration keys
 * for config
 */

$keys = array(
    'cluster.messagebus.debug' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cluster.messagebus.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cluster.messagebus.sns.region' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cluster.messagebus.sns.api_key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cluster.messagebus.sns.api_secret' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cluster.messagebus.sns.topic_arn' => array(
        'type' => 'string',
        'default' => ''
    ),

    'dbcache.debug' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'dbcache.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'dbcache.engine' => array(
        'type' => 'string',
        'default' => 'file'
    ),
    'dbcache.file.gc' => array(
        'type' => 'integer',
        'default' => 3600
    ),
    'dbcache.file.locking' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'dbcache.lifetime' => array(
        'type' => 'integer',
        'default' => 180
    ),
    'dbcache.memcached.persistant' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'dbcache.memcached.servers' => array(
        'type' => 'array',
        'default' => array(
            '127.0.0.1:11211'
        )
    ),
    'dbcache.reject.cookie' => array(
        'type' => 'array',
        'default' => array()
    ),
    'dbcache.reject.logged' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'dbcache.reject.sql' => array(
        'type' => 'array',
        'default' => array(
            'gdsr_',
            'wp_rg_'
        )
    ),
    'dbcache.reject.uri' => array(
        'type' => 'array',
        'default' => array()
    ),
    'dbcache.reject.words' => array(
        'type' => 'array',
        'default' =>  array(
            '^\s*insert\b',
            '^\s*delete\b',
            '^\s*update\b',
            '^\s*replace\b',
            '^\s*create\b',
            '^\s*alter\b',
            '^\s*show\b',
            '^\s*set\b',
            '\bautoload\s+=\s+\'yes\'',
            '\bsql_calc_found_rows\b',
            '\bfound_rows\(\)',
            '\bw3tc_request_data\b'
        )
    ),

    'objectcache.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'objectcache.debug' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'objectcache.engine' => array(
        'type' => 'string',
        'default' => 'file'
    ),
    'objectcache.file.gc' => array(
        'type' => 'integer',
        'default' => 3600
    ),
    'objectcache.file.locking' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'objectcache.memcached.servers' => array(
        'type' => 'array',
        'default' => array(
            '127.0.0.1:11211'
        )
    ),
    'objectcache.memcached.persistant' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'objectcache.groups.global' => array(
        'type' => 'array',
            'default' => array(
            'users',
            'userlogins',
            'usermeta',
            'user_meta',
            'site-transient',
            'site-options',
            'site-lookup',
            'blog-lookup',
            'blog-details',
            'rss',
            'global-posts'
        )
    ),
    'objectcache.groups.nonpersistent' => array(
        'type' => 'array',
        'default' => array(
            'comment',
            'counts',
            'plugins'
        )
    ),
    'objectcache.lifetime' => array(
        'type' => 'integer',
        'default' => 180
    ),
    'objectcache.purge.all' => array(
        'type' => 'boolean',
        'default' => false
    ),

    'fragmentcache.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'fragmentcache.debug' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'fragmentcache.engine' => array(
        'type' => 'string',
        'default' => 'file'
    ),
    'fragmentcache.file.gc' => array(
        'type' => 'integer',
        'default' => 3600
    ),
    'fragmentcache.file.locking' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'fragmentcache.memcached.servers' => array(
        'type' => 'array',
        'default' => array(
            '127.0.0.1:11211'
        )
    ),
    'fragmentcache.memcached.persistant' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'fragmentcache.lifetime' => array(
        'type' => 'integer',
        'default' => 180
    ),
    'fragmentcache.groups' => array(
        'type' => 'array',
        'default' => array()
    ),

    'pgcache.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.comment_cookie_ttl' => array(
        'type' => 'integer',
        'default' => 1800
    ),
    'pgcache.debug' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.engine' => array(
        'type' => 'string',
        'default' => 'file_generic'
    ),
    'pgcache.file.gc' => array(
        'type' => 'integer',
        'default' => 3600
    ),
    'pgcache.file.nfs' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.file.locking' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.lifetime' => array(
        'type' => 'integer',
        'default' => 3600
    ),
    'pgcache.memcached.servers' => array(
        'type' => 'array',
        'default' => array(
            '127.0.0.1:11211'
        )
    ),
    'pgcache.memcached.persistant' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'pgcache.check.domain' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.cache.query' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'pgcache.cache.home' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'pgcache.cache.feed' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.cache.nginx_handle_xml' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.cache.ssl' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.cache.404' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.cache.flush' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.cache.headers' => array(
        'type' => 'array',
        'default' => array(
            'Last-Modified',
            'Content-Type',
            'X-Pingback',
            'P3P'
        )
    ),
    'pgcache.compatibility' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.remove_charset' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.accept.uri' => array(
        'type' => 'array',
        'default' => array(
            'sitemap(_index)?\.xml(\.gz)?',
            '[a-z0-9_\-]+-sitemap([0-9]+)?\.xml(\.gz)?'
        )
    ),
    'pgcache.accept.files' => array(
        'type' => 'array',
        'default' => array(
            'wp-comments-popup.php',
            'wp-links-opml.php',
            'wp-locations.php'
        )
    ),
    'pgcache.accept.qs' => array(
        'type' => 'array',
        'default' => array()
    ),
    'pgcache.reject.front_page' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.reject.logged' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'pgcache.reject.logged_roles' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.reject.roles' => array(
        'type' => 'array',
        'default' => array()
    ),
    'pgcache.reject.uri' => array(
        'type' => 'array',
        'default' => array(
            'wp-.*\.php',
            'index\.php'
        )
    ),
    'pgcache.reject.ua' => array(
        'type' => 'array',
        'default' => array()
    ),
    'pgcache.reject.cookie' => array(
        'type' => 'array',
        'default' => array('wptouch_switch_toggle')
    ),
    'pgcache.reject.request_head' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.front_page' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.home' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'pgcache.purge.post' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'pgcache.purge.comments' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.author' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.terms' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.archive.daily' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.archive.monthly' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.archive.yearly' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.feed.blog' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'pgcache.purge.feed.comments' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.feed.author' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.feed.terms' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.purge.feed.types' => array(
        'type' => 'array',
        'default' => array(
            'rss2'
        )
    ),
    'pgcache.purge.postpages_limit' => array(
        'type' => 'integer',
        'default' => 10
    ),
    'pgcache.purge.pages' => array(
        'type' => 'array',
        'default' => array()
    ),
    'pgcache.purge.sitemap_regex' => array(
        'type' => 'string',
        'default' => '([a-z0-9_\-]*?)sitemap([a-z0-9_\-]*)?\.xml'
    ),
    'pgcache.prime.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'pgcache.prime.interval' => array(
        'type' => 'integer',
        'default' => 900
    ),
    'pgcache.prime.limit' => array(
        'type' => 'integer',
        'default' => 10
    ),
    'pgcache.prime.sitemap' => array(
        'type' => 'string',
        'default' => ''
    ),
    'pgcache.prime.post.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),

    'minify.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.auto' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.debug' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.engine' => array(
        'type' => 'string',
        'default' => 'file'
    ),
    'minify.file.gc' => array(
        'type' => 'integer',
        'default' => 86400
    ),
    'minify.file.nfs' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.file.locking' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.memcached.servers' => array(
        'type' => 'array',
        'default' => array(
            '127.0.0.1:11211'
        )
    ),
    'minify.memcached.persistant' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.rewrite' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.options' => array(
        'type' => 'array',
        'default' => array()
    ),
    'minify.symlinks' => array(
        'type' => 'array',
        'default' => array()
    ),
    'minify.lifetime' => array(
        'type' => 'integer',
        'default' => 86400
    ),
    'minify.upload' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.html.enable' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.html.engine' => array(
        'type' => 'string',
        'default' => 'html'
    ),
    'minify.html.reject.feed' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.html.inline.css' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.html.inline.js' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.html.strip.crlf' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.html.comments.ignore' => array(
        'type' => 'array',
        'default' => array(
            'google_ad_',
            'RSPEAK_'
        )
    ),
    'minify.css.enable' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.css.engine' => array(
        'type' => 'string',
        'default' => 'css'
    ),
    'minify.css.combine' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.css.strip.comments' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.css.strip.crlf' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.css.imports' => array(
        'type' => 'string',
        'default' => ''
    ),
    'minify.css.groups' => array(
        'type' => 'array',
        'default' => array()
    ),
    'minify.js.enable' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.js.engine' => array(
        'type' => 'string',
        'default' => 'js'
    ),
    'minify.js.combine.header' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.js.header.embed_type' => array(
        'type' => 'string',
        'default' => 'blocking'
    ),
    'minify.js.combine.body' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.js.body.embed_type' => array(
        'type' => 'string',
        'default' => 'blocking'
    ),
    'minify.js.combine.footer' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.js.footer.embed_type' => array(
        'type' => 'string',
        'default' => 'blocking'
    ),
    'minify.js.strip.comments' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.js.strip.crlf' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.js.groups' => array(
        'type' => 'array',
        'default' => array()
    ),
    'minify.yuijs.path.java' => array(
        'type' => 'string',
        'default' => 'java'
    ),
    'minify.yuijs.path.jar' => array(
        'type' => 'string',
        'default' => 'yuicompressor.jar'
    ),
    'minify.yuijs.options.line-break' => array(
        'type' => 'integer',
        'default' => 5000
    ),
    'minify.yuijs.options.nomunge' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.yuijs.options.preserve-semi' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.yuijs.options.disable-optimizations' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.yuicss.path.java' => array(
        'type' => 'string',
        'default' => 'java'
    ),
    'minify.yuicss.path.jar' => array(
        'type' => 'string',
        'default' => 'yuicompressor.jar'
    ),
    'minify.yuicss.options.line-break' => array(
        'type' => 'integer',
        'default' => 5000
    ),
    'minify.ccjs.path.java' => array(
        'type' => 'string',
        'default' => 'java'
    ),
    'minify.ccjs.path.jar' => array(
        'type' => 'string',
        'default' => 'compiler.jar'
    ),
    'minify.ccjs.options.compilation_level' => array(
        'type' => 'string',
        'default' => 'SIMPLE_OPTIMIZATIONS'
    ),
    'minify.ccjs.options.formatting' => array(
        'type' => 'string',
        'default' => ''
    ),
    'minify.csstidy.options.remove_bslash' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.csstidy.options.compress_colors' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.csstidy.options.compress_font-weight' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.csstidy.options.lowercase_s' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.csstidy.options.optimise_shorthands' => array(
        'type' => 'integer',
        'default' => 1
    ),
    'minify.csstidy.options.remove_last_;' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.csstidy.options.case_properties' => array(
        'type' => 'integer',
        'default' => 1
    ),
    'minify.csstidy.options.sort_properties' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.csstidy.options.sort_selectors' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.csstidy.options.merge_selectors' => array(
        'type' => 'integer',
        'default' => 2
    ),
    'minify.csstidy.options.discard_invalid_properties' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.csstidy.options.css_level' => array(
        'type' => 'string',
        'default' => 'CSS2.1'
    ),
    'minify.csstidy.options.preserve_css' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.csstidy.options.timestamp' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.csstidy.options.template' => array(
        'type' => 'string',
        'default' => 'default'
    ),
    'minify.htmltidy.options.clean' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.htmltidy.options.hide-comments' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'minify.htmltidy.options.wrap' => array(
        'type' => 'integer',
        'default' => 0
    ),
    'minify.reject.logged' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'minify.reject.ua' => array(
        'type' => 'array',
        'default' => array()
    ),
    'minify.reject.uri' => array(
        'type' => 'array',
        'default' => array()
    ),
    'minify.reject.files.js' => array(
        'type' => 'array',
        'default' => array()
    ),
    'minify.reject.files.css' => array(
        'type' => 'array',
        'default' => array()
    ),
    'minify.cache.files' => array(
        'type' => 'array',
        'default' => array('https://ajax.googleapis.com')
    ),

    'cdn.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.debug' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.engine' => array(
        'type' => 'string',
        'default' => 'ftp'
    ),
    'cdn.uploads.enable' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'cdn.includes.enable' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'cdn.includes.files' => array(
        'type' => 'string',
        'default' => '*.css;*.js;*.gif;*.png;*.jpg;*.xml'
    ),
    'cdn.theme.enable' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'cdn.theme.files' => array(
        'type' => 'string',
        'default' => '*.css;*.js;*.gif;*.png;*.jpg;*.ico;*.ttf;*.otf,*.woff'
    ),
    'cdn.minify.enable' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'cdn.custom.enable' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'cdn.custom.files' => array(
        'type' => 'array',
        'default' => array(
            'favicon.ico',
            '{wp_content_dir}/gallery/*',
            '{wp_content_dir}/uploads/avatars/*',
            '{plugins_dir}/wordpress-seo/css/xml-sitemap.xsl',
            '{plugins_dir}/wp-minify/min*',
            '{plugins_dir}/*.js',
            '{plugins_dir}/*.css',
            '{plugins_dir}/*.gif',
            '{plugins_dir}/*.jpg',
            '{plugins_dir}/*.png',
        )
    ),
    'cdn.import.external' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.import.files' => array(
        'type' => 'string',
        'default' => false
    ),
    'cdn.queue.interval' => array(
        'type' => 'integer',
        'default' => 900
    ),
    'cdn.queue.limit' => array(
        'type' => 'integer',
        'default' => 25
    ),
    'cdn.force.rewrite' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.autoupload.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.autoupload.interval' => array(
        'type' => 'integer',
        'default' => 3600
    ),
    'cdn.canonical_header' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.ftp.host' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.ftp.user' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.ftp.pass' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.ftp.path' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.ftp.pasv' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.ftp.domain' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.ftp.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.s3.key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.s3.secret' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.s3.bucket' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.s3.cname' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.s3.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.cf.key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cf.secret' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cf.bucket' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cf.id' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cf.cname' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.cf.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.cf2.key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cf2.secret' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cf2.id' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cf2.cname' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.cf2.ssl' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.rscf.user' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.rscf.key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.rscf.location' => array(
        'type' => 'string',
        'default' => 'us'
    ),
    'cdn.rscf.container' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.rscf.cname' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.rscf.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.azure.user' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.azure.key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.azure.container' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.azure.cname' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.azure.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.mirror.domain' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.mirror.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.netdna.alias' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.netdna.consumerkey' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.netdna.consumersecret' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.netdna.authorization_key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.netdna.domain' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.netdna.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.netdna.zone_id' => array(
        'type' => 'integer',
        'default' => 0
    ),
    'cdn.maxcdn.authorization_key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.maxcdn.domain' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.maxcdn.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.maxcdn.zone_id' => array(
        'type' => 'integer',
        'default' => 0
    ),
    'cdn.cotendo.username' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cotendo.password' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.cotendo.zones' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.cotendo.domain' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.cotendo.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.akamai.username' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.akamai.password' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.akamai.email_notification' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.akamai.action' => array(
        'type' => 'string',
        'default' => 'invalidate'
    ),
    'cdn.akamai.zone' => array(
        'type' => 'string',
        'default' => 'production'
    ),
    'cdn.akamai.domain' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.akamai.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.edgecast.account' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.edgecast.token' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.edgecast.domain' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.edgecast.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.att.account' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.att.token' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cdn.att.domain' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.att.ssl' => array(
        'type' => 'string',
        'default' => 'auto'
    ),
    'cdn.reject.admins' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.reject.logged_roles' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdn.reject.roles' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.reject.ua' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.reject.uri' => array(
        'type' => 'array',
        'default' => array()
    ),
    'cdn.reject.files' => array(
        'type' => 'array',
        'default' => array(
            '{uploads_dir}/wpcf7_captcha/*',
            '{uploads_dir}/imagerotator.swf',
            '{plugins_dir}/wp-fb-autoconnect/facebook-platform/channel.html'
        )
    ),
    'cdn.reject.ssl' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cdncache.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),


    'cloudflare.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'cloudflare.email' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cloudflare.key' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cloudflare.zone' => array(
        'type' => 'string',
        'default' => ''
    ),
    'cloudflare.ips.ip4' => array(
        'type' => 'array',
        'default' => array("204.93.240.0/24", "204.93.177.0/24", "199.27.128.0/21", "173.245.48.0/20", "103.22.200.0/22", "141.101.64.0/18", "108.162.192.0/18","190.93.240.1/20","188.114.96.0/20", "198.41.128.0/17")
    ),
    'cloudflare.ips.ip6' => array(
        'type' => 'array',
        'default' => array("2400:cb00::/32", "2606:4700::/32", "2803:f800::/32")
    ),

    'varnish.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'varnish.debug' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'varnish.servers' => array(
        'type' => 'array',
        'default' => array()
    ),

    'browsercache.enabled' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'browsercache.no404wp' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.no404wp.exceptions' => array(
        'type' => 'array',
        'default' => array(
            'robots\.txt',
            'sitemap(_index)?\.xml(\.gz)?',
            '[a-z0-9_\-]+-sitemap([0-9]+)?\.xml(\.gz)?'
        )
    ),
    'browsercache.cssjs.last_modified' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'browsercache.cssjs.compression' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'browsercache.cssjs.expires' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.cssjs.lifetime' => array(
        'type' => 'integer',
        'default' => 31536000
    ),
    'browsercache.cssjs.nocookies' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.cssjs.cache.control' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.cssjs.cache.policy' => array(
        'type' => 'string',
        'default' => 'cache_public_maxage'
    ),
    'browsercache.cssjs.etag' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.cssjs.w3tc' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.cssjs.replace' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.html.compression' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'browsercache.html.last_modified' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'browsercache.html.expires' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.html.lifetime' => array(
        'type' => 'integer',
        'default' => 3600
    ),
    'browsercache.html.cache.control' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.html.cache.policy' => array(
        'type' => 'string',
        'default' => 'cache_public_maxage'
    ),
    'browsercache.html.etag' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.html.w3tc' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.html.replace' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.other.last_modified' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'browsercache.other.compression' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'browsercache.other.expires' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.other.lifetime' => array(
        'type' => 'integer',
        'default' => 31536000
    ),
    'browsercache.other.nocookies' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.other.cache.control' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.other.cache.policy' => array(
        'type' => 'string',
        'default' => 'cache_public_maxage'
    ),
    'browsercache.other.etag' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.other.w3tc' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.other.replace' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'browsercache.timestamp' => array(
        'type' => 'string',
        'default' => ''
    ),

    'mobile.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'mobile.rgroups' => array(
        'type' => 'array',
        'default' => array(
            'high' => array(
                'theme' => '',
                'enabled' => false,
                'redirect' => '',
                'agents' => array(
                    'acer\ s100',
                    'android',
                    'archos5',
                    'bada',
                    'blackberry9500',
                    'blackberry9530',
                    'blackberry9550',
                    'blackberry\ 9800',
                    'cupcake',
                    'docomo\ ht\-03a',
                    'dream',
                    'froyo',
                    'googlebot-mobile',
                    'htc\ hero',
                    'htc\ magic',
                    'htc_dream',
                    'htc_magic',
                    'iemobile/7.0',
                    'incognito',
                    'ipad',
                    'iphone',
                    'ipod',
                    'kindle',
                    'lg\-gw620',
                    'liquid\ build',
                    'maemo',
                    'mot\-mb200',
                    'mot\-mb300',
                    'nexus\ one',
                    'opera\ mini',
                    's8000',
                    'samsung\-s8000',
                    'series60.*webkit',
                    'series60/5\.0',
                    'sonyericssone10',
                    'sonyericssonu20',
                    'sonyericssonx10',
                    't\-mobile\ mytouch\ 3g',
                    't\-mobile\ opal',
                    'tattoo',
                    'webmate',
                    'webos'
                )
            ),
            'low' => array(
                'theme' => '',
                'enabled' => false,
                'redirect' => '',
                'agents' => array(
                    '2\.0\ mmp',
                    '240x320',
                    'alcatel',
                    'amoi',
                    'asus',
                    'au\-mic',
                    'audiovox',
                    'avantgo',
                    'benq',
                    'bird',
                    'blackberry',
                    'blazer',
                    'cdm',
                    'cellphone',
                    'danger',
                    'ddipocket',
                    'docomo',
                    'dopod',
                    'elaine/3\.0',
                    'ericsson',
                    'eudoraweb',
                    'fly',
                    'haier',
                    'hiptop',
                    'hp\.ipaq',
                    'htc',
                    'huawei',
                    'i\-mobile',
                    'iemobile',
                    'j\-phone',
                    'kddi',
                    'konka',
                    'kwc',
                    'kyocera/wx310k',
                    'lenovo',
                    'lg',
                    'lg/u990',
                    'lge\ vx',
                    'midp',
                    'midp\-2\.0',
                    'mmef20',
                    'mmp',
                    'mobilephone',
                    'mot\-v',
                    'motorola',
                    'netfront',
                    'newgen',
                    'newt',
                    'nintendo\ ds',
                    'nintendo\ wii',
                    'nitro',
                    'nokia',
                    'novarra',
                    'o2',
                    'openweb',
                    'opera\ mobi',
                    'opera\.mobi',
                    'palm',
                    'panasonic',
                    'pantech',
                    'pdxgw',
                    'pg',
                    'philips',
                    'phone',
                    'playstation\ portable',
                    'portalmmm',
                    '\bppc\b',
                    'proxinet',
                    'psp',
                    'qtek',
                    'sagem',
                    'samsung',
                    'sanyo',
                    'sch',
                    'sec',
                    'sendo',
                    'sgh',
                    'sharp',
                    'sharp\-tq\-gx10',
                    'small',
                    'smartphone',
                    'softbank',
                    'sonyericsson',
                    'sph',
                    'symbian',
                    'symbian\ os',
                    'symbianos',
                    'toshiba',
                    'treo',
                    'ts21i\-10',
                    'up\.browser',
                    'up\.link',
                    'uts',
                    'vertu',
                    'vodafone',
                    'wap',
                    'willcome',
                    'windows\ ce',
                    'windows\.ce',
                    'winwap',
                    'xda',
                    'zte'
                )
            )
        )
    ),


    'referrer.enabled' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'referrer.rgroups' => array(
        'type' => 'array',
        'default' => array(
            'search_engines' => array(
                'theme' => '',
                'enabled' => false,
                'redirect' => '',
                'referrers' => array(
                    'google\.com',
                    'yahoo\.com',
                    'bing\.com',
                    'ask\.com',
                    'msn\.com'
                )
            )
        )
    ),


    'common.support' => array(
        'type' => 'string',
        'default' => ''
    ),
    'common.install' => array(
        'type' => 'integer',
        'default' => 0
    ),
    'common.tweeted' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'config.check' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'config.path' => array(
        'type' => 'string',
        'default' => ''
    ),
    'widget.latest.items' => array(
        'type' => 'integer',
        'default' => 3
    ),
    'widget.latest_news.items' => array(
        'type' => 'integer',
        'default' => 5
    ),
    'widget.pagespeed.enabled' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'widget.pagespeed.key' => array(
        'type' => 'string',
        'default' => ''
    ),

    'notes.wp_content_changed_perms' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.wp_content_perms' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.theme_changed' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'notes.wp_upgraded' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'notes.plugins_updated' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'notes.cdn_upload' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'notes.cdn_reupload' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'notes.need_empty_pgcache' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'notes.need_empty_minify' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'notes.need_empty_objectcache' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'notes.root_rules' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.rules' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.pgcache_rules_wpsc' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.support_us' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.no_curl' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.no_zlib' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.zlib_output_compression' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.no_permalink_rules' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.browsercache_rules_no404wp' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'notes.cloudflare_plugin' => array(
        'type' => 'boolean',
        'default' => true
    ),

    'timelimit.email_send' => array(
        'type' => 'integer',
        'default' => 180
    ),
    'timelimit.varnish_purge' => array(
        'type' => 'integer',
        'default' => 300
    ),
    'timelimit.cache_flush' => array(
        'type' => 'integer',
        'default' => 600
    ),
    'timelimit.cache_gc' => array(
        'type' => 'integer',
        'default' => 600
    ),
    'timelimit.cdn_upload' => array(
        'type' => 'integer',
        'default' => 600
    ),
    'timelimit.cdn_delete' => array(
        'type' => 'integer',
        'default' => 300
    ),
    'timelimit.cdn_purge' => array(
        'type' => 'integer',
        'default' => 300
    ),
    'timelimit.cdn_import' => array(
        'type' => 'integer',
        'default' => 600
    ),
    'timelimit.cdn_test' => array(
        'type' => 'integer',
        'default' => 300
    ),
    'timelimit.cdn_container_create' => array(
        'type' => 'integer',
        'default' => 300
    ),
    'timelimit.cloudflare_api_request' => array(
        'type' => 'integer',
        'default' => 180
    ),
    'timelimit.domain_rename' => array(
        'type' => 'integer',
        'default' => 120
    ),
    'timelimit.minify_recommendations' => array(
        'type' => 'integer',
        'default' => 600
    ),

    'minify.auto.filename_length' => array(
        'type' => 'integer',
        'default' => 150
    ),
    'minify.auto.disable_filename_length_test' => array(
        'type' => 'boolean',
        'default' => false,
    ),
    'common.instance_id' => array(
        'type' => 'integer',
        'default' => 0
    ),
    'common.force_master' => array(
    'type' => 'boolean',
    'default' => true,
    'master_only' => 'true'
    ),
    'newrelic.enabled' => array(
        'type' => 'boolean',
        'default' => false,
    ),
    'newrelic.api_key' => array(
        'type' => 'string',
        'default' => '',
        'master_only' => 'true'
    ),
    'newrelic.account_id' => array(
        'type' => 'string',
        'default' => '',
        'master_only' => 'true'
    ),
    'newrelic.application_id' => array(
        'type' => 'integer',
        'default' => 0,
    ),
    'newrelic.appname' => array(
        'type' => 'string',
        'default' => '',
    ),
    'newrelic.accept.logged_roles' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'newrelic.accept.roles' => array(
        'type' => 'array',
        'default' => array('contributor')
    ),
    'newrelic.use_php_function' => array (
        'type' => 'boolean',
        'default' => true,
    ),
    'notes.new_relic_page_load_notification' => array(
        'type' => 'boolean',
        'default' => true
    ),
    'newrelic.appname_prefix' => array (
        'type' => 'string',
        'default' => 'Child Site - '
    ),
    'newrelic.merge_with_network' => array (
        'type' => 'boolean',
        'default' => true
    ),
    'newrelic.cache_time' => array(
        'type' => 'integer',
        'default' => 5
    ),
    'newrelic.enable_xmit' => array(
        'type' => 'boolean',
        'default' => false
    ),
    'newrelic.use_network_wide_id' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'pgcache.late_init' => array (
        'type' => 'boolean',
        'default' => false
    ),
    'newrelic.include_rum' => array(
        'type' => 'boolean',
        'default' => true,
    )
);


/*
 * Descriptors of configuration keys
 * for admin config
 */
$keys_admin = array(
    'browsercache.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'cdn.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'cloudflare.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'common.install' => array(
        'type' => 'integer',
        'default' => 0,
        'master_only' => 'true'
    ),
    'common.visible_by_master_only' => array(
        'type' => 'boolean',
        'default' => true,
        'master_only' => 'true'
    ),
    'dbcache.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'minify.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'objectcache.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'pgcache.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'previewmode.enabled' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'varnish.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    ),
    'fragmentcache.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    )
    ,'newrelic.configuration_sealed' => array(
        'type' => 'boolean',
        'default' => false,
        'master_only' => 'true'
    )
    ,'notes.minify_error' => array(
        'type' => 'boolean',
        'default' => false
    )
    ,'minify.error.last' => array(
        'type' => 'string',
        'default' => ''
    ),
    'minify.error.notification' => array(
        'type' => 'string',
        'default' => ''
    ),
    'minify.error.notification.last' => array(
        'type' => 'integer',
        'default' => 0
    ),
    'minify.error.file' => array(
        'type' => 'string',
        'default' => ''
    ),
    'notes.remove_w3tc' => array(
        'type' => 'boolean',
        'default' => false
    )
);

$keys_admin['common.install']['default'] = time();



/*
 * Descriptors how sealed configuration keys affect overriding
 */
$sealing_keys_scope = array(
    array(
        'key' => 'browsercache.configuration_sealed',
        'prefix' => 'browsercache.'
    ),
    array(
        'key' => 'cdn.configuration_sealed',
        'prefix' => 'cdn.'
    ),
    array(
        'key' => 'cloudflare.configuration_sealed',
        'prefix' => 'cloudflare.'
    ),
    array(
        'key' => 'dbcache.configuration_sealed',
        'prefix' => 'dbcache.'
    ),
    array(
        'key' => 'minify.configuration_sealed',
        'prefix' => 'minify.'
    ),
    array(
        'key' => 'objectcache.configuration_sealed',
        'prefix' => 'objectcache.'
    ),
    array(
        'key' => 'fragmentcache.configuration_sealed',
        'prefix' => 'fragmentcache.'
    ),
    array(
        'key' => 'pgcache.configuration_sealed',
        'prefix' => 'pgcache.'
    ),
    array(
        'key' => 'varnish.configuration_sealed',
        'prefix' => 'varnish.'
    )
);

