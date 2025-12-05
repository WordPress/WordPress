<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Contents;

class Dimensions
{
    public static function getRecordNameForAction($apiMethod)
    {
        $apiToRecord = array('getContentNames' => \Piwik\Plugins\Contents\Archiver::CONTENTS_NAME_PIECE_RECORD_NAME, 'getContentPieces' => \Piwik\Plugins\Contents\Archiver::CONTENTS_PIECE_NAME_RECORD_NAME);
        return $apiToRecord[$apiMethod];
    }
    public static function getSubtableLabelForApiMethod($apiMethod)
    {
        $labelToMethod = array('getContentNames' => 'Contents_ContentPiece', 'getContentPieces' => 'Contents_ContentName');
        return $labelToMethod[$apiMethod];
    }
}
