<?php

/** @var wfRequestModel $hit */
/** @var stdClass $hitData */

$title = sprintf('Debugging #%d as False Positive', $hit->id);

$fields = array(
	'URL'         => $hit->URL,
	'Timestamp'   => date('r', $hit->ctime),
	'IP'          => wfUtils::inet_ntop($hit->IP),
	'Status Code' => $hit->statusCode,
	'User Agent'  => $hit->UA,
	'Referer'     => $hit->referer,
);

if (isset($hitData->fullRequest)) {
	$requestString = base64_decode($hitData->fullRequest);
	$request = wfWAFRequest::parseString($requestString);
} else {
	$request = new wfWAFRequest();
	$request->setAuth(array());
	$request->setBody(array());
	$request->setCookies(array());
	$request->setFileNames(array());
	$request->setFiles(array());
	$request->setHeaders(array());
	$request->setHost('');
	$request->setIp('');
	$request->setMethod('GET');
	$request->setPath('');
	$request->setProtocol('http');
	$request->setQueryString(array());
	$request->setTimestamp('');
	$request->setUri('');

	$headers = array();
	$urlPieces = parse_url($hit->URL);
	if ($urlPieces) {
		if (array_key_exists('scheme', $urlPieces)) {
			$request->setProtocol($urlPieces['scheme']);
		}
		if (array_key_exists('host', $urlPieces)) {
			$request->setHost($urlPieces['host']);
			$headers['Host'] = $urlPieces['host'];
		}
		$uri = '/';
		if (array_key_exists('path', $urlPieces)) {
			$request->setPath($urlPieces['path']);
			$uri = $urlPieces['path'];
		}
		if (array_key_exists('query', $urlPieces)) {
			$uri .= '?' . $urlPieces['query'];
			parse_str($urlPieces['query'], $query);
			$request->setQueryString($query);
		}
		$request->setUri($uri);
	}
	$headers['User-Agent'] = $hit->UA;
	$headers['Referer'] = $hit->referer;
	$request->setHeaders($headers);

	preg_match('/request\.([a-z]+)(?:\[(.*?)\](.*?))?/i', $hitData->paramKey, $matches);
	if ($matches) {
		switch ($matches[1]) {
			case 'body':
				$request->setMethod('POST');
				parse_str("$matches[2]$matches[3]", $body);
				$request->setBody($body);
				break;
		}
	}
}

$request->setIP(wfUtils::inet_ntop($hit->IP));
$request->setTimestamp($hit->ctime);


$waf = wfWAF::getInstance();
$waf->setRequest($request);

$result = '<strong class="ok">Passed</strong>';
$failedRules = array();
try {
	$waf->runRules();
} catch (wfWAFAllowException $e) {
	$result = '<strong class="ok">Whitelisted</strong>';
} catch (wfWAFBlockException $e) {
	$result = '<strong class="error">Blocked</strong>';
	$failedRules = $waf->getFailedRules();
} catch (wfWAFBlockSQLiException $e) {
	$result = '<strong class="error">Blocked For SQLi</strong>';
	$failedRules = $waf->getFailedRules();
} catch (wfWAFBlockXSSException $e) {
	$result = '<strong class="error">Blocked For XSS</strong>';
	$failedRules = $waf->getFailedRules();
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo esc_html($title) ?></title>
	<link rel="stylesheet" href="<?php echo wfUtils::getBaseURL() . wfUtils::versionedAsset('css/main.css'); ?>">
	<style>
		html {
			font-family: "Open Sans", Helvetica, Arial, sans-serif;
		}
		h1, h2, h3, h4, h5 {
			margin: 20px 0px 8px;
		}
		pre, p {
			margin: 8px 0px 20px;
		}
		pre.request-debug {
			padding: 12px;
			background: #fafafa;
			border: 1px solid #999999;
			overflow: auto;
		}
		pre.request-debug em {
			font-style: normal;
			padding: 1px;
			border: 1px solid #ffb463;
			background-color: #ffffe0;
			border-radius: 2px;
		}
		pre.request-debug strong {
			border: 1px solid #ff4a35;
			background-color: #ffefe7;
			margin: 1px;
		}
		.ok {
			color: #00c000;
		}
		.error {
			color: #ff4a35;
		}
		#wrapper {
			max-width: 1060px;
			margin: 0px auto;
		}
	</style>
</head>
<body>
<div id="wrapper">
	<h1><?php echo esc_html($title) ?></h1>

	<table class="wf-striped-table">
		<thead>
		<tr>
			<th colspan="2">Request Details</th>
		</tr>
		</thead>
		<?php foreach ($fields as $label => $value): ?>
			<tr>
				<td><?php echo esc_html($label) ?>:</td>
				<td><?php echo esc_html($value) ?></td>
			</tr>
		<?php endforeach ?>
	</table>

	<h4>HTTP Request: <?php echo $result ?></h4>
	<?php if (!isset($hitData->fullRequest)): ?>
		<em style="font-size: 14px;">This is a reconstruction of the request using what was flagged by the WAF.
			Full requests are only stored when <code>WFWAF_DEBUG</code> is enabled.</em>
	<?php endif ?>
	<pre class="request-debug"><?php
	$paramKey = wp_hash(uniqid('param', true));
	$matchKey = wp_hash(uniqid('match', true));

	$template = array(
		"[$paramKey]"  => '<em>',
		"[/$paramKey]" => '</em>',
		"[$matchKey]"  => '<strong>',
		"[/$matchKey]" => '</strong>',
	);
	$highlightParamFormat = "[$paramKey]%s[/$paramKey]";
	$highlightMatchFormat = "[$matchKey]%s[/$matchKey]";
	$requestOut = esc_html($request->highlightFailedParams($failedRules, $highlightParamFormat, $highlightMatchFormat));

	echo str_replace(array_keys($template), $template, $requestOut) ?></pre>

	<?php if ($failedRules): ?>
		<h4>Failed Rules</h4>
		<table class="wf-striped-table">
			<thead>
			<tr>
				<th>ID</th>
				<th>Category</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($failedRules as $paramKey => $categories) {
				foreach ($categories as $categoryKey => $failed) {
					foreach ($failed as $failedRule) {
						/** @var wfWAFRule $rule */
						$rule = $failedRule['rule'];
						printf("<tr><td>%d</td><td>%s</td></tr>", $rule->getRuleID(), $rule->getDescription());
					}
				}
			}
			?>
			</tbody>
		</table>

	<?php endif ?>

	<p>
		<button type="button" id="run-waf-rules">Run Through WAF Rules</button>
	</p>

	<script>
		document.getElementById('run-waf-rules').onclick = function() {
			document.location.href = document.location.href;
		}
	</script>


</div>

</body>
</html>
