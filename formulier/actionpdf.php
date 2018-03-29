<?php
require('WriteHTML.php');

$pdf=new PDF_HTML();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();
$pdf->Image('logo.png',10,10,75);
$pdf->SetFont('Arial','B',16);
$pdf->SetTextColor(153,185,66);
$pdf->WriteHTML('');
$pdf->WriteHTML('<br><br><br>');
$pdf->SetFont('Arial','',10); 
$htmlTable='<TABLE>
<TR>
<TD>Bedrijfsnaam:&nbsp;</TD>
<TD>'.$_POST['bedrijfsnaam'].'</TD>
</TR>
<TR>
<TD>Adres:</TD>
<TD>'.$_POST['leveringsadres'].'</TD>
</TR>
<TR>
<TD>Telefoon:</TD>
<TD>'.$_POST['iban'].'</TD>
</TR>
<TR>
<TD>KvK Nummer: </TD>
<TD>'.$_POST['kvknummer'].'</TD>
</TR>
<TR>
<TD>Contactpersoon:</TD>
<TD>'.$_POST['email'].'</TD>
</TR>
<TR>
<TD>E-mail:</TD>
<TD>'.$_POST['telnummer'].'</TD>
</TR>
<TR>
<TD>Administratief contactpersoon:</TD>
<TD>'.$_POST['postcodehuisnummer'].'</TD>
</TR>
<TR>
<TD>E-mail administratief contactpersoon:</TD>
<TD>'.$_POST['factuuradres'].'</TD>
</TR>
<TR>
<TD>Technisch contactpersoon:</TD>
<TD>'.$_POST['postcode2'].'</TD>
</TR>
<TR>
<TD>E-mail technisch contactpersoon:</TD>
<TD>'.$_POST['plaats2'].'</TD>
</TR>
</TABLE>';

$htmlTable3='<TABLE>
<TR>
<TD>Factuuradres:</TD>
<TD>'.$_POST['adresvanfactuur'].'</TD>
</TR>
<TR>
<TD>Naam rekeninghouder:</TD>
<TD>'.$_POST['voorkeur'].'</TD>
</TR>
<TR>
<TD>IBAN nummer:</TD>
<TD>'.$_POST['new1'].'</TD>
</TR>
<TR>
<TD>Einddatum huidig contract:</TD>
<TD>'.$_POST['new2'].'</TD>
</TR>
<TR>
<TD>Kopie legitimatie (paspoort/rijbewijs/ID-kaart) tekenbevoegde bijgevoegd?</TD>
<TD>'.$_POST['looptijd'].'</TD>
</TR>
<TR>
<TD>Kopie uittreksel KvK (max. 6 maanden oud) bijgevoegd?</TD>
<TD>'.$_POST['looptijd2'].'</TD>
</TR>
<TR>
<TD>Kopie van 3 recente facturen bijgevoegd?</TD>
<TD>'.$_POST['looptijd3'].'</TD>
</TR>
<TR>
<TD>Kopie van 3 recente facturen</TD>
<TD>'.$_POST['pic'].' </TD>
</TR>
</TABLE><br>';

$htmlTable4='<TABLE>
<TR>
<TD>Huidige leverancier:</TD>
<TD>Mobiele Telefonie - Sim Only</TD>
</TR>
<TR>
<TD>Huidig type abonnement</TD>
<TD>Exclusief toeste</TD>
</TR>
<TR>
<TD>Gegarandeerde Besparing</TD>
<TD>10%</TD>
</TR>
<TR>
<TD>Looptijd contract</TD>
<TD>2 jaar</TD>
</TR>
<TR>
<TD>Provider</TD>
<TD>T-Mobile</TD>
</TR>

</TABLE>';

$htmlTable6='<TABLE>
<TR>
<TD>Naam:</TD>
<TD>'.$_POST['naam'].'</TD>
</TR>
<TR>
<TD>Functie:</TD>
<TD>'.$_POST['functie'].'</TD>
</TR>
<TR>
<TD>Datum</TD>
<TD>'.date("d-m-Y").'</TD>
</TR>
</TABLE>';


$pdf->SetFont('Arial','B',7);
$pdf->WriteHTML2("$htmlTable");



$pdf->SetFont('Arial','',6); 
$pdf->SetTextColor(0,0,0);
$pdf->WriteHTML('Op basis van een gratis en vrijblijvende Telecom Scan adviseert De BespaarConsulent hoe u de Mobiele Communicatie het beste opnieuw kunt contracteren. Onze besparingsgarantie wordt gebaseerd op de informatie die op de factuur van uw huidige Mobiele Telefonie leverancier vermeld is. U dient 3 recente, opeenvolgende facturen aan ons ter beschikking te stellen om voor de hieronder genoemde besparing in aanmerking te komen.');
$pdf->SetFont('Arial','B',7);
$pdf->SetTextColor(153,185,66);
$pdf->WriteHTML2("$htmlTable2");
$pdf->WriteHTML2("$htmlTable3");
$pdf->WriteHTML2("$htmlTable4");
$pdf->SetFont('Arial','',6); 
$pdf->SetTextColor(0,0,0);
$pdf->WriteHTML('Opdrachtgever verstrekt middels ondertekening van dit formulier een volmacht aan De BespaarConsulent voor het afsluiten van een nieuwe overeenkomst voor Mobiele Telefonie conform bovenstaande afspraken en tegen de minimaal gegarandeerde besparing.<br><br>

Daarnaast machtigt opdrachtgever De BespaarConsulent en partner Telecombinatie Zakelijk om de contractgegevens van opdrachtgever (vb einde contractdatum, opzegtermijn, nummerplan en eventuele kosten) bij de huidige telecomaanbieder op te vragen en alle handelingen te verrichten die nodig zijn om de telecomdienst te activeren.<br><br>

Opdrachtgever machtigt tevens de provider tot automatische incasso van de Mobiele Telefonie facturen.<br><br>

Opdrachtgever is akkoord met de eerstmogelijke ingangsdatum en verklaart hierbij geen nieuwe Mobiele Telefonie overeenkomsten aan te gaan tijdens de overeengekomen looptijd.');
$pdf->SetFont('Arial','B',7);
$pdf->SetTextColor(153,185,66);
$pdf->WriteHTML2("$htmlTable5");
$pdf->WriteHTML2("$htmlTable6");
$pdf->WriteHTML('Handtekening: '.$_POST['naam'].'' );
$pdf->SetFont('Arial','B',7);
$pdf->SetTextColor(0,0,0);
$pdf->Output(); 

// email stuff (change data below)
$to = "taylan_oncu@live.nl"; 
$from = "info@nsvo.nl"; 
$subject = "NSVO - Nieuw contract"; 
$message = "<p>Zie bijlage.</p>";

// a random hash will be necessary to send mixed content
$separator = md5(time());

// carriage return type (we use a PHP end of line constant)
$eol = PHP_EOL;

// attachment name
$filename = "test.pdf";

// encode data (puts attachment in proper format)
$pdfdoc = $pdf->Output("", "S");
$attachment = chunk_split(base64_encode($pdfdoc));

// main header
$headers  = "From: ".$from.$eol;
$headers .= "MIME-Version: 1.0".$eol; 
$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

// no more headers after this, we start the body! //

$body = "--".$separator.$eol;
$body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
$body .= "This is a MIME encoded message.".$eol;

// message
$body .= "--".$separator.$eol;
$body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
$body .= $message.$eol;

// attachment
$body .= "--".$separator.$eol;
$body .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol; 
$body .= "Content-Transfer-Encoding: base64".$eol;
$body .= "Content-Disposition: attachment".$eol.$eol;
$body .= $attachment.$eol;
$body .= "--".$separator."--";

// send message
mail($to, $subject, $body, $headers);


?>