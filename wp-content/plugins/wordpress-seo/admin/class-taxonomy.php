<?php
/**
 * @package WPSEO\Admin
 */

/**
 * Class that handles the edit boxes on taxonomy edit pages.
 */
class WPSEO_Taxonomy {

	/**
	 * @var array   Options array for the no-index options, including translated labels
	 */
	public $no_index_options = array();

	/**
	 * @var array   Options array for the sitemap_include options, including translated labels
	 */
	public $sitemap_include_options = array();

	/**
	 * Class constructor
	 */
	function __construct() {
		$options = WPSEO_Options::get_all();

		if ( is_admin() && ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] !== '' ) &&
		     ( ! isset( $options[ 'hideeditbox-tax-' . $_GET['taxonomy'] ] ) || $options[ 'hideeditbox-tax-' . $_GET['taxonomy'] ] === false )
		) {
			add_action( sanitize_text_field( $_GET['taxonomy'] ) . '_edit_form', array(
				$this,
				'term_seo_form',
			), 90, 1 );
		}

		add_action( 'split_shared_term', array( $this, 'split_shared_term' ), 10, 4 );
		add_action( 'edit_term', array( $this, 'update_term' ), 99, 3 );

		add_action( 'init', array( $this, 'custom_category_descriptions_allow_html' ) );
		add_filter( 'category_description', array( $this, 'custom_category_descriptions_add_shortcode_support' ) );
		add_action( 'admin_init', array( $this, 'translate_meta_options' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Makes sure the taxonomy meta is updated when a taxonomy term is split.
	 *
	 * @link https://make.wordpress.org/core/2015/02/16/taxonomy-term-splitting-in-4-2-a-developer-guide/ Article explaining the taxonomy term splitting in WP 4.2.
	 *
	 * @param string $old_term_id      Old term id of the taxonomy term that was splitted.
	 * @param string $new_term_id      New term id of the taxonomy term that was splitted.
	 * @param string $term_taxonomy_id Term taxonomy id for the taxonomy that was affected.
	 * @param string $taxonomy         The taxonomy that the taxonomy term was splitted for.
	 */
	public function split_shared_term( $old_term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
		$tax_meta = get_option( 'wpseo_taxonomy_meta', array() );

		if ( ! empty( $tax_meta[ $taxonomy ][ $old_term_id ] ) ) {
			$tax_meta[ $taxonomy ][ $new_term_id ] = $tax_meta[ $taxonomy ][ $old_term_id ];
			unset( $tax_meta[ $taxonomy ][ $old_term_id ] );
			update_option( 'wpseo_taxonomy_meta', $tax_meta );
		}
	}

	/**
	 * Translate options text strings for use in the select fields
	 *
	 * @internal IMPORTANT: if you want to add a new string (option) somewhere, make sure you add
	 * that array key to the main options definition array in the class WPSEO_Taxonomy_Meta() as well!!!!
	 */
	public function translate_meta_options() {
		$this->no_index_options        = WPSEO_Taxonomy_Meta::$no_index_options;
		$this->sitemap_include_options = WPSEO_Taxonomy_Meta::$sitemap_include_options;

		$this->no_index_options['default'] = __( 'Use %s default (Currently: %s)', 'wordpress-seo' );
		$this->no_index_options['index']   = __( 'Always index', 'wordpress-seo' );
		$this->no_index_options['noindex'] = __( 'Always noindex', 'wordpress-seo' );

		$this->sitemap_include_options['-']      = __( 'Auto detect', 'wordpress-seo' );
		$this->sitemap_include_options['always'] = __( 'Always include', 'wordpress-seo' );
		$this->sitemap_include_options['never']  = __( 'Never include', 'wordpress-seo' );
	}


	/**
	 * Test whether we are on a public taxonomy - no metabox actions needed if we are not
	 * Unfortunately we have to hook most everything in before the point where all taxonomies are registered and
	 * we know which taxonomy is being requested, so we need to use this check in nearly every hooked in function.
	 *
	 * @since 1.5.0
	 */
	function tax_is_public() {
		// Don't make static as taxonomies may still be added during the run.
		$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );

		return ( isset( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], $taxonomies ) );
	}


	/**
	 * Add our admin css file
	 */
	function admin_enqueue_scripts() {
		if ( $GLOBALS['pagenow'] === 'edit-tags.php' && ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' ) ) {
			wp_enqueue_style( 'yoast-taxonomy-css', plugins_url( 'css/taxonomy-meta' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );
		}
	}


	/**
	 * Create a row in the form table.
	 *
	 * @param string $var      Variable the row controls.
	 * @param string $label    Label for the variable.
	 * @param string $desc     Description of the use of the variable.
	 * @param array  $tax_meta Taxonomy meta value.
	 * @param string $type     Type of form row to create.
	 * @param array  $options  Options to use when form row is a select box.
	 */
	function form_row( $var, $label, $desc, $tax_meta, $type = 'text', $options = array() ) {
		$val = '';
		if ( isset( $tax_meta[ $var ] ) && $tax_meta[ $var ] !== '' ) {
			$val = $tax_meta[ $var ];
		}

		$esc_var = esc_attr( $var );
		$field   = '';

		if ( $type == 'text' ) {
			$field .= '
				<input name="' . $esc_var . '" id="' . $esc_var . '" type="text" value="' . esc_attr( $val ) . '" size="40"/>';
		}
		elseif ( $type == 'checkbox' ) {
			$field .= '
				<input name="' . $esc_var . '" id="' . $esc_var . '" type="checkbox" ' . checked( $val ) . '/>';
		}
		elseif ( $type == 'select' ) {
			if ( is_array( $options ) && $options !== array() ) {
				$field .= '
				<select name="' . $esc_var . '" id="' . $esc_var . '">';

				foreach ( $options as $option => $option_label ) {
					$selected = selected( $option, $val, false );
					$field .= '
					<option ' . $selected . ' value="' . esc_attr( $option ) . '">' . esc_html( $option_label ) . '</option>';
				}
				unset( $option, $option_label, $selected );

				$field .= '
				</select>';
			}
		}
		elseif ( $type == 'hidden' ) {
			$field .= '
				<input name="' . $esc_var . '" id="hidden_' . $esc_var . '" type="hidden" value="' . esc_attr( $val ) . '" />';
		}

		if ( $field !== '' && ( is_string( $desc ) && $desc !== '' ) ) {
			$field .= '
	        <p class="description">' . $desc . '</p>';
		}

		echo '
		<tr class="form-field">
			<th scope="row">' . ( ( '' !== $label ) ? '<label for="' . $esc_var . '">' . esc_html( $label ) . '</label>' : '' ) . '</th>
			<td>' . $field . '</td>
		</tr>';
	}

	/**
	 * Show the SEO inputs for term.
	 *
	 * @param object $term Term to show the edit boxes for.
	 */
	function term_seo_form( $term ) {
		if ( $this->tax_is_public() === false ) {
			return;
		}

		$tax_meta = WPSEO_Taxonomy_Meta::get_term_meta( (int) $term->term_id, $term->taxonomy );
		$options  = WPSEO_Options::get_all();

		/* translators: %1$s expands to Yoast SEO */
		echo '<h3>', sprintf( __( '%1$s Settings', 'wordpress-seo' ), 'Yoast SEO' ) . '</h2>';
		echo '<table class="form-table wpseo-taxonomy-form">';

		$this->form_row( 'wpseo_title', __( 'SEO Title', 'wordpress-seo' ), esc_html__( 'The SEO title is used on the archive page for this term.', 'wordpress-seo' ), $tax_meta );
		$this->form_row( 'wpseo_desc', __( 'SEO Description', 'wordpress-seo' ), esc_html__( 'The SEO description is used for the meta description on the archive page for this term.', 'wordpress-seo' ), $tax_meta );

		if ( $options['usemetakeywords'] === true ) {
			$this->form_row( 'wpseo_metakey', __( 'Meta keywords', 'wordpress-seo' ), esc_html__( 'Meta keywords used on the archive page for this term.', 'wordpress-seo' ), $tax_meta );
		}

		$this->form_row( 'wpseo_canonical', __( 'Canonical', 'wordpress-seo' ), esc_html__( 'The canonical link is shown on the archive page for this term.', 'wordpress-seo' ), $tax_meta );

		if ( $options['breadcrumbs-enable'] === true ) {
			$this->form_row( 'wpseo_bctitle', __( 'Breadcrumbs title', 'wordpress-seo' ), sprintf( esc_html__( 'The Breadcrumbs title is used in the breadcrumbs where this %s appears.', 'wordpress-seo' ), $term->taxonomy ), $tax_meta );
		}

		$current = 'index';
		if ( isset( $options[ 'noindex-tax-' . $term->taxonomy ] ) && $options[ 'noindex-tax-' . $term->taxonomy ] === true ) {
			$current = 'noindex';
		}

		$noindex_options            = $this->no_index_options;
		$noindex_options['default'] = sprintf( $noindex_options['default'], $term->taxonomy, $current );

		$desc = sprintf( esc_html__( 'This %s follows the indexation rules set under Metas and Titles, you can override it here.', 'wordpress-seo' ), $term->taxonomy );
		if ( '0' == get_option( 'blog_public' ) ) {
			$desc .= '<br /><span class="error-message">' . esc_html__( 'Warning: even though you can set the meta robots setting here, the entire site is set to noindex in the sitewide privacy settings, so these settings won\'t have an effect.', 'wordpress-seo' ) . '</span>';
		}

		$this->form_row( 'wpseo_noindex', sprintf( __( 'Noindex this %s', 'wordpress-seo' ), $term->taxonomy ), $desc, $tax_meta, 'select', $noindex_options );
		unset( $current, $no_index_options, $desc );


		$this->form_row( 'wpseo_sitemap_include', __( 'Include in sitemap?', 'wordpress-seo' ), '', $tax_meta, 'select', $this->sitemap_include_options );

		echo '</table>';
	}

	/**
	 * Update the taxonomy meta data on save.
	 *
	 * @param int    $term_id  ID of the term to save data for.
	 * @param int    $tt_id    The taxonomy_term_id for the term.
	 * @param string $taxonomy The taxonomy the term belongs to.
	 */
	function update_term( $term_id, $tt_id, $taxonomy ) {
		$tax_meta = get_option( 'wpseo_taxonomy_meta' );

		/* Create post array with only our values */
		$new_meta_data = array();
		foreach ( WPSEO_Taxonomy_Meta::$defaults_per_term as $key => $default ) {
			if ( isset( $_POST[ $key ] ) ) {
				$new_meta_data[ $key ] = $_POST[ $key ];
			}
		}
		unset( $key, $default );

		/* Validate the post values */
		$old   = WPSEO_Taxonomy_Meta::get_term_meta( $term_id, $taxonomy );
		$clean = WPSEO_Taxonomy_Meta::validate_term_meta_data( $new_meta_data, $old );

		/* Add/remove the result to/from the original option value */
		if ( $clean !== array() ) {
			$tax_meta[ $taxonomy ][ $term_id ] = $clean;
		}
		else {
			unset( $tax_meta[ $taxonomy ][ $term_id ] );
			if ( isset( $tax_meta[ $taxonomy ] ) && $tax_meta[ $taxonomy ] === array() ) {
				unset( $tax_meta[ $taxonomy ] );
			}
		}

		// Prevent complete array validation.
		$tax_meta['wpseo_already_validated'] = true;

		update_option( 'wpseo_taxonomy_meta', $tax_meta );
	}


	/**
	 * Allows HTML in descriptions
	 */
	function custom_category_descriptions_allow_html() {
		$filters = array(
			'pre_term_description',
			'pre_link_description',
			'pre_link_notes',
			'pre_user_description',
		);

		foreach ( $filters as $filter ) {
			remove_filter( $filter, 'wp_filter_kses' );
		}
		remove_filter( 'term_description', 'wp_kses_data' );
	}

	/**
	 * Adds shortcode support to category descriptions.
	 *
	 * @param string $desc String to add shortcodes in.
	 *
	 * @return string
	 */
	function custom_category_descriptions_add_shortcode_support( $desc ) {
		// Wrap in output buffering to prevent shortcodes that echo stuff instead of return from breaking things.
		ob_start();
		$desc = do_shortcode( $desc );
		ob_end_clean();

		return $desc;
	}
} /* End of class */
