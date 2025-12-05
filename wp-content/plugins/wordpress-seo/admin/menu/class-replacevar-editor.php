<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Menu
 */

/**
 * Renders a replacement variable editor.
 */
class WPSEO_Replacevar_Editor {

	/**
	 * Yoast Forms instance.
	 *
	 * @var Yoast_Form
	 */
	private $yform;

	/**
	 * The arguments required for the div to render.
	 *
	 * @var array {
	 *      @type string $title                   The title field id.
	 *      @type string $description             The description field id.
	 *      @type string $page_type_recommended   The page type for the context of the recommended replace vars.
	 *      @type string $page_type_specific      The page type for the context of the editor specific replace vars.
	 *      @type bool   $paper_style             Optional. Whether the editor has paper style.
	 *      @type string $label_title             Optional. The label to use for the title field.
	 *      @type string $label_description       Optional. The label to use for the description field.
	 *      @type string $description_placeholder Optional. The placeholder text to use for the description field.
	 *      @type bool   $has_new_badge           Optional. Whether to show the "New" badge.
	 *      @type bool   $has_premium_badge       Optional. Whether to show the "Premium" badge.
	 * }
	 */
	private $arguments;

	/**
	 * Constructs the object.
	 *
	 * @param Yoast_Form $yform     Yoast forms.
	 * @param array      $arguments {
	 *      The arguments that can be given.
	 *
	 *      @type string $title                   The title field id.
	 *      @type string $description             The description field id.
	 *      @type string $page_type_recommended   The page type for the context of the recommended replace vars.
	 *      @type string $page_type_specific      The page type for the context of the editor specific replace vars.
	 *      @type bool   $paper_style             Optional. Whether the editor has paper style.
	 *      @type string $label_title             Optional. The label to use for the title field.
	 *      @type string $label_description       Optional. The label to use for the description field.
	 *      @type string $description_placeholder Optional. The placeholder text to use for the description field.
	 *      @type bool   $has_new_badge           Optional. Whether to show the "New" badge.
	 *      @type bool   $has_premium_badge       Optional. Whether to show the "Premium" badge.
	 * }
	 */
	public function __construct( Yoast_Form $yform, $arguments ) {
		$arguments = wp_parse_args(
			$arguments,
			[
				'paper_style'             => true,
				'label_title'             => '',
				'label_description'       => '',
				'description_placeholder' => '',
				'has_new_badge'           => false,
				'is_disabled'             => false,
				'has_premium_badge'       => false,
			]
		);

		$this->validate_arguments( $arguments );

		$this->yform     = $yform;
		$this->arguments = [
			'title'                   => (string) $arguments['title'],
			'description'             => (string) $arguments['description'],
			'page_type_recommended'   => (string) $arguments['page_type_recommended'],
			'page_type_specific'      => (string) $arguments['page_type_specific'],
			'paper_style'             => (bool) $arguments['paper_style'],
			'label_title'             => (string) $arguments['label_title'],
			'label_description'       => (string) $arguments['label_description'],
			'description_placeholder' => (string) $arguments['description_placeholder'],
			'has_new_badge'           => (bool) $arguments['has_new_badge'],
			'is_disabled'             => (bool) $arguments['is_disabled'],
			'has_premium_badge'       => (bool) $arguments['has_premium_badge'],
		];
	}

	/**
	 * Renders a div for the react application to mount to, and hidden inputs where
	 * the app should store it's value so they will be properly saved when the form
	 * is submitted.
	 *
	 * @return void
	 */
	public function render() {
		$this->yform->hidden( $this->arguments['title'], $this->arguments['title'] );
		$this->yform->hidden( $this->arguments['description'], $this->arguments['description'] );

		printf(
			'<div
				data-react-replacevar-editor
				data-react-replacevar-title-field-id="%1$s"
				data-react-replacevar-metadesc-field-id="%2$s"
				data-react-replacevar-page-type-recommended="%3$s"
				data-react-replacevar-page-type-specific="%4$s"
				data-react-replacevar-paper-style="%5$s"
				data-react-replacevar-label-title="%6$s"
				data-react-replacevar-label-description="%7$s"
				data-react-replacevar-description-placeholder="%8$s"
				data-react-replacevar-has-new-badge="%9$s"
				data-react-replacevar-is-disabled="%10$s"
				data-react-replacevar-has-premium-badge="%11$s"
			></div>',
			esc_attr( $this->arguments['title'] ),
			esc_attr( $this->arguments['description'] ),
			esc_attr( $this->arguments['page_type_recommended'] ),
			esc_attr( $this->arguments['page_type_specific'] ),
			esc_attr( $this->arguments['paper_style'] ),
			esc_attr( $this->arguments['label_title'] ),
			esc_attr( $this->arguments['label_description'] ),
			esc_attr( $this->arguments['description_placeholder'] ),
			esc_attr( $this->arguments['has_new_badge'] ),
			esc_attr( $this->arguments['is_disabled'] ),
			esc_attr( $this->arguments['has_premium_badge'] )
		);
	}

	/**
	 * Validates the replacement variable editor arguments.
	 *
	 * @param array $arguments The arguments to validate.
	 *
	 * @return void
	 *
	 * @throws InvalidArgumentException Thrown when not all required arguments are present.
	 */
	protected function validate_arguments( array $arguments ) {
		$required_arguments = [
			'title',
			'description',
			'page_type_recommended',
			'page_type_specific',
			'paper_style',
		];

		foreach ( $required_arguments as $field_name ) {
			if ( ! array_key_exists( $field_name, $arguments ) ) {
				throw new InvalidArgumentException(
					sprintf(
						/* translators: %1$s expands to the missing field name.  */
						__( 'Not all required fields are given. Missing field %1$s', 'wordpress-seo' ),
						$field_name
					)
				);
			}
		}
	}
}
