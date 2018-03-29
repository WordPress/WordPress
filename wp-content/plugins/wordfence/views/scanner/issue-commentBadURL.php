<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an issue template.
 */
echo wfView::create('scanner/issue-base', array(
	'internalType' => 'commentBadURL',
	'displayType' => __('URL', 'wordfence'),
	'iconSVG' => '<svg viewBox="0 0 106.37 106.37"><path d="M100.89,64.92,87.34,51.36a18.89,18.89,0,0,0-26.85.26l-5.74-5.73a18.34,18.34,0,0,0,5.74-13.62A18,18,0,0,0,55.07,19L41.65,5.54A17.86,17.86,0,0,0,28.35,0,18,18,0,0,0,15.12,5.41L5.54,14.93A17.79,17.79,0,0,0,0,28.16a18.09,18.09,0,0,0,5.48,13.3L19,55a18.12,18.12,0,0,0,13.3,5.48,18.27,18.27,0,0,0,13.56-5.74l5.73,5.74a18.32,18.32,0,0,0-5.73,13.62A18,18,0,0,0,51.3,87.34l13.43,13.49a18.81,18.81,0,0,0,26.53.13l9.58-9.52a17.79,17.79,0,0,0,5.54-13.23,18.1,18.1,0,0,0-5.48-13.3ZM45.89,37l-1.21-1.24c-.67-.7-1.14-1.16-1.4-1.4s-.68-.56-1.24-1a5.26,5.26,0,0,0-1.66-.85,6.64,6.64,0,0,0-1.79-.23,6.24,6.24,0,0,0-6.26,6.26,6.6,6.6,0,0,0,.23,1.79A5.19,5.19,0,0,0,33.41,42a14.48,14.48,0,0,0,1,1.24c.24.26.71.73,1.4,1.4L37,45.89a6.3,6.3,0,0,1-4.7,2,6,6,0,0,1-4.43-1.76L14.34,32.59a6,6,0,0,1-1.82-4.43,5.94,5.94,0,0,1,1.82-4.36l9.58-9.52a6.3,6.3,0,0,1,4.43-1.76,6,6,0,0,1,4.43,1.83L46.21,27.83A6,6,0,0,1,48,32.26,6.33,6.33,0,0,1,45.89,37ZM92,82.58,82.45,92.1A6.28,6.28,0,0,1,78,93.79,6,6,0,0,1,73.59,92L60.16,78.54a6,6,0,0,1-1.82-4.43,6.33,6.33,0,0,1,2.15-4.76l1.21,1.24c.67.69,1.14,1.16,1.4,1.4a15,15,0,0,0,1.24,1,5.18,5.18,0,0,0,1.66.85,6.6,6.6,0,0,0,1.79.23A6.23,6.23,0,0,0,74,67.79,6.76,6.76,0,0,0,73.82,66,5.34,5.34,0,0,0,73,64.33c-.41-.56-.74-1-1-1.24s-.71-.73-1.4-1.4l-1.24-1.2A6.2,6.2,0,0,1,74,58.4a6,6,0,0,1,4.43,1.82L92,73.78a6,6,0,0,1,1.83,4.43A6,6,0,0,1,92,82.58Zm0,0"/></svg>',
	'summaryControls' => array(wfView::create('scanner/issue-control-edit-comment'), wfView::create('scanner/issue-control-ignore'), wfView::create('scanner/issue-control-show-details')),
	'detailPairs' => array(
		__('Author', 'wordfence') => '${data.author}',
		__('Bad URL', 'wordfence') => '<strong class="wfWarn wf-split-word">${data.badURL}</strong>',
		__('Posted on', 'wordfence') => '${data.commentDate}',
		null,
		__('Details', 'wordfence') => '{{html longMsg}}',
		null,
		__('Multisite Blog ID', 'wordfence') => array('data.isMultisite', '${data.blog_id}'),
		__('Multisite Blog Domain', 'wordfence') => array('data.isMultisite', '${data.domain}'),
		__('Multisite Blog Path', 'wordfence') => array('data.isMultisite', '${data.path}'),
	),
	'detailControls' => array(
		'<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-issue-control-mark-fixed">' . __('Mark as Fixed', 'wordfence') . '</a>',
	)
))->render();
