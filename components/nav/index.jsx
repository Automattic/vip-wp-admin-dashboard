/**
 * External dependencies
 */
var React = require( 'react' );

/**
 * Internal dependencies
 */
var Config = require( '../config.js' );

/**
 * Navigation component
 */
var Nav = React.createClass( {
	getInitialState: function(){
		return {
			focused: 0
		};
	},
	clicked: function( index ) {
		this.setState( { focused: index } );
	},
	render: function() {

		var self = this;
		var url = Config.adminurl;

		// loop over the array of menu entries,
		return (
			<div className="top-header__menu">
				<ul>{ this.props.items.map( function( m, index ) {

					var style = '';

					if ( self.state.focused == index ) {
						style = 'active';
					}

					if ( m.url.search( '.php' ) !== -1 ) {
						url = Config.adminurl.replace( 'admin.php', m.url );
					} else {
						url = Config.adminurl + '?page=' + m.url;
					}

					return <li key={index}>
						<a className={ style } href={ url } onClick={ self.clicked.bind(self, index) }>{ m.title }</a>
					</li>;

				}) }
				</ul>
			</div>
		);
	}
} );
module.exports = Nav;