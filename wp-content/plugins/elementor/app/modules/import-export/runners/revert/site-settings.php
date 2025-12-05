<?php

namespace Elementor\App\Modules\ImportExport\Runners\Revert;

use Elementor\Plugin;
use Elementor\Core\Experiments\Manager as ExperimentsManager;

class Site_Settings extends Revert_Runner_Base {

	public static function get_name(): string {
		return 'site-settings';
	}

	public function should_revert( array $data ): bool {
		return (
			isset( $data['runners'] ) &&
			array_key_exists( static::get_name(), $data['runners'] )
		);
	}

	public function revert( array $data ) {
		Plugin::$instance->kits_manager->revert(
			$data['runners'][ static::get_name() ]['imported_kit_id'],
			$data['runners'][ static::get_name() ]['active_kit_id'],
			$data['runners'][ static::get_name() ]['previous_kit_id']
		);

		$this->revert_theme( $data );
		$this->revert_experiments( $data );
	}

	public function get_theme_upgrader(): \Theme_Upgrader {
		if ( ! class_exists( '\Theme_Upgrader' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		if ( ! class_exists( '\WP_Ajax_Upgrader_Skin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		}

		return new \Theme_Upgrader( new \WP_Ajax_Upgrader_Skin() );
	}

	protected function revert_theme( $data ) {
		$installed_theme = $data['runners'][ static::get_name() ]['installed_theme'];
		$activated_theme = $data['runners'][ static::get_name() ]['activated_theme'];
		$previous_active_theme = $data['runners'][ static::get_name() ]['previous_active_theme'];

		if ( empty( $installed_theme ) && empty( $activated_theme ) ) {
			// no need to remove a theme as it was used before import
			return;
		}

		if ( ! empty( $activated_theme ) ) {
			$previous_theme = wp_get_theme( $previous_active_theme['slug'] );

			// no need to remove imported theme as it existed before import
			$this->activate_previous_theme( $previous_active_theme );
			return;
		}

		if ( ! empty( $installed_theme ) ) {
			$this->activate_previous_theme( $previous_active_theme );
			$this->delete_theme( $installed_theme );
		}
	}

	protected function should_delete_theme( $theme_slug ): bool {
		$current_theme = wp_get_theme();

		return $theme_slug !== $current_theme->get_stylesheet() && wp_get_theme( $theme_slug )->exists();
	}

	protected function delete_theme( $theme_slug ): bool {
		return delete_theme( $theme_slug );
	}

	protected function activate_previous_theme( $previous_active_theme ) {
		if ( ! $previous_active_theme ) {
			return;
		}

		$theme = wp_get_theme( $previous_active_theme['slug'] );

		if ( $theme->exists() ) {
			switch_theme( $theme->get_stylesheet() );
			return;
		}

		$download_url = "https://downloads.wordpress.org/theme/{$previous_active_theme['slug']}.{$previous_active_theme['version']}.zip";
		$install = $this->get_theme_upgrader()->install( $download_url );

		if ( ! $install || is_wp_error( $install ) ) {
			return;
		}

		switch_theme( $previous_active_theme['slug'] );
	}

	protected function revert_experiments( array $data ) {
		$runner_data = $data['runners'][ static::get_name() ];
		$previous_experiments = $runner_data['previous_experiments'] ?? [];

		if ( empty( $previous_experiments ) ) {
			return;
		}

		$experiments_manager = Plugin::$instance->experiments;
		$current_features = $experiments_manager->get_features();

		foreach ( $previous_experiments as $feature_name => $feature_data ) {
			if ( ! isset( $current_features[ $feature_name ] ) ) {
				continue;
			}

			if ( ! array_key_exists( $feature_name, $previous_experiments ) ) {
				continue;
			}

			$option_key = $experiments_manager->get_feature_option_key( $feature_name );
			$previous_state = $feature_data['state'];

			if ( ExperimentsManager::STATE_DEFAULT === $previous_state ) {
				delete_option( $option_key );
			} else {
				update_option( $option_key, $previous_state );
			}
		}
	}
}
