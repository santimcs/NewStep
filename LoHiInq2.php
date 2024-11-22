<?php include('inc/header.php'); ?>

<?php

require "DB.inc";

// Establish a new MySQL connection using mysqli
$connection = new mysqli($hostName, $username, $password, $databaseName);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get the direction and present date from POST data
$direction = isset($_POST['direction']) ? $_POST['direction'] : '';
$present = isset($_POST['present']) ? $_POST['present'] : '';

// Define conditions based on the selected direction
$dirtext = '';
$from_files = '';

if ($direction == "NewLow") {
    $dirtext = " A.Price < B.Price";
    $from_files = "pricetdy AS A, pricelo AS B, stockname AS S, per AS P";
} elseif ($direction == "Low") {
    $dirtext = " A.Price <= B.Price";
    $from_files = "pricetdy AS A, pricelo AS B, stockname AS S, per AS P";
} elseif ($direction == "High") {
    $dirtext = " A.price >= B.Price";
    $from_files = "pricetdy AS A, pricehi AS B, stockname AS S, per AS P";
} elseif ($direction == "NewHigh") {
    $dirtext = " A.price > B.Price";
    $from_files = "pricetdy AS A, pricehi AS B, stockname AS S, per AS P";
}

// Clear existing data and insert new data into temporary tables
$queries = [
    "DELETE FROM pricetdy",
    "INSERT INTO pricetdy SELECT name, price, qty, qty*price AS amt FROM price WHERE date = '$present'",
    "DELETE FROM pricelo",
    "INSERT INTO pricelo SELECT name, MIN(price) FROM price WHERE date >= DATE_SUB('$present', INTERVAL 1 YEAR) AND date < '$present' GROUP BY name",
    "DELETE FROM pricehi",
    "INSERT INTO pricehi SELECT name, MAX(price) FROM price WHERE date >= DATE_SUB('$present', INTERVAL 1 YEAR) AND date < '$present' GROUP BY name"
];

foreach ($queries as $query) {
    if (!$connection->query($query)) {
        die("Error executing query: " . $connection->error);
    }
}

// Main query to fetch stock data
$query = "SELECT A.name AS name, S.category AS cat, A.price AS priceA, FORMAT(A.qty, 0) AS qty, amt, PER, yield, FORMAT(P.dividend, 2) AS dividend, PBV
          FROM $from_files
          WHERE A.name = B.name
          AND $dirtext
          AND A.name = S.name
          AND A.name = P.name
          AND A.price <> 9900
          AND B.price <> 9900
          ORDER BY A.name";

if (!($result = $connection->query($query))) {
    die("Error executing query: " . $connection->error);
}

$num_rows = $result->num_rows;

// HTML header
$stock_header = 
"<html>
    <head>
        <title>LoHi Inquiry</title>
        <link href='css/global.css' rel='stylesheet' type='text/css'>
        <link href='css/vendor/jquery.dataTables.css' rel='stylesheet' type='text/css'>
        <script type='text/javascript' src='js/vendor/jquery.js'></script>
        <script type='text/javascript' src='js/vendor/jquery.dataTables.js'></script>
        <script type='text/javascript'>
            $(document).ready(function() {
                $('#example').dataTable({
                    'pagingType': 'full_numbers',
                    'order': [[0, 'asc']]
                });
            });
        </script>
    </head>  
    <body id='twoCol' class='dt-example'>
        <div id='container'>
            <div id='contentWrap'>
                <div id='main'>
                    <h1>Low/High Inquiry As End Of $present</h1>
                    <table id='example' class='display' cellspacing='0' width='100%'>
                        <thead>  
                            <tr>
                                <th>Name</th>
                                <th>Type</th>                
                                <th>Price</th>    
                                <th>Amount</th>
                                <th>PER</th>    
                                <th>Yield</th>  
                                <th>Dividend</th>
                                <th>PBV</th>
                            </tr>
                        </thead>
                        <tbody>";

// Define header based on the direction
$header = '';
if ($direction == "NewLow") {
    $header = "New Low " . $num_rows . ' Items';
} elseif ($direction == "Low") {
    $header = "Low " . $num_rows . ' Items';
} elseif ($direction == "High") {
    $header = "High " . $num_rows . ' Items';
} elseif ($direction == "NewHigh") {
    $header = "New High " . $num_rows . ' Items';
}

// Fetch the SET index for the present date
$query_set = "SELECT setindex FROM setindex WHERE date = '$present'";
if (!($result_set = $connection->query($query_set))) {
    die("Error executing query: " . $connection->error);
}
$row_set = $result_set->fetch_assoc();
$present_ind = $row_set['setindex'];

// Prepare stock details for display
$stock_details = '';                
while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    $cat = $row['cat'];
    $priceA = $row['priceA'];
    $amt = $row['amt'];  
    $fmtAmt = number_format($amt / 1000000, 3, '.', '');
    $PER = $row['PER'];
    $yield = $row['yield'];    
    $dividend = $row['dividend'];
    $PBV = $row['PBV'];

    $stock_details .= <<<EOD
        <tr>
            <td>$name</td>
            <td>$cat</td>                
            <td>$priceA</td>    
            <td>$fmtAmt</td>    
            <td>$PER</td>        
            <td>$yield</td>         
            <td>$dividend</td>        
            <td>$PBV</td>
        </tr>                    
EOD;
}

// HTML footer
$stock_footer = <<<EOD
                        </tbody>
                    </table>
                    </br>
                    </br>                        
                </div>
                <div id="sidebar">
                    <h2>YieldInq</h2>
                    <br/>
                    <p>Create priceLo table by SELECT name, MIN(price) from price GROUP BY name with one year data.<br>
                    Create priceHi table by SELECT name, MAX(price) from price GROUP BY name with one year data.<br>
                    Create priceTdy table by SELECT name, price from price where date = present date.<br>
                    Select from priceTdy, priceLo or priceHi join stockname on name join per on name.<br>
                    Table per is outdated.</p>
                </div>
            </div>
        </div>
    </body>
</html>
EOD;

// Print the stock details
$stock = $stock_header . $stock_details . $stock_footer;

// print $stock_details;
echo $stock;

// Close the MySQL connection
$connection->close();
?>
