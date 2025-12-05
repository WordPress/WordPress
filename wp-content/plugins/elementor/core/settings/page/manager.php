<?php
namespace Elementor\Core\Settings\Page;

use Elementor\Core\Base\Document;
use Elementor\Core\Files\CSS\Base;
use Elementor\Core\Files\CSS\Post;
use Elementor\Core\Files\CSS\Post_Preview;
use Elementor\Core\Settings\Base\CSS_Manager;
use Elementor\Core\Utils\Exceptions;
use Elementor\Core\Settings\Base\Model as BaseModel;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor page settings manager.
 *
 * Elementor page settings manager handler class is responsible for registering
 * and managing Elementor page settings managers.
 *
 * @since 1.6.0
 */
class Manager extends CSS_Manager {

	/**
	 * Meta key for the page settings.
	 */
	const META_KEY = '_elementor_page_settings';

	/**
	 * Get manager name.
	 *
	 * Retrieve page settings manager name.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Manager name.
	 */
	public function get_name() {
		return 'page';
	}

	/**
	 * Get model for config.
	 *
	 * Retrieve the model for settings configuration.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return BaseModel The model object.
	 */
	public function get_model_for_config() {
		if ( ! is_singular() && ! Plugin::$instance->editor->is_edit_mode() ) {
			return null;
		}

		if ( Plugin::$instance->editor->is_edit_mode() ) {
			$post_id = Plugin::$instance->editor->get_post_id();
			$document = Plugin::$instance->documents->get_doc_or_auto_save( $post_id );
		} else {
			$post_id = get_the_ID();
			$document = Plugin::$instance->documents->get_doc_for_frontend( $post_id );
		}

		if ( ! $document ) {
			return null;
		}

		$model = $this->get_model( $document->get_post()->ID );

		if ( $document->is_autosave() ) {
			$model->set_settings( 'post_status', $document->get_main_post()->post_status );
		}

		return $model;
	}

	/**
	 * Ajax before saving settings.
	 *
	 * Validate the data before saving it and updating the data in the database.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $data Post data.
	 * @param int   $id   Post ID.
	 *
	 * @throws \Exception If invalid post returned using the `$id`.
	 * @throws \Exception If current user don't have permissions to edit the post.
	 */
	public function ajax_before_save_settings( array $data, $id ) {
		$post = get_post( $id );

		if ( empty( $post ) ) {
			throw new \Exception( 'Invalid post.', Exceptions::NOT_FOUND ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		if ( ! Utils::is_wp_cli() && ! current_user_can( 'edit_post', $id ) ) {
			throw new \Exception( 'Access denied.', Exceptions::FORBIDDEN ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		// Avoid save empty post title.
		if ( ! empty( $data['post_title'] ) ) {
			$post->post_title = $data['post_title'];
		}

		if ( isset( $data['post_excerpt'] ) && post_type_supports( $post->post_type, 'excerpt' ) ) {
			$post->post_excerpt = $data['post_excerpt'];
		}

		if ( isset( $data['menu_order'] ) && is_post_type_hierarchical( $post->post_type ) ) {
			$post->menu_order = $data['menu_order'];
		}

		if ( isset( $data['post_status'] ) ) {
			$this->save_post_status( $id, $data['post_status'] );
			unset( $post->post_status );
		}

		if ( isset( $data['comment_status'] ) && post_type_supports( $post->post_type, 'comments' ) ) {
			$post->comment_status = $data['comment_status'];
		}

		wp_update_post( $post );

		// Check updated status.
		if ( Document::STATUS_PUBLISH === get_post_status( $id ) ) {
			$autosave = wp_get_post_autosave( $post->ID );
			if ( $autosave ) {
				wp_delete_post_revision( $autosave->ID );
			}
		}

		if ( isset( $data['post_featured_image'] ) && post_type_supports( $post->post_type, 'thumbnail' ) ) {
			// Check if the user is at least an Author before allowing them to modify the thumbnail.
			if ( ! current_user_can( 'publish_posts' ) ) {
				throw new \Exception( 'You do not have permission to modify the featured image.', Exceptions::FORBIDDEN ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			}

			if ( empty( $data['post_featured_image']['id'] ) ) {
				delete_post_thumbnail( $post->ID );
			} else {
				set_post_thumbnail( $post->ID, $data['post_featured_image']['id'] );
			}
		}

		if ( Utils::is_cpt_custom_templates_supported() ) {
			$template = get_metadata( 'post', $post->ID, '_wp_page_template', true );

			if ( isset( $data['template'] ) ) {
				$template = $data['template'];
			}

			if ( empty( $template ) ) {
				$template = 'default';
			}

			// Use `update_metadata` in order to save also for revisions.
			update_metadata( 'post', $post->ID, '_wp_page_template', $template );
		}
	}

	/**
	 * @inheritDoc
	 *
	 * Override parent because the page setting moved to document.settings.
	 */
	protected function print_editor_template_content( $name ) {
		?>
		<#
		const tabs = elementor.config.document.settings.tabs;

		if ( Object.values( tabs ).length > 1 ) { #>
		<div class="elementor-panel-navigation">
			<# _.each( tabs, function( tabTitle, tabSlug ) {
			$e.bc.ensureTab( 'panel/page-settings', tabSlug ); #>
			<button class="elementor-component-tab elementor-panel-navigation-tab elementor-tab-control-{{ tabSlug }}" data-tab="{{ tabSlug }}">
				<span>{{{ tabTitle }}}</span>
			</button>
			<# } ); #>
		</div>
		<# } #>
		<div id="elementor-panel-<?php echo esc_attr( $name ); ?>-settings-controls"></div>
		<?php
	}

	/**
	 * Save settings to DB.
	 *
	 * Save page settings to the database, as post meta data.
	 *
	 * @since 1.6.0
	 * @access protected
	 *
	 * @param array $settings Settings.
	 * @param int   $id       Post ID.
	 */
	protected function save_settings_to_db( array $settings, $id ) {
		// Use update/delete_metadata in order to handle also revisions.
		if ( ! empty( $settings ) ) {
			// Use `wp_slash` in order to avoid the unslashing during the `update_post_meta`.
			update_metadata( 'post', $id, self::META_KEY, wp_slash( $settings ) );
		} else {
			delete_metadata( 'post', $id, self::META_KEY );
		}
	}

	/**
	 * Get CSS file for update.
	 *
	 * Retrieve the CSS file before updating it.
	 *
	 * This method overrides the parent method to disallow updating CSS files for pages.
	 *
	 * @since 1.6.0
	 * @access protected
	 *
	 * @param int $id Post ID.
	 *
	 * @return false Disallow The updating CSS files for pages.
	 */
	protected function get_css_file_for_update( $id ) {
		return false;
	}

	/**
	 * Get saved settings.
	 *
	 * Retrieve the saved settings from the post meta.
	 *
	 * @since 1.6.0
	 * @access protected
	 *
	 * @param int $id Post ID.
	 *
	 * @return array Saved settings.
	 */
	protected function get_saved_settings( $id ) {
		$settings = get_post_meta( $id, self::META_KEY, true );

		if ( ! $settings ) {
			$settings = [];
		}

		if ( Utils::is_cpt_custom_templates_supported() ) {
			$saved_template = get_post_meta( $id, '_wp_page_template', true );

			if ( $saved_template ) {
				$settings['template'] = $saved_template;
			}
		}

		return $settings;
	}

	/**
	 * Get CSS file name.
	 *
	 * Retrieve CSS file name for the page settings manager.
	 *
	 * @since 1.6.0
	 * @access protected
	 *
	 * @return string CSS file name.
	 */
	protected function get_css_file_name() {
		return 'post';
	}

	/**
	 * Get model for CSS file.
	 *
	 * Retrieve the model for the CSS file.
	 *
	 * @since 1.6.0
	 * @access protected
	 *
	 * @param Base $css_file The requested CSS file.
	 *
	 * @return BaseModel The model object.
	 */
	protected function get_model_for_css_file( Base $css_file ) {
		if ( ! $css_file instanceof Post ) {
			return null;
		}

		$post_id = $css_file->get_post_id();

		if ( $css_file instanceof Post_Preview ) {
			$autosave = Utils::get_post_autosave( $post_id );
			if ( $autosave ) {
				$post_id = $autosave->ID;
			}
		}

		return $this->get_model( $post_id );
	}

	/**
	 * Get special settings names.
	 *
	 * Retrieve the names of the special settings that are not saved as regular
	 * settings. Those settings have a separate saving process.
	 *
	 * @since 1.6.0
	 * @access protected
	 *
	 * @return array Special settings names.
	 */
	protected function get_special_settings_names() {
		return [
			'id',
			'post_title',
			'post_status',
			'template',
			'post_excerpt',
			'post_featured_image',
			'menu_order',
			'comment_status',
		];
	}

	/**
	 * @since 2.0.0
	 * @access public
	 */
	public function save_post_status( $post_id, $status ) {
		$parent_id = wp_is_post_revision( $post_id );

		if ( $parent_id ) {
			// Don't update revisions post-status.
			return;
		}

		$parent_id = $post_id;

		$post = get_post( $parent_id );

		$allowed_post_statuses = get_post_statuses();

		if ( $this->is_contributor_user() && $this->has_invalid_post_status_for_contributor( $status ) ) {
			// If the status is not allowed, set it to 'pending' by default.
			$status = 'pending';
			$post->post_status = $status;
		}

		if ( isset( $allowed_post_statuses[ $status ] ) ) {
			$post_type_object = get_post_type_object( $post->post_type );
			if ( 'publish' !== $status || current_user_can( $post_type_object->cap->publish_posts ) ) {
				$post->post_status = $status;
			}
		}

		wp_update_post( $post );
	}

	private function is_contributor_user(): bool {
		return current_user_can( 'edit_posts' ) && ! current_user_can( 'publish_posts' );
	}

	private function has_invalid_post_status_for_contributor( $status ): bool {
		return 'draft' !== $status && 'pending' !== $status;
	}
}
