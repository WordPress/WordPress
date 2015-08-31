<?php

namespace Composer\Installers;

class WHMCSInstaller extends BaseInstaller
{
    protected $locations = array(
        'gateway' => 'modules/gateways/{$name}/',
    );
}
