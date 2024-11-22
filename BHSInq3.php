<?php include('inc/header.php'); ?>

<?php

require "DB.inc";

// Establish a new MySQL connection using mysqli
$connection = new mysqli($hostName, $username, $password, $databaseName);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Find maximum date
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

// Update gain in SAA table with 2 decimal places for the percentage calculation
$query = "UPDATE SAA 
          SET GAIN = CASE 
                         WHEN PRICE != 0 THEN ROUND((TP - PRICE) / PRICE * 100, 2) 
                         ELSE NULL 
                     END";
if (!($result = $connection->query($query))) {
    die("Error executing query: " . $connection->error);
}

// Fetch the stock data
$query = "SELECT SAA.name AS name, StockName.category AS category,
                 SAA.price, TP, gain, Buy, Hold, Sell, 
                 (Buy * 2) + Hold + (Sell * -2) AS Score, ROE, PER, 
                 SAA.div AS yld, PER.PBV
          FROM SAA 
          INNER JOIN StockName ON StockName.name = SAA.name
          INNER JOIN ROE ON SAA.name = ROE.name
          INNER JOIN PER ON SAA.name = PER.name";

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
                "order": [[5, "desc"], [4, "desc"]]
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
                            <th scope="col">Cat.</th>                            
                            <th scope="col">Price</th>
                            <th scope="col">Cons.</th>
                            <th scope="col">Pct</th>
                            <th scope="col">Div</th>
                            <th scope="col">B</th>
                            <th scope="col">H</th>
                            <th scope="col">S</th>
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
    $category = $row['category'];    
    $price = $row['price'];
    $TP = $row['TP'];
    $Gain = number_format($row['gain'], 2); // Ensure gain is formatted to 2 decimal places
    $Div = $row['yld'];
    $Buy = $row['Buy'];
    $Hold = $row['Hold'];
    $Sell = $row['Sell'];
    $Score = $row['Score'];
    $ROE = $row['ROE'];
    $PER = $row['PER'];
    $PBV = $row['PBV'];

    $stock_details .= <<<EOD
                        <tr>
                            <td>$stock_name</td>
                            <td>$category</td>                            
                            <td>$price</td>
                            <td>$TP</td>
                            <td>$Gain</td> <!-- Format gain (Pct column) to 2 decimal places -->
                            <td>$Div</td>
                            <td>$Buy</td>    
                            <td>$Hold</td>     
                            <td>$Sell</td>     
                            <td>$Score</td>
                            <td>$ROE</td>
                            <td>$PER</td>
                            <td>$PBV</td>
                        </tr>\n
EOD;
}

$stock_footer = <<<EOD
                    </tbody>
                </table>
            </div>
            <div id="sidebar">
                <h2>BHSInq</h2>
            </div>
        </div>
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