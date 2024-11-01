jQuery( function($) {


	/**
	 * Handle the showing of the promotional pop-up when clicking on the
	 * Connect Account link
	 *
	 */
	$(document).on( 'click', '.skp-connect-account-link', function(e) {

		$this = $(this);

		if( $(this).siblings('.skp-platform-account-entry').length > 0 ) {
			e.preventDefault();

			$('.skp-promo-pop-up-wrapper').addClass('skp-active');
		}

	});


	/**
	 * Handle the showing of the promotional pop-up when clicking on the
	 * Add New Schedule button
	 *
	 */
	$(document).on( 'click', '.skp-new-schedule-link', function(e) {

		$this = $(this);
		var $trs = $('.skp-wrap .wp-list-table.skp_schedules tbody tr');

		if( $trs.length >= 1 && ! $trs.first().hasClass('no-items') ) {
			e.preventDefault();

			$('.skp-promo-pop-up-wrapper').addClass('skp-active');
		}

	});


	/**
	 * Closes the parent promo pop-up
	 *
	 */
	$(document).on( 'click', '.skp-promo-pop-up-close', function() {

		$(this).closest('.skp-promo-pop-up-wrapper').removeClass('skp-active');

	});

});