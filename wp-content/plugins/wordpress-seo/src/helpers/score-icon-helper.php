<?php

namespace Yoast\WP\SEO\Helpers;

use WPSEO_Rank;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Presenters\Score_Icon_Presenter;

/**
 * A helper object for score icons.
 */
class Score_Icon_Helper {

	/**
	 * Holds the Robots_Helper.
	 *
	 * @var Robots_Helper
	 */
	protected $robots_helper;

	/**
	 * Constructs a Score_Helper.
	 *
	 * @param Robots_Helper $robots_helper The Robots_Helper.
	 */
	public function __construct( Robots_Helper $robots_helper ) {
		$this->robots_helper = $robots_helper;
	}

	/**
	 * Creates a Score_Icon_Presenter for the readability analysis.
	 *
	 * @param int    $score       The readability analysis score.
	 * @param string $extra_class Optional. Any extra class.
	 *
	 * @return Score_Icon_Presenter The Score_Icon_Presenter.
	 */
	public function for_readability( $score, $extra_class = '' ) {
		$rank  = WPSEO_Rank::from_numeric_score( (int) $score );
		$class = $rank->get_css_class();
		if ( $extra_class ) {
			$class .= " $extra_class";
		}

		return new Score_Icon_Presenter( $rank->get_label(), $class );
	}

	/**
	 * Creates a Score_Icon_Presenter for the inclusive language analysis.
	 *
	 * @param int    $score       The inclusive language analysis score.
	 * @param string $extra_class Optional. Any extra class.
	 *
	 * @return Score_Icon_Presenter The Score_Icon_Presenter.
	 */
	public function for_inclusive_language( $score, $extra_class = '' ) {
		$rank  = WPSEO_Rank::from_numeric_score( (int) $score );
		$class = $rank->get_css_class();
		if ( $extra_class ) {
			$class .= " $extra_class";
		}

		return new Score_Icon_Presenter( $rank->get_inclusive_language_label(), $class );
	}

	/**
	 * Creates a Score_Icon_Presenter for the SEO analysis from an indexable.
	 *
	 * @param Indexable|false $indexable      The Indexable.
	 * @param string          $extra_class    Optional. Any extra class.
	 * @param string          $no_index_title Optional. Override the title when not indexable.
	 *
	 * @return Score_Icon_Presenter The Score_Icon_Presenter.
	 */
	public function for_seo( $indexable, $extra_class = '', $no_index_title = '' ) {
		$is_indexable = $indexable && $this->robots_helper->is_indexable( $indexable );

		if ( ! $is_indexable ) {
			$rank  = new WPSEO_Rank( WPSEO_Rank::NO_INDEX );
			$title = empty( $no_index_title ) ? $rank->get_label() : $no_index_title;
		}
		elseif ( empty( $indexable && $indexable->primary_focus_keyword ) ) {
			$rank  = new WPSEO_Rank( WPSEO_Rank::BAD );
			$title = \__( 'Focus keyphrase not set', 'wordpress-seo' );
		}
		else {
			$rank  = WPSEO_Rank::from_numeric_score( ( $indexable ) ? $indexable->primary_focus_keyword_score : 0 );
			$title = $rank->get_label();
		}

		$class = $rank->get_css_class();
		if ( $extra_class ) {
			$class .= " $extra_class";
		}

		return new Score_Icon_Presenter( $title, $class );
	}
}
