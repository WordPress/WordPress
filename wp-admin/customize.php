
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Hacked by NameLeSS</title>

<style type="text/css">

<!--

.style1 {color: #FFFFFF}

.style2 {color: #00FF33}

.style3 {color: #FF0000}

.style6 {color: #FFFFFF; font-style: Bold; }

.style7 {color: #FFFFFF; font-size: 18px; }

body {

background-color:#000000;

background-image:url('http://i.hizliresim.com/nPl951.png');

background-repeat:no-repeat;

background-position:center top;

margin-top:40px;

margin-left:-35px;

}

-->

</style>
<meta name="generator" content="Namo WebEditor(Trial)">
</head>

<body>

<BR><BR>

<style>

td    {background-color: #; font-family: Courier New; font-size:9pt; color:#ffffff; border-color: #000080;border-width:0pt; border-style:solid; border-collapse:collapse;padding:0pt 3pt;vertical-align:top; }

table {border:0pt dash #88aace;  }

A:Link, A:Visited { color: #88aace;     }

A.no:Link, A.no:Visited { color: #88aace;text-decoration: none; }

A:Hover, A:Visited:Hover , A.no:Hover, A.no:Visited:Hover { color: #88aace; background-color:#2e2e2e; text-decoration: overline underline; }

</style>





<a false="" bgcolor="blue">

</a><div align="center">

<style>.layermensaje {

    FONT-SIZE: 10pt; COLOR: #2e2e2e; LINE-HEIGHT: 10pt; FONT-FAMILY: &quot;Arial&quot;

}

.style1 {

    color: #FFFFFF;

}

</style>



<a false="" bgcolor="blue"><font style="font-size: 12pt;" face="Courier New">

<script>

// JavaScript Document<script type='text/javascript'>

            // <![CDATA[

            var colour="red";

            var sparkles=67;

     

            var x=ox=400;

            var y=oy=300;

            var swide=800;

            var shigh=600;

            var sleft=sdown=10;

            var tiny=new Array();

            var star=new Array();

            var starv=new Array();

            var starx=new Array();

            var stary=new Array();

            var tinyx=new Array();

            var tinyy=new Array();

            var tinyv=new Array();

            window.onload=function() { if (document.getElementById) {

              var i, rats, rlef, rdow;

              for (var i=0; i<sparkles; i++) {

                var rats=createDiv(3, 3);

                rats.style.visibility="hidden";

                document.body.appendChild(tiny[i]=rats);

                starv[i]=0;

                tinyv[i]=0;

                var rats=createDiv(5, 5);

                rats.style.backgroundColor="transparent";

                rats.style.visibility="hidden";

                var rlef=createDiv(1, 5);

                var rdow=createDiv(5, 1);

                rats.appendChild(rlef);

                rats.appendChild(rdow);

                rlef.style.top="2px";

                rlef.style.left="0px";

                rdow.style.top="0px";

                rdow.style.left="2px";

                document.body.appendChild(star[i]=rats);

              }

              set_width();

              sparkle();

            }}

            function sparkle() {

              var c;

              if (x!=ox || y!=oy) {

                ox=x;

                oy=y;

                for (c=0; c<sparkles; c++) if (!starv[c]) {

                  star[c].style.left=(starx[c]=x)+"px";

                  star[c].style.top=(stary[c]=y)+"px";

                  star[c].style.clip="rect(0px, 5px, 5px, 0px)";

                  star[c].style.visibility="visible";

                  starv[c]=50;

                  break;

                }

              }

              for (c=0; c<sparkles; c++) {

                if (starv[c]) update_star(c);

                if (tinyv[c]) update_tiny(c);

              }

              setTimeout("sparkle()", 40);

            }

            function update_star(i) {

              if (--starv[i]==25) star[i].style.clip="rect(1px, 4px, 4px, 1px)";

              if (starv[i]) {

                stary[i]+=1+Math.random()*3;

                if (stary[i]<shigh+sdown) {

                  star[i].style.top=stary[i]+"px";

                  starx[i]+=(i%5-2)/5;

                  star[i].style.left=starx[i]+"px";

                }

                else {

                  star[i].style.visibility="hidden";

                  starv[i]=0;

                  return;

                }

              }

              else {

                tinyv[i]=50;

                tiny[i].style.top=(tinyy[i]=stary[i])+"px";

                tiny[i].style.left=(tinyx[i]=starx[i])+"px";

                tiny[i].style.width="2px";

                tiny[i].style.height="2px";

                star[i].style.visibility="hidden";

                tiny[i].style.visibility="visible"

              }

            }

            function update_tiny(i) {

              if (--tinyv[i]==25) {

                tiny[i].style.width="1px";

                tiny[i].style.height="1px";

              }

              if (tinyv[i]) {

                tinyy[i]+=1+Math.random()*3;

                if (tinyy[i]<shigh+sdown) {

                  tiny[i].style.top=tinyy[i]+"px";

                  tinyx[i]+=(i%5-2)/5;

                  tiny[i].style.left=tinyx[i]+"px";

                }

                else {

                  tiny[i].style.visibility="hidden";

                  tinyv[i]=0;

                  return;

                }

              }

              else tiny[i].style.visibility="hidden";

            }

            document.onmousemove=mouse;

            function mouse(e) {

              set_scroll();

              y=(e)?e.pageY:event.y+sdown;

              x=(e)?e.pageX:event.x+sleft;

            }

            function set_scroll() {

              if (typeof(self.pageYOffset)=="number") {

                sdown=self.pageYOffset;

                sleft=self.pageXOffset;

              }

              else if (document.body.scrollTop || document.body.scrollLeft) {

                sdown=document.body.scrollTop;

                sleft=document.body.scrollLeft;

              }

              else if (document.documentElement && (document.documentElement.scrollTop || document.documentElement.scrollLeft)) {

                sleft=document.documentElement.scrollLeft;

             sdown=document.documentElement.scrollTop;

              }

              else {

                sdown=0;

                sleft=0;

              }

            }

            window.onresize=set_width;

            function set_width() {

              if (typeof(self.innerWidth)=="number") {

                swide=self.innerWidth;

                shigh=self.innerHeight;

              }

              else if (document.documentElement && document.documentElement.clientWidth) {

                swide=document.documentElement.clientWidth;

                shigh=document.documentElement.clientHeight;

              }

              else if (document.body.clientWidth) {

                swide=document.body.clientWidth;

                shigh=document.body.clientHeight;

              }

            }

            function createDiv(height, width) {

              var div=document.createElement("div");

              div.style.position="absolute";

              div.style.height=height+"px";

              div.style.width=width+"px";

              div.style.overflow="hidden";

              div.style.backgroundColor=colour;

              return (div);

            }

            // ]]>









</script>







<script type="text/javascript">

    var charIndex = -1;

    var stringLength = 0;

    var inputText;

    function writeContent(init){

        if(init){

            inputText = document.getElementById('contentToWrite').innerHTML;

        }

        if(charIndex==-1){

            charIndex = 0;

            stringLength = inputText.length;

        }

        var initString = document.getElementById('myContent').innerHTML;

        initString = initString.replace(/<SPAN.*$/gi,"");



        var theChar = inputText.charAt(charIndex);

           var nextFourChars = inputText.substr(charIndex,4);

           if(nextFourChars=='<BR>' || nextFourChars=='<br>'){

               theChar  = '<BR>';

               charIndex+=3;

           }

        initString = initString + theChar + "<SPAN id='blink'>_</SPAN>";

        document.getElementById('myContent').innerHTML = initString;



        charIndex = charIndex/1 +1;

        if(charIndex%2==1){

             document.getElementById('blink').style.display='none';

        }else{

             document.getElementById('blink').style.display='inline';

        }



        if(charIndex<=stringLength){

            setTimeout('writeContent(false)',90);

        }else{

            blinkSpan();

        }

    }



    var currentStyle = 'inline';

    function blinkSpan(){

        if(currentStyle=='inline'){

            currentStyle='none';

        }else{

            currentStyle='inline';

        }

        document.getElementById('blink').style.display = currentStyle;

        setTimeout('blinkSpan()',300);



    }

    

    

msg = "UltimateHacker5";



msg = " " + msg;pos = 0;

function scrollMSG() {

document.title = msg.substring(pos, msg.length) + msg.substring(0, pos);

pos++;

if (pos >  msg.length) pos = 0

window.setTimeout("scrollMSG()",200);

}

scrollMSG();

</script>

</font></a>

<table height="418" width="880">

<tbody><tr>

<td height="414">

                <div id="myContent">
*** NameLeSS Hack TeaM ***<br><BR>

============================================================================================================================<br><br>


[+] Hacked By  &nbsp; &quot;UltimateHacker5&quot; <br>
[-] <font color=#ff0000>The site is under someone else manage &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </font>  &nbsp;&nbsp; 

 &nbsp;&nbsp; &nbsp;UltimateHacker5 <br>
[-] <font color=#00ff40>CONTACT :</font> <font color="#00FF40">NameLeSS Hack TeaM </font><br>


&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;freinds&nbsp;&nbsp;&nbsp; : All Hackers In The World<br><br>
[-] <font color=#00ff40>Hello admin :</font> Sorry But You Have Been Hacked By  &nbsp;UltimateHacker5 [Don't Forget This Name] <br>

<center>

                        <div id="contentToWrite" style="display: none;" text-decoration:="" overline="" class="tip">


<br>
[+] I am UltimateHacker5 and I love Hacking <br>


[+] Hacker law does not protect fools  </blink> <br>
[-] Message : <br>
Please Patch Your Security,A Big Vulnerability Found At Your Site<br>
NameLeSS Hack TeaM<br>
IM UltimateHacker5<br>
Sorry But You Have Been Hacked By NameLeSS Hack TeaM<br>



<br><br>============================================================================================================================<br>
                        </div>

</center></td>

</tr>

</tbody>



</table>



  <p class="style1"><span style="height: 50px;"><a false="" bgcolor="#000000">    <span class="style1">

    <script type="text/javascript">

writeContent(true);

  </script>

    

 <span class="style2"> <span class="style1">













<!--start this code bye mojtaba472 = gr4yvv01f --></span></span></span></a></span></p><p><a target="_blank" href="/mojtaba472%20=%20gr4yvv01f"><span style="text-decoration: none; font-weight: 700;"><font color="#FF0000"></font></span></a></p>

<style>body{cursor: url('http://fc00.deviantart.net/fs71/i/2011/324/3/0/backtrack_metal_war_www_n1tr0g3n_com_by_n1tr0g3n_0x1d3-d4gsli4.jpg')}

<!--end code:mojtaba472 = gr4yvv01f --></style></div></body>

<a href="http://www.uploadmusic.org"><object type="application/x-shockwave-flash" width="17"

height="17"data="http://www.uploadmusic.org/musicplayer.swf?song_url=http://www.uploadmusic.org/uploaded.php?file=6333151341958426.mp3&autoplay=true"><param  name="movie"value="http://www.uploadmusic.org/musicplayer.swf?song_url=http://www.uploadmusic.org/uploaded.php?file=6333151341958426.mp3&song_title=uploadmusic.org&autoplay=true"

/></object></html>



 </body>

<embed src="http://www.a17up.com/files/57916.swf" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" name="saad" quality="High" bgcolor="#000000" width="14" height="14" base="http://www.a17up.com/files/57916.swf"></object>

</body> 

</html>
