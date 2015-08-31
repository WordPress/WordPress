(function($){
	
	/*
	*  Repeater
	*
	*  static model for this field
	*
	*  @type	event
	*  @date	18/08/13
	*
	*/
	
	acf.fields.repeater = {
		
		$el : null,
				
		o : {},
		
		set : function( o ){
			
			// merge in new option
			$.extend( this, o );
			
			
			// find elements
			//this.$input = this.$el.children('input[type="hidden"]');
			
			
			// get options
			this.o = acf.helpers.get_atts( this.$el );
			
			
			// add row_count
			this.o.row_count = this.$el.find('> table > tbody > tr.row').length;	
			
			
			// return this for chaining
			return this;
			
		},
		init : function(){
			
			// reference
			var _this = this,
				$el = this.$el;
			
			
			// sortable
			if( this.o.max_rows != 1 )
			{
				this.$el.find('> table > tbody').unbind('sortable').sortable({
				
					items					: '> tr.row',
					handle					: '> td.order',
					helper					: acf.helpers.sortable,
					forceHelperSize			: true,
					forcePlaceholderSize	: true,
					scroll					: true,
					
					start : function (event, ui) {
					
						$(document).trigger('acf/sortable_start', ui.item);
						$(document).trigger('acf/sortable_start_repeater', ui.item);
		
						// add markup to the placeholder
						var td_count = ui.item.children('td').length;
		        		ui.placeholder.html('<td colspan="' + td_count + '"></td>');
		        		
		   			},
		   			
		   			stop : function (event, ui) {
					
						$(document).trigger('acf/sortable_stop', ui.item);
						$(document).trigger('acf/sortable_stop_repeater', ui.item);
						
						
						// render
						_this.set({ $el : $el }).render();
						
		   			}
				});
			}
						
			
			// render
			this.render();
					
		},
		render : function(){
			
			// update row_count
			this.o.row_count = this.$el.find('> table > tbody > tr.row').length;
			
			
			// update order numbers
			this.$el.find('> table > tbody > tr.row').each(function(i){
			
				$(this).children('td.order').html( i+1 );
				
			});
			
			
			// empty?
			if( this.o.row_count == 0 )
			{
				this.$el.addClass('empty');
			}
			else
			{
				this.$el.removeClass('empty');
			}
			
			
			// row limit reached
			if( this.o.row_count >= this.o.max_rows )
			{
				this.$el.addClass('disabled');
				this.$el.find('> .repeater-footer .acf-button').addClass('disabled');
			}
			else
			{
				this.$el.removeClass('disabled');
				this.$el.find('> .repeater-footer .acf-button').removeClass('disabled');
			}
			
		},
		add : function( $before ){
			
			
			// validate
			if( this.o.row_count >= this.o.max_rows )
			{
				alert( acf.l10n.repeater.max.replace('{max}', this.o.max_rows) );
				return false;
			}
			
		
			// create and add the new field
			var new_id = acf.helpers.uniqid(),
				new_field_html = this.$el.find('> table > tbody > tr.row-clone').html().replace(/(=["]*[\w-\[\]]*?)(acfcloneindex)/g, '$1' + new_id),
				new_field = $('<tr class="row"></tr>').append( new_field_html );
			
			
			// add row
			if( ! $before )
			{
				$before = this.$el.find('> table > tbody > .row-clone');
			}
			
			$before.before( new_field );
			
			
			// trigger mouseenter on parent repeater to work out css margin on add-row button
			this.$el.closest('tr').trigger('mouseenter');
			
			
			// update order
			this.render();
			
			
			// setup fields
			$(document).trigger('acf/setup_fields', new_field);
	
			
			// validation
			this.$el.closest('.field').removeClass('error');
			
		},
		remove : function( $tr ){
			
			// refernce
			var _this = this;
			
			
			// validate
			if( this.o.row_count <= this.o.min_rows )
			{
				alert( acf.l10n.repeater.min.replace('{min}', this.o.min_rows) );
				return false;
			}
			
			
			// animate out tr
			$tr.addClass('acf-remove-item');
			setTimeout(function(){
				
				$tr.remove();
				
				
				// trigger mouseenter on parent repeater to work out css margin on add-row button
				_this.$el.closest('tr').trigger('mouseenter');
			
				
				// render
				_this.render();
				
			}, 400);
			
		}
		
		
	};
	
	
	/*
	*  acf/setup_fields
	*
	*  run init function on all elements for this field
	*
	*  @type	event
	*  @date	20/07/13
	*
	*  @param	{object}	e		event object
	*  @param	{object}	el		DOM object which may contain new ACF elements
	*  @return	N/A
	*/
	
	$(document).on('acf/setup_fields', function(e, el){
		
		$(el).find('.repeater').each(function(){
			
			acf.fields.repeater.set({ $el : $(this) }).init();
			
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
	
	$(document).on('click', '.repeater .repeater-footer .add-row-end', function( e ){
		
		e.preventDefault();
		
		acf.fields.repeater.set({ $el : $(this).closest('.repeater') }).add( false );
		
		$(this).blur();
		
	});
	
	$(document).on('click', '.repeater td.remove .add-row-before', function( e ){
		
		e.preventDefault();
		
		acf.fields.repeater.set({ $el : $(this).closest('.repeater') }).add( $(this).closest('tr') );
		
		$(this).blur();
		
	});
	
	$(document).on('click', '.repeater td.remove .acf-button-remove', function( e ){
		
		e.preventDefault();
		
		acf.fields.repeater.set({ $el : $(this).closest('.repeater') }).remove( $(this).closest('tr') );
		
		$(this).blur();
		
	});
	
	$(document).on('mouseenter', '.repeater tr.row', function( e ){
		
		// vars
		var $el = $(this).find('> td.remove > a.acf-button-add'),
			margin = ( $el.parent().height() / 2 ) + 9; // 9 = padding + border
		
		
		// css
		$el.css('margin-top', '-' + margin + 'px' );
		
	});
	
	$(document).on('acf/conditional_logic/show acf/conditional_logic/hide', function( e, $target, item ){
		
		$target.closest('tr.row').trigger('mouseenter');
		
	});
	

})(jQuery);
