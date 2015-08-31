<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<div id="about">
    <p><?php _e('User experience is an important aspect of every web site and all web sites can benefit from effective caching and file size reduction. We have applied web site optimization methods typically used with high traffic sites and simplified their implementation. Coupling these methods either <a href="http://memcached.org/" target="_blank">memcached</a> and/or opcode caching and the <acronym title="Content Delivery Network">CDN</acronym> of your choosing to provide the following features and benefits:') ?></p>

    <ul>
		<li><?php _e('Improved Google search engine ranking', 'w3-total-cache'); ?></li>
		<li><?php _e('Increased visitor time on site', 'w3-total-cache'); ?></li>
		<li><?php _e('Optimized progressive render (pages start rendering immediately)', 'w3-total-cache'); ?></li>
		<li><?php _e('Reduced <acronym title="Hypertext Transfer Protocol">HTTP</acronym> Transactions, <acronym title="Domain Name System">DNS</acronym> lookups and reduced document load time', 'w3-total-cache'); ?></li>
		<li><?php _e('Bandwidth savings via Minify and <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression of <acronym title="Hypertext Markup Language">HTML</acronym>, <acronym title="Cascading Style Sheet">CSS</acronym>, JavaScript and feeds', 'w3-total-cache'); ?></li>
		<li><?php _e('Increased web server concurrency and increased scale (easily sustain high traffic spikes)', 'w3-total-cache'); ?></li>
		<li><?php _e('Transparent content delivery network (<acronym title="Content Delivery Network">CDN</acronym>) integration with Media Library, theme files and WordPress core', 'w3-total-cache'); ?></li>
		<li><?php _e('Caching of pages / posts in memory or on disk or on CDN (mirror only)', 'w3-total-cache'); ?></li>
		<li><?php _e('Caching of (minified) <acronym title="Cascading Style Sheet">CSS</acronym> and JavaScript in memory, on disk or on <acronym title="Content Delivery Network">CDN</acronym>', 'w3-total-cache'); ?></li>
		<li><?php _e('Caching of database objects in memory or on disk', 'w3-total-cache'); ?></li>
		<li><?php _e('Caching of objects in memory or on disk', 'w3-total-cache'); ?></li>
		<li><?php _e('Caching of feeds (site, categories, tags, comments, search results) in memory or on disk', 'w3-total-cache'); ?></li>
		<li><?php _e('Caching of search results pages (i.e. <acronym title="Uniform Resource Identifier">URI</acronym>s with query string variables) in memory or on disk', 'w3-total-cache'); ?></li>
		<li><?php _e('Minification of posts / pages and feeds', 'w3-total-cache'); ?></li>
		<li><?php _e('Minification (concatenation and white space removal) of inline, external or 3rd party JavaScript / <acronym title="Cascading Style Sheet">CSS</acronym> with automated updates', 'w3-total-cache'); ?></li>
		<li><?php _e('Complete header management including <a href="http://en.wikipedia.org/wiki/HTTP_ETag">Etags</a>', 'w3-total-cache'); ?></li>
		<li><?php _e('JavaScript embedding group and location management', 'w3-total-cache'); ?></li>
		<li><?php _e('Import post attachments directly into the Media Library (and <acronym title="Content Delivery Network">CDN</acronym>)', 'w3-total-cache'); ?></li>
    </ul>

    <p><?php _e('Your users have less data to download, you can now serve more visitors at once without upgrading your hardware and you don\'t have to change how you do anything; just set it and forget it.', 'w3-total-cache'); ?></p>

    <h4><?php _e('Who do I thank for all of this?', 'w3-total-cache'); ?></h4>

    <p><?php _e('It\'s quite difficult to recall all of the innovators that have shared their thoughts, code and experiences in the blogosphere over the years, but here are some names to get you started:', 'w3-total-cache'); ?></p>

    <ul>
        <li><a href="http://stevesouders.com/" target="_blank">Steve Souders</a></li>
        <li><a href="http://mrclay.org/" target="_blank">Steve Clay</a></li>
        <li><a href="http://wonko.com/" target="_blank">Ryan Grove</a></li>
        <li><a href="http://www.nczonline.net/blog/2009/06/23/loading-javascript-without-blocking/" target="_blank">Nicholas Zakas</a> </li>
        <li><a href="http://rtdean.livejournal.com/" target="_blank">Ryan Dean</a></li>
        <li><a href="http://gravitonic.com/" target="_blank">Andrei Zmievski</a></li>
        <li>George Schlossnagle</li>
        <li>Daniel Cowgill</li>
        <li><a href="http://toys.lerdorf.com/" target="_blank">Rasmus Lerdorf</a></li>
        <li><a href="http://notmysock.org/" target="_blank">Gopal Vijayaraghavan</a></li>
        <li><a href="http://eaccelerator.net/" target="_blank">Bart Vanbraban</a></li>
        <li><a href="http://xcache.lighttpd.net/" target="_blank">mOo</a></li>
    </ul>

    <p><?php _e('Please reach out to all of these people and support their projects if you\'re so inclined.', 'w3-total-cache'); ?></p>
</div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
