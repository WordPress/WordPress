<?php
namespace Composer\Installers;

class Concrete5Installer extends BaseInstaller
{
    protected $locations = array(
        'block'      => 'blocks/{$name}/',
        'package'    => 'packages/{$name}/',
        'theme'      => 'themes/{$name}/',
        'update'     => 'updates/{$name}/',
    );
}
