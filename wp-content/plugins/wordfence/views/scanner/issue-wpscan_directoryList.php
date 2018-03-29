<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an issue template.
 */
echo wfView::create('scanner/issue-base', array(
	'internalType' => 'wpscan_fullPathDiscl',
	'displayType' => __('Directory Listing Enabled', 'wordfence'),
	'iconSVG' => '<svg viewBox="0 0 46 55" ><path d="M43.557,13.609l-11.214,-11.175c-1.303,-1.246 -2.962,-2.058 -4.747,-2.324l0,18.223l18.294,0c-0.269,-1.777 -1.084,-3.427 -2.333,-4.724l0,0Z" fill="#9e9e9e"/><path d="M26.465,22.921c-0.005,0 -0.011,0 -0.016,0c-1.885,0 -3.435,-1.545 -3.435,-3.423c0,-0.005 0,-0.011 0,-0.016l0,-19.482l-19.562,0c-0.921,-0.019 -1.809,0.346 -2.449,1.005c-0.658,0.637 -1.022,1.52 -1.003,2.434l0,48.127c-0.019,0.915 0.345,1.797 1.003,2.434c0.639,0.658 1.524,1.023 2.443,1.005l39.102,0c0.004,0.001 0.008,0.001 0.012,0.001c1.884,0 3.435,-1.546 3.435,-3.423c0,-0.006 0,-0.011 -0.001,-0.017l0,-28.645l-19.529,0Z" fill="#9e9e9e"/></svg>',
	'summaryControls' => array(wfView::create('scanner/issue-control-ignore'), wfView::create('scanner/issue-control-show-details')),
	'detailPairs' => array(
		__('URL', 'wordfence') => '<a href="${data.url}" target="_blank" rel="noopener noreferrer">${data.url}</a>',
		null,
		__('Details', 'wordfence') => '{{html longMsg}}'
	),
	'detailControls' => array(
		'{{if data.fileExists}}<a target="_blank" class="wf-btn wf-btn-default wf-btn-callout-subtle" rel="noopener noreferrer" href="${WFAD.makeViewFileLink(data.file)}">' . __('View File', 'wordfence') . '</a>{{/if}}',
		'{{if data.canDelete}}<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle" onclick="WFAD.deleteFile(\'${id}\'); return false;">' . __('Delete File', 'wordfence') . '</a>{{/if}}',
		'<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle" onclick="WFAD.updateIssueStatus(\'${id}\', \'delete\'); return false;">' . __('Mark as Fixed', 'wordfence') . '</a>',
	)
))->render();
