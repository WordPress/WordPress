<?php
namespace Elementor;

use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor audio widget.
 *
 * Elementor widget that displays an audio player.
 *
 * @since 1.0.0
 */
class Widget_Audio extends Widget_Base {

	/**
	 * Current instance.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $_current_instance = [];

	/**
	 * Get widget name.
	 *
	 * Retrieve audio widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'audio';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve audio widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'SoundCloud', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve audio widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-headphones';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'audio', 'player', 'soundcloud', 'embed' ];
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register audio widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_audio',
			[
				'label' => esc_html__( 'SoundCloud', 'elementor' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'default' => [
					'url' => 'https://soundcloud.com/shchxango/john-coltrane-1963-my-favorite',
				],
				'options' => false,
			]
		);

		$this->add_control(
			'visual',
			[
				'label' => esc_html__( 'Visual Player', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'no',
				'options' => [
					'yes' => esc_html__( 'Yes', 'elementor' ),
					'no' => esc_html__( 'No', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'sc_options',
			[
				'label' => esc_html__( 'Additional Options', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sc_auto_play',
			[
				'label' => esc_html__( 'Autoplay', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'sc_buying',
			[
				'label' => esc_html__( 'Buy Button', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sc_liking',
			[
				'label' => esc_html__( 'Like Button', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sc_download',
			[
				'label' => esc_html__( 'Download Button', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sc_show_artwork',
			[
				'label' => esc_html__( 'Artwork', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
				'condition' => [
					'visual' => 'no',
				],
			]
		);

		$this->add_control(
			'sc_sharing',
			[
				'label' => esc_html__( 'Share Button', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sc_show_comments',
			[
				'label' => esc_html__( 'Comments', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sc_show_playcount',
			[
				'label' => esc_html__( 'Play Counts', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sc_show_user',
			[
				'label' => esc_html__( 'Username', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sc_color',
			[
				'label' => esc_html__( 'Controls Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render audio widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['link'] ) ) {
			return;
		}

		$this->_current_instance = $settings;

		add_filter( 'oembed_result', [ $this, 'filter_oembed_result' ], 50, 3 );
		$video_html = wp_oembed_get( $settings['link']['url'], wp_embed_defaults() );
		remove_filter( 'oembed_result', [ $this, 'filter_oembed_result' ], 50 );

		if ( $video_html ) : ?>
			<div class="elementor-soundcloud-wrapper">
				<?php Utils::print_wp_kses_extended( $video_html, [ 'iframe' ] ); ?>
			</div>
			<?php
		endif;
	}

	/**
	 * Filter audio widget oEmbed results.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $html The HTML returned by the oEmbed provider.
	 *
	 * @return string Filtered audio widget oEmbed HTML.
	 */
	public function filter_oembed_result( $html ) {
		$param_keys = [
			'auto_play',
			'buying',
			'liking',
			'download',
			'sharing',
			'show_comments',
			'show_playcount',
			'show_user',
			'show_artwork',
		];

		$params = [];

		foreach ( $param_keys as $param_key ) {
			$params[ $param_key ] = 'yes' === $this->_current_instance[ 'sc_' . $param_key ] ? 'true' : 'false';
		}

		$params['color'] = str_replace( '#', '', $this->_current_instance['sc_color'] );

		preg_match( '/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $html, $matches );

		$url = esc_url( add_query_arg( $params, $matches[1] ) );

		$visual = 'yes' === $this->_current_instance['visual'] ? 'true' : 'false';

		$html = str_replace( [ $matches[1], 'visual=true' ], [ $url, 'visual=' . $visual ], $html );

		if ( 'false' === $visual ) {
			$html = str_replace( 'height="400"', 'height="200"', $html );
		}

		return $html;
	}

	/**
	 * Render audio widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {}
}
