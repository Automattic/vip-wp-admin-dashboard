/**
 * External dependencies
 */
var React = require( 'react' ),
	Chart = require( 'chart.js' ),
	LineChart = require( 'react-chartjs' ).Line;
	// ReactInjection = require( 'react/lib/ReactInjection' );


/**
 * Internal dependencies
 */
var Main = require( './main' ),
	Header = require( './header' ),
	Stats = require( './stats' ),
	Stats_Numbers = require( './stats-numbers' ),
	Widget_Contact = require( './widget-contact' ),
	Widget_Welcome = require( './widget-welcome' );

/**
 * Settings
 */
Chart.defaults.global.responsive = true;

var VIPdashboard = React.createClass({
	getInitialState: function() {
		return {
			lineChartData: {
				labels : ["January","February","March","April","May","June","July"],
				datasets : [
					{
						label: "Posts",
						fillColor : "rgba(45,173,227,0.2)",
						strokeColor : "rgba(45,173,227,1)",
						pointColor : "rgba(220,220,220,1)",
						pointStrokeColor : "#fff",
						pointHighlightFill : "#fff",
						pointHighlightStroke : "rgba(220,220,220,1)",
						data : [10, 35, 28, 50, 20, 50, 42]
					},
					{
						label: "Comments",
						fillColor : "rgba(245,169,28,0.2)",
						strokeColor : "rgba(245,169,28,1)",
						pointColor : "rgba(151,187,205,1)",
						pointStrokeColor : "#fff",
						pointHighlightFill : "#fff",
						pointHighlightStroke : "rgba(151,187,205,1)",
						data : [16, 25, 22, 38, 46, 24, 50]
					}
				]
			}
		}
	},
	render: function() {
		return (
			<Main className="page-dashboard clearfix">

				<Header />

				<Stats>
					<div className="stats__module">
						<LineChart data={this.state.lineChartData} />
					</div>

					<div className="chart-circular__block chart-circular__two">

						<div className="chart-circular__data">
							<span className="chart-circular__value">57%</span>
							<span className="chart-circular__description">Mobile views</span>
							<span className="chart-circular__trend trend-positive">22%</span>
						</div>

					</div>

					<Stats_Numbers className="stats__total-loc" value={7632} trend={2} description="Total Published Posts" />
					<Stats_Numbers className="stats__total-loc" value={123} trend={0} description="Total Users" />
					<Stats_Numbers className="stats__total-loc" value={512} trend={2} description="Media Library (GB)" />
					<Stats_Numbers className="stats__total-loc" value={3759} trend={-5} description="Total Line of Code" />


				</Stats>

				<div className="widgets-area">

					<Widget_Welcome />

					<Widget_Contact />

					<Widget title="Third Widget" />

				</div>

			</Main>
		)
	}
});
React.render(<VIPdashboard />,
	document.getElementById('app')
);


/*var HelloUser = React.createClass({
	getInitialState: function(){
	return {
		username: '@tylermcginnis33'
	}
	},
	handleChange: function(e){
	this.setState({
	  username: e.target.value
	});
	},
	render: function(){
	return (
		<div>
			Hello {this.state.username} <br />
			Change Name: <input type="text" value={this.state.username} onChange={this.handleChange} />
		</div>
	)
	}
});

React.render(
	<HelloUser />,
	document.getElementById('app')
);*/