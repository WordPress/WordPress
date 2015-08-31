<?php
/**
 * @package WPSEO\Admin
 * @since      1.6.2
 */

/**
 * Class WPSEO_Snippet_Preview
 *
 * Generates a Google Search snippet preview.
 *
 * Takes a $post, $title and $description
 */
class WPSEO_Snippet_Preview {
	/**
	 * @var string The dynamically generated html for the snippet preview.
	 */
	protected $content;

	/**
	 * @var array The WPSEO options.
	 */
	protected $options;

	/**
	 * @var object The post for which we want to generate the snippet preview.
	 */
	protected $post;

	/**
	 * @var string The title that is shown in the snippet.
	 */
	protected $title;

	/**
	 * @var string The description that is shown in the snippet.
	 */
	protected $description;

	/**
	 * @var string The date that is shown at the beginning of the description in the snippet.
	 */
	protected $date = '';

	/**
	 * @var string The url that is shown in the snippet.
	 */
	protected $url;

	/**
	 * @var string The slug of the url that is shown in the snippet.
	 */
	protected $slug = '';

	/**
	 * Generates the html for the snippet preview containing dynamically generated text components.
	 * Those components are included as properties which are set in the constructor.
	 *
	 * @param object $post
	 * @param string $title
	 * @param string $description
	 */
	public function __construct( $post, $title, $description ) {
		$this->options     = WPSEO_Options::get_all();
		$this->post        = $post;
		$this->title       = esc_html( $title );
		$this->description = esc_html( $description );

		$this->set_date();
		$this->set_url();
		$this->set_content();
	}

	/**
	 * Getter for $this->content
	 * @return string html for snippet preview
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * Sets date if available
	 */
	protected function set_date() {
		if ( is_object( $this->post ) && isset( $this->options[ 'showdate-' . $this->post->post_type ] ) && $this->options[ 'showdate-' . $this->post->post_type ] === true ) {
			$date       = $this->get_post_date();
			$this->date = '<span class="date">' . $date . ' - </span>';
		}
	}

	/**
	 * Retrieves a post date when post is published, or return current date when it's not.
	 *
	 * @return string
	 */
	protected function get_post_date() {
		if ( isset( $this->post->post_date ) && $this->post->post_status == 'publish' ) {
			$date = date_i18n( 'j M Y', strtotime( $this->post->post_date ) );
		}
		else {
			$date = date_i18n( 'j M Y' );
		}

		return (string) $date;
	}

	/**
	 * Generates the url that is displayed in the snippet preview.
	 */
	protected function set_url() {
		$this->url = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) ) . '/';
		$this->set_slug();
	}

	/**
	 * Sets the slug and adds it to the url if the post has been published and the post name exists.
	 *
	 * If the post is set to be the homepage the slug is also not included.
	 */
	protected function set_slug() {
		$frontpage_post_id = (int) ( get_option( 'page_on_front' ) );

		if ( is_object( $this->post ) && isset( $this->post->post_name ) && $this->post->post_name !== '' && $this->post->ID !== $frontpage_post_id ) {
			$this->slug = sanitize_title( $this->title );
			$this->url .= esc_html( $this->slug );
		}
	}

	/**
	 * Generates the html for the snippet preview and assign it to $this->content.
	 */
	protected function set_content() {
		$content = <<<HTML
<div id="wpseosnippet">
<a class="title" id="wpseosnippet_title" href="#">$this->title</a>
<span class="url">$this->url</span>
<p class="desc">$this->date<span class="autogen"></span><span class="content">$this->description</span></p>
</div>
HTML;
		$this->set_content_through_filter( $content );
	}

	/**
	 * Sets the html for the snippet preview through a filter
	 *
	 * @param string $content
	 */
	protected function set_content_through_filter( $content ) {
		$properties = get_object_vars( $this );

		// Backward compatibility for functions hooking into the wpseo_snippet filter.
		$properties['desc'] = $properties['description'];

		/**
		 * Filter: 'wpseo_snippet' - Allow changing the html for the snippet preview.
		 *
		 * Passing in the post twice because of backwards compatibility.
		 */
		$this->content = apply_filters( 'wpseo_snippet', $content, $this->post, $properties );
	}
}
