<?php
namespace Composer\Installers;


class OsclassInstaller extends BaseInstaller 
{
    
    protected $locations = array(
        'plugin' => 'oc-content/plugins/{$name}/',
        'theme' => 'oc-content/themes/{$name}/',
        'language' => 'oc-content/languages/{$name}/',
    );
    
}
