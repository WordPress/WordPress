<html>
    <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
    <title>Calculatie</title>
    <style>
    form input[type="text"], form textarea, form select {
            border: 0;
    outline: 0;
    padding: 0.4em;
    -moz-border-radius: 8px;
    -webkit-border-radius: 8px;
    border-radius: 8px;
    display: block;
    width: 100%;
    margin-top: 1em;
    font-family: 'Merriweather', sans-serif;
    -moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    resize: none;
    background: #ebebeb;
    }
    body {
        background:#ededed;
    }
    form {
        background:#fff;
            overflow: hidden;
    }
h1,button {
    font-family: 'Source Sans Pro', sans-serif !important;
    font-size: 26px !important;
    text-transform: uppercase !important;
    font-weight: 800 !important;
    color: #3489d6;
}
button{
    width:100%;
    margin-top: 20px;
    padding:20px 0;
    color:white;
}
input:read-only {
    background: #fff !important ;
}
    </style>
    </head>
    <body>
<?php
error_reporting(0);
if( isset( $_REQUEST['bereken'] ))
{
    $operator=$_REQUEST['operator'];
    if($operator=="*")
    {
        $bedrijfsnaam = $_REQUEST['bedrijfsnaam'];
        $aanhef = $_REQUEST['aanhef'];
        $klantnaam = $_REQUEST['klantnaam'];
        $achternaam = $_REQUEST['achternaam'];
        $typeonderneming = $_REQUEST['typeonderneming'];
        $naamverkoper = $_REQUEST['naamverkoper'];
        $schuin = $_REQUEST['schuin'];
        $plat = $_REQUEST['plat'];
        $piek = $_REQUEST['piek'];
        $dal = $_REQUEST['dal'];
        $enkel = $_REQUEST['enkel'];
        $tarief1 = $_REQUEST['tarief1'];
        $tarief2 = $_REQUEST['tarief2'];
        $tarief3 = $_REQUEST['tarief3'];
        $percentage1 = $_REQUEST['percentage1'];
        $percentage2 = $_REQUEST['percentage2'];
        $percentage3 = $_REQUEST['percentage3'];
        $aantalpanelen = $_REQUEST['aantalpanelen'];
        $maxkwh = $_REQUEST['maxkwh'];
        $totaal = $_REQUEST['totaal'];
        $resultaat = $piek+$dal+$enkel;
        $resultaatpercentage1 = ($piek/$resultaat)*100;
        $resultaatpercentage2 = ($dal/$resultaat)*100;
        $resultaatpercentage3 = ($enkel/$resultaat)*100;
        $aantalpanelenberekening = ($schuin/1.65)+($plat/2);
        $maxkwhberekening = $aantalpanelenberekening*280*0.9;
        $maxtweedeberekening = $maxkwh-$totaal;
        $maxkwhresultaat = $_REQUEST['maxkwhresultaat'];
    }
}
?>
<div class="container">
<div class="row">
<form method="post">
    <select name="operator" style="display:none;"><option>*</option></select>

    <div class="col-xs-12"><h1>Gegevens</h1></div>

    <div class="col-xs-12"><input placeholder="Bedrijfsnaam" value="<?php echo $bedrijfsnaam ?>" name="bedrijfsnaam" type="text"/></div>
    
    <div class="col-xs-12"><select name="aanhef"><option <?php if ($_GET['aanhef'] == 'Dhr.') { ?> selected="true" <?php }; ?> value="Dhr.">Dhr.</option><option <?php if ($_GET['aanhef'] == 'Mevr.') { ?> selected="true" <?php }; ?> value="Mevr.">Mevr.</option></select><input placeholder="Klant Naam" name="klantnaam" value="<?php echo $klantnaam ?>" type="text" /></div>
    
    <div class="col-xs-12"><input placeholder="Achternaam"  value="<?php echo $achternaam ?>" name="achternaam" type="text"/></div>
    
    <div class="col-xs-12"><select name="typeonderneming">
    <option>Bedrijfstype (kies hieronder)</option>
    <option <?php if ($_GET['typeonderneming'] == 'vof') { ?> selected="true" <?php }; ?> value="vof">vof</option>
    <option <?php if ($_GET['typeonderneming'] == 'eenmanszaak') { ?> selected="true" <?php }; ?> value="eenmanszaak">eenmanszaak</option>
    <option <?php if ($_GET['typeonderneming'] == 'maatschap') { ?> selected="true" <?php }; ?> value="maatschap">maatschap</option>
    <option <?php if ($_GET['typeonderneming'] == 'zzp') { ?> selected="true" <?php }; ?> value="zzp">zzp</option>
    <option <?php if ($_GET['typeonderneming'] == 'bv') { ?> selected="true" <?php }; ?> value="bv">bv</option>
    </select></div>
    
    <div class="col-xs-12"><input  value="<?php echo $naamverkoper ?>" placeholder="Naam Verkoper" name="naamverkoper" type="text"/></div>
    
    <div class="col-xs-12"><h1>Dakoppervlakte</h1></div>
    <div class="col-xs-12"><input  value="<?php echo $schuin ?>" placeholder="Schuin" name="schuin" type="text"/></div>
    <div class="col-xs-12"><input value="<?php echo $plat ?>" placeholder="Plat" name="plat" type="text"/></div>
    
    <hr>

    <div class="col-xs-4"><h1>Verbruik</h1></div><div class="col-xs-4"><h1>Tarief</h1></div><div class="col-xs-4"><h1>Percentage</h1></div>
    
    <div class="col-xs-4"><input placeholder="Piek" value="<?php echo $piek ?>" name="piek" type="text"/></div> <div class="col-xs-4"><input name="tarief1" value="<?php echo $tarief1 ?>" type="text"/></div> <div class="col-xs-4"><input value="<?php echo $resultaatpercentage1 ?>%" readonly name="percentage1" type="text"/></div> 
    
    <div class="col-xs-4"><input placeholder="Dal" value="<?php echo $dal ?>" name="dal" type="text"/></div> <div class="col-xs-4"><input name="tarief2" value="<?php echo $tarief2 ?>" type="text"/></div><div class="col-xs-4"><input value="<?php echo $resultaatpercentage2 ?>%" readonly name="percentage2" type="text"/></div> 
    
    <div class="col-xs-4"><input placeholder="Enkel" name="enkel" value="<?php echo $enkel ?>" type="text"/></div> <div class="col-xs-4"><input name="tarief3" value="<?php echo $tarief3 ?>" type="text"/></div><div class="col-xs-4"><input value="<?php echo $resultaatpercentage3 ?>%" readonly name="percentage3" type="text"/></div> 

    <div class="col-xs-4"><input value="<?php echo $resultaat ?>" readonly name="totaal" type="text"/></div> <div class="col-xs-4">.</div><div class="col-xs-4">.</div> 

    <div class="col-xs-12"><h1>Berekening</h1></div>
    <div class="col-xs-12">Aantal panelen: <input value="<?php echo round($aantalpanelenberekening) ?>" readonly name="aantalpanelen" type="text"/></div>
    <div class="col-xs-12">Max. op te wekken kWh<input value="<?php echo round($maxkwhberekening) ?>" readonly name="maxkwh" type="text"/></div> 
    
    <div class="col-xs-6"><button class="btn btn-success" name="bereken" id="test" value="Calculate"/>Berekenen</button></div>
    <div class="col-xs-6"><button formaction="resultaat.php" type="submit" class="btn btn-info" name="volgendepagina" id="test" value="Calculate"/>Volgende Pagina</button></div>
    
    <input style="display:none;" value="<?php echo round($maxtweedeberekening) ?>" readonly name="maxkwhresultaat" type="text"/>
</form>
</div>
</div>
</body>
</html>