<?php

/**
 * Title         : Aqua Resizer
 * Description   : Resizes WordPress images on the fly
 * Version       : 1.1.7
 * Author        : Syamil MJ
 * Author URI    : http://aquagraphite.com
 * License       : WTFPL - http://sam.zoy.org/wtfpl/
 * Documentation : https://github.com/sy4mil/Aqua-Resizer/
 *
 * @param string  $url    - (required) must be uploaded using wp media uploader
 * @param int     $width  - (required)
 * @param int     $height - (optional)
 * @param bool    $crop   - (optional) default to soft crop
 * @param bool    $single - (optional) returns an array if false
 * @uses  wp_upload_dir()
 * @uses  image_resize_dimensions() | image_resize()
 * @uses  wp_get_image_editor()
 *
 * @return str|array
 */
if (!class_exists('Aq_Resize')) {

    class Aq_Resize {

        /**
         * The singleton instance
         */
        static private $instance = null;

        /**
         * No initialization allowed
         */
        private function __construct() {
            
        }

        /**
         * No cloning allowed
         */
        private function __clone() {
            
        }

        /**
         * For your custom default usage you may want to initialize an Aq_Resize object by yourself and then have own defaults
         */
        static public function getInstance() {
            if (self::$instance == null) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Run, forest.
         */
        public function process($url, $width = null, $height = null, $crop = null, $single = true, $upscale = false) {
            // Validate inputs.
            if (!$url || (!$width && !$height ))
                return false;

            // Caipt'n, ready to hook.
            if (true === $upscale)
                add_filter('image_resize_dimensions', array($this, 'aq_upscale'), 10, 6);

            // Define upload path & dir.
            $upload_info = wp_upload_dir();
            $upload_dir = $upload_info['basedir'];
            $upload_url = $upload_info['baseurl'];

            $http_prefix = "http://";
            $https_prefix = "https://";

            /* if the $url scheme differs from $upload_url scheme, make them match
              if the schemes differe, images don't show up. */
            if (!strncmp($url, $https_prefix, strlen($https_prefix))) { //if url begins with https:// make $upload_url begin with https:// as well
                $upload_url = str_replace($http_prefix, $https_prefix, $upload_url);
            } elseif (!strncmp($url, $http_prefix, strlen($http_prefix))) { //if url begins with http:// make $upload_url begin with http:// as well
                $upload_url = str_replace($https_prefix, $http_prefix, $upload_url);
            }


            // Check if $img_url is local.
            if (false === strpos($url, $upload_url))
                return false;

            // Define path of image.
            $rel_path = str_replace($upload_url, '', $url);
            $img_path = $upload_dir . $rel_path;

            // Check if img path exists, and is an image indeed.
            if (!file_exists($img_path) or!getimagesize($img_path))
                return false;

            // Get image info.
            $info = pathinfo($img_path);
            $ext = $info['extension'];

            try {
                list( $orig_w, $orig_h ) = getimagesize($img_path);
            } catch (Exception $e) {
                return false;
            }


            // Get image size after cropping.
            $dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
            $dst_w = $dims[4];
            $dst_h = $dims[5];

            // Return the original image only if it exactly fits the needed measures.
            if (!$dims && ( ( ( null === $height && $orig_w == $width ) xor ( null === $width && $orig_h == $height ) ) xor ( $height == $orig_h && $width == $orig_w ) )) {
                $img_url = $url;
                $dst_w = $orig_w;
                $dst_h = $orig_h;
            } else {
                // Use this to check if cropped image already exists, so we can return that instead.
                $suffix = "{$dst_w}x{$dst_h}";
                $dst_rel_path = str_replace('.' . $ext, '', $rel_path);
                $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

                if (!$dims || ( true == $crop && false == $upscale && ( $dst_w < $width || $dst_h < $height ) )) {
                    // Can't resize, so return false saying that the action to do could not be processed as planned.
                    return false;
                }
                // Else check if cache exists.
                elseif (file_exists($destfilename) && getimagesize($destfilename)) {
                    $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
                }
                // Else, we resize the image and return the new resized image url.
                else {

                    $resized_img_path = $this->image_resize($img_path, $width, $height, $crop); // Fallback foo.
                    if (!is_wp_error($resized_img_path)) {
                        $resized_rel_path = str_replace($upload_dir, '', $resized_img_path);
                        $img_url = $upload_url . $resized_rel_path;
                    } else {
                        return false;
                    }
                }
            }

            // Okay, leave the ship.
            if (true === $upscale)
                remove_filter('image_resize_dimensions', array($this, 'aq_upscale'));

            // Return the output.
            if ($single) {
                // str return.
                $image = $img_url;
            } else {
                // array return.
                $image = array(
                    0 => $img_url,
                    1 => $dst_w,
                    2 => $dst_h
                );
            }

            return $image;
        }

        public function image_resize($file, $max_w, $max_h, $crop = false, $suffix = null, $dest_path = null, $jpeg_quality = 90) {

            $editor = wp_get_image_editor($file);
            if (is_wp_error($editor))
                return $editor;
            $editor->set_quality($jpeg_quality);

            $resized = $editor->resize($max_w, $max_h, $crop);
            if (is_wp_error($resized))
                return $resized;

            $dest_file = $editor->generate_filename($suffix, $dest_path);
            $saved = $editor->save($dest_file);

            if (is_wp_error($saved))
                return $saved;

            return $dest_file;
        }

        /**
         * Callback to overwrite WP computing of thumbnail measures
         */
        public function aq_upscale($default, $orig_w, $orig_h, $dest_w, $dest_h, $crop) {
            if (!$crop)
                return null; // Let the wordpress default function handle this.

// Here is the point we allow to use larger image size than the original one.
            $aspect_ratio = $orig_w / $orig_h;
            $new_w = $dest_w;
            $new_h = $dest_h;

            if (!$new_w) {
                $new_w = intval($new_h * $aspect_ratio);
            }

            if (!$new_h) {
                $new_h = intval($new_w / $aspect_ratio);
            }

            $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

            $crop_w = round($new_w / $size_ratio);
            $crop_h = round($new_h / $size_ratio);

            $s_x = floor(( $orig_w - $crop_w ) / 2);
            $s_y = floor(( $orig_h - $crop_h ) / 2);

            return array(0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h);
        }

    }

}



if (!function_exists('woof_aq_resize')) {

    /**
     * This is just a tiny wrapper function for the class above so that there is no
     * need to change any code in your own WP themes. Usage is still the same :)
     */
    function woof_aq_resize($url, $width = null, $height = null, $crop = null, $single = true, $upscale = false) {
        $aq_resize = Aq_Resize::getInstance();
        return $aq_resize->process($url, $width, $height, $crop, $single, $upscale);
    }

}


