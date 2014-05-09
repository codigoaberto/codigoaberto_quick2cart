// JavaScript Document
jQuery(document).ready(function() {
	jQuery('#cck_tabs1Tabs').append('<li><a href="#tab_quick2cart" data-toggle="tab">Quick2Cart</a></li>');
 	jQuery('#cck_tabs1Content').append('<div id="tab_quick2cart" class="tab-pane">'+jQuery('#quick2cart_info').html()+'</div>');
	jQuery('#quick2cart_info').html('');
});