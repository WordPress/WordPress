<?php
//simple safety check
$a = str_replace("\\", '/', dirname(__FILE__));
$b = str_replace("\\", '/', dirname($_POST['file']));
$da = implode('/',array_slice(explode('/', $a),0,-4));
$idx = strpos($b,$da);
if (($idx === false) || ($idx > 0))
	exit();

require_once(dirname(__FILE__).'/class.parser.php');
$parser = new csp_l10n_parser($_POST['path'], $_POST['textdomain'], true, false);
$r = $parser->parseFile($_POST['file'], $_POST['type']);
echo base64_encode(serialize($r));
exit();
?>
