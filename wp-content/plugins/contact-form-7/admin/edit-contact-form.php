<?php

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$save_button = sprintf(
	'<input %s />',
	wpcf7_format_atts( array(
		'type' => 'submit',
		'class' => 'button-primary',
		'name' => 'wpcf7-save',
		'value' => __( 'Save', 'contact-form-7' ),
	) )
);

$formatter = new WPCF7_HTMLFormatter( array(
	'allowed_html' => array_merge( wpcf7_kses_allowed_html(), array(
		'form' => array(
			'method' => true,
			'action' => true,
			'id' => true,
			'class' => true,
			'disabled' => true,
		),
	) ),
) );

$formatter->append_start_tag( 'div', array(
	'class' => 'wrap',
	'id' => 'wpcf7-contact-form-editor',
) );

$formatter->append_start_tag( 'h1', array(
	'class' => 'wp-heading-inline',
) );

$formatter->append_preformatted(
	esc_html( $post->initial()
		? __( 'Add Contact Form', 'contact-form-7' )
		: __( 'Edit Contact Form', 'contact-form-7' )
	)
);

$formatter->end_tag( 'h1' );

if ( ! $post->initial() and current_user_can( 'wpcf7_edit_contact_forms' ) ) {
	$formatter->append_whitespace();

	$formatter->append_preformatted(
		wpcf7_link(
			menu_page_url( 'wpcf7-new', false ),
			__( 'Add Contact Form', 'contact-form-7' ),
			array( 'class' => 'page-title-action' )
		)
	);
}

$formatter->append_start_tag( 'hr', array(
	'class' => 'wp-header-end',
) );

$formatter->call_user_func( static function () use ( $post ) {
	do_action( 'wpcf7_admin_warnings',
		$post->initial() ? 'wpcf7-new' : 'wpcf7',
		wpcf7_current_action(),
		$post
	);

	do_action( 'wpcf7_admin_notices',
		$post->initial() ? 'wpcf7-new' : 'wpcf7',
		wpcf7_current_action(),
		$post
	);
} );

if ( $post ) {
	$formatter->append_start_tag( 'form', array(
		'method' => 'post',
		'action' => esc_url( add_query_arg(
			array( 'post' => $post_id ),
			menu_page_url( 'wpcf7', false )
		) ),
		'id' => 'wpcf7-admin-form-element',
		'disabled' => ! current_user_can( 'wpcf7_edit_contact_form', $post_id ),
	) );

	if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
		$formatter->call_user_func( static function () use ( $post_id ) {
			wp_nonce_field( 'wpcf7-save-contact-form_' . $post_id );
		} );
	}

	$formatter->append_start_tag( 'input', array(
		'type' => 'hidden',
		'id' => 'post_ID',
		'name' => 'post_ID',
		'value' => (int) $post_id,
	) );

	$formatter->append_start_tag( 'input', array(
		'type' => 'hidden',
		'id' => 'wpcf7-locale',
		'name' => 'wpcf7-locale',
		'value' => $post->locale(),
	) );

	$formatter->append_start_tag( 'input', array(
		'type' => 'hidden',
		'id' => 'hiddenaction',
		'name' => 'action',
		'value' => 'save',
	) );

	$formatter->append_start_tag( 'input', array(
		'type' => 'hidden',
		'id' => 'active-tab',
		'name' => 'active-tab',
		'value' => wpcf7_superglobal_get( 'active-tab' ),
	) );

	$formatter->append_start_tag( 'div', array(
		'id' => 'poststuff',
	) );

	$formatter->append_start_tag( 'div', array(
		'id' => 'post-body',
		'class' => 'metabox-holder columns-2 wp-clearfix',
	) );

	$formatter->append_start_tag( 'div', array(
		'id' => 'post-body-content',
	) );

	$formatter->append_start_tag( 'div', array(
		'id' => 'titlediv',
	) );

	$formatter->append_start_tag( 'div', array(
		'id' => 'titlewrap',
	) );

	$formatter->append_start_tag( 'input', array(
		'type' => 'text',
		'name' => 'post_title',
		'value' => $post->initial() ? '' : $post->title(),
		'id' => 'title',
		'spellcheck' => 'true',
		'autocomplete' => 'off',
		'disabled' => ! current_user_can( 'wpcf7_edit_contact_form', $post_id ),
		'placeholder' => __( 'Enter title here', 'contact-form-7' ),
		'aria-label' => __( 'Enter title here', 'contact-form-7' ),
	) );

	$formatter->end_tag( 'div' ); // #titlewrap

	$formatter->append_start_tag( 'div', array(
		'class' => 'inside',
	) );

	if ( ! $post->initial() ) {
		if ( $shortcode = $post->shortcode() ) {
			$formatter->append_start_tag( 'p', array(
				'class' => 'description',
			) );

			$formatter->append_start_tag( 'label', array(
				'for' => 'wpcf7-shortcode',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'Copy this shortcode and paste it into your post, page, or text widget content:', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'label' );

			$formatter->append_whitespace();

			$formatter->append_start_tag( 'span', array(
				'class' => 'shortcode wp-ui-highlight',
			) );

			$formatter->append_start_tag( 'input', array(
				'type' => 'text',
				'id' => 'wpcf7-shortcode',
				'readonly' => true,
				'class' => 'large-text code selectable',
				'value' => $shortcode,
			) );

			$formatter->end_tag( 'p' );
		}

		if ( $shortcode = $post->shortcode( array( 'use_old_format' => true ) ) ) {
			$formatter->append_start_tag( 'p', array(
				'class' => 'description',
			) );

			$formatter->append_start_tag( 'label', array(
				'for' => 'wpcf7-shortcode-old',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'You can also use this old-style shortcode:', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'label' );

			$formatter->append_whitespace();

			$formatter->append_start_tag( 'span', array(
				'class' => 'shortcode old',
			) );

			$formatter->append_start_tag( 'input', array(
				'type' => 'text',
				'id' => 'wpcf7-shortcode-old',
				'readonly' => true,
				'class' => 'large-text code selectable',
				'value' => $shortcode,
			) );

			$formatter->end_tag( 'p' );
		}
	}

	$formatter->end_tag( 'div' ); // .inside
	$formatter->end_tag( 'div' ); // #titlediv
	$formatter->end_tag( 'div' ); // #post-body-content

	$formatter->append_start_tag( 'div', array(
		'id' => 'postbox-container-1',
		'class' => 'postbox-container',
	) );

	if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
		$formatter->append_start_tag( 'section', array(
			'id' => 'submitdiv',
			'class' => 'postbox',
		) );

		$formatter->append_start_tag( 'h2' );

		$formatter->append_preformatted(
			esc_html( __( 'Status', 'contact-form-7' ) )
		);

		$formatter->end_tag( 'h2' );

		$formatter->append_start_tag( 'div', array(
			'class' => 'inside',
		) );

		$formatter->append_start_tag( 'div', array(
			'class' => 'submitbox',
			'id' => 'submitpost',
		) );

		$formatter->append_start_tag( 'div', array(
			'id' => 'minor-publishing-actions',
		) );

		$formatter->append_start_tag( 'div', array(
			'class' => 'hidden',
		) );

		$formatter->append_start_tag( 'input', array(
			'type' => 'submit',
			'class' => 'button-primary',
			'name' => 'wpcf7-save',
			'value' => __( 'Save', 'contact-form-7' ),
		) );

		$formatter->end_tag( 'div' ); // .hidden

		if ( ! $post->initial() ) {
			$formatter->append_start_tag( 'input', array(
				'type' => 'submit',
				'name' => 'wpcf7-copy',
				'class' => 'copy button',
				'value' => __( 'Duplicate', 'contact-form-7' ),
			) );
		}

		$formatter->end_tag( 'div' ); // #minor-publishing-actions

		$formatter->append_start_tag( 'div', array(
			'id' => 'misc-publishing-actions',
		) );

		$formatter->call_user_func( static function () use ( $post_id ) {
			do_action( 'wpcf7_admin_misc_pub_section', $post_id );
		} );

		$formatter->end_tag( 'div' ); // #misc-publishing-actions

		$formatter->append_start_tag( 'div', array(
			'id' => 'major-publishing-actions',
		) );

		if ( ! $post->initial() ) {
			$formatter->append_start_tag( 'div', array(
				'id' => 'delete-action',
			) );

			$formatter->append_start_tag( 'input', array(
				'type' => 'submit',
				'name' => 'wpcf7-delete',
				'class' => 'delete submitdelete',
				'value' => __( 'Delete', 'contact-form-7' ),
			) );

			$formatter->end_tag( 'div' ); // #delete-action
		}

		$formatter->append_start_tag( 'div', array(
			'id' => 'publishing-action',
		) );

		$formatter->append_preformatted( '<span class="spinner"></span>' );
		$formatter->append_preformatted( $save_button );

		$formatter->end_tag( 'div' ); // #publishing-action

		$formatter->append_preformatted( '<div class="clear"></div>' );

		$formatter->end_tag( 'div' ); // #major-publishing-actions
		$formatter->end_tag( 'div' ); // #submitpost
		$formatter->end_tag( 'div' ); // .inside
		$formatter->end_tag( 'section' ); // #submitdiv
	}

	$formatter->append_start_tag( 'section', array(
		'id' => 'informationdiv',
		'class' => 'postbox',
	) );

	$formatter->append_start_tag( 'h2' );

	$formatter->append_preformatted(
		esc_html( __( 'Do you need help?', 'contact-form-7' ) )
	);

	$formatter->end_tag( 'h2' );

	$formatter->append_start_tag( 'div', array(
		'class' => 'inside',
	) );

	$formatter->append_start_tag( 'p' );

	$formatter->append_preformatted(
		esc_html( __( 'Here are some available options to help solve your problems.', 'contact-form-7' ) )
	);

	$formatter->end_tag( 'p' );

	$formatter->append_start_tag( 'ol' );

	$formatter->append_start_tag( 'li' );

	$formatter->append_preformatted(
		sprintf(
			/* translators: 1: URL to FAQ, 2: URL to docs */
			'<a href="%1$s">FAQ</a> and <a href="%2$s">docs</a>',
			__( 'https://contactform7.com/faq/', 'contact-form-7' ),
			__( 'https://contactform7.com/docs/', 'contact-form-7' )
		)
	);

	$formatter->append_start_tag( 'li' );

	$formatter->append_preformatted(
		wpcf7_link(
			__( 'https://wordpress.org/support/plugin/contact-form-7/', 'contact-form-7' ),
			__( 'Support forums', 'contact-form-7' )
		)
	);

	$formatter->append_start_tag( 'li' );

	$formatter->append_preformatted(
		wpcf7_link(
			__( 'https://contactform7.com/custom-development/', 'contact-form-7' ),
			__( 'Professional services', 'contact-form-7' )
		)
	);

	$formatter->end_tag( 'ol' );
	$formatter->end_tag( 'div' ); // .inside
	$formatter->end_tag( 'section' ); // #informationdiv
	$formatter->end_tag( 'div' ); // #postbox-container-1

	$formatter->append_start_tag( 'div', array(
		'id' => 'postbox-container-2',
		'class' => 'postbox-container',
	) );

	$formatter->append_start_tag( 'div', array(
		'id' => 'contact-form-editor',
	) );

	$formatter->call_user_func( static function () use ( $post, $post_id ) {
		$editor = new WPCF7_Editor( $post );
		$panels = array();

		if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
			$panels = array(
				'form-panel' => array(
					'title' => __( 'Form', 'contact-form-7' ),
					'callback' => 'wpcf7_editor_panel_form',
				),
				'mail-panel' => array(
					'title' => __( 'Mail', 'contact-form-7' ),
					'callback' => 'wpcf7_editor_panel_mail',
				),
				'messages-panel' => array(
					'title' => __( 'Messages', 'contact-form-7' ),
					'callback' => 'wpcf7_editor_panel_messages',
				),
			);

			$additional_settings = $post->prop( 'additional_settings' );

			if ( ! is_scalar( $additional_settings ) ) {
				$additional_settings = '';
			}

			$additional_settings = trim( $additional_settings );
			$additional_settings = explode( "\n", $additional_settings );
			$additional_settings = array_filter( $additional_settings );
			$additional_settings = count( $additional_settings );

			$panels['additional-settings-panel'] = array(
				'title' => $additional_settings
					? sprintf(
						/* translators: %d: number of additional settings */
						__( 'Additional Settings (%d)', 'contact-form-7' ),
						$additional_settings
					)
					: __( 'Additional Settings', 'contact-form-7' ),
				'callback' => 'wpcf7_editor_panel_additional_settings',
			);
		}

		$panels = apply_filters( 'wpcf7_editor_panels', $panels );

		foreach ( $panels as $id => $panel ) {
			$editor->add_panel( $id, $panel['title'], $panel['callback'] );
		}

		$editor->display();
	} );

	$formatter->end_tag( 'div' ); // #contact-form-editor

	if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
		$formatter->append_start_tag( 'p', array(
			'class' => 'submit',
		) );

		$formatter->append_preformatted( $save_button );

		$formatter->end_tag( 'p' );
	}

	$formatter->end_tag( 'div' ); // #postbox-container-2
	$formatter->end_tag( 'div' ); // #post-body

	$formatter->append_preformatted( '<br class="clear" />' );

	$formatter->end_tag( 'div' ); // #poststuff
	$formatter->end_tag( 'form' );
}

$formatter->end_tag( 'div' ); // .wrap

$formatter->print();

$tag_generator = WPCF7_TagGenerator::get_instance();
$tag_generator->print_panels( $post );

do_action( 'wpcf7_admin_footer', $post );
