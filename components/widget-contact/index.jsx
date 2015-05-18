/**
 * External dependencies
 */
var React = require( 'react' );

/**
 * Internal dependencies
 */
var Config = require( '../config.js' );
	Widget = require( '../widget' );


/**
 * Contact Widget Component
 */
Widget_Contact = React.createClass( {
	getInitialState: function(){
		return {
			user: Config.user,
			useremail: Config.useremail
		};
	},
	render: function() {
		return (
			<Widget className="widget__contact" title="Contact WordPress.com VIP Support">
				<form className="widget__contact-form" action="submit" method="get">
					<div className="contact-form__row">
						<label for="contact-form__name">Name</label><input type="text" name="" value={ this.state.user } id="contact-form__name" placeholder="First and last name" />
					</div>
					<div className="contact-form__row">
						<label for="contact-form__email">Email</label><input type="text" name="" value={ this.state.useremail } id="contact-form__email" placeholder="Email address" />
					</div>
					<div className="contact-form__row">
						<label for="contact-form__subject">Subject</label><input type="text" name="" value="" id="contact-form__subject" placeholder="Ticket name" />
					</div>
					<div className="contact-form__row">
						<label for="contact-form__type">Type</label><div className="contact-form__select"><select id="contact-form__type">
								<option value="technical" selected="selected">Technical</option>
								<option value="business">Business/Project Management</option>
								<option value="review">Theme/Plugin Review</option>
							</select>
						</div>
					</div>
					<div className="contact-form__row">
						<label for="contact-form__details">Details</label><textarea name="details" rows="4" id="contact-form__details" placeholder="Please be descriptive"></textarea>
					</div>
					<div className="contact-form__row">
						<label for="contact-form__priority">Priority</label><select id="contact-form__priority">
							<optgroup label="Normal Priority">
								<option value="Low">Low</option>
								<option value="Medium" selected="selected">Normal</option>
								<option value="High">High</option>
							</optgroup>
							<optgroup label="Urgent Priority">
								<option value="Emergency">Emergency (Outage, Security, Revert, etc...)</option>
							</optgroup>
						</select>
					</div>
					<div className="contact-form__row">
						<input type="submit" value="Submit Request" />
					</div>
				</form>
			</Widget>
		);
	}
} );
module.exports = Widget_Contact;