<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * One or both of $ignoreC and $ignoreP will be defined. If only one is defined, the ignore button will default to that. If both are defined, the user will be presented with a choice.
 */
?>
<a href="#" class="wf-issue-control wf-issue-control-ignore" data-ignore-action="{{if status == 'new'}}<?php if (isset($ignoreC) && isset($ignoreP)) { echo 'choice'; } else { echo isset($ignoreC) ? 'ignoreC' : 'ignoreP'; } ?>{{else}}new{{/if}}"><svg class="wf-issue-control-icon" viewBox="0 0 116.8 87.6"><path d="M82.45,52A28.43,28.43,0,0,0,87.6,35.46,31.39,31.39,0,0,0,87.08,30L68.83,62.7A28.69,28.69,0,0,0,82.45,52Zm0,0"/><path d="M85.12,6.91a1.64,1.64,0,0,0,.06-.59,2,2,0,0,0-1-1.76l-1.27-.75Q81.8,3.2,80.82,2.61c-.65-.39-1.37-.79-2.15-1.2s-1.45-.75-2-1A3.5,3.5,0,0,0,75.48,0a2,2,0,0,0-1.83,1L70.13,7.37A63.82,63.82,0,0,0,58.4,6.26a61.76,61.76,0,0,0-32.33,8.86A76,76,0,0,0,1.3,39.3a8.4,8.4,0,0,0,0,9,77.76,77.76,0,0,0,13.59,16A67.46,67.46,0,0,0,32.07,75.54q-2.87,4.89-2.87,5.67a2,2,0,0,0,1,1.83q8,4.56,8.74,4.56a2,2,0,0,0,1.82-1L44,80.76q6.91-12.32,20.6-37T85.12,6.91ZM36.18,68.25q-17-7.63-27.83-24.44a64,64,0,0,1,24.83-23,29,29,0,0,0-.78,27.89,28.73,28.73,0,0,0,8.86,10.36ZM60.62,21a3,3,0,0,1-2.22.91A13.58,13.58,0,0,0,44.84,35.46a3.13,3.13,0,1,1-6.26,0,19.07,19.07,0,0,1,5.83-14,19.07,19.07,0,0,1,14-5.83A3.14,3.14,0,0,1,60.62,21Zm0,0"/><path d="M115.5,39.3a64.55,64.55,0,0,0-9.42-12,77.27,77.27,0,0,0-11.89-10l-4.11,7.3A66.76,66.76,0,0,1,108.46,43.8a67.26,67.26,0,0,1-19.65,20,54.11,54.11,0,0,1-25.59,8.93l-4.83,8.6a62.26,62.26,0,0,0,27.34-6.19,70.17,70.17,0,0,0,22.65-17.4,73,73,0,0,0,7.1-9.45,8.4,8.4,0,0,0,0-9Zm0,0"/></svg><span class="wf-issue-control-label">{{if status == 'new'}}<?php _e('Ignore', 'wordfence'); ?>{{else}}<?php _e('Stop Ignoring', 'wordfence'); ?>{{/if}}</span></a>
<?php if (isset($ignoreC) && isset($ignoreP)): ?>
<ul class="wf-issue-control-ignore-menu">
	<li class="wf-issue-control-ignore-menu-ignorec"><div><?php echo $ignoreC; ?></div></li>
	<li class="wf-issue-control-ignore-menu-ignorep"><div><?php echo $ignoreP; ?></div></li>
</ul>
<?php endif; ?>
