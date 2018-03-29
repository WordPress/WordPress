<?php


class ET_Builder_Module_Settings_Migration_BackgroundUI extends ET_Builder_Module_Settings_Migration {

	public $version = '3.0.48';

	public function get_fields() {
		return array(
			'background_image'    => array(
				'affected_fields' => array(
					'background_url' => array( 'et_pb_fullwidth_header' ),
				),
			),
			'background_position' => array(
				'affected_fields' => array(
					'background_position' => $this->get_modules( true ),
				),
				'defaults'        => array(
					'new' => 'center', // just fyi
					'old' => 'top_left',
				),
			),
			'background_repeat'   => array(
				'affected_fields' => array(
					'background_repeat' => $this->get_modules( true ),
				),
				'defaults'        => array(
					'new' => 'no-repeat', // just fyi
					'old' => 'repeat',
				),
			),
			'background_size'     => array(
				'affected_fields' => array(
					'background_size' => $this->get_modules( true ),
				),
				'defaults'        => array(
					'new' => 'cover', // just fyi
					'old' => 'initial',
				),
			),
		);
	}

	public function get_modules( $for_affected_fields = false ) {
		$modules = array(
			'et_pb_audio',
			'et_pb_blurb',
			'et_pb_countdown_timer',
			'et_pb_cta',
			'et_pb_filterable_portfolio',
			'et_pb_fullwidth_portfolio',
			'et_pb_number_counter',
			'et_pb_team_member',
			'et_pb_portfolio',
			'et_pb_row',
			'et_pb_tab',
			'et_pb_tabs',
			'et_pb_testimonial',
			'et_pb_text',
			'et_pb_toggle',
		);

		if ( ! $for_affected_fields ) {
			$modules[] = 'et_pb_fullwidth_header';
		}

		return $modules;
	}

	public function migrate( $field_name, $current_value, $module_slug ) {
		if ( '' !== $current_value || 'background_image' === $field_name ) {
			return $current_value;
		}

		return $this->fields[ $field_name ]['defaults']['old'];
	}
}

return new ET_Builder_Module_Settings_Migration_BackgroundUI();
