<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an issue template.
 */
echo wfView::create('scanner/issue-base', array(
	'internalType' => 'timelimit',
	'displayType' => __('Time Limit', 'wordfence'),
	'iconSVG' => '<svg viewBox="0 0 116.8 91.77"><path d="M112.17,35.72A57.35,57.35,0,0,0,81.08,4.63a57.88,57.88,0,0,0-45.36,0A57.38,57.38,0,0,0,4.63,35.72,58.26,58.26,0,0,0,9.19,89.88a4,4,0,0,0,3.52,1.89h91.38a4,4,0,0,0,3.52-1.89,58.25,58.25,0,0,0,4.56-54.16ZM52.5,10.79a8.34,8.34,0,0,1,14.24,5.9,8,8,0,0,1-2.44,5.9,8.34,8.34,0,0,1-11.8,0,8,8,0,0,1-2.44-5.9,8,8,0,0,1,2.44-5.9ZM22.59,64.3a8,8,0,0,1-5.9,2.45,8,8,0,0,1-5.9-2.45,8.34,8.34,0,0,1,0-11.8,8,8,0,0,1,5.9-2.45,8,8,0,0,1,5.9,2.45,8.35,8.35,0,0,1,0,11.8ZM35.1,35.1a8.34,8.34,0,0,1-11.8,0,8.34,8.34,0,0,1,0-11.8,8.35,8.35,0,0,1,11.8,0,8.34,8.34,0,0,1,0,11.8ZM72,35.65l-6.59,24.9a12.39,12.39,0,0,1,4.7,5.93,12.54,12.54,0,0,1-5.41,15.25A12.2,12.2,0,0,1,55.21,83a12,12,0,0,1-7.63-5.8,12.49,12.49,0,0,1,2.8-15.94,12.42,12.42,0,0,1,7-2.84l6.59-24.9a4.1,4.1,0,0,1,2-2.57A3.88,3.88,0,0,1,69,30.57a4.27,4.27,0,0,1,3,5.08Zm9.68-.56a8.34,8.34,0,0,1,0-11.8,8.34,8.34,0,0,1,14.24,5.9A8.34,8.34,0,0,1,81.7,35.1ZM106,64.3a8.34,8.34,0,1,1-11.8-11.8A8.34,8.34,0,1,1,106,64.3Zm0,0"/></svg>',
	'summaryControls' => array(wfView::create('scanner/issue-control-ignore', array('ignoreP' => __('Ignore', 'wordfence'))), wfView::create('scanner/issue-control-show-details')),
	'detailPairs' => array(
		__('Details', 'wordfence') => '{{html longMsg}}',
	),
	'detailControls' => array(
		'<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-issue-control-mark-fixed">' . __('Mark as Fixed', 'wordfence') . '</a>',
	)
))->render();
