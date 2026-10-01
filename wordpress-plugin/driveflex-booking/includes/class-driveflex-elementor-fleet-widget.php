<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

final class DriveFlex_Elementor_Fleet_Widget extends Widget_Base {
	public function get_name(): string { return 'driveflex-fleet'; }
	public function get_title(): string { return __( 'DriveFlex Fleet', 'driveflex-booking' ); }
	public function get_icon(): string { return 'eicon-car'; }
	public function get_categories(): array { return array( 'driveflex' ); }
	public function get_keywords(): array { return array( 'driveflex', 'fleet', 'cars', 'vehicles', 'booking', 'rental' ); }

	protected function register_controls(): void {
		$this->start_controls_section( 'fleet_help', array( 'label' => __( 'Fleet content', 'driveflex-booking' ) ) );
		$count = (int) wp_count_posts( 'driveflex_vehicle' )->publish;
		$this->add_control( 'fleet_information', array(
			'type' => Controls_Manager::RAW_HTML,
			'raw' => sprintf(
				'<strong>%1$d published vehicles</strong><p>This widget displays every active vehicle from DriveFlex Vehicles. Add or edit vehicles in WordPress; the page updates automatically.</p><p><a class="elementor-button elementor-button-default" href="%2$s" target="_blank">Manage vehicles</a> <a class="elementor-button elementor-button-default" href="%3$s" target="_blank">Import starter fleet</a></p>',
				$count,
				esc_url( admin_url( 'edit.php?post_type=driveflex_vehicle' ) ),
				esc_url( admin_url( 'edit.php?post_type=driveflex_vehicle&page=driveflex-settings#driveflex-starter-fleet' ) )
			),
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
		) );
		$this->end_controls_section();
	}

	protected function render(): void {
		echo do_shortcode( '[driveflex_fleet]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
