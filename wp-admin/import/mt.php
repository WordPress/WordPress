<?php

class MT_Import {

	var $authors = array();
	var $posts = array();
		
	function header() {
		echo '<div class="wrap">';
		echo '<h2>' . __('Import Movable Type') . '</h2>';
	}

	function footer() {
		echo '</div>';		
	}
		
	function greet() {
		$this->header();
?>
<p>Howdy! We&#8217;re about to begin the process to import all of your Movable Type entries into WordPress. Before we get started, you need to edit this file (<code>import/mt.php</code>) and change one line so we know where to find your MT export file. To make this easy put the import file into the <code>wp-admin/import</code> directory. Look for the line that says:</p>
<p><code>define('MTEXPORT', '');</code></p>
<p>and change it to</p>
<p><code>define('MTEXPORT', 'import.txt');</code></p>
<p>You have to do this manually for security reasons.</p>
<p>If you've done that and you&#8217;re all ready, <a href="<?php echo add_query_arg('step', 1)  ?>">let's go</a>! Remember that the import process may take a minute or so if you have a large number of entries and comments. Think of all the rebuilding time you'll be saving once it's done. :)</p>
<p>The importer is smart enough not to import duplicates, so you can run this multiple times without worry if&#8212;for whatever reason&#8212;it doesn't finish. If you get an <strong>out of memory</strong> error try splitting up the import file into pieces. </p>
<?php
		$this->footer();
	}	
	
	function get_entries() {
		set_magic_quotes_runtime(0);
		$importdata = file(MTEXPORT); // Read the file into an array
		$importdata = implode('', $importdata); // squish it
		$importdata = preg_replace("/(\r\n|\n|\r)/", "\n", $importdata);
		$importdata = preg_replace("/\n--------\n/", "--MT-ENTRY--\n", $importdata);
		$this->posts = explode("--MT-ENTRY--", $importdata);
		unset($importdata);
		
		
	}
	
	function import() {
		if ('' != MTEXPORT && !file_exists(MTEXPORT)) die("The file you specified does not seem to exist. Please check the path you've given.");
		if ('' == MTEXPORT) die("You must edit the MTEXPORT line as described on the <a href='import-mt.php'>previous page</a> to continue.");
	
		$this->get_entries();
	}
	
	function dispatch() {
		if (empty($_GET['step']))
			$step = 0;
		else
			$step = (int) $_GET['step'];
		
		switch ($step) {
			case 0:
				$this->greet();
				break;
			case 1:
				$this->import();
				break;
		}
	}
	
	function MT_Import() {
		// Nothing.	
	}
}

$mt_import = new MT_Import();

register_importer('mt', 'Movable Type', 'Import posts and comments from your Movable Type blog', array($mt_import, 'dispatch'));

?>