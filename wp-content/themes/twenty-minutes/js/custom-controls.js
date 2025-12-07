jQuery(document).ready(function($){
//slider-custom-control
// Set our slider defaults and initialise the slider
    $('.slider-custom-control').each(function(){
        var sliderValue = $(this).find('.customize-control-slider-value').val();
        var newSlider = $(this).find('.slider');
        var sliderMinValue = parseFloat(newSlider.attr('slider-min-value'));
        var sliderMaxValue = parseFloat(newSlider.attr('slider-max-value'));
        var sliderStepValue = parseFloat(newSlider.attr('slider-step-value'));

        newSlider.slider({
            value: sliderValue,
            min: sliderMinValue,
            max: sliderMaxValue,
            step: sliderStepValue,
            change: function(e,ui){
                // Important! When slider stops moving make sure to trigger change event so Customizer knows it has to save the field
                $(this).parent().find('.customize-control-slider-value').trigger('change');
          }
        });
    });

    // Change the value of the input field as the slider is moved
    $('.slider').on('slide', function(event, ui) {
        $(this).parent().find('.customize-control-slider-value').val(ui.value);
    });

    // Reset slider and input field back to the default value
    $('.slider-reset').on('click', function() {
        var resetValue = $(this).attr('slider-reset-value');
        $(this).parent().find('.customize-control-slider-value').val(resetValue);
        $(this).parent().find('.slider').slider('value', resetValue);
    });

    // Update slider if the input field loses focus as it's most likely changed
    $('.customize-control-slider-value').blur(function() {
        var resetValue = $(this).val();
        var slider = $(this).parent().find('.slider');
        var sliderMinValue = parseInt(slider.attr('slider-min-value'));
        var sliderMaxValue = parseInt(slider.attr('slider-max-value'));

        // Make sure our manual input value doesn't exceed the minimum & maxmium values
        if(resetValue < sliderMinValue) {
            resetValue = sliderMinValue;
            $(this).val(resetValue);
        }
        if(resetValue > sliderMaxValue) {
            resetValue = sliderMaxValue;
            $(this).val(resetValue);
        }
        $(this).parent().find('.slider').slider('value', resetValue);
    });

});
