/**
 * External dependencies
 */
var React = require( 'react' );

/**
 * Internal dependencies
 */
var Widget = require( '../widget' );

/**
 * Contact Widget Component
 */
Widget_Contact = React.createClass( {
	render: function() {
		return (
			<Widget className="widget__contact" title="Contact WordPress.com VIP Support">
				<p>Form components to go here</p>
			</Widget>
		);
	}
} );
module.exports = Widget_Contact;