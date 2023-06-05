<?php
namespace Composer\Installers;

class AttogramInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );
}
