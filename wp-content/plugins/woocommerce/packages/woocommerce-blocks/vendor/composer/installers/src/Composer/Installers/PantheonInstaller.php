<?php

namespace Composer\Installers;

class PantheonInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'script' => 'web/private/scripts/quicksilver/{$name}',
        'module' => 'web/private/scripts/quicksilver/{$name}',
    );
}
