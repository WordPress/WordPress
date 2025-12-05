<?php

namespace Yoast\WP\SEO\Generators\Schema;

/**
 * Returns schema FAQ data.
 */
class FAQ extends Abstract_Schema_Piece {

	/**
	 * Determines whether a piece should be added to the graph.
	 *
	 * @return bool
	 */
	public function is_needed() {
		if ( empty( $this->context->blocks['yoast/faq-block'] ) ) {
			return false;
		}

		if ( ! \is_array( $this->context->schema_page_type ) ) {
			$this->context->schema_page_type = [ $this->context->schema_page_type ];
		}
		$this->context->schema_page_type[]  = 'FAQPage';
		$this->context->main_entity_of_page = $this->generate_ids();

		return true;
	}

	/**
	 * Generate the IDs so we can link to them in the main entity.
	 *
	 * @return array
	 */
	private function generate_ids() {
		$ids = [];
		foreach ( $this->context->blocks['yoast/faq-block'] as $block ) {
			if ( isset( $block['attrs']['questions'] ) ) {
				foreach ( $block['attrs']['questions'] as $question ) {
					if ( empty( $question['jsonAnswer'] ) ) {
						continue;
					}
					$ids[] = [ '@id' => $this->context->canonical . '#' . \esc_attr( $question['id'] ) ];
				}
			}
		}

		return $ids;
	}

	/**
	 * Render a list of questions, referencing them by ID.
	 *
	 * @return array Our Schema graph.
	 */
	public function generate() {
		$graph = [];

		$questions = [];
		foreach ( $this->context->blocks['yoast/faq-block'] as $block ) {
			if ( isset( $block['attrs']['questions'] ) ) {
				$questions = \array_merge( $questions, $block['attrs']['questions'] );
			}
		}
		foreach ( $questions as $index => $question ) {
			if ( ! isset( $question['jsonAnswer'] ) || empty( $question['jsonAnswer'] ) ) {
				continue;
			}
			$graph[] = $this->generate_question_block( $question, ( $index + 1 ) );
		}

		return $graph;
	}

	/**
	 * Generate a Question piece.
	 *
	 * @param array $question The question to generate schema for.
	 * @param int   $position The position of the question.
	 *
	 * @return array Schema.org Question piece.
	 */
	protected function generate_question_block( $question, $position ) {
		$url = $this->context->canonical . '#' . \esc_attr( $question['id'] );

		$data = [
			'@type'          => 'Question',
			'@id'            => $url,
			'position'       => $position,
			'url'            => $url,
			'name'           => $this->helpers->schema->html->smart_strip_tags( $question['jsonQuestion'] ),
			'answerCount'    => 1,
			'acceptedAnswer' => $this->add_accepted_answer_property( $question ),
		];

		return $this->helpers->schema->language->add_piece_language( $data );
	}

	/**
	 * Adds the Questions `acceptedAnswer` property.
	 *
	 * @param array $question The question to add the acceptedAnswer to.
	 *
	 * @return array Schema.org Question piece.
	 */
	protected function add_accepted_answer_property( $question ) {
		$data = [
			'@type' => 'Answer',
			'text'  => $this->helpers->schema->html->sanitize( $question['jsonAnswer'] ),
		];

		return $this->helpers->schema->language->add_piece_language( $data );
	}
}
