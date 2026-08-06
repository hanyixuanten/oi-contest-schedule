<?php

defined( 'ABSPATH' ) || exit;

final class OICS_Plugin {
	private static $instance;
	private $renderer;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->renderer = new OICS_Schedule_Renderer( new OICS_Contest_Client() );

		add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_shortcode( 'oi_contest_schedule', array( $this, 'render_shortcode' ) );
	}

	private function register_assets() {
		wp_register_style( 'oics-schedule', OICS_URL . 'assets/css/schedule.css', array(), OICS_VERSION );
		wp_register_script( 'oics-schedule', OICS_URL . 'assets/js/schedule.js', array(), OICS_VERSION, true );
		wp_localize_script(
			'oics-schedule',
			'oicsScheduleL10n',
			array(
				'running' => __( 'Running', 'oi-contest-schedule' ),
				'ended'   => __( 'Ended', 'oi-contest-schedule' ),
				'day'     => __( 'd', 'oi-contest-schedule' ),
				'hour'    => __( 'h', 'oi-contest-schedule' ),
				'minute'  => __( 'm', 'oi-contest-schedule' ),
				'second'  => __( 's', 'oi-contest-schedule' ),
			)
		);
	}

	public function enqueue_frontend_assets() {
		$post = get_post();
		if ( ! $post || ! has_shortcode( $post->post_content, 'oi_contest_schedule' ) ) {
			return;
		}

		$this->enqueue_assets();
	}

	public function enqueue_admin_assets( $hook_suffix ) {
		if ( 'index.php' !== $hook_suffix ) {
			return;
		}

		$this->enqueue_assets();
	}

	private function enqueue_assets() {
		$this->register_assets();
		wp_enqueue_style( 'oics-schedule' );
		wp_enqueue_script( 'oics-schedule' );
	}

	public function register_dashboard_widget() {
		wp_add_dashboard_widget(
			'oics_contest_schedule',
			esc_html__( 'Upcoming OI Contests', 'oi-contest-schedule' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	public function render_dashboard_widget() {
		echo $this->renderer->render( 8, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function render_shortcode( $attributes ) {
		$attributes = shortcode_atts(
			array(
				'limit'   => 10,
				'compact' => 'false',
			),
			$attributes,
			'oi_contest_schedule'
		);

		$limit   = min( 50, max( 1, absint( $attributes['limit'] ) ) );
		$compact = filter_var( $attributes['compact'], FILTER_VALIDATE_BOOLEAN );

		return $this->renderer->render( $limit, $compact );
	}
}