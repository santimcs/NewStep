<?php include('inc/header.php'); ?>

<?php

require "DB.inc";

// Establish a new MySQL connection using mysqli
$connection = new mysqli($hostName, $username, $password, $databaseName);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Find maximum date from the price table
$query_max = "SELECT MAX(date) AS max_date FROM price";
if (!($result_max = $connection->query($query_max))) {
    die("Error executing query: " . $connection->error);
}
$row_max = $result_max->fetch_assoc();
$to_date = $row_max['max_date'];

// Update SAA prices based on today's price
$query = "UPDATE SAA, PRICE SET SAA.PRICE = PRICE.PRICE WHERE PRICE.NAME = SAA.NAME AND PRICE.DATE = '$to_date'";
if (!($result = $connection->query($query))) {
    die("Error executing query: " . $connection->error);
}

// Update gain in SAA table
$query = "UPDATE SAA SET GAIN = (TP - PRICE) / PRICE * 100";
if (!($result = $connection->query($query))) {
    die("Error executing query: " . $connection->error);
}

// Fetch stock data
$query = "SELECT SAA.name AS name, buy.date AS date, buy.price AS cost,
                 SAA.price, TP, gain, Buy, Hold, Sell, 
                 (Buy * 2) + Hold + (Sell * -2) AS Score, ROE, PER, 
                 SAA.div AS yld, PER.PBV, tendays.price AS tdprice
          FROM SAA 
          INNER JOIN StockName ON StockName.name = SAA.name
          INNER JOIN ROE ON SAA.name = ROE.name
          INNER JOIN PER ON SAA.name = PER.name
          INNER JOIN tendays ON SAA.name = tendays.name
          INNER JOIN buy ON SAA.name = buy.name 
          WHERE buy.active = 1
          ORDER BY name";

if (!($result = $connection->query($query))) {
    die("Error executing query: " . $connection->error);
}

// Prepare the HTML headers
$stock_header = <<<EOD
<html>
<head>
    <link rel="shortcut icon" type="image/ico" href="media/images/santi.ico" />
    <title>BHS Inquiry</title>
    <link href="css/global.css" rel="stylesheet" type="text/css">
    <link href="css/vendor/jquery.dataTables.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/vendor/jquery.js"></script>
    <script type="text/javascript" src="js/vendor/jquery.dataTables.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').dataTable({
                "pagingType": "full_numbers",
                "order": [[0, "asc"], [1, "asc"]]
            });
        });
    </script>
</head>

<body id="twoCol" class="dt-example">
    <div id="container">
        <div id="contentWrap">
            <div id="main">
                <h1>BHS Inquiry As End Of $to_date</h1>
                <table id="example" class="display" cellspacing="0" width="100%">
                    <thead>  
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Date</th>    
                            <th scope="col">Cost</th>                        
                            <th scope="col">Price</th>
                            <th scope="col">TP</th>
                            <th scope="col">Actual</th>
                            <th scope="col">Project</th>
                            <th scope="col">Div</th>
                            <th scope="col">Buy</th>
                            <th scope="col">Hold</th>
                            <th scope="col">Sell</th>
                            <th scope="col">Score</th>
                            <th scope="col">ROE</th>
                            <th scope="col">P/E</th>
                            <th scope="col">P/BV</th>
                        </tr>
                    </thead>
                    <tbody>
EOD;

$stock_details = '';
while ($row = $result->fetch_assoc()) {
    $stock_name = $row['name'];
    $date = $row['date'];
    $cost = number_format($row['cost'], 2);
    $price = $row['price'];
    $TP = $row['TP'];
    $Gain = number_format($row['gain'], 2); // Format gain to 2 decimal places
    $Div = $row['yld'];
    $Buy = $row['Buy'];
    $Hold = $row['Hold'];
    $Sell = $row['Sell'];
    $Score = $row['Score'];
    $ROE = $row['ROE'];
    $PER = $row['PER'];
    $PBV = $row['PBV'];
    $tdprice = $row['tdprice'];

    // Calculate actual and projected percentage gains with 2 decimal places
    $Actual = number_format(($price - $cost) / $cost * 100, 2);
    $Project = number_format(($TP - $price) / $price * 100, 2);

    $stock_details .= <<<EOT
                        <tr>
                            <td>$stock_name</td>
                            <td>$date</td>    
                            <td>$cost</td>                        
                            <td>$price</td>
                            <td>$TP</td>
                            <td>$Actual</td> <!-- Formatted to 2 decimal places -->
                            <td>$Project</td> <!-- Formatted to 2 decimal places -->
                            <td>$Div</td>
                            <td>$Buy</td>    
                            <td>$Hold</td>     
                            <td>$Sell</td>     
                            <td>$Score</td>
                            <td>$ROE</td>
                            <td>$PER</td>
                            <td>$PBV</td>
                        </tr>\n
EOT;
}

$stock_footer = <<<EOD
                    </tbody>
                </table>
            </div>
            <div id="sidebar">
                <h2>BHSInq</h2>
            </div>
        </div>
        <div id="footer"><a href='mailto: santimcs@hotmail.com'>santimcs@hotmail.com</a></div>
    </div>
</body>
</html>
EOD;

$stock = $stock_header . $stock_details . $stock_footer;

// Output the final HTML
print $stock;

// Close the MySQL connection
$connection->close();
?>