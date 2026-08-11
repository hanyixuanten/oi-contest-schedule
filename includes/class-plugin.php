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

		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_shortcode( 'oics_oi_contest_schedule', array( $this, 'render_shortcode' ) );
	}

	public function register_block() {
		$this->register_assets();
		wp_register_script(
			'oics-schedule-editor',
			OICS_URL . 'assets/js/schedule-editor.js',
			array( 'oics-schedule', 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-server-side-render' ),
			OICS_VERSION,
			true
		);
		wp_localize_script(
			'oics-schedule-editor',
			'oicsScheduleEditorL10n',
			array(
				'title'       => __( 'vblg OI Contest Schedule', 'vblg-oi-contest-schedule' ),
				'description' => __( 'Display upcoming OI contests with local times and live countdowns.', 'vblg-oi-contest-schedule' ),
				'settings'    => __( 'Schedule settings', 'vblg-oi-contest-schedule' ),
				'limit'       => __( 'Number of contests', 'vblg-oi-contest-schedule' ),
				'compact'     => __( 'Compact layout', 'vblg-oi-contest-schedule' ),
			)
		);

		register_block_type(
			'oics/contest-schedule',
			array(
				'api_version'     => 2,
				'editor_script'   => 'oics-schedule-editor',
				'editor_style'    => 'oics-schedule',
				'style'           => 'oics-schedule',
				'attributes'      => array(
					'limit'   => array(
						'type'    => 'number',
						'default' => 10,
					),
					'compact' => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	private function register_assets() {
		$style_version  = OICS_VERSION . '.' . filemtime( OICS_PATH . 'assets/css/schedule.css' );
		$script_version = OICS_VERSION . '.' . filemtime( OICS_PATH . 'assets/js/schedule.js' );

		wp_register_style( 'oics-schedule', OICS_URL . 'assets/css/schedule.css', array(), $style_version );
		wp_register_script( 'oics-schedule', OICS_URL . 'assets/js/schedule.js', array(), $script_version, true );
		wp_localize_script(
			'oics-schedule',
			'oicsScheduleL10n',
			array(
				'running' => __( 'Running', 'vblg-oi-contest-schedule' ),
				'ended'   => __( 'Ended', 'vblg-oi-contest-schedule' ),
				'day'     => __( 'd', 'vblg-oi-contest-schedule' ),
				'hour'    => __( 'h', 'vblg-oi-contest-schedule' ),
				'minute'  => __( 'm', 'vblg-oi-contest-schedule' ),
				'second'  => __( 's', 'vblg-oi-contest-schedule' ),
			)
		);
	}

	public function enqueue_frontend_assets() {
		$post = get_post();
		if ( ! $post || ( ! has_shortcode( $post->post_content, 'oics_oi_contest_schedule' ) && ! has_block( 'oics/contest-schedule', $post ) ) ) {
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
			esc_html__( 'Upcoming OI Contests', 'vblg-oi-contest-schedule' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	public function render_dashboard_widget() {
		echo $this->renderer->render( 8 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

	public function render_block( $attributes ) {
		$limit   = isset( $attributes['limit'] ) ? min( 50, max( 1, absint( $attributes['limit'] ) ) ) : 10;
		$compact = ! empty( $attributes['compact'] );

		return $this->renderer->render( $limit, $compact );
	}
}