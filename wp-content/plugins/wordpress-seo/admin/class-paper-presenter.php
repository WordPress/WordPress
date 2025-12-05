<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Class WPSEO_presenter_paper.
 */
class WPSEO_Paper_Presenter {

	/**
	 * Title of the paper.
	 *
	 * @var string
	 */
	private $title;

	/**
	 * The view variables.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * The path to the view file.
	 *
	 * @var string
	 */
	private $view_file;

	/**
	 * WPSEO_presenter_paper constructor.
	 *
	 * @param string      $title     The title of the paper.
	 * @param string|null $view_file Optional. The path to the view file. Use the content setting
	 *                               if do not wish to use a view file.
	 * @param array       $settings  Optional. Settings for the paper.
	 */
	public function __construct( $title, $view_file = null, array $settings = [] ) {
		$defaults = [
			'paper_id'                 => null,
			'paper_id_prefix'          => 'wpseo-',
			'collapsible'              => false,
			'collapsible_header_class' => '',
			'expanded'                 => false,
			'help_text'                => '',
			'title_after'              => '',
			'class'                    => '',
			'content'                  => '',
			'view_data'                => [],
		];

		$this->settings  = wp_parse_args( $settings, $defaults );
		$this->title     = $title;
		$this->view_file = $view_file;
	}

	/**
	 * Renders the collapsible paper and returns it as a string.
	 *
	 * @return string The rendered paper.
	 */
	public function get_output() {
		$view_variables = $this->get_view_variables();

		extract( $view_variables, EXTR_SKIP );

		$content = $this->settings['content'];

		if ( $this->view_file !== null ) {
			ob_start();
			require $this->view_file;
			$content = ob_get_clean();
		}

		ob_start();
		require WPSEO_PATH . 'admin/views/paper-collapsible.php';
		$rendered_output = ob_get_clean();

		return $rendered_output;
	}

	/**
	 * Retrieves the view variables.
	 *
	 * @return array The view variables.
	 */
	private function get_view_variables() {
		if ( $this->settings['help_text'] instanceof WPSEO_Admin_Help_Panel === false ) {
			$this->settings['help_text'] = new WPSEO_Admin_Help_Panel( '', '', '' );
		}

		$view_variables = [
			'class'                    => $this->settings['class'],
			'collapsible'              => $this->settings['collapsible'],
			'collapsible_config'       => $this->collapsible_config(),
			'collapsible_header_class' => $this->settings['collapsible_header_class'],
			'title_after'              => $this->settings['title_after'],
			'help_text'                => $this->settings['help_text'],
			'view_file'                => $this->view_file,
			'title'                    => $this->title,
			'paper_id'                 => $this->settings['paper_id'],
			'paper_id_prefix'          => $this->settings['paper_id_prefix'],
			'yform'                    => Yoast_Form::get_instance(),
		];

		return array_merge( $this->settings['view_data'], $view_variables );
	}

	/**
	 * Retrieves the collapsible config based on the settings.
	 *
	 * @return array The config.
	 */
	protected function collapsible_config() {
		if ( empty( $this->settings['collapsible'] ) ) {
			return [
				'toggle_icon' => '',
				'class'       => '',
				'expanded'    => '',
			];
		}

		if ( ! empty( $this->settings['expanded'] ) ) {
			return [
				'toggle_icon' => 'dashicons-arrow-up-alt2',
				'class'       => 'toggleable-container',
				'expanded'    => 'true',
			];
		}

		return [
			'toggle_icon' => 'dashicons-arrow-down-alt2',
			'class'       => 'toggleable-container toggleable-container-hidden',
			'expanded'    => 'false',
		];
	}
}
