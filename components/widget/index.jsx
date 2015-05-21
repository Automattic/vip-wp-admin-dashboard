/**
 * External dependencies
 */
var React = require( 'react' ),
	joinClasses = require( 'react/lib/joinClasses' );

/**
 * Internal dependencies
 */

/**
 * Widget Component
 */
Widget = React.createClass( {
	render: function() {
		return (
			<div className={ joinClasses( this.props.className, 'widget' ) }>
				<h2 className="widget__title">{this.props.title}</h2>
				{ this.props.children }
			</div>
		);
	}
} );
module.exports = Widget;