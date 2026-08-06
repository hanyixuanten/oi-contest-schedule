<?php

defined( 'ABSPATH' ) || exit;

final class OICS_Schedule_Renderer {
	private $client;

	public function __construct( OICS_Contest_Client $client ) {
		$this->client = $client;
	}

	public function render( $limit = 10, $compact = false ) {
		$payload = $this->client->get_payload();
		if ( is_wp_error( $payload ) ) {
			return $this->render_notice( __( 'Contest data is temporarily unavailable. Please try again later.', 'oi-contest-schedule' ), 'error' );
		}

		$contests = array_slice( $payload['contests'], 0, $limit );
		if ( empty( $contests ) ) {
			return $this->render_notice( __( 'No upcoming contests.', 'oi-contest-schedule' ), 'empty' );
		}

		$classes = 'oics-schedule';
		if ( $compact ) {
			$classes .= ' oics-schedule--compact';
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $classes ); ?>" data-oics-schedule>
			<div class="oics-schedule__header">
				<div>
					<h2 class="oics-schedule__heading"><?php esc_html_e( 'Upcoming OI Contests', 'oi-contest-schedule' ); ?></h2>
					<p class="oics-schedule__meta">
						<?php esc_html_e( 'Times use your device time zone.', 'oi-contest-schedule' ); ?>
					</p>
				</div>
				<span class="oics-schedule__count"><?php echo esc_html( sprintf( _n( '%d contest', '%d contests', count( $contests ), 'oi-contest-schedule' ), count( $contests ) ) ); ?></span>
			</div>
			<div class="oics-schedule__list">
				<?php foreach ( $contests as $contest ) : ?>
					<?php $this->render_contest( $contest ); ?>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $payload['generated_at'] ) ) : ?>
				<p class="oics-schedule__updated">
					<?php esc_html_e( 'Data updated', 'oi-contest-schedule' ); ?>:
					<time data-oics-time="<?php echo esc_attr( $payload['generated_at'] ); ?>" data-oics-seconds></time>
				</p>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	private function render_contest( $contest ) {
		$platform_class = sanitize_html_class( strtolower( $contest['platform'] ) );
		?>
		<a class="oics-contest" href="<?php echo esc_url( $contest['url'] ); ?>" target="_blank" rel="noopener noreferrer">
			<span class="oics-contest__platform oics-contest__platform--<?php echo esc_attr( $platform_class ); ?>"><?php echo esc_html( $contest['platform'] ); ?></span>
			<span class="oics-contest__info">
				<strong class="oics-contest__title"><?php echo esc_html( $contest['title'] ); ?></strong>
				<span class="oics-contest__time">
					<time data-oics-time="<?php echo esc_attr( $contest['start_time'] ); ?>"></time>
					<span aria-hidden="true">–</span>
					<time data-oics-time="<?php echo esc_attr( $contest['end_time'] ); ?>"></time>
				</span>
			</span>
			<span class="oics-contest__countdown" data-oics-start="<?php echo esc_attr( $contest['start_time'] ); ?>" data-oics-end="<?php echo esc_attr( $contest['end_time'] ); ?>">
				<?php echo 'running' === $contest['status'] ? esc_html__( 'Running', 'oi-contest-schedule' ) : esc_html__( 'Upcoming', 'oi-contest-schedule' ); ?>
			</span>
		</a>
		<?php
	}

	private function render_notice( $message, $type ) {
		return sprintf(
			'<div class="oics-schedule"><p class="oics-schedule__notice oics-schedule__notice--%1$s">%2$s</p></div>',
			esc_attr( $type ),
			esc_html( $message )
		);
	}
}