<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an issue template.
 */
echo wfView::create('scanner/issue-base', array(
	'internalType' => 'suspiciousAdminUsers',
	'displayType' => __('Unknown Administrator', 'wordfence'),
	'iconSVG' => '<svg viewBox="0 0 91.77 100.11"><path d="M45.89,50.06a24.1,24.1,0,0,0,17.69-7.34A24.1,24.1,0,0,0,70.91,25a24.11,24.11,0,0,0-7.33-17.7A24.12,24.12,0,0,0,45.89,0a24.12,24.12,0,0,0-17.7,7.33A24.11,24.11,0,0,0,20.86,25a24.1,24.1,0,0,0,7.33,17.7,24.11,24.11,0,0,0,17.7,7.34Zm0,0"/><path d="M91.54,76.49a66.22,66.22,0,0,0-.91-7.1,54.55,54.55,0,0,0-1.73-7.07A33.35,33.35,0,0,0,86.1,56a22.92,22.92,0,0,0-4-5.28,17,17,0,0,0-5.57-3.49,19.61,19.61,0,0,0-7.27-1.3,8,8,0,0,0-2.74,1.4q-2.15,1.41-4.86,3.13a30.77,30.77,0,0,1-7,3.13,27.68,27.68,0,0,1-17.4,0,30.59,30.59,0,0,1-7-3.13q-2.71-1.72-4.86-3.13a8,8,0,0,0-2.74-1.4,19.6,19.6,0,0,0-7.27,1.3,17,17,0,0,0-5.57,3.49,22.9,22.9,0,0,0-4,5.28,33.29,33.29,0,0,0-2.8,6.35,55.38,55.38,0,0,0-1.73,7.07,66.22,66.22,0,0,0-.91,7.1Q0,79.78,0,83.24q0,7.82,4.76,12.35t12.64,4.53h57q7.89,0,12.65-4.53t4.76-12.35q0-3.46-.23-6.75Zm0,0"/></svg>',
	'summaryControls' => array(wfView::create('scanner/issue-control-ignore', array('ignoreP' => __('Ignore', 'wordfence'))), wfView::create('scanner/issue-control-show-details')),
	'detailPairs' => array(
		__('Details', 'wordfence') => '{{html longMsg}}',
	),
	'detailControls' => array(
		'<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle" onclick="WFAD.deleteAdminUser(\'${id}\'); return false;">' . __('Delete User', 'wordfence') . '</a>',
		'<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle" onclick="WFAD.revokeAdminUser(\'${id}\'); return false;">' . __('Revoke Capabilities', 'wordfence') . '</a>',
		'<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-issue-control-mark-fixed">' . __('Mark as Fixed', 'wordfence') . '</a>',
	)
))->render();
