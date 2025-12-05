<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

use Piwik\Piwik;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}
?>
<div class="table">
	<table class="widefat matomo-table">
		<tbody>
		<tr>
			<td><?php echo esc_html( Piwik::translate( 'CoreHome_ThereIsNoDataForThisReport' ) ); ?></td>
		</tr>
		</tbody>
	</table>
</div>
