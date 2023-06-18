<?php
/**
 * Base class used for Smart Tags throughout the plugin.
 *
 * @package WPCode
 */

/**
 * WPCode_Smart_Tags class.
 */
class WPCode_Smart_Tags {

	/**
	 * The tags array.
	 *
	 * @var array
	 */
	protected $tags;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Add filters to replace the tags in the snippet code.
	 *
	 * @return void
	 */
	public function hooks() {
	}

	/**
	 * Load tags in the instance.
	 *
	 * @return void
	 */
	public function load_tags() {
		$generic_tags = array(
			'id'           => array(
				'label'    => __( 'Content ID', 'insert-headers-and-footers' ),
				'function' => array( $this, 'get_the_ID' ),
			),
			'title'        => array(
				'label'    => __( 'Content title', 'insert-headers-and-footers' ),
				'function' => array( $this, 'get_the_title' ),
			),
			'categories'   => array(
				'label'    => __( 'Content Categories', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_categories' ),
			),
			'email'        => array(
				'label'    => __( 'User\'s email', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_email' ),
			),
			'first_name'   => array(
				'label'    => __( 'User\'s first name', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_first_name' ),
			),
			'last_name'    => array(
				'label'    => __( 'User\'s last name', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_last_name' ),
			),
			'custom_field' => array(
				'label'      => __( 'Custom Field (meta)', 'insert-headers-and-footers' ),
				'function'   => array( $this, 'tag_value_custom_field' ),
				'editor_tag' => 'custom_field="meta_key"',
			),
			'author_id'    => array(
				'label'    => __( 'Author ID', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_author_id' ),
			),
			'author_name'  => array(
				'label'    => __( 'Author Name', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_author_name' ),
			),
			'author_url'   => array(
				'label'    => __( 'Author URL', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_author_url' ),
			),
			'login_url'    => array(
				'label'    => __( 'Login URL', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_login_url' ),
			),
			'logout_url'   => array(
				'label'    => __( 'Logout URL', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_logout_url' ),
			),
		);

		$woocommerce_tags = array(
			'wc_order_number'   => array(
				'label'    => __( 'Order number', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_wc_order_number' ),
			),
			'wc_order_subtotal' => array(
				'label'    => __( 'Order subtotal', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_wc_order_subtotal' ),
			),
			'wc_order_total'    => array(
				'label'    => __( 'Order total', 'insert-headers-and-footers' ),
				'function' => array( $this, 'tag_value_wc_order_total' ),
			),
		);

		$tags = array(
			'generic' => array(
				'label' => '',
				'tags'  => $generic_tags,
			),
		);

		if ( $this->woocommerce_available() ) {
			$tags['woocommerce'] = array(
				'label' => 'WooCommerce',
				'tags'  => $woocommerce_tags,
			);
		}

		$this->tags = apply_filters( 'wpcode_smart_tags', $tags );
	}

	/**
	 * Get smart tags with labels.
	 *
	 * @return array
	 */
	public function get_tags() {
		if ( ! isset( $this->tags ) ) {
			$this->load_tags();
		}

		return $this->tags;
	}

	/**
	 * Check if WooCommerce is installed & active on the site.
	 *
	 * @return bool
	 */
	public function woocommerce_available() {
		return class_exists( 'woocommerce' );
	}

	/**
	 * @param $tag
	 *
	 * @return false|mixed
	 */
	public function get_tag_editor_tag( $tag ) {
		$tags = $this->get_tags();
		foreach ( $tags as $category ) {
			if ( isset( $category['tags'][ $tag ]['editor_tag'] ) ) {
				return $category['tags'][ $tag ]['editor_tag'];
			}
		}

		return $tag;
	}

	/**
	 * Get a tag in the format used in the code.
	 *
	 * @param string $tag The tag to wrap in code format.
	 *
	 * @return string
	 */
	public function get_tag_code( $tag ) {
		return "{{$tag}}";
	}

	/**
	 * Smart tags picker markup with a target id where the selected smart tag will be inserted.
	 *
	 * @param string $target The id of the textarea where the smart tag will be inserted.
	 *
	 * @return void
	 */
	public function smart_tags_picker( $target = '' ) {
		$tags        = $this->get_tags();
		$unavailable = ! empty( $this->upgrade_notice_data() ) ? ' wpcode-smart-tags-unavailable' : '';
		?>
		<div class="wpcode-smart-tags <?php echo esc_attr( $unavailable ); ?>">
			<button class="wpcode-smart-tags-toggle" type="button">
				<?php wpcode_icon( 'tags', 20, 16, '0 0 20 16' ); ?>
				<span class="wpcode-text-default">
					<?php esc_html_e( 'Show Smart Tags', 'insert-headers-and-footers' ); ?>
					</span>
				<span class="wpcode-text-active">
					<?php esc_html_e( 'Hide Smart Tags', 'insert-headers-and-footers' ); ?>
					</span>
			</button>
			<div class="wpcode-smart-tags-dropdown" data-target="<?php echo esc_attr( $target ); ?>" <?php $this->upgrade_data_attributes(); ?>>
				<?php
				foreach ( $tags as $tag_category ) {
					?>
					<ul>
						<?php
						if ( ! empty( $tag_category['label'] ) ) {
							printf(
								'<li class="wpcode-smart-tag-category-label">%s</li>',
								esc_html( $tag_category['label'] )
							);
						}
						if ( ! empty( $tag_category['tags'] ) ) {
							foreach ( $tag_category['tags'] as $tag => $tag_data ) {
								if ( empty( $tag_data['label'] ) ) {
									continue;
								}
								$this->tag_button( $tag, $tag_data['label'] );
							}
						}
						?>
					</ul>
					<?php
				}
				?>
				<div class="wpcode-smart-tags-dropdown-footer">
					<a href="<?php echo esc_url( wpcode_utm_url( 'https://wpcode.com/docs/smart-tags', 'smart-tags', 'dropdown' ) ); ?>" target="_blank" rel="noopener noreferrer">
						<?php wpcode_icon( 'help', 21 ); ?>
						<?php esc_html_e( 'Learn more about Smart Tags', 'insert-headers-and-footers' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the tag button markup.
	 *
	 * @param string $tag The tag.
	 * @param string $label The tag label.
	 *
	 * @return void
	 */
	public function tag_button( $tag, $label = '' ) {
		$tag_code   = $this->get_tag_code( $tag );
		$editor_tag = $this->get_tag_code( $this->get_tag_editor_tag( $tag ) );
		printf(
			'<li><button class="wpcode-insert-smart-tag" data-tag="%3$s" type="button"><code>%1$s</code> - %2$s</button></li>',
			esc_html( $tag_code ),
			esc_html( $label ),
			esc_attr( $editor_tag )
		);
	}

	/**
	 * Get upgrade notice data.
	 *
	 * @return array
	 */
	public function upgrade_notice_data() {
		return array();
	}

	/**
	 * Print upgrade notice data attributes, if any.
	 *
	 * @return void
	 */
	public function upgrade_data_attributes() {
		$upgrade_data = $this->upgrade_notice_data();

		foreach ( $upgrade_data as $attribute => $value ) {
			printf( ' data-upgrade-%s="%s"', esc_attr( $attribute ), esc_attr( $value ) );
		}
	}
}
