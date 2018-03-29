<!DOCTYPE html>
<html>
<head>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
	<title>Calculatie</title>
	<style>
	              body {
	                  background:#ededed;
	              }
	              .inhoud {
	                  background:#fff;
	                  padding:20px 5px;
	                  margin:20px 0;
	              }
	              h1,h2,button {
	                  font-family: 'Source Sans Pro', sans-serif !important;
	                  font-size: 26px !important;
	                  text-transform: uppercase !important;
	                  font-weight: 800 !important;
	                  color: #3489d6;
	                  margin:0;
	                  vertical-align: middle;
	              }
	              h2 {
	                  font-size: 22px !important;
	                  display: inline-block;

	              }
	              button{
	                  width:100%;
	                  margin-top: 20px;
	                  padding:20px 0;
	                  color:white;
	              }
	              input[type=text]:disabled {
	                  background: #fff;
	              }
	              .informatie {
	                  margin:0 20px 10px 20px;
	                  overflow: hidden;
	              }
	              .verti i {
	                  vertical-align: middle;
	                  height: 28px;
	                  display: table-cell;
	              }
	              .verti h2 {
	                  float:right;
	                  display: inline-block;
	              }
	              .margin {
	                  margin:20px;
	              }
	              .streep {
	               border-bottom:1px black solid;
	              }
	              .rood {
	               color:red;
	              }
	              .groen {
	               color:green;
	              }
	</style>
</head>
<body>
	<div class="container">
		<div class="inhoud">
			<div class="row">
				<div class="informatie">
					<div class="col-xs-12">
						<h1>Resultaat</h1>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-1">
						Aan:
					</div>
					<div class="col-xs-11">
						<?php echo $_POST["aanhef"]; ?><?php echo $_POST["klantnaam"]; ?><?php echo $_POST["achternaam"]; ?><br>
						<?php echo $_POST["bedrijfsnaam"]; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-1">
						Datum:
					</div>
					<div class="col-xs-11">
						<?php echo date("Y-m-d") ?><br>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-1">
						Betreft:
					</div>
					<div class="col-xs-11">
						QuickScan Zonnepanelen
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-12">
						<?php if($_POST["aanhef"] == "Dhr."){ echo"Geachte heer " . $_POST['klantnaam'] . ","; } else { echo"Geachte mevrouw " . $_POST['klantnaam'] . ","; } ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-12">
						<p>Aansluitend op het bezoek van <?php echo $_POST["naamverkoper"]; ?> hebben wij voor u een QuickScan gemaakt aangaande de aanschaf van Zonnepanelen.</p>
						<p>Onze organisatie, de BesparingsConsulent, streeft ernaar om onze klanten zoveel mogelijk te ontzorgen en te helpen met kostenreducties binnen haar organisatie. Onze kracht zit hem in het feit dat wij samen met onze aangesloten partners een klantenportefieulle vertegenwoordigen van ruim 48.000 klanten. Buiten de collectiviteitsvoordelen, kunnen wij op deze manier ook de beste partijen uit de markt aan ons koppelen als het gaat over verduurzaming.</p>
						<p>Om ervoor te zorgen dat uw tijd en de tijd van onze business partners zo optimaal mogelijk gebruikt wordt, kiezen wij ervoor om u eerst een QuickScan te presenteren. In deze QuickScan wordt vooraf gekeken naar o.a. de oriëntatie van uw dak(en), uw huidige verbruiken en de maximaal te behalen rendementen. De QuickScan is een zeer nauwkeurige benadering van de uiteindelijke resultaten.</p>
						<p><b>Bij de aanschaf van zonnepanelen zijn er een drietal scenario’s mogelijk;</b></p>
						<ol>
							<li>U wekt het totale huidige verbruik op. Dankzij de salderingsregeling hoeven we hier géén rekening te houden met het piek en dal verbruik. Dit geldt voor aansluitingen tot 3 * 80 amp.</li>
							<li>Uw totale dakoppervlak wordt benut. Mede dankzij het hoge rendement en de SDE subsidie kan het zéér interessant zijn om uw volledige dakoppervlak te benutten.</li>
							<li>Voor organisaties met een dakoppervlakte van meer dan 600m² bestaat de mogelijkheid voor een huurconstructie. Onze partner huurt uw dak, neemt de investering op zich en blijft eigenaar van de zonnepanelen. Uw maandelijkse energierekening blijft gelijk, echter ontvangt u maandelijks een vergoeding per paneel dat kan oplopen tot tienduizenden euro’s per jaar. Uw voordeel is directe waardeverhoging van uw pand, imagoverbetering en rendement zonder investering.</li>
						</ol>
						<p><b>Wat zijn de vervolgstappen na presentatie van de QuickScan;</b></p>
						<ol>
							<li>Bepalen welk scenario voor uw organisatie het beste is.</li>
							<li>Het aanvragen van de SDE subsidie.</li>
							<li>Het inplannen van een afspraak met de leverancier.
								<ul>
									<li>Analyse van de beschikbare daken.</li>
									<li>Bepalen welke omvormers ingezet gaan worden.</li>
									<li>Bepalen of sprake is van verzwaring huidige bekabeling.</li>
								</ul>
							</li>
						</ol>
						<p>Op de volgende pagina’s ontvangt u een uiteenzetting van de mogelijkheden.<br>
						<br>
						Met vriendelijke groeten,<br>
						<br>
						<i>Koen Korsten</i><br>
						Commercieel Directeur Nederland</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-12">
						<h1>Uw dak:</h1>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie verti">
					<div class="col-xs-8">
						<i>Schuin dak: De totaal beschikbare oppervlakte, schuin dak, bedraagt;</i>
					</div>
					<div class="col-xs-4">
						<h2><?php echo $_POST["schuin"]; ?> m²</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie verti">
					<div class="col-xs-8">
						<i>Plat dak: De totaal beschikbare oppervlakte, plat dak, bedraagt;</i>
					</div>
					<div class="col-xs-4">
						<h2><?php echo $_POST["plat"]; ?> m²</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie margin">
					<div class="col-xs-12">
						<h2><?php echo $_POST["maxkwh"]; ?></h2>&nbsp;<span>is de totale kWh wat u per jaar kunt opwekken.</span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie margin">
					<div class="col-xs-12">
						<h2>Uw huidige verbruik en tarifering:</h2><br>
						<span>Om een goede calculatie in deze QuickScan te maken is het van essentieel belang dat we weten wat uw huidige verbruiken en de nieuwe geldende tarieven zijn. Met deze uitgangspunten, sluit de QuickScan het beste aan op uw werkelijke situatie.</span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie margin">
					<div class="col-xs-4">
						<h2>Omschrijving</h2><br>
						Piek<br>
						Dal<br>
						Enkel
					</div>
					<div class="col-xs-4">
						<h2>Verbruik</h2><br>
						<?php echo $_POST["piek"]; ?><br>
						<?php echo $_POST["dal"]; ?><br>
						<div class="streep">
							<?php echo $_POST["enkel"]; ?>
						</div><?php echo $_POST["totaal"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php if($_POST["maxkwhresultaat"] < 0){ echo "<span class='rood'>" . $_POST['maxkwhresultaat'] . "</span>"; } else { echo "<span class='groen'>" . $_POST['maxkwhresultaat'] . "</span>"; } ?>
					</div>
					<div class="col-xs-4">
						<h2>Tarief</h2><br>
						<?php echo $_POST["tarief1"]; ?><br>
						<?php echo $_POST["tarief2"]; ?><br>
						<?php echo $_POST["tarief3"]; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-12">
						<h2>Berekening</h2>
					</div>
					<div class="col-xs-12">
						<?php if($_POST["maxkwhresultaat"] < 0){ echo "<span style='color:red'>U heeft niet voldoende dakoppervlakte om uw huidige verbruik op te wekken<br>Uw maximaal op te wekken kWh's bedraagt <b>" . $_POST['maxkwh'] . "</b></span>"; } else { echo "<b><span style='color:green'>U heeft voldoende dakoppervlakte om uw huidige verbruik op te wekken</span></b><br>U kunt nog overwegen om extra panelen te plaatsen in combinatie met de SDE subsidie"; } ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-12">
						<h2>Uitleg financiële voordelen;</h2>
					</div>
					<div class="col-xs-12">
						<p>1. Verbruikskosten; Uw kosten voor het verbruik van energie komen voor een groot deel te vervallen. Er zal nog een beperkte factuurrelatie met de energieleverancier blijft bestaan, aangezien er ook in de dal-nacht uren energie gevraagd wordt. Dit wordt niet opgewekt. Weliswaar wordt dit door salderingsregeling gecompenseerd.</p>
						<p>2. Energiebelasting en Opslag Duurzame Energie (ODE); Iedere kWh wordt hiermee belast, de hoogte hiervan wordt door de overheid bepaalt. Er bestaan een drietal schijven waar Energiebelasting en ODE over wordt berekend, per 2018 zijn deze tarieven als volgt;</p>
						<ul>
							<li>a. Schijf 1: 0 tot 10.000 kWh -&gt; tarief Energiebelasting en ODE € 0,11697</li>
							<li>b. Schijf 2: 10.001 tot 50.000 kWh -&gt; tarief Energiebelasting en ODE € 0,07035</li>
							<li>c. Schijf 3: 50.001 en meer kWh -&gt; tarief Energiebelasting en ODE € 0,01874</li>
						</ul>
						<p>3. Te ontvangen subsidie, SDE; in 2017 is in totaal € 18 miljard subsidie beschikbaar gesteld. Momenteel is de hoogte inclusief teruglevering tussen de € 0,105 en € 0,116. Voorwaarden is dat er minimaal 15.000 kWp opgewekt moet worden en de aansluiting meer dan 3*80 moet zijn. Wanneer de aansluiting 3*80 of kleiner is, kan het vaak interessant zijn om een meterverzwaring aan te vragen. Hierin wordt u door onze partner geadviseerd.</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-12">
						<h1>Scenario 1.1: Opwekken van het huidige verbruik o.b.v koop</h1>
					</div>
					<div class="col-xs-12">
						<h2>Uitleg Energie Investings Aftrek</h2><br>
						<p>Wilt u als bedrijf, vereniging of stichting fiscaal voordeel behalen? Dat kan als u investeert in energiezuinige technieken en duurzame energie met de regeling Energie-investeringsaftrek (EIA). EIA levert u gemiddeld 13,5% voordeel op. Daarnaast zorgen energiezuinige investeringen ook voor een lagere energierekening. Fiscale aftrek krijgt u voor duidelijk omschreven investeringen (specifiek) maar ook voor maatwerkinvesteringen (generiek) die een forse energiebesparing opleveren. U kunt 55% van de investeringskosten aftrekken van de fiscale winst. Dat kan bovenop uw gebruikelijke afschrijving.</p>
						<h2>Uitleg Kleinschaligheids Investings Aftrek (KIA)</h2><br>
						<p>De KIA biedt u de mogelijkheid de fiscale winst te verlagen. De KIA is een extra aftrekpost die u mag voeren voor milieuvriendelijke investeringen. U mag tot circa 28% van de gemaakte kosten van de fiscale winst aftrekken. Het bedrag van de kleinschaligheidsinvesteringsaftrek hangt af van het geïnvesteerde bedrag in het boekjaar. Om in 2017 in aanmerking te komen voor de KIA moet u een bedrag tussen € 2.301,- en € 312.176,- investeren in bedrijfsmiddelen voor uw onderneming.</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie">
					<div class="col-xs-12">







						<p>Deze calculatie is gebaseerd op het opwekken van max.;



						 <?php 
						 	function testEst() {
    							if($_POST["maxkwhresultaat"] > 0){
    							 $result = "" . $_POST['totaal'] . ""; 
    							}
    							else
    							{ 
    							 $result = "" . $_POST['maxkwh'] . ""; 
    							}
    							return $result;
							}

							echo testEst(); ?> kWh
							<br>
							Uw restant verbruik wordt;
							<?php
								$testtotaal = $_POST['totaal'];
								$resulttwee = $testtotaal - testEst();
								echo $resulttwee; ?> kWh
						 </p>




						<h2 style="font-size:16px !important; margin: 10px 0;">Type onderneming: <?php echo $_POST["typeonderneming"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Uw belasting in dit geval: <?php if($_POST["typeonderneming"] == "bv"){ echo "%20"; } else { echo "%42"; } ?></h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="informatie margin">
					<div class="col-xs-12">
						<h1>Uw Voordelen</h1>
						<h2 style="font-size:16px !important; margin:10px 0;">Niet te betalen Energiebelasting (EB) en Opslag Duurzame Energie (ODE):</h2>
					</div>
					<div class="col-xs-12">
						<table class="table table-striped">
							<thead>
								<tr>
									<th></th>
									<th>Huidige situatie<br> # kWh/schijf</th>
									<th>Nieuwe situatie<br> # kWh/schijf</th>
									<th>Afname verbruik</th>
									<th>€ energiebel. + ODE</th>
									<th><span class="green">Totaal voordeel</span></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><b>Schijf 1:</b></td>
									<td>
										<?php
											function schijfEen()
											{
											    if ($_POST["totaal"] > 10000) {
											        $schijfeen = 10000;
											    } else {
											        $schijfeen = "" . $_POST['totaal'] . "";
											    }
											    return $schijfeen;
											}
											echo schijfEen();
										?>
									</td>
									<td>
										<?php
											function schijfTwee()
											{
												global $resulttwee;
											    if ($resulttwee > 10000) {
											        $schijftwee = 10000;
											    } else {
											        $schijftwee = $resulttwee;
											    }
											    return $schijftwee;
											}
											echo schijfTwee();
										?>
									</td>
									<td><?php $afname=(schijfEen() - schijfTwee()); echo $afname; ?></td>
									<td>€ <?php $energiebel="0,11697"; echo $energiebel; ?></td>
									<td>€ <?php echo($afname * $energiebel) ?></td>
								</tr>
								<tr>
									<td><b>Schijf 2:</b></td>
									<td>Moe</td>
									<td>mary@example.com</td>
									<td>mary@example.com</td>
									<td>mary@example.com</td>
									<td>mary@example.com</td>
								</tr>
								<tr>
									<td><b>Schijf 3:</b></td>
									<td>Dooley</td>
									<td>july@example.com</td>
									<td>july@example.com</td>
									<td>july@example.com</td>
									<td>july@example.com</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>