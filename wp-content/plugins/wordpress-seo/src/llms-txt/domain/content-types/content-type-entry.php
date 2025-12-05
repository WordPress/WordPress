<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Domain\Content_Types;

use WP_Post;
use Yoast\WP\SEO\Surfaces\Values\Meta;

/**
 * This class describes a Content Type Entry.
 */
class Content_Type_Entry {

	/**
	 * The ID of the content type entry.
	 *
	 * @var int
	 */
	private $id;

	/**
	 * The title of the content type entry.
	 *
	 * @var string
	 */
	private $title;

	/**
	 * The URL of the content type entry.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * The description of the content type entry.
	 *
	 * @var string
	 */
	private $description;

	/**
	 * The slug of the content type entry.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * The constructor.
	 *
	 * @param int    $id          The ID of the content type entry.
	 * @param string $title       The title of the content type entry.
	 * @param string $url         The URL of the content type entry.
	 * @param string $description The description of the content type entry.
	 * @param string $slug        The slug of the content type entry.
	 */
	public function __construct(
		int $id,
		?string $title = null,
		?string $url = null,
		?string $description = null,
		?string $slug = null
	) {
		$this->id          = $id;
		$this->title       = $title;
		$this->url         = $url;
		$this->description = $description;
		$this->slug        = $slug;
	}

	/**
	 * Gets the ID of the content type entry.
	 *
	 * @return int The ID of the content type entry.
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * Gets the title of the content type entry.
	 *
	 * @return string The title of the content type entry.
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Gets the URL of the content type entry.
	 *
	 * @return string The URL of the content type entry.
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * Gets the description of the content type entry.
	 *
	 * @return string The description of the content type entry.
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Gets the slug of the content type entry.
	 *
	 * @return string The slug of the content type entry.
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Creates a new instance of the class from the provided Meta object.
	 *
	 * @param Meta $meta The Meta object containing the necessary data to construct the instance.
	 *
	 * @return self A new instance of the class.
	 */
	public static function from_meta( Meta $meta ): self {
		return new self(
			$meta->post->ID,
			$meta->post->post_title,
			$meta->canonical,
			$meta->post->post_excerpt,
			$meta->post->post_name
		);
	}

	/**
	 * Creates an instance of the class from a WordPress post object.
	 *
	 * @param WP_Post $post      The WordPress post object.
	 * @param string  $permalink The permalink of the post.
	 *
	 * @return self An instance of the class.
	 */
	public static function from_post( WP_Post $post, string $permalink ): self {
		return new self(
			$post->ID,
			$post->post_title,
			$permalink,
			$post->post_excerpt,
			$post->post_name
		);
	}
}
