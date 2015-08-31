<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<h4><?php _e('Table of Contents', 'w3-total-cache'); ?></h4>

<div id="toc">
    <ul>
    <?php foreach ($faq as $section => $entries): ?>
    <li class="col">
        <h5><?php echo strtoupper($section); ?>:</h5>
        <ul>
    	    <?php foreach ($entries as $id => $entry): ?>
                <?php if (!isset($entry['question'])): ?>
            <h5><?php echo strtoupper($id); ?>:</h5>
            <ul>
                <?php foreach ($entry as $entry2): ?>
                <li><a href="#q<?php echo $entry2['index']; ?>"><?php echo $entry2['question']; ?></a></li>
                <?php endforeach; ?>
            </ul>
                <?php else:?>
	        <li><a href="#q<?php echo $entry['index']; ?>"><?php echo $entry['question']; ?></a></li>
                <?php endif ?>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endforeach; ?>
    </ul>
</div>
<div id="qa">
	<hr />
    <?php foreach ($faq as $section => $entries): ?>
    <?php foreach ($entries as $id => $entry): ?>
    <?php if (!isset($entry['question'])): ?>
    <?php foreach ($entry as $entry2): ?>
                    <p id="q<?php echo $entry2['index']; ?>"><strong><?php echo $entry2['question']; ?></strong></p>
                    <?php echo $entry2['answer']; ?>
                    <p align="right"><a href="#toc">back to top</a></p>
    <?php endforeach; ?>
    <?php else:?>

    <p id="q<?php echo $entry['index']; ?>"><strong><?php echo $entry['question']; ?></strong></p>
        <?php echo $entry['answer']; ?>
    	<p align="right"><a href="#toc">back to top</a></p>
            <?php endif ?>

        <?php endforeach; ?>
    <?php endforeach; ?>
</div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
