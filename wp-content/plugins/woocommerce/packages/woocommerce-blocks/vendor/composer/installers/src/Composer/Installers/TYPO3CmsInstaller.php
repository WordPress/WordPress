<?php
namespace Composer\Installers;

/**
 * Extension installer for TYPO3 CMS
 *
 * @deprecated since 1.0.25, use https://packagist.org/packages/typo3/cms-composer-installers instead
 *
 * @author Sascha Egerer <sascha.egerer@dkd.de>
 */
class TYPO3CmsInstaller extends BaseInstaller
{
    protected $locations = array(
        'extension'   => 'typo3conf/ext/{$name}/',
    );
}
