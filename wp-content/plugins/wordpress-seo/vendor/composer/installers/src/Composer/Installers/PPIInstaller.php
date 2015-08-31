<?php
namespace Composer\Installers;

class PPIInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
