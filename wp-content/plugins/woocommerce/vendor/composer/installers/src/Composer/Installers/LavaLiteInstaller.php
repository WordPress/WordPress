<?php
namespace Composer\Installers;

class LavaLiteInstaller extends BaseInstaller
{
    protected $locations = array(
        'package' => 'packages/{$vendor}/{$name}/',
        'theme'   => 'public/themes/{$name}/',
    );
}
