<?php
/**
 * TimThumb by Ben Gillbanks and Mark Maunder
 * Based on work done by Tim McDaniels and Darren Hoyt
 * http://code.google.com/p/timthumb/
 * 
 * GNU General Public License, version 2
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Examples and documentation available on the project homepage
 * http://www.binarymoon.co.uk/projects/timthumb/
 * 
 * $Rev$
 */

/*
 * --- TimThumb CONFIGURATION ---
 * To edit the configs it is best to create a file called timthumb-config.php
 * and define variables you want to customize in there. It will automatically be
 * loaded by timthumb. This will save you having to re-edit these variables
 * everytime you download a new version
*/
define ('VERSION', '2.8.13');                                   // Version of this script 
//Load a config file if it exists. Otherwise, use the values below
if( file_exists(dirname(__FILE__) . '/timthumb-config.php'))  require_once('timthumb-config.php');
if(! defined('DEBUG_ON') )          define ('DEBUG_ON', false);               // Enable debug logging to web server error log (STDERR)
if(! defined('DEBUG_LEVEL') )       define ('DEBUG_LEVEL', 1);                // Debug level 1 is less noisy and 3 is the most noisy
if(! defined('MEMORY_LIMIT') )        define ('MEMORY_LIMIT', '30M');             // Set PHP memory limit
if(! defined('BLOCK_EXTERNAL_LEECHERS') )   define ('BLOCK_EXTERNAL_LEECHERS', false);        // If the image or webshot is being loaded on an external site, display a red "No Hotlinking" gif.
if(! defined('DISPLAY_ERROR_MESSAGES') )  define ('DISPLAY_ERROR_MESSAGES', true);        // Display error messages. Set to false to turn off errors (good for production websites)
//Image fetching and caching
if(! defined('ALLOW_EXTERNAL') )      define ('ALLOW_EXTERNAL', FALSE);            // Allow image fetching from external websites. Will check against ALLOWED_SITES if ALLOW_ALL_EXTERNAL_SITES is false
if(! defined('ALLOW_ALL_EXTERNAL_SITES') )  define ('ALLOW_ALL_EXTERNAL_SITES', false);       // Less secure. 
if(! defined('FILE_CACHE_ENABLED') )    define ('FILE_CACHE_ENABLED', TRUE);          // Should we store resized/modified images on disk to speed things up?
if(! defined('FILE_CACHE_TIME_BETWEEN_CLEANS')) define ('FILE_CACHE_TIME_BETWEEN_CLEANS', 86400); // How often the cache is cleaned 

if(! defined('FILE_CACHE_MAX_FILE_AGE') )   define ('FILE_CACHE_MAX_FILE_AGE', 86400);        // How old does a file have to be to be deleted from the cache
if(! defined('FILE_CACHE_SUFFIX') )     define ('FILE_CACHE_SUFFIX', '.timthumb.txt');      // What to put at the end of all files in the cache directory so we can identify them
if(! defined('FILE_CACHE_PREFIX') )     define ('FILE_CACHE_PREFIX', 'timthumb');       // What to put at the beg of all files in the cache directory so we can identify them
if(! defined('FILE_CACHE_DIRECTORY') )    define ('FILE_CACHE_DIRECTORY', './cache');       // Directory where images are cached. Left blank it will use the system temporary directory (which is better for security)
if(! defined('MAX_FILE_SIZE') )       define ('MAX_FILE_SIZE', 10485760);           // 10 Megs is 10485760. This is the max internal or external file size that we'll process.  
if(! defined('CURL_TIMEOUT') )        define ('CURL_TIMEOUT', 20);              // Timeout duration for Curl. This only applies if you have Curl installed and aren't using PHP's default URL fetching mechanism.
if(! defined('WAIT_BETWEEN_FETCH_ERRORS') ) define ('WAIT_BETWEEN_FETCH_ERRORS', 3600);       // Time to wait between errors fetching remote file

//Browser caching
if(! defined('BROWSER_CACHE_MAX_AGE') )   define ('BROWSER_CACHE_MAX_AGE', 864000);       // Time to cache in the browser
if(! defined('BROWSER_CACHE_DISABLE') )   define ('BROWSER_CACHE_DISABLE', false);        // Use for testing if you want to disable all browser caching

//Image size and defaults
if(! defined('MAX_WIDTH') )         define ('MAX_WIDTH', 1500);               // Maximum image width
if(! defined('MAX_HEIGHT') )        define ('MAX_HEIGHT', 1500);              // Maximum image height
if(! defined('NOT_FOUND_IMAGE') )     define ('NOT_FOUND_IMAGE', '');             // Image to serve if any 404 occurs 
if(! defined('ERROR_IMAGE') )       define ('ERROR_IMAGE', '');               // Image to serve if an error occurs instead of showing error message 
if(! defined('PNG_IS_TRANSPARENT') )    define ('PNG_IS_TRANSPARENT', FALSE);         // Define if a png image should have a transparent background color. Use False value if you want to display a custom coloured canvas_colour 
if(! defined('DEFAULT_Q') )         define ('DEFAULT_Q', 90);               // Default image quality. Allows overrid in timthumb-config.php
if(! defined('DEFAULT_ZC') )        define ('DEFAULT_ZC', 1);               // Default zoom/crop setting. Allows overrid in timthumb-config.php
if(! defined('DEFAULT_F') )         define ('DEFAULT_F', '');               // Default image filters. Allows overrid in timthumb-config.php
if(! defined('DEFAULT_S') )         define ('DEFAULT_S', 0);                // Default sharpen value. Allows overrid in timthumb-config.php
if(! defined('DEFAULT_CC') )        define ('DEFAULT_CC', 'ffffff');            // Default canvas colour. Allows overrid in timthumb-config.php
if(! defined('DEFAULT_WIDTH') )       define ('DEFAULT_WIDTH', 100);              // Default thumbnail width. Allows overrid in timthumb-config.php
if(! defined('DEFAULT_HEIGHT') )      define ('DEFAULT_HEIGHT', 100);             // Default thumbnail height. Allows overrid in timthumb-config.php

/**
 * Additional Parameters:
 * LOCAL_FILE_BASE_DIRECTORY = Override the DOCUMENT_ROOT. This is best used in timthumb-config.php
 */

//Image compression is enabled if either of these point to valid paths

//These are now disabled by default because the file sizes of PNGs (and GIFs) are much smaller than we used to generate. 
//They only work for PNGs. GIFs and JPEGs are not affected.
if(! defined('OPTIPNG_ENABLED') )     define ('OPTIPNG_ENABLED', false);  
if(! defined('OPTIPNG_PATH') )      define ('OPTIPNG_PATH', '/usr/bin/optipng'); //This will run first because it gives better compression than pngcrush. 
if(! defined('PNGCRUSH_ENABLED') )    define ('PNGCRUSH_ENABLED', false); 
if(! defined('PNGCRUSH_PATH') )     define ('PNGCRUSH_PATH', '/usr/bin/pngcrush'); //This will only run if OPTIPNG_PATH is not set or is not valid

/*
  -------====Website Screenshots configuration - BETA====-------
  
  If you just want image thumbnails and don't want website screenshots, you can safely leave this as is.  
  
  If you would like to get website screenshots set up, you will need root access to your own server.

  Enable ALLOW_ALL_EXTERNAL_SITES so you can fetch any external web page. This is more secure now that we're using a non-web folder for cache.
  Enable BLOCK_EXTERNAL_LEECHERS so that your site doesn't generate thumbnails for the whole Internet.

  Instructions to get website screenshots enabled on Ubuntu Linux:

  1. Install Xvfb with the following command: sudo apt-get install subversion libqt4-webkit libqt4-dev g++ xvfb
  2. Go to a directory where you can download some code
  3. Check-out the latest version of CutyCapt with the following command: svn co https://cutycapt.svn.sourceforge.net/svnroot/cutycapt
  4. Compile CutyCapt by doing: cd cutycapt/CutyCapt
  5. qmake
  6. make
  7. cp CutyCapt /usr/local/bin/
  8. Test it by running: xvfb-run --server-args="-screen 0, 1024x768x24" CutyCapt --url="http://markmaunder.com/" --out=test.png
  9. If you get a file called test.png with something in it, it probably worked. Now test the script by accessing it as follows:
  10. http://yoursite.com/path/to/timthumb.php?src=http://markmaunder.com/&webshot=1

  Notes on performance: 
  The first time a webshot loads, it will take a few seconds.
  From then on it uses the regular timthumb caching mechanism with the configurable options above
  and loading will be very fast.

  --ADVANCED USERS ONLY--
  If you'd like a slight speedup (about 25%) and you know Linux, you can run the following command which will keep Xvfb running in the background.
  nohup Xvfb :100 -ac -nolisten tcp -screen 0, 1024x768x24 > /dev/null 2>&1 &
  Then set WEBSHOT_XVFB_RUNNING = true below. This will save your server having to fire off a new Xvfb server and shut it down every time a new shot is generated. 
  You will need to take responsibility for keeping Xvfb running in case it crashes. (It seems pretty stable)
  You will also need to take responsibility for server security if you're running Xvfb as root. 


*/
if(! defined('WEBSHOT_ENABLED') )   define ('WEBSHOT_ENABLED', false);      //Beta feature. Adding webshot=1 to your query string will cause the script to return a browser screenshot rather than try to fetch an image.
if(! defined('WEBSHOT_CUTYCAPT') )  define ('WEBSHOT_CUTYCAPT', '/usr/local/bin/CutyCapt'); //The path to CutyCapt. 
if(! defined('WEBSHOT_XVFB') )    define ('WEBSHOT_XVFB', '/usr/bin/xvfb-run');   //The path to the Xvfb server
if(! defined('WEBSHOT_SCREEN_X') )  define ('WEBSHOT_SCREEN_X', '1024');      //1024 works ok
if(! defined('WEBSHOT_SCREEN_Y') )  define ('WEBSHOT_SCREEN_Y', '768');     //768 works ok
if(! defined('WEBSHOT_COLOR_DEPTH') )   define ('WEBSHOT_COLOR_DEPTH', '24');     //I haven't tested anything besides 24
if(! defined('WEBSHOT_IMAGE_FORMAT') )  define ('WEBSHOT_IMAGE_FORMAT', 'png');     //png is about 2.5 times the size of jpg but is a LOT better quality
if(! defined('WEBSHOT_TIMEOUT') )   define ('WEBSHOT_TIMEOUT', '20');     //Seconds to wait for a webshot
if(! defined('WEBSHOT_USER_AGENT') )  define ('WEBSHOT_USER_AGENT', "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.9.2.18) Gecko/20110614 Firefox/3.6.18"); //I hate to do this, but a non-browser robot user agent might not show what humans see. So we pretend to be Firefox
if(! defined('WEBSHOT_JAVASCRIPT_ON') ) define ('WEBSHOT_JAVASCRIPT_ON', true);     //Setting to false might give you a slight speedup and block ads. But it could cause other issues.
if(! defined('WEBSHOT_JAVA_ON') )   define ('WEBSHOT_JAVA_ON', false);      //Have only tested this as fase
if(! defined('WEBSHOT_PLUGINS_ON') )  define ('WEBSHOT_PLUGINS_ON', true);      //Enable flash and other plugins
if(! defined('WEBSHOT_PROXY') )   define ('WEBSHOT_PROXY', '');       //In case you're behind a proxy server. 
if(! defined('WEBSHOT_XVFB_RUNNING') )  define ('WEBSHOT_XVFB_RUNNING', false);     //ADVANCED: Enable this if you've got Xvfb running in the background.


// If ALLOW_EXTERNAL is true and ALLOW_ALL_EXTERNAL_SITES is false, then external images will only be fetched from these domains and their subdomains. 
if(! isset($ALLOWED_SITES)){
  $ALLOWED_SITES = array ();
}
// -------------------------------------------------------------
// -------------- STOP EDITING CONFIGURATION HERE --------------
// -------------------------------------------------------------

timthumb::start();

class timthumb {
  protected $src = "";
  protected $is404 = false;
  protected $docRoot = "";
  protected $lastURLError = false;
  protected $localImage = "";
  protected $localImageMTime = 0;
  protected $url = false;
  protected $myHost = "";
  protected $isURL = false;
  protected $cachefile = '';
  protected $errors = array();
  protected $toDeletes = array();
  protected $cacheDirectory = '';
  protected $startTime = 0;
  protected $lastBenchTime = 0;
  protected $cropTop = false;
  protected $salt = "";
  protected $fileCacheVersion = 1; //Generally if timthumb.php is modifed (upgraded) then the salt changes and all cache files are recreated. This is a backup mechanism to force regen.
  protected $filePrependSecurityBlock = "<?php die('Execution denied!'); //"; //Designed to have three letter mime type, space, question mark and greater than symbol appended. 6 bytes total.
  protected static $curlDataWritten = 0;
  protected static $curlFH = false;
  public static function start(){
    $tim = new timthumb();
    $tim->handleErrors();
    $tim->securityChecks();
    if($tim->tryBrowserCache()){
      exit(0);
    }
    $tim->handleErrors();
    if(FILE_CACHE_ENABLED && $tim->tryServerCache()){
      exit(0);
    }
    $tim->handleErrors();
    $tim->run();
    $tim->handleErrors();
    exit(0);
  }
  public function __construct(){
    global $ALLOWED_SITES;
    $this->startTime = microtime(true);
    date_default_timezone_set('UTC');
    $this->debug(1, "Starting new request from " . $this->getIP() . " to " . $_SERVER['REQUEST_URI']);
    $this->calcDocRoot();
    //On windows systems I'm assuming fileinode returns an empty string or a number that doesn't change. Check this.
    $this->salt = @filemtime(__FILE__) . '-' . @fileinode(__FILE__);
    $this->debug(3, "Salt is: " . $this->salt);
    if(FILE_CACHE_DIRECTORY){
      if(! is_dir(FILE_CACHE_DIRECTORY)){
        @mkdir(FILE_CACHE_DIRECTORY);
        if(! is_dir(FILE_CACHE_DIRECTORY)){
          $this->error("Could not create the file cache directory.");
          return false;
        }
      }
      $this->cacheDirectory = FILE_CACHE_DIRECTORY;
      if (!touch($this->cacheDirectory . '/index.html')) {
        $this->error("Could not create the index.html file - to fix this create an empty file named index.html file in the cache directory.");
      }
    } else {
      $this->cacheDirectory = sys_get_temp_dir();
    }
    //Clean the cache before we do anything because we don't want the first visitor after FILE_CACHE_TIME_BETWEEN_CLEANS expires to get a stale image. 
    $this->cleanCache();
    
    $this->myHost = preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']);
    $this->src = $this->param('src');
    $this->url = parse_url($this->src);
    $this->src = preg_replace('/https?:\/\/(?:www\.)?' . $this->myHost . '/i', '', $this->src);
    
    if(strlen($this->src) <= 3){
      $this->error("No image specified");
      return false;
    }
    if(BLOCK_EXTERNAL_LEECHERS && array_key_exists('HTTP_REFERER', $_SERVER) && (! preg_match('/^https?:\/\/(?:www\.)?' . $this->myHost . '(?:$|\/)/i', $_SERVER['HTTP_REFERER']))){
      // base64 encoded red image that says 'no hotlinkers'
      // nothing to worry about! :)
      $imgData = base64_decode("R0lGODlhUAAMAIAAAP8AAP///yH5BAAHAP8ALAAAAABQAAwAAAJpjI+py+0Po5y0OgAMjjv01YUZ\nOGplhWXfNa6JCLnWkXplrcBmW+spbwvaVr/cDyg7IoFC2KbYVC2NQ5MQ4ZNao9Ynzjl9ScNYpneb\nDULB3RP6JuPuaGfuuV4fumf8PuvqFyhYtjdoeFgAADs=");
      header('Content-Type: image/gif');
      header('Content-Length: ' . strlen($imgData));
      header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
      header("Pragma: no-cache");
      header('Expires: ' . gmdate ('D, d M Y H:i:s', time()));
      echo $imgData;
      return false;
      exit(0);
    }
    if(preg_match('/^https?:\/\/[^\/]+/i', $this->src)){
      $this->debug(2, "Is a request for an external URL: " . $this->src);
      $this->isURL = true;
    } else {
      $this->debug(2, "Is a request for an internal file: " . $this->src);
    }
    if($this->isURL && (! ALLOW_EXTERNAL)){
      $this->error("You are not allowed to fetch images from an external website.");
      return false;
    }
    if($this->isURL){
      if(ALLOW_ALL_EXTERNAL_SITES){
        $this->debug(2, "Fetching from all external sites is enabled.");
      } else {
        $this->debug(2, "Fetching only from selected external sites is enabled.");
        $allowed = false;
        foreach($ALLOWED_SITES as $site){
          if ((strtolower(substr($this->url['host'],-strlen($site)-1)) === strtolower(".$site")) || (strtolower($this->url['host'])===strtolower($site))) {
            $this->debug(3, "URL hostname {$this->url['host']} matches $site so allowing.");
            $allowed = true;
          }
        }
        if(! $allowed){
          return $this->error("You may not fetch images from that site. To enable this site in timthumb, you can either add it to \$ALLOWED_SITES and set ALLOW_EXTERNAL=true. Or you can set ALLOW_ALL_EXTERNAL_SITES=true, depending on your security needs.");
        }
      }
    }

    $cachePrefix = ($this->isURL ? '_ext_' : '_int_');
    if($this->isURL){
      $arr = explode('&', $_SERVER ['QUERY_STRING']);
      asort($arr);
      $this->cachefile = $this->cacheDirectory . '/' . FILE_CACHE_PREFIX . $cachePrefix . md5($this->salt . implode('', $arr) . $this->fileCacheVersion) . FILE_CACHE_SUFFIX;
    } else {
      $this->localImage = $this->getLocalImagePath($this->src);
      if(! $this->localImage){
        $this->debug(1, "Could not find the local image: {$this->localImage}");
        $this->error("Could not find the internal image you specified.");
        $this->set404();
        return false;
      }
      $this->debug(1, "Local image path is {$this->localImage}");
      $this->localImageMTime = @filemtime($this->localImage);
      //We include the mtime of the local file in case in changes on disk.
      $this->cachefile = $this->cacheDirectory . '/' . FILE_CACHE_PREFIX . $cachePrefix . md5($this->salt . $this->localImageMTime . $_SERVER ['QUERY_STRING'] . $this->fileCacheVersion) . FILE_CACHE_SUFFIX;
    }
    $this->debug(2, "Cache file is: " . $this->cachefile);

    return true;
  }
  public function __destruct(){
    foreach($this->toDeletes as $del){
      $this->debug(2, "Deleting temp file $del");
      @unlink($del);
    }
  }
  public function run(){
    if($this->isURL){
      if(! ALLOW_EXTERNAL){
        $this->debug(1, "Got a request for an external image but ALLOW_EXTERNAL is disabled so returning error msg.");
        $this->error("You are not allowed to fetch images from an external website.");
        return false;
      }
      $this->debug(3, "Got request for external image. Starting serveExternalImage.");
      if($this->param('webshot')){
        if(WEBSHOT_ENABLED){
          $this->debug(3, "webshot param is set, so we're going to take a webshot.");
          $this->serveWebshot();
        } else {
          $this->error("You added the webshot parameter but webshots are disabled on this server. You need to set WEBSHOT_ENABLED == true to enable webshots.");
        }
      } else {
        $this->debug(3, "webshot is NOT set so we're going to try to fetch a regular image.");
        $this->serveExternalImage();

      }
    } else {
      $this->debug(3, "Got request for internal image. Starting serveInternalImage()");
      $this->serveInternalImage();
    }
    return true;
  }
  protected function handleErrors(){
    if($this->haveErrors()){ 
      if(NOT_FOUND_IMAGE && $this->is404()){
        if($this->serveImg(NOT_FOUND_IMAGE)){
          exit(0);
        } else {
          $this->error("Additionally, the 404 image that is configured could not be found or there was an error serving it.");
        }
      }
      if(ERROR_IMAGE){
        if($this->serveImg(ERROR_IMAGE)){
          exit(0);
        } else {
          $this->error("Additionally, the error image that is configured could not be found or there was an error serving it.");
        }
      }
      $this->serveErrors(); 
      exit(0); 
    }
    return false;
  }
  protected function tryBrowserCache(){
    if(BROWSER_CACHE_DISABLE){ $this->debug(3, "Browser caching is disabled"); return false; }
    if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ){
      $this->debug(3, "Got a conditional get");
      $mtime = false;
      //We've already checked if the real file exists in the constructor
      if(! is_file($this->cachefile)){
        //If we don't have something cached, regenerate the cached image.
        return false;
      }
      if($this->localImageMTime){
        $mtime = $this->localImageMTime;
        $this->debug(3, "Local real file's modification time is $mtime");
      } else if(is_file($this->cachefile)){ //If it's not a local request then use the mtime of the cached file to determine the 304
        $mtime = @filemtime($this->cachefile);
        $this->debug(3, "Cached file's modification time is $mtime");
      }
      if(! $mtime){ return false; }

      $iftime = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
      $this->debug(3, "The conditional get's if-modified-since unixtime is $iftime");
      if($iftime < 1){
        $this->debug(3, "Got an invalid conditional get modified since time. Returning false.");
        return false;
      }
      if($iftime < $mtime){ //Real file or cache file has been modified since last request, so force refetch.
        $this->debug(3, "File has been modified since last fetch.");
        return false;
      } else { //Otherwise serve a 304
        $this->debug(3, "File has not been modified since last get, so serving a 304.");
        header ($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
        $this->debug(1, "Returning 304 not modified");
        return true;
      }
    }
    return false;
  }
  protected function tryServerCache(){
    $this->debug(3, "Trying server cache");
    if(file_exists($this->cachefile)){
      $this->debug(3, "Cachefile {$this->cachefile} exists");
      if($this->isURL){
        $this->debug(3, "This is an external request, so checking if the cachefile is empty which means the request failed previously.");
        if(filesize($this->cachefile) < 1){
          $this->debug(3, "Found an empty cachefile indicating a failed earlier request. Checking how old it is.");
          //Fetching error occured previously
          if(time() - @filemtime($this->cachefile) > WAIT_BETWEEN_FETCH_ERRORS){
            $this->debug(3, "File is older than " . WAIT_BETWEEN_FETCH_ERRORS . " seconds. Deleting and returning false so app can try and load file.");
            @unlink($this->cachefile);
            return false; //to indicate we didn't serve from cache and app should try and load
          } else {
            $this->debug(3, "Empty cachefile is still fresh so returning message saying we had an error fetching this image from remote host.");
            $this->set404();
            $this->error("An error occured fetching image.");
            return false; 
          }
        }
      } else {
        $this->debug(3, "Trying to serve cachefile {$this->cachefile}");
      }
      if($this->serveCacheFile()){
        $this->debug(3, "Succesfully served cachefile {$this->cachefile}");
        return true;
      } else {
        $this->debug(3, "Failed to serve cachefile {$this->cachefile} - Deleting it from cache.");
        //Image serving failed. We can't retry at this point, but lets remove it from cache so the next request recreates it
        @unlink($this->cachefile);
        return true;
      }
    }
  }
  protected function error($err){
    $this->debug(3, "Adding error message: $err");
    $this->errors[] = $err;
    return false;

  }
  protected function haveErrors(){
    if(sizeof($this->errors) > 0){
      return true;
    }
    return false;
  }
  protected function serveErrors(){
    header ($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
    if ( ! DISPLAY_ERROR_MESSAGES ) {
      return;
    }
    $html = '<ul>';
    foreach($this->errors as $err){
      $html .= '<li>' . htmlentities($err) . '</li>';
    }
    $html .= '</ul>';
    echo '<h1>A TimThumb error has occured</h1>The following error(s) occured:<br />' . $html . '<br />';
    echo '<br />Query String : ' . htmlentities( $_SERVER['QUERY_STRING'], ENT_QUOTES );
    echo '<br />TimThumb version : ' . VERSION . '</pre>';
  }
  protected function serveInternalImage(){
    $this->debug(3, "Local image path is $this->localImage");
    if(! $this->localImage){
      $this->sanityFail("localImage not set after verifying it earlier in the code.");
      return false;
    }
    $fileSize = filesize($this->localImage);
    if($fileSize > MAX_FILE_SIZE){
      $this->error("The file you specified is greater than the maximum allowed file size.");
      return false;
    }
    if($fileSize <= 0){
      $this->error("The file you specified is <= 0 bytes.");
      return false;
    }
    $this->debug(3, "Calling processImageAndWriteToCache() for local image.");
    if($this->processImageAndWriteToCache($this->localImage)){
      $this->serveCacheFile();
      return true;
    } else { 
      return false;
    }
  }
  protected function cleanCache(){
    if (FILE_CACHE_TIME_BETWEEN_CLEANS < 0) {
      return;
    }
    $this->debug(3, "cleanCache() called");
    $lastCleanFile = $this->cacheDirectory . '/timthumb_cacheLastCleanTime.touch';
    
    //If this is a new timthumb installation we need to create the file
    if(! is_file($lastCleanFile)){
      $this->debug(1, "File tracking last clean doesn't exist. Creating $lastCleanFile");
      if (!touch($lastCleanFile)) {
        $this->error("Could not create cache clean timestamp file.");
      }
      return;
    }
    if(@filemtime($lastCleanFile) < (time() - FILE_CACHE_TIME_BETWEEN_CLEANS) ){ //Cache was last cleaned more than 1 day ago
      $this->debug(1, "Cache was last cleaned more than " . FILE_CACHE_TIME_BETWEEN_CLEANS . " seconds ago. Cleaning now.");
      // Very slight race condition here, but worst case we'll have 2 or 3 servers cleaning the cache simultaneously once a day.
      if (!touch($lastCleanFile)) {
        $this->error("Could not create cache clean timestamp file.");
      }
      $files = glob($this->cacheDirectory . '/*' . FILE_CACHE_SUFFIX);
      if ($files) {
        $timeAgo = time() - FILE_CACHE_MAX_FILE_AGE;
        foreach($files as $file){
          if(@filemtime($file) < $timeAgo){
            $this->debug(3, "Deleting cache file $file older than max age: " . FILE_CACHE_MAX_FILE_AGE . " seconds");
            @unlink($file);
          }
        }
      }
      return true;
    } else {
      $this->debug(3, "Cache was cleaned less than " . FILE_CACHE_TIME_BETWEEN_CLEANS . " seconds ago so no cleaning needed.");
    }
    return false;
  }
  protected function processImageAndWriteToCache($localImage){
    $sData = getimagesize($localImage);
    $origType = $sData[2];
    $mimeType = $sData['mime'];

    $this->debug(3, "Mime type of image is $mimeType");
    if(! preg_match('/^image\/(?:gif|jpg|jpeg|png)$/i', $mimeType)){
      return $this->error("The image being resized is not a valid gif, jpg or png.");
    }

    if (!function_exists ('imagecreatetruecolor')) {
        return $this->error('GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library');
    }

    if (function_exists ('imagefilter') && defined ('IMG_FILTER_NEGATE')) {
      $imageFilters = array (
        1 => array (IMG_FILTER_NEGATE, 0),
        2 => array (IMG_FILTER_GRAYSCALE, 0),
        3 => array (IMG_FILTER_BRIGHTNESS, 1),
        4 => array (IMG_FILTER_CONTRAST, 1),
        5 => array (IMG_FILTER_COLORIZE, 4),
        6 => array (IMG_FILTER_EDGEDETECT, 0),
        7 => array (IMG_FILTER_EMBOSS, 0),
        8 => array (IMG_FILTER_GAUSSIAN_BLUR, 0),
        9 => array (IMG_FILTER_SELECTIVE_BLUR, 0),
        10 => array (IMG_FILTER_MEAN_REMOVAL, 0),
        11 => array (IMG_FILTER_SMOOTH, 0),
      );
    }

    // get standard input properties    
    $new_width =  (int) abs ($this->param('w', 0));
    $new_height = (int) abs ($this->param('h', 0));
    $zoom_crop = (int) $this->param('zc', DEFAULT_ZC);
    $quality = (int) abs ($this->param('q', DEFAULT_Q));
    $align = $this->cropTop ? 't' : $this->param('a', 'c');
    $filters = $this->param('f', DEFAULT_F);
    $sharpen = (bool) $this->param('s', DEFAULT_S);
    $canvas_color = $this->param('cc', DEFAULT_CC);
    $canvas_trans = (bool) $this->param('ct', '1');

    // set default width and height if neither are set already
    if ($new_width == 0 && $new_height == 0) {
        $new_width = (int) DEFAULT_WIDTH;
        $new_height = (int) DEFAULT_HEIGHT;
    }

    // ensure size limits can not be abused
    $new_width = min ($new_width, MAX_WIDTH);
    $new_height = min ($new_height, MAX_HEIGHT);

    // set memory limit to be able to have enough space to resize larger images
    $this->setMemoryLimit();

    // open the existing image
    $image = $this->openImage ($mimeType, $localImage);
    if ($image === false) {
      return $this->error('Unable to open image.');
    }

    // Get original width and height
    $width = imagesx ($image);
    $height = imagesy ($image);
    $origin_x = 0;
    $origin_y = 0;

    // generate new w/h if not provided
    if ($new_width && !$new_height) {
      $new_height = floor ($height * ($new_width / $width));
    } else if ($new_height && !$new_width) {
      $new_width = floor ($width * ($new_height / $height));
    }

    // scale down and add borders
    if ($zoom_crop == 3) {

      $final_height = $height * ($new_width / $width);

      if ($final_height > $new_height) {
        $new_width = $width * ($new_height / $height);
      } else {
        $new_height = $final_height;
      }

    }

    // create a new true color image
    $canvas = imagecreatetruecolor ($new_width, $new_height);
    imagealphablending ($canvas, false);

    if (strlen($canvas_color) == 3) { //if is 3-char notation, edit string into 6-char notation
      $canvas_color =  str_repeat(substr($canvas_color, 0, 1), 2) . str_repeat(substr($canvas_color, 1, 1), 2) . str_repeat(substr($canvas_color, 2, 1), 2); 
    } else if (strlen($canvas_color) != 6) {
      $canvas_color = DEFAULT_CC; // on error return default canvas color
    }

    $canvas_color_R = hexdec (substr ($canvas_color, 0, 2));
    $canvas_color_G = hexdec (substr ($canvas_color, 2, 2));
    $canvas_color_B = hexdec (substr ($canvas_color, 4, 2));

    // Create a new transparent color for image
      // If is a png and PNG_IS_TRANSPARENT is false then remove the alpha transparency 
    // (and if is set a canvas color show it in the background)
    if(preg_match('/^image\/png$/i', $mimeType) && !PNG_IS_TRANSPARENT && $canvas_trans){ 
      $color = imagecolorallocatealpha ($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 127);   
    }else{
      $color = imagecolorallocatealpha ($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 0);
    }


    // Completely fill the background of the new image with allocated color.
    imagefill ($canvas, 0, 0, $color);

    // scale down and add borders
    if ($zoom_crop == 2) {

      $final_height = $height * ($new_width / $width);

      if ($final_height > $new_height) {

        $origin_x = $new_width / 2;
        $new_width = $width * ($new_height / $height);
        $origin_x = round ($origin_x - ($new_width / 2));

      } else {

        $origin_y = $new_height / 2;
        $new_height = $final_height;
        $origin_y = round ($origin_y - ($new_height / 2));

      }

    }

    // Restore transparency blending
    imagesavealpha ($canvas, true);

    if ($zoom_crop > 0) {

      $src_x = $src_y = 0;
      $src_w = $width;
      $src_h = $height;

      $cmp_x = $width / $new_width;
      $cmp_y = $height / $new_height;

      // calculate x or y coordinate and width or height of source
      if ($cmp_x > $cmp_y) {

        $src_w = round ($width / $cmp_x * $cmp_y);
        $src_x = round (($width - ($width / $cmp_x * $cmp_y)) / 2);

      } else if ($cmp_y > $cmp_x) {

        $src_h = round ($height / $cmp_y * $cmp_x);
        $src_y = round (($height - ($height / $cmp_y * $cmp_x)) / 2);

      }

      // positional cropping!
      if ($align) {
        if (strpos ($align, 't') !== false) {
          $src_y = 0;
        }
        if (strpos ($align, 'b') !== false) {
          $src_y = $height - $src_h;
        }
        if (strpos ($align, 'l') !== false) {
          $src_x = 0;
        }
        if (strpos ($align, 'r') !== false) {
          $src_x = $width - $src_w;
        }
      }

      imagecopyresampled ($canvas, $image, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);

    } else {

      // copy and resize part of an image with resampling
      imagecopyresampled ($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    }

    if ($filters != '' && function_exists ('imagefilter') && defined ('IMG_FILTER_NEGATE')) {
      // apply filters to image
      $filterList = explode ('|', $filters);
      foreach ($filterList as $fl) {

        $filterSettings = explode (',', $fl);
        if (isset ($imageFilters[$filterSettings[0]])) {

          for ($i = 0; $i < 4; $i ++) {
            if (!isset ($filterSettings[$i])) {
              $filterSettings[$i] = null;
            } else {
              $filterSettings[$i] = (int) $filterSettings[$i];
            }
          }

          switch ($imageFilters[$filterSettings[0]][1]) {

            case 1:

              imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1]);
              break;

            case 2:

              imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2]);
              break;

            case 3:

              imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2], $filterSettings[3]);
              break;

            case 4:

              imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2], $filterSettings[3], $filterSettings[4]);
              break;

            default:

              imagefilter ($canvas, $imageFilters[$filterSettings[0]][0]);
              break;

          }
        }
      }
    }

    // sharpen image
    if ($sharpen && function_exists ('imageconvolution')) {

      $sharpenMatrix = array (
          array (-1,-1,-1),
          array (-1,16,-1),
          array (-1,-1,-1),
          );

      $divisor = 8;
      $offset = 0;

      imageconvolution ($canvas, $sharpenMatrix, $divisor, $offset);

    }
    //Straight from Wordpress core code. Reduces filesize by up to 70% for PNG's
    if ( (IMAGETYPE_PNG == $origType || IMAGETYPE_GIF == $origType) && function_exists('imageistruecolor') && !imageistruecolor( $image ) && imagecolortransparent( $image ) > 0 ){
      imagetruecolortopalette( $canvas, false, imagecolorstotal( $image ) );
    }

    $imgType = "";
    $tempfile = tempnam($this->cacheDirectory, 'timthumb_tmpimg_');
    if(preg_match('/^image\/(?:jpg|jpeg)$/i', $mimeType)){ 
      $imgType = 'jpg';
      imagejpeg($canvas, $tempfile, $quality); 
    } else if(preg_match('/^image\/png$/i', $mimeType)){ 
      $imgType = 'png';
      imagepng($canvas, $tempfile, floor($quality * 0.09));
    } else if(preg_match('/^image\/gif$/i', $mimeType)){
      $imgType = 'gif';
      imagegif($canvas, $tempfile);
    } else {
      return $this->sanityFail("Could not match mime type after verifying it previously.");
    }

    if($imgType == 'png' && OPTIPNG_ENABLED && OPTIPNG_PATH && @is_file(OPTIPNG_PATH)){
      $exec = OPTIPNG_PATH;
      $this->debug(3, "optipng'ing $tempfile");
      $presize = filesize($tempfile);
      $out = `$exec -o1 $tempfile`; //you can use up to -o7 but it really slows things down
      clearstatcache();
      $aftersize = filesize($tempfile);
      $sizeDrop = $presize - $aftersize;
      if($sizeDrop > 0){
        $this->debug(1, "optipng reduced size by $sizeDrop");
      } else if($sizeDrop < 0){
        $this->debug(1, "optipng increased size! Difference was: $sizeDrop");
      } else {
        $this->debug(1, "optipng did not change image size.");
      }
    } else if($imgType == 'png' && PNGCRUSH_ENABLED && PNGCRUSH_PATH && @is_file(PNGCRUSH_PATH)){
      $exec = PNGCRUSH_PATH;
      $tempfile2 = tempnam($this->cacheDirectory, 'timthumb_tmpimg_');
      $this->debug(3, "pngcrush'ing $tempfile to $tempfile2");
      $out = `$exec $tempfile $tempfile2`;
      $todel = "";
      if(is_file($tempfile2)){
        $sizeDrop = filesize($tempfile) - filesize($tempfile2);
        if($sizeDrop > 0){
          $this->debug(1, "pngcrush was succesful and gave a $sizeDrop byte size reduction");
          $todel = $tempfile;
          $tempfile = $tempfile2;
        } else {
          $this->debug(1, "pngcrush did not reduce file size. Difference was $sizeDrop bytes.");
          $todel = $tempfile2;
        }
      } else {
        $this->debug(3, "pngcrush failed with output: $out");
        $todel = $tempfile2;
      }
      @unlink($todel);
    }

    $this->debug(3, "Rewriting image with security header.");
    $tempfile4 = tempnam($this->cacheDirectory, 'timthumb_tmpimg_');
    $context = stream_context_create ();
    $fp = fopen($tempfile,'r',0,$context);
    file_put_contents($tempfile4, $this->filePrependSecurityBlock . $imgType . ' ?' . '>'); //6 extra bytes, first 3 being image type 
    file_put_contents($tempfile4, $fp, FILE_APPEND);
    fclose($fp);
    @unlink($tempfile);
    $this->debug(3, "Locking and replacing cache file.");
    $lockFile = $this->cachefile . '.lock';
    $fh = fopen($lockFile, 'w');
    if(! $fh){
      return $this->error("Could not open the lockfile for writing an image.");
    }
    if(flock($fh, LOCK_EX)){
      @unlink($this->cachefile); //rename generally overwrites, but doing this in case of platform specific quirks. File might not exist yet.
      rename($tempfile4, $this->cachefile);
      flock($fh, LOCK_UN);
      fclose($fh);
      @unlink($lockFile);
    } else {
      fclose($fh);
      @unlink($lockFile);
      @unlink($tempfile4);
      return $this->error("Could not get a lock for writing.");
    }
    $this->debug(3, "Done image replace with security header. Cleaning up and running cleanCache()");
    imagedestroy($canvas);
    imagedestroy($image);
    return true;
  }
  protected function calcDocRoot(){
    $docRoot = @$_SERVER['DOCUMENT_ROOT'];
    if (defined('LOCAL_FILE_BASE_DIRECTORY')) {
      $docRoot = LOCAL_FILE_BASE_DIRECTORY;   
    }
    if(!isset($docRoot)){ 
      $this->debug(3, "DOCUMENT_ROOT is not set. This is probably windows. Starting search 1.");
      if(isset($_SERVER['SCRIPT_FILENAME'])){
        $docRoot = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
        $this->debug(3, "Generated docRoot using SCRIPT_FILENAME and PHP_SELF as: $docRoot");
      } 
    }
    if(!isset($docRoot)){ 
      $this->debug(3, "DOCUMENT_ROOT still is not set. Starting search 2.");
      if(isset($_SERVER['PATH_TRANSLATED'])){
        $docRoot = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])));
        $this->debug(3, "Generated docRoot using PATH_TRANSLATED and PHP_SELF as: $docRoot");
      } 
    }
    if($docRoot && $_SERVER['DOCUMENT_ROOT'] != '/'){ $docRoot = preg_replace('/\/$/', '', $docRoot); }
    $this->debug(3, "Doc root is: " . $docRoot);
    $this->docRoot = $docRoot;

  }
  protected function getLocalImagePath($src){
    $src = ltrim($src, '/'); //strip off the leading '/'
    if(! $this->docRoot){
      $this->debug(3, "We have no document root set, so as a last resort, lets check if the image is in the current dir and serve that.");
      //We don't support serving images outside the current dir if we don't have a doc root for security reasons.
      $file = preg_replace('/^.*?([^\/\\\\]+)$/', '$1', $src); //strip off any path info and just leave the filename.
      if(is_file($file)){
        return $this->realpath($file);
      }
      return $this->error("Could not find your website document root and the file specified doesn't exist in timthumbs directory. We don't support serving files outside timthumb's directory without a document root for security reasons.");
    } else if ( ! is_dir( $this->docRoot ) ) {
      $this->error("Server path does not exist. Ensure variable \$_SERVER['DOCUMENT_ROOT'] is set correctly");
    }
    
    //Do not go past this point without docRoot set

    //Try src under docRoot
    if(file_exists ($this->docRoot . '/' . $src)) {
      $this->debug(3, "Found file as " . $this->docRoot . '/' . $src);
      $real = $this->realpath($this->docRoot . '/' . $src);
      if(stripos($real, $this->docRoot) === 0){
        return $real;
      } else {
        $this->debug(1, "Security block: The file specified occurs outside the document root.");
        //allow search to continue
      }
    }
    //Check absolute paths and then verify the real path is under doc root
    $absolute = $this->realpath('/' . $src);
    if($absolute && file_exists($absolute)){ //realpath does file_exists check, so can probably skip the exists check here
      $this->debug(3, "Found absolute path: $absolute");
      if(! $this->docRoot){ $this->sanityFail("docRoot not set when checking absolute path."); }
      if(stripos($absolute, $this->docRoot) === 0){
        return $absolute;
      } else {
        $this->debug(1, "Security block: The file specified occurs outside the document root.");
        //and continue search
      }
    }
    
    $base = $this->docRoot;
    
    // account for Windows directory structure
    if (strstr($_SERVER['SCRIPT_FILENAME'],':')) {
      $sub_directories = explode('\\', str_replace($this->docRoot, '', $_SERVER['SCRIPT_FILENAME']));
    } else {
      $sub_directories = explode('/', str_replace($this->docRoot, '', $_SERVER['SCRIPT_FILENAME']));
    }

    foreach ($sub_directories as $sub){
      $base .= $sub . '/';
      $this->debug(3, "Trying file as: " . $base . $src);
      if(file_exists($base . $src)){
        $this->debug(3, "Found file as: " . $base . $src);
        $real = $this->realpath($base . $src);
        if(stripos($real, $this->realpath($this->docRoot)) === 0){
          return $real;
        } else {
          $this->debug(1, "Security block: The file specified occurs outside the document root.");
          //And continue search
        }
      }
    }
    return false;
  }
  protected function realpath($path){
    //try to remove any relative paths
    $remove_relatives = '/\w+\/\.\.\//';
    while(preg_match($remove_relatives,$path)){
        $path = preg_replace($remove_relatives, '', $path);
    }
    //if any remain use PHP realpath to strip them out, otherwise return $path
    //if using realpath, any symlinks will also be resolved
    return preg_match('#^\.\./|/\.\./#', $path) ? realpath($path) : $path;
  }
  protected function toDelete($name){
    $this->debug(3, "Scheduling file $name to delete on destruct.");
    $this->toDeletes[] = $name;
  }
  protected function serveWebshot(){
    $this->debug(3, "Starting serveWebshot");
    $instr = "Please follow the instructions at http://code.google.com/p/timthumb/ to set your server up for taking website screenshots.";
    if(! is_file(WEBSHOT_CUTYCAPT)){
      return $this->error("CutyCapt is not installed. $instr");
    }
    if(! is_file(WEBSHOT_XVFB)){
      return $this->Error("Xvfb is not installed. $instr");
    }
    $cuty = WEBSHOT_CUTYCAPT;
    $xv = WEBSHOT_XVFB;
    $screenX = WEBSHOT_SCREEN_X;
    $screenY = WEBSHOT_SCREEN_Y;
    $colDepth = WEBSHOT_COLOR_DEPTH;
    $format = WEBSHOT_IMAGE_FORMAT;
    $timeout = WEBSHOT_TIMEOUT * 1000;
    $ua = WEBSHOT_USER_AGENT;
    $jsOn = WEBSHOT_JAVASCRIPT_ON ? 'on' : 'off';
    $javaOn = WEBSHOT_JAVA_ON ? 'on' : 'off';
    $pluginsOn = WEBSHOT_PLUGINS_ON ? 'on' : 'off';
    $proxy = WEBSHOT_PROXY ? ' --http-proxy=' . WEBSHOT_PROXY : '';
    $tempfile = tempnam($this->cacheDirectory, 'timthumb_webshot');
    $url = $this->src;
    if(! preg_match('/^https?:\/\/[a-zA-Z0-9\.\-]+/i', $url)){
      return $this->error("Invalid URL supplied.");
    }
    $url = preg_replace('/[^A-Za-z0-9\-\.\_\~:\/\?\#\[\]\@\!\$\&\'\(\)\*\+\,\;\=]+/', '', $url); //RFC 3986
    //Very important we don't allow injection of shell commands here. URL is between quotes and we are only allowing through chars allowed by a the RFC 
    // which AFAIKT can't be used for shell injection. 
    if(WEBSHOT_XVFB_RUNNING){
      putenv('DISPLAY=:100.0');
      $command = "$cuty $proxy --max-wait=$timeout --user-agent=\"$ua\" --javascript=$jsOn --java=$javaOn --plugins=$pluginsOn --js-can-open-windows=off --url=\"$url\" --out-format=$format --out=$tempfile";
    } else {
      $command = "$xv --server-args=\"-screen 0, {$screenX}x{$screenY}x{$colDepth}\" $cuty $proxy --max-wait=$timeout --user-agent=\"$ua\" --javascript=$jsOn --java=$javaOn --plugins=$pluginsOn --js-can-open-windows=off --url=\"$url\" --out-format=$format --out=$tempfile";
    }
    $this->debug(3, "Executing command: $command");
    $out = `$command`;
    $this->debug(3, "Received output: $out");
    if(! is_file($tempfile)){
      $this->set404();
      return $this->error("The command to create a thumbnail failed.");
    }
    $this->cropTop = true;
    if($this->processImageAndWriteToCache($tempfile)){
      $this->debug(3, "Image processed succesfully. Serving from cache");
      return $this->serveCacheFile();
    } else {
      return false;
    }
  }
  protected function serveExternalImage(){
    if(! preg_match('/^https?:\/\/[a-zA-Z0-9\-\.]+/i', $this->src)){
      $this->error("Invalid URL supplied.");
      return false;
    }
    $tempfile = tempnam($this->cacheDirectory, 'timthumb');
    $this->debug(3, "Fetching external image into temporary file $tempfile");
    $this->toDelete($tempfile);
    #fetch file here
    if(! $this->getURL($this->src, $tempfile)){
      @unlink($this->cachefile);
      touch($this->cachefile);
      $this->debug(3, "Error fetching URL: " . $this->lastURLError);
      $this->error("Error reading the URL you specified from remote host." . $this->lastURLError);
      return false;
    }

    $mimeType = $this->getMimeType($tempfile);
    if(! preg_match("/^image\/(?:jpg|jpeg|gif|png)$/i", $mimeType)){
      $this->debug(3, "Remote file has invalid mime type: $mimeType");
      @unlink($this->cachefile);
      touch($this->cachefile);
      $this->error("The remote file is not a valid image. Mimetype = '" . $mimeType . "'" . $tempfile);
      return false;
    }
    if($this->processImageAndWriteToCache($tempfile)){
      $this->debug(3, "Image processed succesfully. Serving from cache");
      return $this->serveCacheFile();
    } else {
      return false;
    }
  }
  public static function curlWrite($h, $d){
    fwrite(self::$curlFH, $d);
    self::$curlDataWritten += strlen($d);
    if(self::$curlDataWritten > MAX_FILE_SIZE){
      return 0;
    } else {
      return strlen($d);
    }
  }
  protected function serveCacheFile(){
    $this->debug(3, "Serving {$this->cachefile}");
    if(! is_file($this->cachefile)){
      $this->error("serveCacheFile called in timthumb but we couldn't find the cached file.");
      return false;
    }
    $fp = fopen($this->cachefile, 'rb');
    if(! $fp){ return $this->error("Could not open cachefile."); }
    fseek($fp, strlen($this->filePrependSecurityBlock), SEEK_SET);
    $imgType = fread($fp, 3);
    fseek($fp, 3, SEEK_CUR);
    if(ftell($fp) != strlen($this->filePrependSecurityBlock) + 6){
      @unlink($this->cachefile);
      return $this->error("The cached image file seems to be corrupt.");
    }
    $imageDataSize = filesize($this->cachefile) - (strlen($this->filePrependSecurityBlock) + 6);
    $this->sendImageHeaders($imgType, $imageDataSize);
    $bytesSent = @fpassthru($fp);
    fclose($fp);
    if($bytesSent > 0){
      return true;
    }
    $content = file_get_contents ($this->cachefile);
    if ($content != FALSE) {
      $content = substr($content, strlen($this->filePrependSecurityBlock) + 6);
      echo $content;
      $this->debug(3, "Served using file_get_contents and echo");
      return true;
    } else {
      $this->error("Cache file could not be loaded.");
      return false;
    }
  }
  protected function sendImageHeaders($mimeType, $dataSize){
    if(! preg_match('/^image\//i', $mimeType)){
      $mimeType = 'image/' . $mimeType;
    }
    if(strtolower($mimeType) == 'image/jpg'){
      $mimeType = 'image/jpeg';
    }
    $gmdate_expires = gmdate ('D, d M Y H:i:s', strtotime ('now +10 days')) . ' GMT';
    $gmdate_modified = gmdate ('D, d M Y H:i:s') . ' GMT';
    // send content headers then display image
    header ('Content-Type: ' . $mimeType);
    header ('Accept-Ranges: none'); //Changed this because we don't accept range requests
    header ('Last-Modified: ' . $gmdate_modified);
    header ('Content-Length: ' . $dataSize);
    if(BROWSER_CACHE_DISABLE){
      $this->debug(3, "Browser cache is disabled so setting non-caching headers.");
      header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
      header("Pragma: no-cache");
      header('Expires: ' . gmdate ('D, d M Y H:i:s', time()));
    } else {
      $this->debug(3, "Browser caching is enabled");
      header('Cache-Control: max-age=' . BROWSER_CACHE_MAX_AGE . ', must-revalidate');
      header('Expires: ' . $gmdate_expires);
    }
    return true;
  }
  protected function securityChecks(){
  }
  protected function param($property, $default = ''){
    if (isset ($_GET[$property])) {
      return $_GET[$property];
    } else {
      return $default;
    }
  }
  protected function openImage($mimeType, $src){
    switch ($mimeType) {
      case 'image/jpeg':
        $image = imagecreatefromjpeg ($src);
        break;

      case 'image/png':
        $image = imagecreatefrompng ($src);
        imagealphablending( $image, true );
        imagesavealpha( $image, true );
        break;

      case 'image/gif':
        $image = imagecreatefromgif ($src);
        break;
      
      default:
        $this->error("Unrecognised mimeType");
    }

    return $image;
  }
  protected function getIP(){
    $rem = @$_SERVER["REMOTE_ADDR"];
    $ff = @$_SERVER["HTTP_X_FORWARDED_FOR"];
    $ci = @$_SERVER["HTTP_CLIENT_IP"];
    if(preg_match('/^(?:192\.168|172\.16|10\.|127\.)/', $rem)){ 
      if($ff){ return $ff; }
      if($ci){ return $ci; }
      return $rem;
    } else {
      if($rem){ return $rem; }
      if($ff){ return $ff; }
      if($ci){ return $ci; }
      return "UNKNOWN";
    }
  }
  protected function debug($level, $msg){
    if(DEBUG_ON && $level <= DEBUG_LEVEL){
      $execTime = sprintf('%.6f', microtime(true) - $this->startTime);
      $tick = sprintf('%.6f', 0);
      if($this->lastBenchTime > 0){
        $tick = sprintf('%.6f', microtime(true) - $this->lastBenchTime);
      }
      $this->lastBenchTime = microtime(true);
      error_log("TimThumb Debug line " . __LINE__ . " [$execTime : $tick]: $msg");
    }
  }
  protected function sanityFail($msg){
    return $this->error("There is a problem in the timthumb code. Message: Please report this error at <a href='http://code.google.com/p/timthumb/issues/list'>timthumb's bug tracking page</a>: $msg");
  }
  protected function getMimeType($file){
    $info = getimagesize($file);
    if(is_array($info) && $info['mime']){
      return $info['mime'];
    }
    return '';
  }
  protected function setMemoryLimit(){
    $inimem = ini_get('memory_limit');
    $inibytes = timthumb::returnBytes($inimem);
    $ourbytes = timthumb::returnBytes(MEMORY_LIMIT);
    if($inibytes < $ourbytes){
      ini_set ('memory_limit', MEMORY_LIMIT);
      $this->debug(3, "Increased memory from $inimem to " . MEMORY_LIMIT);
    } else {
      $this->debug(3, "Not adjusting memory size because the current setting is " . $inimem . " and our size of " . MEMORY_LIMIT . " is smaller.");
    }
  }
  protected static function returnBytes($size_str){
    switch (substr ($size_str, -1))
    {
      case 'M': case 'm': return (int)$size_str * 1048576;
      case 'K': case 'k': return (int)$size_str * 1024;
      case 'G': case 'g': return (int)$size_str * 1073741824;
      default: return $size_str;
    }
  }
  
  protected function getURL($url, $tempfile){
    $this->lastURLError = false;
    $url = preg_replace('/ /', '%20', $url);
    if(function_exists('curl_init')){
      $this->debug(3, "Curl is installed so using it to fetch URL.");
      self::$curlFH = fopen($tempfile, 'w');
      if(! self::$curlFH){
        $this->error("Could not open $tempfile for writing.");
        return false;
      }
      self::$curlDataWritten = 0;
      $this->debug(3, "Fetching url with curl: $url");
      $curl = curl_init($url);
      curl_setopt ($curl, CURLOPT_TIMEOUT, CURL_TIMEOUT);
      curl_setopt ($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30");
      curl_setopt ($curl, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt ($curl, CURLOPT_HEADER, 0);
      curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt ($curl, CURLOPT_WRITEFUNCTION, 'timthumb::curlWrite');
      @curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, true);
      @curl_setopt ($curl, CURLOPT_MAXREDIRS, 10);
      
      $curlResult = curl_exec($curl);
      fclose(self::$curlFH);
      $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      if($httpStatus == 404){
        $this->set404();
      }
      if($httpStatus == 302){
        $this->error("External Image is Redirecting. Try alternate image url");
        return false;
      }
      if($curlResult){
        curl_close($curl);
        return true;
      } else {
        $this->lastURLError = curl_error($curl);
        curl_close($curl);
        return false;
      }
    } else {
      $img = @file_get_contents ($url);
      if($img === false){
        $err = error_get_last();
        if(is_array($err) && $err['message']){
          $this->lastURLError = $err['message'];
        } else {
          $this->lastURLError = $err;
        }
        if(preg_match('/404/', $this->lastURLError)){
          $this->set404();
        }

        return false;
      }
      if(! file_put_contents($tempfile, $img)){
        $this->error("Could not write to $tempfile.");
        return false;
      }
      return true;
    }

  }
  protected function serveImg($file){
    $s = getimagesize($file);
    if(! ($s && $s['mime'])){
      return false;
    }
    header ('Content-Type: ' . $s['mime']);
    header ('Content-Length: ' . filesize($file) );
    header ('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header ("Pragma: no-cache");
    $bytes = @readfile($file);
    if($bytes > 0){
      return true;
    }
    $content = @file_get_contents ($file);
    if ($content != FALSE){
      echo $content;
      return true;
    }
    return false;

  }
  protected function set404(){
    $this->is404 = true;
  }
  protected function is404(){
    return $this->is404;
  }
}