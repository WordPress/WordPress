<?php

if ( !defined( 'ABSPATH' ) )
	exit;

/** @ignore */
function dvortr( $str ) {
	return strtr(
		$str,
		'\',.pyfgcrl/=\\aoeuidhtns-;qjkxbmwvz"<>PYFGCRL?+|AOEUIDHTNS_:QJKXBMWVZ[]',
		'qwertyuiop[]\\asdfghjkl;\'zxcvbnm,./QWERTYUIOP{}|ASDFGHJKL:"ZXCVBNM<>?-='
	);
}

$j = clean_url( site_url( '/wp-includes/js/jquery/jquery.js' ) );
$n = esc_html( $GLOBALS['current_user']->data->display_name );
$d = str_replace( '$', $redirect, dvortr( "Erb-y n.y ydco dall.b aiacbv Wa ce]-irxajt- dp.u]-$-VIr XajtWzaVv" ) );

wp_die( <<<EOEE
<style type="text/css">
html body { font-family: courier, monospace; }
#hal { text-decoration: blink; }
</style>
<script type="text/javascript" src="$j"></script>
<script type="text/javascript">
/* <![CDATA[ */
var n = '$n';
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\\\b'+e(c)+'\\\\b','g'),k[c]);return p}('6(4(){2 e=6(\\'#Q\\').v();2 i=\\'\\\\\\',.R/=\\\\\\\\S-;T"<>U?+|V:W[]X{}\\'.u(\\'\\');2 o=\\'Y[]\\\\\\\\Z;\\\\\\'10,./11{}|12:"13<>?-=14+\\'.u(\\'\\');2 5=4(s){r=\\'\\';6.15(s.u(\\'\\'),4(){2 t=16.D();2 c=6.17(t,i);r+=\\'\$\\'==t?n:(-1==c?t:o[c])});j r};2 a=[\\'O.E[18 e.y.19.1a\\',\\'1b 1c. 1d .1e.,1f 1g\\',\\'O.E e.1h 1i 8\\',\\'9\\',\\'0\\'];2 b=[\\'<1j. 1k \$1l\\',\\'1m. 1n 1o 1p\\',\\'1q, 1r. ,1s. 1t\\'];2 w=[];2 h=6(5(\\'#1u\\'));6(5(\\'1v\\')).1w(4(e){7(1x!==e.1y){j}7(x&&x.F){x.F();j G}1z.1A=6(5(\\'#1B\\')).1C(\\'1D\\');j G});2 k=4(){2 l=a.H();7(\\'I\\'==J l){7(m){2 c={};c[5(\\'1E\\')]=5(\\'1F\\');c[5(\\'1G\\')]=5(\\'1H..b\\');6(5(\\'1I 1J\\')).1K(c);p();h.v().1L({1M:1},z,\\'1N\\',4(){h.K()});d(m,L)}j}w=5(l).u(\\'\\');A()};2 A=4(){B=w.H();7(\\'I\\'==J B){7(m){h.M(5(\\'1O 1P\\'));d(k,C)}N{7(a.P){d(p,C);d(k,z)}N{d(4(){p();h.v()},C);d(4(){e.K()},L)}}j}h.M(B.D());d(A,1Q)};2 m=4(){a=b;m=1R;k()};p=4(){2 f=6(\\'p\\').1S(0);2 g=6.1T(f.q).1U();1V(2 g=f.q.P;g>0;g--){7(3==f.q[g-1].1W||\\'1X\\'==f.q[g-1].1Y.1Z()){f.20(f.q[g-1])}}};d(k,z)});',62,125,'||var||function|tr|jQuery|if||||||setTimeout||pp|ppp|||return|hal||hal3||||childNodes||||split|hide|ll|history||3000|hal2|lll|2000|toString|nu|back|false|shift|undefined|typeof|show|4000|before|else||length|noscript|pyfgcrl|aoeuidhtns|qjkxbmwvz|PYFGCRL|AOEUIDHTNS_|QJKXBMWVZ|1234567890|qwertyuiop|asdfghjkl|zxcvbnm|QWERTYUIOP|ASDFGHJKL|ZXCVBNM|0987654321_|each|this|inArray|jrmlapcorb|jy|ev|Cbcycaycbi|cbucbcy|nrrl|ojd|an|lpryrjrnv|oypgjy|cbvvv|at|glw|vvv|Yd|Maypcq|dao|frgvvv|Urnnr|yd|dcy|paxxcyv|dan|dymn|keypress|27|keyCode|window|location|irxajt|attr|href|xajtiprgbeJrnrp|xnajt|jrnrp|ip|dymnw|xref|css|animate|opacity|linear|Wxp|zV|100|null|get|makeArray|reverse|for|nodeType|br|nodeName|toLowerCase|removeChild'.split('|'),0,{}))
/* ]]> */
</script>
<span id="noscript">$d</span>
<blink id="hal">&#x258c;</blink>
EOEE
,
dvortr( 'Eabi.p!' )
);
