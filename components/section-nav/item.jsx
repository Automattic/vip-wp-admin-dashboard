/**
 * External Dependencies
 */
var React = require( 'react/addons' );

/**
 * Internal Dependencies
 */
// none

/**
 * Main
 */
var NavItem = React.createClass( {

	mixins: [ React.addons.PureRenderMixin ],

	propTypes: {
		itemType: React.PropTypes.string,
		path: React.PropTypes.string,
		selected: React.PropTypes.bool,
		tabIndex: React.PropTypes.number,
		onClick: React.PropTypes.func,
		isExternalLink: React.PropTypes.bool
	},

	render: function() {
		var itemClassPrefix = ( this.props.itemType ) ? this.props.itemType : 'tab',
			itemClasses, itemClassName, target;

		itemClasses = {
			'is-selected': this.props.selected,
			'is-external': this.props.isExternalLink
		};
		itemClasses[ 'section-nav-' + itemClassPrefix ] = true;

		itemClassName = React.addons.classSet( itemClasses );

		if ( this.props.isExternalLink ) {
			target = '_blank';
		}

		return (
			<li className={ itemClassName }>
				<a
					href={ this.props.path }
					target={ target }
					className={ 'section-nav-' + itemClassPrefix + '__link' }
					onTouchTap={ this.props.onClick }
					tabIndex={ this.props.tabIndex || 0 }
					aria-selected={ this.props.selected }
					role="menuitem">
					<span className={ 'section-nav-' + itemClassPrefix + '__text' }>{ this.props.children }</span>
				</a>
			</li>
		);
	}

} );

module.exports = NavItem;
