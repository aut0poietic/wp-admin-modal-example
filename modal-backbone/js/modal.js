/**
 * Backbone Application File
 * @internal Obviously, I've dumped all the code into one file. This should probably be broken out into multiple
 * files and then concatenated and minified ( via a tool like CodeKit ) but as it's an example, it's all one lumpy file.
 * @package aut0poietic.backbone_modal
 */

/**
 * @type {Object} JavaScript namespace for our application.
 */
var aut0poietic = {
	backbone_modal : {
		__instance : undefined
	}
};

/**
 * Primary template object.
 * The Templates class is a Backbone View that is not attached to the dom. Instead, it loads in a set of
 * HTML templates using the wp_ajax method 'get_template_data' ( implemented in Plugin::get_template_data ).
 * The resulting HTML-fragment contains a set of script elements with ID's corresponding to the specific template.
 *
 * The file uses ERB-style delimiters for use with underscore's template method.
 *
 * Fires the Templates.loadEvent event once the template data is loaded.
 *
 * To use the template, call Template.get( templateID ) where template ID is the jQuery query of the script block
 * containing the template you want. The get method will return a compiled version of the template, ready for your data.
 * All future calls to get for that template are pre-compiled.
 *
 * Example:
 * <code>
 *     var myTemplate = this.templates.get( '#my-template' ) ;
 *     $( "body" ).append( myTemplate( { variable_1 : "Foo" , variable_2 : "Bar" } );
 * </code>
 *
 * @internal I prefer Mustache-style delimiters, but it's inappropriate for the plugin to alter the template behavior
 * for the _entire WordPress install_. Hopefully other plugin developers respect this as well and don't alter
 * _.templateSettings.interpolate.
 */
aut0poietic.backbone_modal.Templates = Backbone.View.extend(
	{
		/**
		 * @var string Event name for the templates loaded event
		 */
		loadEvent : "templatesLoaded" ,

		/**
		 * @var {Object} hash of the compiled templates
		 */
		complied  : {} ,

		initialize : function () {
			"use strict";
			_.bindAll( this , 'load', 'done', 'fail', 'get' );
		} ,

		/**
		 * Loads the HTML templates. Fires the Templates.loadEvent event when all templates are loaded.
		 * @todo Rudimentary error handling; This should be beefed up in any real plugin.
		 */
		load : function () {
			"use strict";

			var data = {
				action : "get_template_data"
			};
			jQuery.get( ajaxurl , data , "html" )
				.done( this.done )
				.fail( this.fail );
		} ,

		/**
		 * Success/Done Promise handler
		 * @param data string HTML Fragment provided by the wp_ajax_get_template_data call.
		 */
		done : function ( data ) {
			"use strict";

			this.setElement( data );
			this.trigger( this.loadEvent );
		} ,

		/**
		 * Failure Promise handler
		 * @todo: Should add the error parameters and some error handling or recovery.
		 */
		fail : function () {
			"use strict";
			// don't send event, signal error.
		} ,

		/**
		 * Fetches a compiled template for assembling a Backbone UI.
		 * @param template string A jQuery selector, typically an id selector (#).
		 * @return {_.template} A compiled template for the template name specified or a blank template.
		 * @todo Should provide more clear error / not found return type -- null or undefined?
		 */
		get : function ( template ) {
			"use strict";

			if ( this.complied[ template ] === undefined ) {
				this.complied[ template ] = _.template( this.$( template ).html() );
			}
			return this.complied[ template ];
		}
	} );

/**
 * Primary Modal Application Class
 */
aut0poietic.backbone_modal.Application = Backbone.View.extend(
	{
		id        : "backbone_modal_dialog" ,
		events : {
			"click .backbone_modal-close" : "closeModal" ,
			"click #btn-cancel"           : "closeModal" ,
			"click #btn-ok"               : "saveModal" ,
			"click .navigation-bar a"     : "doNothing"
		} ,

		/**
		 *  Local storage for the template object.
		 *  @todo You may want to promote the scope of this object if you have very many Backbone Views to allow them
		 *  to share templates.
		 */
		templates : undefined ,

		/**
		 * Simple object to store any UI elements we need to use over the life of the application.
		 */
		ui : {
			nav     : undefined ,
			content : undefined
		} ,

		/**
		 * Instantiates the Template object and triggers load.
		 */
		initialize : function () {
			"use strict";

			_.bindAll( this , 'render', 'preserveFocus', 'closeModal', 'saveModal', 'doNothing' );
			this.templates = new aut0poietic.backbone_modal.Templates();
			this.templates
				.on( this.templates.loadEvent , this.render )
				.load();
		} ,

		/**
		 * Assembles the UI from loaded templates.
		 * @internal Obviously, if the templates fail to load, our modal never launches.
		 */
		render : function () {
			"use strict";

			// remove the promise handler, we're done with it.
			this.templates.off( this.templates.loadEvent ) ;

			// Build the base window and backdrop, attaching them to the $el.
			// Setting the tab index allows us to capture focus and redirect it in Application.preserveFocus
			this.$el.attr( 'tabindex' , '0' )
				.append( this.templates.get( '#modal-window' )() )
				.append( this.templates.get( '#modal-backdrop' )() );

			// Save a reference to the navigation bar's unordered list and populate it with items.
			// This is here mostly to demonstrate the use of the template class.
			this.ui.nav = this.$( '.navigation-bar nav ul' )
				.append( this.templates.get( '#modal-menu-item' )( { url : "#one" , name : "Option 1" } ) )
				.append( this.templates.get( '#modal-menu-item' )( { url : "#two" , name : "Option 2" } ) )
				.append( this.templates.get( '#modal-menu-item-separator' )() )
				.append( this.templates.get( '#modal-menu-item' )( { url : "#three" , name : "Option 3" } ) );

			// The l10n object generated by wp_localize_script() should be available, but check to be sure.
			// Again, this is a trivial example for demonstration.
			if ( typeof aut0poietic_backbone_modal_l10n === "object" ) {
				this.ui.content = this.$( '.backbone_modal-main article' )
					.append( "<p>" + aut0poietic_backbone_modal_l10n.replace_message + "</p>" );
			}
			// Handle any attempt to move focus out of the modal.
			jQuery( document ).on( "focusin" , this.preserveFocus );

			// set overflow to "hidden" on the body so that it ignores any scroll events while the modal is active
			// and append the modal to the body.
			// TODO: this might better be represented as a class "modal-open" rather than a direct style declaration.
			jQuery( "body" ).css( { "overflow" : "hidden" } ).append( this.$el );
			// Set focus on the modal to prevent accidental actions in the underlying page
			// Not strictly necessary, but nice to do.
			this.$el.focus();
		} ,

		/**
		 * Ensures that keyboard focus remains within the Modal dialog.
		 * @param e {object} A jQuery-normalized event object.
		 */
		preserveFocus : function ( e ) {
			"use strict";
			if ( this.$el[0] !== e.target && !this.$el.has( e.target ).length ) {
				this.$el.focus();
			}
		} ,

		/**
		 * Closes the modal and cleans up after the instance.
		 * @param e {object} A jQuery-normalized event object.
		 */
		closeModal : function ( e ) {
			"use strict";

			e.preventDefault();
			this.undelegateEvents();
			jQuery( document ).off( "focusin" );
			jQuery( "body" ).css( { "overflow" : "auto" } );
			this.remove();
			aut0poietic.backbone_modal.__instance = undefined;
		} ,

		/**
		 * Responds to the btn-ok.click event
		 * @param e {object} A jQuery-normalized event object.
		 * @todo You should make this your own.
		 */
		saveModal : function ( e ) {
			"use strict";
			this.closeModal( e );
		} ,

		/**
		 * Ensures that events do nothing.
		 * @param e {object} A jQuery-normalized event object.
		 * @todo You should probably delete this and add your own handlers.
		 */
		doNothing : function ( e ) {
			"use strict";
			e.preventDefault();
		}

	} );

jQuery( function ( $ ) {
	"use strict";
	/**
	 * Attach a click event to the meta-box button that instantiates the Application object, if it's not already open.
	 */
	$( "#open-backbone_modal" ).click( function ( e ) {
		e.preventDefault();
		if ( aut0poietic.backbone_modal.__instance === undefined ) {
			aut0poietic.backbone_modal.__instance = new aut0poietic.backbone_modal.Application();
		}
	} );
} );