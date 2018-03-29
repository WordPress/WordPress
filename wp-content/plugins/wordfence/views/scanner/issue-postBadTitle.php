<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an issue template.
 */
echo wfView::create('scanner/issue-base', array(
	'internalType' => 'postBadTitle',
	'displayType' => __('Post', 'wordfence'),
	'iconSVG' => '<svg viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10.44 3.02l1.82-1.82 6.36 6.35-1.83 1.82c-1.05-.68-2.48-.57-3.41.36l-.75.75c-.92.93-1.04 2.35-.35 3.41l-1.83 1.82-2.41-2.41-2.8 2.79c-.42.42-3.38 2.71-3.8 2.29s1.86-3.39 2.28-3.81l2.79-2.79L4.1 9.36l1.83-1.82c1.05.69 2.48.57 3.4-.36l.75-.75c.93-.92 1.05-2.35.36-3.41z"/></g></svg>',
	'summaryControls' => array(wfView::create('scanner/issue-control-edit-post'), wfView::create('scanner/issue-control-ignore', array('ignoreP' => __('Always Ignore', 'wordfence'), 'ignoreC' => __('Ignore Only this Title', 'wordfence'))), wfView::create('scanner/issue-control-show-details')),
	'detailPairs' => array(
		__('Title', 'wordfence') => '<strong class="wfWarn">${data.postTitle}</strong>',
		__('Posted on', 'wordfence') => '${data.postDate}',
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
