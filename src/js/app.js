$(document).ready(function(){
	blank.init();
});

var blank = {
	
	settings: {
		var: 0,
		urlSite: '',
		gallery: '',
	},

	init: function() {
		urlSite = 'blank';
		this.bindActions();
		this.chartsInit();
	},
	
	bindActions: function() {
		
	},
	
	chartsAnim: function( selector, percent ) {
		var path = $( selector ).get(0);
		var pathLen = path.getTotalLength();
		var adjustedLen = ( 100 - percent ) * pathLen / 100;
		$( selector ).css('stroke-dashoffset', adjustedLen);
	},
	
	chartsInit: function() {
		this.chartsAnim( '.chart-views-desktop', 43 );
		this.chartsAnim( '.chart-views-mobile', 57 );
	},
	
}

	var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
	var lineChartData = {
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

window.onload = function(){
	var ctx = document.getElementById("canvas").getContext("2d");
	window.myLine = new Chart(ctx).Line(lineChartData, {
		responsive: false,
		showScale: false,
		scaleShowGridLines : false,
		scaleShowLabels: false,
		pointDot : false,
		datasetStrokeWidth : 3,
		scaleBeginAtZero: true,
		tooltipYPadding: 0,
		tooltipXPadding: 0,
	});
}