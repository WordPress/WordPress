<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<div id="install">
    <ol>
        <li>
        	<?php _e('Set the permissions of wp-content/ back to 755, e.g.:', 'w3-total-cache'); ?>
         	<pre class="console"># chmod 755 /var/www/vhosts/domain.com/httpdocs/wp-content/</pre>
        </li>
        <li><?php _e('On the "<a href="admin.php?page=w3tc_general">General</a>" tab and select your caching methods for page, database and minify. In most cases, "disk enhanced" mode for page cache, "disk" mode for minify and "disk" mode for database caching are "good" settings.', 'w3-total-cache'); ?></li>
        <li><?php _e('1. The "Compatibility Mode" option found in the advanced section of the "<a href="admin.php?page=w3tc_pgcache">Page Cache Settings</a>" tab will enable functionality that optimizes the interoperablity of caching with WordPress, is disabled by default, but highly recommended. Years of testing in hundreds of thousands of installations have helped us learn how to make caching behave well with WordPress. The tradeoff is that disk enhanced page cache performance under load tests will be decreased by ~20% at scale.', 'w3-total-cache'); ?></li>
        <li><?php _e('<em>Recommended:</em> On the "<a href="admin.php?page=w3tc_minify">Minify</a>" tab all of the recommended settings are preset. Use the help button to simplify discovery of your <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> files and groups. Pay close attention to the method and location of your <acronym title="JavaScript">JS</acronym> group embeddings. See the plugin\'s <a href="admin.php?page=w3tc_faq">FAQ</a> for more information on usage.', 'w3-total-cache'); ?></li>
        <li><?php _e('<em>Recommended:</em> On the "<a href="admin.php?page=w3tc_browsercache">Browser Cache</a>" tab, <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression is enabled by default. Make sure to enable other options to suit your goals.', 'w3-total-cache'); ?></li>
        <li><?php _e('<em>Recommended:</em> If you already have a content delivery network (<acronym title="Content Delivery Network">CDN</acronym>) provider, proceed to the "<a href="admin.php?page=w3tc_cdn">Content Delivery Network</a>" tab and populate the fields and set your preferences. If you do not use the Media Library, you will need to import your images etc into the default locations. Use the Media Library Import Tool on the "Content Delivery Network" tab to perform this task. If you do not have a <acronym title="Content Delivery Network">CDN</acronym> provider, you can still improve your site\'s performance using the "Self-hosted" method. On your own server, create a subdomain and matching <acronym title="Domain Name System">DNS</acronym> Zone record; e.g. static.domain.com and configure <acronym title="File Transfer Protocol">FTP</acronym> options on the "Content Delivery Network" tab accordingly. Be sure to <acronym title="File Transfer Protocol">FTP</acronym> upload the appropriate files, using the available upload buttons.', 'w3-total-cache'); ?></li>
        <li><?php _e('<em>Optional:</em> On the "<a href="admin.php?page=w3tc_dbcache">Database Cache</a>" tab the recommended settings are preset. If using a shared hosting account use the "disk" method with caution; in either of these cases the response time of the disk may not be fast enough, so this option is disabled by default.', 'w3-total-cache'); ?></li>
        <li><?php _e('<em>Optional:</em> On the "<a href="admin.php?page=w3tc_objectcache">Object Cache</a>" tab the recommended settings are preset. If using a shared hosting account use the "disk" method with caution, the response time of the disk may not be fast enough, so this option is disabled by default. Test this option with and without database cache to ensure that it provides a performance increase.', 'w3-total-cache'); ?></li>
        <li><?php _e('<em>Optional:</em> On the "<a href="admin.php?page=w3tc_mobile">User Agent Groups</a>" tab, specify any user agents, like mobile phones if a mobile theme is used.', 'w3-total-cache'); ?></li>
    </ol>

    <p>
    	<?php _e('Check out the <acronym title="Frequently Asked Questions">FAQ</acronym> for more details on <a href="admin.php?page=w3tc_faq">usage</a>', 'w3-total-cache'); ?>.
    </p>

	<hr />
    <?php if (count($rewrite_rules_descriptors)): ?>
	<h3><?php _e('Rewrite rules', 'w3-total-cache'); ?></h3>
    <?php foreach ($rewrite_rules_descriptors as $descriptor): ?>
     <p><strong><?php echo htmlspecialchars($descriptor['filename']); ?>:</strong></p>
    <pre class="code"><?php echo htmlspecialchars($descriptor['content']); ?></pre>
    <?php endforeach; ?>
    <hr />
    <?php endif; ?>
    <?php if (count($other_areas)): ?>
        <h3><?php _e('Other', 'w3-total-cache'); ?></h3>
        <?php foreach ($other_areas as $area => $descriptors): ?>
            <?php foreach ($descriptors as $descriptor): ?>
            <p><strong><?php echo htmlspecialchars($descriptor['title']); ?>:</strong></p>
            <pre class="code"><?php echo htmlspecialchars($descriptor['content']); ?></pre>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <hr />
    <?php endif; ?>
    <h3><?php _e('Software Installation for Dedicated / Virtual Dedicated / Multiple Servers (Optional)', 'w3-total-cache'); ?></h3>

	<p><?php _e('<strong>Server Preparation:</strong><br /><em>Time required: ~1 minute</em>', 'w3-total-cache'); ?></p>

    <ol>
        <li>
        	<?php _e('<a href="http://www.google.com/search?q=installing%20yum&amp;output=search&amp;tbs=qdr:y&amp;tbo=1" target="_blank">Install yum</a> if you don\'t already have it. Then, if you like, you can update all of your installed software, but do so only if you have the experience and time to double check configurations afterwards:', 'w3-total-cache'); ?>
        	<pre class="console"># yum update</pre>
        </li>
        <li>
        	<?php _e('Install <acronym title="PHP Extension Community Library">PECL</acronym>:', 'w3-total-cache'); ?>
        	<pre class="console"># yum install php-pear</pre>
        </li>
        <li>
        	<?php _e('Install the <acronym title="Hypertext Preprocessor">PHP</acronym> Development package:', 'w3-total-cache'); ?>
	        <pre class="console"># yum install php-devel</pre>
		</li>
        <li>
        	<?php _e('Install apxs with the following command:', 'w3-total-cache'); ?>
        	<pre class="console"># yum install httpd-devel</pre>
        </li>
        <li>
        	<?php _e('Make sure GCC is up-to-date:', 'w3-total-cache'); ?>
			<pre class="console"># yum install gcc make</pre>
		</li>
        <li>
        	<?php _e('Make sure ZLIB is fully installed:', 'w3-total-cache'); ?>
			<pre class="console"># yum install zlib-devel</pre>
		</li>
        <li>
        	<?php _e('Make sure PCRE is fully installed:', 'w3-total-cache'); ?>
			<pre class="console"># yum install pcre-devel</pre>
		</li>
    </ol>

	<hr />

    <p id="memcached"><strong><?php _e('Memcached (Daemon) Installation:</strong><br /><em>Time required: 2 minutes</em>', 'w3-total-cache'); ?></p>

    <ol>
        <li>
        	<?php _e('Try to install with yum:', 'w3-total-cache'); ?>
        	<pre class="console"># yum install libevent libevent-devel</pre>

	        <?php _e('If this succeeds skip to #5. If this fails, then let\'s compile. Download and extract the <a href="http://www.monkey.org/~provos/libevent/" target="_blank">latest stable version</a>:', 'w3-total-cache'); ?>
        	<pre class="console"># cd /usr/local/src &amp;&amp; wget <a href="http://www.monkey.org/~provos/libevent-2.0.12-stable.tar.gz" target="_blank">http://monkey.org/~provos/libevent-2.0.14-stable.tar.gz</a> &amp;&amp; tar -xzf libevent-2.0.12-stable.tar.gz &amp;&amp; cd libevent-2.0.12-stable</pre>
        </li>
        <li>
        	<?php _e('Let\'s compile:', 'w3-total-cache'); ?>
        	<pre class="console"># ./configure &amp;&amp; make &amp;&amp; make install</pre>
        </li>
        <li>
        	<?php _e('In the output you should see:', 'w3-total-cache'); ?>
	        <pre class="console"><?php echo sprintf(__('Libraries have been installed in: %s', 'w3-total-cache'), '/usr/local/lib'); ?></pre>
	        <?php _e('If so you can:', 'w3-total-cache'); ?>
        	<pre class="console"># echo "/usr/local/lib/" &gt; /etc/ld.so.conf.d/libevent.conf</pre>
        </li>
        <li>
        	<?php _e('Configure your server for the new install:', 'w3-total-cache'); ?>
        	<pre class="console"># ldconfig -v</pre>
        </li>
        <li>
        	<?php _e('Now find the <a href="http://memcached.org/" target="_blank">latest stable memcached</a>, download and extract:', 'w3-total-cache'); ?>
        	<pre class="console"># cd /usr/local/src &amp;&amp; wget <a href="http://memcached.googlecode.com/files/memcached-1.4.6.tar.gz" target="_blank">http://memcached.googlecode.com/files/memcached-1.4.6.tar.gz</a> &amp;&amp; tar -xzf memcached-1.4.6.tar.gz &amp;&amp; cd memcached-1.4.6</pre>
        </li>
        <li>
        	<?php _e('Let\'s compile:', 'w3-total-cache'); ?>
        	<pre class="console"># ./configure &amp;&amp; make &amp;&amp; make install</pre>
        </li>
        <li>
        	<?php _e('Make sure memcached is starts automatically on server boot:', 'w3-total-cache'); ?>
        	<pre class="console"># touch /etc/init.d/memcached
# echo '#!/bin/sh -e' &gt;&gt; /etc/init.d/memcached
# echo '/usr/local/bin/memcached -d -m 128 -p 11211 -u nobody -l localhost' &gt;&gt; /etc/init.d/memcached
# chmod u+x /etc/init.d/memcached
# echo '/etc/init.d/memcached' &gt;&gt; /etc/rc.local</pre>
		</li>
        <li>
        	<?php _e('And finally, let\'s start memcached:', 'w3-total-cache'); ?>
        	<pre class="console"># /etc/init.d/memcached</pre>
        </li>
    </ol>

	<hr />

	<p id="memcache"><?php _e('<strong><acronym title="PHP Extension Community Library">PECL</acronym> Memcache Module Installation:</strong><br /><em>Time required: 1 minute</em>', 'w3-total-cache'); ?></p>

    <ol>
        <li>
        	<?php _e('Either use <acronym title="PHP Extension Community Library">PECL</acronym> (and skip to #4 if successful):', 'w3-total-cache'); ?>
        	<pre class="console"># pecl install memcache</pre>
        </li>
        <li>
        	<?php _e('Or via compilation. Download the <a href="http://pecl.php.net/package/memcache" target="_blank">latest stable version</a> and extract:', 'w3-total-cache'); ?>
        	<pre class="console"># cd /usr/local/src/ &amp;&amp; wget <a href="http://pecl.php.net/get/memcache-2.2.6.tgz" target="_blank">http://pecl.php.net/get/memcache-2.2.6.tgz</a> &amp;&amp; tar -xzf memcache-2.2.6.tgz &amp;&amp; cd memcache-2.2.6</pre>
        </li>
        <li>
        	<?php _e('Now we start to compile:', 'w3-total-cache'); ?>
        	<pre class="console"># phpize &amp;&amp; ./configure &amp;&amp; make &amp;&amp; make install</pre>
        </li>
        <li>
        	<?php _e('You can also use the memcache.ini file we prepared for you:', 'w3-total-cache'); ?>
        	<pre class="console"># cp /var/www/vhosts/domain.com/httpdocs/wp-content/plugins/w3-total-cache/ini/memcache.ini /etc/php.d/</pre>
        </li>
        <li>
        	<?php _e('Finally restart apache:', 'w3-total-cache'); ?>
        	<pre class="console"># /etc/init.d/httpd restart</pre>
        </li>
        <li>
        	<?php _e('You\'re done! Memcache should now be available. If the following command retuns anything, you\'re all set:', 'w3-total-cache'); ?>
			<pre class="console"># php -r 'phpinfo();' | grep 'memcache'</pre>
		</li>
    </ol>

	<hr />

	<p id="APC"><?php _e('<strong><acronym title="PHP Extension Community Library">PECL</acronym> Alternative PHP Cache (<acronym title="Alternative PHP Cache">APC</acronym>) Installation (Recommended):</strong><br /><em>Time required: 1 minute</em>', 'w3-total-cache'); ?></p>

    <ol>
        <li>
        	<?php _e('Install <acronym title="Alternative PHP Cache">APC</acronym> using the <acronym title="PHP Extension Community Library">PECL</acronym> command (and skip to #5 if successful):', 'w3-total-cache'); ?>
        	<pre class="console"># pecl install apc</pre>
        </li>
        <li>
        	<?php _e('Or via compilation. Download the <a href="http://pecl.php.net/package/APC" target="_blank">latest stable version</a> and extract:', 'w3-total-cache'); ?>
			<pre class="console"># cd /usr/local/src &amp;&amp; wget <a href="http://pecl.php.net/get/APC-3.1.9.tgz" target="_blank">http://pecl.php.net/get/APC-3.1.9.tgz</a> &amp;&amp; tar -xzf APC-3.1.9.tgz &amp;&amp; cd APC-3.1.9</pre>
		</li>
		<li>
			<?php _e('Note the paths returned for the following commands:', 'w3-total-cache'); ?>
			<pre class="console"># whereis php-config
# whereis apxs</pre>
		</li>
		<li>
			<?php _e('Use the output from #2 to modify the --with-apxs and --with-php-config flags in the following compile command:', 'w3-total-cache'); ?>
			<pre class="console"># phpize &amp;&amp; ./configure --enable-apc --enable-apc-mmap --with-apxs=/usr/sbin/apxs --with-php-config=/usr/bin/php-config &amp;&amp; make &amp;&amp; make install</pre>
			The result should be similar to:
			<pre class="console"><?php echo sprintf(__('Installing shared extensions: %s', 'w3-total-cache'), '/usr/lib/php/modules/'); ?></pre>
		</li>
        <li>
        	<?php _e('You can also use the apc.ini file we prepared for you:', 'w3-total-cache'); ?>
			<pre class="console"># cp /var/www/vhosts/domain.com/httpdocs/wp-content/plugins/w3-total-cache/ini/apc.ini /etc/php.d/</pre>
		</li>
		<li>
			<?php _e('Restart apache when ready:', 'w3-total-cache'); ?>
			<pre class="console"># /etc/init.d/httpd restart</pre>
		</li>
		<li>
			<?php _e('You\'re done! <acronym title="Alternative PHP Cache">APC</acronym> should now be available. If the following command retuns anything, you\'re all set:', 'w3-total-cache'); ?>
			<pre class="console"># php -r 'phpinfo();' | grep 'apc'</pre>
		</li>
    </ol>

	<hr />

	<p id="XCache"><?php _e('<strong>XCache Installation:</strong><br /><em>Time required: 1 minute</em>', 'w3-total-cache'); ?></p>

    <ol>
        <li>
        	<?php _e('Download the <a href="http://xcache.lighttpd.net/wiki/ReleaseArchive" target="_blank">latest compatible version</a> and extract:', 'w3-total-cache'); ?>
			<pre class="console"># cd /usr/local/src &amp;&amp; wget <a href="http://xcache.lighttpd.net/pub/Releases/1.3.2/xcache-1.3.2.tar.gz" target="_blank">http://xcache.lighttpd.net/pub/Releases/1.3.2/xcache-1.3.2.tar.gz</a> &amp;&amp; tar -xzf xcache-1.3.2.tar.gz &amp;&amp; cd xcache-1.3.2</pre>
		</li>
		<li>
			<?php _e('Note the path returned for the following command:', 'w3-total-cache'); ?>
			<pre class="console"># whereis php-config</pre>
		</li>
		<li>
			<?php _e('Use the output from #2 to modify the --with-php-config flag in the following compile command:', 'w3-total-cache'); ?>
			<pre class="console"># phpize &amp;&amp; ./configure --with-php-config=/usr/bin/php-config --enable-xcache --enable-xcache-optimizer --enable-xcache-coverager &amp;&amp; make &amp;&amp; make install</pre>
			<?php _e('The result should be similar to:', 'w3-total-cache'); ?>
			<pre class="console">Installing shared extensions: /usr/lib/php/modules/</pre>
		</li>
        <li>
        	<?php _e('You can also use the eaccelerator.ini file we prepared for you:', 'w3-total-cache'); ?>
			<pre class="console"># cp /var/www/vhosts/domain.com/httpdocs/wp-content/plugins/w3-total-cache/ini/xcache.ini /etc/php.d/</pre>
		</li>
		<li>
			<?php _e('Restart apache when ready:', 'w3-total-cache'); ?>
			<pre class="console"># /etc/init.d/httpd restart</pre>
		</li>
		<li>
			<?php _e('You\'re done! XCache should now be available. If the following command retuns anything, you\'re all set:', 'w3-total-cache'); ?>
			<pre class="console"># php -r 'phpinfo();' | grep 'xcache'</pre>
		</li>
    </ol>

	<hr />

	<p id="eAccelerator"><?php _e('<strong>eAccelerator Installation:</strong><br /><em>Time required: 1 minute</em>', 'w3-total-cache'); ?></p>

    <ol>
        <li>
        	<?php _e('If using <acronym title="Hypertext Preprocessor">PHP</acronym> v5+, download the <a href="http://eaccelerator.net/" target="_blank">lastest compatible version</a> and extract. Remember v0.9.5.3 is the last version that supports user objects, later versions only support opcode caching.', 'w3-total-cache'); ?>
			<pre class="console"># cd /usr/local/src &amp;&amp; wget <a href="http://autosetup1.googlecode.com/files/eaccelerator-0.9.5.3.tar.bz2" target="_blank">http://autosetup1.googlecode.com/files/eaccelerator-0.9.5.3.tar.bz2</a> &amp;&amp; tar -xjf eaccelerator-0.9.5.3.tar.bz2 &amp;&amp; cd eaccelerator-0.9.5.3</pre>
		</li>
		<li>
			<?php _e('Note the path returned for the following command:', 'w3-total-cache'); ?>
			<pre class="console"># whereis php-config</pre>
		</li>
		<li>
			<?php _e('Use the output from #2 to modify the --with-php-config flag in the following compile command:', 'w3-total-cache'); ?>
			<pre class="console"># phpize &amp;&amp; ./configure --with-eaccelerator-shared-memory --with-php-config=/usr/bin/php-config &amp;&amp; make &amp;&amp; make install</pre>
			<?php _e('The result should be similar to:', 'w3-total-cache'); ?>
			<pre class="console">Installing shared extensions: /usr/lib/php/modules/</pre>
		</li>
        <li>
        	<?php _e('You can also use the eaccelerator.ini file we prepared for you:', 'w3-total-cache'); ?>
			<pre class="console"># cp /var/www/vhosts/domain.com/httpdocs/wp-content/plugins/w3-total-cache/ini/eaccelerator.ini /etc/php.d/</pre>
		</li>
		<li>
			<?php _e('Restart apache when ready:', 'w3-total-cache'); ?>
			<pre class="console"># /etc/init.d/httpd restart</pre>
		</li>
		<li>
			<?php _e('You\'re done! eAccelerator should now be available. If the following command retuns anything, you\'re all set:', 'w3-total-cache'); ?>
			<pre class="console"># php -r 'phpinfo();' | grep 'eaccelerator'</pre>
		</li>
    </ol>
    <hr />

    <p id="newrelic"><strong>New Relic Installation:</strong><br /><em>Time required: Mileage varies.</em></p>
    <p>
        Find requirements and installation instructions on New Relic website: <a href="https://newrelic.com/docs/php/new-relic-for-php" target="_blank">Installing New Relic for PHP</a>.
        To see if this server supports New Relic click "compatibility check" on the <a href="<?php echo admin_url('admin.php?page=w3tc_dashboard')?>">Dashboard tab</a>. Other key resources can be found below:
    </p>
<ul>
  <li><a href="https://newrelic.com/docs/php/quick-installation-instructions-advanced-users" target="_blank">Quick Installation Instructions (Advanced Users)</a></li>
  <li><a href="https://newrelic.com/docs/php/the-newrelic-install-script" target="_blank">The newrelic-install Script</a></li>
  <li><a href="https://newrelic.com/docs/php/php-agent-installation-redhat-and-centos" target="_blank">Installing with yum or rpm (RedHat, CentOS)</a></li>
  <li><a href="https://newrelic.com/docs/php/php-agent-installation-ubuntu-and-debian" target="_blank">Installing with dpkg or apt (Ubuntu, Debian)</a></li>
  <li><a href="https://newrelic.com/docs/php/php-agent-installation-tar-files" target="_blank">Installing from tar Files (other Linux variants, OpenSolaris, FreeBSD)</a></li>
  <li><a href="https://newrelic.com/docs/php/newrelic-daemon-startup-modes" target="_blank">New Relic Daemon Startup Modes</a></li>
</ul>
    <hr />

    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('Note(s):', 'w3-total-cache')); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <ul>
                        <li><?php _e('The provided instructions are for 32-bit CentOS, however we can provide others based on <a href="mailto:wordpressexperts@w3-edge.com">your requests</a>.', 'w3-total-cache'); ?></li>
						<li><?php _e('Best compatibility with <a href="http://www.iis.net/" target="_blank">IIS</a> is realized via <a href="http://www.microsoft.com/web/webmatrix/" target="_blank">WebMatrix</a>, which also includes the supported <a href="http://www.iis.net/download/wincacheforphp" target="_blank">WinCache</a> opcode cache.', 'w3-total-cache'); ?></li>
                        <li><?php _e('In the case where Apache is not used, the .htaccess file located in the root directory of the WordPress installation, wp-content/w3tc/pgcache/.htaccess and wp-content/w3tc/min/.htaccess contain directives that must be manually created for your web server software.', 'w3-total-cache'); ?></li>
                        <li><?php _e('Restarting the web server will empty the opcode cache, which means it will have to be rebuilt over time and your site\'s performance will suffer during this period. Still, an opcode cache should be installed in any case to maximize WordPress performance.', 'w3-total-cache'); ?></li>
                        <li><?php _e('Consider using memcached for objects that must persist across web server restarts or that you wish to share amongst your pool of servers (or cluster), e.g.: database objects or page cache.', 'w3-total-cache'); ?></li>
							<li><?php _e('Some yum or mirrors may not have the necessary packages, in such cases you may have to do a manual installation.', 'w3-total-cache'); ?></li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>
    </div>
</div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>