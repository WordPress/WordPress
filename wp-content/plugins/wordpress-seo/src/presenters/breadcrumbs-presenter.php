<?php

namespace Yoast\WP\SEO\Presenters;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;

/**
 * Presenter class for the breadcrumbs.
 */
class Breadcrumbs_Presenter extends Abstract_Indexable_Presenter {

	/**
	 * The id attribute.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The class name attribute.
	 *
	 * @var string
	 */
	private $class;

	/**
	 * The wrapper element name.
	 *
	 * @var string
	 */
	private $wrapper;

	/**
	 * Separator to use.
	 *
	 * @var string
	 */
	private $separator;

	/**
	 * The element.
	 *
	 * @var string
	 */
	private $element;

	/**
	 * Presents the breadcrumbs.
	 *
	 * @return string The breadcrumbs HTML.
	 */
	public function present() {
		$breadcrumbs = $this->get();
		if ( ! \is_array( $breadcrumbs ) || empty( $breadcrumbs ) ) {
			return '';
		}

		$links = [];
		$total = \count( $breadcrumbs );
		foreach ( $breadcrumbs as $index => $breadcrumb ) {
			$links[ $index ] = $this->crumb_to_link( $breadcrumb, $index, $total );
		}

		// Removes any effectively empty links.
		$links  = \array_map( 'trim', $links );
		$links  = \array_filter( $links );
		$output = \implode( $this->get_separator(), $links );

		if ( empty( $output ) ) {
			return '';
		}

		$output = '<' . $this->get_wrapper() . $this->get_id() . $this->get_class() . '>' . $output . '</' . $this->get_wrapper() . '>';
		$output = $this->filter( $output );

		$prefix = $this->helpers->options->get( 'breadcrumbs-prefix' );
		if ( $prefix !== '' ) {
			$output = "\t" . $prefix . "\n" . $output;
		}

		return $output;
	}

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return array The raw value.
	 */
	public function get() {
		return $this->presentation->breadcrumbs;
	}

	/**
	 * Filters the output.
	 *
	 * @param string $output The HTML output.
	 *
	 * @return string The filtered output.
	 */
	protected function filter( $output ) {
		/**
		 * Filter: 'wpseo_breadcrumb_output' - Allow changing the HTML output of the Yoast SEO breadcrumbs class.
		 *
		 * @param string                 $output       The HTML output.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		return \apply_filters( 'wpseo_breadcrumb_output', $output, $this->presentation );
	}

	/**
	 * Create a breadcrumb element string.
	 *
	 * @param array $breadcrumb Link info array containing the keys:
	 *                          'text'                  => (string) link text.
	 *                          'url'                   => (string) link url.
	 *                          (optional) 'title'      => (string) link title attribute text.
	 * @param int   $index      Index for the current breadcrumb.
	 * @param int   $total      The total number of breadcrumbs.
	 *
	 * @return string The breadcrumb link.
	 */
	protected function crumb_to_link( $breadcrumb, $index, $total ) {
		$link = '';

		if ( ! isset( $breadcrumb['text'] ) || ! \is_string( $breadcrumb['text'] ) || empty( $breadcrumb['text'] ) ) {
			return $link;
		}

		$text = \trim( $breadcrumb['text'] );

		if (
			$index < ( $total - 1 )
			&& isset( $breadcrumb['url'] )
			&& \is_string( $breadcrumb['url'] )
			&& ! empty( $breadcrumb['url'] )
		) {
			// If it's not the last element and we have a url.
			$link      .= '<' . $this->get_element() . '>';
			$title_attr = isset( $breadcrumb['title'] ) ? ' title="' . \esc_attr( $breadcrumb['title'] ) . '"' : '';
			$link      .= '<a';

			if ( $this->should_link_target_blank() ) {
				$link .= ' target="_blank"';
			}
			$link .= ' href="' . \esc_url( $breadcrumb['url'] ) . '"' . $title_attr . '>' . $text . '</a>';
			$link .= '</' . $this->get_element() . '>';
		}
		elseif ( $index === ( $total - 1 ) ) {
			// If it's the last element.

			if ( $this->helpers->options->get( 'breadcrumbs-boldlast' ) === true ) {
				$text = '<strong>' . $text . '</strong>';
			}

			$link .= '<' . $this->get_element() . ' class="breadcrumb_last" aria-current="page">' . $text . '</' . $this->get_element() . '>';
		}
		else {
			// It's not the last element and has no url.
			$link .= '<' . $this->get_element() . '>' . $text . '</' . $this->get_element() . '>';
		}

		/**
		 * Filter: 'wpseo_breadcrumb_single_link' - Allow changing of each link being put out by the Yoast SEO breadcrumbs class.
		 *
		 * @param string $link_output The output string.
		 * @param array  $link        The breadcrumb link array.
		 */
		return \apply_filters( 'wpseo_breadcrumb_single_link', $link, $breadcrumb );
	}

	/**
	 * Retrieves HTML ID attribute.
	 *
	 * @return string The id attribute.
	 */
	protected function get_id() {
		if ( ! $this->id ) {
			/**
			 * Filter: 'wpseo_breadcrumb_output_id' - Allow changing the HTML ID on the Yoast SEO breadcrumbs wrapper element.
			 *
			 * @param string $unsigned ID to add to the wrapper element.
			 */
			$this->id = \apply_filters( 'wpseo_breadcrumb_output_id', '' );
			if ( ! \is_string( $this->id ) ) {
				return '';
			}

			if ( $this->id !== '' ) {
				$this->id = ' id="' . \esc_attr( $this->id ) . '"';
			}
		}

		return $this->id;
	}

	/**
	 * Retrieves HTML Class attribute.
	 *
	 * @return string The class attribute.
	 */
	protected function get_class() {
		if ( ! $this->class ) {
			/**
			 * Filter: 'wpseo_breadcrumb_output_class' - Allow changing the HTML class on the Yoast SEO breadcrumbs wrapper element.
			 *
			 * @param string $unsigned Class to add to the wrapper element.
			 */
			$this->class = \apply_filters( 'wpseo_breadcrumb_output_class', '' );
			if ( ! \is_string( $this->class ) ) {
				return '';
			}

			if ( $this->class !== '' ) {
				$this->class = ' class="' . \esc_attr( $this->class ) . '"';
			}
		}

		return $this->class;
	}

	/**
	 * Retrieves the wrapper element name.
	 *
	 * @return string The wrapper element name.
	 */
	protected function get_wrapper() {
		if ( ! $this->wrapper ) {
			$this->wrapper = \apply_filters( 'wpseo_breadcrumb_output_wrapper', 'span' );
			$this->wrapper = \tag_escape( $this->wrapper );
			if ( ! \is_string( $this->wrapper ) || $this->wrapper === '' ) {
				$this->wrapper = 'span';
			}
		}

		return $this->wrapper;
	}

	/**
	 * Retrieves the separator.
	 *
	 * @return string The separator.
	 */
	protected function get_separator() {
		if ( ! $this->separator ) {
			$this->separator = \apply_filters( 'wpseo_breadcrumb_separator', $this->helpers->options->get( 'breadcrumbs-sep' ) );
			$this->separator = ' ' . $this->separator . ' ';
		}

		return $this->separator;
	}

	/**
	 * Retrieves the crumb element name.
	 *
	 * @return string The element to use.
	 */
	protected function get_element() {
		if ( ! $this->element ) {
			$this->element = \esc_attr( \apply_filters( 'wpseo_breadcrumb_single_link_wrapper', 'span' ) );
		}

		return $this->element;
	}

	/**
	 * This is needed because when the editor is loaded in an Iframe the link needs to open in a different browser window.
	 * We don't want this behaviour in the front-end and the way to check this is to check if the block is rendered in a REST request with the `context` set as 'edit'. Thus being in the editor.
	 *
	 * @return bool returns if the breadcrumb should be opened in another window.
	 */
	private function should_link_target_blank(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['context'] ) && \is_string( $_GET['context'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are only strictly comparing.
			if ( \wp_unslash( $_GET['context'] ) === 'edit' ) {
				return true;
			}
		}
		return false;
	}
}
