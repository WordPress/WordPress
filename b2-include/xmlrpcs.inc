<?php
// by Edd Dumbill (C) 1999-2001
// <edd@usefulinc.com>
// $Id$


# additional fixes for case of missing xml extension file by Michel Valdrighi <m@tidakada.com>


// Copyright (c) 1999,2000,2001 Edd Dumbill.
// All rights reserved.
//
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions
// are met:
//
//    * Redistributions of source code must retain the above copyright
//      notice, this list of conditions and the following disclaimer.
//
//    * Redistributions in binary form must reproduce the above
//      copyright notice, this list of conditions and the following
//      disclaimer in the documentation and/or other materials provided
//      with the distribution.
//
//    * Neither the name of the "XML-RPC for PHP" nor the names of its
//      contributors may be used to endorse or promote products derived
//      from this software without specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
// "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
// LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
// FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
// REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
// INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
// SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
// HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
// STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
// OF THE POSSIBILITY OF SUCH DAMAGE.

// XML RPC Server class
// requires: xmlrpc.inc

// listMethods: either a string, or nothing
$_xmlrpcs_listMethods_sig=array(array($xmlrpcArray, $xmlrpcString), 
																array($xmlrpcArray));
$_xmlrpcs_listMethods_doc='This method lists all the methods that the XML-RPC server knows how to dispatch';
function _xmlrpcs_listMethods($server, $m) {
	global $xmlrpcerr, $xmlrpcstr, $_xmlrpcs_dmap;
	$v=new xmlrpcval();
	$dmap=$server->dmap;
	$outAr=array();
	for(reset($dmap); list($key, $val)=each($dmap); ) {
		$outAr[]=new xmlrpcval($key, "string");
	}
	$dmap=$_xmlrpcs_dmap;
	for(reset($dmap); list($key, $val)=each($dmap); ) {
		$outAr[]=new xmlrpcval($key, "string");
	}
	$v->addArray($outAr);
	return new xmlrpcresp($v);
}

$_xmlrpcs_methodSignature_sig=array(array($xmlrpcArray, $xmlrpcString));
$_xmlrpcs_methodSignature_doc='Returns an array of known signatures (an array of arrays) for the method name passed. If no signatures are known, returns a none-array (test for type != array to detect missing signature)';
function _xmlrpcs_methodSignature($server, $m) {
	global $xmlrpcerr, $xmlrpcstr, $_xmlrpcs_dmap;

	$methName=$m->getParam(0);
	$methName=$methName->scalarval();
	if (ereg("^system\.", $methName)) {
		$dmap=$_xmlrpcs_dmap; $sysCall=1;
	} else {
		$dmap=$server->dmap; $sysCall=0;
	}
	//	print "<!-- ${methName} -->\n";
	if (isset($dmap[$methName])) {
		if ($dmap[$methName]["signature"]) {
			$sigs=array();
			$thesigs=$dmap[$methName]["signature"];
			for($i=0; $i<sizeof($thesigs); $i++) {
				$cursig=array();
				$inSig=$thesigs[$i];
				for($j=0; $j<sizeof($inSig); $j++) {
					$cursig[]=new xmlrpcval($inSig[$j], "string");
				}
				$sigs[]=new xmlrpcval($cursig, "array");
			}
			$r=new xmlrpcresp(new xmlrpcval($sigs, "array"));
		} else {
			$r=new xmlrpcresp(new xmlrpcval("undef", "string"));
		}
	} else {
			$r=new xmlrpcresp(0,
						  $xmlrpcerr["introspect_unknown"],
						  $xmlrpcstr["introspect_unknown"]);
	}
	return $r;
}

$_xmlrpcs_methodHelp_sig=array(array($xmlrpcString, $xmlrpcString));
$_xmlrpcs_methodHelp_doc='Returns help text if defined for the method passed, otherwise returns an empty string';
function _xmlrpcs_methodHelp($server, $m) {
	global $xmlrpcerr, $xmlrpcstr, $_xmlrpcs_dmap;

	$methName=$m->getParam(0);
	$methName=$methName->scalarval();
	if (ereg("^system\.", $methName)) {
		$dmap=$_xmlrpcs_dmap; $sysCall=1;
	} else {
		$dmap=$server->dmap; $sysCall=0;
	}
	//	print "<!-- ${methName} -->\n";
	if (isset($dmap[$methName])) {
		if ($dmap[$methName]["docstring"]) {
			$r=new xmlrpcresp(new xmlrpcval($dmap[$methName]["docstring"]),
												"string");
		} else {
			$r=new xmlrpcresp(new xmlrpcval("", "string"));
		}
	} else {
			$r=new xmlrpcresp(0,
						  $xmlrpcerr["introspect_unknown"],
						  $xmlrpcstr["introspect_unknown"]);
	}
	return $r;
}

$_xmlrpcs_dmap=array(
										 "system.listMethods" =>
										 array("function" => "_xmlrpcs_listMethods",
													 "signature" => $_xmlrpcs_listMethods_sig,
													 "docstring" => $_xmlrpcs_listMethods_doc),
										 "system.methodHelp" =>
										 array("function" => "_xmlrpcs_methodHelp",
													 "signature" => $_xmlrpcs_methodHelp_sig,
													 "docstring" => $_xmlrpcs_methodHelp_doc),
										 "system.methodSignature" =>
										 array("function" => "_xmlrpcs_methodSignature",
													 "signature" => $_xmlrpcs_methodSignature_sig,
													 "docstring" => $_xmlrpcs_methodSignature_doc)
										 );

$_xmlrpc_debuginfo="";
function xmlrpc_debugmsg($m) {
	global $_xmlrpc_debuginfo;
	$_xmlrpc_debuginfo=$_xmlrpc_debuginfo . $m . "\n";
}

class xmlrpc_server {
  var $dmap=array();

  function xmlrpc_server($dispMap, $serviceNow=1) {
		global $HTTP_RAW_POST_DATA;
		// dispMap is a despatch array of methods
		// mapped to function names and signatures
		// if a method
		// doesn't appear in the map then an unknown
		// method error is generated
		$this->dmap=$dispMap;
		if ($serviceNow) {
			$this->service();
		}
  }

	function serializeDebug() {
		global $_xmlrpc_debuginfo;
		if ($_xmlrpc_debuginfo!="") 
			return "<!-- DEBUG INFO:\n\n" .
				$_xmlrpc_debuginfo . "\n-->\n";
		else
			return "";
	}

	function service() {
		$r=$this->parseRequest();
		$payload="<?xml version=\"1.0\"?>\n" . 
			$this->serializeDebug() .
			$r->serialize();
		Header("Content-type: text/xml\r\nContent-length: " . 
					 strlen(trim($payload)));
		print trim($payload);
	}

	function verifySignature($in, $sig) {
		for($i=0; $i<sizeof($sig); $i++) {
			// check each possible signature in turn
			$cursig=$sig[$i];
			if (sizeof($cursig)==$in->getNumParams()+1) {
				$itsOK=1;
				for($n=0; $n<$in->getNumParams(); $n++) {
					$p=$in->getParam($n);
					// print "<!-- $p -->\n";
					if ($p->kindOf() == "scalar") {
						$pt=$p->scalartyp();
					} else {
						$pt=$p->kindOf();
					}
					// $n+1 as first type of sig is return type
					if ($pt != $cursig[$n+1]) {
						$itsOK=0;
						$pno=$n+1; $wanted=$cursig[$n+1]; $got=$pt;
						break;
					}
				}
			if ($itsOK) 
				return array(1);
			}
		}
		return array(0, "Wanted ${wanted}, got ${got} at param ${pno})");
	}

  function parseRequest($data="") {
	global $_xh,$HTTP_RAW_POST_DATA;
	global $xmlrpcerr, $xmlrpcstr, $xmlrpcerrxml, $xmlrpc_defencoding,
		$_xmlrpcs_dmap;

	

	if ($data=="") {
	  $data=$HTTP_RAW_POST_DATA;
	}
	$parser = xml_parser_create($xmlrpc_defencoding);

	$_xh[$parser]=array();
	$_xh[$parser]['st']="";
	$_xh[$parser]['cm']=0; 
	$_xh[$parser]['isf']=0; 
	$_xh[$parser]['params']=array();
	$_xh[$parser]['method']="";

	// decompose incoming XML into request structure

	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
	xml_set_element_handler($parser, "xmlrpc_se", "xmlrpc_ee");
	xml_set_character_data_handler($parser, "xmlrpc_cd");
	xml_set_default_handler($parser, "xmlrpc_dh");
	if (!xml_parse($parser, $data, 1)) {
	  // return XML error as a faultCode
	  $r=new xmlrpcresp(0,
						$xmlrpcerrxml+xml_get_error_code($parser),
						sprintf("XML error: %s at line %d",
					xml_error_string(xml_get_error_code($parser)),
				  xml_get_current_line_number($parser)));
	  xml_parser_free($parser);
	} else {
	  xml_parser_free($parser);
	  $m=new xmlrpcmsg($_xh[$parser]['method']);
	  // now add parameters in
	  $plist="";
	  for($i=0; $i<sizeof($_xh[$parser]['params']); $i++) {
			//print "<!-- " . $_xh[$parser]['params'][$i]. "-->\n";
			$plist.="$i - " .  $_xh[$parser]['params'][$i]. " \n";
			eval('$m->addParam(' . $_xh[$parser]['params'][$i]. ");");
	  }
		// uncomment this to really see what the server's getting!
		// xmlrpc_debugmsg($plist);
	  // now to deal with the method
		$methName=$_xh[$parser]['method'];
		if (ereg("^system\.", $methName)) {
			$dmap=$_xmlrpcs_dmap; $sysCall=1;
		} else {
			$dmap=$this->dmap; $sysCall=0;
		}
	  if (isset($dmap[$methName]['function'])) {
			// dispatch if exists
			if (isset($dmap[$methName]['signature'])) {
				$sr=$this->verifySignature($m, 
																	$dmap[$methName]['signature'] );
			}
			if ( (!isset($dmap[$methName]['signature']))
					 || $sr[0]) {
				// if no signature or correct signature
				if ($sysCall) { 
					eval('$r=' . $dmap[$methName]['function'] . 
							 '($this, $m);');
				} else {
					eval('$r=' . $dmap[$methName]['function'] . 
							 '($m);');
				}
			} else {
				$r=new xmlrpcresp(0,
						  $xmlrpcerr["incorrect_params"],
						  $xmlrpcstr["incorrect_params"].": ". $sr[1]);
			}
	  } else {
		// else prepare error response
		$r=new xmlrpcresp(0,
						  $xmlrpcerr["unknown_method"],
						  $xmlrpcstr["unknown_method"]);
	  }
	}
	return $r;
  }

  function echoInput() {
	global $HTTP_RAW_POST_DATA;

	// a debugging routine: just echos back the input
	// packet as a string value

	$r=new xmlrpcresp;
	$r->xv=new xmlrpcval( "'Aha said I: '" . $HTTP_RAW_POST_DATA, "string");
	print $r->serialize();
  }
}

?>
