<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\API;

class BaseReference
{
    protected $referenceId;
    protected $referenceName;
    protected $referenceTypeName;
    protected $referenceType;
    public function __construct($referenceId, $referenceName, $referenceType, $referenceTypeName)
    {
        $this->referenceId = $referenceId;
        $this->referenceType = $referenceType;
        $this->referenceTypeName = $referenceTypeName;
        $this->referenceName = $referenceName;
    }
    public function toArray()
    {
        return array('referenceId' => $this->referenceId, 'referenceType' => $this->referenceType, 'referenceTypeName' => $this->referenceTypeName, 'referenceName' => $this->referenceName);
    }
}
