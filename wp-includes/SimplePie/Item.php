<?php
/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2016, Ryan Parman, Sam Sneddon, Ryan McCue, and contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * 	* Neither the name of the SimplePie Team nor the names of its contributors may be used
 * 	  to endorse or promote products derived from this software without specific prior
 * 	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package SimplePie
 * @copyright 2004-2016 Ryan Parman, Sam Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Sam Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */


/**
 * Manages all item-related data
 *
 * Used by {@see SimplePie::get_item()} and {@see SimplePie::get_items()}
 *
 * This class can be overloaded with {@see SimplePie::set_item_class()}
 *
 * @package SimplePie
 * @subpackage API
 */
class SimplePie_Item
{
	/**
	 * Parent feed
	 *
	 * @access private
	 * @var SimplePie
	 */
	var $feed;

	/**
	 * Raw data
	 *
	 * @access private
	 * @var array
	 */
	var $data = array();

	/**
	 * Registry object
	 *
	 * @see set_registry
	 * @var SimplePie_Registry
	 */
	protected $registry;

	/**
	 * Create a new item object
	 *
	 * This is usually used by {@see SimplePie::get_items} and
	 * {@see SimplePie::get_item}. Avoid creating this manually.
	 *
	 * @param SimplePie $feed Parent feed
	 * @param array $data Raw data
	 */
	public function __construct($feed, $data)
	{
		$this->feed = $feed;
		$this->data = $data;
	}

	/**
	 * Set the registry handler
	 *
	 * This is usually used by {@see SimplePie_Registry::create}
	 *
	 * @since 1.3
	 * @param SimplePie_Registry $registry
	 */
	public function set_registry(SimplePie_Registry $registry)
	{
		$this->registry = $registry;
	}

	/**
	 * Get a string representation of the item
	 *
	 * @return string
	 */
	public function __toString()
	{
		return md5(serialize($this->data));
	}

	/**
	 * Remove items that link back to this before destroying this object
	 */
	public function __destruct()
	{
		if (!gc_enabled())
		{
			unset($this->feed);
		}
	}

	/**
	 * Get data for an item-level element
	 *
	 * This method allows you to get access to ANY element/attribute that is a
	 * sub-element of the item/entry tag.
	 *
	 * See {@see SimplePie::get_feed_tags()} for a description of the return value
	 *
	 * @since 1.0
	 * @see http://simplepie.org/wiki/faq/supported_xml_namespaces
	 * @param string $namespace The URL of the XML namespace of the elements you're trying to access
	 * @param string $tag Tag name
	 * @return array
	 */
	public function get_item_tags($namespace, $tag)
	{
		if (isset($this->data['child'][$namespace][$tag]))
		{
			return $this->data['child'][$namespace][$tag];
		}

		return null;
	}

	/**
	 * Get the base URL value from the parent feed
	 *
	 * Uses `<xml:base>`
	 *
	 * @param array $element
	 * @return string
	 */
	public function get_base($element = array())
	{
		return $this->feed->get_base($element);
	}

	/**
	 * Sanitize feed data
	 *
	 * @access private
	 * @see SimplePie::sanitize()
	 * @param string $data Data to sanitize
	 * @param int $type One of the SIMPLEPIE_CONSTRUCT_* constants
	 * @param string $base Base URL to resolve URLs against
	 * @return string Sanitized data
	 */
	public function sanitize($data, $type, $base = '')
	{
		return $this->feed->sanitize($data, $type, $base);
	}

	/**
	 * Get the parent feed
	 *
	 * Note: this may not work as you think for multifeeds!
	 *
	 * @link http://simplepie.org/faq/typical_multifeed_gotchas#missing_data_from_feed
	 * @since 1.0
	 * @return SimplePie
	 */
	public function get_feed()
	{
		return $this->feed;
	}

	/**
	 * Get the unique identifier for the item
	 *
	 * This is usually used when writing code to check for new items in a feed.
	 *
	 * Uses `<atom:id>`, `<guid>`, `<dc:identifier>` or the `about` attribute
	 * for RDF. If none of these are supplied (or `$hash` is true), creates an
	 * MD5 hash based on the permalink, title and content.
	 *
	 * @since Beta 2
	 * @param boolean $hash Should we force using a hash instead of the supplied ID?
	 * @param string|false $fn User-supplied function to generate an hash
	 * @return string|null
	 */
	public function get_id($hash = false, $fn = 'md5')
	{
		if (!$hash)
		{
			if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'id'))
			{
				return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'id'))
			{
				return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'guid'))
			{
				return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'identifier'))
			{
				return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_10, 'identifier'))
			{
				return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			elseif (isset($this->data['attribs'][SIMPLEPIE_NAMESPACE_RDF]['about']))
			{
				return $this->sanitize($this->data['attribs'][SIMPLEPIE_NAMESPACE_RDF]['about'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
		}
		if ($fn === false)
		{
			return null;
		}
		elseif (!is_callable($fn))
		{
			trigger_error('User-supplied function $fn must be callable', E_USER_WARNING);
			$fn = 'md5';
		}
		return call_user_func($fn,
		       $this->get_permalink().$this->get_title().$this->get_content());
	}

	/**
	 * Get the title of the item
	 *
	 * Uses `<atom:title>`, `<title>` or `<dc:title>`
	 *
	 * @since Beta 2 (previously called `get_item_title` since 0.8)
	 * @return string|null
	 */
	public function get_title()
	{
		if (!isset($this->data['title']))
		{
			if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'title'))
			{
				$this->data['title'] = $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_10_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'title'))
			{
				$this->data['title'] = $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_03_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'title'))
			{
				$this->data['title'] = $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'title'))
			{
				$this->data['title'] = $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'title'))
			{
				$this->data['title'] = $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'title'))
			{
				$this->data['title'] = $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_10, 'title'))
			{
				$this->data['title'] = $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			else
			{
				$this->data['title'] = null;
			}
		}
		return $this->data['title'];
	}

	/**
	 * Get the content for the item
	 *
	 * Prefers summaries over full content , but will return full content if a
	 * summary does not exist.
	 *
	 * To prefer full content instead, use {@see get_content}
	 *
	 * Uses `<atom:summary>`, `<description>`, `<dc:description>` or
	 * `<itunes:subtitle>`
	 *
	 * @since 0.8
	 * @param boolean $description_only Should we avoid falling back to the content?
	 * @return string|null
	 */
	public function get_description($description_only = false)
	{
		if (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'summary')) &&
		    ($return = $this->sanitize($tags[0]['data'], $this->registry->call('Misc', 'atom_10_construct_type', array($tags[0]['attribs'])), $this->get_base($tags[0]))))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'summary')) &&
		        ($return = $this->sanitize($tags[0]['data'], $this->registry->call('Misc', 'atom_03_construct_type', array($tags[0]['attribs'])), $this->get_base($tags[0]))))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'description')) &&
		        ($return = $this->sanitize($tags[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($tags[0]))))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'description')) &&
		        ($return = $this->sanitize($tags[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($tags[0]))))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'description')) &&
		        ($return = $this->sanitize($tags[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT)))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_10, 'description')) &&
		        ($return = $this->sanitize($tags[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT)))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'summary')) &&
		        ($return = $this->sanitize($tags[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($tags[0]))))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'subtitle')) &&
		        ($return = $this->sanitize($tags[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT)))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'description')) &&
		        ($return = $this->sanitize($tags[0]['data'], SIMPLEPIE_CONSTRUCT_HTML)))
		{
			return $return;
		}

		elseif (!$description_only)
		{
			return $this->get_content(true);
		}

		return null;
	}

	/**
	 * Get the content for the item
	 *
	 * Prefers full content over summaries, but will return a summary if full
	 * content does not exist.
	 *
	 * To prefer summaries instead, use {@see get_description}
	 *
	 * Uses `<atom:content>` or `<content:encoded>` (RSS 1.0 Content Module)
	 *
	 * @since 1.0
	 * @param boolean $content_only Should we avoid falling back to the description?
	 * @return string|null
	 */
	public function get_content($content_only = false)
	{
		if (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'content')) &&
		    ($return = $this->sanitize($tags[0]['data'], $this->registry->call('Misc', 'atom_10_content_construct_type', array($tags[0]['attribs'])), $this->get_base($tags[0]))))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'content')) &&
		        ($return = $this->sanitize($tags[0]['data'], $this->registry->call('Misc', 'atom_03_construct_type', array($tags[0]['attribs'])), $this->get_base($tags[0]))))
		{
			return $return;
		}
		elseif (($tags = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_10_MODULES_CONTENT, 'encoded')) &&
		        ($return = $this->sanitize($tags[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($tags[0]))))
		{
			return $return;
		}
		elseif (!$content_only)
		{
			return $this->get_description(true);
		}

		return null;
	}

	/**
	 * Get the media:thumbnail of the item
	 *
	 * Uses `<media:thumbnail>`
	 *
	 *
	 * @return array|null
	 */
	public function get_thumbnail()
	{
		if (!isset($this->data['thumbnail']))
		{
			if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'thumbnail'))
			{
				$this->data['thumbnail'] = $return[0]['attribs'][''];
			}
			else
			{
				$this->data['thumbnail'] = null;
			}
		}
		return $this->data['thumbnail'];
	}

	/**
	 * Get a category for the item
	 *
	 * @since Beta 3 (previously called `get_categories()` since Beta 2)
	 * @param int $key The category that you want to return.  Remember that arrays begin with 0, not 1
	 * @return SimplePie_Category|null
	 */
	public function get_category($key = 0)
	{
		$categories = $this->get_categories();
		if (isset($categories[$key]))
		{
			return $categories[$key];
		}

		return null;
	}

	/**
	 * Get all categories for the item
	 *
	 * Uses `<atom:category>`, `<category>` or `<dc:subject>`
	 *
	 * @since Beta 3
	 * @return SimplePie_Category[]|null List of {@see SimplePie_Category} objects
	 */
	public function get_categories()
	{
		$categories = array();

		$type = 'category';
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, $type) as $category)
		{
			$term = null;
			$scheme = null;
			$label = null;
			if (isset($category['attribs']['']['term']))
			{
				$term = $this->sanitize($category['attribs']['']['term'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($category['attribs']['']['scheme']))
			{
				$scheme = $this->sanitize($category['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($category['attribs']['']['label']))
			{
				$label = $this->sanitize($category['attribs']['']['label'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			$categories[] = $this->registry->create('Category', array($term, $scheme, $label, $type));
		}
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, $type) as $category)
		{
			// This is really the label, but keep this as the term also for BC.
			// Label will also work on retrieving because that falls back to term.
			$term = $this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			if (isset($category['attribs']['']['domain']))
			{
				$scheme = $this->sanitize($category['attribs']['']['domain'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			else
			{
				$scheme = null;
			}
			$categories[] = $this->registry->create('Category', array($term, $scheme, null, $type));
		}

		$type = 'subject';
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, $type) as $category)
		{
			$categories[] = $this->registry->create('Category', array($this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null, $type));
		}
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_10, $type) as $category)
		{
			$categories[] = $this->registry->create('Category', array($this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null, $type));
		}

		if (!empty($categories))
		{
			return array_unique($categories);
		}

		return null;
	}

	/**
	 * Get an author for the item
	 *
	 * @since Beta 2
	 * @param int $key The author that you want to return.  Remember that arrays begin with 0, not 1
	 * @return SimplePie_Author|null
	 */
	public function get_author($key = 0)
	{
		$authors = $this->get_authors();
		if (isset($authors[$key]))
		{
			return $authors[$key];
		}

		return null;
	}

	/**
	 * Get a contributor for the item
	 *
	 * @since 1.1
	 * @param int $key The contrbutor that you want to return.  Remember that arrays begin with 0, not 1
	 * @return SimplePie_Author|null
	 */
	public function get_contributor($key = 0)
	{
		$contributors = $this->get_contributors();
		if (isset($contributors[$key]))
		{
			return $contributors[$key];
		}

		return null;
	}

	/**
	 * Get all contributors for the item
	 *
	 * Uses `<atom:contributor>`
	 *
	 * @since 1.1
	 * @return SimplePie_Author[]|null List of {@see SimplePie_Author} objects
	 */
	public function get_contributors()
	{
		$contributors = array();
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'contributor') as $contributor)
		{
			$name = null;
			$uri = null;
			$email = null;
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data']))
			{
				$name = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data']))
			{
				$uri = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]));
			}
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data']))
			{
				$email = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if ($name !== null || $email !== null || $uri !== null)
			{
				$contributors[] = $this->registry->create('Author', array($name, $uri, $email));
			}
		}
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'contributor') as $contributor)
		{
			$name = null;
			$url = null;
			$email = null;
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data']))
			{
				$name = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data']))
			{
				$url = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]));
			}
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data']))
			{
				$email = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if ($name !== null || $email !== null || $url !== null)
			{
				$contributors[] = $this->registry->create('Author', array($name, $url, $email));
			}
		}

		if (!empty($contributors))
		{
			return array_unique($contributors);
		}

		return null;
	}

	/**
	 * Get all authors for the item
	 *
	 * Uses `<atom:author>`, `<author>`, `<dc:creator>` or `<itunes:author>`
	 *
	 * @since Beta 2
	 * @return SimplePie_Author[]|null List of {@see SimplePie_Author} objects
	 */
	public function get_authors()
	{
		$authors = array();
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'author') as $author)
		{
			$name = null;
			$uri = null;
			$email = null;
			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data']))
			{
				$name = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data']))
			{
				$uri = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]));
			}
			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data']))
			{
				$email = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if ($name !== null || $email !== null || $uri !== null)
			{
				$authors[] = $this->registry->create('Author', array($name, $uri, $email));
			}
		}
		if ($author = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'author'))
		{
			$name = null;
			$url = null;
			$email = null;
			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data']))
			{
				$name = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data']))
			{
				$url = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]));
			}
			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data']))
			{
				$email = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if ($name !== null || $email !== null || $url !== null)
			{
				$authors[] = $this->registry->create('Author', array($name, $url, $email));
			}
		}
		if ($author = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'author'))
		{
			$authors[] = $this->registry->create('Author', array(null, null, $this->sanitize($author[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT)));
		}
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'creator') as $author)
		{
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_10, 'creator') as $author)
		{
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}
		foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'author') as $author)
		{
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}

		if (!empty($authors))
		{
			return array_unique($authors);
		}
		elseif (($source = $this->get_source()) && ($authors = $source->get_authors()))
		{
			return $authors;
		}
		elseif ($authors = $this->feed->get_authors())
		{
			return $authors;
		}

		return null;
	}

	/**
	 * Get the copyright info for the item
	 *
	 * Uses `<atom:rights>` or `<dc:rights>`
	 *
	 * @since 1.1
	 * @return string
	 */
	public function get_copyright()
	{
		if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'rights'))
		{
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_10_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
		}
		elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'rights'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_10, 'rights'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}

		return null;
	}

	/**
	 * Get the posting date/time for the item
	 *
	 * Uses `<atom:published>`, `<atom:updated>`, `<atom:issued>`,
	 * `<atom:modified>`, `<pubDate>` or `<dc:date>`
	 *
	 * Note: obeys PHP's timezone setting. To get a UTC date/time, use
	 * {@see get_gmdate}
	 *
	 * @since Beta 2 (previously called `get_item_date` since 0.8)
	 *
	 * @param string $date_format Supports any PHP date format from {@see http://php.net/date} (empty for the raw data)
	 * @return int|string|null
	 */
	public function get_date($date_format = 'j F Y, g:i a')
	{
		if (!isset($this->data['date']))
		{
			if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'published'))
			{
				$this->data['date']['raw'] = $return[0]['data'];
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'pubDate'))
			{
				$this->data['date']['raw'] = $return[0]['data'];
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'date'))
			{
				$this->data['date']['raw'] = $return[0]['data'];
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_DC_10, 'date'))
			{
				$this->data['date']['raw'] = $return[0]['data'];
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'updated'))
			{
				$this->data['date']['raw'] = $return[0]['data'];
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'issued'))
			{
				$this->data['date']['raw'] = $return[0]['data'];
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'created'))
			{
				$this->data['date']['raw'] = $return[0]['data'];
			}
			elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'modified'))
			{
				$this->data['date']['raw'] = $return[0]['data'];
			}

			if (!empty($this->data['date']['raw']))
			{
				$parser = $this->registry->call('Parse_Date', 'get');
				$this->data['date']['parsed'] = $parser->parse($this->data['date']['raw']);
			}
			else
			{
				$this->data['date'] = null;
			}
		}
		if ($this->data['date'])
		{
			$date_format = (string) $date_format;
			switch ($date_format)
			{
				case '':
					return $this->sanitize($this->data['date']['raw'], SIMPLEPIE_CONSTRUCT_TEXT);

				case 'U':
					return $this->data['date']['parsed'];

				default:
					return date($date_format, $this->data['date']['parsed']);
			}
		}

		return null;
	}

	/**
	 * Get the update date/time for the item
	 *
	 * Uses `<atom:updated>`
	 *
	 * Note: obeys PHP's timezone setting. To get a UTC date/time, use
	 * {@see get_gmdate}
	 *
	 * @param string $date_format Supports any PHP date format from {@see http://php.net/date} (empty for the raw data)
	 * @return int|string|null
	 */
	public function get_updated_date($date_format = 'j F Y, g:i a')
	{
		if (!isset($this->data['updated']))
		{
			if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'updated'))
			{
				$this->data['updated']['raw'] = $return[0]['data'];
			}

			if (!empty($this->data['updated']['raw']))
			{
				$parser = $this->registry->call('Parse_Date', 'get');
				$this->data['updated']['parsed'] = $parser->parse($this->data['updated']['raw']);
			}
			else
			{
				$this->data['updated'] = null;
			}
		}
		if ($this->data['updated'])
		{
			$date_format = (string) $date_format;
			switch ($date_format)
			{
				case '':
					return $this->sanitize($this->data['updated']['raw'], SIMPLEPIE_CONSTRUCT_TEXT);

				case 'U':
					return $this->data['updated']['parsed'];

				default:
					return date($date_format, $this->data['updated']['parsed']);
			}
		}

		return null;
	}

	/**
	 * Get the localized posting date/time for the item
	 *
	 * Returns the date formatted in the localized language. To display in
	 * languages other than the server's default, you need to change the locale
	 * with {@link http://php.net/setlocale setlocale()}. The available
	 * localizations depend on which ones are installed on your web server.
	 *
	 * @since 1.0
	 *
	 * @param string $date_format Supports any PHP date format from {@see http://php.net/strftime} (empty for the raw data)
	 * @return int|string|null
	 */
	public function get_local_date($date_format = '%c')
	{
		if (!$date_format)
		{
			return $this->sanitize($this->get_date(''), SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif (($date = $this->get_date('U')) !== null && $date !== false)
		{
			return strftime($date_format, $date);
		}

		return null;
	}

	/**
	 * Get the posting date/time for the item (UTC time)
	 *
	 * @see get_date
	 * @param string $date_format Supports any PHP date format from {@see http://php.net/date}
	 * @return int|string|null
	 */
	public function get_gmdate($date_format = 'j F Y, g:i a')
	{
		$date = $this->get_date('U');
		if ($date === null)
		{
			return null;
		}

		return gmdate($date_format, $date);
	}

	/**
	 * Get the update date/time for the item (UTC time)
	 *
	 * @see get_updated_date
	 * @param string $date_format Supports any PHP date format from {@see http://php.net/date}
	 * @return int|string|null
	 */
	public function get_updated_gmdate($date_format = 'j F Y, g:i a')
	{
		$date = $this->get_updated_date('U');
		if ($date === null)
		{
			return null;
		}

		return gmdate($date_format, $date);
	}

	/**
	 * Get the permalink for the item
	 *
	 * Returns the first link available with a relationship of "alternate".
	 * Identical to {@see get_link()} with key 0
	 *
	 * @see get_link
	 * @since 0.8
	 * @return string|null Permalink URL
	 */
	public function get_permalink()
	{
		$link = $this->get_link();
		$enclosure = $this->get_enclosure(0);
		if ($link !== null)
		{
			return $link;
		}
		elseif ($enclosure !== null)
		{
			return $enclosure->get_link();
		}

		return null;
	}

	/**
	 * Get a single link for the item
	 *
	 * @since Beta 3
	 * @param int $key The link that you want to return.  Remember that arrays begin with 0, not 1
	 * @param string $rel The relationship of the link to return
	 * @return string|null Link URL
	 */
	public function get_link($key = 0, $rel = 'alternate')
	{
		$links = $this->get_links($rel);
		if ($links && $links[$key] !== null)
		{
			return $links[$key];
		}

		return null;
	}

	/**
	 * Get all links for the item
	 *
	 * Uses `<atom:link>`, `<link>` or `<guid>`
	 *
	 * @since Beta 2
	 * @param string $rel The relationship of links to return
	 * @return array|null Links found for the item (strings)
	 */
	public function get_links($rel = 'alternate')
	{
		if (!isset($this->data['links']))
		{
			$this->data['links'] = array();
			foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'link') as $link)
			{
				if (isset($link['attribs']['']['href']))
				{
					$link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
					$this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($link));

				}
			}
			foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'link') as $link)
			{
				if (isset($link['attribs']['']['href']))
				{
					$link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
					$this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($link));
				}
			}
			if ($links = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'link'))
			{
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}
			if ($links = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'link'))
			{
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}
			if ($links = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'link'))
			{
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}
			if ($links = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'guid'))
			{
				if (!isset($links[0]['attribs']['']['isPermaLink']) || strtolower(trim($links[0]['attribs']['']['isPermaLink'])) === 'true')
				{
					$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
				}
			}

			$keys = array_keys($this->data['links']);
			foreach ($keys as $key)
			{
				if ($this->registry->call('Misc', 'is_isegment_nz_nc', array($key)))
				{
					if (isset($this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key]))
					{
						$this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key] = array_merge($this->data['links'][$key], $this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key]);
						$this->data['links'][$key] =& $this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key];
					}
					else
					{
						$this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key] =& $this->data['links'][$key];
					}
				}
				elseif (substr($key, 0, 41) === SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY)
				{
					$this->data['links'][substr($key, 41)] =& $this->data['links'][$key];
				}
				$this->data['links'][$key] = array_unique($this->data['links'][$key]);
			}
		}
		if (isset($this->data['links'][$rel]))
		{
			return $this->data['links'][$rel];
		}

		return null;
	}

	/**
	 * Get an enclosure from the item
	 *
	 * Supports the <enclosure> RSS tag, as well as Media RSS and iTunes RSS.
	 *
	 * @since Beta 2
	 * @todo Add ability to prefer one type of content over another (in a media group).
	 * @param int $key The enclosure that you want to return.  Remember that arrays begin with 0, not 1
	 * @return SimplePie_Enclosure|null
	 */
	public function get_enclosure($key = 0, $prefer = null)
	{
		$enclosures = $this->get_enclosures();
		if (isset($enclosures[$key]))
		{
			return $enclosures[$key];
		}

		return null;
	}

	/**
	 * Get all available enclosures (podcasts, etc.)
	 *
	 * Supports the <enclosure> RSS tag, as well as Media RSS and iTunes RSS.
	 *
	 * At this point, we're pretty much assuming that all enclosures for an item
	 * are the same content.  Anything else is too complicated to
	 * properly support.
	 *
	 * @since Beta 2
	 * @todo Add support for end-user defined sorting of enclosures by type/handler (so we can prefer the faster-loading FLV over MP4).
	 * @todo If an element exists at a level, but its value is empty, we should fall back to the value from the parent (if it exists).
	 * @return SimplePie_Enclosure[]|null List of SimplePie_Enclosure items
	 */
	public function get_enclosures()
	{
		if (!isset($this->data['enclosures']))
		{
			$this->data['enclosures'] = array();

			// Elements
			$captions_parent = null;
			$categories_parent = null;
			$copyrights_parent = null;
			$credits_parent = null;
			$description_parent = null;
			$duration_parent = null;
			$hashes_parent = null;
			$keywords_parent = null;
			$player_parent = null;
			$ratings_parent = null;
			$restrictions_parent = null;
			$thumbnails_parent = null;
			$title_parent = null;

			// Let's do the channel and item-level ones first, and just re-use them if we need to.
			$parent = $this->get_feed();

			// CAPTIONS
			if ($captions = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'text'))
			{
				foreach ($captions as $caption)
				{
					$caption_type = null;
					$caption_lang = null;
					$caption_startTime = null;
					$caption_endTime = null;
					$caption_text = null;
					if (isset($caption['attribs']['']['type']))
					{
						$caption_type = $this->sanitize($caption['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($caption['attribs']['']['lang']))
					{
						$caption_lang = $this->sanitize($caption['attribs']['']['lang'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($caption['attribs']['']['start']))
					{
						$caption_startTime = $this->sanitize($caption['attribs']['']['start'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($caption['attribs']['']['end']))
					{
						$caption_endTime = $this->sanitize($caption['attribs']['']['end'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($caption['data']))
					{
						$caption_text = $this->sanitize($caption['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$captions_parent[] = $this->registry->create('Caption', array($caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text));
				}
			}
			elseif ($captions = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'text'))
			{
				foreach ($captions as $caption)
				{
					$caption_type = null;
					$caption_lang = null;
					$caption_startTime = null;
					$caption_endTime = null;
					$caption_text = null;
					if (isset($caption['attribs']['']['type']))
					{
						$caption_type = $this->sanitize($caption['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($caption['attribs']['']['lang']))
					{
						$caption_lang = $this->sanitize($caption['attribs']['']['lang'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($caption['attribs']['']['start']))
					{
						$caption_startTime = $this->sanitize($caption['attribs']['']['start'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($caption['attribs']['']['end']))
					{
						$caption_endTime = $this->sanitize($caption['attribs']['']['end'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($caption['data']))
					{
						$caption_text = $this->sanitize($caption['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$captions_parent[] = $this->registry->create('Caption', array($caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text));
				}
			}
			if (is_array($captions_parent))
			{
				$captions_parent = array_values(array_unique($captions_parent));
			}

			// CATEGORIES
			foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'category') as $category)
			{
				$term = null;
				$scheme = null;
				$label = null;
				if (isset($category['data']))
				{
					$term = $this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				if (isset($category['attribs']['']['scheme']))
				{
					$scheme = $this->sanitize($category['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				else
				{
					$scheme = 'http://search.yahoo.com/mrss/category_schema';
				}
				if (isset($category['attribs']['']['label']))
				{
					$label = $this->sanitize($category['attribs']['']['label'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				$categories_parent[] = $this->registry->create('Category', array($term, $scheme, $label));
			}
			foreach ((array) $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'category') as $category)
			{
				$term = null;
				$scheme = null;
				$label = null;
				if (isset($category['data']))
				{
					$term = $this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				if (isset($category['attribs']['']['scheme']))
				{
					$scheme = $this->sanitize($category['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				else
				{
					$scheme = 'http://search.yahoo.com/mrss/category_schema';
				}
				if (isset($category['attribs']['']['label']))
				{
					$label = $this->sanitize($category['attribs']['']['label'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				$categories_parent[] = $this->registry->create('Category', array($term, $scheme, $label));
			}
			foreach ((array) $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'category') as $category)
			{
				$term = null;
				$scheme = 'http://www.itunes.com/dtds/podcast-1.0.dtd';
				$label = null;
				if (isset($category['attribs']['']['text']))
				{
					$label = $this->sanitize($category['attribs']['']['text'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				$categories_parent[] = $this->registry->create('Category', array($term, $scheme, $label));

				if (isset($category['child'][SIMPLEPIE_NAMESPACE_ITUNES]['category']))
				{
					foreach ((array) $category['child'][SIMPLEPIE_NAMESPACE_ITUNES]['category'] as $subcategory)
					{
						if (isset($subcategory['attribs']['']['text']))
						{
							$label = $this->sanitize($subcategory['attribs']['']['text'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						$categories_parent[] = $this->registry->create('Category', array($term, $scheme, $label));
					}
				}
			}
			if (is_array($categories_parent))
			{
				$categories_parent = array_values(array_unique($categories_parent));
			}

			// COPYRIGHT
			if ($copyright = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'copyright'))
			{
				$copyright_url = null;
				$copyright_label = null;
				if (isset($copyright[0]['attribs']['']['url']))
				{
					$copyright_url = $this->sanitize($copyright[0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				if (isset($copyright[0]['data']))
				{
					$copyright_label = $this->sanitize($copyright[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				$copyrights_parent = $this->registry->create('Copyright', array($copyright_url, $copyright_label));
			}
			elseif ($copyright = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'copyright'))
			{
				$copyright_url = null;
				$copyright_label = null;
				if (isset($copyright[0]['attribs']['']['url']))
				{
					$copyright_url = $this->sanitize($copyright[0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				if (isset($copyright[0]['data']))
				{
					$copyright_label = $this->sanitize($copyright[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
				$copyrights_parent = $this->registry->create('Copyright', array($copyright_url, $copyright_label));
			}

			// CREDITS
			if ($credits = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'credit'))
			{
				foreach ($credits as $credit)
				{
					$credit_role = null;
					$credit_scheme = null;
					$credit_name = null;
					if (isset($credit['attribs']['']['role']))
					{
						$credit_role = $this->sanitize($credit['attribs']['']['role'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($credit['attribs']['']['scheme']))
					{
						$credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					else
					{
						$credit_scheme = 'urn:ebu';
					}
					if (isset($credit['data']))
					{
						$credit_name = $this->sanitize($credit['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$credits_parent[] = $this->registry->create('Credit', array($credit_role, $credit_scheme, $credit_name));
				}
			}
			elseif ($credits = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'credit'))
			{
				foreach ($credits as $credit)
				{
					$credit_role = null;
					$credit_scheme = null;
					$credit_name = null;
					if (isset($credit['attribs']['']['role']))
					{
						$credit_role = $this->sanitize($credit['attribs']['']['role'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($credit['attribs']['']['scheme']))
					{
						$credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					else
					{
						$credit_scheme = 'urn:ebu';
					}
					if (isset($credit['data']))
					{
						$credit_name = $this->sanitize($credit['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$credits_parent[] = $this->registry->create('Credit', array($credit_role, $credit_scheme, $credit_name));
				}
			}
			if (is_array($credits_parent))
			{
				$credits_parent = array_values(array_unique($credits_parent));
			}

			// DESCRIPTION
			if ($description_parent = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'description'))
			{
				if (isset($description_parent[0]['data']))
				{
					$description_parent = $this->sanitize($description_parent[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
			}
			elseif ($description_parent = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'description'))
			{
				if (isset($description_parent[0]['data']))
				{
					$description_parent = $this->sanitize($description_parent[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
			}

			// DURATION
			if ($duration_parent = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'duration'))
			{
				$seconds = null;
				$minutes = null;
				$hours = null;
				if (isset($duration_parent[0]['data']))
				{
					$temp = explode(':', $this->sanitize($duration_parent[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT));
					if (sizeof($temp) > 0)
					{
						$seconds = (int) array_pop($temp);
					}
					if (sizeof($temp) > 0)
					{
						$minutes = (int) array_pop($temp);
						$seconds += $minutes * 60;
					}
					if (sizeof($temp) > 0)
					{
						$hours = (int) array_pop($temp);
						$seconds += $hours * 3600;
					}
					unset($temp);
					$duration_parent = $seconds;
				}
			}

			// HASHES
			if ($hashes_iterator = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'hash'))
			{
				foreach ($hashes_iterator as $hash)
				{
					$value = null;
					$algo = null;
					if (isset($hash['data']))
					{
						$value = $this->sanitize($hash['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($hash['attribs']['']['algo']))
					{
						$algo = $this->sanitize($hash['attribs']['']['algo'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					else
					{
						$algo = 'md5';
					}
					$hashes_parent[] = $algo.':'.$value;
				}
			}
			elseif ($hashes_iterator = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'hash'))
			{
				foreach ($hashes_iterator as $hash)
				{
					$value = null;
					$algo = null;
					if (isset($hash['data']))
					{
						$value = $this->sanitize($hash['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($hash['attribs']['']['algo']))
					{
						$algo = $this->sanitize($hash['attribs']['']['algo'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					else
					{
						$algo = 'md5';
					}
					$hashes_parent[] = $algo.':'.$value;
				}
			}
			if (is_array($hashes_parent))
			{
				$hashes_parent = array_values(array_unique($hashes_parent));
			}

			// KEYWORDS
			if ($keywords = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'keywords'))
			{
				if (isset($keywords[0]['data']))
				{
					$temp = explode(',', $this->sanitize($keywords[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT));
					foreach ($temp as $word)
					{
						$keywords_parent[] = trim($word);
					}
				}
				unset($temp);
			}
			elseif ($keywords = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'keywords'))
			{
				if (isset($keywords[0]['data']))
				{
					$temp = explode(',', $this->sanitize($keywords[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT));
					foreach ($temp as $word)
					{
						$keywords_parent[] = trim($word);
					}
				}
				unset($temp);
			}
			elseif ($keywords = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'keywords'))
			{
				if (isset($keywords[0]['data']))
				{
					$temp = explode(',', $this->sanitize($keywords[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT));
					foreach ($temp as $word)
					{
						$keywords_parent[] = trim($word);
					}
				}
				unset($temp);
			}
			elseif ($keywords = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'keywords'))
			{
				if (isset($keywords[0]['data']))
				{
					$temp = explode(',', $this->sanitize($keywords[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT));
					foreach ($temp as $word)
					{
						$keywords_parent[] = trim($word);
					}
				}
				unset($temp);
			}
			if (is_array($keywords_parent))
			{
				$keywords_parent = array_values(array_unique($keywords_parent));
			}

			// PLAYER
			if ($player_parent = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'player'))
			{
				if (isset($player_parent[0]['attribs']['']['url']))
				{
					$player_parent = $this->sanitize($player_parent[0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
				}
			}
			elseif ($player_parent = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'player'))
			{
				if (isset($player_parent[0]['attribs']['']['url']))
				{
					$player_parent = $this->sanitize($player_parent[0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
				}
			}

			// RATINGS
			if ($ratings = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'rating'))
			{
				foreach ($ratings as $rating)
				{
					$rating_scheme = null;
					$rating_value = null;
					if (isset($rating['attribs']['']['scheme']))
					{
						$rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					else
					{
						$rating_scheme = 'urn:simple';
					}
					if (isset($rating['data']))
					{
						$rating_value = $this->sanitize($rating['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$ratings_parent[] = $this->registry->create('Rating', array($rating_scheme, $rating_value));
				}
			}
			elseif ($ratings = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'explicit'))
			{
				foreach ($ratings as $rating)
				{
					$rating_scheme = 'urn:itunes';
					$rating_value = null;
					if (isset($rating['data']))
					{
						$rating_value = $this->sanitize($rating['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$ratings_parent[] = $this->registry->create('Rating', array($rating_scheme, $rating_value));
				}
			}
			elseif ($ratings = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'rating'))
			{
				foreach ($ratings as $rating)
				{
					$rating_scheme = null;
					$rating_value = null;
					if (isset($rating['attribs']['']['scheme']))
					{
						$rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					else
					{
						$rating_scheme = 'urn:simple';
					}
					if (isset($rating['data']))
					{
						$rating_value = $this->sanitize($rating['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$ratings_parent[] = $this->registry->create('Rating', array($rating_scheme, $rating_value));
				}
			}
			elseif ($ratings = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'explicit'))
			{
				foreach ($ratings as $rating)
				{
					$rating_scheme = 'urn:itunes';
					$rating_value = null;
					if (isset($rating['data']))
					{
						$rating_value = $this->sanitize($rating['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$ratings_parent[] = $this->registry->create('Rating', array($rating_scheme, $rating_value));
				}
			}
			if (is_array($ratings_parent))
			{
				$ratings_parent = array_values(array_unique($ratings_parent));
			}

			// RESTRICTIONS
			if ($restrictions = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'restriction'))
			{
				foreach ($restrictions as $restriction)
				{
					$restriction_relationship = null;
					$restriction_type = null;
					$restriction_value = null;
					if (isset($restriction['attribs']['']['relationship']))
					{
						$restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($restriction['attribs']['']['type']))
					{
						$restriction_type = $this->sanitize($restriction['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($restriction['data']))
					{
						$restriction_value = $this->sanitize($restriction['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$restrictions_parent[] = $this->registry->create('Restriction', array($restriction_relationship, $restriction_type, $restriction_value));
				}
			}
			elseif ($restrictions = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'block'))
			{
				foreach ($restrictions as $restriction)
				{
					$restriction_relationship = 'allow';
					$restriction_type = null;
					$restriction_value = 'itunes';
					if (isset($restriction['data']) && strtolower($restriction['data']) === 'yes')
					{
						$restriction_relationship = 'deny';
					}
					$restrictions_parent[] = $this->registry->create('Restriction', array($restriction_relationship, $restriction_type, $restriction_value));
				}
			}
			elseif ($restrictions = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'restriction'))
			{
				foreach ($restrictions as $restriction)
				{
					$restriction_relationship = null;
					$restriction_type = null;
					$restriction_value = null;
					if (isset($restriction['attribs']['']['relationship']))
					{
						$restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($restriction['attribs']['']['type']))
					{
						$restriction_type = $this->sanitize($restriction['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($restriction['data']))
					{
						$restriction_value = $this->sanitize($restriction['data'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					$restrictions_parent[] = $this->registry->create('Restriction', array($restriction_relationship, $restriction_type, $restriction_value));
				}
			}
			elseif ($restrictions = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'block'))
			{
				foreach ($restrictions as $restriction)
				{
					$restriction_relationship = 'allow';
					$restriction_type = null;
					$restriction_value = 'itunes';
					if (isset($restriction['data']) && strtolower($restriction['data']) === 'yes')
					{
						$restriction_relationship = 'deny';
					}
					$restrictions_parent[] = $this->registry->create('Restriction', array($restriction_relationship, $restriction_type, $restriction_value));
				}
			}
			if (is_array($restrictions_parent))
			{
				$restrictions_parent = array_values(array_unique($restrictions_parent));
			}
			else
			{
				$restrictions_parent = array(new SimplePie_Restriction('allow', null, 'default'));
			}

			// THUMBNAILS
			if ($thumbnails = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'thumbnail'))
			{
				foreach ($thumbnails as $thumbnail)
				{
					if (isset($thumbnail['attribs']['']['url']))
					{
						$thumbnails_parent[] = $this->sanitize($thumbnail['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
					}
				}
			}
			elseif ($thumbnails = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'thumbnail'))
			{
				foreach ($thumbnails as $thumbnail)
				{
					if (isset($thumbnail['attribs']['']['url']))
					{
						$thumbnails_parent[] = $this->sanitize($thumbnail['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
					}
				}
			}

			// TITLES
			if ($title_parent = $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'title'))
			{
				if (isset($title_parent[0]['data']))
				{
					$title_parent = $this->sanitize($title_parent[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
			}
			elseif ($title_parent = $parent->get_channel_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'title'))
			{
				if (isset($title_parent[0]['data']))
				{
					$title_parent = $this->sanitize($title_parent[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
				}
			}

			// Clear the memory
			unset($parent);

			// Attributes
			$bitrate = null;
			$channels = null;
			$duration = null;
			$expression = null;
			$framerate = null;
			$height = null;
			$javascript = null;
			$lang = null;
			$length = null;
			$medium = null;
			$samplingrate = null;
			$type = null;
			$url = null;
			$width = null;

			// Elements
			$captions = null;
			$categories = null;
			$copyrights = null;
			$credits = null;
			$description = null;
			$hashes = null;
			$keywords = null;
			$player = null;
			$ratings = null;
			$restrictions = null;
			$thumbnails = null;
			$title = null;

			// If we have media:group tags, loop through them.
			foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'group') as $group)
			{
				if(isset($group['child']) && isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['content']))
				{
					// If we have media:content tags, loop through them.
					foreach ((array) $group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['content'] as $content)
					{
						if (isset($content['attribs']['']['url']))
						{
							// Attributes
							$bitrate = null;
							$channels = null;
							$duration = null;
							$expression = null;
							$framerate = null;
							$height = null;
							$javascript = null;
							$lang = null;
							$length = null;
							$medium = null;
							$samplingrate = null;
							$type = null;
							$url = null;
							$width = null;

							// Elements
							$captions = null;
							$categories = null;
							$copyrights = null;
							$credits = null;
							$description = null;
							$hashes = null;
							$keywords = null;
							$player = null;
							$ratings = null;
							$restrictions = null;
							$thumbnails = null;
							$title = null;

							// Start checking the attributes of media:content
							if (isset($content['attribs']['']['bitrate']))
							{
								$bitrate = $this->sanitize($content['attribs']['']['bitrate'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['channels']))
							{
								$channels = $this->sanitize($content['attribs']['']['channels'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['duration']))
							{
								$duration = $this->sanitize($content['attribs']['']['duration'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							else
							{
								$duration = $duration_parent;
							}
							if (isset($content['attribs']['']['expression']))
							{
								$expression = $this->sanitize($content['attribs']['']['expression'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['framerate']))
							{
								$framerate = $this->sanitize($content['attribs']['']['framerate'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['height']))
							{
								$height = $this->sanitize($content['attribs']['']['height'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['lang']))
							{
								$lang = $this->sanitize($content['attribs']['']['lang'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['fileSize']))
							{
								$length = intval($content['attribs']['']['fileSize']);
							}
							if (isset($content['attribs']['']['medium']))
							{
								$medium = $this->sanitize($content['attribs']['']['medium'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['samplingrate']))
							{
								$samplingrate = $this->sanitize($content['attribs']['']['samplingrate'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['type']))
							{
								$type = $this->sanitize($content['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['attribs']['']['width']))
							{
								$width = $this->sanitize($content['attribs']['']['width'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							$url = $this->sanitize($content['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);

							// Checking the other optional media: elements. Priority: media:content, media:group, item, channel

							// CAPTIONS
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['text']))
							{
								foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['text'] as $caption)
								{
									$caption_type = null;
									$caption_lang = null;
									$caption_startTime = null;
									$caption_endTime = null;
									$caption_text = null;
									if (isset($caption['attribs']['']['type']))
									{
										$caption_type = $this->sanitize($caption['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($caption['attribs']['']['lang']))
									{
										$caption_lang = $this->sanitize($caption['attribs']['']['lang'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($caption['attribs']['']['start']))
									{
										$caption_startTime = $this->sanitize($caption['attribs']['']['start'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($caption['attribs']['']['end']))
									{
										$caption_endTime = $this->sanitize($caption['attribs']['']['end'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($caption['data']))
									{
										$caption_text = $this->sanitize($caption['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$captions[] = $this->registry->create('Caption', array($caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text));
								}
								if (is_array($captions))
								{
									$captions = array_values(array_unique($captions));
								}
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['text']))
							{
								foreach ($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['text'] as $caption)
								{
									$caption_type = null;
									$caption_lang = null;
									$caption_startTime = null;
									$caption_endTime = null;
									$caption_text = null;
									if (isset($caption['attribs']['']['type']))
									{
										$caption_type = $this->sanitize($caption['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($caption['attribs']['']['lang']))
									{
										$caption_lang = $this->sanitize($caption['attribs']['']['lang'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($caption['attribs']['']['start']))
									{
										$caption_startTime = $this->sanitize($caption['attribs']['']['start'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($caption['attribs']['']['end']))
									{
										$caption_endTime = $this->sanitize($caption['attribs']['']['end'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($caption['data']))
									{
										$caption_text = $this->sanitize($caption['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$captions[] = $this->registry->create('Caption', array($caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text));
								}
								if (is_array($captions))
								{
									$captions = array_values(array_unique($captions));
								}
							}
							else
							{
								$captions = $captions_parent;
							}

							// CATEGORIES
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['category']))
							{
								foreach ((array) $content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['category'] as $category)
								{
									$term = null;
									$scheme = null;
									$label = null;
									if (isset($category['data']))
									{
										$term = $this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($category['attribs']['']['scheme']))
									{
										$scheme = $this->sanitize($category['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									else
									{
										$scheme = 'http://search.yahoo.com/mrss/category_schema';
									}
									if (isset($category['attribs']['']['label']))
									{
										$label = $this->sanitize($category['attribs']['']['label'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$categories[] = $this->registry->create('Category', array($term, $scheme, $label));
								}
							}
							if (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['category']))
							{
								foreach ((array) $group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['category'] as $category)
								{
									$term = null;
									$scheme = null;
									$label = null;
									if (isset($category['data']))
									{
										$term = $this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($category['attribs']['']['scheme']))
									{
										$scheme = $this->sanitize($category['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									else
									{
										$scheme = 'http://search.yahoo.com/mrss/category_schema';
									}
									if (isset($category['attribs']['']['label']))
									{
										$label = $this->sanitize($category['attribs']['']['label'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$categories[] = $this->registry->create('Category', array($term, $scheme, $label));
								}
							}
							if (is_array($categories) && is_array($categories_parent))
							{
								$categories = array_values(array_unique(array_merge($categories, $categories_parent)));
							}
							elseif (is_array($categories))
							{
								$categories = array_values(array_unique($categories));
							}
							elseif (is_array($categories_parent))
							{
								$categories = array_values(array_unique($categories_parent));
							}

							// COPYRIGHTS
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright']))
							{
								$copyright_url = null;
								$copyright_label = null;
								if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url']))
								{
									$copyright_url = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['data']))
								{
									$copyright_label = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								$copyrights = $this->registry->create('Copyright', array($copyright_url, $copyright_label));
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright']))
							{
								$copyright_url = null;
								$copyright_label = null;
								if (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url']))
								{
									$copyright_url = $this->sanitize($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['data']))
								{
									$copyright_label = $this->sanitize($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								$copyrights = $this->registry->create('Copyright', array($copyright_url, $copyright_label));
							}
							else
							{
								$copyrights = $copyrights_parent;
							}

							// CREDITS
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['credit']))
							{
								foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['credit'] as $credit)
								{
									$credit_role = null;
									$credit_scheme = null;
									$credit_name = null;
									if (isset($credit['attribs']['']['role']))
									{
										$credit_role = $this->sanitize($credit['attribs']['']['role'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($credit['attribs']['']['scheme']))
									{
										$credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									else
									{
										$credit_scheme = 'urn:ebu';
									}
									if (isset($credit['data']))
									{
										$credit_name = $this->sanitize($credit['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$credits[] = $this->registry->create('Credit', array($credit_role, $credit_scheme, $credit_name));
								}
								if (is_array($credits))
								{
									$credits = array_values(array_unique($credits));
								}
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['credit']))
							{
								foreach ($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['credit'] as $credit)
								{
									$credit_role = null;
									$credit_scheme = null;
									$credit_name = null;
									if (isset($credit['attribs']['']['role']))
									{
										$credit_role = $this->sanitize($credit['attribs']['']['role'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($credit['attribs']['']['scheme']))
									{
										$credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									else
									{
										$credit_scheme = 'urn:ebu';
									}
									if (isset($credit['data']))
									{
										$credit_name = $this->sanitize($credit['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$credits[] = $this->registry->create('Credit', array($credit_role, $credit_scheme, $credit_name));
								}
								if (is_array($credits))
								{
									$credits = array_values(array_unique($credits));
								}
							}
							else
							{
								$credits = $credits_parent;
							}

							// DESCRIPTION
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['description']))
							{
								$description = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['description'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['description']))
							{
								$description = $this->sanitize($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['description'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							else
							{
								$description = $description_parent;
							}

							// HASHES
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['hash']))
							{
								foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['hash'] as $hash)
								{
									$value = null;
									$algo = null;
									if (isset($hash['data']))
									{
										$value = $this->sanitize($hash['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($hash['attribs']['']['algo']))
									{
										$algo = $this->sanitize($hash['attribs']['']['algo'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									else
									{
										$algo = 'md5';
									}
									$hashes[] = $algo.':'.$value;
								}
								if (is_array($hashes))
								{
									$hashes = array_values(array_unique($hashes));
								}
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['hash']))
							{
								foreach ($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['hash'] as $hash)
								{
									$value = null;
									$algo = null;
									if (isset($hash['data']))
									{
										$value = $this->sanitize($hash['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($hash['attribs']['']['algo']))
									{
										$algo = $this->sanitize($hash['attribs']['']['algo'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									else
									{
										$algo = 'md5';
									}
									$hashes[] = $algo.':'.$value;
								}
								if (is_array($hashes))
								{
									$hashes = array_values(array_unique($hashes));
								}
							}
							else
							{
								$hashes = $hashes_parent;
							}

							// KEYWORDS
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords']))
							{
								if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords'][0]['data']))
								{
									$temp = explode(',', $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT));
									foreach ($temp as $word)
									{
										$keywords[] = trim($word);
									}
									unset($temp);
								}
								if (is_array($keywords))
								{
									$keywords = array_values(array_unique($keywords));
								}
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords']))
							{
								if (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords'][0]['data']))
								{
									$temp = explode(',', $this->sanitize($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT));
									foreach ($temp as $word)
									{
										$keywords[] = trim($word);
									}
									unset($temp);
								}
								if (is_array($keywords))
								{
									$keywords = array_values(array_unique($keywords));
								}
							}
							else
							{
								$keywords = $keywords_parent;
							}

							// PLAYER
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['player']))
							{
								$player = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['player'][0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['player']))
							{
								$player = $this->sanitize($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['player'][0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
							}
							else
							{
								$player = $player_parent;
							}

							// RATINGS
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['rating']))
							{
								foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['rating'] as $rating)
								{
									$rating_scheme = null;
									$rating_value = null;
									if (isset($rating['attribs']['']['scheme']))
									{
										$rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									else
									{
										$rating_scheme = 'urn:simple';
									}
									if (isset($rating['data']))
									{
										$rating_value = $this->sanitize($rating['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$ratings[] = $this->registry->create('Rating', array($rating_scheme, $rating_value));
								}
								if (is_array($ratings))
								{
									$ratings = array_values(array_unique($ratings));
								}
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['rating']))
							{
								foreach ($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['rating'] as $rating)
								{
									$rating_scheme = null;
									$rating_value = null;
									if (isset($rating['attribs']['']['scheme']))
									{
										$rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									else
									{
										$rating_scheme = 'urn:simple';
									}
									if (isset($rating['data']))
									{
										$rating_value = $this->sanitize($rating['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$ratings[] = $this->registry->create('Rating', array($rating_scheme, $rating_value));
								}
								if (is_array($ratings))
								{
									$ratings = array_values(array_unique($ratings));
								}
							}
							else
							{
								$ratings = $ratings_parent;
							}

							// RESTRICTIONS
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['restriction']))
							{
								foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['restriction'] as $restriction)
								{
									$restriction_relationship = null;
									$restriction_type = null;
									$restriction_value = null;
									if (isset($restriction['attribs']['']['relationship']))
									{
										$restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($restriction['attribs']['']['type']))
									{
										$restriction_type = $this->sanitize($restriction['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($restriction['data']))
									{
										$restriction_value = $this->sanitize($restriction['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$restrictions[] = $this->registry->create('Restriction', array($restriction_relationship, $restriction_type, $restriction_value));
								}
								if (is_array($restrictions))
								{
									$restrictions = array_values(array_unique($restrictions));
								}
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['restriction']))
							{
								foreach ($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['restriction'] as $restriction)
								{
									$restriction_relationship = null;
									$restriction_type = null;
									$restriction_value = null;
									if (isset($restriction['attribs']['']['relationship']))
									{
										$restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($restriction['attribs']['']['type']))
									{
										$restriction_type = $this->sanitize($restriction['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									if (isset($restriction['data']))
									{
										$restriction_value = $this->sanitize($restriction['data'], SIMPLEPIE_CONSTRUCT_TEXT);
									}
									$restrictions[] = $this->registry->create('Restriction', array($restriction_relationship, $restriction_type, $restriction_value));
								}
								if (is_array($restrictions))
								{
									$restrictions = array_values(array_unique($restrictions));
								}
							}
							else
							{
								$restrictions = $restrictions_parent;
							}

							// THUMBNAILS
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['thumbnail']))
							{
								foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['thumbnail'] as $thumbnail)
								{
									$thumbnails[] = $this->sanitize($thumbnail['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
								}
								if (is_array($thumbnails))
								{
									$thumbnails = array_values(array_unique($thumbnails));
								}
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['thumbnail']))
							{
								foreach ($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['thumbnail'] as $thumbnail)
								{
									$thumbnails[] = $this->sanitize($thumbnail['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
								}
								if (is_array($thumbnails))
								{
									$thumbnails = array_values(array_unique($thumbnails));
								}
							}
							else
							{
								$thumbnails = $thumbnails_parent;
							}

							// TITLES
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['title']))
							{
								$title = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['title'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							elseif (isset($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['title']))
							{
								$title = $this->sanitize($group['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['title'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							else
							{
								$title = $title_parent;
							}

							$this->data['enclosures'][] = $this->registry->create('Enclosure', array($url, $type, $length, null, $bitrate, $captions, $categories, $channels, $copyrights, $credits, $description, $duration, $expression, $framerate, $hashes, $height, $keywords, $lang, $medium, $player, $ratings, $restrictions, $samplingrate, $thumbnails, $title, $width));
						}
					}
				}
			}

			// If we have standalone media:content tags, loop through them.
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['content']))
			{
				foreach ((array) $this->data['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['content'] as $content)
				{
					if (isset($content['attribs']['']['url']) || isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['player']))
					{
						// Attributes
						$bitrate = null;
						$channels = null;
						$duration = null;
						$expression = null;
						$framerate = null;
						$height = null;
						$javascript = null;
						$lang = null;
						$length = null;
						$medium = null;
						$samplingrate = null;
						$type = null;
						$url = null;
						$width = null;

						// Elements
						$captions = null;
						$categories = null;
						$copyrights = null;
						$credits = null;
						$description = null;
						$hashes = null;
						$keywords = null;
						$player = null;
						$ratings = null;
						$restrictions = null;
						$thumbnails = null;
						$title = null;

						// Start checking the attributes of media:content
						if (isset($content['attribs']['']['bitrate']))
						{
							$bitrate = $this->sanitize($content['attribs']['']['bitrate'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['channels']))
						{
							$channels = $this->sanitize($content['attribs']['']['channels'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['duration']))
						{
							$duration = $this->sanitize($content['attribs']['']['duration'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						else
						{
							$duration = $duration_parent;
						}
						if (isset($content['attribs']['']['expression']))
						{
							$expression = $this->sanitize($content['attribs']['']['expression'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['framerate']))
						{
							$framerate = $this->sanitize($content['attribs']['']['framerate'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['height']))
						{
							$height = $this->sanitize($content['attribs']['']['height'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['lang']))
						{
							$lang = $this->sanitize($content['attribs']['']['lang'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['fileSize']))
						{
							$length = intval($content['attribs']['']['fileSize']);
						}
						if (isset($content['attribs']['']['medium']))
						{
							$medium = $this->sanitize($content['attribs']['']['medium'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['samplingrate']))
						{
							$samplingrate = $this->sanitize($content['attribs']['']['samplingrate'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['type']))
						{
							$type = $this->sanitize($content['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['width']))
						{
							$width = $this->sanitize($content['attribs']['']['width'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						if (isset($content['attribs']['']['url']))
						{
							$url = $this->sanitize($content['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
						}
						// Checking the other optional media: elements. Priority: media:content, media:group, item, channel

						// CAPTIONS
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['text']))
						{
							foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['text'] as $caption)
							{
								$caption_type = null;
								$caption_lang = null;
								$caption_startTime = null;
								$caption_endTime = null;
								$caption_text = null;
								if (isset($caption['attribs']['']['type']))
								{
									$caption_type = $this->sanitize($caption['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($caption['attribs']['']['lang']))
								{
									$caption_lang = $this->sanitize($caption['attribs']['']['lang'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($caption['attribs']['']['start']))
								{
									$caption_startTime = $this->sanitize($caption['attribs']['']['start'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($caption['attribs']['']['end']))
								{
									$caption_endTime = $this->sanitize($caption['attribs']['']['end'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($caption['data']))
								{
									$caption_text = $this->sanitize($caption['data'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								$captions[] = $this->registry->create('Caption', array($caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text));
							}
							if (is_array($captions))
							{
								$captions = array_values(array_unique($captions));
							}
						}
						else
						{
							$captions = $captions_parent;
						}

						// CATEGORIES
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['category']))
						{
							foreach ((array) $content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['category'] as $category)
							{
								$term = null;
								$scheme = null;
								$label = null;
								if (isset($category['data']))
								{
									$term = $this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($category['attribs']['']['scheme']))
								{
									$scheme = $this->sanitize($category['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								else
								{
									$scheme = 'http://search.yahoo.com/mrss/category_schema';
								}
								if (isset($category['attribs']['']['label']))
								{
									$label = $this->sanitize($category['attribs']['']['label'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								$categories[] = $this->registry->create('Category', array($term, $scheme, $label));
							}
						}
						if (is_array($categories) && is_array($categories_parent))
						{
							$categories = array_values(array_unique(array_merge($categories, $categories_parent)));
						}
						elseif (is_array($categories))
						{
							$categories = array_values(array_unique($categories));
						}
						elseif (is_array($categories_parent))
						{
							$categories = array_values(array_unique($categories_parent));
						}
						else
						{
							$categories = null;
						}

						// COPYRIGHTS
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright']))
						{
							$copyright_url = null;
							$copyright_label = null;
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url']))
							{
								$copyright_url = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['data']))
							{
								$copyright_label = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['copyright'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
							}
							$copyrights = $this->registry->create('Copyright', array($copyright_url, $copyright_label));
						}
						else
						{
							$copyrights = $copyrights_parent;
						}

						// CREDITS
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['credit']))
						{
							foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['credit'] as $credit)
							{
								$credit_role = null;
								$credit_scheme = null;
								$credit_name = null;
								if (isset($credit['attribs']['']['role']))
								{
									$credit_role = $this->sanitize($credit['attribs']['']['role'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($credit['attribs']['']['scheme']))
								{
									$credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								else
								{
									$credit_scheme = 'urn:ebu';
								}
								if (isset($credit['data']))
								{
									$credit_name = $this->sanitize($credit['data'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								$credits[] = $this->registry->create('Credit', array($credit_role, $credit_scheme, $credit_name));
							}
							if (is_array($credits))
							{
								$credits = array_values(array_unique($credits));
							}
						}
						else
						{
							$credits = $credits_parent;
						}

						// DESCRIPTION
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['description']))
						{
							$description = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['description'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						else
						{
							$description = $description_parent;
						}

						// HASHES
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['hash']))
						{
							foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['hash'] as $hash)
							{
								$value = null;
								$algo = null;
								if (isset($hash['data']))
								{
									$value = $this->sanitize($hash['data'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($hash['attribs']['']['algo']))
								{
									$algo = $this->sanitize($hash['attribs']['']['algo'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								else
								{
									$algo = 'md5';
								}
								$hashes[] = $algo.':'.$value;
							}
							if (is_array($hashes))
							{
								$hashes = array_values(array_unique($hashes));
							}
						}
						else
						{
							$hashes = $hashes_parent;
						}

						// KEYWORDS
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords']))
						{
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords'][0]['data']))
							{
								$temp = explode(',', $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['keywords'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT));
								foreach ($temp as $word)
								{
									$keywords[] = trim($word);
								}
								unset($temp);
							}
							if (is_array($keywords))
							{
								$keywords = array_values(array_unique($keywords));
							}
						}
						else
						{
							$keywords = $keywords_parent;
						}

						// PLAYER
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['player']))
						{
							if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['player'][0]['attribs']['']['url'])) {
								$player = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['player'][0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
							}
						}
						else
						{
							$player = $player_parent;
						}

						// RATINGS
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['rating']))
						{
							foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['rating'] as $rating)
							{
								$rating_scheme = null;
								$rating_value = null;
								if (isset($rating['attribs']['']['scheme']))
								{
									$rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								else
								{
									$rating_scheme = 'urn:simple';
								}
								if (isset($rating['data']))
								{
									$rating_value = $this->sanitize($rating['data'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								$ratings[] = $this->registry->create('Rating', array($rating_scheme, $rating_value));
							}
							if (is_array($ratings))
							{
								$ratings = array_values(array_unique($ratings));
							}
						}
						else
						{
							$ratings = $ratings_parent;
						}

						// RESTRICTIONS
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['restriction']))
						{
							foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['restriction'] as $restriction)
							{
								$restriction_relationship = null;
								$restriction_type = null;
								$restriction_value = null;
								if (isset($restriction['attribs']['']['relationship']))
								{
									$restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($restriction['attribs']['']['type']))
								{
									$restriction_type = $this->sanitize($restriction['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								if (isset($restriction['data']))
								{
									$restriction_value = $this->sanitize($restriction['data'], SIMPLEPIE_CONSTRUCT_TEXT);
								}
								$restrictions[] = $this->registry->create('Restriction', array($restriction_relationship, $restriction_type, $restriction_value));
							}
							if (is_array($restrictions))
							{
								$restrictions = array_values(array_unique($restrictions));
							}
						}
						else
						{
							$restrictions = $restrictions_parent;
						}

						// THUMBNAILS
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['thumbnail']))
						{
							foreach ($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['thumbnail'] as $thumbnail)
							{
								if (isset($thumbnail['attribs']['']['url'])) {
									$thumbnails[] = $this->sanitize($thumbnail['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI);
								}
							}
							if (is_array($thumbnails))
							{
								$thumbnails = array_values(array_unique($thumbnails));
							}
						}
						else
						{
							$thumbnails = $thumbnails_parent;
						}

						// TITLES
						if (isset($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['title']))
						{
							$title = $this->sanitize($content['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['title'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
						}
						else
						{
							$title = $title_parent;
						}

						$this->data['enclosures'][] = $this->registry->create('Enclosure', array($url, $type, $length, null, $bitrate, $captions, $categories, $channels, $copyrights, $credits, $description, $duration, $expression, $framerate, $hashes, $height, $keywords, $lang, $medium, $player, $ratings, $restrictions, $samplingrate, $thumbnails, $title, $width));
					}
				}
			}

			foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'link') as $link)
			{
				if (isset($link['attribs']['']['href']) && !empty($link['attribs']['']['rel']) && $link['attribs']['']['rel'] === 'enclosure')
				{
					// Attributes
					$bitrate = null;
					$channels = null;
					$duration = null;
					$expression = null;
					$framerate = null;
					$height = null;
					$javascript = null;
					$lang = null;
					$length = null;
					$medium = null;
					$samplingrate = null;
					$type = null;
					$url = null;
					$width = null;

					$url = $this->sanitize($link['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($link));
					if (isset($link['attribs']['']['type']))
					{
						$type = $this->sanitize($link['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($link['attribs']['']['length']))
					{
						$length = intval($link['attribs']['']['length']);
					}
					if (isset($link['attribs']['']['title']))
					{
						$title = $this->sanitize($link['attribs']['']['title'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					else
					{
						$title = $title_parent;
					}

					// Since we don't have group or content for these, we'll just pass the '*_parent' variables directly to the constructor
					$this->data['enclosures'][] = $this->registry->create('Enclosure', array($url, $type, $length, null, $bitrate, $captions_parent, $categories_parent, $channels, $copyrights_parent, $credits_parent, $description_parent, $duration_parent, $expression, $framerate, $hashes_parent, $height, $keywords_parent, $lang, $medium, $player_parent, $ratings_parent, $restrictions_parent, $samplingrate, $thumbnails_parent, $title, $width));
				}
			}

			foreach ((array) $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'link') as $link)
			{
				if (isset($link['attribs']['']['href']) && !empty($link['attribs']['']['rel']) && $link['attribs']['']['rel'] === 'enclosure')
				{
					// Attributes
					$bitrate = null;
					$channels = null;
					$duration = null;
					$expression = null;
					$framerate = null;
					$height = null;
					$javascript = null;
					$lang = null;
					$length = null;
					$medium = null;
					$samplingrate = null;
					$type = null;
					$url = null;
					$width = null;

					$url = $this->sanitize($link['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($link));
					if (isset($link['attribs']['']['type']))
					{
						$type = $this->sanitize($link['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($link['attribs']['']['length']))
					{
						$length = intval($link['attribs']['']['length']);
					}

					// Since we don't have group or content for these, we'll just pass the '*_parent' variables directly to the constructor
					$this->data['enclosures'][] = $this->registry->create('Enclosure', array($url, $type, $length, null, $bitrate, $captions_parent, $categories_parent, $channels, $copyrights_parent, $credits_parent, $description_parent, $duration_parent, $expression, $framerate, $hashes_parent, $height, $keywords_parent, $lang, $medium, $player_parent, $ratings_parent, $restrictions_parent, $samplingrate, $thumbnails_parent, $title_parent, $width));
				}
			}

			if ($enclosure = $this->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'enclosure'))
			{
				if (isset($enclosure[0]['attribs']['']['url']))
				{
					// Attributes
					$bitrate = null;
					$channels = null;
					$duration = null;
					$expression = null;
					$framerate = null;
					$height = null;
					$javascript = null;
					$lang = null;
					$length = null;
					$medium = null;
					$samplingrate = null;
					$type = null;
					$url = null;
					$width = null;

					$url = $this->sanitize($enclosure[0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($enclosure[0]));
					$url = $this->feed->sanitize->https_url($url);
					if (isset($enclosure[0]['attribs']['']['type']))
					{
						$type = $this->sanitize($enclosure[0]['attribs']['']['type'], SIMPLEPIE_CONSTRUCT_TEXT);
					}
					if (isset($enclosure[0]['attribs']['']['length']))
					{
						$length = intval($enclosure[0]['attribs']['']['length']);
					}

					// Since we don't have group or content for these, we'll just pass the '*_parent' variables directly to the constructor
					$this->data['enclosures'][] = $this->registry->create('Enclosure', array($url, $type, $length, null, $bitrate, $captions_parent, $categories_parent, $channels, $copyrights_parent, $credits_parent, $description_parent, $duration_parent, $expression, $framerate, $hashes_parent, $height, $keywords_parent, $lang, $medium, $player_parent, $ratings_parent, $restrictions_parent, $samplingrate, $thumbnails_parent, $title_parent, $width));
				}
			}

			if (sizeof($this->data['enclosures']) === 0 && ($url || $type || $length || $bitrate || $captions_parent || $categories_parent || $channels || $copyrights_parent || $credits_parent || $description_parent || $duration_parent || $expression || $framerate || $hashes_parent || $height || $keywords_parent || $lang || $medium || $player_parent || $ratings_parent || $restrictions_parent || $samplingrate || $thumbnails_parent || $title_parent || $width))
			{
				// Since we don't have group or content for these, we'll just pass the '*_parent' variables directly to the constructor
				$this->data['enclosures'][] = $this->registry->create('Enclosure', array($url, $type, $length, null, $bitrate, $captions_parent, $categories_parent, $channels, $copyrights_parent, $credits_parent, $description_parent, $duration_parent, $expression, $framerate, $hashes_parent, $height, $keywords_parent, $lang, $medium, $player_parent, $ratings_parent, $restrictions_parent, $samplingrate, $thumbnails_parent, $title_parent, $width));
			}

			$this->data['enclosures'] = array_values(array_unique($this->data['enclosures']));
		}
		if (!empty($this->data['enclosures']))
		{
			return $this->data['enclosures'];
		}

		return null;
	}

	/**
	 * Get the latitude coordinates for the item
	 *
	 * Compatible with the W3C WGS84 Basic Geo and GeoRSS specifications
	 *
	 * Uses `<geo:lat>` or `<georss:point>`
	 *
	 * @since 1.0
	 * @link http://www.w3.org/2003/01/geo/ W3C WGS84 Basic Geo
	 * @link http://www.georss.org/ GeoRSS
	 * @return string|null
	 */
	public function get_latitude()
	{
		if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO, 'lat'))
		{
			return (float) $return[0]['data'];
		}
		elseif (($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_GEORSS, 'point')) && preg_match('/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim($return[0]['data']), $match))
		{
			return (float) $match[1];
		}

		return null;
	}

	/**
	 * Get the longitude coordinates for the item
	 *
	 * Compatible with the W3C WGS84 Basic Geo and GeoRSS specifications
	 *
	 * Uses `<geo:long>`, `<geo:lon>` or `<georss:point>`
	 *
	 * @since 1.0
	 * @link http://www.w3.org/2003/01/geo/ W3C WGS84 Basic Geo
	 * @link http://www.georss.org/ GeoRSS
	 * @return string|null
	 */
	public function get_longitude()
	{
		if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO, 'long'))
		{
			return (float) $return[0]['data'];
		}
		elseif ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO, 'lon'))
		{
			return (float) $return[0]['data'];
		}
		elseif (($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_GEORSS, 'point')) && preg_match('/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim($return[0]['data']), $match))
		{
			return (float) $match[2];
		}

		return null;
	}

	/**
	 * Get the `<atom:source>` for the item
	 *
	 * @since 1.1
	 * @return SimplePie_Source|null
	 */
	public function get_source()
	{
		if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'source'))
		{
			return $this->registry->create('Source', array($this, $return[0]));
		}

		return null;
	}
}
