<?php
	
	echo '<div class="rss-widget">';

		wp_widget_rss_output(array(
				'url' 			=> 'https://ultimatemember.com/blog/feed/',
				'title' 		=> 'Latest From Ultimate Member',
				'items'        	=> 4,
				'show_summary' 	=> 0,
				'show_author'  	=> 0,
				'show_date'    	=> 1,
		));

		echo "</div>";
		
	echo "<style type='text/css'>#um-metaboxes-mainbox-1 a.rsswidget {font-weight: 400}#um-metaboxes-mainbox-1 .rss-widget span.rss-date{ color: #777; margin-left: 12px;}</style>";

?>