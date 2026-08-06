( function () {
	'use strict';

	var strings = window.oicsScheduleL10n || {};

	function formatTime( timestamp, includeSeconds ) {
		var options = {
			year: 'numeric',
			month: '2-digit',
			day: '2-digit',
			hour: '2-digit',
			minute: '2-digit',
			hour12: false
		};

		if ( includeSeconds ) {
			options.second = '2-digit';
		}

		return new Intl.DateTimeFormat( document.documentElement.lang || undefined, options ).format( new Date( timestamp * 1000 ) );
	}

	function updateTimes() {
		document.querySelectorAll( '[data-oics-time]' ).forEach( function ( element ) {
			var timestamp = Number.parseInt( element.dataset.oicsTime, 10 );
			if ( timestamp ) {
				element.textContent = formatTime( timestamp, element.hasAttribute( 'data-oics-seconds' ) );
			}
		} );
	}

	function updateTimeZones() {
		var timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
		document.querySelectorAll( '[data-oics-time-zone]' ).forEach( function ( element ) {
			element.textContent = timeZone;
		} );
	}

	function countdownText( startTimestamp, endTimestamp ) {
		var now = Date.now();
		if ( endTimestamp * 1000 <= now ) {
			return strings.ended || 'Ended';
		}
		if ( startTimestamp * 1000 <= now ) {
			return strings.running || 'Running';
		}

		var remaining = startTimestamp * 1000 - now;
		var days = Math.floor( remaining / 86400000 );
		var hours = Math.floor( ( remaining % 86400000 ) / 3600000 );
		var minutes = Math.floor( ( remaining % 3600000 ) / 60000 );
		var seconds = Math.floor( ( remaining % 60000 ) / 1000 );

		return days + ( strings.day || 'd' ) + ' ' + hours + ( strings.hour || 'h' ) + ' ' + minutes + ( strings.minute || 'm' ) + ' ' + seconds + ( strings.second || 's' );
	}

	function updateCountdowns() {
		document.querySelectorAll( '[data-oics-start]' ).forEach( function ( element ) {
			var startTimestamp = Number.parseInt( element.dataset.oicsStart, 10 );
			var endTimestamp = Number.parseInt( element.dataset.oicsEnd, 10 );
			element.textContent = countdownText( startTimestamp, endTimestamp );
		} );
	}

	function initialize() {
		updateTimes();
		updateTimeZones();
		updateCountdowns();
		window.setInterval( updateCountdowns, 1000 );

		if ( 'MutationObserver' in window ) {
			new MutationObserver( function ( mutations ) {
				var hasAddedElements = mutations.some( function ( mutation ) {
					return Array.prototype.some.call( mutation.addedNodes, function ( node ) {
						return 1 === node.nodeType;
					} );
				} );

				if ( hasAddedElements ) {
					updateTimes();
					updateTimeZones();
					updateCountdowns();
				}
			} ).observe( document.body, { childList: true, subtree: true } );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initialize );
	} else {
		initialize();
	}
}() );