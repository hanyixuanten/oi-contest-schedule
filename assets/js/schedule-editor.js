( function ( blocks, blockEditor, components, element, ServerSideRender ) {
	'use strict';

	var createElement = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var RangeControl = components.RangeControl;
	var ToggleControl = components.ToggleControl;
	var strings = window.oicsScheduleEditorL10n;

	blocks.registerBlockType( 'oics/contest-schedule', {
		apiVersion: 2,
		title: strings.title,
		description: strings.description,
		icon: 'calendar-alt',
		category: 'widgets',
		attributes: {
			limit: {
				type: 'number',
				default: 10
			},
			compact: {
				type: 'boolean',
				default: false
			}
		},
		edit: function ( props ) {
			return createElement(
				element.Fragment,
				null,
				createElement(
					InspectorControls,
					null,
					createElement(
						PanelBody,
						{ title: strings.settings },
						createElement( RangeControl, {
							label: strings.limit,
							value: props.attributes.limit,
							min: 1,
							max: 50,
							onChange: function ( value ) {
								props.setAttributes( { limit: value } );
							}
						} ),
						createElement( ToggleControl, {
							label: strings.compact,
							checked: props.attributes.compact,
							onChange: function ( value ) {
								props.setAttributes( { compact: value } );
							}
						} )
					)
				),
				createElement( ServerSideRender, {
					block: 'oics/contest-schedule',
					attributes: props.attributes
				} )
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.serverSideRender );