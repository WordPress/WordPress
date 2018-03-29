<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an issue template.
 */
echo wfView::create('scanner/issue-base', array(
	'internalType' => 'wfThemeUpgrade',
	'displayType' => __('Theme Upgrade', 'wordfence'),
	'iconSVG' => '<svg viewBox="0 0 20 20"><g><path d="M14.48 11.06L7.41 3.99l1.5-1.5c.5-.56 2.3-.47 3.51.32 1.21.8 1.43 1.28 2.91 2.1 1.18.64 2.45 1.26 4.45.85zm-.71.71L6.7 4.7 4.93 6.47c-.39.39-.39 1.02 0 1.41l1.06 1.06c.39.39.39 1.03 0 1.42-.6.6-1.43 1.11-2.21 1.69-.35.26-.7.53-1.01.84C1.43 14.23.4 16.08 1.4 17.07c.99 1 2.84-.03 4.18-1.36.31-.31.58-.66.85-1.02.57-.78 1.08-1.61 1.69-2.21.39-.39 1.02-.39 1.41 0l1.06 1.06c.39.39 1.02.39 1.41 0z"/></g></svg>',
	'summaryControls' => array(wfView::create('scanner/issue-control-ignore', array('ignoreC' => __('Ignore ', 'wordfence'))), wfView::create('scanner/issue-control-show-details')),
	'detailPairs' => array(
		__('Theme Name', 'wordfence') => '${data.name}',
		__('Current Theme Version', 'wordfence') => '${data.version}',
		__('New Theme Version', 'wordfence') => '${data.newVersion}',
		null,
		__('Details', 'wordfence') => '{{if data.vulnerable}}<strong>' . __('Update includes security-related fixes.', 'wordfence') . '</strong><br>{{/if}}{{html longMsg}}<br><a href="' . get_admin_url() . 'update-core.php' . '">' . __('Click here to update now', 'wordfence') . '</a>.',
		null,
		__('Theme URL', 'wordfence') => '<a href="${data.URL}" target="_blank" rel="noopener noreferrer"><span class="wf-hidden-xs wf-split-word">${data.URL}</span><span class="wf-visible-xs">' . __('View', 'wordfence') . '</span></a>',
		__('Vulnerability Information', 'wordfence') => array('data.vulnerabilityLink', '<a href="${data.vulnerabilityLink}" target="_blank" rel="noopener noreferrer"><span class="wf-hidden-xs wf-split-word">${data.vulnerabilityLink}</span><span class="wf-visible-xs">' . __('View', 'wordfence') . '</span></a>'),
		),
	'detailControls' => array(
		'<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-issue-control-mark-fixed">' . __('Mark as Fixed', 'wordfence') . '</a>',
		'<a href="' . esc_url(network_admin_url('update-core.php')) . '" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-issue-control-view-updates">' . __('View Updates', 'wordfence') . '</a>',
	)
))->render();
