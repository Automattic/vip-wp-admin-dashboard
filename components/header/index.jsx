/**
 * External dependencies
 */
var React = require( 'react' ),
	joinClasses = require( 'react/lib/joinClasses' );

/**
 * Internal dependencies
 */

/**
 * Header Component
 */
Header = React.createClass( {
	render: function() {
		return (
			<div className={ joinClasses( this.props.className, 'top-header' ) }>
				<h1><img src="assets/img/wpcom-vip-logo.svg" alt="WordPress.com VIP" className="top-header__logo" /></h1>
				<div className="top-header__menu">
					<p>Swap this for nav component</p>
					<ul>
						<li><a className="active" href="#">Dashboard</a></li>
						<li><a href="#">User Management</a></li>
						<li><a href="#">SVN Access</a></li>
						<li><a href="#">Revisions</a></li>
						<li><a href="#">Plugins</a></li>
						<li><a href="#">Support</a></li>
						<li><a href="#">Billing</a></li>
					</ul>
				</div>
			</div>
		);
	}
} );
module.exports = Header;