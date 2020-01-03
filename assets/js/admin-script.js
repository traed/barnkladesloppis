jQuery(function($) {
	$(document.body).on('click', '#clear_seller_number', function() {
		$(this).siblings('input').val('0');
	});
});