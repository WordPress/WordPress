<?php

namespace Composer\Installers;

class MiaoxingInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin' => 'plugins/{$name}/',
    );
}
