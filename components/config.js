var config = {};

// load certain settings from WordPress via data-attribtues on the #app div
var app = document.getElementById( 'app' );
var asseturl = app.getAttribute( 'data-asseturl' );

config.title = 'VIP Dashboard Test';
config.asseturl = asseturl;

module.exports = config;