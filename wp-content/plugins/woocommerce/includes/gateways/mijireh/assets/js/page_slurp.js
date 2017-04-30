(function($) {

  $('#page_slurp').click(function() {
    var page_id = $(this).attr('rel');
    var data = {
      action: 'page_slurp',
      page_id: page_id
    };

    $('#page_slurp').attr('disabled', 'disabled');
    $('#slurp_progress').show();
    $('#slurp_progress_bar').html('Starting up');

    $.post(ajaxurl, data, function(job_id) {
      // The job id is the id for the page slurp job or 0 if the slurp failed
      if(job_id.substring(0, 4) === 'http') {
        var msg = 'Your PHP configuration does not allow for Page Slurp from within WordPress. Please log into your Mijireh account and Slurp the page by pasting in the URL to this page as shown below.\
        \n\n' + job_id + '\n\nPlease set this page to be publicly accessible during the Slurp then set it back to private after the Slurp is complete.';
        alert(msg);
      }
      else {
        pusher = new Pusher('7dcd33b15307eb9be5fb');
        channel_name = 'slurp-' + job_id;
        channel = pusher.subscribe(channel_name);

        channel.bind('status_changed', function (data) {
          // console.log(data);
          if(data.level == 'info') {
            $('#slurp_progress_bar').html(data.message);
            $('#slurp_progress_bar').width(data.progress + '%');
          }

          if(data.progress == 100) {
            pusher.unsubscribe(channel_name);
            $('#slurp_progress').hide();
            $('#page_slurp').removeAttr('disabled');
          }
        });
      }

    })
    .error(function(response) {
      $('#slurp_progress').hide();
      $('#page_slurp').removeAttr('disabled');
      alert('Please make sure your Mijireh access key is correct');
    });

    return false;
  });

})(jQuery.noConflict());