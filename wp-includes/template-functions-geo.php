<?php

function get_Lat() {
    global $post;

   if ($post->post_lat != '') {
       return trim($post->post_lat);
   } else if(get_settings('use_default_geourl')) {
       return trim(get_settings('default_geourl_lat'));
   }

   return '';
}

function get_Lon() {
    global $post;

   if ($post->post_lon != '') {
       return trim($post->post_lon);
   } else if(get_settings('use_default_geourl')) {
       return trim(get_settings('default_geourl_lon'));
   }

   return '';
}

function print_Lat() {
    if(get_settings('use_geo_positions')) {
        if(get_Lat() > 0) {
            echo "".get_Lat()."N";
        } else {
            echo "".get_Lat()."S";
        }
    }
}

function print_Lon() {
    global $id, $postdata;
    if(get_settings('use_geo_positions')) {
        if(get_Lon() < 0) {
            $temp = get_Lon() * -1;
            echo "".$temp."W";
        } else {
            echo "".get_Lon()."E";
        }
    }
}

function print_PopUpScript() {
    echo "
<script type='text/javascript'>
<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!!  -->
function formHandler(form) {
  var URL = form.site.options[form.site.selectedIndex].value;
  if(URL != \".\") {
    popup = window.open(URL,\"MenuPopup\");
  }
}
</script> ";
}

function print_UrlPopNav() {
    $sites = array(
                   array('http://www.acme.com/mapper/?lat='.get_Lat().'&amp;long='.get_Lon().'&amp;scale=11&amp;theme=Image&amp;width=3&amp;height=2&amp;dot=Yes',
                         __('Acme Mapper')),
                   array('http://geourl.org/near/?lat='.get_Lat().'&amp;lon='.get_Lon().'&amp;dist=500',
                         __('GeoURLs near here')),
                   array('http://www.geocaching.com/seek/nearest.aspx?origin_lat='.get_Lat().'&amp;origin_long='.get_Lon().'&amp;dist=5',
                         __('Geocaches near here')),
                   array('http://www.mapquest.com/maps/map.adp?latlongtype=decimal&amp;latitude='.get_Lat().'&amp;longitude='.get_Lon(),
                         __('Mapquest map of this spot')),
                   array('http://www.sidebit.com/ProjectGeoURLMap.php?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         __('SideBit URL Map of this spot')),
                   array('http://confluence.org/confluence.php?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         __('Confluence.org near here')),
                   array('http://www.topozone.com/map.asp?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         __('Topozone near here')),
                   array('http://www.findu.com/cgi-bin/near.cgi?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         __('FindU near here')),
                   array('http://mapserver.maptech.com/api/espn/index.cfm?lat='.get_Lat().'&amp;lon='.get_Lon().'&amp;scale=100000&amp;zoom=50&amp;type=1&amp;icon=0&amp;&amp;scriptfile=http://mapserver.maptech.com/api/espn/index.cfm',
                         __('Maptech near here'))
                  );
    echo '<form action=""><div>
<select name="site" size="1" onchange="formHandler(this.form);" >'."\n";
    echo '<option value=".">' . sprintf(__("Sites referencing %s x %s"), get_Lat(), get_Lon()) . "</option>\n";
    foreach($sites as $site) {
        echo "\t".'<option value="'.$site[0].'">'.$site[1]."</option>\n";
    }
    echo '</select></div>
</form>'."\n";
}

function longitude_invalid() {
    if (get_Lon() == null) return true;
    if (get_Lon() > 360) return true;
    if (get_Lon() < -360) return true;
}

function print_AcmeMap_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.acme.com/mapper?lat=".get_Lat()."&amp;long=".get_Lon()."&amp;scale=11&amp;theme=Image&amp;width=3&amp;height=2&amp;dot=Yes";
}

function print_GeoURL_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://geourl.org/near/?lat=".get_Lat()."&amp;lon=".get_Lon()."&amp;dist=500";
}

function print_GeoCache_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.geocaching.com/seek/nearest.aspx?origin_lat=".get_Lat()."&amp;origin_long=".get_Lon()."&amp;dist=5";
}

function print_MapQuest_Url() {
    if (!get_settings('use_geo_positions')) return;

    if (longitude_invalid()) return;
    echo "http://www.mapquest.com/maps/map.adp?latlongtype=decimal&amp;latitude=".get_Lat()."&amp;longitude=".get_Lon();
}

function print_SideBit_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.sidebit.com/ProjectGeoURLMap.php?lat=".get_Lat()."&amp;lon=".get_Lon();
}

function print_DegreeConfluence_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://confluence.org/confluence.php?lat=".get_Lat()."&amp;lon=".get_Lon();
}

function print_TopoZone_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.topozone.com/map.asp?lat=".get_Lat()."&amp;lon=".get_Lon();
}

function print_FindU_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.findu.com/cgi-bin/near.cgi?lat=".get_Lat()."&amp;lon=".get_Lon()."&amp;scale=100000&amp;zoom=50&amp;type=1&amp;icon=0&amp;&amp;scriptfile=http://mapserver.maptech.com/api/espn/index.cfm";
}

function print_MapTech_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://mapserver.maptech.com/api/espn/index.cfm?lat=".get_Lat()."&amp;lon=".get_Lon();
}

?>
