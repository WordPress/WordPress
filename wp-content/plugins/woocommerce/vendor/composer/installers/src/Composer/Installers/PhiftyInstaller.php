<?php
namespace Composer\Installers;

class PhiftyInstaller extends BaseInstaller
{
    protected $locations = array(
        'bundle' => 'bundles/{$name}/',
        'library' => 'libraries/{$name}/',
        'framework' => 'frameworks/{$name}/',
    );
}
