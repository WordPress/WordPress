<?php
namespace Elementor\Core\Utils\ImportExport\Parsers;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WordPress eXtended RSS file parser implementations
 * Originally made by WordPress part of WordPress/Importer.
 * https://plugins.trac.wordpress.org/browser/wordpress-importer/trunk/parsers/class-wxr-parser-regex.php
 *
 * What was done:
 * Reformat of the code.
 * Changed text domain.
 * Changed methods visibility.
 */

/**
 * WXR Parser that uses regular expressions. Fallback for installs without an XML parser.
 */
class WXR_Parser_Regex {
	/**
	 * @var bool
	 */
	private $has_gzip;

	private $authors = [];
	private $posts = [];
	private $categories = [];
	private $tags = [];
	private $terms = [];
	private $base_url = '';
	private $base_blog_url = '';

	/**
	 * @param string $file
	 *
	 * @return array|\WP_Error
	 */
	public function parse( $file ) {
		$wxr_version = '';
		$in_multiline = false;

		$multiline_content = '';

		$multiline_tags = [
			'item' => [
				'posts',
				function ( $post ) {
					return $this->process_post( $post );
				},
			],
			'wp:category' => [
				'categories',
				function ( $category ) {
					return $this->process_category( $category );
				},
			],
			'wp:tag' => [
				'tags',
				function ( $tag ) {
					return $this->process_tag( $tag );
				},
			],
			'wp:term' => [
				'terms',
				function ( $term ) {
					return $this->process_term( $term );
				},
			],
		];

		$fp = $this->fopen( $file, 'r' );
		if ( $fp ) {
			while ( ! $this->feof( $fp ) ) {
				$importline = rtrim( $this->fgets( $fp ) );

				if ( ! $wxr_version && preg_match( '|<wp:wxr_version>(\d+\.\d+)</wp:wxr_version>|', $importline, $version ) ) {
					$wxr_version = $version[1];
				}

				if ( false !== strpos( $importline, '<wp:base_site_url>' ) ) {
					preg_match( '|<wp:base_site_url>(.*?)</wp:base_site_url>|is', $importline, $url );
					$this->base_url = $url[1];
					continue;
				}

				if ( false !== strpos( $importline, '<wp:base_blog_url>' ) ) {
					preg_match( '|<wp:base_blog_url>(.*?)</wp:base_blog_url>|is', $importline, $blog_url );
					$this->base_blog_url = $blog_url[1];
					continue;
				} else {
					$this->base_blog_url = $this->base_url;
				}

				if ( false !== strpos( $importline, '<wp:author>' ) ) {
					preg_match( '|<wp:author>(.*?)</wp:author>|is', $importline, $author );
					$a = $this->process_author( $author[1] );
					$this->authors[ $a['author_login'] ] = $a;
					continue;
				}

				foreach ( $multiline_tags as $tag => $handler ) {
					// Handle multi-line tags on a singular line.
					if ( preg_match( '|<' . $tag . '>(.*?)</' . $tag . '>|is', $importline, $matches ) ) {
						$this->{$handler[0]}[] = call_user_func( $handler[1], $matches[1] );

						continue;
					}

					$pos = strpos( $importline, "<$tag>" );

					if ( false !== $pos ) {
						// Take note of any content after the opening tag.
						$multiline_content = trim( substr( $importline, $pos + strlen( $tag ) + 2 ) );

						// We don't want to have this line added to `$is_multiline` below.
						$importline = '';
						$in_multiline = $tag;

						continue;
					}

					$pos = strpos( $importline, "</$tag>" );

					if ( false !== $pos ) {
						$in_multiline = false;
						$multiline_content .= trim( substr( $importline, 0, $pos ) );

						$this->{$handler[0]}[] = call_user_func( $handler[1], $multiline_content );
					}
				}

				if ( $in_multiline && $importline ) {
					$multiline_content .= $importline . "\n";
				}
			}

			$this->fclose( $fp );
		}

		if ( ! $wxr_version ) {
			return new WP_Error( 'WXR_parse_error', esc_html__( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'elementor' ) );
		}

		return [
			'authors' => $this->authors,
			'posts' => $this->posts,
			'categories' => $this->categories,
			'tags' => $this->tags,
			'terms' => $this->terms,
			'base_url' => $this->base_url,
			'base_blog_url' => $this->base_blog_url,
			'version' => $wxr_version,
		];
	}

	private function process_category( $category ) {
		$term = [
			'term_id' => $this->get_tag( $category, 'wp:term_id' ),
			'cat_name' => $this->get_tag( $category, 'wp:cat_name' ),
			'category_nicename' => $this->get_tag( $category, 'wp:category_nicename' ),
			'category_parent' => $this->get_tag( $category, 'wp:category_parent' ),
			'category_description' => $this->get_tag( $category, 'wp:category_description' ),
		];

		$term_meta = $this->process_meta( $category, 'wp:termmeta' );
		if ( ! empty( $term_meta ) ) {
			$term['termmeta'] = $term_meta;
		}

		return $term;
	}

	private function process_tag( $tag ) {
		$term = [
			'term_id' => $this->get_tag( $tag, 'wp:term_id' ),
			'tag_name' => $this->get_tag( $tag, 'wp:tag_name' ),
			'tag_slug' => $this->get_tag( $tag, 'wp:tag_slug' ),
			'tag_description' => $this->get_tag( $tag, 'wp:tag_description' ),
		];

		$term_meta = $this->process_meta( $tag, 'wp:termmeta' );
		if ( ! empty( $term_meta ) ) {
			$term['termmeta'] = $term_meta;
		}

		return $term;
	}

	private function process_term( $term ) {
		$term_data = [
			'term_id' => $this->get_tag( $term, 'wp:term_id' ),
			'term_taxonomy' => $this->get_tag( $term, 'wp:term_taxonomy' ),
			'slug' => $this->get_tag( $term, 'wp:term_slug' ),
			'term_parent' => $this->get_tag( $term, 'wp:term_parent' ),
			'term_name' => $this->get_tag( $term, 'wp:term_name' ),
			'term_description' => $this->get_tag( $term, 'wp:term_description' ),
		];

		$term_meta = $this->process_meta( $term, 'wp:termmeta' );
		if ( ! empty( $term_meta ) ) {
			$term_data['termmeta'] = $term_meta;
		}

		return $term_data;
	}

	private function process_meta( $meta_string, $tag ) {
		$parsed_meta = [];

		preg_match_all( "|<$tag>(.+?)</$tag>|is", $meta_string, $meta );

		if ( ! isset( $meta[1] ) ) {
			return $parsed_meta;
		}

		foreach ( $meta[1] as $m ) {
			$parsed_meta[] = [
				'key' => $this->get_tag( $m, 'wp:meta_key' ),
				'value' => $this->get_tag( $m, 'wp:meta_value' ),
			];
		}

		return $parsed_meta;
	}

	private function process_author( $a ) {
		return [
			'author_id' => $this->get_tag( $a, 'wp:author_id' ),
			'author_login' => $this->get_tag( $a, 'wp:author_login' ),
			'author_email' => $this->get_tag( $a, 'wp:author_email' ),
			'author_display_name' => $this->get_tag( $a, 'wp:author_display_name' ),
			'author_first_name' => $this->get_tag( $a, 'wp:author_first_name' ),
			'author_last_name' => $this->get_tag( $a, 'wp:author_last_name' ),
		];
	}

	private function process_post( $post ) {
		$normalize_tag_callback = function ( $matches ) {
			return $this->normalize_tag( $matches );
		};

		$post_id = $this->get_tag( $post, 'wp:post_id' );
		$post_title = $this->get_tag( $post, 'title' );
		$post_date = $this->get_tag( $post, 'wp:post_date' );
		$post_date_gmt = $this->get_tag( $post, 'wp:post_date_gmt' );
		$comment_status = $this->get_tag( $post, 'wp:comment_status' );
		$ping_status = $this->get_tag( $post, 'wp:ping_status' );
		$status = $this->get_tag( $post, 'wp:status' );
		$post_name = $this->get_tag( $post, 'wp:post_name' );
		$post_parent = $this->get_tag( $post, 'wp:post_parent' );
		$menu_order = $this->get_tag( $post, 'wp:menu_order' );
		$post_type = $this->get_tag( $post, 'wp:post_type' );
		$post_password = $this->get_tag( $post, 'wp:post_password' );
		$is_sticky = $this->get_tag( $post, 'wp:is_sticky' );
		$guid = $this->get_tag( $post, 'guid' );
		$post_author = $this->get_tag( $post, 'dc:creator' );

		$post_excerpt = $this->get_tag( $post, 'excerpt:encoded' );
		$post_excerpt = preg_replace_callback( '|<(/?[A-Z]+)|', $normalize_tag_callback, $post_excerpt );
		$post_excerpt = str_replace( '<br>', '<br />', $post_excerpt );
		$post_excerpt = str_replace( '<hr>', '<hr />', $post_excerpt );

		$post_content = $this->get_tag( $post, 'content:encoded' );
		$post_content = preg_replace_callback( '|<(/?[A-Z]+)|', $normalize_tag_callback, $post_content );
		$post_content = str_replace( '<br>', '<br />', $post_content );
		$post_content = str_replace( '<hr>', '<hr />', $post_content );

		$postdata = compact( 'post_id', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_excerpt', 'post_title', 'status', 'post_name', 'comment_status', 'ping_status', 'guid', 'post_parent', 'menu_order', 'post_type', 'post_password', 'is_sticky' );

		$attachment_url = $this->get_tag( $post, 'wp:attachment_url' );
		if ( $attachment_url ) {
			$postdata['attachment_url'] = $attachment_url;
		}

		preg_match_all( '|<category domain="([^"]+?)" nicename="([^"]+?)">(.+?)</category>|is', $post, $terms, PREG_SET_ORDER );
		foreach ( $terms as $t ) {
			$post_terms[] = [
				'slug' => $t[2],
				'domain' => $t[1],
				'name' => str_replace( [ '<![CDATA[', ']]>' ], '', $t[3] ),
			];
		}
		if ( ! empty( $post_terms ) ) {
			$postdata['terms'] = $post_terms;
		}

		preg_match_all( '|<wp:comment>(.+?)</wp:comment>|is', $post, $comments );
		$comments = $comments[1];
		if ( $comments ) {
			foreach ( $comments as $comment ) {
				$post_comments[] = [
					'comment_id' => $this->get_tag( $comment, 'wp:comment_id' ),
					'comment_author' => $this->get_tag( $comment, 'wp:comment_author' ),
					'comment_author_email' => $this->get_tag( $comment, 'wp:comment_author_email' ),
					'comment_author_IP' => $this->get_tag( $comment, 'wp:comment_author_IP' ),
					'comment_author_url' => $this->get_tag( $comment, 'wp:comment_author_url' ),
					'comment_date' => $this->get_tag( $comment, 'wp:comment_date' ),
					'comment_date_gmt' => $this->get_tag( $comment, 'wp:comment_date_gmt' ),
					'comment_content' => $this->get_tag( $comment, 'wp:comment_content' ),
					'comment_approved' => $this->get_tag( $comment, 'wp:comment_approved' ),
					'comment_type' => $this->get_tag( $comment, 'wp:comment_type' ),
					'comment_parent' => $this->get_tag( $comment, 'wp:comment_parent' ),
					'comment_user_id' => $this->get_tag( $comment, 'wp:comment_user_id' ),
					'commentmeta' => $this->process_meta( $comment, 'wp:commentmeta' ),
				];
			}
		}
		if ( ! empty( $post_comments ) ) {
			$postdata['comments'] = $post_comments;
		}

		$post_meta = $this->process_meta( $post, 'wp:postmeta' );
		if ( ! empty( $post_meta ) ) {
			$postdata['postmeta'] = $post_meta;
		}

		return $postdata;
	}

	private function get_tag( $text_string, $tag ) {
		preg_match( "|<$tag.*?>(.*?)</$tag>|is", $text_string, $return );
		if ( isset( $return[1] ) ) {
			if ( substr( $return[1], 0, 9 ) == '<![CDATA[' ) {
				if ( strpos( $return[1], ']]]]><![CDATA[>' ) !== false ) {
					preg_match_all( '|<!\[CDATA\[(.*?)\]\]>|s', $return[1], $matches );
					$return = '';
					foreach ( $matches[1] as $match ) {
						$return .= $match;
					}
				} else {
					$return = preg_replace( '|^<!\[CDATA\[(.*)\]\]>$|s', '$1', $return[1] );
				}
			} else {
				$return = $return[1];
			}
		} else {
			$return = '';
		}

		return $return;
	}

	private function normalize_tag( $matches ) {
		return '<' . strtolower( $matches[1] );
	}

	private function fopen( $filename, $mode = 'r' ) {
		if ( $this->has_gzip ) {
			return gzopen( $filename, $mode );
		}

		return fopen( $filename, $mode );
	}

	private function feof( $fp ) {
		if ( $this->has_gzip ) {
			return gzeof( $fp );
		}

		return feof( $fp );
	}

	private function fgets( $fp, $len = 8192 ) {
		if ( $this->has_gzip ) {
			return gzgets( $fp, $len );
		}

		return fgets( $fp, $len );
	}

	private function fclose( $fp ) {
		if ( $this->has_gzip ) {
			return gzclose( $fp );
		}

		return fclose( $fp );
	}

	public function __construct() {
		$this->has_gzip = is_callable( 'gzopen' );
	}
}
