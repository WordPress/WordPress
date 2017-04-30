<?php
echo "
<h4>Introduction Shortcode Use</h4>
<p>Use this shortcode to add the calendar with daily view: <b>[add_eventon_dv]</b><br/><br/>
You can also use the variable <b>month_incre</b> to show a different focus month just like the eventon calendar.<br/>
eg. <b>[add_eventon_dv month_incre=+3]</b> - this will show events a month with same date that is  3 months in advance.
</p>
<p>
The other new variable is <b>day_incre</b> will focus a different date for the daily view calendar.<br/>
eg. <b>[add_eventon_dv day_incre=+3]</b> - this will show events for a day that is 3 days in advance.
</p>
<p>
Other variables that work with this shortcode are <b>event_type, event_type_2, and event_count</b>. Also note you will see easy shortcode buttons for daily view in EventON shortcode popup in WYSIWYG text editor on pages.
</p>


<h4>Php template tags</h4>
<p>
&lt;?php<br/> if(function_exists(add_eventon_dv)){<br/>
add_eventon_dv(&#36;args);</br>
}?&gt;
</p>

";
?>