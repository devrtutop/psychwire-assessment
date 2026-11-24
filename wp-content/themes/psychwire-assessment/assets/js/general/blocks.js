( function ( blocks, element, blockEditor, components ) {
	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	var RichText = blockEditor.RichText;
	var MediaUpload = blockEditor.MediaUpload;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var Button = components.Button;
	var TextControl = components.TextControl;
	var copy = ( window.PsychwireAssessment && PsychwireAssessment.copy ) || {};

	function field( tag, className, value, onChange ) {
		return el( RichText, {
			tagName: tag,
			className: className,
			value: value || '',
			allowedFormats: [],
			onChange: onChange
		} );
	}

	function replaceAt( list, index, key, value ) {
		return list.map( function ( item, itemIndex ) {
			if ( itemIndex !== index ) {
				return item;
			}
			var next = Object.assign( {}, item );
			next[ key ] = value;
			return next;
		} );
	}

	function removeAt( list, index ) {
		return list.filter( function ( item, itemIndex ) {
			return itemIndex !== index;
		} );
	}

	blocks.registerBlockType( 'psychwire-assessment/intro', {
		apiVersion: 3,
		title: 'Introduction',
		description: 'Name, title, biography, portrait, and training stat.',
		category: 'theme',
		icon: 'id',
		attributes: {
			eyebrow: { type: 'string', default: copy.eyebrow },
			name: { type: 'string', default: copy.name },
			jobTitle: { type: 'string', default: copy.jobTitle },
			bio: { type: 'string', default: copy.bio },
			buttonText: { type: 'string', default: copy.buttonText },
			buttons: {
				type: 'array',
				default: [ { text: copy.buttonText || 'Explore courses', url: '#courses' } ]
			},
			imageId: { type: 'number', default: 0 },
			imageUrl: { type: 'string', default: '' },
			statValue: { type: 'string', default: copy.statValue },
			statLabel: { type: 'string', default: copy.statLabel },
			imageAlt: { type: 'string', default: copy.imageAlt }
		},
		edit: function ( props ) {
			var attributes = props.attributes;
			var set = props.setAttributes;
			var buttons = attributes.buttons && attributes.buttons.length
				? attributes.buttons
				: [ { text: attributes.buttonText || 'Explore courses', url: '#courses' } ];

			function updateButton( index, key, value ) {
				set( { buttons: replaceAt( buttons, index, key, value ) } );
			}

			return el( element.Fragment, {},
				el( InspectorControls, {},
					el( PanelBody, { title: 'Portrait', initialOpen: true },
						el( MediaUpload, {
							onSelect: function ( media ) {
								set( {
									imageId: media.id,
									imageUrl: media.url,
									imageAlt: media.alt || attributes.imageAlt
								} );
							},
							allowedTypes: [ 'image' ],
							value: attributes.imageId || 0,
							render: function ( media ) {
								return el( Button, {
									variant: 'secondary',
									onClick: media.open
								}, attributes.imageId ? 'Replace image' : 'Select image' );
							}
						} )
					),
					el( PanelBody, { title: 'Buttons', initialOpen: true },
						buttons.map( function ( button, index ) {
							return el( 'div', { key: 'button-' + index },
								el( TextControl, {
									label: 'Button ' + ( index + 1 ) + ' label',
									value: button.text || '',
									onChange: function ( value ) { updateButton( index, 'text', value ); }
								} ),
								el( TextControl, {
									label: 'Button ' + ( index + 1 ) + ' link',
									value: button.url || '',
									onChange: function ( value ) { updateButton( index, 'url', value ); }
								} ),
								buttons.length > 1 ? el( Button, {
									variant: 'link',
									isDestructive: true,
									onClick: function () {
										set( { buttons: removeAt( buttons, index ) } );
									}
								}, 'Remove button' ) : null
							);
						} ),
						el( Button, {
							variant: 'secondary',
							onClick: function () {
								set( { buttons: buttons.concat( [ { text: 'New button', url: '#' } ] ) } );
							}
						}, 'Add button' )
					)
				),
				el( 'section', useBlockProps( { className: 'intro' } ),
					el( 'div', {
						className: 'intro__inner',
						style: { gridTemplateColumns: 'minmax(0, 1fr) 220px', alignItems: 'start' }
					},
						el( 'div', { className: 'intro__copy' },
							field( 'p', 'eyebrow', attributes.eyebrow, function ( value ) { set( { eyebrow: value } ); } ),
							field( 'h1', '', attributes.name, function ( value ) { set( { name: value } ); } ),
							field( 'p', 'intro__role', attributes.jobTitle, function ( value ) { set( { jobTitle: value } ); } ),
							field( 'p', 'intro__bio', attributes.bio, function ( value ) { set( { bio: value } ); } ),
							el( 'div', { className: 'intro__buttons' },
								buttons.map( function ( button, index ) {
									return el( 'span', { className: 'button', key: 'button-preview-' + index },
										field( 'span', '', button.text, function ( value ) { updateButton( index, 'text', value ); } )
									);
								} )
							)
						),
						el( 'div', { className: 'intro__media' },
							el( 'img', {
								className: 'intro__photo',
								src: attributes.imageUrl || ( window.PsychwireAssessment && PsychwireAssessment.portrait ) || '',
								alt: attributes.imageAlt || '',
								style: { width: '100%', height: '260px', objectFit: 'cover', objectPosition: 'center 30%' }
							} ),
							el( 'p', { className: 'intro__stat' },
								field( 'strong', '', attributes.statValue, function ( value ) { set( { statValue: value } ); } ),
								field( 'span', '', attributes.statLabel, function ( value ) { set( { statLabel: value } ); } )
							)
						)
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );

	blocks.registerBlockType( 'psychwire-assessment/course-list', {
		apiVersion: 3,
		title: 'Course list',
		description: 'Courses, rendered on the server.',
		category: 'theme',
		icon: 'list-view',
		attributes: {
			heading: { type: 'string', default: copy.coursesHeading },
			subheading: { type: 'string', default: copy.subheading },
			moreText: { type: 'string', default: copy.moreText },
			courses: { type: 'array', default: copy.courses || [] }
		},
		edit: function ( props ) {
			var attributes = props.attributes;
			var courses = attributes.courses || [];

			function updateCourse( index, key, value ) {
				props.setAttributes( { courses: replaceAt( courses, index, key, value ) } );
			}

			function addCourse() {
				var number = String( courses.length + 1 );
				props.setAttributes( {
					courses: courses.concat( [ {
						number: number.length < 2 ? '0' + number : number,
						name: 'New course',
						description: '',
						length: '',
						url: '#',
						linkLabel: 'View course'
					} ] )
				} );
			}

			return el( element.Fragment, {},
				el( InspectorControls, {},
					el( PanelBody, { title: 'Courses', initialOpen: true },
						courses.map( function ( course, index ) {
							return el( 'div', { key: 'course-settings-' + index },
								el( TextControl, {
									label: 'Course ' + ( index + 1 ) + ' link',
									value: course.url || '',
									onChange: function ( value ) { updateCourse( index, 'url', value ); }
								} ),
								courses.length > 1 ? el( Button, {
									variant: 'link',
									isDestructive: true,
									onClick: function () {
										props.setAttributes( { courses: removeAt( courses, index ) } );
									}
								}, 'Remove course ' + ( index + 1 ) ) : null
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addCourse }, 'Add course' )
					)
				),
				el( 'section', useBlockProps( { className: 'courses' } ),
					el( 'div', { className: 'courses__inner' },
						el( 'header', { className: 'courses__heading' },
							field( 'h2', '', attributes.heading, function ( value ) { props.setAttributes( { heading: value } ); } ),
							field( 'p', '', attributes.subheading, function ( value ) { props.setAttributes( { subheading: value } ); } )
						),
						el( 'ol', { className: 'course-list' },
							courses.map( function ( course, index ) {
								return el( 'li', { key: 'course-' + index },
									el( 'article', { className: 'course-card' },
										el( 'p', { className: 'course-card__meta' },
											field( 'span', 'course-card__number', course.number, function ( value ) { updateCourse( index, 'number', value ); } ),
											field( 'span', '', course.length, function ( value ) { updateCourse( index, 'length', value ); } )
										),
										field( 'h3', '', course.name, function ( value ) { updateCourse( index, 'name', value ); } ),
										field( 'p', 'course-card__summary', course.description, function ( value ) { updateCourse( index, 'description', value ); } ),
										field( 'span', 'course-card__link', course.linkLabel, function ( value ) { updateCourse( index, 'linkLabel', value ); } )
									)
								);
							} )
						),
						el( 'p', { className: 'courses__more' },
							el( 'span', { className: 'button button--compact' },
								field( 'span', '', attributes.moreText, function ( value ) { props.setAttributes( { moreText: value } ); } )
							)
						)
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );

	blocks.registerBlockType( 'psychwire-assessment/contact', {
		apiVersion: 3,
		title: 'Contact',
		description: 'Contact heading and email.',
		category: 'theme',
		icon: 'email',
		attributes: {
			heading: { type: 'string', default: copy.contactHeading },
			location: { type: 'string', default: copy.location },
			email: { type: 'string', default: copy.email }
		},
		edit: function ( props ) {
			var attributes = props.attributes;
			var set = props.setAttributes;

			return el( 'section', useBlockProps( { className: 'contact' } ),
				el( 'div', { className: 'contact__inner' },
					el( 'div', { className: 'contact__copy' },
						field( 'h2', '', attributes.heading, function ( value ) { set( { heading: value } ); } ),
						field( 'p', '', attributes.location, function ( value ) { set( { location: value } ); } )
					),
					el( 'p', { className: 'contact__email' },
						field( 'span', '', attributes.email, function ( value ) { set( { email: value } ); } )
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components );
