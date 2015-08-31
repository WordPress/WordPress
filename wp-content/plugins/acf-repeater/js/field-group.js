(function($){
	
	var repeater = {
		
		$el : null,
		
		set : function( o ){
			
			// merge in new option
			$.extend( this, o );
			
				
			// return this for chaining
			return this;
			
		},
		
		init : function(){
			
			this.render();
			
		},
		
		render : function(){
			
			// vars
			var id = this.$el.attr('data-id'),
				layout = 'table';
			
			
			// find layout value
			if( this.$el.find('input[name="fields[' + id + '][layout]"]:checked').length > 0 )
			{
				layout = this.$el.find('input[name="fields[' + id + '][layout]"]:checked').val();
			}
			
			
			// add class
			this.$el.find('.repeater:first').removeClass('layout-row layout-table').addClass( 'layout-' + layout );
			
		}
		
	};
	
	
	/*
	*  Document Ready
	*
	*  description
	*
	*  @type	function
	*  @date	18/08/13
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	$(document).ready(function(){
		
		$('.field_type-repeater').each(function(){
			
			repeater.set({ $el : $(this) }).init();
			
		});
		
	});
	
	
	/*
	*  Events
	*
	*  jQuery events for this field
	*
	*  @type	function
	*  @date	1/03/2011
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	$(document).on('click', '.field_option_repeater_layout input[type="radio"]', function( e ){
		
		repeater.set({ $el : $(this).closest('.field_type-repeater') }).render();
		
	});
	
	
	$(document).on('acf/field_form-open', function(e, field){
		
		// vars
		$el = $(field);
		
		
		if( $el.hasClass('field_type-repeater') )
		{
			repeater.set({ $el : $el }).render();
		}
		
	});
	

})(jQuery);
