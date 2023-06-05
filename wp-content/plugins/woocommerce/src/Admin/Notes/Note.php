<?php
/**
 * WooCommerce Admin (Dashboard) Notes.
 *
 * The WooCommerce admin notes class gets admin notes data from storage and checks validity.
 */

namespace Automattic\WooCommerce\Admin\Notes;

defined( 'ABSPATH' ) || exit;

/**
 * Note class.
 */
class Note extends \WC_Data {

	// Note types.
	const E_WC_ADMIN_NOTE_ERROR         = 'error';     // used for presenting error conditions.
	const E_WC_ADMIN_NOTE_WARNING       = 'warning';   // used for presenting warning conditions.
	const E_WC_ADMIN_NOTE_UPDATE        = 'update';    // i.e. used when a new version is available.
	const E_WC_ADMIN_NOTE_INFORMATIONAL = 'info';      // used for presenting informational messages.
	const E_WC_ADMIN_NOTE_MARKETING     = 'marketing'; // used for adding marketing messages.
	const E_WC_ADMIN_NOTE_SURVEY        = 'survey';    // used for adding survey messages.
	const E_WC_ADMIN_NOTE_EMAIL         = 'email';     // used for adding notes that will be sent by email.

	// Note status codes.
	const E_WC_ADMIN_NOTE_PENDING    = 'pending';    // the note is pending - hidden but not actioned.
	const E_WC_ADMIN_NOTE_UNACTIONED = 'unactioned'; // the note has not yet been actioned by a user.
	const E_WC_ADMIN_NOTE_ACTIONED   = 'actioned';   // the note has had its action completed by a user.
	const E_WC_ADMIN_NOTE_SNOOZED    = 'snoozed';    // the note has been snoozed by a user.
	const E_WC_ADMIN_NOTE_SENT       = 'sent';    // the note has been sent by email to the user.

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'admin-note';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'admin-note';

	/**
	 * Note constructor. Loads note data.
	 *
	 * @param mixed $data Note data, object, or ID.
	 */
	public function __construct( $data = '' ) {
		// Set default data here to allow `content_data` to be an object.
		$this->data = array(
			'name'          => '-',
			'type'          => self::E_WC_ADMIN_NOTE_INFORMATIONAL,
			'locale'        => 'en_US',
			'title'         => '-',
			'content'       => '-',
			'content_data'  => new \stdClass(),
			'status'        => self::E_WC_ADMIN_NOTE_UNACTIONED,
			'source'        => 'woocommerce',
			'date_created'  => '0000-00-00 00:00:00',
			'date_reminder' => '',
			'is_snoozable'  => false,
			'actions'       => array(),
			'layout'        => 'plain',
			'image'         => '',
			'is_deleted'    => false,
			'is_read'       => false,
		);

		parent::__construct( $data );

		if ( $data instanceof Note ) {
			$this->set_id( absint( $data->get_id() ) );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( is_object( $data ) && ! empty( $data->note_id ) ) {
			$this->set_id( $data->note_id );
			unset( $data->icon ); // Icons are deprecated.
			$this->set_props( (array) $data );
			$this->set_object_read( true );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = Notes::load_data_store();
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * Merge changes with data and clear.
	 *
	 * @since 3.0.0
	 */
	public function apply_changes() {
		$this->data = array_replace_recursive( $this->data, $this->changes ); // @codingStandardsIgnoreLine

		// Note actions need to be replaced wholesale.
		// Merging arrays doesn't allow for deleting note actions.
		if ( isset( $this->changes['actions'] ) ) {
			$this->data['actions'] = $this->changes['actions'];
		}

		$this->changes = array();
	}

	/*
	|--------------------------------------------------------------------------
	| Helpers
	|--------------------------------------------------------------------------
	|
	| Methods for getting allowed types, statuses.
	|
	*/

	/**
	 * Get allowed types.
	 *
	 * @return array
	 */
	public static function get_allowed_types() {
		$allowed_types = array(
			self::E_WC_ADMIN_NOTE_ERROR,
			self::E_WC_ADMIN_NOTE_WARNING,
			self::E_WC_ADMIN_NOTE_UPDATE,
			self::E_WC_ADMIN_NOTE_INFORMATIONAL,
			self::E_WC_ADMIN_NOTE_MARKETING,
			self::E_WC_ADMIN_NOTE_SURVEY,
			self::E_WC_ADMIN_NOTE_EMAIL,
		);

		return apply_filters( 'woocommerce_note_types', $allowed_types );
	}

	/**
	 * Get allowed statuses.
	 *
	 * @return array
	 */
	public static function get_allowed_statuses() {
		$allowed_statuses = array(
			self::E_WC_ADMIN_NOTE_PENDING,
			self::E_WC_ADMIN_NOTE_ACTIONED,
			self::E_WC_ADMIN_NOTE_UNACTIONED,
			self::E_WC_ADMIN_NOTE_SNOOZED,
			self::E_WC_ADMIN_NOTE_SENT,
		);

		return apply_filters( 'woocommerce_note_statuses', $allowed_statuses );
	}


	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the note object.
	|
	*/

	/**
	 * Returns all data for this object.
	 *
	 * Override \WC_Data::get_data() to avoid errantly including meta data
	 * from ID collisions with the posts table.
	 *
	 * @return array
	 */
	public function get_data() {
		return array_merge( array( 'id' => $this->get_id() ), $this->data );
	}

	/**
	 * Get note name.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get note type.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_type( $context = 'view' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Get note locale.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_locale( $context = 'view' ) {
		return $this->get_prop( 'locale', $context );
	}

	/**
	 * Get note title.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
	}

	/**
	 * Get note content.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_content( $context = 'view' ) {
		return $this->get_prop( 'content', $context );
	}

	/**
	 * Get note content data (i.e. values that would be needed for re-localization)
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return object
	 */
	public function get_content_data( $context = 'view' ) {
		return $this->get_prop( 'content_data', $context );
	}

	/**
	 * Get note status.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Get note source.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_source( $context = 'view' ) {
		return $this->get_prop( 'source', $context );
	}

	/**
	 * Get date note was created.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get date on which user should be reminded of the note (if any).
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_reminder( $context = 'view' ) {
		return $this->get_prop( 'date_reminder', $context );
	}

	/**
	 * Get note snoozability.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return bool   Whether or not the note can be snoozed.
	 */
	public function get_is_snoozable( $context = 'view' ) {
		return $this->get_prop( 'is_snoozable', $context );
	}

	/**
	 * Get actions on the note (if any).
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return array
	 */
	public function get_actions( $context = 'view' ) {
		return $this->get_prop( 'actions', $context );
	}

	/**
	 * Get action by action name on the note.
	 *
	 * @param  string $action_name The action name.
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return object the action.
	 */
	public function get_action( $action_name, $context = 'view' ) {
		$actions = $this->get_prop( 'actions', $context );

		$matching_action = null;
		foreach ( $actions as $i => $action ) {
			if ( $action->name === $action_name ) {
				$matching_action =& $actions[ $i ];
				break;
			}
		}
		return $matching_action;
	}

	/**
	 * Get note layout (the old notes won't have one).
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return array
	 */
	public function get_layout( $context = 'view' ) {
		return $this->get_prop( 'layout', $context );
	}

	/**
	 * Get note image (if any).
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return array
	 */
	public function get_image( $context = 'view' ) {
		return $this->get_prop( 'image', $context );
	}

	/**
	 * Get deleted status.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return array
	 */
	public function get_is_deleted( $context = 'view' ) {
		return $this->get_prop( 'is_deleted', $context );
	}

	/**
	 * Get is_read status.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return array
	 */
	public function get_is_read( $context = 'view' ) {
		return $this->get_prop( 'is_read', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Methods for setting note data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	|
	*/

	/**
	 * Set note name.
	 *
	 * @param string $name Note name.
	 */
	public function set_name( $name ) {
		// Don't allow empty names.
		if ( empty( $name ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note name prop cannot be empty.', 'woocommerce' ) );
		}

		$this->set_prop( 'name', $name );
	}

	/**
	 * Set note type.
	 *
	 * @param string $type Note type.
	 */
	public function set_type( $type ) {
		if ( empty( $type ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note type prop cannot be empty.', 'woocommerce' ) );
		}

		if ( ! in_array( $type, self::get_allowed_types(), true ) ) {
			$this->error(
				'admin_note_invalid_data',
				sprintf(
					/* translators: %s: admin note type. */
					__( 'The admin note type prop (%s) is not one of the supported types.', 'woocommerce' ),
					$type
				)
			);
		}

		$this->set_prop( 'type', $type );
	}

	/**
	 * Set note locale.
	 *
	 * @param string $locale Note locale.
	 */
	public function set_locale( $locale ) {
		if ( empty( $locale ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note locale prop cannot be empty.', 'woocommerce' ) );
		}

		$this->set_prop( 'locale', $locale );
	}

	/**
	 * Set note title.
	 *
	 * @param string $title Note title.
	 */
	public function set_title( $title ) {
		if ( empty( $title ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note title prop cannot be empty.', 'woocommerce' ) );
		}

		$this->set_prop( 'title', $title );
	}

	/**
	 * Set note icon (Deprecated).
	 *
	 * @param string $icon Note icon.
	 */
	public function set_icon( $icon ) {
		wc_deprecated_function( 'set_icon', '4.3' );
	}

	/**
	 * Set note content.
	 *
	 * @param string $content Note content.
	 */
	public function set_content( $content ) {
		$allowed_html = array(
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'a'      => array(
				'href'     => true,
				'rel'      => true,
				'name'     => true,
				'target'   => true,
				'download' => array(
					'valueless' => 'y',
				),
			),
			'p'      => array(),
		);

		$content = wp_kses( $content, $allowed_html );

		if ( empty( $content ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note content prop cannot be empty.', 'woocommerce' ) );
		}

		$this->set_prop( 'content', $content );
	}

	/**
	 * Set note data for potential re-localization.
	 *
	 * @todo Set a default empty array? https://github.com/woocommerce/woocommerce-admin/pull/1763#pullrequestreview-212442921.
	 * @param object $content_data Note data.
	 */
	public function set_content_data( $content_data ) {
		$allowed_type = false;

		// Make sure $content_data is stdClass Object or an array.
		if ( ! ( $content_data instanceof \stdClass ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note content_data prop must be an instance of stdClass.', 'woocommerce' ) );
		}

		$this->set_prop( 'content_data', $content_data );
	}

	/**
	 * Set note status.
	 *
	 * @param string $status Note status.
	 */
	public function set_status( $status ) {
		if ( empty( $status ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note status prop cannot be empty.', 'woocommerce' ) );
		}

		if ( ! in_array( $status, self::get_allowed_statuses(), true ) ) {
			$this->error(
				'admin_note_invalid_data',
				sprintf(
					/* translators: %s: admin note status property. */
					__( 'The admin note status prop (%s) is not one of the supported statuses.', 'woocommerce' ),
					$status
				)
			);
		}

		$this->set_prop( 'status', $status );
	}

	/**
	 * Set note source.
	 *
	 * @param string $source Note source.
	 */
	public function set_source( $source ) {
		if ( empty( $source ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note source prop cannot be empty.', 'woocommerce' ) );
		}

		$this->set_prop( 'source', $source );
	}

	/**
	 * Set date note was created. NULL is not allowed
	 *
	 * @param string|integer $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed.
	 */
	public function set_date_created( $date ) {
		if ( empty( $date ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note date prop cannot be empty.', 'woocommerce' ) );
		}

		if ( is_string( $date ) ) {
			$date = wc_string_to_timestamp( $date );
		}
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set date admin should be reminded of note. NULL IS allowed
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if there is no date.
	 */
	public function set_date_reminder( $date ) {
		if ( is_string( $date ) ) {
			$date = wc_string_to_timestamp( $date );
		}
		$this->set_date_prop( 'date_reminder', $date );
	}

	/**
	 * Set note snoozability.
	 *
	 * @param bool $is_snoozable Whether or not the note can be snoozed.
	 */
	public function set_is_snoozable( $is_snoozable ) {
		return $this->set_prop( 'is_snoozable', $is_snoozable );
	}

	/**
	 * Clear actions from a note.
	 */
	public function clear_actions() {
		$this->set_prop( 'actions', array() );
	}

	/**
	 * Set note layout.
	 *
	 * @param string $layout Note layout.
	 */
	public function set_layout( $layout ) {
		// If we don't receive a layout we will set it by default as "plain".
		if ( empty( $layout ) ) {
			$layout = 'plain';
		}
		$valid_layouts = array( 'banner', 'plain', 'thumbnail' );
		if ( in_array( $layout, $valid_layouts, true ) ) {
			$this->set_prop( 'layout', $layout );
		} else {
			$this->error( 'admin_note_invalid_data', __( 'The admin note layout has a wrong prop value.', 'woocommerce' ) );
		}
	}

	/**
	 * Set note image.
	 *
	 * @param string $image Note image.
	 */
	public function set_image( $image ) {
		$this->set_prop( 'image', $image );
	}

	/**
	 * Set note deleted status. NULL is not allowed
	 *
	 * @param bool $is_deleted Note deleted status.
	 */
	public function set_is_deleted( $is_deleted ) {
		$this->set_prop( 'is_deleted', $is_deleted );
	}

	/**
	 * Set note is_read status. NULL is not allowed
	 *
	 * @param bool $is_read Note is_read status.
	 */
	public function set_is_read( $is_read ) {
		$this->set_prop( 'is_read', $is_read );
	}

	/**
	 * Add an action to the note
	 *
	 * @param string  $name           Action name (not presented to user).
	 * @param string  $label          Action label (presented as button label).
	 * @param string  $url            Action URL, if navigation needed. Optional.
	 * @param string  $status         Status to transition parent Note to upon click. Defaults to 'actioned'.
	 * @param boolean $primary        Deprecated since version 3.4.0.
	 * @param string  $actioned_text The label to display after the note has been actioned but before it is dismissed in the UI.
	 */
	public function add_action(
		$name,
		$label,
		$url = '',
		$status = self::E_WC_ADMIN_NOTE_ACTIONED,
		$primary = false,
		$actioned_text = ''
	) {
		$name          = wc_clean( $name );
		$label         = wc_clean( $label );
		$query         = esc_url_raw( $url );
		$status        = wc_clean( $status );
		$actioned_text = wc_clean( $actioned_text );

		if ( empty( $name ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note action name prop cannot be empty.', 'woocommerce' ) );
		}

		if ( empty( $label ) ) {
			$this->error( 'admin_note_invalid_data', __( 'The admin note action label prop cannot be empty.', 'woocommerce' ) );
		}

		$action = array(
			'name'          => $name,
			'label'         => $label,
			'query'         => $query,
			'status'        => $status,
			'actioned_text' => $actioned_text,
			'nonce_name'    => null,
			'nonce_action'  => null,
		);

		$note_actions   = $this->get_prop( 'actions', 'edit' );
		$note_actions[] = (object) $action;
		$this->set_prop( 'actions', $note_actions );
	}

	/**
	 * Set actions on a note.
	 *
	 * @param array $actions Note actions.
	 */
	public function set_actions( $actions ) {
		$this->set_prop( 'actions', $actions );
	}

	/**
	 * Add a nonce to an existing note action.
	 *
	 * @link https://codex.wordpress.org/WordPress_Nonces
	 *
	 * @param string $note_action_name Name of action to add a nonce to.
	 * @param string $nonce_action The nonce action.
	 * @param string $nonce_name The nonce Name. This is used as the paramater name in the resulting URL for the action.
	 * @return void
	 * @throws \Exception If note name cannot be found.
	 */
	public function add_nonce_to_action( string $note_action_name, string $nonce_action, string $nonce_name ) {
		$actions = $this->get_prop( 'actions', 'edit' );

		$matching_action = null;
		foreach ( $actions as $i => $action ) {
			if ( $action->name === $note_action_name ) {
				$matching_action =& $actions[ $i ];
			}
		}

		if ( empty( $matching_action ) ) {
			throw new \Exception( sprintf( 'Could not find action %s in note %s', $note_action_name, $this->get_name() ) );
		}

		$matching_action->nonce_action = $nonce_action;
		$matching_action->nonce_name   = $nonce_name;

		$this->set_actions( $actions );
	}
}
