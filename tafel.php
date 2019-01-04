<html>
    <head>
        <link rel="stylesheet" href="tafel.css">
        <title>Tafelgigant</title>
    </head>
<body>
<div class="container">
    <h1>Bestelformulier</h1>
    <h2>Houten tafels</h2>
    <form action="tafel.php" method="post">
    <fieldset id="naw">
        Naam:<br><input id="naam" name="naam" type="text" required><br><br>
        Leeftijd:<br><input id="leeftijd" name="leeftijd" type="text" required><br><br>
        Kortingscode:<br><input id="kortingsCode" name="kortingsCode" type="text">
        <input type="submit" value="OK">
    </fieldset>

    <br><h2>Stel uw eigen tafel samen:</h2>

    <fieldset id="kies">
        Houtsoort:<br>
        <select id="houtSoort" name="houtSoort">
            <option value="grenen">Grenen</option>
            <option value="eiken">Eiken</option>
            <option value="teak">Teak</option>
        </select><br><br>
        Lengte:<br>
        <select id="lengte" name="lengte">
            <option value ="1">1 meter</option>
            <option value ="2">2 meter</option>
            <option value ="3">3 meter</option>
            <option value ="4">4 meter</option>
            <option value ="5">5 meter</option>
            <option value ="6">6 meter</option>
        </select><br><br>
        Breedte:<br>
        <select id="breedte" name="breedte">
            <option value ="1">1 meter</option>
            <option value ="1.5">1,5 meter</option>
            <option value ="2">2 meter</option>
            <option value ="2.5">2,5 meter</option>
            <option value ="3">3 meter</option>
            <option value ="3.5">3,5 meter</option>
            <option value ="4">4 meter</option>
        </select><br><br>
        Hoogte:<br>
        <select id="hoogte" name="hoogte">
        <option value ="60">60 cm</option>
            <option value ="70">70 cm</option>
            <option value ="80">80 cm</option>
            <option value ="90">90 cm</option>
            <option value ="100">100 cm</option>
            <option value ="110">110 cm</option>
            <option value ="120">120 cm</option>
            <option value ="130">130 cm</option>
        </select><br><br>
        Model:<br>
        <select id="model" name="model">
            <option value ="klassiek">Klassiek</option>
            <option value ="klassiekzwaar">Klassiek extra zwaar</option>
            <option value ="modernlicht">Modern, licht model</option>
            <option value ="modern">Modern standaard</option>
        </select><br><br>
        Aantal:<br>
        <input name="aantal" type="number" min=1 required>
        <input type="submit" value="OK">
    </fieldset>
    </form>
   
    
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $name = $_POST["naam"];
            $age = $_POST["leeftijd"];
            echo ($age > 18) ? "<b>Offerte</b><br><br>Te leveren aan: $name <br><br>" :  "Wij kunnen helaas alleen leveren aan meerderjarigen.<br>";
            // $houtSoort = ucfirst($_POST["houtSoort"]);
            // echo $houtSoort;

            // bereken basisprijs: (oppervlak + hoogte) * (factor voor houtsoort) * (factor voor model)
            // basisprijs = (50 / m2) + (0.1 / 10cm)

            // bereken korting: (factor voor aantal) * (factor voor kortingscode)
            
            $opp = $_POST["lengte"] * $_POST["breedte"]; //oppervlakte
            $hoogte = $_POST["hoogte"];
            $model = $_POST["model"];
            $hout = $_POST["houtSoort"];
            $aantal = $_POST["aantal"];
            $kortingsCode = $_POST["kortingsCode"]; // voor eventuele couponkorting
            $basisPrijs = 0;    // init basisprijs
            $teBetalen = 0;
            $houtFactor = 0;
            $modelFactor = 0;
            $aantalFactor = 1; // kortingsfactor bij hogere aantallen
            $aantalGrens = 0; // variable voor gebruik in uitvoer
            $couponFactor = 1; // voor als er kortingscodes worden gebruikt
            $modelOmschrijving = "";
            
            if ($aantal >= 25){
                $aantalFactor = 0.85;
                $aantalGrens = 25;
            }
            else if ($aantal >= 10){
                $aantalFactor = 0.9;
                $aantalGrens = 10;
            }
            else if ($aantal >= 3){
                $aantalFactor = 0.95;
                $aantalGrens = 3;
            }
                    
            if ($kortingsCode != null) {
                if (($kortingsCode == 123) || ($kortingsCode == 132) || ($kortingsCode == 213) || ($kortingsCode == 231) ||
                    ($kortingsCode == 312) || ($kortingsCode == 321))
                        {$couponFactor = 0.9;}
                elseif (($kortingsCode == 1234) || ($kortingsCode == 4321))
                        {$couponFactor = 0.8;} 
            }

            switch($model) {
                case 'klassiek': $modelOmschrijving = "Klassiek"; break;
                case 'klassiekzwaar': $modelOmschrijving = "Klassiek, zwaar"; break;
                case 'modernlicht': $modelOmschrijving = "Modern, licht"; break;
                case 'modern': $modelOmschrijving = "Modern"; break;
            }

            switch($hout) {                             
                case 'grenen': $houtFactor = 1; break;
                case 'eiken': $houtFactor = 2; break;
                case 'teak': $houtFactor = 3; break;
            }

            switch($model) {
                case 'klassiek': $modelFactor = 1; break; // basismodel
                case 'klassiekzwaar': $modelFactor = 1.1; break;  // + 10% 
                case 'modernlicht': $modelFactor = 0.8; break; // -20%
                case 'modern': $modelFactor = 0.9; break; // - 10 %
            }

            $basisPrijs = (($opp * 50) + ($hoogte * 0.1)) * $houtFactor * $modelFactor;  //basisprijs
            $teBetalen = $basisPrijs * $aantal * $aantalFactor;
            if ($age > 18){
                echo "Aantal: " . $aantal . "<br>";
                echo "Model: " . $modelOmschrijving . "<br>";
                echo "Houtsoort: " . ucfirst($_POST["houtSoort"]) . "<br>";
                echo "Bladoppervlak: " . $opp . " m<sup>2</sup><br>";
                echo "Hoogte: " . $hoogte . " cm<br>";
                echo "Basisprijs: &euro;" . $basisPrijs . "<br><br>";

                if ($aantal >= 3){
                    echo "Korting op basisprijs bij aanschaf van " . $aantalGrens . " of meer tafels: " . (100 * (1 - $aantalFactor)) . "%<br>";
                    echo "Nieuwe basisprijs: &euro;" . $basisPrijs * $aantalFactor . "<br><br>";
                }

                if ($couponFactor != 1){
                    echo "Kortingscode \"" . $kortingsCode . "\" ingevoerd voor een extra korting van " . (100 * (1 - $couponFactor)) ."%
                    op het eindbedrag <br><br>";
                    echo "Te betalen: " . 100*$couponFactor . "% x " . $aantal . " x " . $basisPrijs * $aantalFactor . " =  <b>&euro;" . $teBetalen * $couponFactor . "</b></br>";
                }
                else echo "Te betalen: " . $aantal . " x " . $basisPrijs * $aantalFactor . " =  <b>&euro;" . $teBetalen . "</b></br>";
            }    
        }    
    ?>
</div>     
</body>

</html>