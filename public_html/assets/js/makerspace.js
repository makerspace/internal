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
	
	$("table.tablesorter").tablesorter();
});