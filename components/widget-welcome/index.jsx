/**
 * External dependencies
 */
var React = require( 'react' );

/**
 * Internal dependencies
 */
var Widget = require( '../widget' );

/**
 * Welcome Widget Component
 */
Widget_Welcome = React.createClass( {
	render: function() {
		return (
			<Widget className="widget__welcome" title="Welcome to WordPress.com VIP">
				<p>We believe that WordPress.com VIP is a partnership between WordPress.com and some of the most high-profile, innovative and smart WordPress websites out there. We’re excited to have you here.</p>

				<h3 className="widget__subtitle">Helpful Links</h3>

				<div className="widget__col-2">
					<ul className="widget__list">
						<li>
							<a href="">VIP Lobby</a>
							<span>Important service updates</span>
						</li>
						<li>
							<a href="">VIP Documentation</a>
							<span>Coding for WordPress.com VIP</span>
						</li>
						<li>
							<a href="">VIP Plugins</a>
							<span>Available shared VIP plugins</span>
						</li>
						<li>
							<a href="">VIP Support Portal</a>
							<span>Your organization’s tickets</span>
						</li>
					</ul>
				</div>

				<div className="widget__col-2">
					<ul className="widget__list">
						<li>
							<a href="">Launch Checklist</a>
							<span>Steps to launch</span>
						</li>
						<li>
							<a href="">Your VIP Toolbox</a>
							<span>Navigating VIP Tools</span>
						</li>
						<li>
							<a href="">VIP News</a>
							<span>New features, case studies</span>
						</li>
						<li>
							<a href="">Featured Partners</a>
							<span>Agencies and technology partners</span>
						</li>
					</ul>
				</div>
			</Widget>
		);
	}
} );
module.exports = Widget_Welcome;