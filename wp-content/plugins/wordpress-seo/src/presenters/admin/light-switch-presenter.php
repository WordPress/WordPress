<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Class Light_Switch_Presenter.
 *
 * @package Yoast\WP\SEO\Presenters\Admin
 */
class Light_Switch_Presenter extends Abstract_Presenter {

	/**
	 * The variable to create the checkbox for.
	 *
	 * @var string
	 */
	protected $var;

	/**
	 * The visual label text for the toggle.
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * Array of two visual labels for the buttons.
	 *
	 * @var array
	 */
	protected $buttons;

	/**
	 * The name of the underlying checkbox.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The variable current value.
	 *
	 * @var string|bool
	 */
	protected $value;

	/**
	 * Reverse order of buttons.
	 *
	 * @var bool
	 */
	protected $reverse;

	/**
	 * The inline Help HTML.
	 *
	 * @var string
	 */
	protected $help;

	/**
	 * Whether the visual label is displayed in strong text.
	 *
	 * @var bool
	 */
	protected $strong;

	/**
	 * The disabled attribute HTML.
	 *
	 * @var string
	 */
	protected $disabled_attribute;

	/**
	 * Light_Switch_Presenter constructor.
	 *
	 * @param string      $variable           The variable to create the checkbox for.
	 * @param string      $label              The visual label text for the toggle.
	 * @param array       $buttons            Array of two visual labels for the buttons (defaults Disabled/Enabled).
	 * @param string      $name               The name of the underlying checkbox.
	 * @param string|bool $value              The variable current value, to determine the checked attribute.
	 * @param bool        $reverse            Optional. Reverse order of buttons (default true).
	 * @param string      $help               Optional. Inline Help HTML that will be printed out before the toggle. Default is empty.
	 * @param bool        $strong             Optional. Whether the visual label is displayed in strong text. Default is false.
	 *                                        Starting from Yoast SEO 16.5, the visual label is forced to bold via CSS.
	 * @param string      $disabled_attribute Optional. The disabled HTML attribute. Default is empty.
	 */
	public function __construct(
		$variable,
		$label,
		$buttons,
		$name,
		$value,
		$reverse = true,
		$help = '',
		$strong = false,
		$disabled_attribute = ''
	) {
		$this->var                = $variable;
		$this->label              = $label;
		$this->buttons            = $buttons;
		$this->name               = $name;
		$this->value              = $value;
		$this->reverse            = $reverse;
		$this->help               = $help;
		$this->strong             = $strong;
		$this->disabled_attribute = $disabled_attribute;
	}

	/**
	 * Presents the light switch toggle.
	 *
	 * @return string The light switch's HTML.
	 */
	public function present() {
		if ( empty( $this->buttons ) ) {
			$this->buttons = [ \__( 'Disabled', 'wordpress-seo' ), \__( 'Enabled', 'wordpress-seo' ) ];
		}

		list( $off_button, $on_button ) = $this->buttons;

		$class = 'switch-light switch-candy switch-yoast-seo';

		if ( $this->reverse ) {
			$class .= ' switch-yoast-seo-reverse';
		}

		$help_class   = ! empty( $this->help ) ? ' switch-container__has-help' : '';
		$strong_class = ( $this->strong ) ? ' switch-light-visual-label__strong' : '';

		$output  = '<div class="switch-container' . $help_class . '">';
		$output .= \sprintf(
			'<span class="switch-light-visual-label%1$s" id="%2$s">%3$s</span>%4$s',
			$strong_class, // phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $strong_class output is hardcoded.
			\esc_attr( $this->var . '-label' ),
			\esc_html( $this->label ),
			$this->help // phpcs:ignore WordPress.Security.EscapeOutput -- Reason: The help contains HTML.
		);
		$output .= '<label class="' . $class . '"><b class="switch-yoast-seo-jaws-a11y">&nbsp;</b>';
		$output .= \sprintf(
			'<input type="checkbox" aria-labelledby="%1$s" id="%2$s" name="%3$s" value="on"%4$s%5$s/>',
			\esc_attr( $this->var . '-label' ),
			\esc_attr( $this->var ),
			\esc_attr( $this->name ),
			\checked( $this->value, 'on', false ), // phpcs:ignore WordPress.Security.EscapeOutput -- Reason: The output is hardcoded by WordPress.
			$this->disabled_attribute // phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $disabled_attribute output is hardcoded.
		);
		$output .= '<span aria-hidden="true">';
		$output .= '<span>' . \esc_html( $off_button ) . '</span>';
		$output .= '<span>' . \esc_html( $on_button ) . '</span>';
		$output .= '<a></a>';
		$output .= '</span></label><div class="clear"></div></div>';

		return $output;
	}
}
