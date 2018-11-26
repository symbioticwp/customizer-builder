jQuery( document ).ready(function($) {
	"use strict";

	/**
	 * Dropdown Select2 Custom Control
	 *
	 */
	$('.customize-control-dropdown-select2').each(function(){
		$('.customize-control-select2').select2({
			allowClear: true
		});
	});

	$(".customize-control-select2").on("change", function() {
		var select2Val = $(this).val();
		$(this).parent().find('.customize-control-dropdown-select2').val(select2Val).trigger('change');
	});


	$('.customize-control-')
});