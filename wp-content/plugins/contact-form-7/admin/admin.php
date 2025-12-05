<?php

require_once WPCF7_PLUGIN_DIR . '/admin/includes/admin-functions.php';
require_once WPCF7_PLUGIN_DIR . '/admin/includes/help-tabs.php';
require_once WPCF7_PLUGIN_DIR . '/admin/includes/tag-generator.php';
require_once WPCF7_PLUGIN_DIR . '/admin/includes/welcome-panel.php';
require_once WPCF7_PLUGIN_DIR . '/admin/includes/config-validator.php';


add_action(
	'admin_init',
	static function () {
		do_action( 'wpcf7_admin_init' );
	},
	10, 0
);


add_action(
	'admin_menu',
	'wpcf7_admin_menu',
	9, 0
);

function wpcf7_admin_menu() {
	do_action( 'wpcf7_admin_menu' );

	add_menu_page(
		__( 'Contact Form 7', 'contact-form-7' ),
		__( 'Contact', 'contact-form-7' )
			. wpcf7_admin_menu_change_notice(),
		'wpcf7_read_contact_forms',
		'wpcf7',
		'wpcf7_admin_management_page',
		'dashicons-email',
		30
	);

	$edit = add_submenu_page( 'wpcf7',
		__( 'Edit Contact Form', 'contact-form-7' ),
		__( 'Contact Forms', 'contact-form-7' )
			. wpcf7_admin_menu_change_notice( 'wpcf7' ),
		'wpcf7_read_contact_forms',
		'wpcf7',
		'wpcf7_admin_management_page'
	);

	add_action( 'load-' . $edit, 'wpcf7_load_contact_form_admin', 10, 0 );

	$addnew = add_submenu_page( 'wpcf7',
		__( 'Add Contact Form', 'contact-form-7' ),
		__( 'Add Contact Form', 'contact-form-7' )
			. wpcf7_admin_menu_change_notice( 'wpcf7-new' ),
		'wpcf7_edit_contact_forms',
		'wpcf7-new',
		'wpcf7_admin_add_new_page'
	);

	add_action( 'load-' . $addnew, 'wpcf7_load_contact_form_admin', 10, 0 );

	$integration = WPCF7_Integration::get_instance();

	if ( $integration->service_exists() ) {
		$integration = add_submenu_page( 'wpcf7',
			__( 'Integration with External API', 'contact-form-7' ),
			__( 'Integration', 'contact-form-7' )
				. wpcf7_admin_menu_change_notice( 'wpcf7-integration' ),
			'wpcf7_manage_integration',
			'wpcf7-integration',
			'wpcf7_admin_integration_page'
		);

		add_action( 'load-' . $integration, 'wpcf7_load_integration_page', 10, 0 );
	}
}


function wpcf7_admin_menu_change_notice( $menu_slug = '' ) {
	$counts = apply_filters( 'wpcf7_admin_menu_change_notice',
		array(
			'wpcf7' => 0,
			'wpcf7-new' => 0,
			'wpcf7-integration' => 0,
		)
	);

	if ( empty( $menu_slug ) ) {
		$count = absint( array_sum( $counts ) );
	} elseif ( isset( $counts[$menu_slug] ) ) {
		$count = absint( $counts[$menu_slug] );
	} else {
		$count = 0;
	}

	if ( $count ) {
		return sprintf(
			' <span class="update-plugins %1$d"><span class="plugin-count">%2$s</span></span>',
			$count,
			esc_html( number_format_i18n( $count ) )
		);
	}

	return '';
}


add_action(
	'admin_enqueue_scripts',
	'wpcf7_admin_enqueue_scripts',
	10, 1
);

function wpcf7_admin_enqueue_scripts( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'wpcf7' ) ) {
		return;
	}

	wp_enqueue_style( 'contact-form-7-admin',
		wpcf7_plugin_url( 'admin/includes/css/styles.css' ),
		array(), WPCF7_VERSION, 'all'
	);

	if ( wpcf7_is_rtl() ) {
		wp_enqueue_style( 'contact-form-7-admin-rtl',
			wpcf7_plugin_url( 'admin/includes/css/styles-rtl.css' ),
			array(), WPCF7_VERSION, 'all'
		);
	}

	$assets = include wpcf7_plugin_path( 'admin/includes/js/index.asset.php' );

	$assets = wp_parse_args( $assets, array(
		'dependencies' => array(),
		'version' => WPCF7_VERSION,
	) );

	wp_enqueue_script( 'wpcf7-admin',
		wpcf7_plugin_url( 'admin/includes/js/index.js' ),
		$assets['dependencies'],
		$assets['version'],
		array( 'in_footer' => true )
	);

	wp_set_script_translations( 'wpcf7-admin', 'contact-form-7' );

	$wpcf7_obj = array(
		'apiSettings' => array(
			'root' => sanitize_url( rest_url( 'contact-form-7/v1' ) ),
			'namespace' => 'contact-form-7/v1',
		),
	);

	$post = wpcf7_get_current_contact_form();

	if ( $post ) {
		$wpcf7_obj = array_merge( $wpcf7_obj, array(
			'nonce' => array(
				'save' => wp_create_nonce(
					sprintf(
						'wpcf7-save-contact-form_%s',
						$post->initial() ? -1 : $post->id()
					)
				),
				'copy' => wp_create_nonce(
					sprintf(
						'wpcf7-copy-contact-form_%s',
						$post->initial() ? -1 : $post->id()
					)
				),
				'delete' => wp_create_nonce(
					sprintf(
						'wpcf7-delete-contact-form_%s',
						$post->initial() ? -1 : $post->id()
					)
				),
			),
			'configValidator' => array(
				'errors' => array(),
				'docUrl' => WPCF7_ConfigValidator::get_doc_link(),
			),
		) );

		if (
			current_user_can( 'wpcf7_edit_contact_form', $post->id() ) and
			wpcf7_validate_configuration()
		) {
			$config_validator = new WPCF7_ConfigValidator( $post );
			$config_validator->restore();

			$wpcf7_obj['configValidator'] = array_merge(
				$wpcf7_obj['configValidator'],
				array(
					'errors' => $config_validator->collect_error_messages(
						array( 'decodes_html_entities' => true )
					),
				)
			);
		}
	}

	wp_add_inline_script( 'wpcf7-admin',
		sprintf(
			'var wpcf7 = %s;',
			wp_json_encode( $wpcf7_obj, JSON_PRETTY_PRINT )
		),
		'before'
	);
}


add_filter(
	'set_screen_option_wpcf7_contact_forms_per_page',
	static function ( $result, $option, $value ) {
		$wpcf7_screens = array(
			'wpcf7_contact_forms_per_page',
		);

		if ( in_array( $option, $wpcf7_screens, true ) ) {
			$result = $value;
		}

		return $result;
	},
	10, 3
);


function wpcf7_load_contact_form_admin() {
	global $plugin_page;

	$action = wpcf7_current_action();

	do_action( 'wpcf7_admin_load',
		wpcf7_superglobal_get( 'page' ),
		$action
	);

	if ( 'save' === $action ) {
		$id = wpcf7_superglobal_post( 'post_ID', '-1' );

		check_admin_referer( 'wpcf7-save-contact-form_' . $id );

		if ( ! current_user_can( 'wpcf7_edit_contact_form', $id ) ) {
			wp_die(
				esc_html( __( 'You are not allowed to edit this item.', 'contact-form-7' ) )
			);
		}

		$contact_form = wpcf7_save_contact_form(
			array_merge(
				wp_unslash( $_REQUEST ),
				array(
					'id' => $id,
					'title' => wpcf7_superglobal_post( 'post_title', null ),
					'locale' => wpcf7_superglobal_post( 'wpcf7-locale', null ),
					'form' => wpcf7_superglobal_post( 'wpcf7-form', '' ),
					'mail' => wpcf7_superglobal_post( 'wpcf7-mail', array() ),
					'mail_2' => wpcf7_superglobal_post( 'wpcf7-mail-2', array() ),
					'messages' => wpcf7_superglobal_post( 'wpcf7-messages', array() ),
					'additional_settings' => wpcf7_superglobal_post( 'wpcf7-additional-settings', '' ),
				)
			)
		);

		if ( $contact_form and wpcf7_validate_configuration() ) {
			$config_validator = new WPCF7_ConfigValidator( $contact_form );
			$config_validator->validate();
			$config_validator->save();
		}

		$query = array(
			'post' => $contact_form ? $contact_form->id() : 0,
			'active-tab' => wpcf7_canonicalize_name(
				wpcf7_superglobal_post( 'active-tab' )
			),
		);

		if ( ! $contact_form ) {
			$query['message'] = 'failed';
		} elseif ( -1 === (int) $id ) {
			$query['message'] = 'created';
		} else {
			$query['message'] = 'saved';
		}

		$redirect_to = add_query_arg( $query, menu_page_url( 'wpcf7', false ) );
		wp_safe_redirect( $redirect_to );
		exit();
	}

	if ( 'copy' === $action ) {
		$id = absint( $_POST['post_ID'] ?? $_REQUEST['post'] ?? '' );

		check_admin_referer( 'wpcf7-copy-contact-form_' . $id );

		if ( ! current_user_can( 'wpcf7_edit_contact_form', $id ) ) {
			wp_die(
				esc_html( __( 'You are not allowed to edit this item.', 'contact-form-7' ) )
			);
		}

		$query = array();

		if ( $contact_form = wpcf7_contact_form( $id ) ) {
			$new_contact_form = $contact_form->copy();
			$new_contact_form->save();

			$query['post'] = $new_contact_form->id();
			$query['message'] = 'created';
		}

		$redirect_to = add_query_arg( $query, menu_page_url( 'wpcf7', false ) );

		wp_safe_redirect( $redirect_to );
		exit();
	}

	if ( 'delete' === $action ) {
		$nonce_action = 'bulk-posts';

		if (
			$post_id = wpcf7_superglobal_post( 'post_ID' ) or
			! is_array( $post_id = wpcf7_superglobal_request( 'post', array() ) )
		) {
			$nonce_action = sprintf( 'wpcf7-delete-contact-form_%s', $post_id );
		}

		check_admin_referer( $nonce_action );

		$posts = array_filter( (array) $post_id );

		$deleted = 0;

		foreach ( $posts as $post ) {
			$post = WPCF7_ContactForm::get_instance( $post );

			if ( empty( $post ) ) {
				continue;
			}

			if ( ! current_user_can( 'wpcf7_delete_contact_form', $post->id() ) ) {
				wp_die(
					esc_html( __( 'You are not allowed to delete this item.', 'contact-form-7' ) )
				);
			}

			if ( ! $post->delete() ) {
				wp_die(
					esc_html( __( 'Error in deleting.', 'contact-form-7' ) )
				);
			}

			$deleted += 1;
		}

		$query = array();

		if ( ! empty( $deleted ) ) {
			$query['message'] = 'deleted';
		}

		$redirect_to = add_query_arg( $query, menu_page_url( 'wpcf7', false ) );

		wp_safe_redirect( $redirect_to );
		exit();
	}

	$post = null;

	if ( 'wpcf7-new' === $plugin_page ) {
		$post = WPCF7_ContactForm::get_template( array(
			'locale' => wpcf7_superglobal_get( 'locale', null ),
		) );
	} elseif ( $post_id = wpcf7_superglobal_get( 'post' ) ) {
		$post = WPCF7_ContactForm::get_instance( $post_id );
	}

	$current_screen = get_current_screen();

	$help_tabs = new WPCF7_Help_Tabs( $current_screen );

	if ( $post and current_user_can( 'wpcf7_edit_contact_form', $post->id() ) ) {
		$help_tabs->set_help_tabs( 'edit' );
	} else {
		$help_tabs->set_help_tabs( 'list' );

		if ( ! class_exists( 'WPCF7_Contact_Form_List_Table' ) ) {
			require_once WPCF7_PLUGIN_DIR . '/admin/includes/class-contact-forms-list-table.php';
		}

		add_filter(
			'manage_' . $current_screen->id . '_columns',
			array( 'WPCF7_Contact_Form_List_Table', 'define_columns' ),
			10, 0
		);

		add_screen_option( 'per_page', array(
			'default' => 20,
			'option' => 'wpcf7_contact_forms_per_page',
		) );
	}
}


function wpcf7_admin_management_page() {
	if ( $post = wpcf7_get_current_contact_form() ) {
		$post_id = $post->initial() ? -1 : $post->id();

		require_once WPCF7_PLUGIN_DIR . '/admin/includes/editor.php';
		require_once WPCF7_PLUGIN_DIR . '/admin/edit-contact-form.php';
		return;
	}

	if (
		'validate' === wpcf7_current_action() and
		wpcf7_validate_configuration() and
		current_user_can( 'wpcf7_edit_contact_forms' )
	) {
		wpcf7_admin_bulk_validate_page();
		return;
	}

	$list_table = new WPCF7_Contact_Form_List_Table();
	$list_table->prepare_items();

	$formatter = new WPCF7_HTMLFormatter( array(
		'allowed_html' => array_merge( wpcf7_kses_allowed_html(), array(
			'form' => array(
				'method' => true,
			),
		) ),
	) );

	$formatter->append_start_tag( 'div', array(
		'class' => 'wrap',
		'id' => 'wpcf7-contact-form-list-table',
	) );

	$formatter->append_start_tag( 'h1', array(
		'class' => 'wp-heading-inline',
	) );

	$formatter->append_preformatted(
		esc_html( __( 'Contact Forms', 'contact-form-7' ) )
	);

	$formatter->end_tag( 'h1' );

	if ( current_user_can( 'wpcf7_edit_contact_forms' ) ) {
		$formatter->append_preformatted(
			wpcf7_link(
				menu_page_url( 'wpcf7-new', false ),
				__( 'Add Contact Form', 'contact-form-7' ),
				array( 'class' => 'page-title-action' )
			)
		);
	}

	if ( $search_keyword = wpcf7_superglobal_request( 's' ) ) {
		$formatter->append_start_tag( 'span', array(
			'class' => 'subtitle',
		) );

		$formatter->append_preformatted(
			sprintf(
				/* translators: %s: Search query. */
				__( 'Search results for: <strong>%s</strong>', 'contact-form-7' ),
				esc_html( $search_keyword )
			)
		);

		$formatter->end_tag( 'span' );
	}

	$formatter->append_start_tag( 'hr', array(
		'class' => 'wp-header-end',
	) );

	$formatter->call_user_func( static function () {
		do_action( 'wpcf7_admin_warnings',
			'wpcf7', wpcf7_current_action(), null
		);

		wpcf7_welcome_panel();

		do_action( 'wpcf7_admin_notices',
			'wpcf7', wpcf7_current_action(), null
		);
	} );

	$formatter->append_start_tag( 'form', array(
		'method' => 'get',
	) );

	$formatter->append_start_tag( 'input', array(
		'type' => 'hidden',
		'name' => 'page',
		'value' => wpcf7_superglobal_request( 'page' ),
	) );

	$formatter->call_user_func( static function () use ( $list_table ) {
		$list_table->search_box(
			__( 'Search Contact Forms', 'contact-form-7' ),
			'wpcf7-contact'
		);

		$list_table->display();
	} );

	$formatter->print();
}


function wpcf7_admin_add_new_page() {
	$post = wpcf7_get_current_contact_form();

	if ( ! $post ) {
		$post = WPCF7_ContactForm::get_template();
	}

	$post_id = -1;

	require_once WPCF7_PLUGIN_DIR . '/admin/includes/editor.php';
	require_once WPCF7_PLUGIN_DIR . '/admin/edit-contact-form.php';
}


function wpcf7_load_integration_page() {
	do_action( 'wpcf7_admin_load',
		wpcf7_superglobal_get( 'page' ),
		wpcf7_current_action()
	);

	$integration = WPCF7_Integration::get_instance();

	if (
		$service_name = wpcf7_superglobal_request( 'service' ) and
		$integration->service_exists( $service_name )
	) {
		$service = $integration->get_service( $service_name );
		$service->load( wpcf7_current_action() );
	}

	$help_tabs = new WPCF7_Help_Tabs( get_current_screen() );
	$help_tabs->set_help_tabs( 'integration' );
}


function wpcf7_admin_integration_page() {
	$integration = WPCF7_Integration::get_instance();

	$service_name = wpcf7_superglobal_request( 'service' );
	$service = null;

	if ( $service_name and $integration->service_exists( $service_name ) ) {
		$service = $integration->get_service( $service_name );
	}

	$formatter = new WPCF7_HTMLFormatter( array(
		'allowed_html' => array_merge( wpcf7_kses_allowed_html(), array(
			'form' => array(
				'action' => true,
				'method' => true,
			),
		) ),
	) );

	$formatter->append_start_tag( 'div', array(
		'class' => 'wrap',
		'id' => 'wpcf7-integration',
	) );

	$formatter->append_start_tag( 'h1' );

	$formatter->append_preformatted(
		esc_html( __( 'Integration with External API', 'contact-form-7' ) )
	);

	$formatter->end_tag( 'h1' );

	$formatter->append_start_tag( 'p' );

	$formatter->append_preformatted(
		sprintf(
			/* translators: %s: URL to support page about integration with external APIs */
			__( 'You can expand the possibilities of your contact forms by integrating them with external services. For details, see <a href="%s">Integration with external APIs</a>.', 'contact-form-7' ),
			__( 'https://contactform7.com/integration-with-external-apis/', 'contact-form-7' )
		)
	);

	$formatter->end_tag( 'p' );

	$formatter->call_user_func(
		static function () use ( $integration, $service, $service_name ) {
			do_action( 'wpcf7_admin_warnings',
				'wpcf7-integration', wpcf7_current_action(), $service
			);

			do_action( 'wpcf7_admin_notices',
				'wpcf7-integration', wpcf7_current_action(), $service
			);

			if ( $service ) {
				$message = wpcf7_superglobal_request( 'message' );
				$service->admin_notice( $message );

				$integration->list_services( array(
					'include' => $service_name,
				) );
			} else {
				$integration->list_services();
			}
		}
	);

	$formatter->print();
}


add_action( 'wpcf7_admin_notices', 'wpcf7_admin_updated_message', 10, 3 );

function wpcf7_admin_updated_message( $page, $action, $object ) {
	if ( ! in_array( $page, array( 'wpcf7', 'wpcf7-new' ), true ) ) {
		return;
	}

	$message_type = wpcf7_superglobal_request( 'message' );

	if ( ! $message_type ) {
		return;
	}

	$notice_type = 'success';

	if ( 'created' === $message_type ) {
		$message = __( 'Contact form created.', 'contact-form-7' );
	} elseif ( 'saved' === $message_type ) {
		$message = __( 'Contact form saved.', 'contact-form-7' );
	} elseif ( 'deleted' === $message_type ) {
		$message = __( 'Contact form deleted.', 'contact-form-7' );
	} elseif ( 'failed' === $message_type ) {
		$notice_type = 'error';
		$message = __( 'There was an error saving the contact form.', 'contact-form-7' );
	} elseif ( 'validated' === $message_type ) {
		$bulk_validate = WPCF7::get_option( 'bulk_validate', array() );
		$count_invalid = absint( $bulk_validate['count_invalid'] ?? 0 );

		if ( $count_invalid ) {
			$notice_type = 'warning';

			$message = sprintf(
				/* translators: %s: number of contact forms */
				_n(
					'Configuration validation completed. %s invalid contact form was found.',
					'Configuration validation completed. %s invalid contact forms were found.',
					$count_invalid, 'contact-form-7'
				),
				number_format_i18n( $count_invalid )
			);
		} else {
			$message = __( 'Configuration validation completed. No invalid contact form was found.', 'contact-form-7' );
		}
	}

	if ( ! empty( $message ) ) {
		wp_admin_notice(
			$message,
			array( 'type' => $notice_type )
		);
	}
}


add_filter( 'plugin_action_links', 'wpcf7_plugin_action_links', 10, 2 );

function wpcf7_plugin_action_links( $links, $file ) {
	if ( WPCF7_PLUGIN_BASENAME !== $file ) {
		return $links;
	}

	if ( ! current_user_can( 'wpcf7_read_contact_forms' ) ) {
		return $links;
	}

	$settings_link = wpcf7_link(
		menu_page_url( 'wpcf7', false ),
		__( 'Settings', 'contact-form-7' )
	);

	array_unshift( $links, $settings_link );

	return $links;
}


add_action( 'wpcf7_admin_warnings', 'wpcf7_old_wp_version_error', 10, 3 );

function wpcf7_old_wp_version_error( $page, $action, $object ) {
	$wp_version = get_bloginfo( 'version' );

	if ( version_compare( $wp_version, WPCF7_REQUIRED_WP_VERSION, '<' ) ) {
		wp_admin_notice(
			sprintf(
				/* translators: 1: version of Contact Form 7, 2: version of WordPress, 3: URL */
				__( '<strong>Contact Form 7 %1$s requires WordPress %2$s or higher.</strong> Please <a href="%3$s">update WordPress</a> first.', 'contact-form-7' ),
				WPCF7_VERSION,
				WPCF7_REQUIRED_WP_VERSION,
				admin_url( 'update-core.php' )
			),
			array( 'type' => 'warning' )
		);
	}
}


add_action( 'wpcf7_admin_warnings', 'wpcf7_not_allowed_to_edit', 10, 3 );

function wpcf7_not_allowed_to_edit( $page, $action, $object ) {
	if ( $object instanceof WPCF7_ContactForm ) {
		$contact_form = $object;
	} else {
		return;
	}

	if ( ! current_user_can( 'wpcf7_edit_contact_form', $contact_form->id() ) ) {
		wp_admin_notice(
			__( 'You are not allowed to edit this contact form.', 'contact-form-7' ),
			array( 'type' => 'warning' )
		);
	}
}


add_action( 'wpcf7_admin_warnings', 'wpcf7_ctct_deprecated_warning', 10, 3 );

function wpcf7_ctct_deprecated_warning( $page, $action, $object ) {
	$service = WPCF7_ConstantContact::get_instance();

	if ( $service->is_active() ) {
		wp_admin_notice(
			__( 'Contact Form 7 has completed the <a href="https://contactform7.com/2025/01/08/complete-removal-of-constant-contact-integration/">removal of the Constant Contact integration</a>. We recommend <a href="https://contactform7.com/sendinblue-integration/">Brevo</a> as an alternative.', 'contact-form-7' ),
			array( 'type' => 'warning' )
		);
	}
}


add_action( 'wpcf7_admin_warnings', 'wpcf7_captcha_future_warning', 10, 3 );

function wpcf7_captcha_future_warning( $page, $action, $object ) {
	$service = WPCF7_RECAPTCHA::get_instance();

	if ( $service->is_active() ) {
		wp_admin_notice(
			__( '<strong>Attention reCAPTCHA users:</strong> Google attempts to make all reCAPTCHA users migrate to reCAPTCHA Enterprise, meaning Google charges you for API calls exceeding the free tier. Contact Form 7 supports <a href="https://contactform7.com/turnstile-integration/">Cloudflare Turnstile</a>, and we recommend it unless you have reasons to use reCAPTCHA.', 'contact-form-7' ),
			array( 'type' => 'warning' )
		);
	}
}
