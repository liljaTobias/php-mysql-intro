<!DOCTYPE HTML>
<html>
<head>
      <meta charset="utf-8">
      <title>Datateknik eller?</title>
      
      <style>
      .btn {
        -webkit-border-radius: 28;
        -moz-border-radius: 28;
        border-radius: 28px;
        text-shadow: 1px 1px 3px #666666;
        font-family: Courier New;
        color: #d424d4;
        font-size: 40px;
        background: #dda5e8;
        padding: 10px 20px 10px 20px;
        text-decoration: none;
      }

      .btn:hover {
        background: #3cb0fd;
        background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
        background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
        background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
        background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
        background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
        text-decoration: none;
      }
      </style>
      
</head>

<body>

      <div style="margin:20px;">
            <h2>Här kan du få information om ett land</h2>
            <form action="index.php" method="post">
              Antal invånare i landet är mindre än: <input type="text" name="antal"><br>
              <input class="btn" type="submit" value="Sök i databasen">
        </form>
  </div>

  <div style="margin-top:20px;">

      <?php

      /*** Kolla om något värde fyllts i i rutan ***/
      if (isset($_POST['antal'])) {

            /*** Hämta inloggningsuppgifter till databasen ***/
            include 'dbPass.php';

            /*** Lagra antalet i en variabel ***/
            $antal = $_POST['antal'];

            /*** Fixa uppkopplingen till databasen ***/
            $dbh = new PDO("mysql:host=$hostname;dbname=testDB", $username, $password,
                  array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")); //Sätt till UTF-8

            /*** Felhantering ***/
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            /*** Skapa SQL-frågan 
            
            När det står City.Population referar man till kolumnen Population i table City
            
            Då man skriver AS väljer man att döpa om rubriken på kolumnen, tänk excel och en rad med rubriker längst upp.
            Man kan se i foreach-loopen att jag använder t.ex. city istället för att vara tvungen att skriva City.Name
            
            Sen väljer jag att joina table Country då Country.Code = City.CountryCode
            
            Till slut visas bara resultat där Country.Population < :antal 
            :antal hämtas från $_POST['antal']
            
            ***/
            $stmt = $dbh->prepare(" SELECT      City.Name AS city, City.Population AS cityPopulation,
                                                Country.Name AS country, Country.SurfaceArea AS surfaceArea, Country.Population AS countryPopulation,
                                                CountryLanguage.Language AS language
                                                
                                    FROM City 

                                    INNER JOIN Country
                                    ON Country.Code = City.CountryCode

                                    INNER JOIN CountryLanguage
                                    ON Country.Code = CountryLanguage.CountryCode

                                    WHERE Country.Population < :antal
                                    ");

            /*** Binda sökparametrarna (Detta har med SQL-injections att göra, dvs säkerhet (Vill du veta mer... googla)) ***/
            $stmt->bindParam(':antal', $antal, PDO::PARAM_INT);

            /*** Kör frågan mot databasen ***/
            $stmt->execute();

            /*** Hämta resultaten ***/
            $result = $stmt->fetchAll();

            /*** Printa en table ***/
            echo '<table border="1" style="width:700px;">';
            echo '<tr><th>Stad</th><th>Invånare i staden</th><th>Tillhör land</th><th>Invånare i landet</th><th>Språk</th><th>Landets area</th></tr>';

            /*** Gå igenom resultaten. Varje rad i resultatet "döps" till $row i foreach-loopen ***/
            foreach($result as $row)
            {     
                  echo '<tr>';
                  echo '<td>' . $row['city'] . '</td>';
                  echo '<td>' . $row['cityPopulation'] . '</td>';
                  echo '<td>' . $row['country'] . '</td>';
                  echo '<td>' . $row['countryPopulation'] . '</td>';
                  echo '<td>' . $row['language'] . '</td>';
                  echo '<td>' . $row['surfaceArea'] . '</td>';
                  echo '</tr>';
            }

            echo '</table';

      }

      ?>
</div>
</body>
</html>
