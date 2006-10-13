<?php
/****************************************************
SIMPLEPIE
A PHP-Based RSS and Atom Feed Framework
Takes the hard work out of managing a complete RSS/Atom solution.

Version: "Lemon Meringue"
Updated: 13 October 2006
Copyright: 2004-2006 Ryan Parman, Geoffrey Sneddon
http://simplepie.org

*****************************************************
LICENSE:

GNU Lesser General Public License 2.1 (LGPL)
http://creativecommons.org/licenses/LGPL/2.1/

*****************************************************
Please submit all bug reports and feature requests to the SimplePie forums.
http://simplepie.org/support/

****************************************************/

class SimplePie
{
	// SimplePie Info
	var $name = 'SimplePie';
	var $version = 'Lemon Meringue';
	var $build = '20061013';
	var $url = 'http://simplepie.org/';
	var $useragent;
	var $linkback;
	
	// Other objects, instances created here so we can set options on them
	var $sanitize;
	
	// Options
	var $rss_url;
	var $file;
	var $timeout = 10;
	var $xml_dump = false;
	var $enable_cache = true;
	var $max_minutes = 60;
	var $cache_location = './cache';
	var $order_by_date = true;
	var $input_encoding = false;
	var $cache_class = 'SimplePie_Cache';
	var $locator_class = 'SimplePie_Locator';
	var $parser_class = 'SimplePie_Parser';
	
	// Misc. variables
	var $data;
	var $error;
	
	function SimplePie($feed_url = null, $cache_location = null, $cache_max_minutes = null)
	{
		// Couple of variables built up from other variables
		$this->useragent = $this->name . '/' . $this->version . ' (Feed Parser; ' . $this->url . '; Allow like Gecko) Build/' . $this->build;
		$this->linkback = '<a href="' . $this->url . '" title="' . $this->name . ' ' . $this->version . '">' . $this->name . '</a>';
		
		// Other objects, instances created here so we can set options on them
		$this->sanitize = new SimplePie_Sanitize;
		
		// Set options if they're passed to the constructor
		if (!is_null($feed_url))
		{
			$this->feed_url($feed_url);
		}

		if (!is_null($cache_location))
		{
			$this->cache_location($cache_location);
		}

		if (!is_null($cache_max_minutes))
		{
			$this->cache_max_minutes($cache_max_minutes);
		}

		// If we've passed an xmldump variable in the URL, snap into XMLdump mode
		if (isset($_GET['xmldump']))
		{
			$this->enable_xmldump(true);
		}
		
		// Only init the script if we're passed a feed URL
		if (!is_null($feed_url))
		{
			return $this->init();
		}
	}
	
	function feed_url($url)
	{
		$this->rss_url = SimplePie_Misc::fix_protocol($url, 1);
	}
	
	function set_file(&$file)
	{
		if (is_a($file, 'SimplePie_File'))
		{
			$this->rss_url = $file->url;
			$this->file =& $file;
		}
	}
	
	function set_timeout($timeout = 10)
	{
		$this->timeout = (int) $timeout;
	}
	
	function set_raw_data($data)
	{
		$this->raw_data = trim((string) $data);
	}
	
	function enable_xmldump($enable = false)
	{
		$this->xml_dump = (bool) $enable;
	}
	
	function enable_caching($enable = true)
	{
		$this->enable_cache = (bool) $enable;
	}
	
	function cache_max_minutes($minutes = 60)
	{
		$this->max_minutes = (float) $minutes;
	}
	
	function cache_location($location = './cache')
	{
		$this->cache_location = (string) $location;
	}
	
	function order_by_date($enable = true)
	{
		$this->order_by_date = (bool) $enable;
	}
	
	function input_encoding($encoding = false)
	{
		if ($encoding)
		{
			$this->input_encoding = (string) $encoding;
		}
		else
		{
			$this->input_encoding = false;
		}
	}
	
	function set_cache_class($class = 'SimplePie_Cache')
	{
		if (SimplePie_Misc::is_a_class($class, 'SimplePie_Cache'))
		{
			$this->cache_class = $class;
			return true;
		}
		return false;
	}
	
	function set_locator_class($class = 'SimplePie_Locator')
	{
		if (SimplePie_Misc::is_a_class($class, 'SimplePie_Locator'))
		{
			$this->locator_class = $class;
			return true;
		}
		return false;
	}
	
	function set_parser_class($class = 'SimplePie_Parser')
	{
		if (SimplePie_Misc::is_a_class($class, 'SimplePie_Parser'))
		{
			$this->parser_class = $class;
			return true;
		}
		return false;
	}
	
	function set_sanitize_class($object = 'SimplePie_Sanitize')
	{
		if (class_exists($object))
		{
			$this->sanitize = new $object;
			return true;
		}
		return false;
	}
	
	function set_useragent($ua)
	{
		$this->useragent = (string) $ua;
	}
	
	function bypass_image_hotlink($get = false)
	{
		$this->sanitize->bypass_image_hotlink($get);
	}
	
	function bypass_image_hotlink_page($page = false)
	{
		$this->sanitize->bypass_image_hotlink_page($page);
	}
	
	function replace_headers($enable = false)
	{
		$this->sanitize->replace_headers($enable);
	}
	
	function remove_div($enable = true)
	{
		$this->sanitize->remove_div($enable);
	}
	
	function strip_ads($enable = false)
	{
		$this->sanitize->strip_ads($enable);
	}
	
	function strip_htmltags($tags = array('base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style'), $encode = null)
	{
		$this->sanitize->strip_htmltags($tags);
		if (!is_null($encode))
		{
			$this->sanitize->encode_instead_of_strip($tags);
		}
	}
	
	function encode_instead_of_strip($enable = true)
	{
		$this->sanitize->encode_instead_of_strip($enable);
	}
	
	function strip_attributes($attribs = array('bgsound', 'class', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur'))
	{
		$this->sanitize->strip_attributes($attribs);
	}
	
	function output_encoding($encoding = 'UTF-8')
	{
		$this->sanitize->output_encoding($encoding);
	}
	
	function set_item_class($class = 'SimplePie_Item')
	{
		return $this->sanitize->set_item_class($class);
	}
	
	function set_author_class($class = 'SimplePie_Author')
	{
		return $this->sanitize->set_author_class($class);
	}
	
	function set_enclosure_class($class = 'SimplePie_Enclosure')
	{
		return $this->sanitize->set_enclosure_class($class);
	}
	
	function init()
	{
		if (!(function_exists('version_compare') && ((version_compare(phpversion(), '4.3.2', '>=') && version_compare(phpversion(), '5', '<')) || version_compare(phpversion(), '5.0.3', '>='))) || !extension_loaded('xml') || !extension_loaded('pcre'))
		{
			return false;
		}
		if ($this->sanitize->bypass_image_hotlink && !empty($_GET[$this->sanitize->bypass_image_hotlink]))
		{
			if (get_magic_quotes_gpc())
			{
				$_GET[$this->sanitize->bypass_image_hotlink] = stripslashes($_GET[$this->sanitize->bypass_image_hotlink]);
			}
			SimplePie_Misc::display_file($_GET[$this->sanitize->bypass_image_hotlink], 10, $this->useragent);
		}
		
		if (isset($_GET['js']))
		{
			$embed = <<<EOT
function embed_odeo(link) {
	document.writeln('<embed src="http://odeo.com/flash/audio_player_fullsize.swf" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" quality="high" width="440" height="80" wmode="transparent" allowScriptAccess="any" flashvars="valid_sample_rate=true&external_url='+link+'"></embed>');
}

function embed_quicktime(type, bgcolor, width, height, link, placeholder, loop) {
	if (placeholder != '') {
		document.writeln('<embed type="'+type+'" style="cursor:hand; cursor:pointer;" href="'+link+'" src="'+placeholder+'" width="'+width+'" height="'+height+'" autoplay="false" target="myself" controller="false" loop="'+loop+'" scale="aspect" bgcolor="'+bgcolor+'" pluginspage="http://www.apple.com/quicktime/download/"></embed>');
	}
	else {
		document.writeln('<embed type="'+type+'" style="cursor:hand; cursor:pointer;" src="'+link+'" width="'+width+'" height="'+height+'" autoplay="false" target="myself" controller="true" loop="'+loop+'" scale="aspect" bgcolor="'+bgcolor+'" pluginspage="http://www.apple.com/quicktime/download/"></embed>');
	}
}

function embed_flash(bgcolor, width, height, link, loop, type) {
	document.writeln('<embed src="'+link+'" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="'+type+'" quality="high" width="'+width+'" height="'+height+'" bgcolor="'+bgcolor+'" loop="'+loop+'"></embed>');
}

function embed_wmedia(width, height, link) {
	document.writeln('<embed type="application/x-mplayer2" src="'+link+'" autosize="1" width="'+width+'" height="'+height+'" showcontrols="1" showstatusbar="0" showdisplay="0" autostart="0"></embed>');
}
EOT;

			if (function_exists('ob_gzhandler'))
			{
				ob_start('ob_gzhandler');
			}
			header('Content-type: text/javascript; charset: UTF-8'); 
			header('Cache-Control: must-revalidate'); 
			header('Expires: ' .  gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
			echo $embed;
			exit;
		}
		
		if (!empty($this->rss_url) || !empty($this->raw_data))
		{
			$this->data = array();
			$cache = false;
			
			if (!empty($this->rss_url))
			{
				// Decide whether to enable caching
				if ($this->enable_cache && preg_match('/^http(s)?:\/\//i', $this->rss_url))
				{
					$cache = new $this->cache_class($this->cache_location, sha1($this->rss_url), 'spc');
				}
				// If it's enabled and we don't want an XML dump, use the cache
				if ($cache && !$this->xml_dump)
				{
					// Load the Cache
					$this->data = $cache->load();
					if (!empty($this->data))
					{
						// If we've hit a sha1 collision just rerun it with caching disabled
						if (isset($this->data['url']) && $this->data['url'] != $this->rss_url)
						{
							$cache = false;
						}
						// If we've got a feed_url stored (if the page isn't actually a feed, or is a redirect) use that URL
						else if (!empty($this->data['feed_url']))
						{
							$this->feed_url($this->data['feed_url']);
							return $this->init();
						}
						// If the cache is new enough
						else if ($cache->mtime() + $this->max_minutes * 60 < time())
						{
							// If we have last-modified and/or etag set
							if (!empty($this->data['last-modified']) || !empty($this->data['etag']))
							{
								$headers = array();
								if (!empty($this->data['last-modified']))
								{
									$headers['if-modified-since'] = $this->data['last-modified'];
								}
								if (!empty($this->data['etag']))
								{
									$headers['if-none-match'] = $this->data['etag'];
								}
								$file = new SimplePie_File($this->rss_url, $this->timeout/10, 5, $headers, $this->useragent);
								if ($file->success)
								{
									$headers = $file->headers();
									if ($headers['status']['code'] == 304)
									{
										$cache->touch();
										return true;
									}
								}
								unset($file);
							}
							// If we don't have last-modified or etag set, just clear the cache
							else
							{
								$cache->unlink();
							}
						}
						// If the cache is still valid, just return true
						else
						{
							return true;
						}
					}
					// If the cache is empty, delete it
					else
					{
						$cache->unlink();
					}
				}
				$this->data = array();
				// If we don't already have the file (it'll only exist if we've opened it to check if the cache has been modified), open it.
				if (!isset($file))
				{
					if (is_a($this->file, 'SimplePie_File') && $this->file->url == $this->rss_url)
					{
						$file =& $this->file;
					}
					else
					{
						$file = new SimplePie_File($this->rss_url, $this->timeout, 5, null, $this->useragent);
					}
				}
				// If the file connection has an error, set SimplePie::error to that and quit
				if (!$file->success)
				{
					$this->error = $file->error;
					return false;
				}
				
				// Check if the supplied URL is a feed, if it isn't, look for it.
				$locate = new $this->locator_class($file, $this->timeout, $this->useragent);
				if (!$locate->is_feed($file))
				{
					$feed = $locate->find();
					if ($feed)
					{
						if ($cache && !$cache->save(array('url' => $this->rss_url, 'feed_url' => $feed)))
						{
							$this->error = "$cache->name is not writeable";
							SimplePie_Misc::error($this->error, E_USER_WARNING, __FILE__, __LINE__);
						}
						$this->rss_url = $feed;
						return $this->init();
					}
					else
					{
						$this->error = "A feed could not be found at $this->rss_url";
						SimplePie_Misc::error($this->error, E_USER_WARNING, __FILE__, __LINE__);
						return false;
					}
				}
				
				$headers = $file->headers();
				$data = trim($file->body());
				$file->close();
				unset($file);
			}
			else
			{
				$data = $this->raw_data;
			}
			
			// First check to see if input has been overridden.
			if (!empty($this->input_encoding))
			{
				$encoding = $this->input_encoding;
			}
			// Second try HTTP headers
			else if (!empty($headers['content-type']) && preg_match('/charset\s*=\s*([^;]*)/i', $headers['content-type'], $charset))
			{
				$encoding = $charset[1];
			}
			// Then prolog, if at the very start of the document
			else if (preg_match('/^<\?xml(.*)?>/msiU', $data, $prolog) && preg_match('/encoding\s*=\s*("([^"]*)"|\'([^\']*)\')/Ui', $prolog[1], $encoding))
			{
				$encoding = substr($encoding[1], 1, -1);
			}
			// UTF-32 Big Endian BOM
			else if (strpos($data, sprintf('%c%c%c%c', 0x00, 0x00, 0xFE, 0xFF)) === 0)
			{
				$encoding = 'UTF-32be';
			}
			// UTF-32 Little Endian BOM
			else if (strpos($data, sprintf('%c%c%c%c', 0xFF, 0xFE, 0x00, 0x00)) === 0)
			{
				$encoding = 'UTF-32';
			}
			// UTF-16 Big Endian BOM
			else if (strpos($data, sprintf('%c%c', 0xFE, 0xFF)) === 0)
			{
				$encoding = 'UTF-16be';
			}
			// UTF-16 Little Endian BOM
			else if (strpos($data, sprintf('%c%c', 0xFF, 0xFE)) === 0)
			{
				$encoding = 'UTF-16le';
			}
			// UTF-8 BOM
			else if (strpos($data, sprintf('%c%c%c', 0xEF, 0xBB, 0xBF)) === 0)
			{
				$encoding = 'UTF-8';
			}
			// Fallback to the default
			else
			{
				$encoding = null;
			}
			
			// Change the encoding to UTF-8 (as we always use UTF-8 internally)
			$data = SimplePie_Misc::change_encoding($data, $encoding, 'UTF-8');
			
			// Start parsing
			$data = new $this->parser_class($data, 'UTF-8', $this->xml_dump);
			// If we want the XML, just output that and quit
			if ($this->xml_dump)
			{
				header('Content-type: text/xml; charset=UTF-8');
				echo $data->data;
				exit;
			}
			// If it's parsed fine
			else if (!$data->error_code)
			{
				// Parse the data, and make it sane
				$this->sanitize->parse_data_array($data->data, $this->rss_url);
				unset($data);
				// Get the sane data
				$this->data['feedinfo'] = $this->sanitize->feedinfo;
				unset($this->sanitize->feedinfo);
				$this->data['info'] = $this->sanitize->info;
				unset($this->sanitize->info);
				$this->data['items'] = $this->sanitize->items;
				unset($this->sanitize->items);
				$this->data['feedinfo']['encoding'] = $this->sanitize->output_encoding;
				$this->data['url'] = $this->rss_url;
				
				// Store the headers that we need
				if (!empty($headers['last-modified']))
				{
					$this->data['last-modified'] = $headers['last-modified'];
				}
				if (!empty($headers['etag']))
				{
					$this->data['etag'] = $headers['etag'];
				}
				
				// If we want to order it by date, check if all items have a date, and then sort it
				if ($this->order_by_date && !empty($this->data['items']))
				{
					$do_sort = true;
					foreach ($this->data['items'] as $item)
					{
						if (!$item->get_date('U'))
						{
							$do_sort = false;
							break;
						}
					}
					if ($do_sort)
					{
						usort($this->data['items'], create_function('$a, $b', 'if ($a->get_date(\'U\') == $b->get_date(\'U\')) return 1; return ($a->get_date(\'U\') < $b->get_date(\'U\')) ? 1 : -1;'));
					}
				}
				
				// Cache the file if caching is enabled
				if ($cache && !$cache->save($this->data))
				{
					$this->error = "$cache->name is not writeable";
					SimplePie_Misc::error($this->error, E_USER_WARNING, __FILE__, __LINE__);
				}
				return true;
			}
			// If we have an error, just set SimplePie::error to it and quit
			else
			{
				$this->error = "XML error: $data->error_string at line $data->current_line, column $data->current_column";
				SimplePie_Misc::error($this->error, E_USER_WARNING, __FILE__, __LINE__);
				return false;
			}
		}
	}
	
	function get_encoding()
	{
		if (!empty($this->data['feedinfo']['encoding']))
		{
			return $this->data['feedinfo']['encoding'];
		}
		else
		{
			return false;
		}
	}
	
	function handle_content_type($mime = 'text/html')
	{
		if (!headers_sent())
		{
			$header = "Content-type: $mime;";
			if ($this->get_encoding())
			{
				$header .= ' charset=' . $this->get_encoding();
			}
			else
			{
				$header .= ' charset=UTF-8';
			}
			header($header);
		}
	}
	
	function get_type()
	{
		if (!empty($this->data['feedinfo']['type']))
		{
			return $this->data['feedinfo']['type'];
		}
		else
		{
			return false;
		}
	}
	
	function get_version()
	{
		if (!empty($this->data['feedinfo']['version']))
		{
			return $this->data['feedinfo']['version'];
		}
		else
		{
			return false;
		}
	}
	
	function get_favicon($check = false, $alternate = null)
	{
		if (!empty($this->data['info']['link']['alternate'][0]))
		{
			$favicon = SimplePie_Misc::absolutize_url('/favicon.ico', $this->get_feed_link());

			if ($check)
			{
				$file = new SimplePie_File($favicon, $this->timeout/10, 5, null, $this->useragent);
				$headers = $file->headers();
				$file->close();

				if ($headers['status']['code'] == 200)
				{
					return $favicon;
				}
			}
			else
			{
				return $favicon;
			}
		}
		if (!is_null($alternate))
		{
			return $alternate;
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_url()
	{
		if (!empty($this->rss_url))
		{
			return $this->rss_url;
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_feed()
	{
		if (!empty($this->rss_url))
		{
			return SimplePie_Misc::fix_protocol($this->rss_url, 2);
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_outlook()
	{
		if (!empty($this->rss_url))
		{
			return 'outlook' . SimplePie_Misc::fix_protocol($this->rss_url, 2);
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_podcast()
	{
		if (!empty($this->rss_url))
		{
			return SimplePie_Misc::fix_protocol($this->rss_url, 3);
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_aol()
	{
		if ($this->subscribe_url())
		{
			return 'http://feeds.my.aol.com/add.jsp?url=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_bloglines()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.bloglines.com/sub/' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_eskobo()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.eskobo.com/?AddToMyPage=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_feedfeeds()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.feedfeeds.com/add?feed=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_feedlounge()
	{
		if ($this->subscribe_url())
		{
			return 'http://my.feedlounge.com/external/subscribe?url=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_feedster()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.feedster.com/myfeedster.php?action=addrss&amp;confirm=no&amp;rssurl=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_google()
	{
		if ($this->subscribe_url())
		{
			return 'http://fusion.google.com/add?feedurl=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_gritwire()
	{
		if ($this->subscribe_url())
		{
			return 'http://my.gritwire.com/feeds/addExternalFeed.aspx?FeedUrl=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_msn()
	{
		if ($this->subscribe_url())
		{
			$url = 'http://my.msn.com/addtomymsn.armx?id=rss&amp;ut=' . rawurlencode($this->subscribe_url());
			if ($this->get_feed_link())
			{
				$url .= '&amp;ru=' . rawurlencode($this->get_feed_link());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_netvibes()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.netvibes.com/subscribe.php?url=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_newsburst()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.newsburst.com/Source/?add=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_newsgator()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.newsgator.com/ngs/subscriber/subext.aspx?url=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_odeo()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.odeo.com/listen/subscribe?feed=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_pluck()
	{
		if ($this->subscribe_url())
		{
			return 'http://client.pluck.com/pluckit/prompt.aspx?GCID=C12286x053&amp;a=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_podnova()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.podnova.com/index_your_podcasts.srf?action=add&amp;url=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_rojo()
	{
		if ($this->subscribe_url())
		{
			return 'http://www.rojo.com/add-subscription?resource=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function subscribe_yahoo()
	{
		if ($this->subscribe_url())
		{
			return 'http://add.my.yahoo.com/rss?url=' . rawurlencode($this->subscribe_url());
		}
		else
		{
			return false;
		}
	}
	
	function get_feed_title()
	{
		if (!empty($this->data['info']['title']))
		{
			return $this->data['info']['title'];
		}
		else
		{
			return false;
		}
	}
	
	function get_feed_link()
	{
		if (!empty($this->data['info']['link']['alternate'][0]))
		{
			return $this->data['info']['link']['alternate'][0];
		}
		else
		{
			return false;
		}
	}
	
	function get_feed_links()
	{
		if (!empty($this->data['info']['link']))
		{
			return $this->data['info']['link'];
		}
		else
		{
			return false;
		}
	}
	
	function get_feed_description()
	{
		if (!empty($this->data['info']['description']))
		{
			return $this->data['info']['description'];
		}
		else if (!empty($this->data['info']['dc:description']))
		{
			return $this->data['info']['dc:description'];
		}
		else if (!empty($this->data['info']['tagline']))
		{
			return $this->data['info']['tagline'];
		}
		else if (!empty($this->data['info']['subtitle']))
		{
			return $this->data['info']['subtitle'];
		}
		else
		{
			return false;
		}
	}
	
	function get_feed_copyright()
	{
		if (!empty($this->data['info']['copyright']))
		{
			return $this->data['info']['copyright'];
		}
		else
		{
			return false;
		}
	}
	
	function get_feed_language()
	{
		if (!empty($this->data['info']['language']))
		{
			return $this->data['info']['language'];
		}
		else if (!empty($this->data['info']['xml:lang']))
		{
			return $this->data['info']['xml:lang'];
		}
		else
		{
			return false;
		}
	}
	
	function get_image_exist()
	{
		if (!empty($this->data['info']['image']['url']) || !empty($this->data['info']['image']['logo']))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function get_image_title()
	{
		if (!empty($this->data['info']['image']['title']))
		{
			return $this->data['info']['image']['title'];
		}
		else
		{
			return false;
		}
	}
	
	function get_image_url()
	{
		if (!empty($this->data['info']['image']['url']))
		{
			return $this->data['info']['image']['url'];
		}
		else if (!empty($this->data['info']['image']['logo']))
		{
			return $this->data['info']['image']['logo'];
		}
		else
		{
			return false;
		}
	}
	
	function get_image_link()
	{
		if (!empty($this->data['info']['image']['link']))
		{
			return $this->data['info']['image']['link'];
		}
		else
		{
			return false;
		}
	}
	
	function get_image_width()
	{
		if (!empty($this->data['info']['image']['width']))
		{
			return $this->data['info']['image']['width'];
		}
		else
		{
			return false;
		}
	}
	
	function get_image_height()
	{
		if (!empty($this->data['info']['image']['height']))
		{
			return $this->data['info']['image']['height'];
		}
		else
		{
			return false;
		}
	}
	
	function get_item_quantity($max = 0)
	{
		if (!empty($this->data['items']))
		{
			$qty = sizeof($this->data['items']);
		}
		else
		{
			$qty = 0;
		}
		if ($max == 0)
		{
			return $qty;
		}
		else
		{
			return ($qty > $max) ? $max : $qty;
		}
	}
	
	function get_item($key = 0)
	{
		if (!empty($this->data['items'][$key]))
		{
			return $this->data['items'][$key];
		}
		else
		{
			return false;
		}
	}
	
	function get_items($start = 0, $end = 0)
	{
		if ($this->get_item_quantity() > 0)
		{
			if ($end == 0)
			{
				return array_slice($this->data['items'], $start);
			}
			else
			{
				return array_slice($this->data['items'], $start, $end);
			}
		}
		else
		{
			return false;
		}
	}
}

class SimplePie_Item
{
	var $data;
	
	function SimplePie_Item($data)
	{
		$this->data =& $data;
	}
	
	function get_id()
	{
		if (!empty($this->data['guid']['data']))
		{
			return $this->data['guid']['data'];
		}
		else if (!empty($this->data['id']))
		{
			return $this->data['id'];
		}
		else
		{
			return false;
		}
	}
	
	function get_title()
	{
		if (!empty($this->data['title']))
		{
			return $this->data['title'];
		}
		else if (!empty($this->data['dc:title']))
		{
			return $this->data['dc:title'];
		}
		else
		{
			return false;
		}
	}
	
	function get_description()
	{
		if (!empty($this->data['content']))
		{
			return $this->data['content'];
		}
		else if (!empty($this->data['encoded']))
		{
			return $this->data['encoded'];
		}
		else if (!empty($this->data['summary']))
		{
			return $this->data['summary'];
		}
		else if (!empty($this->data['description']))
		{
			return $this->data['description'];
		}
		else if (!empty($this->data['dc:description']))
		{
			return $this->data['dc:description'];
		}
		else if (!empty($this->data['longdesc']))
		{
			return $this->data['longdesc'];
		}
		else
		{
			return false;
		}
	}
	
	function get_category($key = 0)
	{
		$categories = $this->get_categories();
		if (!empty($categories[$key]))
		{
			return $categories[$key];
		}
		else
		{
			return false;
		}
	}
	
	function get_categories()
	{
		$categories = array();
		if (!empty($this->data['category']))
		{
			$categories = array_merge($categories, $this->data['category']);
		}
		if (!empty($this->data['subject']))
		{
			$categories = array_merge($categories, $this->data['subject']);
		}
		if (!empty($this->data['term']))
		{
			$categories = array_merge($categories, $this->data['term']);
		}
		if (!empty($categories))
		{
			return array_unique($categories);
		}
		else
		{
			return false;
		}
	}
	
	function get_author($key = 0)
	{
		$authors = $this->get_authors();
		if (!empty($authors[$key]))
		{
			return $authors[$key];
		}
		else
		{
			return false;
		}
	}
	
	function get_authors()
	{
		$authors = array();
		if (!empty($this->data['author']))
		{
			$authors = array_merge($authors, $this->data['author']);
		}
		if (!empty($this->data['creator']))
		{
			$authors = array_merge($authors, $this->data['creator']);
		}
		if (!empty($authors))
		{
			return array_unique($authors);
		}
		else
		{
			return false;
		}
	}
	
	function get_date($date_format = 'j F Y, g:i a')
	{
		if (!empty($this->data['pubdate']))
		{
			return date($date_format, $this->data['pubdate']);
		}
		else if (!empty($this->data['dc:date']))
		{
			return date($date_format, $this->data['dc:date']);
		}
		else if (!empty($this->data['issued']))
		{
			return date($date_format, $this->data['issued']);
		}
		else if (!empty($this->data['published']))
		{
			return date($date_format, $this->data['published']);
		}
		else if (!empty($this->data['modified']))
		{
			return date($date_format, $this->data['modified']);
		}
		else if (!empty($this->data['updated']))
		{
			return date($date_format, $this->data['updated']);
		}
		else
		{
			return false;
		}
	}
	
	function get_permalink()
	{
		$link = $this->get_link(0);
		$enclosure = $this->get_enclosure(0);
		if (!empty($link))
		{
			return $link;
		}
		else if (!empty($enclosure))
		{
			return $enclosure->get_link();
		}
		else
		{
			return false;
		}
	}
	
	function get_link($key = 0, $rel = 'alternate')
	{
		$links = $this->get_links($rel);
		if (!empty($links[$key]))
		{
			return $links[$key];
		}
		else
		{
			return false;
		}
	}
	
	function get_links($rel = 'alternate')
	{
		if ($rel == 'alternate')
		{
			$links = array();
			if (!empty($this->data['link'][$rel]))
			{
				$links = $this->data['link'][$rel];
			}
			if (!empty($this->data['guid']['data']) && $this->data['guid']['permalink'] == true)
			{
				$links[] = $this->data['guid']['data'];
			}
			return $links;
		}
		else if (!empty($this->data['link'][$rel]))
		{
			return $this->data['link'][$rel];
		}
		else
		{
			return false;
		}
	}
	
	function get_enclosure($key = 0)
	{
		$enclosures = $this->get_enclosures();
		if (!empty($enclosures[$key]))
		{
			return $enclosures[$key];
		}
		else
		{
			return false;
		}
	}
	
	function get_enclosures()
	{
		$enclosures = array();
		$links = $this->get_links('enclosure');
		if (!empty($this->data['enclosures']))
		{
			$enclosures = array_merge($enclosures, $this->data['enclosures']);
		}
		if (!empty($links))
		{
			$enclosures = array_merge($enclosures, $links);
		}
		if (!empty($enclosures))
		{
			return array_unique($enclosures);
		}
		else
		{
			return false;
		}
	}
	
	function add_to_blinklist()
	{
		if ($this->get_permalink())
		{
			$url = 'http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Description=&amp;Url=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;Title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_blogmarks()
	{
		if ($this->get_permalink())
		{
			$url = 'http://blogmarks.net/my/new.php?mini=1&amp;simple=1&amp;url=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_delicious()
	{
		if ($this->get_permalink())
		{
			$url = 'http://del.icio.us/post/?v=3&amp;url=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_digg()
	{
		if ($this->get_permalink())
		{
			return 'http://digg.com/submit?phase=2&amp;URL=' . rawurlencode($this->get_permalink());
		}
		else
		{
			return false;
		}
	}
	
	function add_to_furl()
	{
		if ($this->get_permalink())
		{
			$url = 'http://www.furl.net/storeIt.jsp?u=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;t=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_magnolia()
	{
		if ($this->get_permalink())
		{
			$url = 'http://ma.gnolia.com/bookmarklet/add?url=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_myweb20()
	{
		if ($this->get_permalink())
		{
			$url = 'http://myweb2.search.yahoo.com/myresults/bookmarklet?u=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;t=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_newsvine()
	{
		if ($this->get_permalink())
		{
			$url = 'http://www.newsvine.com/_wine/save?u=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;h=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_reddit()
	{
		if ($this->get_permalink())
		{
			$url = 'http://reddit.com/submit?url=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_segnalo()
	{
		if ($this->get_permalink())
		{
			$url = 'http://segnalo.com/post.html.php?url=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_simpy()
	{
		if ($this->get_permalink())
		{
			$url = 'http://www.simpy.com/simpy/LinkAdd.do?href=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_smarking()
	{
		if ($this->get_permalink())
		{
			return 'http://smarking.com/editbookmark/?url=' . rawurlencode($this->get_permalink());
		}
		else
		{
			return false;
		}
	}
	
	function add_to_spurl()
	{
		if ($this->get_permalink())
		{
			$url = 'http://www.spurl.net/spurl.php?v=3&amp;url=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function add_to_wists()
	{
		if ($this->get_permalink())
		{
			$url = 'http://wists.com/r.php?c=&amp;r=' . rawurlencode($this->get_permalink());
			if ($this->get_title())
			{
				$url .= '&amp;title=' . rawurlencode($this->get_title());
			}
			return $url;
		}
		else
		{
			return false;
		}
	}
	
	function search_technorati()
	{
		if ($this->get_permalink())
		{
			return 'http://www.technorati.com/search/' . rawurlencode($this->get_permalink());
		}
		else
		{
			return false;
		}
	}
}

class SimplePie_Author
{
	var $name;
	var $link;
	var $email;
	
	// Constructor, used to input the data
	function SimplePie_Author($name, $link, $email)
	{
		$this->name = $name;
		$this->link = $link;
		$this->email = $email;
	}
	
	function get_name()
	{
		if (!empty($this->name))
		{
			return $this->name;
		}
		else
		{
			return false;
		}
	}
	
	function get_link()
	{
		if (!empty($this->link))
		{
			return $this->link;
		}
		else
		{
			return false;
		}
	}
	
	function get_email()
	{
		if (!empty($this->email))
		{
			return $this->email;
		}
		else
		{
			return false;
		}
	}
}

class SimplePie_Enclosure
{
	var $link;
	var $type;
	var $length;

	// Constructor, used to input the data
	function SimplePie_Enclosure($link, $type, $length)
	{
		$this->link = $link;
		$this->type = $type;
		$this->length = $length;
	}

	function get_link()
	{
		if (!empty($this->link))
		{
			if (class_exists('idna_convert'))
			{
				$idn = new idna_convert;
				$this->link = $idn->encode($this->link);
			}
			return $this->link;
		}
		else
		{
			return false;
		}
	}

	function get_extension()
	{
		if (!empty($this->link))
		{
			return pathinfo($this->link, PATHINFO_EXTENSION);
		}
		else
		{
			return false;
		}
	}

	function get_type()
	{
		if (!empty($this->type))
		{
			return $this->type;
		}
		else
		{
			return false;
		}
	}

	function get_length()
	{
		if (!empty($this->length))
		{
			return $this->length;
		}
		else
		{
			return false;
		}
	}

	function get_size()
	{
		$length = $this->get_length();
		if (!empty($length))
		{
			return round($length/1048576, 2);
		}
		else
		{
			return false;
		}
	}

	function native_embed($options='')
	{
		return $this->embed($options, true);		
	}

	function embed($options = '', $native = false)
	{
		// Set up defaults
		$audio = '';
		$video = '';
		$alt = '';
		$altclass = '';
		$loop = 'false';
		$width = 'auto';
		$height = 'auto';
		$bgcolor = '#ffffff';

		// Process options and reassign values as necessary
		if (is_array($options))
		{
			extract($options);
		}
		else
		{
			$options = explode(',', $options);
			foreach($options as $option)
			{
				$opt = explode(':', $option, 2);
				if (isset($opt[0], $opt[1]))
				{
					$opt[0] = trim($opt[0]);
					$opt[1] = trim($opt[1]);
					switch ($opt[0])
					{
						case 'audio':
							$audio = $opt[1];
							break;
						
						case 'video':
							$video = $opt[1];
							break;
						
						case 'alt':
							$alt = $opt[1];
							break;
						
						case 'altclass':
							$altclass = $opt[1];
							break;
						
						case 'loop':
							$loop = $opt[1];
							break;
						
						case 'width':
							$width = $opt[1];
							break;
						
						case 'height':
							$height = $opt[1];
							break;
						
						case 'bgcolor':
							$bgcolor = $opt[1];
							break;
					}
				}
			}
		}
	
		$type = strtolower($this->get_type());

		// If we encounter an unsupported mime-type, check the file extension and guess intelligently.
		if (!in_array($type, array('audio/3gpp', 'audio/3gpp2', 'audio/aac', 'audio/x-aac', 'audio/aiff', 'audio/x-aiff', 'audio/mid', 'audio/midi', 'audio/x-midi', 'audio/mpeg', 'audio/x-mpeg', 'audio/mp3', 'x-audio/mp3', 'audio/mp4', 'audio/m4a', 'audio/x-m4a', 'audio/wav', 'audio/x-wav', 'video/3gpp', 'video/3gpp2', 'video/m4v', 'video/x-m4v', 'video/mp4', 'video/mpeg', 'video/x-mpeg', 'video/quicktime', 'video/sd-video', 'application/x-shockwave-flash', 'application/futuresplash', 'application/asx', 'application/x-mplayer2', 'audio/x-ms-wma', 'audio/x-ms-wax', 'video/x-ms-asf-plugin', 'video/x-ms-asf', 'video/x-ms-wm', 'video/x-ms-wmv', 'video/x-ms-wvx')))
		{			
			switch (strtolower($this->get_extension()))
			{
				// Audio mime-types
				case 'aac':
				case 'adts':
					$type = 'audio/acc';
					break;
				
				case 'aif':
				case 'aifc':
				case 'aiff':
				case 'cdda':
					$type = 'audio/aiff';
					break;
				
				case 'bwf':
					$type = 'audio/wav';
					break;
				
				case 'kar':
				case 'mid':
				case 'midi':
				case 'smf':
					$type = 'audio/midi';
					break;
				
				case 'm4a':
					$type = 'audio/x-m4a';
					break;
				
				case 'mp3':
				case 'swa':
					$type = 'audio/mp3';
					break;
				
				case 'wav':
					$type = 'audio/wav';
					break;
				
				case 'wax':
					$type = 'audio/x-ms-wax';
					break;
				
				case 'wma':
					$type = 'audio/x-ms-wma';
					break;
				
				// Video mime-types
				case '3gp':
				case '3gpp':
					$type = 'video/3gpp';
					break;

				case '3g2':
				case '3gp2':
					$type = 'video/3gpp2';
					break;

				case 'asf':
					$type = 'video/x-ms-asf';
					break;

				case 'm1a':
				case 'm1s':
				case 'm1v':
				case 'm15':
				case 'm75':
				case 'mp2':
				case 'mpa':
				case 'mpeg':
				case 'mpg':
				case 'mpm':
				case 'mpv':
					$type = 'video/mpeg';
					break;

				case 'm4v':
					$type = 'video/x-m4v';
					break;

				case 'mov':
				case 'qt':
					$type = 'video/quicktime';
					break;

				case 'mp4':
				case 'mpg4':
					$type = 'video/mp4';
					break;

				case 'sdv':
					$type = 'video/sd-video';
					break;

				case 'wm':
					$type = 'video/x-ms-wm';
					break;

				case 'wmv':
					$type = 'video/x-ms-wmv';
					break;

				case 'wvx':
					$type = 'video/x-ms-wvx';
					break;
					
				// Flash mime-types
				case 'spl':
					$type = 'application/futuresplash';
					break;

				case 'swf':
					$type = 'application/x-shockwave-flash';
					break;
			}
		}

		$mime = explode('/', $type, 2);
		$mime = $mime[0];
		
		// Process values for 'auto'
		if ($width == 'auto')
		{
			if ($mime == 'video')
			{
				$width = '320';
			}
			else
			{
				$width = '100%';
			}
		}
		if ($height == 'auto')
		{
			if ($mime == 'audio')
			{
				$height = 0;
			}
			else if ($mime == 'video')
			{
				$height = 240;
			}
			else
			{
				$height = 256;
			}
		}

		// Set proper placeholder value
		if ($mime == 'audio')
		{
			$placeholder = $audio;
		}
		else if ($mime == 'video')
		{
			$placeholder = $video;
		}

		$embed = '';

		// Make sure the JS library is included
		// (I know it'll be included multiple times, but I can't think of a better way to do this automatically)
		if (!$native)
		{
			$embed .= '<script type="text/javascript" src="?js"></script>';			
		}

		// Odeo Feed MP3's
		if (substr(strtolower($this->get_link()), 0, 15) == 'http://odeo.com') {
			if ($native)
			{
				$embed .= '<embed src="http://odeo.com/flash/audio_player_fullsize.swf" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" quality="high" width="440" height="80" wmode="transparent" allowScriptAccess="any" flashvars="valid_sample_rate=true&external_url=' . $this->get_link() . '"></embed>';
			}
			else
			{
				$embed .= '<script type="text/javascript">embed_odeo("' . $this->get_link() . '");</script>';				
			}
		}

		// QuickTime 7 file types.  Need to test with QuickTime 6.
		else if (in_array($type, array('audio/3gpp', 'audio/3gpp2', 'audio/aac', 'audio/x-aac', 'audio/aiff', 'audio/x-aiff', 'audio/mid', 'audio/midi', 'audio/x-midi', 'audio/mpeg', 'audio/x-mpeg', 'audio/mp3', 'x-audio/mp3', 'audio/mp4', 'audio/m4a', 'audio/x-m4a', 'audio/wav', 'audio/x-wav', 'video/3gpp', 'video/3gpp2', 'video/m4v', 'video/x-m4v', 'video/mp4', 'video/mpeg', 'video/x-mpeg', 'video/quicktime', 'video/sd-video')))
		{
			$height += 16;
			if ($native)
			{
				if ($placeholder != "") {
					$embed .= "<embed type=\"$type\" style=\"cursor:hand; cursor:pointer;\" href=\"" . $this->get_link() . "\" src=\"$placeholder\" width=\"$width\" height=\"$height\" autoplay=\"false\" target=\"myself\" controller=\"false\" loop=\"$loop\" scale=\"aspect\" bgcolor=\"$bgcolor\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed>";
				}
				else {
					$embed .= "<embed type=\"$type\" style=\"cursor:hand; cursor:pointer;\" src=\"" . $this->get_link() . "\" width=\"$width+\" height=\"$height\" autoplay=\"false\" target=\"myself\" controller=\"true\" loop=\"$loop\" scale=\"aspect\" bgcolor=\"$bgcolor\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed>";
				}
			}
			else
			{
				$embed .= "<script type='text/javascript'>embed_quicktime('$type', '$bgcolor', '$width', '$height', '" . $this->get_link() . "', '$placeholder', '$loop');</script>";
			}
		}

		// Flash
		else if (in_array($type, array('application/x-shockwave-flash', 'application/futuresplash')))
		{
			if ($native)
			{
				$embed .= "<embed src=\"" . $this->get_link() . "\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"$type\" quality=\"high\" width=\"$width\" height=\"$height\" bgcolor=\"$bgcolor\" loop=\"$loop\"></embed>";
			}
			else
			{
				$embed .= "<script type='text/javascript'>embed_flash('$bgcolor', '$width', '$height', '" . $this->get_link() . "', '$loop', '$type');</script>";
			}
		}

		// Windows Media
		else if (in_array($type, array('application/asx', 'application/x-mplayer2', 'audio/x-ms-wma', 'audio/x-ms-wax', 'video/x-ms-asf-plugin', 'video/x-ms-asf', 'video/x-ms-wm', 'video/x-ms-wmv', 'video/x-ms-wvx')))
		{
			$height += 45;
			if ($native)
			{
				$embed .= "<embed type=\"application/x-mplayer2\" src=\"" . $this->get_link() . "\" autosize=\"1\" width=\"$width\" height=\"$height\" showcontrols=\"1\" showstatusbar=\"0\" showdisplay=\"0\" autostart=\"0\"></embed>";
			}
			else
			{
				$embed .= "<script type='text/javascript'>embed_wmedia('$width', '$height', '" . $this->get_link() . "');</script>";
			}
		}

		// Everything else
		else $embed .= '<a href="' . $this->get_link() . '" class="' . $altclass . '">' . $alt . '</a>';

		return $embed;
	}
}

class SimplePie_File
{
	var $url;
	var $useragent;
	var $success = true;
	var $headers = array();
	var $body;
	var $fp;
	var $redirects = 0;
	var $error;
	var $method;
	
	function SimplePie_File($url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null)
	{
		if (class_exists('idna_convert'))
		{
			$idn = new idna_convert;
			$url = $idn->encode($url);
		}
		$this->url = $url;
		$this->useragent = $useragent;
		if (preg_match('/^http(s)?:\/\//i', $url))
		{
			if (empty($useragent))
			{
				$useragent = ini_get('user_agent');
				$this->useragent = $useragent;
			}
			if (!is_array($headers))
			{
				$headers = array();
			}
			if (extension_loaded('curl') && version_compare(SimplePie_Misc::get_curl_version(), '7.10', '>='))
			{
				$this->method = 'curl';
				$fp = curl_init();
				$headers2 = array();
				foreach ($headers as $key => $value)
				{
					$headers2[] = "$key: $value";
				}
				curl_setopt($fp, CURLOPT_ENCODING, '');
				curl_setopt($fp, CURLOPT_URL, $url);
				curl_setopt($fp, CURLOPT_HEADER, 1);
				curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($fp, CURLOPT_TIMEOUT, $timeout);
				curl_setopt($fp, CURLOPT_REFERER, $url);
				curl_setopt($fp, CURLOPT_USERAGENT, $useragent);
				curl_setopt($fp, CURLOPT_HTTPHEADER, $headers2);
				if (!ini_get('open_basedir') && !ini_get('safe_mode'))
				{
					curl_setopt($fp, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($fp, CURLOPT_MAXREDIRS, $redirects);
				}

				$this->headers = trim(curl_exec($fp));
				if (curl_errno($fp))
				{
					$this->error = 'cURL error: ' . curl_error($fp);
					$this->success = false;
					return false;
				}
				else
				{
					$info = curl_getinfo($fp);
					$this->headers = explode("\r\n\r\n", $this->headers, $info['redirect_count'] + 2);
					$this->body = array_pop($this->headers);
					$this->headers = array_pop($this->headers);
					$this->headers = $this->parse_headers($this->headers);
					if (($this->headers['status']['code'] == 301 || $this->headers['status']['code'] == 302 || $this->headers['status']['code'] == 303 || $this->headers['status']['code'] == 307) && !empty($this->headers['location']) && $this->redirects < $redirects)
					{
						$this->redirects++;
						return $this->SimplePie_File($this->headers['location'], $timeout, $redirects, $headers, $useragent);
					}
				}
			}
			else
			{
				$this->method = 'fsockopen';
				$url_parts = parse_url($url);
				if (isset($url_parts['scheme']) && strtolower($url_parts['scheme']) == 'https')
				{
					$url_parts['host'] = "ssl://$url_parts[host]";
					$url_parts['port'] = 443;
				}
				if (!isset($url_parts['port']))
				{
					$url_parts['port'] = 80;
				}
				$this->fp = fsockopen($url_parts['host'], $url_parts['port'], $errno, $errstr, $timeout);
				if (!$this->fp)
				{
					$this->error = 'fsockopen error: ' . $errstr;
					$this->success = false;
					return false;
				}
				else
				{
					stream_set_timeout($this->fp, $timeout);
					$get = (isset($url_parts['query'])) ? "$url_parts[path]?$url_parts[query]" : $url_parts['path'];
					$out = "GET $get HTTP/1.0\r\n";
					$out .= "Host: $url_parts[host]\r\n";
					$out .= "User-Agent: $useragent\r\n";
					if (function_exists('gzinflate'))
					{
						$out .= "Accept-Encoding: gzip,deflate\r\n";
					}

					if (!empty($url_parts['user']) && !empty($url_parts['pass']))
					{
						$out .= "Authorization: Basic " . base64_encode("$url_parts[user]:$url_parts[pass]") . "\r\n";
					}
					foreach ($headers as $key => $value)
					{
						$out .= "$key: $value\r\n";
					}
					$out .= "Connection: Close\r\n\r\n";
					fwrite($this->fp, $out);
					
					$info = stream_get_meta_data($this->fp);
					$data = '';
					while (strpos($data, "\r\n\r\n") === false && !$info['timed_out'])
					{
						$data .= fgets($this->fp, 128);
						$info = stream_get_meta_data($this->fp);
					}
					if (!$info['timed_out'])
					{
						$this->headers = $this->parse_headers($data);
						if (($this->headers['status']['code'] == 301 || $this->headers['status']['code'] == 302 || $this->headers['status']['code'] == 303 || $this->headers['status']['code'] == 307) && !empty($this->headers['location']) && $this->redirects < $redirects)
						{
							$this->redirects++;
							return $this->SimplePie_File($this->headers['location'], $timeout, $redirects, $headers, $useragent);
						}
					}
					else
					{
						$this->close();
						$this->error = 'fsocket timed out';
						$this->success = false;
						return false;
					}
				}
			}
			return $this->headers['status']['code'];
		}
		else
		{
			$this->method = 'fopen';
			if ($this->fp = fopen($url, 'r'))
			{
				return true;
			}
			else
			{
				$this->error = 'fopen could not open the file';
				$this->success = false;
				return false;
			}
		}
	}
	
	function headers()
	{
		return $this->headers;
	}
	
	function body()
	{
		if (is_null($this->body))
		{
			if ($this->fp)
			{
				$info = stream_get_meta_data($this->fp);
				$this->body = '';
				while (!$info['eof'] && !$info['timed_out'])
				{
					$this->body .= fread($this->fp, 1024);
					$info = stream_get_meta_data($this->fp);
				}
				if (!$info['timed_out'])
				{
					$this->body = trim($this->body);
					if ($this->method == 'fsockopen' && !empty($this->headers['content-encoding']) && $this->headers['content-encoding'] == 'gzip')
					{
						$this->body = substr($this->body, 10);
						$this->body = gzinflate($this->body);
					}
					$this->close();
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		return $this->body;
	}
	
	function close()
	{
		if (!is_null($this->fp))
		{
			if (fclose($this->fp))
			{
				$this->fp = null;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function parse_headers($headers)
	{
		$headers = explode("\r\n", trim($headers));
		$status = array_shift($headers);
		foreach ($headers as $header)
		{
			$data = explode(':', $header, 2);
			$head[strtolower(trim($data[0]))] = trim($data[1]);
		}
		if (preg_match('/HTTP\/[0-9\.]+ ([0-9]+)(.*)$/i', $status, $matches))
		{
			if (isset($head['status']))
			{
				unset($head['status']);
			}
			$head['status']['code'] = $matches[1];
			$head['status']['name'] = trim($matches[2]);
		}
		return $head;
	}
}

class SimplePie_Cache
{
	var $location;
	var $filename;
	var $extension;
	var $name;
	
	function SimplePie_Cache($location, $filename, $extension)
	{
		$this->location = $location;
		$this->filename = rawurlencode($filename);
		$this->extension = rawurlencode($extension);
		$this->name = "$location/$this->filename.$this->extension";
	}
	
	function save($data)
	{
		if (file_exists($this->name) && is_writeable($this->name) || file_exists($this->location) && is_writeable($this->location))
		{
			$fp = fopen($this->name, 'w');
			if ($fp)
			{
				fwrite($fp, serialize($data));
				fclose($fp);
				return true;
			}
		}
		return false;
	}
	
	function load()
	{
		if (file_exists($this->name) && is_readable($this->name))
		{
			return unserialize(file_get_contents($this->name));
		}
		return false;
	}
	
	function mtime()
	{
		if (file_exists($this->name))
		{
			return filemtime($this->name);
		}
		return false;
	}
	
	function touch()
	{
		if (file_exists($this->name))
		{
			return touch($this->name);
		}
		return false;
	}
	
	function unlink()
	{
		if (file_exists($this->name))
		{
			return unlink($this->name);
		}
		return false;
	}
}

class SimplePie_Misc
{
	function absolutize_url($relative, $base)
	{
		$relative = trim($relative);
		$base = trim($base);
		if (!empty($relative))
		{
			$relative = SimplePie_Misc::parse_url($relative, false);
			$relative = array('scheme' => $relative[2], 'authority' => $relative[3], 'path' => $relative[5], 'query' => $relative[7], 'fragment' => $relative[9]);
			if (!empty($relative['scheme']))
			{
				$target = $relative;
			}
			else if (!empty($base))
			{
				$base = SimplePie_Misc::parse_url($base, false);
				$base = array('scheme' => $base[2], 'authority' => $base[3], 'path' => $base[5], 'query' => $base[7], 'fragment' => $base[9]);
				$target['scheme'] = $base['scheme'];
				if (!empty($relative['authority']))
				{
					$target = array_merge($relative, $target);
				}
				else
				{
					$target['authority'] = $base['authority'];
					if (!empty($relative['path']))
					{
						if (strpos($relative['path'], '/') === 0)
						{
							$target['path'] = $relative['path'];
						}
						else
						{
							if (!empty($base['path']))
							{
								$target['path'] = dirname("$base[path].") . '/' . $relative['path'];
							}
							else
							{
								$target['path'] = '/' . $relative['path'];
							}
						}
						if (!empty($relative['query']))
						{
							$target['query'] = $relative['query'];
						}
						$input = $target['path'];
						$target['path'] = '';
						while (!empty($input))
						{
							if (strpos($input, '../') === 0)
							{
								$input = substr($input, 3);
							}
							else if (strpos($input, './') === 0)
							{
								$input = substr($input, 2);
							}
							else if (strpos($input, '/./') === 0)
							{
								$input = substr_replace($input, '/', 0, 3);
							}
							else if (strpos($input, '/.') === 0 && SimplePie_Misc::strendpos($input, '/.') === 0)
							{
								$input = substr_replace($input, '/', -2);
							}
							else if (strpos($input, '/../') === 0)
							{
								$input = substr_replace($input, '/', 0, 4);
								$target['path'] = preg_replace('/(\/)?([^\/]+)$/U', '', $target['path']);
							}
							else if (strpos($input, '/..') === 0 && SimplePie_Misc::strendpos($input, '/..') === 0)
							{
								$input = substr_replace($input, '/', 0, 3);
								$target['path'] = preg_replace('/(\/)?([^\/]+)$/U', '', $target['path']);
							}
							else if ($input == '.' || $input == '..')
							{
								$input = '';
							}
							else
							{
								if (preg_match('/^(.+)(\/|$)/U', $input, $match))
								{
									$target['path'] .= $match[1];
									$input = substr_replace($input, '', 0, strlen($match[1]));
								}
							}
						}
					}
					else
					{
						if (!empty($base['path']))
						{
							$target['path'] = $base['path'];
						}
						else
						{
							$target['path'] = '/';
						}
						if (!empty($relative['query']))
						{
							$target['query'] = $relative['query'];
						}
						else if (!empty($base['query']))
						{
							$target['query'] = $base['query'];
						}
					}
				}
				if (!empty($relative['fragment']))
				{
					$target['fragment'] = $relative['fragment'];
				}
			}
			else
			{
				return false;
			}
			$return = '';
			if (!empty($target['scheme']))
			{
				$return .= "$target[scheme]:";
			}
			if (!empty($target['authority']))
			{
				$return .= $target['authority'];
			}
			if (!empty($target['path']))
			{
				$return .= $target['path'];
			}
			if (!empty($target['query']))
			{
				$return .= "?$target[query]";
			}
			if (!empty($target['fragment']))
			{
				$return .= "#$target[fragment]";
			}
		}
		else
		{
			$return = $base;
		}
		return $return;
	}
	
	function strendpos($haystack, $needle)
	{
		return strlen($haystack) - strpos($haystack, $needle) - strlen($needle);
	}
	
	function get_element($realname, $string)
	{
		$return = array();
		$name = preg_quote($realname, '/');
		preg_match_all("/<($name)((\s*((\w+:)?\w+)\s*=\s*(\"([^\"]*)\"|'([^']*)'|(.*)))*)\s*((\/)?>|>(.*)<\/$name>)/msiU", $string, $matches, PREG_SET_ORDER);
		for ($i = 0; $i < count($matches); $i++)
		{
			$return[$i]['tag'] = $realname;
			$return[$i]['full'] = $matches[$i][0];
			if (strlen($matches[$i][10]) <= 2)
			{
				$return[$i]['self_closing'] = true;
			}
			else
			{
				$return[$i]['self_closing'] = false;
				$return[$i]['content'] = $matches[$i][12];
			}
			$return[$i]['attribs'] = array();
			if (!empty($matches[$i][2]))
			{
				preg_match_all('/((\w+:)?\w+)\s*=\s*("([^"]*)"|\'([^\']*)\'|(\S+))\s/msiU', ' ' . $matches[$i][2] . ' ', $attribs, PREG_SET_ORDER);
				for ($j = 0; $j < count($attribs);  $j++)
				{
					$return[$i]['attribs'][strtoupper($attribs[$j][1])]['data'] = $attribs[$j][count($attribs[$j])-1];
					$first = substr($attribs[$j][2], 0, 1);
					$return[$i]['attribs'][strtoupper($attribs[$j][1])]['split'] = ($first == '"' || $first == "'") ? $first : '"';
				}
			}
		}
		return $return;
	}
	
	function element_implode($element)
	{
		$full = "<$element[tag]";
		foreach ($element['attribs'] as $key => $value)
		{
			$key = strtolower($key);
			$full .= " $key=$value[split]$value[data]$value[split]";
		}
		if ($element['self_closing'])
		{
			$full .= ' />';
		}
		else
		{
			$full .= ">$element[content]</$element[tag]>";
		}
		return $full;
	}
	
	function error($message, $level, $file, $line)
	{
		switch ($level)
		{
			case E_USER_ERROR:
				$note = 'PHP Error';
				break;
			case E_USER_WARNING:
				$note = 'PHP Warning';
				break;
			case E_USER_NOTICE:
				$note = 'PHP Notice';
				break;
			default:
				$note = 'Unknown Error';
				break;
		}
		error_log("$note: $message in $file on line $line", 0);
		return $message;
	}
	
	function display_file($url, $timeout = 10, $useragent = null)
	{
		$file = new SimplePie_File($url, $timeout, 5, null, $useragent);
		$headers = $file->headers();
		if ($file->body() !== false)
		{
			header('Content-type: ' . $headers['content-type']);
			echo $file->body();
			exit;
		}
	}
	
	function fix_protocol($url, $http = 1)
	{
		$parsed = SimplePie_Misc::parse_url($url);
		if (!empty($parsed['scheme']) && strtolower($parsed['scheme']) != 'http' && strtolower($parsed['scheme']) != 'https')
		{
			return SimplePie_Misc::fix_protocol("$parsed[authority]$parsed[path]$parsed[query]$parsed[fragment]", $http);
		}
		if (!file_exists($url) && empty($parsed['scheme']))
		{
			return SimplePie_Misc::fix_protocol("http://$url", $http);
		}

		if ($http == 2 && !empty($parsed['scheme']))
		{
			return "feed:$url";
		}
		else if ($http == 3 && strtolower($parsed['scheme']) == 'http')
		{
			return substr_replace($url, 'podcast', 0, 4);
		}
		else
		{
			return $url;
		}
	}
	
	function parse_url($url, $parse_match = true)
	{
		preg_match('/^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/i', $url, $match);
		if (empty($match[0]))
		{
			return false;
		}
		else
		{
			for ($i = 6; $i < 10; $i++)
			{
				if (!isset($match[$i]))
				{
					$match[$i] = '';
				}
			}
			if ($parse_match)
			{
				$match = array('scheme' => $match[2], 'authority' => $match[4], 'path' => $match[5], 'query' => $match[6], 'fragment' => $match[8]);
			}
			return $match;
		}
	}
	
	function change_encoding($data, $input, $output)
	{
		$input = SimplePie_Misc::encoding($input);
		$output = SimplePie_Misc::encoding($output);
		
		if ($input != $output)
		{
			if (function_exists('iconv') && $input['use_iconv'] && $output['use_iconv'] && iconv($input['encoding'], "$output[encoding]//TRANSLIT", $data))
			{
				return iconv($input['encoding'], "$output[encoding]//TRANSLIT", $data);
			}
			else if (function_exists('iconv') && $input['use_iconv'] && $output['use_iconv'] && iconv($input['encoding'], $output['encoding'], $data))
			{
				return iconv($input['encoding'], $output['encoding'], $data);	
			}
			else if (function_exists('mb_convert_encoding') && $input['use_mbstring'] && $output['use_mbstring'])
			{
				return mb_convert_encoding($data, $output['encoding'], $input['encoding']);
			}
			else if ($input['encoding'] == 'ISO-8859-1' && $output['encoding'] == 'UTF-8')
			{
				return utf8_encode($data);
			}
			else if ($input['encoding'] == 'UTF-8' && $output['encoding'] == 'ISO-8859-1')
			{
				return utf8_decode($data);
			}
		}
		return $data;
	}
	
	function encoding($encoding)
	{
		$return['use_mbstring'] = false;
		$return['use_iconv'] = false;
		switch (strtolower($encoding))
		{

			// 7bit
			case '7bit':
			case '7-bit':
				$return['encoding'] = '7bit';
				$return['use_mbstring'] = true;
				break;

			// 8bit
			case '8bit':
			case '8-bit':
				$return['encoding'] = '8bit';
				$return['use_mbstring'] = true;
				break;

			// ARMSCII-8
			case 'armscii-8':
			case 'armscii':
				$return['encoding'] = 'ARMSCII-8';
				$return['use_iconv'] = true;
				break;

			// ASCII
			case 'us-ascii':
			case 'ascii':
				$return['encoding'] = 'US-ASCII';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// BASE64
			case 'base64':
			case 'base-64':
				$return['encoding'] = 'BASE64';
				$return['use_mbstring'] = true;
				break;

			// Big5 - Traditional Chinese, mainly used in Taiwan
			case 'big5':
			case '950':
				$return['encoding'] = 'BIG5';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// Big5 with Hong Kong extensions, Traditional Chinese
			case 'big5-hkscs':
				$return['encoding'] = 'BIG5-HKSCS';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// byte2be
			case 'byte2be':
				$return['encoding'] = 'byte2be';
				$return['use_mbstring'] = true;
				break;

			// byte2le
			case 'byte2le':
				$return['encoding'] = 'byte2le';
				$return['use_mbstring'] = true;
				break;

			// byte4be
			case 'byte4be':
				$return['encoding'] = 'byte4be';
				$return['use_mbstring'] = true;
				break;

			// byte4le
			case 'byte4le':
				$return['encoding'] = 'byte4le';
				$return['use_mbstring'] = true;
				break;

			// EUC-CN
			case 'euc-cn':
			case 'euccn':
				$return['encoding'] = 'EUC-CN';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// EUC-JISX0213
			case 'euc-jisx0213':
			case 'eucjisx0213':
				$return['encoding'] = 'EUC-JISX0213';
				$return['use_iconv'] = true;
				break;

			// EUC-JP
			case 'euc-jp':
			case 'eucjp':
				$return['encoding'] = 'EUC-JP';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// EUCJP-win
			case 'euc-jp-win':
			case 'eucjp-win':
			case 'eucjpwin':
				$return['encoding'] = 'EUCJP-win';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// EUC-KR
			case 'euc-kr':
			case 'euckr':
				$return['encoding'] = 'EUC-KR';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// EUC-TW
			case 'euc-tw':
			case 'euctw':
				$return['encoding'] = 'EUC-TW';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// GB18030 - Simplified Chinese, national standard character set
			case 'gb18030-2000':
			case 'gb18030':
				$return['encoding'] = 'GB18030';
				$return['use_iconv'] = true;
				break;

			// GB2312 - Simplified Chinese, national standard character set
			case 'gb2312':
			case '936':
				$return['encoding'] = 'GB2312';
				$return['use_mbstring'] = true;
				break;

			// GBK
			case 'gbk':
				$return['encoding'] = 'GBK';
				$return['use_iconv'] = true;
				break;

			// Georgian-Academy
			case 'georgian-academy':
				$return['encoding'] = 'Georgian-Academy';
				$return['use_iconv'] = true;
				break;

			// Georgian-PS
			case 'georgian-ps':
				$return['encoding'] = 'Georgian-PS';
				$return['use_iconv'] = true;
				break;

			// HTML-ENTITIES
			case 'html-entities':
			case 'htmlentities':
				$return['encoding'] = 'HTML-ENTITIES';
				$return['use_mbstring'] = true;
				break;

			// HZ
			case 'hz':
				$return['encoding'] = 'HZ';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-2022-CN
			case 'iso-2022-cn':
			case 'iso2022-cn':
			case 'iso2022cn':
				$return['encoding'] = 'ISO-2022-CN';
				$return['use_iconv'] = true;
				break;

			// ISO-2022-CN-EXT
			case 'iso-2022-cn-ext':
			case 'iso2022-cn-ext':
			case 'iso2022cn-ext':
			case 'iso2022cnext':
				$return['encoding'] = 'ISO-2022-CN';
				$return['use_iconv'] = true;
				break;

			// ISO-2022-JP
			case 'iso-2022-jp':
			case 'iso2022-jp':
			case 'iso2022jp':
				$return['encoding'] = 'ISO-2022-JP';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-2022-JP-1
			case 'iso-2022-jp-1':
			case 'iso2022-jp-1':
			case 'iso2022jp-1':
			case 'iso2022jp1':
				$return['encoding'] = 'ISO-2022-JP-1';
				$return['use_iconv'] = true;
				break;

			// ISO-2022-JP-2
			case 'iso-2022-jp-2':
			case 'iso2022-jp-2':
			case 'iso2022jp-2':
			case 'iso2022jp2':
				$return['encoding'] = 'ISO-2022-JP-2';
				$return['use_iconv'] = true;
				break;

			// ISO-2022-JP-3
			case 'iso-2022-jp-3':
			case 'iso2022-jp-3':
			case 'iso2022jp-3':
			case 'iso2022jp3':
				$return['encoding'] = 'ISO-2022-JP-3';
				$return['use_iconv'] = true;
				break;

			// ISO-2022-KR
			case 'iso-2022-kr':
			case 'iso2022-kr':
			case 'iso2022kr':
				$return['encoding'] = 'ISO-2022-KR';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-1
			case 'iso-8859-1':
			case 'iso8859-1':
				$return['encoding'] = 'ISO-8859-1';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-2
			case 'iso-8859-2':
			case 'iso8859-2':
				$return['encoding'] = 'ISO-8859-2';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-3
			case 'iso-8859-3':
			case 'iso8859-3':
				$return['encoding'] = 'ISO-8859-3';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-4
			case 'iso-8859-4':
			case 'iso8859-4':
				$return['encoding'] = 'ISO-8859-4';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-5
			case 'iso-8859-5':
			case 'iso8859-5':
				$return['encoding'] = 'ISO-8859-5';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-6
			case 'iso-8859-6':
			case 'iso8859-6':
				$return['encoding'] = 'ISO-8859-6';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-7
			case 'iso-8859-7':
			case 'iso8859-7':
				$return['encoding'] = 'ISO-8859-7';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-8
			case 'iso-8859-8':
			case 'iso8859-8':
				$return['encoding'] = 'ISO-8859-8';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-9
			case 'iso-8859-9':
			case 'iso8859-9':
				$return['encoding'] = 'ISO-8859-9';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-10
			case 'iso-8859-10':
			case 'iso8859-10':
				$return['encoding'] = 'ISO-8859-10';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// mbstring/iconv functions don't appear to support 11 & 12

			// ISO-8859-13
			case 'iso-8859-13':
			case 'iso8859-13':
				$return['encoding'] = 'ISO-8859-13';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-14
			case 'iso-8859-14':
			case 'iso8859-14':
				$return['encoding'] = 'ISO-8859-14';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-15
			case 'iso-8859-15':
			case 'iso8859-15':
				$return['encoding'] = 'ISO-8859-15';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// ISO-8859-16
			case 'iso-8859-16':
			case 'iso8859-16':
				$return['encoding'] = 'ISO-8859-16';
				$return['use_iconv'] = true;
				break;

			// JIS
			case 'jis':
				$return['encoding'] = 'JIS';
				$return['use_mbstring'] = true;
				break;

			// JOHAB - Korean
			case 'johab':
				$return['encoding'] = 'JOHAB';
				$return['use_iconv'] = true;
				break;

			// Russian
			case 'koi8-r':
			case 'koi8r':
				$return['encoding'] = 'KOI8-R';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// Turkish
			case 'koi8-t':
			case 'koi8t':
				$return['encoding'] = 'KOI8-T';
				$return['use_iconv'] = true;
				break;

			// Ukrainian
			case 'koi8-u':
			case 'koi8u':
				$return['encoding'] = 'KOI8-U';
				$return['use_iconv'] = true;
				break;

			// Russian+Ukrainian
			case 'koi8-ru':
			case 'koi8ru':
				$return['encoding'] = 'KOI8-RU';
				$return['use_iconv'] = true;
				break;

			// Macintosh (Mac OS Classic)
			case 'macintosh':
				$return['encoding'] = 'Macintosh';
				$return['use_iconv'] = true;
				break;

			// MacArabic (Mac OS Classic)
			case 'macarabic':
				$return['encoding'] = 'MacArabic';
				$return['use_iconv'] = true;
				break;

			// MacCentralEurope (Mac OS Classic)
			case 'maccentraleurope':
				$return['encoding'] = 'MacCentralEurope';
				$return['use_iconv'] = true;
				break;

			// MacCroatian (Mac OS Classic)
			case 'maccroatian':
				$return['encoding'] = 'MacCroatian';
				$return['use_iconv'] = true;
				break;

			// MacCyrillic (Mac OS Classic)
			case 'maccyrillic':
				$return['encoding'] = 'MacCyrillic';
				$return['use_iconv'] = true;
				break;

			// MacGreek (Mac OS Classic)
			case 'macgreek':
				$return['encoding'] = 'MacGreek';
				$return['use_iconv'] = true;
				break;

			// MacHebrew (Mac OS Classic)
			case 'machebrew':
				$return['encoding'] = 'MacHebrew';
				$return['use_iconv'] = true;
				break;

			// MacIceland (Mac OS Classic)
			case 'maciceland':
				$return['encoding'] = 'MacIceland';
				$return['use_iconv'] = true;
				break;

			// MacRoman (Mac OS Classic)
			case 'macroman':
				$return['encoding'] = 'MacRoman';
				$return['use_iconv'] = true;
				break;

			// MacRomania (Mac OS Classic)
			case 'macromania':
				$return['encoding'] = 'MacRomania';
				$return['use_iconv'] = true;
				break;

			// MacThai (Mac OS Classic)
			case 'macthai':
				$return['encoding'] = 'MacThai';
				$return['use_iconv'] = true;
				break;

			// MacTurkish (Mac OS Classic)
			case 'macturkish':
				$return['encoding'] = 'MacTurkish';
				$return['use_iconv'] = true;
				break;

			// MacUkraine (Mac OS Classic)
			case 'macukraine':
				$return['encoding'] = 'MacUkraine';
				$return['use_iconv'] = true;
				break;

			// MuleLao-1
			case 'mulelao-1':
			case 'mulelao1':
				$return['encoding'] = 'MuleLao-1';
				$return['use_iconv'] = true;
				break;

			// Shift_JIS
			case 'shift_jis':
			case 'sjis':
			case '932':
				$return['encoding'] = 'Shift_JIS';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// Shift_JISX0213
			case 'shift-jisx0213':
			case 'shiftjisx0213':
				$return['encoding'] = 'Shift_JISX0213';
				$return['use_iconv'] = true;
				break;

			// SJIS-win
			case 'sjis-win':
			case 'sjiswin':
			case 'shift_jis-win':
				$return['encoding'] = 'SJIS-win';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// TCVN - Vietnamese
			case 'tcvn':
				$return['encoding'] = 'TCVN';
				$return['use_iconv'] = true;
				break;

			// TDS565 - Turkish
			case 'tds565':
				$return['encoding'] = 'TDS565';
				$return['use_iconv'] = true;
				break;

			// TIS-620 Thai
			case 'tis-620':
			case 'tis620':
				$return['encoding'] = 'TIS-620';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-2
			case 'ucs-2':
			case 'ucs2':
			case 'utf-16':
			case 'utf16':
				$return['encoding'] = 'UCS-2';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-2BE
			case 'ucs-2be':
			case 'ucs2be':
			case 'utf-16be':
			case 'utf16be':
				$return['encoding'] = 'UCS-2BE';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-2LE
			case 'ucs-2le':
			case 'ucs2le':
			case 'utf-16le':
			case 'utf16le':
				$return['encoding'] = 'UCS-2LE';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-2-INTERNAL
			case 'ucs-2-internal':
			case 'ucs2internal':
				$return['encoding'] = 'UCS-2-INTERNAL';
				$return['use_iconv'] = true;
				break;

			// UCS-4
			case 'ucs-4':
			case 'ucs4':
			case 'utf-32':
			case 'utf32':
				$return['encoding'] = 'UCS-4';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-4BE
			case 'ucs-4be':
			case 'ucs4be':
			case 'utf-32be':
			case 'utf32be':
				$return['encoding'] = 'UCS-4BE';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-4LE
			case 'ucs-4le':
			case 'ucs4le':
			case 'utf-32le':
			case 'utf32le':
				$return['encoding'] = 'UCS-4LE';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-4-INTERNAL
			case 'ucs-4-internal':
			case 'ucs4internal':
				$return['encoding'] = 'UCS-4-INTERNAL';
				$return['use_iconv'] = true;
				break;

			// UCS-16
			case 'ucs-16':
			case 'ucs16':
				$return['encoding'] = 'UCS-16';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-16BE
			case 'ucs-16be':
			case 'ucs16be':
				$return['encoding'] = 'UCS-16BE';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-16LE
			case 'ucs-16le':
			case 'ucs16le':
				$return['encoding'] = 'UCS-16LE';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-32
			case 'ucs-32':
			case 'ucs32':
				$return['encoding'] = 'UCS-32';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-32BE
			case 'ucs-32be':
			case 'ucs32be':
				$return['encoding'] = 'UCS-32BE';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UCS-32LE
			case 'ucs-32le':
			case 'ucs32le':
				$return['encoding'] = 'UCS-32LE';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UTF-7
			case 'utf-7':
			case 'utf7':
				$return['encoding'] = 'UTF-7';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// UTF7-IMAP
			case 'utf-7-imap':
			case 'utf7-imap':
			case 'utf7imap':
				$return['encoding'] = 'UTF7-IMAP';
				$return['use_mbstring'] = true;
				break;

			// VISCII - Vietnamese ASCII
			case 'viscii':
				$return['encoding'] = 'VISCII';
				$return['use_iconv'] = true;
				break;

			// Windows-specific Central & Eastern Europe
			case 'cp1250':
			case 'windows-1250':
			case 'win-1250':
			case '1250':
				$return['encoding'] = 'Windows-1250';
				$return['use_iconv'] = true;
				break;

			// Windows-specific Cyrillic
			case 'cp1251':
			case 'windows-1251':
			case 'win-1251':
			case '1251':
				$return['encoding'] = 'Windows-1251';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// Windows-specific Western Europe
			case 'cp1252':
			case 'windows-1252':
			case '1252':
				$return['encoding'] = 'Windows-1252';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;

			// Windows-specific Greek
			case 'cp1253':
			case 'windows-1253':
			case '1253':
				$return['encoding'] = 'Windows-1253';
				$return['use_iconv'] = true;
				break;

			// Windows-specific Turkish
			case 'cp1254':
			case 'windows-1254':
			case '1254':
				$return['encoding'] = 'Windows-1254';
				$return['use_iconv'] = true;
				break;

			// Windows-specific Hebrew
			case 'cp1255':
			case 'windows-1255':
			case '1255':
				$return['encoding'] = 'Windows-1255';
				$return['use_iconv'] = true;
				break;

			// Windows-specific Arabic
			case 'cp1256':
			case 'windows-1256':
			case '1256':
				$return['encoding'] = 'Windows-1256';
				$return['use_iconv'] = true;
				break;

			// Windows-specific Baltic
			case 'cp1257':
			case 'windows-1257':
			case '1257':
				$return['encoding'] = 'Windows-1257';
				$return['use_iconv'] = true;
				break;

			// Windows-specific Vietnamese
			case 'cp1258':
			case 'windows-1258':
			case '1258':
				$return['encoding'] = 'Windows-1258';
				$return['use_iconv'] = true;
				break;

			// Default to UTF-8
			default:
				$return['encoding'] = 'UTF-8';
				$return['use_iconv'] = true;
				$return['use_mbstring'] = true;
				break;
		}
		
		// Then, return it.
		return $return;
	}
	
	function get_curl_version()
	{
		$curl = 0;
		if (is_array(curl_version()))
		{
			$curl = curl_version();
			$curl = $curl['version'];
		}
		else
		{
			$curl = curl_version();
			$curl = explode(' ', $curl);
			$curl = explode('/', $curl[0]);
			$curl = $curl[1];
		}
		return $curl;
	}
	
	function is_a_class($class1, $class2)
	{
		if (class_exists($class1))
		{
			$classes = array(strtolower($class1));
			while ($class1 = get_parent_class($class1))
			{
				$classes[] = strtolower($class1);
			}
			return in_array(strtolower($class2), $classes);
		}
		else
		{
			return false;
		}
	}
}

class SimplePie_Locator
{
	var $useragent;
	var $timeout;
	var $file;
	var $local;
	var $elsewhere;
	
	function SimplePie_Locator(&$file, $timeout = 10, $useragent = null)
	{
		if (!is_a($file, 'SimplePie_File'))
		{
			$this->file = new SimplePie_File($file, $timeout, $useragent);
		}
		else
		{
			$this->file =& $file;
		}
		$this->useragent = $useragent;
		$this->timeout = $timeout;
	}
		
	
	function find()
	{		
		if ($this->is_feed($this->file))
		{
			return $this->file->url;
		}
		
		$autodiscovery = $this->autodiscovery($this->file);
		if ($autodiscovery)
		{
			return $autodiscovery;
		}
		
		if ($this->get_links($this->file))
		{
			if (!empty($this->local))
			{
				$extension_local = $this->extension($this->local);
				if ($extension_local)
				{
					return $extension_local;
				}
			
				$body_local = $this->body($this->local);
				if ($body_local)
				{
					return $body_local;
				}
			}
			
			if (!empty($this->elsewhere))
			{
				$extension_elsewhere = $this->extension($this->elsewhere);
				if ($extension_elsewhere)
				{
					return $extension_elsewhere;
				}
				
				$body_elsewhere = $this->body($this->elsewhere);
				if ($body_elsewhere)
				{
					return $body_elsewhere;
				}
			}
		}
		return false;
	}
	
	function is_feed(&$file)
	{
		if (!is_a($file, 'SimplePie_File'))
		{
			if (isset($this))
			{
				$file2 = new SimplePie_File($file, $this->timeout, 5, null, $this->useragent);
			}
			else
			{
				$file2 = new SimplePie_File($file);
			}
			$file2->body();
			$file2->close();
		}
		else
		{
			$file2 =& $file;
		}
		$body = preg_replace('/<\!-(.*)-\>/msiU', '', $file2->body());
		if (preg_match('/<(\w+\:)?rss/msiU', $body) || preg_match('/<(\w+\:)?RDF/mi', $body) || preg_match('/<(\w+\:)?feed/mi', $body))
		{
			return true;
		}
		return false;
	}
	
	function autodiscovery(&$file)
	{
		$links = SimplePie_Misc::get_element('link', $file->body());
		$done = array();
		foreach ($links as $link)
		{
			if (!empty($link['attribs']['TYPE']['data']) && !empty($link['attribs']['HREF']['data']))
			{
				$type = strtolower(trim($link['attribs']['TYPE']['data']));
				$href = SimplePie_Misc::absolutize_url(trim($link['attribs']['HREF']['data']), $this->file->url);
				if (!in_array($href, $done) && in_array($type, array('application/rss+xml', 'application/atom+xml', 'application/rdf+xml', 'application/xml+rss', 'application/xml+atom', 'application/xml+rdf', 'application/xml', 'application/x.atom+xml', 'text/xml')))
				{
					$feed = $this->is_feed($href);
					if ($feed)
					{
						return $href;
					}
				}
				$done[] = $href;
			}
		}
		return false;
	}
	
	function get_links(&$file)
	{
		$links = SimplePie_Misc::get_element('a', $file->body());
		foreach ($links as $link)
		{
			if (!empty($link['attribs']['HREF']['data']))
			{
				$href = trim($link['attribs']['HREF']['data']);
				$parsed = SimplePie_Misc::parse_url($href);
				if (empty($parsed['scheme']) || $parsed['scheme'] != 'javascript')
				{
					$current = SimplePie_Misc::parse_url($this->file->url);
					if (empty($parsed['authority']) || $parsed['authority'] == $current['authority'])
					{
						$this->local[] = SimplePie_Misc::absolutize_url($href, $this->file->url);
					}
					else
					{
						$this->elsewhere[] = SimplePie_Misc::absolutize_url($href, $this->file->url);
					}
				}
			}
		}
		if (!empty($this->local))
		{
			$this->local = array_unique($this->local);
		}
		if (!empty($this->elsewhere))
		{
			$this->elsewhere = array_unique($this->elsewhere);
		}
		if (!empty($this->local) || !empty($this->elsewhere))
		{
			return true;
		}
		return false;
	}
	
	function extension(&$array)
	{
		foreach ($array as $key => $value)
		{
			$value = SimplePie_Misc::absolutize_url($value, $this->file->url);
			if (in_array(strrchr($value, '.'), array('.rss', '.rdf', '.atom', '.xml')))
			{
				if ($this->is_feed($value))
				{
					return $value;
				}
				else
				{
					unset($array[$key]);
				}
			}
		}
		return false;
	}
	
	function body(&$array)
	{
		foreach ($array as $key => $value)
		{
			$value = SimplePie_Misc::absolutize_url($value, $this->file->url);
			if (preg_match('/(rss|rdf|atom|xml)/i', $value))
			{
				if ($this->is_feed($value))
				{
					return $value;
				}
				else
				{
					unset($array[$key]);
				}
			}
		}
		return false;
	}
}

class SimplePie_Parser
{	
	var $encoding;
	var $data;
	var $namespaces = array('xml' => 'HTTP://WWW.W3.ORG/XML/1998/NAMESPACE', 'atom' => 'ATOM', 'rss2' => 'RSS', 'rdf' => 'RDF', 'rss1' => 'RSS', 'dc' => 'DC', 'xhtml' => 'XHTML', 'content' => 'CONTENT');
	var $xml;
	var $error_code;
	var $error_string;
	var $current_line;
	var $current_column;
	var $current_byte;
	var $tag_name;
	var $inside_item;
	var $item_number = 0;
	var $inside_channel;
	var $author_number= 0;
	var $category_number = 0;
	var $enclosure_number = 0;
	var $link_number = 0;
	var $item_link_number = 0;
	var $inside_image;
	var $attribs;
	var $is_first;
	var $inside_author;
	
		
	function SimplePie_Parser($data, $encoding, $return_xml = false)
	{
		$this->encoding = $encoding;
		
		// Strip BOM:
		// UTF-32 Big Endian BOM
		if (strpos($data, sprintf('%c%c%c%c', 0x00, 0x00, 0xFE, 0xFF)) === 0)
		{
			$data = substr($data, 4);
		}
		// UTF-32 Little Endian BOM
		else if (strpos($data, sprintf('%c%c%c%c', 0xFF, 0xFE, 0x00, 0x00)) === 0)
		{
			$data = substr($data, 4);
		}
		// UTF-16 Big Endian BOM
		else if (strpos($data, sprintf('%c%c', 0xFE, 0xFF)) === 0)
		{
			$data = substr($data, 2);
		}
		// UTF-16 Little Endian BOM
		else if (strpos($data, sprintf('%c%c', 0xFF, 0xFE)) === 0)
		{
			$data = substr($data, 2);
		}
		// UTF-8 BOM
		else if (strpos($data, sprintf('%c%c%c', 0xEF, 0xBB, 0xBF)) === 0)
		{
			$data = substr($data, 3);
		}
		
		// Make sure the XML prolog is sane and has the correct encoding
		if (preg_match('/^<\?xml(.*)?>/msiU', $data, $prolog))
		{
			$data = substr_replace($data, '', 0, strlen($prolog[0]));
		}
		$data = "<?xml version='1.0' encoding='$encoding'?>\n" . $data;
		
		// Add an internal attribute to CDATA sections
		$data = preg_replace_callback('/<(\S+)((\s*((\w+:)?\w+)\s*=\s*("([^"]*)"|\'([^\']*)\'))*)\s*(\/>|>\s*<\!\[CDATA\[(.*)<\/\\1>)/msiU', array(&$this, 'spencoded'), $data);
		
		// Put some data into CDATA blocks
		// If we're RSS
		if ((stristr($data, '<rss') || preg_match('/<([a-z0-9]+\:)?RDF/mi', $data)) && (preg_match('/<([a-z0-9]+\:)?channel/mi', $data) || preg_match('/<([a-z0-9]+\:)?item/mi', $data)))
		{
			$sp_elements = array(
				'author',
				'description',
				'link',
				'title',
			);
		}
		// Or if we're Atom
		else
		{
			$sp_elements = array(
				'content',
				'copyright',
				'name',
				'subtitle',
				'summary',
				'tagline',
				'title',
			);
		}
		foreach ($sp_elements as $full)
		{
			$data = preg_replace_callback("/<($full)((\s*((\w+:)?\w+)\s*=\s*(\"([^\"]*)\"|'([^']*)'))*)\s*(\/>|>(.*)<\/$full>)/msiU", array(&$this, 'add_cdata'), $data);
		}
		foreach ($sp_elements as $full)
		{
			// Deal with CDATA within CDATA (this can be caused by us inserting CDATA above)
			$data = preg_replace_callback("/<($full)((\s*((\w+:)?\w+)\s*=\s*(\"([^\"]*)\"|'([^']*)'))*)\s*(\/>|><!\[CDATA\[(.*)\]\]><\/$full>)/msiU", array(&$this, 'cdata_in_cdata'), $data);
		}
		
		// Return the XML, if so desired
		if ($return_xml)
		{
			$this->data =& $data;
			return;
		}
		
		// Create the parser
		$this->xml = xml_parser_create_ns($encoding);
		xml_parser_set_option($this->xml, XML_OPTION_SKIP_WHITE, 1);
		xml_set_object($this->xml, $this);
		xml_set_character_data_handler($this->xml, 'data_handler');
		xml_set_element_handler($this->xml, 'start_handler', 'end_handler');
		xml_set_start_namespace_decl_handler($this->xml, 'start_name_space');
		xml_set_end_namespace_decl_handler($this->xml, 'end_name_space');
		
		// Parse!
		if (!xml_parse($this->xml, $data))
		{
			$this->data = null;
			$this->error_code = xml_get_error_code($this->xml);
			$this->error_string = xml_error_string($this->error_code);
		}
		$this->current_line = xml_get_current_line_number($this->xml);
		$this->current_column = xml_get_current_column_number($this->xml);
		$this->current_byte = xml_get_current_byte_index($this->xml);
		xml_parser_free($this->xml);
		return;
	}
	
	function add_cdata($match)
	{
		if (isset($match[10]))
		{
			$match[10] = preg_replace('/^\s*<\!\[CDATA\[(.*)]]>\s*$/msiU', '\\1', $match[10]);
			return "<$match[1]$match[2]><![CDATA[$match[10]]]></$match[1]>";
		}
		return $match[0];
	}
	
	function spencoded($match)
	{
		if (isset($match[10]))
		{
			return "<$match[1]$match[2] spencoded=\"false\"><![CDATA[$match[10]</$match[1]>";
		}
		return $match[0];
	}

	function cdata_in_cdata($match)
	{
		if (isset($match[10]))
		{
			$match[10] = preg_replace_callback('/<!\[CDATA\[(.*)\]\]>/msiU', array(&$this, 'real_cdata_in_cdata'), $match[10]);
			return "<$match[1]$match[2]><![CDATA[$match[10]]]></$match[1]>";
		}
		return $match[0];
	}
	
	function real_cdata_in_cdata($match)
	{
		return htmlentities($match[1], ENT_NOQUOTES, $this->encoding);
	}
	
	function do_add_content(&$array, $data)
	{
		if ($this->is_first)
		{
			$array['data'] = $data;
			$array['attribs'] = $this->attribs;
		}
		else
		{
			$array['data'] .= $data;
		}
	}
	
	function start_handler($parser, $name, $attribs)
	{
		$this->tag_name = $name;
		$this->attribs = $attribs;
		$this->is_first = true;
		switch ($this->tag_name)
		{
			case 'ITEM':
			case $this->namespaces['rss2'] . ':ITEM':
			case $this->namespaces['rss1'] . ':ITEM':
			case 'ENTRY':
			case $this->namespaces['atom'] . ':ENTRY':
				$this->inside_item = true;
				$this->do_add_content($this->data['items'][$this->item_number], '');
				break;

			case 'CHANNEL':
			case $this->namespaces['rss2'] . ':CHANNEL':
			case $this->namespaces['rss1'] . ':CHANNEL':
				$this->inside_channel = true;
				break;

			case 'RSS':
			case $this->namespaces['rss2'] . ':RSS':
				$this->data['feedinfo']['type'] = 'RSS';
				$this->do_add_content($this->data['feeddata'], '');
				if (!empty($attribs['VERSION']))
				{
					$this->data['feedinfo']['version'] = trim($attribs['VERSION']);
				}
				break;

			case $this->namespaces['rdf'] . ':RDF':
				$this->data['feedinfo']['type'] = 'RSS';
				$this->do_add_content($this->data['feeddata'], '');
				$this->data['feedinfo']['version'] = 1;
				break;

			case 'FEED':
			case $this->namespaces['atom'] . ':FEED':
				$this->data['feedinfo']['type'] = 'Atom';
				$this->do_add_content($this->data['feeddata'], '');
				if (!empty($attribs['VERSION']))
				{
					$this->data['feedinfo']['version'] = trim($attribs['VERSION']);
				}
				break;

			case 'IMAGE':
			case $this->namespaces['rss2'] . ':IMAGE':
			case $this->namespaces['rss1'] . ':IMAGE':
				if ($this->inside_channel)
				{
					$this->inside_image = true;
				}
				break;
		}

		if (!empty($this->data['feedinfo']['type']) && $this->data['feedinfo']['type'] == 'Atom' && ($this->tag_name == 'AUTHOR' || $this->tag_name == $this->namespaces['atom'] . ':AUTHOR'))
		{
			$this->inside_author = true;
		}
		$this->data_handler($this->xml, '');
	}

	function data_handler($parser, $data)
	{
		if ($this->inside_item)
		{
			switch ($this->tag_name)
			{
				case 'TITLE':
				case $this->namespaces['rss1'] . ':TITLE':
				case $this->namespaces['rss2'] . ':TITLE':
				case $this->namespaces['atom'] . ':TITLE':
					$this->do_add_content($this->data['items'][$this->item_number]['title'], $data);
					break;
					
				case $this->namespaces['dc'] . ':TITLE':
					$this->do_add_content($this->data['items'][$this->item_number]['dc:title'], $data);
					break;

				case 'CONTENT':
				case $this->namespaces['atom'] . ':CONTENT':
					$this->do_add_content($this->data['items'][$this->item_number]['content'], $data);
					break;

				case $this->namespaces['content'] . ':ENCODED':
					$this->do_add_content($this->data['items'][$this->item_number]['encoded'], $data);
					break;

				case 'SUMMARY':
				case $this->namespaces['atom'] . ':SUMMARY':
					$this->do_add_content($this->data['items'][$this->item_number]['summary'], $data);
					break;

				case 'LONGDESC':
					$this->do_add_content($this->data['items'][$this->item_number]['longdesc'], $data);
					break;

				case 'DESCRIPTION':
				case $this->namespaces['rss1'] . ':DESCRIPTION':
				case $this->namespaces['rss2'] . ':DESCRIPTION':
					$this->do_add_content($this->data['items'][$this->item_number]['description'], $data);
					break;

				case $this->namespaces['dc'] . ':DESCRIPTION':
					$this->do_add_content($this->data['items'][$this->item_number]['dc:description'], $data);
					break;

				case 'LINK':
				case $this->namespaces['rss1'] . ':LINK':
				case $this->namespaces['rss2'] . ':LINK':
				case $this->namespaces['atom'] . ':LINK':
					$this->do_add_content($this->data['items'][$this->item_number]['link'][$this->item_link_number], $data);
					break;
					
				case 'ENCLOSURE':
				case $this->namespaces['rss1'] . ':ENCLOSURE':
				case $this->namespaces['rss2'] . ':ENCLOSURE':
				case $this->namespaces['atom'] . ':ENCLOSURE':
					$this->do_add_content($this->data['items'][$this->item_number]['enclosure'][$this->enclosure_number], $data);
					break;

				case 'GUID':
				case $this->namespaces['rss1'] . ':GUID':
				case $this->namespaces['rss2'] . ':GUID':
					$this->do_add_content($this->data['items'][$this->item_number]['guid'], $data);
					break;

				case 'ID':
				case $this->namespaces['atom'] . ':ID':
					$this->do_add_content($this->data['items'][$this->item_number]['id'], $data);
					break;

				case 'PUBDATE':
				case $this->namespaces['rss1'] . ':PUBDATE':
				case $this->namespaces['rss2'] . ':PUBDATE':
					$this->do_add_content($this->data['items'][$this->item_number]['pubdate'], $data);
					break;

				case $this->namespaces['dc'] . ':DATE':
					$this->do_add_content($this->data['items'][$this->item_number]['dc:date'], $data);
					break;

				case 'ISSUED':
				case $this->namespaces['atom'] . ':ISSUED':
					$this->do_add_content($this->data['items'][$this->item_number]['issued'], $data);
					break;

				case 'PUBLISHED':
				case $this->namespaces['atom'] . ':PUBLISHED':
					$this->do_add_content($this->data['items'][$this->item_number]['published'], $data);
					break;

				case 'MODIFIED':
				case $this->namespaces['atom'] . ':MODIFIED':
					$this->do_add_content($this->data['items'][$this->item_number]['modified'], $data);
					break;

				case 'UPDATED':
				case $this->namespaces['atom'] . ':UPDATED':
					$this->do_add_content($this->data['items'][$this->item_number]['updated'], $data);
					break;
	
				case 'CATEGORY':
				case $this->namespaces['rss1'] . ':CATEGORY':
				case $this->namespaces['rss2'] . ':CATEGORY':
				case $this->namespaces['atom'] . ':CATEGORY':
					$this->do_add_content($this->data['items'][$this->item_number]['category'][$this->category_number], $data);
					break;

				case $this->namespaces['dc'] . ':SUBJECT':
					$this->do_add_content($this->data['items'][$this->item_number]['subject'][$this->category_number], $data);
					break;

				case $this->namespaces['dc'] . ':CREATOR':
					$this->do_add_content($this->data['items'][$this->item_number]['creator'][$this->author_number], $data);
					break;

				case 'AUTHOR':
				case $this->namespaces['rss1'] . ':AUTHOR':
				case $this->namespaces['rss2'] . ':AUTHOR':
					$this->do_add_content($this->data['items'][$this->item_number]['author'][$this->author_number]['rss'], $data);
					break;
			}

			if ($this->inside_author)
			{
				switch ($this->tag_name)
				{
					case 'NAME':
					case $this->namespaces['atom'] . ':NAME':
						$this->do_add_content($this->data['items'][$this->item_number]['author'][$this->author_number]['name'], $data);
						break;

					case 'URL':
					case $this->namespaces['atom'] . ':URL':
						$this->do_add_content($this->data['items'][$this->item_number]['author'][$this->author_number]['url'], $data);
						break;

					case 'URI':
					case $this->namespaces['atom'] . ':URI':
						$this->do_add_content($this->data['items'][$this->item_number]['author'][$this->author_number]['uri'], $data);
						break;

					case 'HOMEPAGE':
					case $this->namespaces['atom'] . ':HOMEPAGE':
						$this->do_add_content($this->data['items'][$this->item_number]['author'][$this->author_number]['homepage'], $data);
						break;

					case 'EMAIL':
					case $this->namespaces['atom'] . ':EMAIL':
						$this->do_add_content($this->data['items'][$this->item_number]['author'][$this->author_number]['email'], $data);
						break;
				}
			}
		}

		else if (($this->inside_channel && !$this->inside_image) || (isset($this->data['feedinfo']['type']) && $this->data['feedinfo']['type'] == 'Atom'))
		{
			switch ($this->tag_name)
			{
				case 'TITLE':
				case $this->namespaces['rss1'] . ':TITLE':
				case $this->namespaces['rss2'] . ':TITLE':
				case $this->namespaces['atom'] . ':TITLE':
					$this->do_add_content($this->data['info']['title'], $data);
					break;

				case 'LINK':
				case $this->namespaces['rss1'] . ':LINK':
				case $this->namespaces['rss2'] . ':LINK':
				case $this->namespaces['atom'] . ':LINK':
					$this->do_add_content($this->data['info']['link'][$this->link_number], $data);
					break;

				case 'DESCRIPTION':
				case $this->namespaces['rss1'] . ':DESCRIPTION':
				case $this->namespaces['rss2'] . ':DESCRIPTION':
					$this->do_add_content($this->data['info']['description'], $data);
					break;

				case $this->namespaces['dc'] . ':DESCRIPTION':
					$this->do_add_content($this->data['info']['dc:description'], $data);
					break;

				case 'TAGLINE':
				case $this->namespaces['atom'] . ':TAGLINE':
					$this->do_add_content($this->data['info']['tagline'], $data);
					break;

				case 'SUBTITLE':
				case $this->namespaces['atom'] . ':SUBTITLE':
					$this->do_add_content($this->data['info']['subtitle'], $data);
					break;

				case 'COPYRIGHT':
				case $this->namespaces['rss1'] . ':COPYRIGHT':
				case $this->namespaces['rss2'] . ':COPYRIGHT':
				case $this->namespaces['atom'] . ':COPYRIGHT':
					$this->do_add_content($this->data['info']['copyright'], $data);
					break;

				case 'LANGUAGE':
				case $this->namespaces['rss1'] . ':LANGUAGE':
				case $this->namespaces['rss2'] . ':LANGUAGE':
					$this->do_add_content($this->data['info']['language'], $data);
					break;
				
				case 'LOGO':
				case $this->namespaces['atom'] . ':LOGO':
					$this->do_add_content($this->data['info']['logo'], $data);
					break;
				
			}
		}

		else if ($this->inside_channel && $this->inside_image)
		{
			switch ($this->tag_name)
			{
				case 'TITLE':
				case $this->namespaces['rss1'] . ':TITLE':
				case $this->namespaces['rss2'] . ':TITLE':
					$this->do_add_content($this->data['info']['image']['title'], $data);
					break;

				case 'URL':
				case $this->namespaces['rss1'] . ':URL':
				case $this->namespaces['rss2'] . ':URL':
					$this->do_add_content($this->data['info']['image']['url'], $data);
					break;

				case 'LINK':
				case $this->namespaces['rss1'] . ':LINK':
				case $this->namespaces['rss2'] . ':LINK':
					$this->do_add_content($this->data['info']['image']['link'], $data);
					break;

				case 'WIDTH':
				case $this->namespaces['rss1'] . ':WIDTH':
				case $this->namespaces['rss2'] . ':WIDTH':
					$this->do_add_content($this->data['info']['image']['width'], $data);
					break;

				case 'HEIGHT':
				case $this->namespaces['rss1'] . ':HEIGHT':
				case $this->namespaces['rss2'] . ':HEIGHT':
					$this->do_add_content($this->data['info']['image']['height'], $data);
					break;
			}
		}
		$this->is_first = false;
	}

	function end_handler($parser, $name)
	{
		$this->tag_name = '';
		switch ($name)
		{
			case 'ITEM':
			case $this->namespaces['rss1'] . ':ITEM':
			case $this->namespaces['rss2'] . ':ITEM':
			case 'ENTRY':
			case $this->namespaces['atom'] . ':ENTRY':
				$this->inside_item = false;
				$this->item_number++;
				$this->author_number = 0;
				$this->category_number = 0;
				$this->enclosure_number = 0;
				$this->item_link_number = 0;
				break;

			case 'CHANNEL':
			case $this->namespaces['rss1'] . ':CHANNEL':
			case $this->namespaces['rss2'] . ':CHANNEL':
				$this->inside_channel = false;
				break;

			case 'IMAGE':
			case $this->namespaces['rss1'] . ':IMAGE':
			case $this->namespaces['rss2'] . ':IMAGE':
				$this->inside_image = false;
				break;

			case 'AUTHOR':
			case $this->namespaces['rss1'] . ':AUTHOR':
			case $this->namespaces['rss2'] . ':AUTHOR':
			case $this->namespaces['atom'] . ':AUTHOR':
				$this->author_number++;
				$this->inside_author = false;
				break;

			case 'CATEGORY':
			case $this->namespaces['rss1'] . ':CATEGORY':
			case $this->namespaces['rss2'] . ':CATEGORY':
			case $this->namespaces['atom'] . ':CATEGORY':
			case $this->namespaces['dc'] . ':SUBJECT':
				$this->category_number++;
				break;
			
			case 'ENCLOSURE':
			case $this->namespaces['rss1'] . ':ENCLOSURE':
			case $this->namespaces['rss2'] . ':ENCLOSURE':
				$this->enclosure_number++;
				break;
				
			case 'LINK':
			case $this->namespaces['rss1'] . ':LINK':
			case $this->namespaces['rss2'] . ':LINK':
			case $this->namespaces['atom'] . ':LINK':
				if ($this->inside_item)
				{
					$this->item_link_number++;
				}
				else
				{
					$this->link_number++;
				}
				break;
		}
	}
	
	function start_name_space($parser, $prefix, $uri = null)
	{
		$prefix = strtoupper($prefix);
		$uri = strtoupper($uri);
		if ($prefix == 'ATOM' || $uri == 'HTTP://WWW.W3.ORG/2005/ATOM' || $uri == 'HTTP://PURL.ORG/ATOM/NS#')
		{
			$this->namespaces['atom'] = $uri;
		}
		else if ($prefix == 'RSS2' || $uri == 'HTTP://BACKEND.USERLAND.COM/RSS2')
		{
			$this->namespaces['rss2'] = $uri;
		}
		else if ($prefix == 'RDF' || $uri == 'HTTP://WWW.W3.ORG/1999/02/22-RDF-SYNTAX-NS#')
		{
			$this->namespaces['rdf'] = $uri;
		}
		else if ($prefix == 'RSS' || $uri == 'HTTP://PURL.ORG/RSS/1.0/' || $uri == 'HTTP://MY.NETSCAPE.COM/RDF/SIMPLE/0.9/')
		{
			$this->namespaces['rss1'] = $uri;
		}
		else if ($prefix == 'DC' || $uri == 'HTTP://PURL.ORG/DC/ELEMENTS/1.1/')
		{
			$this->namespaces['dc'] = $uri;
		}
		else if ($prefix == 'XHTML' || $uri == 'HTTP://WWW.W3.ORG/1999/XHTML')
		{
			$this->namespaces['xhtml'] = $uri;
			$this->xhtml_prefix = $prefix;
		}
		else if ($prefix == 'CONTENT' || $uri == 'HTTP://PURL.ORG/RSS/1.0/MODULES/CONTENT/')
		{
			$this->namespaces['content'] = $uri;
		}
	}
	
	function end_name_space($parser, $prefix)
	{
		if ($key = array_search(strtoupper($prefix), $this->namespaces))
		{
			if ($key == 'atom')
			{
				$this->namespaces['atom'] = 'ATOM';
			}
			else if ($key == 'rss2')
			{
				$this->namespaces['rss2'] = 'RSS';
			}
			else if ($key == 'rdf')
			{
				$this->namespaces['rdf'] = 'RDF';
			}
			else if ($key == 'rss1')
			{
				$this->namespaces['rss1'] = 'RSS';
			}
			else if ($key == 'dc')
			{
				$this->namespaces['dc'] = 'DC';
			}
			else if ($key == 'xhtml')
			{
				$this->namespaces['xhtml'] = 'XHTML';
				$this->xhtml_prefix = 'XHTML';
			}
			else if ($key == 'content')
			{
				$this->namespaces['content'] = 'CONTENT';
			}
		}
	}
}

class SimplePie_Sanitize
{
	// Private vars
	var $feedinfo;
	var $info;
	var $items;
	var $feed_xmlbase;
	var $item_xmlbase;
	var $attribs;
	var $cached_entities;
	
	// Options
	var $remove_div = true;
	var $strip_ads = false;
	var $replace_headers = false;
	var $bypass_image_hotlink = false;
	var $bypass_image_hotlink_page = false;
	var $strip_htmltags = array('base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style');
	var $encode_instead_of_strip = false;
	var $strip_attributes = array('bgsound', 'class', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur');
	var $input_encoding = 'UTF-8';
	var $output_encoding = 'UTF-8';
	var $item_class = 'SimplePie_Item';
	var $author_class = 'SimplePie_Author';
	var $enclosure_class = 'SimplePie_Enclosure';
	
	function remove_div($enable = true)
	{
		$this->remove_div = (bool) $enable;
	}
	
	function strip_ads($enable = false)
	{
		$this->strip_ads = (bool) $enable;
	}
	
	function replace_headers($enable = false)
	{
		$this->enable_headers = (bool) $enable;
	}
	
	function bypass_image_hotlink($get = false)
	{
		if ($get)
		{
			$this->bypass_image_hotlink = (string) $get;
		}
		else
		{
			$this->bypass_image_hotlink = false;
		}
	}
	
	function bypass_image_hotlink_page($page = false)
	{
		if ($page)
		{
			$this->bypass_image_hotlink_page = (string) $page;
		}
		else
		{
			$this->bypass_image_hotlink_page = false;
		}
	}
	
	function strip_htmltags($tags = array('base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style'))
	{
		if ($tags)
		{
			if (is_array($tags))
			{
				$this->strip_htmltags = $tags;
			}
			else
			{
				$this->strip_htmltags = explode(',', $tags);
			}
		}
		else
		{
			$this->strip_htmltags = false;
		}
	}
	
	function encode_instead_of_strip($enable = false)
	{
		$this->encode_instead_of_strip = (bool) $enable;
	}
	
	function strip_attributes($attribs = array('bgsound', 'class', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur'))
	{
		if ($attribs)
		{
			if (is_array($attribs))
			{
				$this->strip_attributes = $attribs;
			}
			else
			{
				$this->strip_attributes = explode(',', $attribs);
			}
		}
		else
		{
			$this->strip_attributes = false;
		}
	}
	
	function input_encoding($encoding = 'UTF-8')
	{
		$this->input_encoding = (string) $encoding;
	}
	
	function output_encoding($encoding = 'UTF-8')
	{
		$this->output_encoding = (string) $encoding;
	}
	
	function set_item_class($class = 'SimplePie_Item')
	{
		if (SimplePie_Misc::is_a_class($class, 'SimplePie_Item'))
		{
			$this->item_class = $class;
			return true;
		}
		return false;
	}
	
	function set_author_class($class = 'SimplePie_Author')
	{
		if (SimplePie_Misc::is_a_class($class, 'SimplePie_Author'))
		{
			$this->author_class = $class;
			return true;
		}
		return false;
	}
	
	function set_enclosure_class($class = 'SimplePie_Enclosure')
	{
		if (SimplePie_Misc::is_a_class($class, 'SimplePie_Enclosure'))
		{
			$this->enclosure_class = $class;
			return true;
		}
		return false;
	}
	
	function parse_data_array(&$data, $url)
	{		
		// Feed Info (Type and Version)
		if (!empty($data['feedinfo']['type']))
		{
			$this->feedinfo = $data['feedinfo'];
		}
		
		// Feed level xml:base
		if (!empty($data['feeddata']['attribs']['XML:BASE']))
		{
			$this->feed_xmlbase = $data['feeddata']['attribs']['XML:BASE'];
		}
		else if (!empty($data['feeddata']['attribs']['HTTP://WWW.W3.ORG/XML/1998/NAMESPACE:BASE']))
		{
			$this->feed_xmlbase = $data['feeddata']['attribs']['HTTP://WWW.W3.ORG/XML/1998/NAMESPACE:BASE'];
		}
		// FeedBurner feeds use alternate link
		else if (strpos($url, 'http://feeds.feedburner.com/') !== 0)
		{
			$this->feed_xmlbase = SimplePie_Misc::parse_url($url);
			if (empty($this->feed_xmlbase['authority']))
			{
				$this->feed_xmlbase = preg_replace('/^' . preg_quote(realpath($_SERVER['DOCUMENT_ROOT']), '/') . '/', '', realpath($url));
			}
			else
			{
				$this->feed_xmlbase = $url;
			}
		}
		
		
		// Feed link(s)
		if (!empty($data['info']['link']))
		{
			foreach ($data['info']['link'] as $link)
			{
				if (empty($link['attribs']['REL']))
				{
					$rel = 'alternate';
				}
				else
				{
					$rel = strtolower($link['attribs']['REL']);
				}
				if ($rel == 'enclosure')
				{
					$href = null;
					$type = null;
					$length = null;
					if (!empty($link['data']))
					{
						$href = $this->sanitize($link['data'], $link['attribs'], true);
					}
					else if (!empty($link['attribs']['HREF']))
					{
						$href = $this->sanitize($link['attribs']['HREF'], $link['attribs'], true);
					}
					if (!empty($link['attribs']['TYPE'])) {
						$type = $this->sanitize($link['attribs']['TYPE'], $link['attribs']);
					}
					if (!empty($link['attribs']['LENGTH'])) {
						$length = $this->sanitize($link['attribs']['LENGTH'], $link['attribs']);
					}
					$this->info['link']['enclosure'][] = new $this->enclosure_class($href, $type, $length);
				}
				else
				{
					if (!empty($link['data']))
					{
						$this->info['link'][$rel][] = $this->sanitize($link['data'], $link['attribs'], true);
					}
					else if (!empty($link['attribs']['HREF']))
					{
						$this->info['link'][$rel][] = $this->sanitize($link['attribs']['HREF'], $link['attribs'], true);
					}
				}
			}
		}
		
		// Use the first alternate link if we don't have any feed xml:base
		if (empty($this->feed_xmlbase) && !empty($this->info['link']['alternate'][0]))
		{
			$this->feed_xmlbase = $this->info['link']['alternate'][0];
		}
		
		// Feed Title
		if (!empty($data['info']['title']['data']))
		{
			$this->info['title'] = $this->sanitize($data['info']['title']['data'], $data['info']['title']['attribs']);
		}
		
		// Feed Descriptions
		if (!empty($data['info']['description']['data']))
		{
			$this->info['description'] = $this->sanitize($data['info']['description']['data'], $data['info']['description']['attribs']);
		}
		if (!empty($data['info']['dc:description']['data']))
		{
			$this->info['dc:description'] = $this->sanitize($data['info']['dc:description']['data'], $data['info']['dc:description']['attribs']);
		}
		if (!empty($data['info']['tagline']['data']))
		{
			$this->info['tagline'] = $this->sanitize($data['info']['tagline']['data'], $data['info']['tagline']['attribs']);
		}
		if (!empty($data['info']['subtitle']['data']))
		{
			$this->info['subtitle'] = $this->sanitize($data['info']['subtitle']['data'], $data['info']['subtitle']['attribs']);
		}
		
		// Feed Language
		if (!empty($data['info']['language']['data']))
		{
			$this->info['language'] = $this->sanitize($data['info']['language']['data'], $data['info']['language']['attribs']);
		}
		if (!empty($data['feeddata']['attribs']['XML:LANG']))
		{
			$this->info['xml:lang'] = $this->sanitize($data['feeddata']['attribs']['XML:LANG'], null);
		}
		else if (!empty($data['feeddata']['attribs']['HTTP://WWW.W3.ORG/XML/1998/NAMESPACE:LANG']))
		{
			$this->info['xml:lang'] = $this->sanitize($data['feeddata']['attribs']['HTTP://WWW.W3.ORG/XML/1998/NAMESPACE:LANG'], null);
		}
		
		// Feed Copyright
		if (!empty($data['info']['copyright']['data']))
		{
			$this->info['copyright'] = $this->sanitize($data['info']['copyright']['data'], $data['info']['copyright']['attribs']);
		}
		
		// Feed Image
		if (!empty($data['info']['image']['title']['data']))
		{
			$this->info['image']['title'] = $this->sanitize($data['info']['image']['title']['data'], $data['info']['image']['title']['attribs']);
		}
		if (!empty($data['info']['image']['url']['data']))
		{
			$this->info['image']['url'] = $this->sanitize($data['info']['image']['url']['data'], $data['info']['image']['url']['attribs'], true);
		}
		if (!empty($data['info']['logo']['data']))
		{
			$this->info['image']['logo'] = $this->sanitize($data['info']['logo']['data'], $data['info']['logo']['attribs'], true);
		}
		if (!empty($data['info']['image']['link']['data']))
		{
			$this->info['image']['link'] = $this->sanitize($data['info']['image']['link']['data'], $data['info']['image']['link']['attribs'], true);
		}
		if (!empty($data['info']['image']['width']['data']))
		{
			$this->info['image']['width'] = $this->sanitize($data['info']['image']['width']['data'], $data['info']['image']['width']['attribs']);
		}
		if (!empty($data['info']['image']['height']['data']))
		{
			$this->info['image']['height'] = $this->sanitize($data['info']['image']['height']['data'], $data['info']['image']['height']['attribs']);
		}
		
		// Items
		if (!empty($data['items']))
		{
			foreach ($data['items'] as $key => $item)
			{
				$newitem = null;
				
				// Item level xml:base
				if (!empty($item['attribs']['XML:BASE']))
				{
					$this->item_xmlbase = SimplePie_Misc::absolutize_url($item['attribs']['XML:BASE'], $this->feed_xmlbase);
				}
				else if (!empty($item['attribs']['HTTP://WWW.W3.ORG/XML/1998/NAMESPACE:BASE']))
				{
					$this->item_xmlbase = SimplePie_Misc::absolutize_url($item['attribs']['HTTP://WWW.W3.ORG/XML/1998/NAMESPACE:BASE'], $this->feed_xmlbase);
				}
				else
				{
					$this->item_xmlbase = null;
				}
	
				// Title
				if (!empty($item['title']['data'])) {
					$newitem['title'] = $this->sanitize($item['title']['data'], $item['title']['attribs']);
				}
				if (!empty($item['dc:title']['data']))
				{
					$newitem['dc:title'] = $this->sanitize($item['dc:title']['data'], $item['dc:title']['attribs']);
				}
				
				// Description
				if (!empty($item['content']['data']))
				{
					$newitem['content'] = $this->sanitize($item['content']['data'], $item['content']['attribs']);
				}
				if (!empty($item['encoded']['data']))
				{
					$newitem['encoded'] = $this->sanitize($item['encoded']['data'], $item['encoded']['attribs']);
				}
				if (!empty($item['summary']['data']))
				{
					$newitem['summary'] = $this->sanitize($item['summary']['data'], $item['summary']['attribs']);
				}
				if (!empty($item['description']['data']))
				{
					$newitem['description'] = $this->sanitize($item['description']['data'], $item['description']['attribs']);
				}
				if (!empty($item['dc:description']['data']))
				{
					$newitem['dc:description'] = $this->sanitize($item['dc:description']['data'], $item['dc:description']['attribs']);
				}
				if (!empty($item['longdesc']['data']))
				{
					$newitem['longdesc'] = $this->sanitize($item['longdesc']['data'], $item['longdesc']['attribs']);
				}
		
				// Link(s)
				if (!empty($item['link']))
				{
					foreach ($item['link'] as $link)
					{
						if (empty($link['attribs']['REL']))
						{
							$rel = 'alternate';
						}
						else
						{
							$rel = strtolower($link['attribs']['REL']);
						}
						if ($rel == 'enclosure')
						{
							$href = null;
							$type = null;
							$length = null;
							if (!empty($link['data']))
							{
								$href = $this->sanitize($link['data'], $link['attribs'], true);
							}
							else if (!empty($link['attribs']['HREF']))
							{
								$href = $this->sanitize($link['attribs']['HREF'], $link['attribs'], true);
							}
							if (!empty($link['attribs']['TYPE'])) {
								$type = $this->sanitize($link['attribs']['TYPE'], $link['attribs']);
							}
							if (!empty($link['attribs']['LENGTH'])) {
								$length = $this->sanitize($link['attribs']['LENGTH'], $link['attribs']);
							}
							if (!empty($href))
							{
								$newitem['link'][$rel][] = new $this->enclosure_class($href, $type, $length);
							}
						}
						else
						{
							if (!empty($link['data']))
							{
								$newitem['link'][$rel][] = $this->sanitize($link['data'], $link['attribs'], true);
							}
							else if (!empty($link['attribs']['HREF']))
							{
								$newitem['link'][$rel][] = $this->sanitize($link['attribs']['HREF'], $link['attribs'], true);
							}
						}
					}
				}
				
				// Enclosure(s)
				if (!empty($item['enclosure']))
				{
					foreach ($item['enclosure'] as $enclosure)
					{
						if (!empty($enclosure['attribs']['URL']))
						{
							$type = null;
							$length = null;
							$href = $this->sanitize($enclosure['attribs']['URL'], $enclosure['attribs'], true);
							if (!empty($enclosure['attribs']['TYPE']))
							{
								$type = $this->sanitize($enclosure['attribs']['TYPE'], $enclosure['attribs']);
							}
							if (!empty($enclosure['attribs']['LENGTH']))
							{
								$length = $this->sanitize($enclosure['attribs']['LENGTH'], $enclosure['attribs']);
							}
							$newitem['enclosures'][] = new $this->enclosure_class($href, $type, $length);
						}
					}
				}
				
				// ID
				if (!empty($item['guid']['data']))
				{
					if (!empty($item['guid']['attribs']['ISPERMALINK']) && strtolower($item['guid']['attribs']['ISPERMALINK']) == 'false')
					{
						$newitem['guid']['permalink'] = false;
					}
					else
					{
						$newitem['guid']['permalink'] = true;
					}
					$newitem['guid']['data'] = $this->sanitize($item['guid']['data'], $item['guid']['attribs']);
				}
				if (!empty($item['id']['data']))
				{
					$newitem['id'] = $this->sanitize($item['id']['data'], $item['id']['attribs']);
				}
				
				// Date
				if (!empty($item['pubdate']['data']))
				{
					$newitem['pubdate'] = $this->parse_date($this->sanitize($item['pubdate']['data'], $item['pubdate']['attribs']));
				}
				if (!empty($item['dc:date']['data']))
				{
					$newitem['dc:date'] = $this->parse_date($this->sanitize($item['dc:date']['data'], $item['dc:date']['attribs']));
				}
				if (!empty($item['issued']['data']))
				{
					$newitem['issued'] = $this->parse_date($this->sanitize($item['issued']['data'], $item['issued']['attribs']));
				}
				if (!empty($item['published']['data']))
				{
					$newitem['published'] = $this->parse_date($this->sanitize($item['published']['data'], $item['published']['attribs']));
				}
				if (!empty($item['modified']['data']))
				{
					$newitem['modified'] = $this->parse_date($this->sanitize($item['modified']['data'], $item['modified']['attribs']));
				}
				if (!empty($item['updated']['data']))
				{
					$newitem['updated'] = $this->parse_date($this->sanitize($item['updated']['data'], $item['updated']['attribs']));
				}
				
				// Categories
				if (!empty($item['category']))
				{
					foreach ($item['category'] as $category)
					{
						if (!empty($category['data']))
						{
							$newitem['category'][] = $this->sanitize($category['data'], $category['attribs']);
						}
						else if (!empty($category['attribs']['TERM']))
						{
							$newitem['term'][] = $this->sanitize($category['attribs']['TERM'], $category['attribs']);
						}
					}
				}
				if (!empty($item['subject']))
				{
					foreach ($item['subject'] as $category)
					{
						if (!empty($category['data']))
						{
							$newitem['subject'][] = $this->sanitize($category['data'], $category['attribs']);
						}
					}
				}
				
				// Author
				if (!empty($item['creator']))
				{
					foreach ($item['creator'] as $creator)
					{
						if (!empty($creator['data']))
						{
							$newitem['creator'][] = new $this->author_class($this->sanitize($creator['data'], $creator['attribs']), null, null);
						}
					}
				}
				if (!empty($item['author']))
				{
					foreach ($item['author'] as $author)
					{
						$name = null;
						$link = null;
						$email = null;
						if (!empty($author['rss']))
						{
							$sane = $this->sanitize($author['rss']['data'], $author['rss']['attribs']);
							if (preg_match('/(.*)@(.*) \((.*)\)/msiU', $sane, $matches)) {
								$name = trim($matches[3]);
								$email = trim("$matches[1]@$matches[2]");
							} else {
								$email = $sane;
							}
						}
						else
						{
							if (!empty($author['name']))
							{
								$name = $this->sanitize($author['name']['data'], $author['name']['attribs']);
							}
							if (!empty($author['url']))
							{
								$link = $this->sanitize($author['url']['data'], $author['url']['attribs'], true);
							}
							else if (!empty($author['uri']))
							{
								$link = $this->sanitize($author['uri']['data'], $author['uri']['attribs'], true);
							}
							else if (!empty($author['homepage']))
							{
								$link = $this->sanitize($author['homepage']['data'], $author['homepage']['attribs'], true);
							}
							if (!empty($author['email'])) {
								$email = $this->sanitize($author['email']['data'], $author['email']['attribs']);
							}
						}
						$newitem['author'][] = new $this->author_class($name, $link, $email);
					}
				}
				unset($data['items'][$key]);
				$this->items[] = new $this->item_class($newitem);
			}
		}
	}
	
	function sanitize($data, $attribs, $is_url = false)
	{
		$this->attribs = $attribs;
		if (isset($this->feedinfo['type']) && $this->feedinfo['type'] == 'Atom')
		{
			if ((!empty($attribs['MODE']) && $attribs['MODE'] == 'base64') || (!empty($attribs['TYPE']) && $attribs['TYPE'] == 'application/octet-stream'))
			{
				$data = trim($data);
				$data = base64_decode($data);
			}
			else if ((!empty($attribs['MODE']) && $attribs['MODE'] == 'escaped' || !empty($attribs['TYPE']) && ($attribs['TYPE'] == 'html' || $attribs['TYPE'] == 'text/html')) && (empty($attribs['SPENCODED']) || $attribs['SPENCODED'] != 'false'))
			{
				$data = $this->entities_decode($data);
			}
			if (!empty($attribs['TYPE']) && ($attribs['TYPE'] == 'xhtml' || $attribs['TYPE'] == 'application/xhtml+xml'))
			{
				if ($this->remove_div)
				{
					$data = preg_replace('/<div( .*)?>/msiU', '', strrev(preg_replace('/>vid\/</i', '', strrev($data), 1)), 1);
				}
				else
				{
					$data = preg_replace('/<div( .*)?>/msiU', '<div>', $data, 1);
				}
			}
		}
		else
		{
			if (empty($attribs['SPENCODED']) || $attribs['SPENCODED'] != 'false')
			{
				$data = $this->entities_decode($data);
			}
		}
		$data = trim($data);
		$data = str_replace(' spencoded="false">', '>', $data);

		// If Strip Ads is enabled, strip them.
		if ($this->strip_ads)
		{
			$data = preg_replace('/<a (.*)href=(.*)click\.phdo\?s=(.*)<\/a>/msiU', '', $data); // Pheedo links (tested with Dooce.com)
			$data = preg_replace('/<p(.*)>(.*)<a href="http:\/\/ad.doubleclick.net\/jump\/(.*)<\/p>/msiU', '', $data); // Doubleclick links (tested with InfoWorld.com)
			$data = preg_replace('/<p><map (.*)name=(.*)google_ad_map(.*)<\/p>/msiU', '', $data); // Google AdSense for Feeds (tested with tuaw.com).
			// Feedflare, from Feedburner
		}

		// Replace H1, H2, and H3 tags with the less important H4 tags.
		// This is because on a site, the more important headers might make sense,
		// but it most likely doesn't fit in the context of RSS-in-a-webpage.
		if ($this->replace_headers)
		{
			$data = preg_replace('/<h[1-3]((\s*((\w+:)?\w+)\s*=\s*("([^"]*)"|\'([^\']*)\'|(.*)))*)\s*>/msiU', '<h4\\1>', $data);
			$data = preg_replace('/<\/h[1-3]>/i', '</h4>', $data);
		}

		if ($is_url)
		{
			$data = $this->replace_urls($data, true);
		}
		else
		{
			$data = preg_replace_callback('/<(\S+)((\s*((\w+:)?\w+)\s*=\s*("([^"]*)"|\'([^\']*)\'|(.*)))*)\s*(\/>|>(.*)<\/\S+>)/msiU', array(&$this, 'replace_urls'), $data);
		}

		// If Bypass Image Hotlink is enabled, rewrite all the image tags.
		if ($this->bypass_image_hotlink)
		{
			$images = SimplePie_Misc::get_element('img', $data);
			foreach ($images as $img)
			{
				if (!empty($img['attribs']['SRC']['data']))
				{
					$pre = '';
					if ($this->bypass_image_hotlink_page)
					{
						$pre = $this->bypass_image_hotlink_page;
					}
					$pre .= "?$this->bypass_image_hotlink=";
					$img['attribs']['SRC']['data'] = $pre . rawurlencode(strtr($img['attribs']['SRC']['data'], array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES))));
					$data = str_replace($img['full'], SimplePie_Misc::element_implode($img), $data);
				}
			}
		}

		// Strip out HTML tags and attributes that might cause various security problems.
		// Based on recommendations by Mark Pilgrim at:
		// http://diveintomark.org/archives/2003/06/12/how_to_consume_rss_safely
		if ($this->strip_htmltags)
		{
			foreach ($this->strip_htmltags as $tag)
			{
				$data = preg_replace_callback("/<($tag)((\s*((\w+:)?\w+)(\s*=\s*(\"([^\"]*)\"|'([^']*)'|(.*)))?)*)\s*(\/>|>(.*)<\/($tag)((\s*((\w+:)?\w+)(\s*=\s*(\"([^\"]*)\"|'([^']*)'|(.*)))?)*)\s*>)/msiU", array(&$this, 'do_strip_htmltags'), $data);
			}
		}

		if ($this->strip_attributes)
		{
			foreach ($this->strip_attributes as $attrib)
			{
				$data = preg_replace('/ '. trim($attrib) .'=("|&quot;)(\w|\s|=|-|:|;|\/|\.|\?|&|,|#|!|\(|\)|\'|&apos;|<|>|\+|{|})*("|&quot;)/i', '', $data);
				$data = preg_replace('/ '. trim($attrib) .'=(\'|&apos;)(\w|\s|=|-|:|;|\/|\.|\?|&|,|#|!|\(|\)|"|&quot;|<|>|\+|{|})*(\'|&apos;)/i', '', $data);
				$data = preg_replace('/ '. trim($attrib) .'=(\w|\s|=|-|:|;|\/|\.|\?|&|,|#|!|\(|\)|\+|{|})*/i', '', $data);
			}
		}
		
		// Convert encoding
		$data = SimplePie_Misc::change_encoding($data, $this->input_encoding, $this->output_encoding);

		return $data;
	}
	
	function do_strip_htmltags($match)
	{
		if ($this->encode_instead_of_strip)
		{
			if (isset($match[12]) && !in_array(strtolower($match[1]), array('script', 'style')))
			{
				return "&lt;$match[1]$match[2]&gt;$match[12]&lt;/$match[1]&gt;";
			}
			else if (isset($match[12]))
			{
				return "&lt;$match[1]$match[2]&gt;&lt;/$match[1]&gt;";
			}
			else
			{
				return "&lt;$match[1]$match[2]/&gt;";
			}
		}
		else
		{
			if (isset($match[12]) && !in_array(strtolower($match[1]), array('script', 'style')))
			{
				return $match[12];
			}
			else
			{
				return '';
			}
		}
	}
	
	function replace_urls($data, $raw_url = false)
	{
		if (!empty($this->attribs['XML:BASE']))
		{
			$xmlbase = $attribs['XML:BASE'];
		}
		else if (!empty($this->attribs['HTTP://WWW.W3.ORG/XML/1998/NAMESPACE:BASE']))
		{
			$xmlbase = $this->attribs['HTTP://WWW.W3.ORG/XML/1998/NAMESPACE:BASE'];
		}
		if (!empty($xmlbase))
		{
			if (!empty($this->item_xmlbase))
			{
				$xmlbase = SimplePie_Misc::absolutize_url($xmlbase, $this->item_xmlbase);
			}
			else
			{
				$xmlbase = SimplePie_Misc::absolutize_url($xmlbase, $this->feed_xmlbase);
			}
		}
		else if (!empty($this->item_xmlbase))
		{
			$xmlbase = $this->item_xmlbase;
		}
		else
		{
			$xmlbase = $this->feed_xmlbase;
		}
		
		if ($raw_url)
		{
			return SimplePie_Misc::absolutize_url($data, $xmlbase);
		}
		else
		{
			$attributes = array(
				'background',
				'href',
				'src',
				'longdesc',
				'usemap',
				'codebase',
				'data',
				'classid',
				'cite',
				'action',
				'profile',
				'for'
			);
			foreach ($attributes as $attribute)
			{
				if (preg_match("/$attribute='(.*)'/siU", $data[0], $attrib) || preg_match("/$attribute=\"(.*)\"/siU", $data[0], $attrib) || preg_match("/$attribute=(.*)[ |\/|>]/siU", $data[0], $attrib))
				{
					$new_tag = str_replace($attrib[1], SimplePie_Misc::absolutize_url($attrib[1], $xmlbase), $attrib[0]);
					$data[0] = str_replace($attrib[0], $new_tag, $data[0]);
				}
			}
			return $data[0];
		}
	}
	
	function entities_decode($data)
	{
		return preg_replace_callback('/&(#)?(x)?([0-9a-z]+);/mi', array(&$this, 'do_entites_decode'), $data);
	}
	
	function do_entites_decode($data)
	{
		if (isset($this->cached_entities[$data[0]]))
		{
			return $this->cached_entities[$data[0]];
		}
		else
		{
			$return = SimplePie_Misc::change_encoding(html_entity_decode($data[0], ENT_QUOTES), 'ISO-8859-1', $this->input_encoding);
			if ($return == $data[0])
			{
				$return = SimplePie_Misc::change_encoding(preg_replace_callback('/&#([0-9a-fx]+);/mi', array(&$this, 'replace_num_entity'), $data[0]), 'UTF-8', $this->input_encoding);
			}
			$this->cached_entities[$data[0]] = $return;
			return $return;
		}
	}

	/*
	 * Escape numeric entities
	 * From a PHP Manual note (on html_entity_decode())
	 * Copyright (c) 2005 by "php dot net at c dash ovidiu dot tk", 
	 * "emilianomartinezluque at yahoo dot com" and "hurricane at cyberworldz dot org".
	 *
	 * This material may be distributed only subject to the terms and conditions set forth in 
	 * the Open Publication License, v1.0 or later (the latest version is presently available at 
	 * http://www.opencontent.org/openpub/).
	 */
	function replace_num_entity($ord)
	{
		$ord = $ord[1];
		if (preg_match('/^x([0-9a-f]+)$/i', $ord, $match))
		{
			$ord = hexdec($match[1]);
		}
		else
		{
			$ord = intval($ord);
		}
		
		$no_bytes = 0;
		$byte = array();
		if ($ord < 128)
		{
			return chr($ord);
		}
		if ($ord < 2048)
		{
			$no_bytes = 2;
		}
		else if ($ord < 65536)
		{
			$no_bytes = 3;
		}
		else if ($ord < 1114112)
		{
			$no_bytes = 4;
		}
		else
		{
			return;
		}
		switch ($no_bytes)
		{
			case 2:
				$prefix = array(31, 192);
				break;
				
			case 3:
				$prefix = array(15, 224);
				break;
				
			case 4:
				$prefix = array(7, 240);
				break;
		}
		
		for ($i = 0; $i < $no_bytes; $i++)
		{
			$byte[$no_bytes-$i-1] = (($ord & (63 * pow(2,6*$i))) / pow(2,6*$i)) & 63 | 128;
		}
		$byte[0] = ($byte[0] & $prefix[0]) | $prefix[1];
		
		$ret = '';
		for ($i = 0; $i < $no_bytes; $i++)
		{
			$ret .= chr($byte[$i]);
		}
		return $ret;
	}
	
	function parse_date($date)
	{
		$military_timezone = array('A' => '-0100', 'B' => '-0200', 'C' => '-0300', 'D' => '-0400', 'E' => '-0500', 'F' => '-0600', 'G' => '-0700', 'H' => '-0800', 'I' => '-0900', 'K' => '-1000', 'L' => '-1100', 'M' => '-1200', 'N' => '+0100', 'O' => '+0200', 'P' => '+0300', 'Q' => '+0400', 'R' => '+0500', 'S' => '+0600', 'T' => '+0700', 'U' => '+0800', 'V' => '+0900', 'W' => '+1000', 'X' => '+1100', 'Y' => '+1200', 'Z' => '-0000');
		$north_american_timezone = array('GMT' => '-0000', 'EST' => '-0500', 'EDT' => '-0400', 'CST' => '-0600', 'CDT' => '-0500', 'MST' => '-0700', 'MDT' => '-0600', 'PST' => '-0800', 'PDT' => '-0700');
		if (preg_match('/([0-9]{2,4})-?([0-9]{2})-?([0-9]{2})T([0-9]{2}):?([0-9]{2})(:?([0-9]{2}(\.[0-9]*)?))?(UT|GMT|EST|EDT|CST|CDT|MST|MDT|PST|PDT|[a-z]|(\\+|-)[0-9]{4}|(\\+|-)[0-9]{2}:[0-9]{2})?/i', $date, $matches))
		{
			if (!isset($matches[7]))
			{
				$matches[7] = '';
			}
			if (!isset($matches[9]))
			{
				$matches[9] = '';
			}
			$matches[7] = str_pad(round($matches[7]), 2, '0', STR_PAD_LEFT);
			switch (strlen($matches[9]))
			{
				case 0:
					$timezone = '';
					break;
					
				case 1:
					$timezone = $military_timezone[strtoupper($matches[9])];
					break;
				
				case 2:
					$timezone = '-0000';
					break;
				
				case 3:
					$timezone = $north_american_timezone[strtoupper($matches[9])];
					break;
				
				case 5:
					$timezone = $matches[9];
					break;
				
				case 6:
					$timezone = substr_replace($matches[9], '', 3, 1);
					break;
			}
			$date = strtotime("$matches[1]-$matches[2]-$matches[3] $matches[4]:$matches[5]:$matches[7] $timezone");
		}
		else if (preg_match('/([0-9]{1,2})\s*(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s*([0-9]{2}|[0-9]{4})\s*([0-9]{2}):([0-9]{2})(:([0-9]{2}(\.[0-9]*)?))?\s*(UT|GMT|EST|EDT|CST|CDT|MST|MDT|PST|PDT|[a-z]|(\\+|-)[0-9]{4}|(\\+|-)[0-9]{2}:[0-9]{2})?/i', $date, $matches))
		{
			$three_month = array('Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12);
			$month = $three_month[$matches[2]];
			if (strlen($matches[3]) == 2)
			{
				$year = ($matches[3] < 70) ? "20$matches[3]" : "19$matches[3]";
			}
			else
			{
				$year = $matches[3];
			}
			if (!isset($matches[7]))
			{
				$matches[7] = '';
			}
			if (!isset($matches[9]))
			{
				$matches[9] = '';
			}
			$second = str_pad(round($matches[7]), 2, '0', STR_PAD_LEFT);
			switch (strlen($matches[9]))
			{
				case 0:
					$timezone = '';
					break;
					
				case 1:
					$timezone = $military_timezone[strtoupper($matches[9])];
					break;
				
				case 2:
					$timezone = '-0000';
					break;
				
				case 3:
					$timezone = $north_american_timezone[strtoupper($matches[9])];
					break;
				
				case 5:
					$timezone = $matches[9];
					break;
				
				case 6:
					$timezone = substr_replace($matches[9], '', 3, 1);
					break;
			}
			$date = strtotime("$year-$month-$matches[1] $matches[4]:$matches[5]:$second $timezone");
		}
		else
		{
			$date = strtotime($date);
		}
		return ($date > 0) ? $date : false;
	}
}

?>