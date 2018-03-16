jQuery(document).ready(function(){
	
	/*append the dom popup if present to show the quick setup nicely*/
	if(jQuery('.smmch-popup').length > 0){
		var smmchPopup = jQuery('.smmch-popup').detach();
		jQuery('#wpwrap').before(smmchPopup);
		jQuery('.smmch-popup').fadeIn();
	}
	if(jQuery('.smmch-each-section').length > 0){
		jQuery('.smmch-each-section .my-color-picker').wpColorPicker();
	}
});