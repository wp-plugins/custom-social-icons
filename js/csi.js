jQuery(document).ready(function($) {
	jQuery('table.csi-social-icon tr td img').hover(function() {
		jQuery(this).animate({
			opacity: 0.5
			//marginTop:'-5px'
		  }, 200 );
	},
	function() {
		jQuery(this).animate({
			opacity: 1
			//marginTop:'0px'
		  }, 200 );
	});
	
	jQuery('ul.csi-social-icon li img').hover(function() {
		jQuery(this).animate({
			opacity: 0.5
		  }, 200 );
	},
	function() {
		jQuery(this).animate({
			opacity: 1
		  }, 200 );
	});
});