<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$root_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

// Require the mijireh library classes
require_once $root_dir . 'Rest.php';
require_once $root_dir . 'RestJSON.php';
require_once $root_dir . 'Model.php';
require_once $root_dir . 'Address.php';
require_once $root_dir . 'Item.php';
require_once $root_dir . 'Order.php';

class Mijireh_Exception extends Exception {}
class Mijireh_ClientError extends Mijireh_Exception {}         /* Status: 400-499 */
class Mijireh_BadRequest extends Mijireh_ClientError {}        /* Status: 400 */
class Mijireh_Unauthorized extends Mijireh_ClientError {}      /* Status: 401 */
class Mijireh_NotFound extends Mijireh_ClientError {}          /* Status: 404 */
class Mijireh_ServerError extends Mijireh_Exception {}         /* Status: 500-599 */
class Mijireh_InternalError extends Mijireh_ServerError {}     /* Status: 500 */

class Mijireh {

  /* Live server urls */
  public static $base_url = 'https://secure.mijireh.com/';
  public static $url      = 'https://secure.mijireh.com/api/1/';

  public static $access_key;

  /**
   * Return the job id of the slurp
   */
  public static function slurp($url, $page_id, $return_url) {
    $url_format = '/^(https?):\/\/'.                           // protocol
    '(([a-z0-9$_\.\+!\*\'\(\),;\?&=-]|%[0-9a-f]{2})+'.         // username
    '(:([a-z0-9$_\.\+!\*\'\(\),;\?&=-]|%[0-9a-f]{2})+)?'.      // password
    '@)?(?#'.                                                  // auth requires @
    ')((([a-z0-9][a-z0-9-]*[a-z0-9]\.)*'.                      // domain segments AND
    '[a-z][a-z0-9-]*[a-z0-9]'.                                 // top level domain  OR
    '|((\d|[1-9]\d|1\d{2}|2[0-4][0-9]|25[0-5])\.){3}'.
    '(\d|[1-9]\d|1\d{2}|2[0-4][0-9]|25[0-5])'.                 // IP address
    ')(:\d+)?'.                                                // port
    ')(((\/+([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)*'. // path
    '(\?([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)'.      // query string
    '?)?)?'.                                                   // path and query string optional
    '(#([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)?'.      // fragment
    '$/i';

    if(!preg_match($url_format, $url)) {
      throw new Mijireh_NotFound('Unable to slurp invalid URL: $url');
    }

    try {
      $rest = new Mijireh_Rest($url);
      $data = array(
        'url' => $url,
        'page_id' => $page_id,
        'return_url' => $return_url
      );
      $rest = new Mijireh_RestJSON(self::$url);
      $rest->setupAuth(self::$access_key, '');
      $result = $rest->post('slurps', $data);
      return $result['job_id'];
    }
    catch(Mijireh_Rest_Unauthorized $e) {
      throw new Mijireh_Unauthorized("Unauthorized. Please check your api access key");
    }
    catch(Mijireh_Rest_NotFound $e) {
      throw new Mijireh_NotFound("Mijireh resource not found: " . $rest->last_request['url']);
    }
    catch(Mijireh_Rest_ClientError $e) {
      throw new Mijireh_ClientError($e->getMessage());
    }
    catch(Mijireh_Rest_ServerError $e) {
      throw new Mijireh_ServerError($e->getMessage());
    }
    catch(Mijireh_Rest_UnknownResponse $e) {
      throw new Mijireh_Exception('Unable to slurp the URL: $url');
    }
  }

  /**
   * Return an array of store information
   */
  public static function get_store_info() {
    $rest = new Mijireh_RestJSON(self::$url);
    $rest->setupAuth(self::$access_key, '');
    try {
      $result = $rest->get('store');
      return $result;
    }
    catch(Mijireh_Rest_BadRequest $e) {
      throw new Mijireh_BadRequest($e->getMessage());
    }
    catch(Mijireh_Rest_Unauthorized $e) {
      throw new Mijireh_Unauthorized("Unauthorized. Please check your api access key");
    }
    catch(Mijireh_Rest_NotFound $e) {
      throw new Mijireh_NotFound("Mijireh resource not found: " . $rest->last_request['url']);
    }
    catch(Mijireh_Rest_ClientError $e) {
      throw new Mijireh_ClientError($e->getMessage());
    }
    catch(Mijireh_Rest_ServerError $e) {
      throw new Mijireh_ServerError($e->getMessage());
    }
  }

  public static function preview_checkout_link() {
    if(empty(Mijireh::$access_key)) {
      throw new Mijireh_Exception('Access key required to view checkout preview');
    }

    return self::$base_url . 'checkout/' . self::$access_key;
  }

}
