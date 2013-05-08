/**
 *  Modal / Workflow specific code
 *  This is where you'll build your workflow javascript. Remember to rename the "aut0poietic_iframe_modal_close_handler".
 */
jQuery( function($){
	"use strict";
	// Tells any anchor with only a hash url to do nothing.
	$("a[href='#']" ).on('click', function( e ){ e.preventDefault(); });
	// Tells both the OK and Cancel buttons to close the modal. You'll want to make these your own.
	$('#btn-ok, #btn-cancel' ).on( 'click' , parent.aut0poietic_iframe_modal_close_handler );
});