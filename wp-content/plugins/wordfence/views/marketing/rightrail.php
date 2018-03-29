<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<?php if (!wfConfig::get('isPaid')) { ?>
	<div id="wordfenceRightRail" class="<?php echo wfStyle::rightRailClasses(); ?>"> 
		<ul>
			<li><a href="https://www.wordfence.com/gnl1rightRailGetPremium/wordfence-signup/" target="_blank" rel="noopener noreferrer"><img src="<?php echo wfUtils::getBaseURL() . 'images/rr_premium.png'; ?>" alt="Upgrade your protection - Get Wordfence Premium"></a></li>
			<li><a href="https://www.wordfence.com/gnl1rightRailSiteCleaning/wordfence-site-cleanings/" target="_blank" rel="noopener noreferrer"><img src="<?php echo wfUtils::getBaseURL() . 'images/rr_sitecleaning.jpg'; ?>" alt="Have you been hacked? Get help from Wordfence"></a></li> 
			<li>
				<p class="center"><strong>Would you like to remove these ads?</strong><br><a href="https://www.wordfence.com/gnl1rightRailBottomUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer">Get Premium</a></p>
			</li>
		</ul>
	</div>
<?php } ?>