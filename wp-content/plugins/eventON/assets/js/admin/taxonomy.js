
jQuery(document).ready(function($){

	// color picker on taxnomy
		$('.evo_et1_color_circle').ColorPicker({
			onBeforeShow: function(){
				$(this).ColorPickerSetColor( $(this).attr('hex'));
			},	
			onChange:function(hsb, hex, rgb,el){
				//console.log(hex);
				//$(el).attr({'backgroundColor': '#' + hex});
				$(el).html( hex);
			},	
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).siblings('input').attr({'value':hex});
				$(el).css('backgroundColor', '#' + hex);
				$(el).attr({'title': '#' + hex});
				$(el).ColorPickerHide();
			}
		});
});