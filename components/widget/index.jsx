/**
 * External dependencies
 */
var React = require( 'react' );

/**
 * Internal dependencies
 */


/**
 *
 */
Widget = React.createClass( {
	componentWillMount: function() {},
	componentDidMount: function() {},
	componentWillUnmount: function() {},

	render: function() {
		return (
			<div className="widget widget__welcome">
				<h2 class="widget__title">{this.props.title}</h2>
				
			</div>
		);
	}
} );

module.exports = Widget;