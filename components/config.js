var config = {};

// load certain settings from WordPress via data-attribtues on the #app div
var app = document.getElementById( 'app' ),
	ajaxurl = app.getAttribute( 'data-ajaxurl' ),
	asseturl = app.getAttribute( 'data-asseturl' ),
	user = app.getAttribute( 'data-name' ),
	useremail = app.getAttribute( 'data-email' );

config.title = 'VIP Dashboard Test';
config.ajaxurl = ajaxurl;
config.asseturl = asseturl;
config.user = user;
config.useremail = useremail;

module.exports = config;