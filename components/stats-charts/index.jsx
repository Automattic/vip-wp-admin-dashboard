/**
 * External dependencies
 */
var React = require( 'react' ),

/**
 * Internal dependencies
 */

/**
 * Stats Charts Component
 */
Stats_Charts = React.createClass( {

	render: function() {
		return (
			<div className="stats__module stats__graphs">
			
				<div className="chart-circular__block chart-circular__one">

					<div className="chart-circular__data">
						<span className="numbers__value value-primary value-chart">43%</span>
						<span className="numbers__description">Desktop views</span>
						<span className="numbers__trend trend-negative trend-center">16%</span>
					</div>
		
					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 159.6 159.6" enable-background="new 0 0 159.6 159.6">
						<path className="chart-graph-dummy" fill="none" stroke="#d8e2e9" strokeWidth="6" d="M79.8,3c42.4,0,76.8,34.4,76.8,76.8 s-34.4,76.8-76.8,76.8S3,122.2,3,79.8S37.4,3,79.8,3"/>
						<path className="chart-graph chart-views-desktop" id="chart-views-desktop" fill="none" stroke="#d8e2e9" strokeWidth="6" d="M79.8,3c42.4,0,76.8,34.4,76.8,76.8 s-34.4,76.8-76.8,76.8S3,122.2,3,79.8S37.4,3,79.8,3"/>
					</svg>

				</div>
			
				<div className="chart-circular__block chart-circular__two">
			
					<div className="chart-circular__data">
						<span className="numbers__value value-secondary value-chart">57%</span>
						<span className="numbers__description">Mobile views</span>
						<span className="numbers__trend trend-positive trend-center">22%</span>
					</div>
		
					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 159.6 159.6" enable-background="new 0 0 159.6 159.6">
						<path className="chart-graph-dummy" fill="none" stroke="#d8e2e9" strokeWidth="6" d="M79.8,3c42.4,0,76.8,34.4,76.8,76.8 s-34.4,76.8-76.8,76.8S3,122.2,3,79.8S37.4,3,79.8,3"/>
						<path className="chart-graph chart-views-mobile" id="chart-views-mobile" fill="none" stroke="#d8e2e9" strokeWidth="6" d="M79.8,3c42.4,0,76.8,34.4,76.8,76.8 s-34.4,76.8-76.8,76.8S3,122.2,3,79.8S37.4,3,79.8,3"/>
					</svg>
			
				</div>
					
				<div className="stats__numbers">
					<Stats_Numbers className="stats__visitors" value={3734} trend={16} description="Visitors" />
					<Stats_Numbers className="stats__views" value={12158} trend={3} description="Views" />
				</div>
					
			</div>
		);
	}
} );
module.exports = Stats_Charts;