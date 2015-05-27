/**
 * External dependencies
 */
var React = require( 'react' ),
	joinClasses = require( 'react/lib/joinClasses' ),

/**
 * Internal dependencies
 */
CounTo = require( '../count');

/**
 * Stats Number Component
 */
Stats_Numbers = React.createClass( {
	getInitialState: function() {
		return {
			value: this.props.value,
			trend: this.props.trend
		};
	},
	spin: function(e) {
		this.setState({
			value: (Math.floor(Math.random() * 10000) + 1),
			trend: Math.floor(Math.random() * 20) - 10
		});
	},
	render: function() {

		var trend = '';

		if ( this.state.trend > 0 ) {
			trend = 'trend-positive';
		} else if ( this.state.trend < 0 ) {
			trend = 'trend-negative';
		} else {
			trend = 'trend-neutral';
		}

		return (
			<div className={ joinClasses( this.props.className, 'stats_numbers' ) } onClick={this.spin}>
				<span className="numbers__value"><CountTo to={ this.state.value } from={0} speed={ 500 } /></span>
				<span className={ joinClasses( trend, 'numbers__trend' )}>{ this.state.trend + '%' }</span>
				<span className="numbers__description">{ this.props.description }</span>
			</div>
		);
	}
} );
module.exports = Stats_Numbers;