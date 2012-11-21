<?php
/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2012, Ryan Parman, Geoffrey Sneddon, Ryan McCue, and contributors
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
 * @version 1.3.1
 * @copyright 2004-2012 Ryan Parman, Geoffrey Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Geoffrey Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Handles everything related to enclosures (including Media RSS and iTunes RSS)
 *
 * Used by {@see SimplePie_Item::get_enclosure()} and {@see SimplePie_Item::get_enclosures()}
 *
 * This class can be overloaded with {@see SimplePie::set_enclosure_class()}
 *
 * @package SimplePie
 * @subpackage API
 */
class SimplePie_Enclosure
{
	/**
	 * @var string
	 * @see get_bitrate()
	 */
	var $bitrate;

	/**
	 * @var array
	 * @see get_captions()
	 */
	var $captions;

	/**
	 * @var array
	 * @see get_categories()
	 */
	var $categories;

	/**
	 * @var int
	 * @see get_channels()
	 */
	var $channels;

	/**
	 * @var SimplePie_Copyright
	 * @see get_copyright()
	 */
	var $copyright;

	/**
	 * @var array
	 * @see get_credits()
	 */
	var $credits;

	/**
	 * @var string
	 * @see get_description()
	 */
	var $description;

	/**
	 * @var int
	 * @see get_duration()
	 */
	var $duration;

	/**
	 * @var string
	 * @see get_expression()
	 */
	var $expression;

	/**
	 * @var string
	 * @see get_framerate()
	 */
	var $framerate;

	/**
	 * @var string
	 * @see get_handler()
	 */
	var $handler;

	/**
	 * @var array
	 * @see get_hashes()
	 */
	var $hashes;

	/**
	 * @var string
	 * @see get_height()
	 */
	var $height;

	/**
	 * @deprecated
	 * @var null
	 */
	var $javascript;

	/**
	 * @var array
	 * @see get_keywords()
	 */
	var $keywords;

	/**
	 * @var string
	 * @see get_language()
	 */
	var $lang;

	/**
	 * @var string
	 * @see get_length()
	 */
	var $length;

	/**
	 * @var string
	 * @see get_link()
	 */
	var $link;

	/**
	 * @var string
	 * @see get_medium()
	 */
	var $medium;

	/**
	 * @var string
	 * @see get_player()
	 */
	var $player;

	/**
	 * @var array
	 * @see get_ratings()
	 */
	var $ratings;

	/**
	 * @var array
	 * @see get_restrictions()
	 */
	var $restrictions;

	/**
	 * @var string
	 * @see get_sampling_rate()
	 */
	var $samplingrate;

	/**
	 * @var array
	 * @see get_thumbnails()
	 */
	var $thumbnails;

	/**
	 * @var string
	 * @see get_title()
	 */
	var $title;

	/**
	 * @var string
	 * @see get_type()
	 */
	var $type;

	/**
	 * @var string
	 * @see get_width()
	 */
	var $width;

	/**
	 * Constructor, used to input the data
	 *
	 * For documentation on all the parameters, see the corresponding
	 * properties and their accessors
	 *
	 * @uses idna_convert If available, this will convert an IDN
	 */
	public function __construct($link = null, $type = null, $length = null, $javascript = null, $bitrate = null, $captions = null, $categories = null, $channels = null, $copyright = null, $credits = null, $description = null, $duration = null, $expression = null, $framerate = null, $hashes = null, $height = null, $keywords = null, $lang = null, $medium = null, $player = null, $ratings = null, $restrictions = null, $samplingrate = null, $thumbnails = null, $title = null, $width = null)
	{
		$this->bitrate = $bitrate;
		$this->captions = $captions;
		$this->categories = $categories;
		$this->channels = $channels;
		$this->copyright = $copyright;
		$this->credits = $credits;
		$this->description = $description;
		$this->duration = $duration;
		$this->expression = $expression;
		$this->framerate = $framerate;
		$this->hashes = $hashes;
		$this->height = $height;
		$this->keywords = $keywords;
		$this->lang = $lang;
		$this->length = $length;
		$this->link = $link;
		$this->medium = $medium;
		$this->player = $player;
		$this->ratings = $ratings;
		$this->restrictions = $restrictions;
		$this->samplingrate = $samplingrate;
		$this->thumbnails = $thumbnails;
		$this->title = $title;
		$this->type = $type;
		$this->width = $width;

		if (class_exists('idna_convert'))
		{
			$idn = new idna_convert();
			$parsed = SimplePie_Misc::parse_url($link);
			$this->link = SimplePie_Misc::compress_parse_url($parsed['scheme'], $idn->encode($parsed['authority']), $parsed['path'], $parsed['query'], $parsed['fragment']);
		}
		$this->handler = $this->get_handler(); // Needs to load last
	}

	/**
	 * String-ified version
	 *
	 * @return string
	 */
	public function __toString()
	{
		// There is no $this->data here
		return md5(serialize($this));
	}

	/**
	 * Get the bitrate
	 *
	 * @return string|null
	 */
	public function get_bitrate()
	{
		if ($this->bitrate !== null)
		{
			return $this->bitrate;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get a single caption
	 *
	 * @param int $key
	 * @return SimplePie_Caption|null
	 */
	public function get_caption($key = 0)
	{
		$captions = $this->get_captions();
		if (isset($captions[$key]))
		{
			return $captions[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get all captions
	 *
	 * @return array|null Array of {@see SimplePie_Caption} objects
	 */
	public function get_captions()
	{
		if ($this->captions !== null)
		{
			return $this->captions;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get a single category
	 *
	 * @param int $key
	 * @return SimplePie_Category|null
	 */
	public function get_category($key = 0)
	{
		$categories = $this->get_categories();
		if (isset($categories[$key]))
		{
			return $categories[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get all categories
	 *
	 * @return array|null Array of {@see SimplePie_Category} objects
	 */
	public function get_categories()
	{
		if ($this->categories !== null)
		{
			return $this->categories;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the number of audio channels
	 *
	 * @return int|null
	 */
	public function get_channels()
	{
		if ($this->channels !== null)
		{
			return $this->channels;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the copyright information
	 *
	 * @return SimplePie_Copyright|null
	 */
	public function get_copyright()
	{
		if ($this->copyright !== null)
		{
			return $this->copyright;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get a single credit
	 *
	 * @param int $key
	 * @return SimplePie_Credit|null
	 */
	public function get_credit($key = 0)
	{
		$credits = $this->get_credits();
		if (isset($credits[$key]))
		{
			return $credits[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get all credits
	 *
	 * @return array|null Array of {@see SimplePie_Credit} objects
	 */
	public function get_credits()
	{
		if ($this->credits !== null)
		{
			return $this->credits;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the description of the enclosure
	 *
	 * @return string|null
	 */
	public function get_description()
	{
		if ($this->description !== null)
		{
			return $this->description;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the duration of the enclosure
	 *
	 * @param string $convert Convert seconds into hh:mm:ss
	 * @return string|int|null 'hh:mm:ss' string if `$convert` was specified, otherwise integer (or null if none found)
	 */
	public function get_duration($convert = false)
	{
		if ($this->duration !== null)
		{
			if ($convert)
			{
				$time = SimplePie_Misc::time_hms($this->duration);
				return $time;
			}
			else
			{
				return $this->duration;
			}
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the expression
	 *
	 * @return string Probably one of 'sample', 'full', 'nonstop', 'clip'. Defaults to 'full'
	 */
	public function get_expression()
	{
		if ($this->expression !== null)
		{
			return $this->expression;
		}
		else
		{
			return 'full';
		}
	}

	/**
	 * Get the file extension
	 *
	 * @return string|null
	 */
	public function get_extension()
	{
		if ($this->link !== null)
		{
			$url = SimplePie_Misc::parse_url($this->link);
			if ($url['path'] !== '')
			{
				return pathinfo($url['path'], PATHINFO_EXTENSION);
			}
		}
		return null;
	}

	/**
	 * Get the framerate (in frames-per-second)
	 *
	 * @return string|null
	 */
	public function get_framerate()
	{
		if ($this->framerate !== null)
		{
			return $this->framerate;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the preferred handler
	 *
	 * @return string|null One of 'flash', 'fmedia', 'quicktime', 'wmedia', 'mp3'
	 */
	public function get_handler()
	{
		return $this->get_real_type(true);
	}

	/**
	 * Get a single hash
	 *
	 * @link http://www.rssboard.org/media-rss#media-hash
	 * @param int $key
	 * @return string|null Hash as per `media:hash`, prefixed with "$algo:"
	 */
	public function get_hash($key = 0)
	{
		$hashes = $this->get_hashes();
		if (isset($hashes[$key]))
		{
			return $hashes[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get all credits
	 *
	 * @return array|null Array of strings, see {@see get_hash()}
	 */
	public function get_hashes()
	{
		if ($this->hashes !== null)
		{
			return $this->hashes;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the height
	 *
	 * @return string|null
	 */
	public function get_height()
	{
		if ($this->height !== null)
		{
			return $this->height;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the language
	 *
	 * @link http://tools.ietf.org/html/rfc3066
	 * @return string|null Language code as per RFC 3066
	 */
	public function get_language()
	{
		if ($this->lang !== null)
		{
			return $this->lang;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get a single keyword
	 *
	 * @param int $key
	 * @return string|null
	 */
	public function get_keyword($key = 0)
	{
		$keywords = $this->get_keywords();
		if (isset($keywords[$key]))
		{
			return $keywords[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get all keywords
	 *
	 * @return array|null Array of strings
	 */
	public function get_keywords()
	{
		if ($this->keywords !== null)
		{
			return $this->keywords;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get length
	 *
	 * @return float Length in bytes
	 */
	public function get_length()
	{
		if ($this->length !== null)
		{
			return $this->length;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the URL
	 *
	 * @return string|null
	 */
	public function get_link()
	{
		if ($this->link !== null)
		{
			return urldecode($this->link);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the medium
	 *
	 * @link http://www.rssboard.org/media-rss#media-content
	 * @return string|null Should be one of 'image', 'audio', 'video', 'document', 'executable'
	 */
	public function get_medium()
	{
		if ($this->medium !== null)
		{
			return $this->medium;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the player URL
	 *
	 * Typically the same as {@see get_permalink()}
	 * @return string|null Player URL
	 */
	public function get_player()
	{
		if ($this->player !== null)
		{
			return $this->player;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get a single rating
	 *
	 * @param int $key
	 * @return SimplePie_Rating|null
	 */
	public function get_rating($key = 0)
	{
		$ratings = $this->get_ratings();
		if (isset($ratings[$key]))
		{
			return $ratings[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get all ratings
	 *
	 * @return array|null Array of {@see SimplePie_Rating} objects
	 */
	public function get_ratings()
	{
		if ($this->ratings !== null)
		{
			return $this->ratings;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get a single restriction
	 *
	 * @param int $key
	 * @return SimplePie_Restriction|null
	 */
	public function get_restriction($key = 0)
	{
		$restrictions = $this->get_restrictions();
		if (isset($restrictions[$key]))
		{
			return $restrictions[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get all restrictions
	 *
	 * @return array|null Array of {@see SimplePie_Restriction} objects
	 */
	public function get_restrictions()
	{
		if ($this->restrictions !== null)
		{
			return $this->restrictions;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the sampling rate (in kHz)
	 *
	 * @return string|null
	 */
	public function get_sampling_rate()
	{
		if ($this->samplingrate !== null)
		{
			return $this->samplingrate;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the file size (in MiB)
	 *
	 * @return float|null File size in mebibytes (1048 bytes)
	 */
	public function get_size()
	{
		$length = $this->get_length();
		if ($length !== null)
		{
			return round($length/1048576, 2);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get a single thumbnail
	 *
	 * @param int $key
	 * @return string|null Thumbnail URL
	 */
	public function get_thumbnail($key = 0)
	{
		$thumbnails = $this->get_thumbnails();
		if (isset($thumbnails[$key]))
		{
			return $thumbnails[$key];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get all thumbnails
	 *
	 * @return array|null Array of thumbnail URLs
	 */
	public function get_thumbnails()
	{
		if ($this->thumbnails !== null)
		{
			return $this->thumbnails;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the title
	 *
	 * @return string|null
	 */
	public function get_title()
	{
		if ($this->title !== null)
		{
			return $this->title;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get mimetype of the enclosure
	 *
	 * @see get_real_type()
	 * @return string|null MIME type
	 */
	public function get_type()
	{
		if ($this->type !== null)
		{
			return $this->type;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the width
	 *
	 * @return string|null
	 */
	public function get_width()
	{
		if ($this->width !== null)
		{
			return $this->width;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Embed the enclosure using `<embed>`
	 *
	 * @deprecated Use the second parameter to {@see embed} instead
	 *
	 * @param array|string $options See first paramter to {@see embed}
	 * @return string HTML string to output
	 */
	public function native_embed($options='')
	{
		return $this->embed($options, true);
	}

	/**
	 * Embed the enclosure using Javascript
	 *
	 * `$options` is an array or comma-separated key:value string, with the
	 * following properties:
	 *
	 * - `alt` (string): Alternate content for when an end-user does not have
	 *    the appropriate handler installed or when a file type is
	 *    unsupported. Can be any text or HTML. Defaults to blank.
	 * - `altclass` (string): If a file type is unsupported, the end-user will
	 *    see the alt text (above) linked directly to the content. That link
	 *    will have this value as its class name. Defaults to blank.
	 * - `audio` (string): This is an image that should be used as a
	 *    placeholder for audio files before they're loaded (QuickTime-only).
	 *    Can be any relative or absolute URL. Defaults to blank.
	 * - `bgcolor` (string): The background color for the media, if not
	 *    already transparent. Defaults to `#ffffff`.
	 * - `height` (integer): The height of the embedded media. Accepts any
	 *    numeric pixel value (such as `360`) or `auto`. Defaults to `auto`,
	 *    and it is recommended that you use this default.
	 * - `loop` (boolean): Do you want the media to loop when its done?
	 *    Defaults to `false`.
	 * - `mediaplayer` (string): The location of the included
	 *    `mediaplayer.swf` file. This allows for the playback of Flash Video
	 *    (`.flv`) files, and is the default handler for non-Odeo MP3's.
	 *    Defaults to blank.
	 * - `video` (string): This is an image that should be used as a
	 *    placeholder for video files before they're loaded (QuickTime-only).
	 *    Can be any relative or absolute URL. Defaults to blank.
	 * - `width` (integer): The width of the embedded media. Accepts any
	 *    numeric pixel value (such as `480`) or `auto`. Defaults to `auto`,
	 *    and it is recommended that you use this default.
	 * - `widescreen` (boolean): Is the enclosure widescreen or standard?
	 *    This applies only to video enclosures, and will automatically resize
	 *    the content appropriately.  Defaults to `false`, implying 4:3 mode.
	 *
	 * Note: Non-widescreen (4:3) mode with `width` and `height` set to `auto`
	 * will default to 480x360 video resolution.  Widescreen (16:9) mode with
	 * `width` and `height` set to `auto` will default to 480x270 video resolution.
	 *
	 * @todo If the dimensions for media:content are defined, use them when width/height are set to 'auto'.
	 * @param array|string $options Comma-separated key:value list, or array
	 * @param bool $native Use `<embed>`
	 * @return string HTML string to output
	 */
	public function embed($options = '', $native = false)
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
		$mediaplayer = '';
		$widescreen = false;
		$handler = $this->get_handler();
		$type = $this->get_real_type();

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

						case 'mediaplayer':
							$mediaplayer = $opt[1];
							break;

						case 'widescreen':
							$widescreen = $opt[1];
							break;
					}
				}
			}
		}

		$mime = explode('/', $type, 2);
		$mime = $mime[0];

		// Process values for 'auto'
		if ($width === 'auto')
		{
			if ($mime === 'video')
			{
				if ($height === 'auto')
				{
					$width = 480;
				}
				elseif ($widescreen)
				{
					$width = round((intval($height)/9)*16);
				}
				else
				{
					$width = round((intval($height)/3)*4);
				}
			}
			else
			{
				$width = '100%';
			}
		}

		if ($height === 'auto')
		{
			if ($mime === 'audio')
			{
				$height = 0;
			}
			elseif ($mime === 'video')
			{
				if ($width === 'auto')
				{
					if ($widescreen)
					{
						$height = 270;
					}
					else
					{
						$height = 360;
					}
				}
				elseif ($widescreen)
				{
					$height = round((intval($width)/16)*9);
				}
				else
				{
					$height = round((intval($width)/4)*3);
				}
			}
			else
			{
				$height = 376;
			}
		}
		elseif ($mime === 'audio')
		{
			$height = 0;
		}

		// Set proper placeholder value
		if ($mime === 'audio')
		{
			$placeholder = $audio;
		}
		elseif ($mime === 'video')
		{
			$placeholder = $video;
		}

		$embed = '';

		// Flash
		if ($handler === 'flash')
		{
			if ($native)
			{
				$embed .= "<embed src=\"" . $this->get_link() . "\" pluginspage=\"http://adobe.com/go/getflashplayer\" type=\"$type\" quality=\"high\" width=\"$width\" height=\"$height\" bgcolor=\"$bgcolor\" loop=\"$loop\"></embed>";
			}
			else
			{
				$embed .= "<script type='text/javascript'>embed_flash('$bgcolor', '$width', '$height', '" . $this->get_link() . "', '$loop', '$type');</script>";
			}
		}

		// Flash Media Player file types.
		// Preferred handler for MP3 file types.
		elseif ($handler === 'fmedia' || ($handler === 'mp3' && $mediaplayer !== ''))
		{
			$height += 20;
			if ($native)
			{
				$embed .= "<embed src=\"$mediaplayer\" pluginspage=\"http://adobe.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" quality=\"high\" width=\"$width\" height=\"$height\" wmode=\"transparent\" flashvars=\"file=" . rawurlencode($this->get_link().'?file_extension=.'.$this->get_extension()) . "&autostart=false&repeat=$loop&showdigits=true&showfsbutton=false\"></embed>";
			}
			else
			{
				$embed .= "<script type='text/javascript'>embed_flv('$width', '$height', '" . rawurlencode($this->get_link().'?file_extension=.'.$this->get_extension()) . "', '$placeholder', '$loop', '$mediaplayer');</script>";
			}
		}

		// QuickTime 7 file types.  Need to test with QuickTime 6.
		// Only handle MP3's if the Flash Media Player is not present.
		elseif ($handler === 'quicktime' || ($handler === 'mp3' && $mediaplayer === ''))
		{
			$height += 16;
			if ($native)
			{
				if ($placeholder !== '')
				{
					$embed .= "<embed type=\"$type\" style=\"cursor:hand; cursor:pointer;\" href=\"" . $this->get_link() . "\" src=\"$placeholder\" width=\"$width\" height=\"$height\" autoplay=\"false\" target=\"myself\" controller=\"false\" loop=\"$loop\" scale=\"aspect\" bgcolor=\"$bgcolor\" pluginspage=\"http://apple.com/quicktime/download/\"></embed>";
				}
				else
				{
					$embed .= "<embed type=\"$type\" style=\"cursor:hand; cursor:pointer;\" src=\"" . $this->get_link() . "\" width=\"$width\" height=\"$height\" autoplay=\"false\" target=\"myself\" controller=\"true\" loop=\"$loop\" scale=\"aspect\" bgcolor=\"$bgcolor\" pluginspage=\"http://apple.com/quicktime/download/\"></embed>";
				}
			}
			else
			{
				$embed .= "<script type='text/javascript'>embed_quicktime('$type', '$bgcolor', '$width', '$height', '" . $this->get_link() . "', '$placeholder', '$loop');</script>";
			}
		}

		// Windows Media
		elseif ($handler === 'wmedia')
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

	/**
	 * Get the real media type
	 *
	 * Often, feeds lie to us, necessitating a bit of deeper inspection. This
	 * converts types to their canonical representations based on the file
	 * extension
	 *
	 * @see get_type()
	 * @param bool $find_handler Internal use only, use {@see get_handler()} instead
	 * @return string MIME type
	 */
	public function get_real_type($find_handler = false)
	{
		// Mime-types by handler.
		$types_flash = array('application/x-shockwave-flash', 'application/futuresplash'); // Flash
		$types_fmedia = array('video/flv', 'video/x-flv','flv-application/octet-stream'); // Flash Media Player
		$types_quicktime = array('audio/3gpp', 'audio/3gpp2', 'audio/aac', 'audio/x-aac', 'audio/aiff', 'audio/x-aiff', 'audio/mid', 'audio/midi', 'audio/x-midi', 'audio/mp4', 'audio/m4a', 'audio/x-m4a', 'audio/wav', 'audio/x-wav', 'video/3gpp', 'video/3gpp2', 'video/m4v', 'video/x-m4v', 'video/mp4', 'video/mpeg', 'video/x-mpeg', 'video/quicktime', 'video/sd-video'); // QuickTime
		$types_wmedia = array('application/asx', 'application/x-mplayer2', 'audio/x-ms-wma', 'audio/x-ms-wax', 'video/x-ms-asf-plugin', 'video/x-ms-asf', 'video/x-ms-wm', 'video/x-ms-wmv', 'video/x-ms-wvx'); // Windows Media
		$types_mp3 = array('audio/mp3', 'audio/x-mp3', 'audio/mpeg', 'audio/x-mpeg'); // MP3

		if ($this->get_type() !== null)
		{
			$type = strtolower($this->type);
		}
		else
		{
			$type = null;
		}

		// If we encounter an unsupported mime-type, check the file extension and guess intelligently.
		if (!in_array($type, array_merge($types_flash, $types_fmedia, $types_quicktime, $types_wmedia, $types_mp3)))
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

				case 'flv':
					$type = 'video/x-flv';
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

		if ($find_handler)
		{
			if (in_array($type, $types_flash))
			{
				return 'flash';
			}
			elseif (in_array($type, $types_fmedia))
			{
				return 'fmedia';
			}
			elseif (in_array($type, $types_quicktime))
			{
				return 'quicktime';
			}
			elseif (in_array($type, $types_wmedia))
			{
				return 'wmedia';
			}
			elseif (in_array($type, $types_mp3))
			{
				return 'mp3';
			}
			else
			{
				return null;
			}
		}
		else
		{
			return $type;
		}
	}
}

