<?php

class UM_Mail {

	function __construct() {

		add_filter('mandrill_nl2br', array(&$this, 'mandrill_nl2br') );

		$this->force_plain_text = '';

	}

	/***
	***	@mandrill compatibility
	***/
	function mandrill_nl2br($nl2br, $message = '') {

		// text emails
		if ( !um_get_option('email_html') ) {
			$nl2br = true;
		}

		return $nl2br;

	}

	/***
	***	@check If template exists
	***/
	function email_template( $template, $args = array() ) {
		$lang = '';
		$template_path = false;

		if ( function_exists('icl_get_current_language') ) {
			if ( icl_get_current_language() != 'en' ) {
				$lang = icl_get_current_language() . '/';
			}
		} else {
			
			$lang = get_locale();
			$arr_english_lang = array('en','en_US','en_NZ','en_ZA','en_AU','en_GB');

			if( in_array( $lang, $arr_english_lang ) || strpos( $lang , 'en_' ) > -1 || empty( $lang ) ||  $lang == 0 ){
				$lang = '';
			} else {
				$lang .= '/';
			}

		}
		
		if ( file_exists( get_stylesheet_directory() . '/ultimate-member/templates/email/' . $lang . $template . '.html' ) ) {
			$template_path = get_stylesheet_directory() . '/ultimate-member/templates/email/' . $lang . $template . '.html';
		} else {
			if ( isset( $args['path'] ) ) {
				$path = $args['path'] . $lang;
			} else {
				$path = um_path . 'templates/email/' . $lang;
			}

			if ( file_exists( $path . $template . '.html' ) ) {
				$template_path = $path . $template . '.html';
			}
		}

		return apply_filters( 'um_email_template_path', $template_path, $template, $args );

	}

	/***
	***	@sends an email to any user
	***/
	function send( $email, $template=null, $args = array() ) {

		if ( !$template ) return;
		if ( um_get_option( $template . '_on' ) != 1 ) return;
		if ( !is_email( $email ) ) return;

		$this->attachments = null;
		$this->headers = 'From: '. um_get_option('mail_from') .' <'. um_get_option('mail_from_addr') .'>' . "\r\n";

		$this->subject = um_get_option( $template . '_sub' );
		$this->subject = um_convert_tags( $this->subject, $args );

		if ( isset( $args['admin'] ) || isset( $args['plain_text'] ) ) {
			$this->force_plain_text = 'forced';
		}


		// HTML e-mail or text
		if ( um_get_option('email_html') && $this->email_template( $template, $args ) ) {
			add_filter( 'wp_mail_content_type', array(&$this, 'set_content_type') );
			$this->message = file_get_contents( $this->email_template( $template, $args ) );
		} else {
			$this->message = um_get_option( $template );
		}

		// Convert tags in body
		$this->message = um_convert_tags( $this->message, $args );
		
		// Send mail
		wp_mail( $email, $this->subject, $this->message, $this->headers, $this->attachments );
		remove_filter( 'wp_mail_content_type', array(&$this, 'set_content_type')  );

		// reset globals
		$this->force_plain_text = '';

	}

	/***
	***	@maybe sending HTML emails
	***/
	function set_content_type( $content_type ) {
		if ( $this->force_plain_text == 'forced' )
			return 'text/plain';

		if ( um_get_option('email_html') )
			return 'text/html';

		return 'text/plain';
	}


}
