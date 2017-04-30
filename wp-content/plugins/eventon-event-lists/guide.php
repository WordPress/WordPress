<?php
echo "
<p>Use this shortcode to add the calendar with event list: <b>[add_eventon_el]</b></p>

<p><b><u>Shortcode and PHP Variable guide</u></b></p>

<p><b>cal_id</b> - Unique calendar ID</p>
<p><b>el_title</b> - Calendar title text to show above event list</p>
<p><b>el_type</b> - Event list type to generate</p>
<p><b>pec</b> - Abbreviation for past event cut-off. Meaning, when you like the calendar events to be cut-off for event lists.</p>
<p><b>number_of_months</b> - Number of months to look for events for the list.</p>
<p><b>event_count</b> - Number of events to limit the event list to.</p>
<p><b>event_order</b> - Events order for the list. Either ascending or descending.</p>
<p><b>etop_month</b> - Show 3 letter event month name on eventTop.</p>
<p>Use eventON shortcode generator to generate event lists shortcodes easily</p>

<br/>
<p>
<b>PHP template tags</b><br/>
&lt;?php<br/> if(function_exists(add_eventon_el)){<br/>
add_eventon_el(&#36;args);</br>
}?&gt;
</p>

";
?>