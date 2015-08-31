<?php
namespace Composer\Installers;

class KirbyInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin'    => 'site/plugins/{$name}/',
    );
}
