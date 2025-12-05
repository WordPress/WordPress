<?php

class WPCF7_Help_Tabs {

	private $screen;

	public function __construct( WP_Screen $screen ) {
		$this->screen = $screen;
	}

	public function set_help_tabs( $screen_type ) {
		switch ( $screen_type ) {
			case 'list':
				$this->screen->add_help_tab( array(
					'id' => 'list_overview',
					'title' => __( 'Overview', 'contact-form-7' ),
					'content' => $this->content( 'list_overview' ),
				) );

				$this->screen->add_help_tab( array(
					'id' => 'list_available_actions',
					'title' => __( 'Available Actions', 'contact-form-7' ),
					'content' => $this->content( 'list_available_actions' ),
				) );

				$this->sidebar();

				return;
			case 'edit':
				$this->screen->add_help_tab( array(
					'id' => 'edit_overview',
					'title' => __( 'Overview', 'contact-form-7' ),
					'content' => $this->content( 'edit_overview' ),
				) );

				$this->screen->add_help_tab( array(
					'id' => 'edit_form_tags',
					'title' => __( 'Form-tags', 'contact-form-7' ),
					'content' => $this->content( 'edit_form_tags' ),
				) );

				$this->screen->add_help_tab( array(
					'id' => 'edit_mail_tags',
					'title' => __( 'Mail-tags', 'contact-form-7' ),
					'content' => $this->content( 'edit_mail_tags' ),
				) );

				$this->sidebar();

				return;
			case 'integration':
				$this->screen->add_help_tab( array(
					'id' => 'integration_overview',
					'title' => __( 'Overview', 'contact-form-7' ),
					'content' => $this->content( 'integration_overview' ),
				) );

				$this->sidebar();

				return;
		}
	}

	private function content( $name ) {
		$content = array();

		$content['list_overview'] = '<p>' . __( 'On this screen, you can manage contact forms provided by Contact Form 7. You can manage an unlimited number of contact forms. Each contact form has a unique ID and Contact Form 7 shortcode ([contact-form-7 ...]). To insert a contact form into a post or a text widget, insert the shortcode into the target.', 'contact-form-7' ) . '</p>';

		$content['list_available_actions'] = '<p>' . __( 'Hovering over a row in the contact forms list will display action links that allow you to manage your contact form. You can perform the following actions:', 'contact-form-7' ) . '</p>';
		$content['list_available_actions'] .= '<p>' . __( '<strong>Edit</strong> - Navigates to the editing screen for that contact form. You can also reach that screen by clicking on the contact form title.', 'contact-form-7' ) . '</p>';
		$content['list_available_actions'] .= '<p>' . __( '<strong>Duplicate</strong> - Clones that contact form. A cloned contact form inherits all content from the original, but has a different ID.', 'contact-form-7' ) . '</p>';

		$content['edit_overview'] = '<p>' . __( 'On this screen, you can edit a contact form. A contact form is comprised of the following components:', 'contact-form-7' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( '<strong>Title</strong> is the title of a contact form. This title is only used for labeling a contact form, and can be edited.', 'contact-form-7' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( '<strong>Form</strong> is a content of HTML form. You can use arbitrary HTML, which is allowed inside a form element. You can also use Contact Form 7&#8217;s form-tags here.', 'contact-form-7' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( '<strong>Mail</strong> manages a mail template (headers and message body) that this contact form will send when users submit it. You can use Contact Form 7&#8217;s mail-tags here.', 'contact-form-7' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( '<strong>Mail (2)</strong> is an additional mail template that works similar to Mail. Mail (2) is different in that it is sent only when Mail has been sent successfully.', 'contact-form-7' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( 'In <strong>Messages</strong>, you can edit various types of messages used for this contact form. These messages are relatively short messages, like a validation error message you see when you leave a required field blank.', 'contact-form-7' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( '<strong>Additional Settings</strong> provides a place where you can customize the behavior of this contact form by adding code snippets.', 'contact-form-7' ) . '</p>';

		$content['edit_form_tags'] = '<p>' . __( 'A form-tag is a short code enclosed in square brackets used in a form content. A form-tag generally represents an input field, and its components can be separated into four parts: type, name, options, and values. Contact Form 7 supports several types of form-tags including text fields, number fields, date fields, checkboxes, radio buttons, menus, file-uploading fields, CAPTCHAs, and quiz fields.', 'contact-form-7' ) . '</p>';
		$content['edit_form_tags'] .= '<p>' . __( 'While form-tags have a comparatively complex syntax, you do not need to know the syntax to add form-tags because you can use the straightforward tag generator (<strong>Generate Tag</strong> button on this screen).', 'contact-form-7' ) . '</p>';

		$content['edit_mail_tags'] = '<p>' . __( 'A mail-tag is also a short code enclosed in square brackets that you can use in every Mail and Mail (2) field. A mail-tag represents a user input value through an input field of a corresponding form-tag.', 'contact-form-7' ) . '</p>';
		$content['edit_mail_tags'] .= '<p>' . __( 'There are also special mail-tags that have specific names, but do not have corresponding form-tags. They are used to represent meta information of form submissions like the submitter&#8217;s IP address or the URL of the page.', 'contact-form-7' ) . '</p>';

		$content['integration_overview'] = '<p>' . __( 'On this screen, you can manage services that are available through Contact Form 7. Using API will allow you to collaborate with any services that are available.', 'contact-form-7' ) . '</p>';
		$content['integration_overview'] .= '<p>' . __( 'You may need to first sign up for an account with the service that you plan to use. When you do so, you would need to authorize Contact Form 7 to access the service with your account.', 'contact-form-7' ) . '</p>';
		$content['integration_overview'] .= '<p>' . __( 'Any information you provide will not be shared with service providers without your authorization.', 'contact-form-7' ) . '</p>';

		if ( ! empty( $content[$name] ) ) {
			return $content[$name];
		}
	}

	public function sidebar() {
		$content = '<p><strong>' . __( 'For more information:', 'contact-form-7' ) . '</strong></p>';
		$content .= '<p>' . wpcf7_link( __( 'https://contactform7.com/docs/', 'contact-form-7' ), __( 'Docs', 'contact-form-7' ) ) . '</p>';
		$content .= '<p>' . wpcf7_link( __( 'https://contactform7.com/faq/', 'contact-form-7' ), __( 'FAQ', 'contact-form-7' ) ) . '</p>';
		$content .= '<p>' . wpcf7_link( __( 'https://contactform7.com/support/', 'contact-form-7' ), __( 'Support', 'contact-form-7' ) ) . '</p>';

		$this->screen->set_help_sidebar( $content );
	}
}
