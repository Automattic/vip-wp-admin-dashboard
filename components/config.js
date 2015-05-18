var config = {};

// load certain settings from WordPress via data-attribtues on the #app div
var app = document.getElementById( 'app' ),
	asseturl = app.getAttribute( 'data-asseturl' ),
	user = app.getAttribute( 'data-user' ),
	useremail = app.getAttribute( 'data-email' );

config.title = 'VIP Dashboard Test';
config.asseturl = asseturl;
config.user = user;
config.useremail = useremail;

module.exports = config;