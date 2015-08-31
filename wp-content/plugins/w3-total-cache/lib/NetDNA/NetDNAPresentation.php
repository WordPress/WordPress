<?php

class NetDNAPresentation {

    public static function format_popular($popular_files) {
        $formatted = array();
        foreach ($popular_files as $file) {
            // basename cannot be used, kills chinese chars and similar characters
            $filename = substr($file['uri'], strrpos($file['uri'], '/')+1);
            $formatted[] = array('file' => $filename, 'title' => $file['uri'],
                'group' => self::get_file_group($file['uri']), 'hit' => $file['hit']);
        }

        return $formatted;
    }

    public static function group_hits_per_filetype_group($filetypes) {
        $groups = array();
        foreach ($filetypes as $file) {
            $file_group = self::get_file_group($file['file_type']);
            if (!isset($groups[$file_group]))
                $groups[$file_group] = 0;
            $groups[$file_group] += $file['hit'];
        }
        return $groups;
    }
    public static function get_file_group($uri) {
        $ext = end(explode('.', $uri));
        switch ($ext) {
            case 'css':
            case 'js':
                return $ext;
                break;
            case 'png':
            case 'tiff':
            case 'gif':
            case 'jpg':
            case 'jpeg':
                return 'images';
                break;
            default:
                return 'misc';
                break;
        }
    }

    public static function get_file_group_color($group) {
        switch ($group){
            case 'css':
                return '#739468';
            case 'js':
                return '#ffb05d';
            case 'images':
                return '#b080df';
            default:
                return '#4ba0fa';
        }
    }

    public static function get_account_status($status)  {
        switch ($status) {
            case 1:
                return __('Pending', 'w3-total-cache');
            case 2:
                return __('Active', 'w3-total-cache');
            case 3:
                return __('Cancelled', 'w3-total-cache');
            case 4:
                return __('Suspended', 'w3-total-cache');
            case 5:
                return __('Fraud', 'w3-total-cache');
            default:
                return __('unknown', 'w3-total-cache');
        }
    }
}