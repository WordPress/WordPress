<?php
/**
 * @package WPSEO\Admin
 */

/**
 * This class generates the metabox on the edit post / page as well as contains all page analysis functionality.
 */
class WPSEO_Metabox extends WPSEO_Meta {

	/**
	 * @var object Holds the Text statistics object
	 */
	public $statistics;

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_insert_post', array( $this, 'save_postdata' ) );
		add_action( 'edit_attachment', array( $this, 'save_postdata' ) );
		add_action( 'add_attachment', array( $this, 'save_postdata' ) );
		add_action( 'admin_init', array( $this, 'setup_page_analysis' ) );
		add_action( 'admin_init', array( $this, 'translate_meta_boxes' ) );
	}

	/**
	 * Translate text strings for use in the meta box
	 *
	 * IMPORTANT: if you want to add a new string (option) somewhere, make sure you add that array key to
	 * the main meta box definition array in the class WPSEO_Meta() as well!!!!
	 */
	public static function translate_meta_boxes() {
		self::$meta_fields['general']['snippetpreview']['title'] = __( 'Snippet Preview', 'wordpress-seo' );
		self::$meta_fields['general']['snippetpreview']['help']  = sprintf( __( 'This is a rendering of what this post might look like in Google\'s search results.<br/><br/>Read %sthis post%s for more info.', 'wordpress-seo' ), '<a href="https://yoast.com/snippet-preview/#utm_source=wordpress-seo-metabox&amp;utm_medium=inline-help&amp;utm_campaign=snippet-preview">', '</a>' );

		self::$meta_fields['general']['focuskw']['title'] = __( 'Focus Keyword', 'wordpress-seo' );
		self::$meta_fields['general']['focuskw']['help']  = sprintf( __( 'Pick the main keyword or keyphrase that this post/page is about.<br/><br/>Read %sthis post%s for more info.', 'wordpress-seo' ), '<a href="https://yoast.com/focus-keyword/#utm_source=wordpress-seo-metabox&amp;utm_medium=inline-help&amp;utm_campaign=focus-keyword">', '</a>' );

		self::$meta_fields['general']['title']['title']       = __( 'SEO Title', 'wordpress-seo' );
		self::$meta_fields['general']['title']['description'] = '<p id="yoast_wpseo_title-length-warning"><span class="wrong">' . __( 'Warning:', 'wordpress-seo' ) . '</span> ' . __( 'Title display in Google is limited to a fixed width, yours is too long.', 'wordpress-seo' ) . '</p>';
		self::$meta_fields['general']['title']['help']        = __( 'The SEO title defaults to what is generated based on this sites title template for this posttype.', 'wordpress-seo' );

		self::$meta_fields['general']['metadesc']['title']       = __( 'Meta description', 'wordpress-seo' );
		self::$meta_fields['general']['metadesc']['description'] = sprintf( __( 'The <code>meta</code> description will be limited to %s chars%s, %s chars left.', 'wordpress-seo' ), self::$meta_length, self::$meta_length_reason, '<span id="yoast_wpseo_metadesc-length"></span>' ) . ' <div id="yoast_wpseo_metadesc_notice"></div>';
		self::$meta_fields['general']['metadesc']['help']        = sprintf( __( 'The meta description is often shown as the black text under the title in a search result. For this to work it has to contain the keyword that was searched for.<br/><br/>Read %sthis post%s for more info.', 'wordpress-seo' ), '<a href="https://yoast.com/snippet-preview/#utm_source=wordpress-seo-metabox&amp;utm_medium=inline-help&amp;utm_campaign=focus-keyword">', '</a>' );

		self::$meta_fields['general']['metakeywords']['title']       = __( 'Meta keywords', 'wordpress-seo' );
		self::$meta_fields['general']['metakeywords']['description'] = __( 'If you type something above it will override your %smeta keywords template%s.', 'wordpress-seo' );


		self::$meta_fields['advanced']['meta-robots-noindex']['title'] = __( 'Meta Robots Index', 'wordpress-seo' );
		if ( '0' == get_option( 'blog_public' ) ) {
			self::$meta_fields['advanced']['meta-robots-noindex']['description'] = '<p class="error-message">' . __( 'Warning: even though you can set the meta robots setting here, the entire site is set to noindex in the sitewide privacy settings, so these settings won\'t have an effect.', 'wordpress-seo' ) . '</p>';
		}
		self::$meta_fields['advanced']['meta-robots-noindex']['options']['0'] = __( 'Default for post type, currently: %s', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-noindex']['options']['2'] = __( 'index', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-noindex']['options']['1'] = __( 'noindex', 'wordpress-seo' );

		self::$meta_fields['advanced']['meta-robots-nofollow']['title']        = __( 'Meta Robots Follow', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-nofollow']['options']['0'] = __( 'Follow', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-nofollow']['options']['1'] = __( 'Nofollow', 'wordpress-seo' );

		self::$meta_fields['advanced']['meta-robots-adv']['title']                   = __( 'Meta Robots Advanced', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-adv']['description']             = __( 'Advanced <code>meta</code> robots settings for this page.', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-adv']['options']['-']            = __( 'Site-wide default: %s', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-adv']['options']['none']         = __( 'None', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-adv']['options']['noodp']        = __( 'NO ODP', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-adv']['options']['noydir']       = __( 'NO YDIR', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-adv']['options']['noimageindex'] = __( 'No Image Index', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-adv']['options']['noarchive']    = __( 'No Archive', 'wordpress-seo' );
		self::$meta_fields['advanced']['meta-robots-adv']['options']['nosnippet']    = __( 'No Snippet', 'wordpress-seo' );

		self::$meta_fields['advanced']['bctitle']['title']       = __( 'Breadcrumbs Title', 'wordpress-seo' );
		self::$meta_fields['advanced']['bctitle']['description'] = __( 'Title to use for this page in breadcrumb paths', 'wordpress-seo' );

		self::$meta_fields['advanced']['canonical']['title']       = __( 'Canonical URL', 'wordpress-seo' );
		self::$meta_fields['advanced']['canonical']['description'] = sprintf( __( 'The canonical URL that this page should point to, leave empty to default to permalink. %sCross domain canonical%s supported too.', 'wordpress-seo' ), '<a target="_blank" href="http://googlewebmastercentral.blogspot.com/2009/12/handling-legitimate-cross-domain.html">', '</a>' );

		self::$meta_fields['advanced']['redirect']['title']       = __( '301 Redirect', 'wordpress-seo' );
		self::$meta_fields['advanced']['redirect']['description'] = __( 'The URL that this page should redirect to.', 'wordpress-seo' );

		do_action( 'wpseo_tab_translate' );
	}

	/**
	 * Test whether the metabox should be hidden either by choice of the admin or because
	 * the post type is not a public post type
	 *
	 * @since 1.5.0
	 *
	 * @param  string $post_type (optional) The post type to test, defaults to the current post post_type.
	 *
	 * @return  bool        Whether or not the meta box (and associated columns etc) should be hidden
	 */
	function is_metabox_hidden( $post_type = null ) {
		if ( ! isset( $post_type ) ) {
			if ( isset( $GLOBALS['post'] ) && ( is_object( $GLOBALS['post'] ) && isset( $GLOBALS['post']->post_type ) ) ) {
				$post_type = $GLOBALS['post']->post_type;
			}
			elseif ( isset( $_GET['post_type'] ) && $_GET['post_type'] !== '' ) {
				$post_type = sanitize_text_field( $_GET['post_type'] );
			}
		}

		if ( isset( $post_type ) ) {
			// Don't make static as post_types may still be added during the run.
			$cpts    = get_post_types( array( 'public' => true ), 'names' );
			$options = get_option( 'wpseo_titles' );

			return ( ( isset( $options[ 'hideeditbox-' . $post_type ] ) && $options[ 'hideeditbox-' . $post_type ] === true ) || in_array( $post_type, $cpts ) === false );
		}
		return false;
	}

	/**
	 * Sets up all the functionality related to the prominence of the page analysis functionality.
	 */
	public function setup_page_analysis() {

		if ( apply_filters( 'wpseo_use_page_analysis', true ) === true ) {

			$post_types = get_post_types( array( 'public' => true ), 'names' );

			if ( is_array( $post_types ) && $post_types !== array() ) {
				foreach ( $post_types as $pt ) {
					if ( $this->is_metabox_hidden( $pt ) === false ) {
						add_filter( 'manage_' . $pt . '_posts_columns', array( $this, 'column_heading' ), 10, 1 );
						add_action( 'manage_' . $pt . '_posts_custom_column', array(
							$this,
							'column_content',
						), 10, 2 );
						add_action( 'manage_edit-' . $pt . '_sortable_columns', array(
							$this,
							'column_sort',
						), 10, 2 );

						/*
						 * Use the `get_user_option_{$option}` filter to change the output of the get_user_option
						 * function for the `manage{$screen}columnshidden` option, which is based on the current
						 * admin screen. The admin screen we want to target is the `edit-{$post_type}` screen.
						 */
						$filter = sprintf( 'get_user_option_%s', sprintf( 'manage%scolumnshidden', 'edit-' . $pt ) );
						add_filter( $filter, array( $this, 'column_hidden' ), 10, 3 );
					}
				}
				unset( $pt );
			}
			add_action( 'restrict_manage_posts', array( $this, 'posts_filter_dropdown' ) );
			add_filter( 'request', array( $this, 'column_sort_orderby' ) );

			add_action( 'post_submitbox_misc_actions', array( $this, 'publish_box' ) );
		}
	}

	/**
	 * Get an instance of the text statistics class
	 *
	 * @return Yoast_TextStatistics
	 */
	private function statistics() {
		if ( ! isset( $this->statistics ) ) {
			$this->statistics = new Yoast_TextStatistics( get_bloginfo( 'charset' ) );
		}

		return $this->statistics;
	}

	/**
	 * Returns post in metabox context
	 *
	 * @returns WP_Post
	 */
	private function get_metabox_post() {
		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) WPSEO_Utils::validate_int( $_GET['post'] );
			$post    = get_post( $post_id );
		}
		else {
			$post = $GLOBALS['post'];
		}

		return $post;
	}

	/**
	 * Lowercase a sentence while preserving "weird" characters.
	 *
	 * This should work with Greek, Russian, Polish & French amongst other languages...
	 *
	 * @param string $string String to lowercase.
	 *
	 * @return string
	 */
	public function strtolower_utf8( $string ) {

		// Prevent comparison between utf8 characters and html entities (é vs &eacute;).
		$string = html_entity_decode( $string );

		$convert_to   = array(
			'a',
			'b',
			'c',
			'd',
			'e',
			'f',
			'g',
			'h',
			'i',
			'j',
			'k',
			'l',
			'm',
			'n',
			'o',
			'p',
			'q',
			'r',
			's',
			't',
			'u',
			'v',
			'w',
			'x',
			'y',
			'z',
			'à',
			'á',
			'â',
			'ã',
			'ä',
			'å',
			'æ',
			'ç',
			'è',
			'é',
			'ê',
			'ë',
			'ì',
			'í',
			'î',
			'ï',
			'ð',
			'ñ',
			'ò',
			'ó',
			'ô',
			'õ',
			'ö',
			'ø',
			'ù',
			'ú',
			'û',
			'ü',
			'ý',
			'а',
			'б',
			'в',
			'г',
			'д',
			'е',
			'ё',
			'ж',
			'з',
			'и',
			'й',
			'к',
			'л',
			'м',
			'н',
			'о',
			'п',
			'р',
			'с',
			'т',
			'у',
			'ф',
			'х',
			'ц',
			'ч',
			'ш',
			'щ',
			'ъ',
			'ы',
			'ь',
			'э',
			'ю',
			'я',
			'ą',
			'ć',
			'ę',
			'ł',
			'ń',
			'ó',
			'ś',
			'ź',
			'ż',
		);
		$convert_from = array(
			'A',
			'B',
			'C',
			'D',
			'E',
			'F',
			'G',
			'H',
			'I',
			'J',
			'K',
			'L',
			'M',
			'N',
			'O',
			'P',
			'Q',
			'R',
			'S',
			'T',
			'U',
			'V',
			'W',
			'X',
			'Y',
			'Z',
			'À',
			'Á',
			'Â',
			'Ã',
			'Ä',
			'Å',
			'Æ',
			'Ç',
			'È',
			'É',
			'Ê',
			'Ë',
			'Ì',
			'Í',
			'Î',
			'Ï',
			'Ð',
			'Ñ',
			'Ò',
			'Ó',
			'Ô',
			'Õ',
			'Ö',
			'Ø',
			'Ù',
			'Ú',
			'Û',
			'Ü',
			'Ý',
			'А',
			'Б',
			'В',
			'Г',
			'Д',
			'Е',
			'Ё',
			'Ж',
			'З',
			'И',
			'Й',
			'К',
			'Л',
			'М',
			'Н',
			'О',
			'П',
			'Р',
			'С',
			'Т',
			'У',
			'Ф',
			'Х',
			'Ц',
			'Ч',
			'Ш',
			'Щ',
			'Ъ',
			'Ъ',
			'Ь',
			'Э',
			'Ю',
			'Я',
			'Ą',
			'Ć',
			'Ę',
			'Ł',
			'Ń',
			'Ó',
			'Ś',
			'Ź',
			'Ż',
		);

		return str_replace( $convert_from, $convert_to, $string );
	}

	/**
	 * Outputs the page analysis score in the Publish Box.
	 */
	public function publish_box() {
		if ( $this->is_metabox_hidden() === true ) {
			return;
		}


		$post = $this->get_metabox_post();

		if ( self::get_value( 'meta-robots-noindex', $post->ID ) === '1' ) {
			$score_label = 'noindex';
			$title       = __( 'Post is set to noindex.', 'wordpress-seo' );
			$score_title = $title;
		}
		else {

			$score   = '';
			$results = $this->calculate_results( $post );
			if ( ! is_wp_error( $results ) && isset( $results['total'] ) ) {
				$score = $results['total'];
				unset( $results );
			}

			if ( $score === '' ) {
				$score_label = 'na';
				$title       = __( 'No focus keyword set.', 'wordpress-seo' );
			}
			else {
				$score_label = WPSEO_Utils::translate_score( $score );
			}

			$score_title = WPSEO_Utils::translate_score( $score, false );
			if ( ! isset( $title ) ) {
				$title = $score_title;
			}
		}

		printf( '
		<div class="misc-pub-section misc-yoast misc-pub-section-last">
			<div title="%1$s" class="%2$s"></div>
			%3$s <span class="wpseo-score-title">%4$s</span>
			<a class="wpseo_tablink scroll" href="#wpseo_linkdex">%5$s</a>
		</div>',
			esc_attr( $title ),
			esc_attr( 'wpseo-score-icon ' . $score_label ),
			__( 'SEO:', 'wordpress-seo' ),
			$score_title,
			__( 'Check', 'wordpress-seo' )
		);
	}

	/**
	 * Adds the Yoast SEO meta box to the edit boxes in the edit post / page  / cpt pages.
	 */
	public function add_meta_box() {
		$post_types = get_post_types( array( 'public' => true ) );

		if ( is_array( $post_types ) && $post_types !== array() ) {
			foreach ( $post_types as $post_type ) {
				if ( $this->is_metabox_hidden( $post_type ) === false ) {
					add_meta_box( 'wpseo_meta', 'Yoast SEO', array(
						$this,
						'meta_box',
					), $post_type, 'normal', apply_filters( 'wpseo_metabox_prio', 'high' ) );
				}
			}
		}
	}

	/**
	 * Pass some variables to js for the edit / post page overview, snippet preview, etc.
	 *
	 * @return  array
	 */
	public function localize_script() {
		$post = $this->get_metabox_post();

		if ( ( ! is_object( $post ) || ! isset( $post->post_type ) ) || $this->is_metabox_hidden( $post->post_type ) === true ) {
			return array();
		}

		$options = get_option( 'wpseo_titles' );

		$date = '';
		if ( isset( $options[ 'showdate-' . $post->post_type ] ) && $options[ 'showdate-' . $post->post_type ] === true ) {
			$date = $this->get_post_date( $post );

			self::$meta_length        = ( self::$meta_length - ( strlen( $date ) + 5 ) );
			self::$meta_length_reason = __( ' (because of date display)', 'wordpress-seo' );
		}

		self::$meta_length_reason = apply_filters( 'wpseo_metadesc_length_reason', self::$meta_length_reason, $post );
		self::$meta_length        = apply_filters( 'wpseo_metadesc_length', self::$meta_length, $post );

		unset( $date );

		$title_template = '';
		if ( isset( $options[ 'title-' . $post->post_type ] ) && $options[ 'title-' . $post->post_type ] !== '' ) {
			$title_template = $options[ 'title-' . $post->post_type ];
		}

		// If there's no title template set, use the default, otherwise title preview won't work.
		if ( $title_template == '' ) {
			$title_template = '%%title%% - %%sitename%%';
		}

		$metadesc_template = '';
		if ( isset( $options[ 'metadesc-' . $post->post_type ] ) && $options[ 'metadesc-' . $post->post_type ] !== '' ) {
			$metadesc_template = $options[ 'metadesc-' . $post->post_type ];
		}

		$sample_permalink = get_sample_permalink( $post->ID );
		$sample_permalink = str_replace( '%page', '%post', $sample_permalink[0] );

		$cached_replacement_vars = array();

		$vars_to_cache = array(
				'date',
				'id',
				'sitename',
				'sitedesc',
				'sep',
				'page',
				'currenttime',
				'currentdate',
				'currentday',
				'currentmonth',
				'currentyear',
		);
		foreach ( $vars_to_cache as $var ) {
			$cached_replacement_vars[ $var ] = wpseo_replace_vars( '%%' . $var . '%%', $post );
		}

		return array_merge( $cached_replacement_vars, array(
			'field_prefix'                => self::$form_prefix,
			'keyword_header'              => '<strong>' . __( 'Focus keyword usage', 'wordpress-seo' ) . '</strong><br>' . __( 'Your focus keyword was found in:', 'wordpress-seo' ),
			'article_header_text'         => __( 'Article Heading: ', 'wordpress-seo' ),
			'page_title_text'             => __( 'Page title: ', 'wordpress-seo' ),
			'page_url_text'               => __( 'Page URL: ', 'wordpress-seo' ),
			'content_text'                => __( 'Content: ', 'wordpress-seo' ),
			'meta_description_text'       => __( 'Meta description: ', 'wordpress-seo' ),
			'choose_image'                => __( 'Use Image', 'wordpress-seo' ),
			'wpseo_meta_desc_length'      => self::$meta_length,
			'wpseo_title_template'        => $title_template,
			'wpseo_metadesc_template'     => $metadesc_template,
			'wpseo_permalink_template'    => $sample_permalink,
			'wpseo_keyword_suggest_nonce' => wp_create_nonce( 'wpseo-get-suggest' ),
			'wpseo_replace_vars_nonce'    => wp_create_nonce( 'wpseo-replace-vars' ),
			'no_parent_text'              => __( '(no parent)', 'wordpress-seo' ),
			'featured_image_notice'       => __( 'The featured image should be at least 200x200 pixels to be picked up by Facebook and other social media sites.', 'wordpress-seo' ),
		) );
	}

	/**
	 * Output a tab in the Yoast SEO Metabox
	 *
	 * @param string $id      CSS ID of the tab.
	 * @param string $heading Heading for the tab.
	 * @param string $content Content of the tab. This content should be escaped.
	 */
	public function do_tab( $id, $heading, $content ) {
		?>
		<div class="wpseotab <?php echo esc_attr( $id ) ?>">
			<h4 class="wpseo-heading"><?php echo esc_html( $heading ); ?></h4>
			<table class="form-table">
				<?php echo $content ?>
			</table>
		</div>
	<?php
	}

	/**
	 * Output the meta box
	 */
	function meta_box() {
		$post    = $this->get_metabox_post();
		$options = WPSEO_Options::get_all();

		?>
		<div class="wpseo-metabox-tabs-div">
		<ul class="wpseo-metabox-tabs" id="wpseo-metabox-tabs">
			<li class="general">
				<a class="wpseo_tablink" href="#wpseo_general"><?php _e( 'General', 'wordpress-seo' ); ?></a></li>
			<li id="linkdex" class="linkdex">
				<a class="wpseo_tablink" href="#wpseo_linkdex"><?php _e( 'Page Analysis', 'wordpress-seo' ); ?></a>
			</li>
			<?php if ( current_user_can( 'manage_options' ) || $options['disableadvanced_meta'] === false ) : ?>
				<li class="advanced">
					<a class="wpseo_tablink" href="#wpseo_advanced"><?php _e( 'Advanced', 'wordpress-seo' ); ?></a>
				</li>
			<?php endif; ?>
			<?php do_action( 'wpseo_tab_header' ); ?>
		</ul>
		<?php
		$content = '';
		if ( is_object( $post ) && isset( $post->post_type ) ) {
			foreach ( $this->get_meta_field_defs( 'general', $post->post_type ) as $key => $meta_field ) {
				$content .= $this->do_meta_box( $meta_field, $key );
			}
			unset( $key, $meta_field );
		}
		$this->do_tab( 'general', __( 'General', 'wordpress-seo' ), $content );

		$this->do_tab( 'linkdex', __( 'Page Analysis', 'wordpress-seo' ), $this->linkdex_output( $post ) );

		if ( current_user_can( 'manage_options' ) || $options['disableadvanced_meta'] === false ) {
			$content = '';
			foreach ( $this->get_meta_field_defs( 'advanced' ) as $key => $meta_field ) {
				$content .= $this->do_meta_box( $meta_field, $key );
			}
			unset( $key, $meta_field );
			$this->do_tab( 'advanced', __( 'Advanced', 'wordpress-seo' ), $content );
		}

		do_action( 'wpseo_tab_content' );

		echo '</div>';
	}

	/**
	 * Adds a line in the meta box
	 *
	 * @todo [JRF] check if $class is added appropriately everywhere
	 *
	 * @param   array  $meta_field_def Contains the vars based on which output is generated.
	 * @param   string $key            Internal key (without prefix).
	 *
	 * @return  string
	 */
	function do_meta_box( $meta_field_def, $key = '' ) {
		$content      = '';
		$esc_form_key = esc_attr( self::$form_prefix . $key );
		$post         = $this->get_metabox_post();
		$meta_value   = self::get_value( $key, $post->ID );

		$class = '';
		if ( isset( $meta_field_def['class'] ) && $meta_field_def['class'] !== '' ) {
			$class = ' ' . $meta_field_def['class'];
		}

		$placeholder = '';
		if ( isset( $meta_field_def['placeholder'] ) && $meta_field_def['placeholder'] !== '' ) {
			$placeholder = $meta_field_def['placeholder'];
		}

		switch ( $meta_field_def['type'] ) {
			case 'snippetpreview':
				$content .= $this->snippet();
				break;

			case 'text':
				$ac = '';
				if ( isset( $meta_field_def['autocomplete'] ) && $meta_field_def['autocomplete'] === false ) {
					$ac = 'autocomplete="off" ';
				}
				if ( $placeholder !== '' ) {
					$placeholder = ' placeholder="' . esc_attr( $placeholder ) . '"';
				}
				$content .= '<input type="text"' . $placeholder . ' id="' . $esc_form_key . '" ' . $ac . 'name="' . $esc_form_key . '" value="' . esc_attr( $meta_value ) . '" class="large-text' . $class . '"/><br />';
				break;

			case 'textarea':
				$rows = 3;
				if ( isset( $meta_field_def['rows'] ) && $meta_field_def['rows'] > 0 ) {
					$rows = $meta_field_def['rows'];
				}
				$content .= '<textarea class="large-text' . $class . '" rows="' . esc_attr( $rows ) . '" id="' . $esc_form_key . '" name="' . $esc_form_key . '">' . esc_textarea( $meta_value ) . '</textarea>';
				break;

			case 'select':
				if ( isset( $meta_field_def['options'] ) && is_array( $meta_field_def['options'] ) && $meta_field_def['options'] !== array() ) {
					$content .= '<select name="' . $esc_form_key . '" id="' . $esc_form_key . '" class="yoast' . $class . '">';
					foreach ( $meta_field_def['options'] as $val => $option ) {
						$selected = selected( $meta_value, $val, false );
						$content .= '<option ' . $selected . ' value="' . esc_attr( $val ) . '">' . esc_html( $option ) . '</option>';
					}
					unset( $val, $option, $selected );
					$content .= '</select>';
				}
				break;

			case 'multiselect':
				if ( isset( $meta_field_def['options'] ) && is_array( $meta_field_def['options'] ) && $meta_field_def['options'] !== array() ) {

					// Set $meta_value as $selected_arr.
					$selected_arr = $meta_value;

					// If the multiselect field is 'meta-robots-adv' we should explode on ,.
					if ( 'meta-robots-adv' === $key ) {
						$selected_arr = explode( ',', $meta_value );
					}

					if ( ! is_array( $selected_arr ) ) {
						$selected_arr = (array) $selected_arr;
					}

					$options_count = count( $meta_field_def['options'] );

					// @todo [JRF => whomever] verify height calculation for older WP versions, was 16x, for WP3.8 20x is more appropriate.
					$content .= '<select multiple="multiple" size="' . esc_attr( $options_count ) . '" style="height: ' . esc_attr( ( $options_count * 20 ) + 4 ) . 'px;" name="' . $esc_form_key . '[]" id="' . $esc_form_key . '" class="yoast' . $class . '">';
					foreach ( $meta_field_def['options'] as $val => $option ) {
						$selected = '';
						if ( in_array( $val, $selected_arr ) ) {
							$selected = ' selected="selected"';
						}
						$content .= '<option ' . $selected . ' value="' . esc_attr( $val ) . '">' . esc_html( $option ) . '</option>';
					}
					$content .= '</select>';
					unset( $val, $option, $selected, $selected_arr, $options_count );
				}
				break;

			case 'checkbox':
				$checked = checked( $meta_value, 'on', false );
				$expl    = ( isset( $meta_field_def['expl'] ) ) ? esc_html( $meta_field_def['expl'] ) : '';
				$content .= '<label for="' . $esc_form_key . '"><input type="checkbox" id="' . $esc_form_key . '" name="' . $esc_form_key . '" ' . $checked . ' value="on" class="yoast' . $class . '"/> ' . $expl . '</label><br />';
				unset( $checked, $expl );
				break;

			case 'radio':
				if ( isset( $meta_field_def['options'] ) && is_array( $meta_field_def['options'] ) && $meta_field_def['options'] !== array() ) {
					foreach ( $meta_field_def['options'] as $val => $option ) {
						$checked = checked( $meta_value, $val, false );
						$content .= '<input type="radio" ' . $checked . ' id="' . $esc_form_key . '_' . esc_attr( $val ) . '" name="' . $esc_form_key . '" value="' . esc_attr( $val ) . '"/> <label for="' . $esc_form_key . '_' . esc_attr( $val ) . '">' . esc_html( $option ) . '</label> ';
					}
					unset( $val, $option, $checked );
				}
				break;

			case 'upload':
				$content .= '<input id="' . $esc_form_key . '" type="text" size="36" class="' . $class . '" name="' . $esc_form_key . '" value="' . esc_attr( $meta_value ) . '" />';
				$content .= '<input id="' . $esc_form_key . '_button" class="wpseo_image_upload_button button" type="button" value="Upload Image" />';
				break;
		}


		$html = '';
		if ( $content === '' ) {
			$content = apply_filters( 'wpseo_do_meta_box_field_' . $key, $content, $meta_value, $esc_form_key, $meta_field_def, $key );
		}

		if ( $content !== '' ) {

			$label = esc_html( $meta_field_def['title'] );
			if ( in_array( $meta_field_def['type'], array(
					'snippetpreview',
					'radio',
					'checkbox',
				), true ) === false
			) {
				$label = '<label for="' . $esc_form_key . '">' . $label . ':</label>';
			}

			$help = '';
			if ( isset( $meta_field_def['help'] ) && $meta_field_def['help'] !== '' ) {
				$help = '<img src="' . plugins_url( 'images/question-mark.png', WPSEO_FILE ) . '" class="alignright yoast_help" id="' . esc_attr( $key . 'help' ) . '" alt="' . esc_attr( $meta_field_def['help'] ) . '" />';
			}

			$html = '
				<tr>
					<th scope="row">' . $label . $help . '</th>
					<td>';

			$html .= $content;

			if ( isset( $meta_field_def['description'] ) ) {
				$html .= '<div>' . $meta_field_def['description'] . '</div>';
			}

			$html .= '
					</td>
				</tr>';
		}

		return $html;
	}

	/**
	 * Retrieve a post date when post is published, or return current date when it's not.
	 *
	 * @param object $post Post to retrieve the date for.
	 *
	 * @return string
	 */
	function get_post_date( $post ) {
		if ( isset( $post->post_date ) && $post->post_status == 'publish' ) {
			$date = date_i18n( 'j M Y', strtotime( $post->post_date ) );
		}
		else {
			$date = date_i18n( 'j M Y' );
		}

		return (string) $date;
	}

	/**
	 * Generate a snippet preview.
	 *
	 * @return string
	 */
	function snippet() {
		$post        = $this->get_metabox_post();
		$title       = self::get_value( 'title', $post->ID );
		$description = self::get_value( 'metadesc', $post->ID );

		$snippet_preview = new WPSEO_Snippet_Preview( $post, $title, $description );

		return $snippet_preview->get_content();
	}

	/**
	 * Save the Yoast SEO metadata for posts.
	 *
	 * @internal $_POST parameters are validated via sanitize_post_meta()
	 *
	 * @param  int $post_id
	 *
	 * @return  bool|void   Boolean false if invalid save post request
	 */
	function save_postdata( $post_id ) {
		if ( $post_id === null ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			$post_id = wp_is_post_revision( $post_id );
		}

		clean_post_cache( $post_id );
		$post = get_post( $post_id );

		if ( ! is_object( $post ) ) {
			// Non-existent post.
			return false;
		}

		do_action( 'wpseo_save_compare_data', $post );

		$meta_boxes = apply_filters( 'wpseo_save_metaboxes', array() );
		$meta_boxes = array_merge( $meta_boxes, $this->get_meta_field_defs( 'general', $post->post_type ), $this->get_meta_field_defs( 'advanced' ) );

		foreach ( $meta_boxes as $key => $meta_box ) {
			$data = null;
			if ( 'checkbox' === $meta_box['type'] ) {
				$data = isset( $_POST[ self::$form_prefix . $key ] ) ? 'on' : 'off';
			}
			else {
				if ( isset( $_POST[ self::$form_prefix . $key ] ) ) {
					$data = $_POST[ self::$form_prefix . $key ];
				}
			}
			if ( isset( $data ) ) {
				self::set_value( $key, $data, $post_id );
			}
		}

		do_action( 'wpseo_saved_postdata' );
	}

	/**
	 * Enqueues all the needed JS and CSS.
	 * @todo [JRF => whomever] create css/metabox-mp6.css file and add it to the below allowed colors array when done
	 */
	public function enqueue() {
		global $pagenow;
		/* Filter 'wpseo_always_register_metaboxes_on_admin' documented in wpseo-main.php */
		if ( ( ! in_array( $pagenow, array(
					'post-new.php',
					'post.php',
					'edit.php',
				), true ) && apply_filters( 'wpseo_always_register_metaboxes_on_admin', false ) === false ) || $this->is_metabox_hidden() === true
		) {
			return;
		}


		$color = get_user_meta( get_current_user_id(), 'admin_color', true );
		if ( '' == $color || in_array( $color, array( 'classic', 'fresh' ), true ) === false ) {
			$color = 'fresh';
		}


		if ( $pagenow == 'edit.php' ) {
			wp_enqueue_style( 'edit-page', plugins_url( 'css/edit-page' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );
		}
		else {
			if ( 0 != get_queried_object_id() ) {
				wp_enqueue_media( array( 'post' => get_queried_object_id() ) ); // Enqueue files needed for upload functionality.
			}
			wp_enqueue_style( 'metabox-tabs', plugins_url( 'css/metabox-tabs' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );
			wp_enqueue_style( "metabox-$color", plugins_url( 'css/metabox-' . esc_attr( $color ) . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );
			wp_enqueue_style( 'featured-image', plugins_url( 'css/featured-image' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );
			wp_enqueue_style( 'jquery-qtip.js', plugins_url( 'css/jquery.qtip' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), '2.2.1' );

			wp_enqueue_script( 'jquery-ui-autocomplete' );

			// Always enqueue minified as it's not our code.
			wp_enqueue_script( 'jquery-qtip', plugins_url( 'js/jquery.qtip.min.js', WPSEO_FILE ), array( 'jquery' ), '2.2.1', true );

			wp_enqueue_script( 'wp-seo-metabox', plugins_url( 'js/wp-seo-metabox' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-autocomplete',
			), WPSEO_VERSION, true );

			if ( post_type_supports( get_post_type(), 'thumbnail' ) ) {
				wp_enqueue_script( 'wp-seo-featured-image', plugins_url( 'js/wp-seo-featured-image' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array( 'jquery' ), WPSEO_VERSION, true );
			}

			wp_enqueue_script( 'wpseo-admin-media', plugins_url( 'js/wp-seo-admin-media' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array(
				'jquery',
				'jquery-ui-core',
			), WPSEO_VERSION, true );

			wp_localize_script( 'wpseo-admin-media', 'wpseoMediaL10n', $this->localize_media_script() );

			// Text strings to pass to metabox for keyword analysis.
			wp_localize_script( 'wp-seo-metabox', 'wpseoMetaboxL10n', $this->localize_script() );
		}
	}

	/**
	 * Pass some variables to js for upload module.
	 *
	 * @return  array
	 */
	public function localize_media_script() {
		return array(
			'choose_image' => __( 'Use Image', 'wordpress-seo' ),
		);
	}

	/**
	 * Adds a dropdown that allows filtering on the posts SEO Quality.
	 *
	 * @return void
	 */
	function posts_filter_dropdown() {
		if ( $GLOBALS['pagenow'] === 'upload.php' || $this->is_metabox_hidden() === true ) {
			return;
		}

		$scores_array = array(
			'na'      => __( 'SEO: No Focus Keyword', 'wordpress-seo' ),
			'bad'     => __( 'SEO: Bad', 'wordpress-seo' ),
			'poor'    => __( 'SEO: Poor', 'wordpress-seo' ),
			'ok'      => __( 'SEO: OK', 'wordpress-seo' ),
			'good'    => __( 'SEO: Good', 'wordpress-seo' ),
			'noindex' => __( 'SEO: Post Noindexed', 'wordpress-seo' ),
		);

		echo '
			<select name="seo_filter">
				<option value="">', __( 'All SEO Scores', 'wordpress-seo' ), '</option>';
		foreach ( $scores_array as $val => $text ) {
			$sel = '';
			if ( isset( $_GET['seo_filter'] ) ) {
				$sel = selected( $_GET['seo_filter'], $val, false );
			}
			echo '
				<option ', $sel, 'value="', $val, '">', $text. '</option>';
		}
		echo '
			</select>';
	}

	/**
	 * Adds the column headings for the SEO plugin for edit posts / pages overview
	 *
	 * @param array $columns Already existing columns.
	 *
	 * @return array
	 */
	function column_heading( $columns ) {
		if ( $this->is_metabox_hidden() === true ) {
			return $columns;
		}

		return array_merge( $columns, array(
			'wpseo-score'    => __( 'SEO', 'wordpress-seo' ),
			'wpseo-title'    => __( 'SEO Title', 'wordpress-seo' ),
			'wpseo-metadesc' => __( 'Meta Desc.', 'wordpress-seo' ),
			'wpseo-focuskw'  => __( 'Focus KW', 'wordpress-seo' ),
		) );
	}

	/**
	 * Display the column content for the given column
	 *
	 * @param string $column_name Column to display the content for.
	 * @param int    $post_id     Post to display the column content for.
	 */
	function column_content( $column_name, $post_id ) {
		if ( $this->is_metabox_hidden() === true ) {
			return;
		}

		if ( $column_name === 'wpseo-score' ) {
			$score = self::get_value( 'linkdex', $post_id );
			if ( self::get_value( 'meta-robots-noindex', $post_id ) === '1' ) {
				$score_label = 'noindex';
				$title       = __( 'Post is set to noindex.', 'wordpress-seo' );
				self::set_value( 'linkdex', 0, $post_id );
			}
			elseif ( $score !== '' ) {
				$nr          = WPSEO_Utils::calc( $score, '/', 10, true );
				$score_label = WPSEO_Utils::translate_score( $nr );
				$title       = WPSEO_Utils::translate_score( $nr, false );
				unset( $nr );
			}
			else {
				$this->calculate_results( get_post( $post_id ) );
				$score = self::get_value( 'linkdex', $post_id );
				if ( $score === '' ) {
					$score_label = 'na';
					$title       = __( 'Focus keyword not set.', 'wordpress-seo' );
				}
				else {
					$score_label = WPSEO_Utils::translate_score( $score );
					$title       = WPSEO_Utils::translate_score( $score, false );
				}
			}

			echo '<div title="', esc_attr( $title ), '" class="wpseo-score-icon ', esc_attr( $score_label ), '"></div>';
		}
		if ( $column_name === 'wpseo-title' ) {
			echo esc_html( apply_filters( 'wpseo_title', wpseo_replace_vars( $this->page_title( $post_id ), get_post( $post_id, ARRAY_A ) ) ) );
		}
		if ( $column_name === 'wpseo-metadesc' ) {
			echo esc_html( apply_filters( 'wpseo_metadesc', wpseo_replace_vars( self::get_value( 'metadesc', $post_id ), get_post( $post_id, ARRAY_A ) ) ) );
		}
		if ( $column_name === 'wpseo-focuskw' ) {
			$focuskw = self::get_value( 'focuskw', $post_id );
			echo esc_html( $focuskw );
		}
	}

	/**
	 * Indicate which of the SEO columns are sortable.
	 *
	 * @param array $columns appended with their orderby variable.
	 *
	 * @return array
	 */
	function column_sort( $columns ) {
		if ( $this->is_metabox_hidden() === true ) {
			return $columns;
		}

		$columns['wpseo-score']    = 'wpseo-score';
		$columns['wpseo-metadesc'] = 'wpseo-metadesc';
		$columns['wpseo-focuskw']  = 'wpseo-focuskw';

		return $columns;
	}

	/**
	 * Modify the query based on the seo_filter variable in $_GET
	 *
	 * @param array $vars Query variables.
	 *
	 * @return array
	 */
	function column_sort_orderby( $vars ) {
		if ( isset( $_GET['seo_filter'] ) ) {
			$na      = false;
			$noindex = false;
			$high    = false;
			switch ( $_GET['seo_filter'] ) {
				case 'noindex':
					$low     = false;
					$noindex = true;
					break;
				case 'na':
					$low = false;
					$na  = true;
					break;
				case 'bad':
					$low  = 1;
					$high = 34;
					break;
				case 'poor':
					$low  = 35;
					$high = 54;
					break;
				case 'ok':
					$low  = 55;
					$high = 74;
					break;
				case 'good':
					$low  = 75;
					$high = 100;
					break;
				default:
					$low     = false;
					$high    = false;
					$noindex = false;
					break;
			}
			if ( $low !== false ) {
				/**
				 * @internal DON'T touch the order of these without double-checking/adjusting the seo_score_posts_where() method below!
				 */
				$vars = array_merge(
					$vars,
					array(
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'     => self::$meta_prefix . 'linkdex',
								'value'   => array( $low, $high ),
								'type'    => 'numeric',
								'compare' => 'BETWEEN',
							),
							array(
								'key'     => self::$meta_prefix . 'meta-robots-noindex',
								'value'   => 'needs-a-value-anyway',
								'compare' => 'NOT EXISTS',
							),
							array(
								'key'     => self::$meta_prefix . 'meta-robots-noindex',
								'value'   => '1',
								'compare' => '!=',
							),
						),
					)
				);

				add_filter( 'posts_where', array( $this, 'seo_score_posts_where' ) );

			}
			elseif ( $na ) {
				$vars = array_merge(
					$vars,
					array(
						'meta_query' => array(
							'relation' => 'OR',
							array(
								'key'     => self::$meta_prefix . 'linkdex',
								'value'   => 'needs-a-value-anyway',
								'compare' => 'NOT EXISTS',
							)
						),
					)
				);

			}
			elseif ( $noindex ) {
				$vars = array_merge(
					$vars,
					array(
						'meta_query' => array(
							array(
								'key'     => self::$meta_prefix . 'meta-robots-noindex',
								'value'   => '1',
								'compare' => '=',
							),
						),
					)
				);
			}
		}
		if ( isset( $_GET['seo_kw_filter'] ) && $_GET['seo_kw_filter'] !== '' ) {
			$vars = array_merge(
				$vars, array(
					'post_type'  => 'any',
					'meta_key'   => self::$meta_prefix . 'focuskw',
					'meta_value' => sanitize_text_field( $_GET['seo_kw_filter'] ),
				)
			);
		}
		if ( isset( $vars['orderby'] ) && 'wpseo-score' === $vars['orderby'] ) {
			$vars = array_merge(
				$vars, array(
					'meta_key' => self::$meta_prefix . 'linkdex',
					'orderby'  => 'meta_value_num',
				)
			);
		}
		if ( isset( $vars['orderby'] ) && 'wpseo-metadesc' === $vars['orderby'] ) {
			$vars = array_merge(
				$vars, array(
					'meta_key' => self::$meta_prefix . 'metadesc',
					'orderby'  => 'meta_value',
				)
			);
		}
		if ( isset( $vars['orderby'] ) && 'wpseo-focuskw' === $vars['orderby'] ) {
			$vars = array_merge(
				$vars, array(
					'meta_key' => self::$meta_prefix . 'focuskw',
					'orderby'  => 'meta_value',
				)
			);
		}

		return $vars;
	}

	/**
	 * Hide the SEO Title, Meta Desc and Focus KW columns if the user hasn't chosen which columns to hide
	 *
	 * @param array|false $result
	 * @param string      $option
	 * @param WP_User     $user
	 *
	 * @return array|false $result
	 */
	public function column_hidden( $result, $option, $user ) {
		global $wpdb;

		$prefix = $wpdb->get_blog_prefix();
		if ( ! $user->has_prop( $prefix . $option ) && ! $user->has_prop( $option ) ) {

			if ( ! is_array( $result ) ) {
				$result = array();
			}

			array_push( $result, 'wpseo-title', 'wpseo-metadesc', 'wpseo-focuskw' );
		}

		return $result;
	}

	/**
	 * Hacky way to get round the limitation that you can only have AND *or* OR relationship between
	 * meta key clauses and not a combination - which is what we need.
	 *
	 * @param    string $where
	 *
	 * @return    string
	 */
	function seo_score_posts_where( $where ) {
		global $wpdb;

		/* Find the two mutually exclusive noindex clauses which should be changed from AND to OR relation */
		$find = '`([\s]+AND[\s]+)((?:' . $wpdb->prefix . 'postmeta|mt[0-9]|mt1)\.post_id IS NULL[\s]+)AND([\s]+\([\s]*(?:' . $wpdb->prefix . 'postmeta|mt[0-9])\.meta_key = \'' . self::$meta_prefix . 'meta-robots-noindex\' AND CAST\([^\)]+\)[^\)]+\))`';

		$replace = '$1( $2OR$3 )';

		$new_where = preg_replace( $find, $replace, $where );

		if ( $new_where ) {
			return $new_where;
		}
		return $where;
	}

	/**
	 * Retrieve the page title.
	 *
	 * @param int $post_id Post to retrieve the title for.
	 *
	 * @return string
	 */
	function page_title( $post_id ) {
		$fixed_title = self::get_value( 'title', $post_id );
		if ( $fixed_title !== '' ) {
			return $fixed_title;
		}
		else {
			$post    = get_post( $post_id );
			$options = WPSEO_Options::get_all();
			if ( is_object( $post ) && ( isset( $options[ 'title-' . $post->post_type ] ) && $options[ 'title-' . $post->post_type ] !== '' ) ) {
				$title_template = $options[ 'title-' . $post->post_type ];
				$title_template = str_replace( ' %%page%% ', ' ', $title_template );

				return wpseo_replace_vars( $title_template, $post );
			}
			else {
				return wpseo_replace_vars( '%%title%%', $post );
			}
		}
	}

	/**
	 * Sort an array by a given key.
	 *
	 * @param array  $array Array to sort, array is returned sorted.
	 * @param string $key   Key to sort array by.
	 */
	function aasort( &$array, $key ) {
		$sorter = array();
		$ret    = array();
		reset( $array );
		foreach ( $array as $ii => $va ) {
			$sorter[ $ii ] = $va[ $key ];
		}
		asort( $sorter );
		foreach ( $sorter as $ii => $va ) {
			$ret[ $ii ] = $array[ $ii ];
		}
		$array = $ret;
	}

	/**
	 * Output the page analysis results.
	 *
	 * @param object $post Post to output the page analysis results for.
	 *
	 * @return string
	 */
	function linkdex_output( $post ) {
		$results = $this->calculate_results( $post );

		if ( is_wp_error( $results ) ) {
			$error = $results->get_error_messages();

			return '<tr><td><div class="wpseo_msg"><p><strong>' . esc_html( $error[0] ) . '</strong></p></div></td></tr>';
		}
		$output = '';

		if ( is_array( $results ) && $results !== array() ) {

			$output     = '<table class="wpseoanalysis">';
			$perc_score = absint( $results['total'] );
			unset( $results['total'] ); // Unset to prevent echoing it.

			foreach ( $results as $result ) {
				if ( is_array( $result ) ) {
					$score = WPSEO_Utils::translate_score( $result['val'] );
					$output .= '<tr><td class="score"><div class="' . esc_attr( 'wpseo-score-icon ' . $score ) . '"></div></td><td>' . $result['msg'] . '</td></tr>';
				}
			}
			unset( $result, $score );
			$output .= '</table>';

			if ( WP_DEBUG === true || ( defined( 'WPSEO_DEBUG' ) && WPSEO_DEBUG === true ) ) {
				$output .= '<p><small>(' . $perc_score . '%)</small></p>';
			}
		}

		$output = '<div class="wpseo_msg"><p>' . __( 'To update this page analysis, save as draft or update and check this tab again', 'wordpress-seo' ) . '.</p></div>' . $output;

		unset( $results );

		return $output;
	}

	/**
	 * Calculate the page analysis results for post.
	 *
	 * @todo [JRF => whomever] check whether the results of this method are always checked with is_wp_error()
	 * @todo [JRF => whomever] check the usage of this method as it's quite intense/heavy, see if it's only
	 * used when really necessary
	 * @todo [JRF => whomever] see if we can get rid of the passing by reference of $results as it makes
	 * the code obfuscated
	 *
	 * @param  object $post Post to calculate the results for.
	 *
	 * @return  array|WP_Error
	 */
	function calculate_results( $post ) {
		$options = WPSEO_Options::get_all();

		if ( ! class_exists( 'DOMDocument' ) ) {
			$result = new WP_Error( 'no-domdocument', sprintf( __( "Your hosting environment does not support PHP's %sDocument Object Model%s.", 'wordpress-seo' ), '<a href="http://php.net/manual/en/book.dom.php">', '</a>' ) . ' ' . __( "To enjoy all the benefits of the page analysis feature, you'll need to (get your host to) install it.", 'wordpress-seo' ) );

			return $result;
		}

		if ( ! is_array( $post ) && ! is_object( $post ) ) {
			$result = new WP_Error( 'no-post', __( 'No post content to analyse.', 'wordpress-seo' ) );

			return $result;
		}
		elseif ( self::get_value( 'focuskw', $post->ID ) === '' ) {
			$result = new WP_Error( 'no-focuskw', sprintf( __( 'No focus keyword was set for this %s. If you do not set a focus keyword, no score can be calculated.', 'wordpress-seo' ), $post->post_type ) );

			self::set_value( 'linkdex', 0, $post->ID );

			return $result;
		}
		elseif ( apply_filters( 'wpseo_use_page_analysis', true ) !== true ) {
			$result = new WP_Error( 'page-analysis-disabled', sprintf( __( 'Page Analysis has been disabled.', 'wordpress-seo' ), $post->post_type ) );

			return $result;
		}

		$results = array();
		$job     = array();

		$sampleurl             = $this->get_sample_permalink( $post );
		$job['pageUrl']        = preg_replace( '`%(?:post|page)name%`', $sampleurl[1], $sampleurl[0] );
		$job['pageSlug']       = urldecode( $post->post_name );
		$job['keyword']        = self::get_value( 'focuskw', $post->ID );
		$job['keyword_folded'] = $this->strip_separators_and_fold( $job['keyword'] );
		$job['post_id']        = $post->ID;
		$job['post_type']      = $post->post_type;

		$dom                      = new domDocument;
		$dom->strictErrorChecking = false;
		$dom->preserveWhiteSpace  = false;

		/**
		 * Filter: 'wpseo_pre_analysis_post_content' - Make the post content filterable before calculating the page analysis
		 *
		 * @api string $post_content The post content
		 *
		 * @param object $post The post.
		 */
		$post_content = apply_filters( 'wpseo_pre_analysis_post_content', $post->post_content, $post );

		// Check if the post content is not empty.
		if ( ! empty( $post_content ) ) {
			@$dom->loadHTML( $post_content );
		}

		unset( $post_content );

		$xpath = new DOMXPath( $dom );

		// Check if this focus keyword has been used already.
		$this->check_double_focus_keyword( $job, $results );

		// Keyword.
		$this->score_keyword( $job['keyword'], $results );

		// Title.
		$title = self::get_value( 'title', $post->ID );
		if ( $title !== '' ) {
			$job['title'] = $title;
		}
		else {
			if ( isset( $options[ 'title-' . $post->post_type ] ) && $options[ 'title-' . $post->post_type ] !== '' ) {
				$title_template = $options[ 'title-' . $post->post_type ];
			}
			else {
				$title_template = '%%title%% - %%sitename%%';
			}
			$job['title'] = wpseo_replace_vars( $title_template, $post );
		}
		unset( $title );
		$this->score_title( $job, $results );

		// Meta description.
		$description = '';
		$desc_meta   = self::get_value( 'metadesc', $post->ID );
		if ( $desc_meta !== '' ) {
			$description = $desc_meta;
		}
		elseif ( isset( $options[ 'metadesc-' . $post->post_type ] ) && $options[ 'metadesc-' . $post->post_type ] !== '' ) {
			$description = wpseo_replace_vars( $options[ 'metadesc-' . $post->post_type ], $post );
		}
		unset( $desc_meta );

		self::$meta_length = apply_filters( 'wpseo_metadesc_length', self::$meta_length, $post );

		$this->score_description( $job, $results, $description, self::$meta_length );
		unset( $description );

		// Body.
		$body   = $this->get_body( $post );
		$firstp = $this->get_first_paragraph( $body );
		$this->score_body( $job, $results, $body, $firstp );
		unset( $firstp );

		// URL.
		$this->score_url( $job, $results );

		// Headings.
		$headings = $this->get_headings( $body );
		$this->score_headings( $job, $results, $headings );
		unset( $headings );

		// Images.
		$imgs          = array();
		$imgs['count'] = substr_count( $body, '<img' );
		$imgs          = $this->get_images_alt_text( $post->ID, $body, $imgs );

		// Check featured image.
		if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			$imgs['count'] += 1;

			if ( empty( $imgs['alts'] ) ) {
				$imgs['alts'] = array();
			}

			$imgs['alts'][] = $this->strtolower_utf8( get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true ) );
		}

		$this->score_images_alt_text( $job, $results, $imgs );
		unset( $imgs );
		unset( $body );

		// Anchors.
		$anchors = $this->get_anchor_texts( $xpath );
		$count   = $this->get_anchor_count( $xpath );

		$this->score_anchor_texts( $job, $results, $anchors, $count );
		unset( $anchors, $count, $dom );

		$results = apply_filters( 'wpseo_linkdex_results', $results, $job, $post );

		$this->aasort( $results, 'val' );

		$overall     = 0;
		$overall_max = 0;

		foreach ( $results as $result ) {
			$overall     += $result['val'];
			$overall_max += 9;
		}
		unset( $result );

		if ( $overall < 1 ) {
			$overall = 1;
		}
		$score = WPSEO_Utils::calc( WPSEO_Utils::calc( $overall, '/', $overall_max ), '*', 100, true );

		if ( ! is_wp_error( $score ) ) {
			self::set_value( 'linkdex', absint( $score ), $post->ID );

			$results['total'] = $score;
		}

		return $results;
	}

	/**
	 * Get sample permalink
	 *
	 * @param    object $post
	 *
	 * @return    array
	 */
	function get_sample_permalink( $post ) {
		if ( ! function_exists( 'get_sample_permalink' ) ) {
			// Front-end post update.
			include_once( ABSPATH . 'wp-admin/includes/post.php' );
		}

		return get_sample_permalink( $post );
	}

	/**
	 * Save the score result to the results array.
	 *
	 * @param array  $results      The results array used to store results.
	 * @param int    $scoreValue   The score value.
	 * @param string $scoreMessage The score message.
	 * @param string $scoreLabel   The label of the score to use in the results array.
	 * @param string $rawScore     The raw score, to be used by other filters.
	 */
	function save_score_result( &$results, $scoreValue, $scoreMessage, $scoreLabel, $rawScore = null ) {
		$score                  = array(
			'val' => $scoreValue,
			'msg' => $scoreMessage,
			'raw' => $rawScore,
		);
		$results[ $scoreLabel ] = $score;
	}

	/**
	 * Clean up the input string.
	 *
	 * @param string $inputString              String to clean up.
	 * @param bool   $removeOptionalCharacters Whether or not to do a cleanup of optional chars too.
	 *
	 * @return string
	 */
	function strip_separators_and_fold( $inputString, $removeOptionalCharacters = false ) {
		$keywordCharactersAlwaysReplacedBySpace = array( ',', "'", '"', '?', '’', '“', '”', '|', '/' );
		$keywordCharactersRemovedOrReplaced     = array( '_', '-' );
		$keywordWordsRemoved                    = array( ' a ', ' in ', ' an ', ' on ', ' for ', ' the ', ' and ' );

		// Lower.
		$inputString = $this->strtolower_utf8( $inputString );

		// Default characters replaced by space.
		$inputString = str_replace( $keywordCharactersAlwaysReplacedBySpace, ' ', $inputString );

		// Standardise whitespace.
		$inputString = WPSEO_Utils::standardize_whitespace( $inputString );

		// Deal with the separators that can be either removed or replaced by space.
		if ( $removeOptionalCharacters ) {
			// Remove word separators with a space.
			$inputString = str_replace( $keywordWordsRemoved, ' ', $inputString );

			$inputString = str_replace( $keywordCharactersRemovedOrReplaced, '', $inputString );
		}
		else {
			$inputString = str_replace( $keywordCharactersRemovedOrReplaced, ' ', $inputString );
		}

		// Standardise whitespace again.
		$inputString = WPSEO_Utils::standardize_whitespace( $inputString );

		return trim( $inputString );
	}

	/**
	 * Check whether this focus keyword has been used for other posts before.
	 *
	 * @param array $job
	 * @param array $results
	 */
	function check_double_focus_keyword( $job, &$results ) {
		$posts = get_posts(
			array(
				'meta_key'    => self::$meta_prefix . 'focuskw',
				'meta_value'  => $job['keyword'],
				'exclude'     => $job['post_id'],
				'fields'      => 'ids',
				'post_type'   => 'any',
				'numberposts' => -1,
			)
		);

		if ( count( $posts ) == 0 ) {
			$this->save_score_result( $results, 9, __( 'You\'ve never used this focus keyword before, very good.', 'wordpress-seo' ), 'keyword_overused' );
		}
		elseif ( count( $posts ) == 1 ) {
			$this->save_score_result( $results, 6, sprintf( __( 'You\'ve used this focus keyword %1$sonce before%2$s, be sure to make very clear which URL on your site is the most important for this keyword.', 'wordpress-seo' ), '<a href="' . esc_url( add_query_arg( array(
					'post'   => $posts[0],
					'action' => 'edit',
				), admin_url( 'post.php' ) ) ) . '">', '</a>' ), 'keyword_overused' );
		}
		else {
			$this->save_score_result( $results, 1, sprintf( __( 'You\'ve used this focus keyword %3$s%4$d times before%2$s, it\'s probably a good idea to read %1$sthis post on cornerstone content%2$s and improve your keyword strategy.', 'wordpress-seo' ), '<a href="https://yoast.com/cornerstone-content-rank/">', '</a>', '<a href="' . esc_url( add_query_arg( array( 'seo_kw_filter' => $job['keyword'] ), admin_url( 'edit.php' ) ) ) . '">', count( $posts ) ), 'keyword_overused' );
		}
	}

	/**
	 * Check whether the keyword contains stopwords.
	 *
	 * @param string $keyword The keyword to check for stopwords.
	 * @param array  $results The results array.
	 */
	function score_keyword( $keyword, &$results ) {
		global $wpseo_admin;

		$keywordStopWord = __( 'The keyword for this page contains one or more %sstop words%s, consider removing them. Found \'%s\'.', 'wordpress-seo' );

		if ( $wpseo_admin->stopwords_check( $keyword ) !== false ) {
			$this->save_score_result( $results, 5, sprintf( $keywordStopWord, '<a href="http://en.wikipedia.org/wiki/Stop_words">', '</a>', $wpseo_admin->stopwords_check( $keyword ) ), 'keyword_stopwords' );
		}
	}

	/**
	 * Check whether the keyword is contained in the URL.
	 *
	 * @param array $job     The job array holding both the keyword and the URLs.
	 * @param array $results The results array.
	 */
	function score_url( $job, &$results ) {
		$urlGood      = __( 'The keyword / phrase appears in the URL for this page.', 'wordpress-seo' );
		$urlMedium    = __( 'The keyword / phrase does not appear in the URL for this page. If you decide to rename the URL be sure to check the old URL 301 redirects to the new one!', 'wordpress-seo' );
		$urlStopWords = __( 'The slug for this page contains one or more <a href="http://en.wikipedia.org/wiki/Stop_words">stop words</a>, consider removing them.', 'wordpress-seo' );
		$longSlug     = __( 'The slug for this page is a bit long, consider shortening it.', 'wordpress-seo' );

		$needle    = $this->strip_separators_and_fold( remove_accents( $job['keyword'] ) );
		$haystack1 = $this->strip_separators_and_fold( $job['pageUrl'], true );
		$haystack2 = $this->strip_separators_and_fold( $job['pageUrl'], false );

		if ( stripos( $haystack1, $needle ) || stripos( $haystack2, $needle ) ) {
			$this->save_score_result( $results, 9, $urlGood, 'url_keyword' );
		}
		else {
			$this->save_score_result( $results, 6, $urlMedium, 'url_keyword' );
		}

		// Check for Stop Words in the slug.
		if ( $GLOBALS['wpseo_admin']->stopwords_check( $job['pageSlug'], true ) !== false ) {
			$this->save_score_result( $results, 5, $urlStopWords, 'url_stopword' );
		}

		// Check if the slug isn't too long relative to the length of the keyword.
		if ( ( $this->statistics()->text_length( $job['keyword'] ) + 20 ) < $this->statistics()->text_length( $job['pageSlug'] ) && 40 < $this->statistics()->text_length( $job['pageSlug'] ) ) {
			$this->save_score_result( $results, 5, $longSlug, 'url_length' );
		}
	}

	/**
	 * Check whether the keyword is contained in the title.
	 *
	 * @param array $job     The job array holding both the keyword versions.
	 * @param array $results The results array.
	 */
	function score_title( $job, &$results ) {
		$scoreTitleMinLength    = 40;
		$scoreTitleMaxLength    = 70;
		$scoreTitleKeywordLimit = 0;

		$scoreTitleMissing          = __( 'Please create a page title.', 'wordpress-seo' );
		$scoreTitleCorrectLength    = __( 'The page title is more than 40 characters and less than the recommended 70 character limit.', 'wordpress-seo' );
		$scoreTitleTooShort         = __( 'The page title contains %d characters, which is less than the recommended minimum of 40 characters. Use the space to add keyword variations or create compelling call-to-action copy.', 'wordpress-seo' );
		$scoreTitleTooLong          = __( 'The page title contains %d characters, which is more than the viewable limit of 70 characters; some words will not be visible to users in your listing.', 'wordpress-seo' );
		$scoreTitleKeywordMissing   = __( 'The keyword / phrase %s does not appear in the page title.', 'wordpress-seo' );
		$scoreTitleKeywordBeginning = __( 'The page title contains keyword / phrase, at the beginning which is considered to improve rankings.', 'wordpress-seo' );
		$scoreTitleKeywordEnd       = __( 'The page title contains keyword / phrase, but it does not appear at the beginning; try and move it to the beginning.', 'wordpress-seo' );

		if ( $job['title'] == '' ) {
			$this->save_score_result( $results, 1, $scoreTitleMissing, 'title' );
		}
		else {
			$job['title'] = wp_strip_all_tags( $job['title'] );

			$length = $this->statistics()->text_length( $job['title'] );
			if ( $length < $scoreTitleMinLength ) {
				$this->save_score_result( $results, 6, sprintf( $scoreTitleTooShort, $length ), 'title_length' );
			}
			elseif ( $length > $scoreTitleMaxLength ) {
				$this->save_score_result( $results, 6, sprintf( $scoreTitleTooLong, $length ), 'title_length' );
			}
			else {
				$this->save_score_result( $results, 9, $scoreTitleCorrectLength, 'title_length' );
			}

			// @todo MA Keyword/Title matching is exact match with separators removed, but should extend to distributed match.
			$needle_position = stripos( $job['title'], $job['keyword_folded'] );

			if ( $needle_position === false ) {
				$needle_position = stripos( $job['title'], $job['keyword'] );
			}

			if ( $needle_position === false ) {
				$this->save_score_result( $results, 2, sprintf( $scoreTitleKeywordMissing, $job['keyword_folded'] ), 'title_keyword' );
			}
			elseif ( $needle_position <= $scoreTitleKeywordLimit ) {
				$this->save_score_result( $results, 9, $scoreTitleKeywordBeginning, 'title_keyword' );
			}
			else {
				$this->save_score_result( $results, 6, $scoreTitleKeywordEnd, 'title_keyword' );
			}
		}
	}

	/**
	 * Check whether the document contains outbound links and whether it's anchor text matches the keyword.
	 *
	 * @param array $job          The job array holding both the keyword versions.
	 * @param array $results      The results array.
	 * @param array $anchor_texts The array holding all anchors in the document.
	 * @param array $count        The number of anchors in the document, grouped by type.
	 */
	function score_anchor_texts( $job, &$results, $anchor_texts, $count ) {
		$scoreNoLinks               = __( 'No outbound links appear in this page, consider adding some as appropriate.', 'wordpress-seo' );
		$scoreKeywordInOutboundLink = __( 'You\'re linking to another page with the keyword you want this page to rank for, consider changing that if you truly want this page to rank.', 'wordpress-seo' );
		$scoreLinksDofollow         = __( 'This page has %s outbound link(s).', 'wordpress-seo' );
		$scoreLinksNofollow         = __( 'This page has %s outbound link(s), all nofollowed.', 'wordpress-seo' );
		$scoreLinks                 = __( 'This page has %s nofollowed link(s) and %s normal outbound link(s).', 'wordpress-seo' );

		if ( $count['external']['nofollow'] == 0 && $count['external']['dofollow'] == 0 ) {
			$this->save_score_result( $results, 6, $scoreNoLinks, 'links' );
		}
		else {
			$found = false;
			if ( is_array( $anchor_texts ) && $anchor_texts !== array() ) {
				foreach ( $anchor_texts as $anchor_text ) {
					if ( $this->strtolower_utf8( $anchor_text ) == $job['keyword_folded'] ) {
						$found = true;
					}
				}
				unset( $anchor_text );
			}
			if ( $found ) {
				$this->save_score_result( $results, 2, $scoreKeywordInOutboundLink, 'links_focus_keyword' );
			}

			if ( $count['external']['nofollow'] == 0 && $count['external']['dofollow'] > 0 ) {
				$this->save_score_result( $results, 9, sprintf( $scoreLinksDofollow, $count['external']['dofollow'] ), 'links_number' );
			}
			elseif ( $count['external']['nofollow'] > 0 && $count['external']['dofollow'] == 0 ) {
				$this->save_score_result( $results, 7, sprintf( $scoreLinksNofollow, $count['external']['nofollow'] ), 'links_number' );
			}
			else {
				$this->save_score_result( $results, 8, sprintf( $scoreLinks, $count['external']['nofollow'], $count['external']['dofollow'] ), 'links_number' );
			}
		}
	}

	/**
	 * Retrieve the anchor texts used in the current document.
	 *
	 * @param object $xpath An XPATH object of the current document.
	 *
	 * @return array
	 */
	function get_anchor_texts( &$xpath ) {
		$query        = '//a|//A';
		$dom_objects  = $xpath->query( $query );
		$anchor_texts = array();
		if ( is_object( $dom_objects ) && is_a( $dom_objects, 'DOMNodeList' ) && $dom_objects->length > 0 ) {
			foreach ( $dom_objects as $dom_object ) {
				if ( $dom_object->attributes->getNamedItem( 'href' ) ) {
					$href = $dom_object->attributes->getNamedItem( 'href' )->textContent;
					if ( substr( $href, 0, 4 ) == 'http' ) {
						$anchor_texts['external'] = $dom_object->textContent;
					}
				}
			}
		}

		return $anchor_texts;
	}

	/**
	 * Count the number of anchors and group them by type.
	 *
	 * @param object $xpath An XPATH object of the current document.
	 *
	 * @return array
	 */
	function get_anchor_count( &$xpath ) {
		$query       = '//a|//A';
		$dom_objects = $xpath->query( $query );

		$count = array(
			'total'    => 0,
			'internal' => array( 'nofollow' => 0, 'dofollow' => 0 ),
			'external' => array( 'nofollow' => 0, 'dofollow' => 0 ),
			'other'    => array( 'nofollow' => 0, 'dofollow' => 0 ),
		);

		if ( is_object( $dom_objects ) && is_a( $dom_objects, 'DOMNodeList' ) && $dom_objects->length > 0 ) {
			foreach ( $dom_objects as $dom_object ) {
				$count['total'] ++;
				if ( $dom_object->attributes->getNamedItem( 'href' ) ) {
					$href  = $dom_object->attributes->getNamedItem( 'href' )->textContent;
					$wpurl = get_bloginfo( 'url' );
					if ( WPSEO_Utils::is_url_relative( $href ) === true || substr( $href, 0, strlen( $wpurl ) ) === $wpurl ) {
						$type = 'internal';
					}
					elseif ( substr( $href, 0, 4 ) == 'http' ) {
						$type = 'external';
					}
					else {
						$type = 'other';
					}

					if ( $dom_object->attributes->getNamedItem( 'rel' ) ) {
						$link_rel = $dom_object->attributes->getNamedItem( 'rel' )->textContent;
						if ( stripos( $link_rel, 'nofollow' ) !== false ) {
							$count[ $type ]['nofollow'] ++;
						}
						else {
							$count[ $type ]['dofollow'] ++;
						}
					}
					else {
						$count[ $type ]['dofollow'] ++;
					}
				}
			}
		}

		return $count;
	}

	/**
	 * Check whether the images alt texts contain the keyword.
	 *
	 * @param array $job     The job array holding both the keyword versions.
	 * @param array $results The results array.
	 * @param array $imgs    The array with images alt texts.
	 */
	function score_images_alt_text( $job, &$results, $imgs ) {
		$scoreImagesNoImages          = __( 'No images appear in this page, consider adding some as appropriate.', 'wordpress-seo' );
		$scoreImagesNoAlt             = __( 'The images on this page are missing alt tags.', 'wordpress-seo' );
		$scoreImagesAltKeywordIn      = __( 'The images on this page contain alt tags with the target keyword / phrase.', 'wordpress-seo' );
		$scoreImagesAltKeywordMissing = __( 'The images on this page do not have alt tags containing your keyword / phrase.', 'wordpress-seo' );

		if ( $imgs['count'] == 0 ) {
			$this->save_score_result( $results, 3, $scoreImagesNoImages, 'images_alt' );
		}
		elseif ( count( $imgs['alts'] ) == 0 && $imgs['count'] != 0 ) {
			$this->save_score_result( $results, 5, $scoreImagesNoAlt, 'images_alt' );
		}
		else {
			$found = false;
			foreach ( $imgs['alts'] as $alt ) {
				$haystack1 = $this->strip_separators_and_fold( $alt, true );
				$haystack2 = $this->strip_separators_and_fold( $alt, false );
				if ( strrpos( $haystack1, $job['keyword_folded'] ) !== false ) {
					$found = true;
				}
				elseif ( strrpos( $haystack2, $job['keyword_folded'] ) !== false ) {
					$found = true;
				}
			}
			unset( $alt, $haystack1, $haystack2 );

			if ( $found ) {
				$this->save_score_result( $results, 9, $scoreImagesAltKeywordIn, 'images_alt' );
			}
			else {
				$this->save_score_result( $results, 5, $scoreImagesAltKeywordMissing, 'images_alt' );
			}
		}
	}

	/**
	 * Retrieve the alt texts from the images.
	 *
	 * @param int    $post_id The post to find images in.
	 * @param string $body    The post content to find images in.
	 * @param array  $imgs    The array holding the image information.
	 *
	 * @return array The updated images array.
	 */
	function get_images_alt_text( $post_id, $body, $imgs ) {
		$imgs['alts'] = array();
		if ( preg_match_all( '`<img[^>]+>`im', $body, $matches ) ) {
			foreach ( $matches[0] as $img ) {
				if ( preg_match( '`alt=(["\'])(.*?)\1`', $img, $alt ) && isset( $alt[2] ) ) {
					$imgs['alts'][] = $this->strtolower_utf8( $alt[2] );
				}
			}
			unset( $img, $alt );
		}
		unset( $matches );

		if ( strpos( $body, '[gallery' ) !== false ) {
			$attachments = get_children( array(
				'post_parent'    => $post_id,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'fields'         => 'ids',
			) );
			if ( is_array( $attachments ) && $attachments !== array() ) {
				foreach ( $attachments as $att_id ) {
					$alt = get_post_meta( $att_id, '_wp_attachment_image_alt', true );
					if ( $alt && ! empty( $alt ) ) {
						$imgs['alts'][] = $alt;
					}
					$imgs['count'] ++;
				}
			}
		}

		return $imgs;
	}

	/**
	 * Score the headings for keyword appearance.
	 *
	 * @param array $job      The array holding the keywords.
	 * @param array $results  The results array.
	 * @param array $headings The headings found in the document.
	 */
	function score_headings( $job, &$results, $headings ) {
		$scoreHeadingsNone           = __( 'No subheading tags (like an H2) appear in the copy.', 'wordpress-seo' );
		$scoreHeadingsKeywordIn      = __( 'Keyword / keyphrase appears in %s (out of %s) subheadings in the copy. While not a major ranking factor, this is beneficial.', 'wordpress-seo' );
		$scoreHeadingsKeywordMissing = __( 'You have not used your keyword / keyphrase in any subheading (such as an H2) in your copy.', 'wordpress-seo' );

		$headingCount = count( $headings );
		if ( $headingCount == 0 ) {
			$this->save_score_result( $results, 7, $scoreHeadingsNone, 'headings' );
		}
		else {
			$found = 0;
			foreach ( $headings as $heading ) {
				$haystack1 = $this->strip_separators_and_fold( $heading, true );
				$haystack2 = $this->strip_separators_and_fold( $heading, false );

				if ( strrpos( $haystack1, $job['keyword_folded'] ) !== false ) {
					$found ++;
				}
				elseif ( strrpos( $haystack2, $job['keyword_folded'] ) !== false ) {
					$found ++;
				}
			}
			unset( $heading, $haystack1, $haystack2 );

			if ( $found ) {
				$this->save_score_result( $results, 9, sprintf( $scoreHeadingsKeywordIn, $found, $headingCount ), 'headings' );
			}
			else {
				$this->save_score_result( $results, 3, $scoreHeadingsKeywordMissing, 'headings' );
			}
		}
	}

	/**
	 * Fetch all headings and return their content.
	 *
	 * @param string $postcontent Post content to find headings in.
	 *
	 * @return array Array of heading texts.
	 */
	function get_headings( $postcontent ) {
		$headings = array();

		preg_match_all( '`<h([1-6])(?:[^>]+)?>(.*?)</h\\1>`si', $postcontent, $matches );

		if ( isset( $matches[2] ) && is_array( $matches[2] ) && $matches[2] !== array() ) {
			foreach ( $matches[2] as $heading ) {
				$headings[] = $this->strtolower_utf8( $heading );
			}
		}

		return $headings;
	}

	/**
	 * Score the meta description for length and keyword appearance.
	 *
	 * @param array  $job         The array holding the keywords.
	 * @param array  $results     The results array.
	 * @param string $description The meta description.
	 * @param int    $maxlength   The maximum length of the meta description.
	 */
	function score_description( $job, &$results, $description, $maxlength = 155 ) {
		$scoreDescriptionMinLength      = 120;
		$scoreDescriptionCorrectLength  = __( 'In the specified meta description, consider: How does it compare to the competition? Could it be made more appealing?', 'wordpress-seo' );
		$scoreDescriptionTooShort       = __( 'The meta description is under 120 characters, however up to %s characters are available. %s', 'wordpress-seo' );
		$scoreDescriptionTooLong        = __( 'The specified meta description is over %s characters, reducing it will ensure the entire description is visible. %s', 'wordpress-seo' );
		$scoreDescriptionMissing        = __( 'No meta description has been specified, search engines will display copy from the page instead.', 'wordpress-seo' );
		$scoreDescriptionKeywordIn      = __( 'The meta description contains the primary keyword / phrase.', 'wordpress-seo' );
		$scoreDescriptionKeywordMissing = __( 'A meta description has been specified, but it does not contain the target keyword / phrase.', 'wordpress-seo' );

		$metaShorter = '';
		if ( $maxlength != 155 ) {
			$metaShorter = __( 'The available space is shorter than the usual 155 characters because Google will also include the publication date in the snippet.', 'wordpress-seo' );
		}

		if ( $description == '' ) {
			$this->save_score_result( $results, 1, $scoreDescriptionMissing, 'description_length' );
		}
		else {
			$length = $this->statistics()->text_length( $description );

			if ( $length < $scoreDescriptionMinLength ) {
				$this->save_score_result( $results, 6, sprintf( $scoreDescriptionTooShort, $maxlength, $metaShorter ), 'description_length' );
			}
			elseif ( $length <= $maxlength ) {
				$this->save_score_result( $results, 9, $scoreDescriptionCorrectLength, 'description_length' );
			}
			else {
				$this->save_score_result( $results, 6, sprintf( $scoreDescriptionTooLong, $maxlength, $metaShorter ), 'description_length' );
			}

			// @todo MA Keyword/Title matching is exact match with separators removed, but should extend to distributed match.
			$haystack1 = $this->strip_separators_and_fold( $description, true );
			$haystack2 = $this->strip_separators_and_fold( $description, false );
			if ( strrpos( $haystack1, $job['keyword_folded'] ) === false && strrpos( $haystack2, $job['keyword_folded'] ) === false ) {
				$this->save_score_result( $results, 3, $scoreDescriptionKeywordMissing, 'description_keyword' );
			}
			else {
				$this->save_score_result( $results, 9, $scoreDescriptionKeywordIn, 'description_keyword' );
			}
		}
	}

	/**
	 * Score the body for length and keyword appearance.
	 *
	 * @param array  $job     The array holding the keywords.
	 * @param array  $results The results array.
	 * @param string $body    The body.
	 * @param string $firstp  The first paragraph.
	 */
	function score_body( $job, &$results, $body, $firstp ) {
		$lengthScore = array(
			'good' => 300,
			'ok'   => 250,
			'poor' => 200,
			'bad'  => 100,
		);
		$lengthScore = apply_filters( 'wpseo_body_length_score', $lengthScore, $job );

		$scoreBodyGoodLength = __( 'There are %d words contained in the body copy, this is more than the %d word recommended minimum.', 'wordpress-seo' );
		$scoreBodyPoorLength = __( 'There are %d words contained in the body copy, this is below the %d word recommended minimum. Add more useful content on this topic for readers.', 'wordpress-seo' );
		$scoreBodyOKLength   = __( 'There are %d words contained in the body copy, this is slightly below the %d word recommended minimum, add a bit more copy.', 'wordpress-seo' );
		$scoreBodyBadLength  = __( 'There are %d words contained in the body copy. This is far too low and should be increased.', 'wordpress-seo' );

		$scoreKeywordDensityLow  = __( 'The keyword density is %s%%, which is a bit low, the keyword was found %s times.', 'wordpress-seo' );
		$scoreKeywordDensityHigh = __( 'The keyword density is %s%%, which is over the advised 4.5%% maximum, the keyword was found %s times.', 'wordpress-seo' );
		$scoreKeywordDensityGood = __( 'The keyword density is %s%%, which is great, the keyword was found %s times.', 'wordpress-seo' );

		$scoreFirstParagraphLow  = __( 'The keyword doesn\'t appear in the first paragraph of the copy, make sure the topic is clear immediately.', 'wordpress-seo' );
		$scoreFirstParagraphHigh = __( 'The keyword appears in the first paragraph of the copy.', 'wordpress-seo' );

		$fleschurl   = '<a href="http://en.wikipedia.org/wiki/Flesch-Kincaid_readability_test#Flesch_Reading_Ease">' . __( 'Flesch Reading Ease', 'wordpress-seo' ) . '</a>';
		$scoreFlesch = __( 'The copy scores %s in the %s test, which is considered %s to read. %s', 'wordpress-seo' );

		// Replace images with their alt tags, then strip all tags.
		$body = preg_replace( '`<img(?:[^>]+)?alt="([^"]+)"(?:[^>]+)>`', '$1', $body );
		$body = strip_tags( $body );

		// Copy length check.
		$wordCount = $this->statistics()->word_count( $body );

		if ( $wordCount < $lengthScore['bad'] ) {
			$this->save_score_result( $results, - 20, sprintf( $scoreBodyBadLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
		}
		elseif ( $wordCount < $lengthScore['poor'] ) {
			$this->save_score_result( $results, - 10, sprintf( $scoreBodyPoorLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
		}
		elseif ( $wordCount < $lengthScore['ok'] ) {
			$this->save_score_result( $results, 5, sprintf( $scoreBodyPoorLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
		}
		elseif ( $wordCount < $lengthScore['good'] ) {
			$this->save_score_result( $results, 7, sprintf( $scoreBodyOKLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
		}
		else {
			$this->save_score_result( $results, 9, sprintf( $scoreBodyGoodLength, $wordCount, $lengthScore['good'] ), 'body_length', $wordCount );
		}

		$body           = $this->strtolower_utf8( $body );
		$job['keyword'] = $this->strtolower_utf8( $job['keyword'] );

		$keywordWordCount = $this->statistics()->word_count( $job['keyword'] );

		if ( $keywordWordCount > 10 ) {
			$this->save_score_result( $results, 0, __( 'Your keyphrase is over 10 words, a keyphrase should be shorter and there can be only one keyphrase.', 'wordpress-seo' ), 'focus_keyword_length' );
		}
		else {
			// Keyword Density check.
			$keywordDensity = 0;
			if ( $wordCount > 100 ) {
				$keywordCount = preg_match_all( '`\b' . preg_quote( $job['keyword'], '`' ) . '\b`miu', $body, $matches );
				if ( ( $keywordCount > 0 && $keywordWordCount > 0 ) && $wordCount > $keywordCount ) {
					$keywordDensity = WPSEO_Utils::calc( WPSEO_Utils::calc( $keywordCount, '/', WPSEO_Utils::calc( $wordCount, '-', ( WPSEO_Utils::calc( WPSEO_Utils::calc( $keywordWordCount, '-', 1 ), '*', $keywordCount ) ) ) ), '*', 100, true, 2 );
				}
				if ( $keywordDensity < 1 ) {
					$this->save_score_result( $results, 4, sprintf( $scoreKeywordDensityLow, $keywordDensity, $keywordCount ), 'keyword_density' );
				}
				elseif ( $keywordDensity > 4.5 ) {
					$this->save_score_result( $results, - 50, sprintf( $scoreKeywordDensityHigh, $keywordDensity, $keywordCount ), 'keyword_density' );
				}
				else {
					$this->save_score_result( $results, 9, sprintf( $scoreKeywordDensityGood, $keywordDensity, $keywordCount ), 'keyword_density' );
				}
			}
		}

		$firstp = $this->strtolower_utf8( $firstp );

		// First Paragraph Test.
		// Check without /u modifier as well as /u might break with non UTF-8 chars.
		if ( preg_match( '`\b' . preg_quote( $job['keyword'], '`' ) . '\b`miu', $firstp ) || preg_match( '`\b' . preg_quote( $job['keyword'], '`' ) . '\b`mi', $firstp ) || preg_match( '`\b' . preg_quote( $job['keyword_folded'], '`' ) . '\b`miu', $firstp )
		) {
			$this->save_score_result( $results, 9, $scoreFirstParagraphHigh, 'keyword_first_paragraph' );
		}
		else {
			$this->save_score_result( $results, 3, $scoreFirstParagraphLow, 'keyword_first_paragraph' );
		}

		$lang = get_bloginfo( 'language' );
		if ( substr( $lang, 0, 2 ) == 'en' && $wordCount > 100 ) {
			// Flesch Reading Ease check.
			$flesch = $this->statistics()->flesch_kincaid_reading_ease( $body );

			$note  = '';
			$level = '';
			$score = 1;
			if ( $flesch >= 90 ) {
				$level = __( 'very easy', 'wordpress-seo' );
				$score = 9;
			}
			elseif ( $flesch >= 80 ) {
				$level = __( 'easy', 'wordpress-seo' );
				$score = 9;
			}
			elseif ( $flesch >= 70 ) {
				$level = __( 'fairly easy', 'wordpress-seo' );
				$score = 8;
			}
			elseif ( $flesch >= 60 ) {
				$level = __( 'OK', 'wordpress-seo' );
				$score = 7;
			}
			elseif ( $flesch >= 50 ) {
				$level = __( 'fairly difficult', 'wordpress-seo' );
				$note  = __( 'Try to make shorter sentences to improve readability.', 'wordpress-seo' );
				$score = 6;
			}
			elseif ( $flesch >= 30 ) {
				$level = __( 'difficult', 'wordpress-seo' );
				$note  = __( 'Try to make shorter sentences, using less difficult words to improve readability.', 'wordpress-seo' );
				$score = 5;
			}
			elseif ( $flesch >= 0 ) {
				$level = __( 'very difficult', 'wordpress-seo' );
				$note  = __( 'Try to make shorter sentences, using less difficult words to improve readability.', 'wordpress-seo' );
				$score = 4;
			}
			$this->save_score_result( $results, $score, sprintf( $scoreFlesch, $flesch, $fleschurl, $level, $note ), 'flesch_kincaid' );
		}
	}

	/**
	 * Retrieve the body from the post.
	 *
	 * @param object $post The post object.
	 *
	 * @return string The post content.
	 */
	function get_body( $post ) {
		// This filter allows plugins to add their content to the content to be analyzed.
		$post_content = apply_filters( 'wpseo_pre_analysis_post_content', $post->post_content, $post );

		// Strip shortcodes, for obvious reasons, if plugins think their content should be in the analysis, they should
		// hook into the above filter.
		$post_content = WPSEO_Utils::trim_nbsp_from_string( WPSEO_Utils::strip_shortcode( $post_content ) );

		if ( trim( $post_content ) == '' ) {
			return '';
		}

		$htmdata3 = preg_replace( '`<(?:\x20*script|script).*?(?:/>|/script>)`', '', $post_content );
		if ( $htmdata3 == null ) {
			$htmdata3 = $post_content;
		}
		else {
			unset( $post_content );
		}

		$htmdata4 = preg_replace( '`<!--.*?-->`', '', $htmdata3 );
		if ( $htmdata4 == null ) {
			$htmdata4 = $htmdata3;
		}
		else {
			unset( $htmdata3 );
		}

		$htmdata5 = preg_replace( '`<(?:\x20*style|style).*?(?:/>|/style>)`', '', $htmdata4 );
		if ( $htmdata5 == null ) {
			$htmdata5 = $htmdata4;
		}
		else {
			unset( $htmdata4 );
		}

		return $htmdata5;
	}

	/**
	 * Retrieve the first paragraph from the post.
	 *
	 * @param string $body The post content to retrieve the first paragraph from.
	 *
	 * @return string
	 */
	function get_first_paragraph( $body ) {
		// To determine the first paragraph we first need to autop the content, then match the first paragraph and return.
		if ( preg_match( '`<p[.]*?>(.*)</p>`s', wpautop( $body ), $matches ) ) {
			return $matches[1];
		}

		return false;
	}

	/********************** DEPRECATED METHODS **********************/

	/**
	 * Adds the Yoast SEO box
	 *
	 * @deprecated 1.4.24
	 * @deprecated use WPSEO_Metabox::add_meta_box()
	 * @see        WPSEO_Meta::add_meta_box()
	 */
	public function add_custom_box() {
		_deprecated_function( __METHOD__, 'WPSEO 1.4.24', 'WPSEO_Metabox::add_meta_box()' );
		$this->add_meta_box();
	}

	/**
	 * Retrieve the meta boxes for the given post type.
	 *
	 * @deprecated 1.5.0
	 * @deprecated use WPSEO_Meta::get_meta_field_defs()
	 * @see        WPSEO_Meta::get_meta_field_defs()
	 *
	 * @param  string $post_type
	 *
	 * @return  array
	 */
	public function get_meta_boxes( $post_type = 'post' ) {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.0', 'WPSEO_Meta::get_meta_field_defs()' );

		return $this->get_meta_field_defs( 'general', $post_type );
	}

	/**
	 * Pass some variables to js
	 *
	 * @deprecated 1.5.0
	 * @deprecated use WPSEO_Meta::localize_script()
	 * @see        WPSEO_Meta::localize_script()
	 */
	public function script() {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.0', 'WPSEO_Meta::localize_script()' );

		return $this->localize_script();
	}

} /* End of class */
