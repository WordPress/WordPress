<?php
define('W3TC_CDN_EDGECAST_PURGE_URL', 'http://api.acdn.att.com/v2/mcc/customers/%s/edge/purge');
w3_require_once(W3TC_LIB_W3_DIR . '/Cdn/Mirror/Edgecast.php');

/**
 * Class W3_Cdn_Mirror_ATT
 */
class W3_Cdn_Mirror_ATT extends W3_Cdn_Mirror_Edgecast {

}
