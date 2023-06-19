// Toggle button control for collapsible elements
jQuery(".toggle-btn").click(function () {
  $header = jQuery(this);
  $content = $header.next();
  $content.slideToggle(250, "swing", function () {
      jQuery("span.toggle-indicator", $header).toggleClass('dashicons-arrow-down');
  });
});

// Attach an event to export buttons
jQuery("#exportSettings").click(function(){exportSettings();});