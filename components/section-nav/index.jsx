/**
 * External Dependencies
 */
var React = require( 'react/addons' );
	//isEqual = require( 'lodash/lang/isEqual' );

/**
 * Internal Dependencies
 */
var NavTabs = require( './tabs' );
	//Search = require( 'components/search' );

/**
 * Main
 */
var SectionNav = React.createClass( {

	propTypes: {
		children: React.PropTypes.node.isRequired,
		selectedText: React.PropTypes.node.isRequired
	},

	getInitialState: function() {
		return {
			mobileOpen: false
		};
	},

	componentWillMount: function() {
		this._checkForSiblingControls( this.props.children );
	},

	componentWillReceiveProps: function( nextProps ) {
		if ( isEqual( this.props, nextProps ) ) {
			return;
		}

		this._checkForSiblingControls( nextProps.children );

		if ( ! this.hasSiblingControls ) {
			this._closeMobilePanel();
		}
	},

	render: function() {
		var children = this._getChildren(),
			className;

		className = React.addons.classSet( {
			'section-nav': true,
			'is-open': this.state.mobileOpen,
			'has-pinned-search': this.hasPinnedSearch
		} );

		return (
			<div className={ className }>
				<div className="section-nav__mobile-header" onTouchTap={ this._toggleMobileOpenState }>
					<span className="section-nav__mobile-header-text">{ this.props.selectedText }</span>
				</div>
				<div className="section-nav__panel">
					{ children }
				</div>
			</div>
		);
	},

	_getChildren: function() {
		return React.Children.map( this.props.children, function( child ) {
			var extraProps = {
				hasSiblingControls: this.hasSiblingControls
			};

			if ( ! child ) {
				return null;
			}

			if ( child.type === NavTabs.type && ! child.props.selectedText && typeof this.props.selectedText === 'string' ) {
				extraProps.selectedText = this.props.selectedText;
			}

			if ( child.type === Search.type ) {
				if ( child.props.pinned ) {
					this.hasPinnedSearch = true;
				}

				extraProps.onSearch = this._generateOnSearch( child.props.onSearch );
			}

			return React.addons.cloneWithProps( child, extraProps );
		}.bind( this ) );
	},

	_closeMobilePanel: function() {
		if ( window.innerWidth < 480 && this.state.mobileOpen ) {
			this.setState( {
				mobileOpen: false
			} );
		}
	},

	_toggleMobileOpenState: function() {
		this.setState( {
			mobileOpen: ! this.state.mobileOpen
		} );
	},

	_generateOnSearch: function( existingOnSearch ) {
		return function() {
			existingOnSearch.apply(this, arguments);
			this._closeMobilePanel();
		}.bind( this );
	},

	_checkForSiblingControls: function( children ) {
		this.hasSiblingControls = false;

		React.Children.forEach( children, function( child, index ) {
			// Checking for at least 2 controls groups that are not search or null
			if ( index && child && child.type !== Search.type ) {
				this.hasSiblingControls = true;
			}
		}.bind( this ) );
	}

} );

module.exports = SectionNav;
