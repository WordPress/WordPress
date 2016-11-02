// TODO adjust to match code standards
(function($){
	var USSidebar = function(){

		this.widget = $('.widget-liquid-right');
		this.widgetsRight = $('#widgets-right');
		this.addWidgetAreaForm = $('#us_add_widget_area');

		this.initForm();
		this.initCustomWidgets();
		this.bindEvents();

	};

	USSidebar.prototype = {

		initForm: function(){
			this.widget.append(this.addWidgetAreaForm.html());
			this.nonce = this.widget.find('input[name="us_delete_widget_area_nonce"]').val();
			this.confirmMessage = this.widget.find('#us_confirm_widget_area_deletion').html();
		},

		initCustomWidgets: function(){
			this.widgetsRight.find('.sidebar-us-custom-area').append('<span class="us-custom-area-delete"></span>');
		},

		bindEvents: function(){
			this.widget.on('click', '.us-custom-area-delete', $.proxy(this.deleteWidgetArea, this));
		},


		//delete the sidebar area with all widgets within, then re calculate the other sidebar ids and re save the order
		deleteWidgetArea: function(e){
			var deleteIt = confirm(this.confirmMessage);

			if (deleteIt == false) return false;

			var widget = $(e.currentTarget).parents('.widgets-holder-wrap:eq(0)'),
				title = widget.find('.sidebar-name h3 , .sidebar-name h2'),
				spinner = title.find('.spinner'),
				widgetName = $.trim(title.text()),
				obj = this;

			$.ajax({
				type: "POST",
				url: window.ajaxurl,
				data: {
					action: 'us_delete_custom_widget_area',
					name: widgetName,
					_wpnonce: obj.nonce
				},

				beforeSend: function(){
					spinner.addClass('activate_spinner');
				},
				success: function(response){
					if (response == 'success') {
						widget.slideUp(200, function(){

							$('.widget-control-remove', widget).trigger('click'); //delete all widgets inside
							widget.remove();

						});
					}
				}
			});
		}

	};

	$(function(){
		new USSidebar();
	});


})(jQuery);
