/**
 * External Dependencies
 */
var React = require( 'react/addons' );
	//debounce = require( 'lodash/function/debounce' );

/**
 * Internal Dependencies
 */
var SelectDropdown = require( 'forms/select-dropdown' ),
	DropdownItem = require( 'forms/select-dropdown/item' );

/**
 * Internal Variables
 */
var MOBILE_PANEL_THRESHOLD = 480;

/**
 * Main
 */
var NavTabs = React.createClass( {

	propTypes: {
		selectedText: React.PropTypes.string.isRequired,
		label: React.PropTypes.string,
		hasSiblingControls: React.PropTypes.bool
	},

	getDefaultProps: function() {
		return {
			hasSiblingControls: false
		};
	},

	getInitialState: function() {
		return {
			isDropdown: false
		};
	},

	componentDidMount: function() {
		this._setDropdown();
		this._debouncedAfterResize = debounce( this._setDropdown, 300 );

		window.addEventListener( 'resize', this._debouncedAfterResize );
	},

	componentWillReceiveProps: function() {
		this._setDropdown();
	},

	componentWillUnmount: function() {
		window.removeEventListener( 'resize', this._debouncedAfterResize );
	},

	render: function() {
		var tabs, tabsClassName;

		tabs = React.Children.map( this.props.children, function( child, index ) {
			return React.addons.cloneWithProps( child, {
				ref: 'tab-' + index
			} );
		}.bind( this ) );

		tabsClassName = React.addons.classSet( {
			'section-nav-tabs': true,
			'is-dropdown': this.state.isDropdown,
			'is-open': this.state.isDropdownOpen,
			'has-siblings': this.props.hasSiblingControls
		} );

		return (
			<div className="section-nav-group" ref="navGroup">
				<div className={ tabsClassName }>
					{ ( this.props.label ) ?
						<h6 className="section-nav-group__label">{ this.props.label }</h6>
					: null }
					<ul className="section-nav-tabs__list" role="menu" onKeyDown={ this._keyHandler }>
						{ tabs }
					</ul>
					{ ( this.state.isDropdown && window.innerWidth > MOBILE_PANEL_THRESHOLD ) ?
						this._getDropdown()
					: null }
				</div>
			</div>
		);

	},

	_getTabWidths: function() {
		var totalWidth = 0;

		this.props.children.forEach( function( child, index ) {
			var tabWidth = this.refs[ 'tab-' + index ].getDOMNode().offsetWidth;

			totalWidth += tabWidth;
		}, this );

		this.tabsWidth = totalWidth;
	},

	_getDropdown: function() {
		var dropdownOptions;

		dropdownOptions = React.Children.map( this.props.children, function( child, index ) {
			return (
				<DropdownItem {...child.props} key={ 'navTabsDropdown-' + index }>
					{ child.props.children }
				</DropdownItem>
			);
		} );

		return (
			<SelectDropdown className="section-nav-tabs__dropdown" selectedText={ this.props.selectedText }>
				{ dropdownOptions }
			</SelectDropdown>
		);
	},

	_setDropdown: function() {
		var navGroupWidth;

		if ( window.innerWidth > MOBILE_PANEL_THRESHOLD ) {
			navGroupWidth = this.refs.navGroup.getDOMNode().offsetWidth;

			if ( ! this.tabsWidth ) {
				this._getTabWidths();
			}

			if ( navGroupWidth <= this.tabsWidth && ! this.state.isDropdown ) {
				this.setState( {
					isDropdown: true
				} );
			} else if ( navGroupWidth > this.tabsWidth && this.state.isDropdown ) {
				this.setState( {
					isDropdown: false
				} );
			}
		} else if ( window.innerWidth <= MOBILE_PANEL_THRESHOLD && this.state.isDropdown ) {
			this.setState( {
				isDropdown: false
			} );
		}
	},

	_keyHandler: function( event ) {
		switch ( event.keyCode ) {
			case 32: // space
			case 13: // enter
				event.preventDefault();
				document.activeElement.click();
				break;
		}
	}

} );

module.exports = NavTabs;
