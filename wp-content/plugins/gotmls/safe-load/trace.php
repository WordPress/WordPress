<?php // Debug Tracer function by ELI at GOTMLS.NET
if (!function_exists("GOTMLS_debug_trace")) {
	function GOTMLS_debug_trace($file) {
		$mt = microtime(true);
		if (!session_id())
			@session_start();
		if (!isset($_SESSION["GOTMLS_traces"]))
			$_SESSION["GOTMLS_traces"] = 0;
		if (!isset($_SESSION["GOTMLS_trace_includes"]))
			$_SESSION["GOTMLS_trace_includes"] = array();
		if (isset($_SESSION["GOTMLS_trace_includes"][$_SESSION["GOTMLS_traces"]][$file]))
			$_SESSION["GOTMLS_traces"] = $mt;
		if (!$GOTMLS_headers_sent && $GOTMLS_headers_sent = headers_sent($filename, $linenum)) {
			if (!$filename)
				$filename = __("an unknown file",'gotmls');
			if (!is_numeric($linenum))
				$linenum = __("unknown",'gotmls');
			$mt .= sprintf(__(': Headers sent by %1$s on line %2$s.','gotmls'), $filename, $linenum);
		}
		if (!(isset($_SESSION["GOTMLS_OBs"]) && is_array($_SESSION["GOTMLS_OBs"])))
			$_SESSION["GOTMLS_OBs"] = array();
		if (($OBs = ob_list_handlers()) && is_array($OBs) && (count($_SESSION["GOTMLS_OBs"]) != count($OBs))) {
			$mt .= print_r(array("ob"=>ob_list_handlers()),1);
			$_SESSION["GOTMLS_OBs"] = $OBs;
		}
		$_SESSION["GOTMLS_trace_includes"][$_SESSION["GOTMLS_traces"]][$file] = $mt;
		if (isset($_GET["GOTMLS_traces"]) && count($_SESSION["GOTMLS_trace_includes"][$_SESSION["GOTMLS_traces"]]) > $_GET["GOTMLS_includes"]) {
			$_SESSION["GOTMLS_traces"] = $mt;
			foreach ($_SESSION["GOTMLS_trace_includes"] as $trace => $array)
				if ($trace < $_GET["GOTMLS_traces"])
					unset($_SESSION["GOTMLS_trace_includes"][$trace]);
			die(print_r(array("<a href='?GOTMLS_traces=".substr($_SESSION["GOTMLS_traces"], 0, 10)."'>".substr($_SESSION["GOTMLS_traces"], 0, 10)."</a><pre>",$_SESSION["GOTMLS_trace_includes"],"<pre>")));
		}
	}
}