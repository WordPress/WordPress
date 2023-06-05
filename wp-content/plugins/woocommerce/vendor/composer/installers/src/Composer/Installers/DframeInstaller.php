<?php

namespace Composer\Installers;

class DframeInstaller extends BaseInstaller
{
    protected $locations = array(
        'module'  => 'modules/{$vendor}/{$name}/',
    );
}
