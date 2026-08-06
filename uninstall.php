<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_transient( 'oics_contest_payload_v1' );