<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<?php //$data is defined here as an array of login attempts: array('IP' => binary ip, 'countryCode' => string, 'blockCount' => int, 'unixday' => int, 'totalIPs' => int, 'totalBlockCount' => int, 'countryName' => string) ?>
<table class="wf-table wf-table-hover">
	<thead>
		<tr>
			<th colspan="2">Country</th>
			<th>Block Count</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($data as $l): ?>
		<tr>
			<td><?php echo esc_html($l['countryName']); ?></td>
			<td><img src="<?php echo wfUtils::getBaseURL() . 'images/flags/' . esc_attr(strtolower($l['countryCode'])); ?>.png" class="wfFlag" height="11" width="16" alt="<?php echo esc_attr($l['countryName']); ?>" title="<?php echo esc_attr($l['countryName']); ?>"></td>
			<td><?php echo esc_html(number_format_i18n($l['totalBlockCount'])); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>