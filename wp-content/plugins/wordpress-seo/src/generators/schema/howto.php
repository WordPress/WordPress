<?php

namespace Yoast\WP\SEO\Generators\Schema;

use Yoast\WP\SEO\Config\Schema_IDs;

/**
 * Returns schema HowTo data.
 */
class HowTo extends Abstract_Schema_Piece {

	/**
	 * Determines whether or not a piece should be added to the graph.
	 *
	 * @return bool
	 */
	public function is_needed() {
		return ! empty( $this->context->blocks['yoast/how-to-block'] );
	}

	/**
	 * Renders a list of questions, referencing them by ID.
	 *
	 * @return array Our Schema graph.
	 */
	public function generate() {
		$graph = [];

		foreach ( $this->context->blocks['yoast/how-to-block'] as $index => $block ) {
			$this->add_how_to( $graph, $block, $index );
		}

		return $graph;
	}

	/**
	 * Adds the duration of the task to the Schema.
	 *
	 * @param array $data       Our How-To schema data.
	 * @param array $attributes The block data attributes.
	 *
	 * @return void
	 */
	private function add_duration( &$data, $attributes ) {
		if ( empty( $attributes['hasDuration'] ) ) {
			return;
		}

		$days    = empty( $attributes['days'] ) ? 0 : $attributes['days'];
		$hours   = empty( $attributes['hours'] ) ? 0 : $attributes['hours'];
		$minutes = empty( $attributes['minutes'] ) ? 0 : $attributes['minutes'];

		if ( ( $days + $hours + $minutes ) > 0 ) {
			$data['totalTime'] = \esc_attr( 'P' . $days . 'DT' . $hours . 'H' . $minutes . 'M' );
		}
	}

	/**
	 * Adds the steps to our How-To output.
	 *
	 * @param array $data  Our How-To schema data.
	 * @param array $steps Our How-To block's steps.
	 *
	 * @return void
	 */
	private function add_steps( &$data, $steps ) {
		foreach ( $steps as $step ) {
			$schema_id   = $this->context->canonical . '#' . \esc_attr( $step['id'] );
			$schema_step = [
				'@type' => 'HowToStep',
				'url'   => $schema_id,
			];

			if ( isset( $step['jsonText'] ) ) {
				$json_text = $this->helpers->schema->html->sanitize( $step['jsonText'] );
			}

			if ( isset( $step['jsonName'] ) ) {
				$json_name = $this->helpers->schema->html->smart_strip_tags( $step['jsonName'] );
			}

			if ( empty( $json_name ) ) {
				if ( empty( $step['text'] ) ) {
					continue;
				}

				$schema_step['text'] = '';

				$this->add_step_image( $schema_step, $step );

				// If there is no text and no image, don't output the step.
				if ( empty( $json_text ) && empty( $schema_step['image'] ) ) {
					continue;
				}

				if ( ! empty( $json_text ) ) {
					$schema_step['text'] = $json_text;
				}
			}

			elseif ( empty( $json_text ) ) {
				$schema_step['text'] = $json_name;
			}
			else {
				$schema_step['name'] = $json_name;

				$this->add_step_description( $schema_step, $json_text );
				$this->add_step_image( $schema_step, $step );
			}

			$data['step'][] = $schema_step;
		}
	}

	/**
	 * Checks if we have a step description, if we do, add it.
	 *
	 * @param array  $schema_step Our Schema output for the Step.
	 * @param string $json_text   The step text.
	 *
	 * @return void
	 */
	private function add_step_description( &$schema_step, $json_text ) {
		$schema_step['itemListElement'] = [
			[
				'@type' => 'HowToDirection',
				'text'  => $json_text,
			],
		];
	}

	/**
	 * Checks if we have a step image, if we do, add it.
	 *
	 * @param array $schema_step Our Schema output for the Step.
	 * @param array $step        The step block data.
	 *
	 * @return void
	 */
	private function add_step_image( &$schema_step, $step ) {
		if ( isset( $step['text'] ) && \is_array( $step['text'] ) ) {
			foreach ( $step['text'] as $line ) {
				if ( \is_array( $line ) && isset( $line['type'] ) && $line['type'] === 'img' ) {
					$schema_step['image'] = $this->get_image_schema( \esc_url( $line['props']['src'] ) );
				}
			}
		}
	}

	/**
	 * Generates the HowTo schema for a block.
	 *
	 * @param array $graph Our Schema data.
	 * @param array $block The How-To block content.
	 * @param int   $index The index of the current block.
	 *
	 * @return void
	 */
	protected function add_how_to( &$graph, $block, $index ) {
		$data = [
			'@type'            => 'HowTo',
			'@id'              => $this->context->canonical . '#howto-' . ( $index + 1 ),
			'name'             => $this->helpers->schema->html->smart_strip_tags( $this->helpers->post->get_post_title_with_fallback( $this->context->id ) ),
			'mainEntityOfPage' => [ '@id' => $this->context->main_schema_id ],
			'description'      => '',
		];

		if ( $this->context->has_article ) {
			$data['mainEntityOfPage'] = [ '@id' => $this->context->main_schema_id . Schema_IDs::ARTICLE_HASH ];
		}

		if ( isset( $block['attrs']['jsonDescription'] ) ) {
			$data['description'] = $this->helpers->schema->html->sanitize( $block['attrs']['jsonDescription'] );
		}

		$this->add_duration( $data, $block['attrs'] );

		if ( isset( $block['attrs']['steps'] ) ) {
			$this->add_steps( $data, $block['attrs']['steps'] );
		}

		$data = $this->helpers->schema->language->add_piece_language( $data );

		$graph[] = $data;
	}

	/**
	 * Generates the image schema from the attachment $url.
	 *
	 * @param string $url Attachment url.
	 *
	 * @return array Image schema.
	 */
	protected function get_image_schema( $url ) {
		$schema_id = $this->context->canonical . '#schema-image-' . \md5( $url );

		return $this->helpers->schema->image->generate_from_url( $schema_id, $url );
	}
}
