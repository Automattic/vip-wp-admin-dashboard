(function ($) {

	function init() {
		bindActions();
		goResize();
		evenHeights('.plugin');
		protectCodeActivatedPlugins();
	}

	function bindActions() {
		$('.plugin').hover(
			function() {
				var plugin = $(this);
				pluginHover( plugin );
			},
			function() {
				var plugin = $(this);
				pluginReset( plugin );
			}
		);
	}

	function pluginHover( plugin ) {
		plugin.addClass('hovered'),
		plugin.find('.interstitial').addClass('visible');
		setTimeout(function() {
			plugin.find('.fp-button').addClass('visible');
		}, 280);
	}

	function pluginReset( plugin ) {
		plugin.removeClass('hovered'),
		plugin.find('.interstitial').removeClass('visible'),
		plugin.find('.fp-button').removeClass('visible');
	}

	function evenHeights(selector) {
		var maxHeight = 0;
		$(selector).each(function(){
			$(this).height('auto');
			maxHeight = Math.max(maxHeight, $(this).height());
		});
		if ( maxHeight === 0 ) {
			maxHeight = 'auto';
		}
		$(selector).each(function(){
			$(this).height(maxHeight);
		});
	}

	function goResize() {
		function onResize(c,t) {
			onresize=function() {
				clearTimeout(t);
				t = setTimeout(c,100);
			};
		return c;
		}

		onResize(function() {
			evenHeights('.plugin');
		});
	}

	function protectCodeActivatedPlugins() {
		var $row = $('.vip-code-activated-plugin').closest('tr');
		$row.addClass('active').removeClass('inactive');
		$row.find('input[type="checkbox"]').hide();
	}

	$(function() {
		init();
	});

}(jQuery));
