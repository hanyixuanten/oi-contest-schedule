<?php

defined( 'ABSPATH' ) || exit;

final class OICS_Contest_Client {
	const DATA_URL      = 'https://raw.githubusercontent.com/hanyixuanten/OI-contest-fetch/master/contests_all.json';
	const TRANSIENT_KEY = 'oics_contest_payload_v1';
	const CACHE_TTL     = 5 * MINUTE_IN_SECONDS;

	public function get_payload() {
		$cached = get_transient( self::TRANSIENT_KEY );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$response = wp_safe_remote_get(
			apply_filters( 'oics_contest_data_url', self::DATA_URL ),
			array(
				'timeout'    => 10,
				'user-agent' => 'OI Contest Schedule/' . OICS_VERSION . '; ' . home_url( '/' ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'oics_http_error', __( 'The contest data source returned an unexpected response.', 'oi-contest-schedule' ) );
		}

		$payload = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! $this->is_valid_payload( $payload ) ) {
			return new WP_Error( 'oics_invalid_payload', __( 'The contest data source returned invalid data.', 'oi-contest-schedule' ) );
		}

		$normalized = array(
			'generated_at' => isset( $payload['generated_at'] ) ? absint( $payload['generated_at'] ) : 0,
			'contests'     => $this->normalize_contests( $payload['contests'] ),
		);

		set_transient( self::TRANSIENT_KEY, $normalized, self::CACHE_TTL );

		return $normalized;
	}

	private function is_valid_payload( $payload ) {
		return is_array( $payload ) && isset( $payload['contests'] ) && is_array( $payload['contests'] );
	}

	private function normalize_contests( $contests ) {
		$normalized = array();
		$now        = time();

		foreach ( $contests as $contest ) {
			if ( ! is_array( $contest ) ) {
				continue;
			}

			$start_time = isset( $contest['start_time'] ) ? absint( $contest['start_time'] ) : 0;
			$end_time   = isset( $contest['end_time'] ) ? absint( $contest['end_time'] ) : 0;
			$url        = isset( $contest['url'] ) ? esc_url_raw( $contest['url'] ) : '';
			if ( ! $start_time || ! $end_time || $end_time <= $now || ! $url ) {
				continue;
			}

			$normalized[] = array(
				'platform'   => isset( $contest['platform'] ) ? sanitize_text_field( $contest['platform'] ) : '',
				'title'      => isset( $contest['title'] ) ? sanitize_text_field( $contest['title'] ) : '',
				'start_time' => $start_time,
				'end_time'   => $end_time,
				'status'     => $start_time <= $now ? 'running' : 'upcoming',
				'url'        => $url,
			);
		}

		usort(
			$normalized,
			static function ( $left, $right ) {
				return $left['start_time'] <=> $right['start_time'];
			}
		);

		return $normalized;
	}
}