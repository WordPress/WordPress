/**
 * Slider Setting
 */
jQuery(window).load(function(){var transition_effect=travelify_slider_value.transition_effect;var transition_delay=travelify_slider_value.transition_delay;var transition_duration=travelify_slider_value.transition_duration;jQuery('.slider-cycle').cycle({fx:transition_effect,pager:'#controllers',activePagerClass:'active',timeout:transition_delay,speed:transition_duration,pause:1,pauseOnPagerHover:1,width:'100%',containerResize:0,fit:1,after:function(){jQuery(this).parent().css("height",jQuery(this).height())},cleartypeNoBg:true})});