RewriteEngine On
RewriteBase BASE/

#uploaded files
RewriteRule ^(.*/)?files/$ index.php [L]
RewriteCond %{REQUEST_URI} !.*wp-content/plugins.*
RewriteRule ^(.*/)?files/(.*) wp-content/blogs.php?file=$2 [L]

# add a trailing slash to /wp-admin
RewriteCond %{REQUEST_URI} ^.*/wp-admin$
RewriteRule ^(.+)$ $1/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule . - [L]
RewriteRule  ^([_0-9a-zA-Z-]+/)?(wp-.*) $2 [L]
RewriteRule  ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]

<IfModule mod_security.c>
<Files async-upload.php>
SecFilterEngine Off
SecFilterScanPOST Off
</Files>
</IfModule>

