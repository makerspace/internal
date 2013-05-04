/* ======================================
 * Javascript for internal.makerspace.se
 * @author Jim Nelin
 * ====================================== */
$(function(){
	// Add datepicker to all input.datepicker
	//$('.datepicker').datepicker();
	
	// Auto-close alerts
	window.setTimeout(function() {
		$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
		});
	}, 4000); // 4 seconds
	
	// Add tablesorter
	$("table.tablesorter").tablesorter();
	
	// Empty password/e-mail fields.
	$("input[type=email], input[type=password]").attr("autocomplete","off");
	
	// Allow checking all/no checkboxes
	$('.checkall').click(function () {
		selector = $(this).data('selector');
		$('input:checkbox[name="'+selector+'[]"]').prop("checked", true);
		return false;
	});
	
	$('.checknone').click(function () {
		selector = $(this).data('selector');
		$('input:checkbox[name="'+selector+'[]"]').prop("checked", false);
		return false;
	});
	
});