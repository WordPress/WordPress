<?php
/**
 * WooCommerce Admin: Selling Online Courses note
 *
 * Adds a note to encourage selling online courses.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;

/**
 * Selling_Online_Courses
 */
class SellingOnlineCourses {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-selling-online-courses';

	/**
	 * Attach hooks.
	 */
	public function __construct() {
		add_action(
			'update_option_' . OnboardingProfile::DATA_OPTION,
			array( $this, 'check_onboarding_profile' ),
			10,
			3
		);
	}

	/**
	 * Check to see if the profiler options match before possibly adding note.
	 *
	 * @param object $old_value The old option value.
	 * @param object $value     The new option value.
	 * @param string $option    The name of the option.
	 */
	public static function check_onboarding_profile( $old_value, $value, $option ) {
		// Skip adding if this store is in the education/learning industry.
		if ( ! isset( $value['industry'] ) ) {
			return;
		}
		$industry_slugs = array_column( $value['industry'], 'slug' );
		if ( ! in_array( 'education-and-learning', $industry_slugs, true ) ) {
			return;
		}

		self::possibly_add_note();
	}

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		$note = new Note();

		$note->set_title( __( 'Do you want to sell online courses?', 'woocommerce' ) );
		$note->set_content( __( 'Online courses are a great solution for any business that can teach a new skill. Since courses don’t require physical product development or shipping, they’re affordable, fast to create, and can generate passive income for years to come. In this article, we provide you more information about selling courses using WooCommerce.', 'woocommerce' ) );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_MARKETING );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'learn-more',
			__( 'Learn more', 'woocommerce' ),
			'https://woocommerce.com/posts/how-to-sell-online-courses-wordpress/?utm_source=inbox&utm_medium=product',
			Note::E_WC_ADMIN_NOTE_ACTIONED
		);

		return $note;
	}
}
