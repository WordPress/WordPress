This is a community-contributed list of [referrer spammers](http://en.wikipedia.org/wiki/Referer_spam) maintained by [Matomo](https://matomo.org/), the leading open source web analytics platform.

## Usage

The list is stored in this repository in `spammers.txt`. This text file contains one host per line.

You can [download this file manually](https://github.com/matomo-org/referrer-spam-list/blob/master/spammers.txt), download the [whole folder as zip](https://github.com/matomo-org/referrer-spam-list/archive/master.zip) or clone the repository using git:

```
git clone https://github.com/matomo-org/referrer-spam-list.git
```

### PHP

If you are using PHP, you can also install the list through Composer:

```
composer require matomo/referrer-spam-blacklist
```

Parsing the file should be pretty easy using your favorite language. Beware that the file can contain empty lines.

Here is an example using PHP:

```php
$list = file('spammers.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
```

### Nginx

Nginx's `server` block can be configured to check the referer and return an error:

```nginx
if ($http_referer ~ '0n-line.tv') {return 403;}
if ($http_referer ~ '100dollars-seo.com') {return 403;}
...
```
When combined, list exceeds the max length for a single regex expression, so hosts must be broken up as shown above.

Here is a bash script to create an nginx conf file:
```bash
sort spammers.txt | uniq | sed 's/\./\\\\./g' | while read host; 
do 
    echo "if (\$http_referer ~ '$host') {return 403;}" >> /etc/nginx/referer_spam.conf
done;
```

you would then `include /etc/nginx/referer_spam.conf;` inside your `server` block

Now as a daily cron job so the list stays up to date:

```bash
0 0 * * * cd /etc/nginx/referrer-spam-blacklist/ && git pull > /dev/null && echo "" > /etc/nginx/referer_spam.conf && sort spammers.txt | uniq | sed 's/\./\\\\\\\\./g' | while read host; do echo "if (\$http_referer ~ '$host') {return 403;}" >> /etc/nginx/referer_spam.conf; done; service nginx reload > /dev/null
```


### In Matomo (formerly Piwik)

This list is included in each [Matomo](https://matomo.org) release so that referrer spam is filtered automatically. Matomo will also automatically update this list to its latest version every week.

## Contributing

To add a new referrer spammer to the list, [click here to edit the spammers.txt file](https://github.com/matomo-org/referrer-spam-list/edit/master/spammers.txt) and select `Create a new branch for this commit and start a pull request. `. In your pull request please explain where the referrer domain appeared and why you think it is a spammer. **Please open one pull request per new domain**.

If you open a pull request, it is appreciated if you keep one hostname per line, keep the list ordered alphabetically, and use [Linux line endings](http://en.wikipedia.org/wiki/Newline).

Please [search](https://github.com/matomo-org/referrer-spam-list/pulls) if somebody already reported the host before opening a new one.

### Subdomains

Matomo does sub-string matching on domain names from this list, so adding `semalt.com` is enough to block all subdomain referrers too, such as `semalt.semalt.com`.

However, there are cases where you'd only want to add a subdomain but not the root domain. For example, add `referrerspammer.tumblr.com` but not `tumblr.com`, otherwise all `*.tumblr.com` sites would be affected.

### Sorting

To keep the list sorted the same way across forks it is recommended to let the computer do the sorting. The list follows the merge sort algorithm as implemented in [sort](https://en.wikipedia.org/wiki/Sort_(Unix)). You can use sort to both sort the list and filter out doubles:

```
sort -uf -o spammers.txt spammers.txt
```

### Community Projects
[Apache .htaccess referrer spam blacklist](https://github.com/kambrium/apache-referrer-spam-blacklist) - A script for Apache users that generates a list of RewriteConds based on `spammers.txt`.

## Disclaimer

This list of Referrer spammers is contributed by the community and is provided as is. Use at your own discretion: it may be incomplete (although we aim to keep it up to date) and it may contain outdated entries (let us know if a hostname was added but is not actually a spammer).

## License

Public Domain (no copyright).
