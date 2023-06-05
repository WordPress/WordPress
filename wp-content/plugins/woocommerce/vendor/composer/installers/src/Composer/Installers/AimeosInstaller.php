<?php
namespace Composer\Installers;

class AimeosInstaller extends BaseInstaller
{
    protected $locations = array(
        'extension'   => 'ext/{$name}/',
    );
}
