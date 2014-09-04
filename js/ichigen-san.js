(function($){
	var selectIchigenSan    = $( 'select#ichigen_san_enabling' );
	var selectIchigenSanVal = '';

	if ( selectIchigenSan[0] ) {
		selectIchigenSanVal = selectIchigenSan.val();
		if ( 2 != selectIchigenSanVal ) {
			selectIchigenSan.parents('tr').nextAll('tr').hide();
		}
		selectIchigenSan.on( 'change', function(event) {
			event.preventDefault();
			selectIchigenSanVal = selectIchigenSan.val();
			if ( 2 != selectIchigenSanVal ) {
				selectIchigenSan.parents('tr').nextAll('tr').hide();
			} else {
				selectIchigenSan.parents('tr').nextAll('tr').show();
			}
		});
	}

})(jQuery);
