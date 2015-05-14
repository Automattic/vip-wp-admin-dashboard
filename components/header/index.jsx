/**
 * External dependencies
 */
var React = require( 'react' ),
	joinClasses = require( 'react/lib/joinClasses' );

/**
 * Internal dependencies
 */
var Config = require( '../config.js' ),
	Nav = require( '../nav' ),

/**
 * Header Component
 */
Header = React.createClass( {
	render: function() {
		return (
			<div className={ joinClasses( this.props.className, 'top-header' ) }>
				<h1><img src={ Config.asseturl + "img/wpcom-vip-logo.svg"} alt="WordPress.com VIP" className="top-header__logo" /></h1>

				<Nav items={ ['Dashboard', 'User Management', 'SVN Access', 'Revisions', 'Plugins', 'Support', 'Billing'] } />,

				{ this.props.children }

			</div>
		);
	}
} );
module.exports = Header;