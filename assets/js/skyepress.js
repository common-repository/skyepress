jQuery( function($) {
    
    /*
     * Move the WP update nag after the plugin header so it won't mess up the layout
     *
     */
     
    if($('.skp-page-header').length > 0 && $('.update-nag').length > 0){
        $('.update-nag').insertAfter($('.skp-page-header'));
    } 

    /*
     * Strips one query argument from a given URL string
     *
     */
    function remove_query_arg( key, sourceURL ) {

        var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }

            rtn = rtn + "?" + params_arr.join("&");

        }

        if(rtn.split("?")[1] == "") {
            rtn = rtn.split("?")[0];
        }

        return rtn;
    }


    /*
     * Adds an argument name, value pair to a given URL string
     *
     */
    function add_query_arg( key, value, sourceURL ) {

        return sourceURL + '&' + key + '=' + value;

    }


    

	
	/*****************************************************/
	/* Tab Navigation
	/*****************************************************/
	$('.skp-nav-tab').on( 'click', function(e) {
		e.preventDefault();

		// Change http referrer
		$_wp_http_referer = $('input[name=_wp_http_referer]');

		var _wp_http_referer = $_wp_http_referer.val();
		_wp_http_referer = remove_query_arg( 'skp-tab', _wp_http_referer );
		$_wp_http_referer.val( add_query_arg( 'skp-tab', $(this).attr('data-tab'), _wp_http_referer ) );

		// Nav Tab activation
		$('.skp-nav-tab').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');

		// Show tab
		$('.skp-tab').removeClass('skp-tab-active');

		var nav_tab = $(this).attr('data-tab');
		$('#skp-tab-' + nav_tab).addClass('skp-tab-active');
		
	});
    

    /*****************************************************/
    /* Schedule
    /*****************************************************/
    $(".wrap").on( 'change', '#skp_schedule_post_type', function(e){
        e.preventDefault();
        $s = $(this);

        $s.siblings('.spinner').addClass('is-active');

        var data = {
    		'action': 'skp_schedule_load_taxonomies',
    		'post_type': $s.val()
    	};        
        $.post(ajaxurl, data, function(response) {
            $("#skp_schedule_taxonomies_ajax").html(response);
            $s.siblings('.spinner').removeClass('is-active');
    	});
    })
    

    /*****************************************************/
    /* Enable / Disable Custom messages based on what
    /* platform accounts are selected
    /*****************************************************/
    $('.skp-platform-account-entry input').each( function() {
        if( $(this).is(':checked') ) {
            $('.skp-platform-account-messages-wrapper').find('.skp-platform-account-messages-nav-tab[data-tab="' + $(this).val() + '"]').removeClass('skp-nav-tab-disabled');
            $('.skp-platform-account-messages-wrapper').find('.skp-platform-account-messages-tab[data-tab="' + $(this).val() + '"] textarea').attr( 'disabled', false );
        }
    });

    $('.skp-platform-account-entry input').on( 'change', function() {

        $nav_tav  = $('.skp-platform-account-messages-wrapper').find('.skp-platform-account-messages-nav-tab[data-tab="' + $(this).val() + '"]');
        $textarea = $('.skp-platform-account-messages-wrapper').find('.skp-platform-account-messages-tab[data-tab="' + $(this).val() + '"] textarea');

        if( $(this).is(':checked') ) {
            $nav_tav.removeClass('skp-nav-tab-disabled');
            $textarea.attr( 'disabled', false );
        } else {
            $nav_tav.addClass('skp-nav-tab-disabled');
            $textarea.attr( 'disabled', true );
        }
    });
    
    /*
    
    Counts the number of available posts when adding a schedule.
    
    $(".wrap").on('change','#skp_schedule_older_than',function(e){
        e.preventDefault();        

        var taxonomies = {};
        $('#skp_schedule_taxonomies_ajax :checkbox:checked').each(function(i){
            if(typeof taxonomies[$(this).data('taxonomy')] == 'undefined'){
                taxonomies[$(this).data('taxonomy')] = [];
            }
            taxonomies[$(this).data('taxonomy')][i] = $(this).val();
        });

        var data = {
    		'action': 'skp_get_number_of_matching_posts',
    		'post_type': $('#skp_schedule_post_type').val(),
            'taxonomies': JSON.stringify(taxonomies),
            'older_than': $('#skp_schedule_older_than').val()
    	};        
        $.post(ajaxurl, data, function(response) {
            $("#info").html(response);
            
    	});
    })
    */


    /*****************************************************/
    /* Add Posting Time
    /*****************************************************/

    // Declare the posting time template as a global
    // Used to cache the hidden HTML template
    var $posting_time_template;

    // Set the posting time template from the hidden posting time HTML element
    $(document).ready( function() {

        // Cache the hidden HTML
        $posting_time_template = $('.skp-posting-time-template').clone();
        $posting_time_template.removeClass('skp-posting-time-template');

        // Remove the HTML template
        $('.skp-posting-time-template').remove();

    });

    /*
     * Adds a new Posting Time into the DOM
     *
     */
    $(document).on( 'click', '#skp-add-posting-time-btn', function(e) {

        e.preventDefault();

        var $posting_time, $this, times_count;

        $this = $(this);
        $this.blur();

        // Calculate the number of posting times
        last_id = $('.skp-posting-time').last().data('key');
        last_id = ( last_id == undefined ? 0 : last_id );

        // Clone posting time template, replace the name and add it into the DOM
        $posting_time = $posting_time_template.clone();
        $posting_time.attr( 'data-key', last_id + 1 );

        $posting_time.find('select').each( function() {
            $(this).attr( 'name', $(this).attr('name').replace( "-1", last_id + 1 ) );
        });

        $this.parent().before( $posting_time );

    });

    /*
     * Removes the current Posting Time from the DOM
     *
     */
    $(document).on( 'click', '.skp-posting-time-remove', function(e) {
        
        e.preventDefault();

        $(this).closest('.skp-posting-time').remove();

    });


    /**
     * Used for: Meta-box Share on Publish
     *
     * Show / hide the Share on Publish custom content part of the meta-box
     * when actioning the enable/disable switch
     *
     *
     */
    if( $('input[name="_skp_share_on_publish"]').is(':checked') ) {

        $('.skp-share-on-publish-custom-content').show();

    }

    $(document).on( 'change', 'input[name="_skp_share_on_publish"]', function() {

        var $this = $(this);

        if( $this.is(':checked') ) {
            $('.skp-share-on-publish-custom-content').stop().slideDown();
        } else {
            $('.skp-share-on-publish-custom-content').stop().slideUp();
        }

    });


    /*
     * Initialize jQuery select2
     *
     *
    if( $.fn.select2 ) {
        $('.skp-form-field-select select').select2({
            minimumResultsForSearch : Infinity
        }).on('select2:open', function() {
            var container = $('.select2-container').last();
            container.addClass('skp-select2');
        });
    }
    */
    
    /*
     * Disable the uninstaller submit button until "REMOVE" is written in the input box
     *
     */
    $(document).on( 'keyup', '#skp-uninstall-confirmation', function(e) {
        
        e.preventDefault();
        
        $("#skp-uninstall-plugin-submit").prop('disabled', true);
        
        if($(this).val() === 'REMOVE')
            $("#skp-uninstall-plugin-submit").prop('disabled', false);

    });


    /*****************************************************/
    /* Partial View: Accounts Custom Messages
    /*****************************************************/
    $(document).on( 'click', '.skp-platform-account-messages-nav-tab', function() {

        $this    = $(this);
        $wrapper = $this.closest('.skp-platform-account-messages-wrapper');

        $wrapper.find('.skp-platform-account-messages-nav-tab').removeClass('skp-nav-tab-active');
        $wrapper.find('.skp-platform-account-messages-tab').removeClass('skp-tab-active');

        $this.addClass('skp-nav-tab-active');
        $wrapper.find('.skp-platform-account-messages-tab[data-tab=' + $this.data('tab') + ']').addClass('skp-tab-active');

    });

    
    /*****************************************************/
	/* Feedback Form
	/*****************************************************/

	// Show form
	$('#skp-show-feedback-form').on( 'click', function(e) {
		e.preventDefault();

		$('#skp-feedback-form').show();
		$('#skp-feedback-form-overlay').show();

		$('#skp-feedback-form').find('textarea').first().focus();
	});

	// Hide form
	$('#skp-close-feedback, #skp-feedback-done > a').on( 'click', function(e) {
		e.preventDefault();
		
		$('#skp-feedback-form').hide();
		$('#skp-feedback-form-overlay').hide();
	});

	// Send feedback
	$('#skp-feedback-form input[type=submit]').on( 'click', function(e) {
		e.preventDefault();

		if( validateFeedback() ) {

			$(this).siblings('.skp-error').hide();
			$(this).after('<div class="spinner">');
			$(this).attr( 'disabled', true );

			sendFeedback().done( function( response ) {
				if( response == 1 ) {
					$('#skp-feedback-form form').hide();
					$('#skp-feedback-done').show();
				}
			});

		} else {

			$(this).siblings('.skp-error').css('display', 'inline-block');

		}

	});

	/*
	 * Check to see if all fields are filled
	 */
	function validateFeedback() {

		if( $('#skp-feedback-mail').val() == '' )
			return false;

		if( $('#skp-feedback-name').val() == '' )
			return false;

		if( $('#skp-feedback-textarea-1').val() == '' )
			return false;

		if( $('#skp-feedback-textarea-2').val() == '' )
			return false;

		return true;
	}

	/*
	 * Make an AJAX call to send feedback
	 */
	function sendFeedback() {

		var data = {
			'action' 	: 'skp_send_feedback',
			'email'	 	: $('#skp-feedback-mail').val(),
			'name'	 	: $('#skp-feedback-name').val(),
			'textarea-1': $('#skp-feedback-textarea-1').val(),
			'textarea-2': $('#skp-feedback-textarea-2').val()
		}

		return $.post( ajaxurl, data, function() {});

	}
    

    /*****************************************************/
    /* Deactivation Form
    /*****************************************************/    
    $('.wp-admin.plugins-php tr[data-slug="skyepress"] .row-actions .deactivate a').click(function(e) {
        e.preventDefault();  
        $('#skp-deactivate-modal').show();
    });

    $('#skp-deactivate-modal form input[type="radio"]').click(function () {
        $('#skp-deactivate-modal form textarea, #skp-deactivate-modal form input[type="text"]').hide();
        $(this).parents('li').next('li').children('input[type="text"], textarea').show();
    });


    $('#skp-feedback-submit').click(function (e) {
        e.preventDefault();        
        $('#skp-deactivate-modal').hide();        
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'skp_send_deactivation_feedback',
                data: $('#skp-deactivate-modal form').serialize()
            },
            complete: function (MLHttpRequest, textStatus, errorThrown) {
                $('#skp-deactivate-modal').remove();
                window.location.href = $('.wp-admin.plugins-php tr[data-slug="skyepress"] .row-actions .deactivate a').attr('href');   
            }
        });      
    });
    
    $('#skp-only-deactivate').click(function (e) {
        e.preventDefault();
        $('#skp-deactivate-modal').hide();        
        $('#skp-deactivate-modal').remove();
        window.location.href = $('.wp-admin.plugins-php tr[data-slug="skyepress"] .row-actions .deactivate a').attr('href');
        
    });    
    
    $('.skp-deactivate-close').click(function (e) {
        e.preventDefault();
        $('#skp-deactivate-modal').hide();
    });

});