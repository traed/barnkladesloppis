jQuery(function($) {
	$(document.body).on('click', '#clear_seller_id', function() {
		$(this).siblings('input').val('0');
	});

	$(document.body).on('change', '#recipients', function() {
		if($(this).val().indexOf('occasion') === 0) {
			$('#user_status_row').show();
		} else {
			$('#user_status_row').hide();
		}
	});
});