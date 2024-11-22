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

// Main query to fetch day trade data
$query = "SELECT A.name AS name, S.category AS cat, A.minp AS minp, 
          A.price AS price, A.maxp AS maxp, FORMAT(A.qty, 0) AS volume,
          FORMAT(A.price * A.qty / 1000000, 0) AS amount, PER, yield,
          FORMAT(P.dividend, 2) AS dividend, PBV, T.price AS avgp,
          CASE 
              WHEN A.price < T.price THEN 'ddd'
              WHEN A.price > T.price THEN 'uuu'
              ELSE 'sss' 
          END AS trend
          FROM daytrade D 
          JOIN price A USING (name)
          JOIN stockname S USING (name)
          JOIN per P USING (name)
          JOIN tendays T USING (name)			
          WHERE A.date = '$to_date'
          ORDER BY D.name";

// Execute the query
if (!($result = $connection->query($query))) {
    die("Error executing query: " . $connection->error);
}

$num_rows = $result->num_rows;

// Prepare the HTML headers
$stock_header = <<<EOD
<html>
    <head>
        <link rel="shortcut icon" type="image/ico" href="media/images/santi.ico" />
        <title>Day Trade Inquiry</title>
        <link href="css/global.css" rel="stylesheet" type="text/css">
        <link href="css/vendor/jquery.dataTables.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="js/vendor/jquery.js"></script>
        <script type="text/javascript" src="js/vendor/jquery.dataTables.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#example').dataTable({
                    "pagingType": "full_numbers",
                    "order": [[9, "desc"]]
                });
            });
        </script>
    </head>
    <body id="twoCol" class="dt-example">
        <div id="container">
            <div id="contentWrap">
                <div id="main">
                    <h1>Day Trading Inquiry As End Of $to_date</h1>
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>                
                                <th>Min</th>    
                                <th>Price</th>    
                                <th>Max</th>    
                                <th>Amt</th>                              
                                <th>PER</th>                            
                                <th>P/BV</th>                            
                                <th>Avg Price</th>
                                <th>Pct</th>                            
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>                
                                <th>Min</th>    
                                <th>Price</th>    
                                <th>Max</th>    
                                <th>Amt</th>                              
                                <th>PER</th>                        
                                <th>P/BV</th>                            
                                <th>10 Days</th>
                                <th>Pct</th>                            
                            </tr>
                        </tfoot>                    
                        <tbody>
EOD;

$item = 0;
$stock_details = '';
while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    $cat = $row['cat'];
    $minp = $row['minp'];
    $price = $row['price'];          
    $maxp = $row['maxp'];
    $amount = $row['amount'];    
    $PER = $row['PER'];
    $yield = $row['yield'];    
    $dividend = $row['dividend'];    
    $PBV = $row['PBV'];
    $avgp = $row['avgp'];    
    $trend = $row['trend'];    
    $item = $item + 1;
    $pct = number_format(($price - $avgp) / $avgp * 100, 2);

    $stock_details .= <<<EOD
                            <tr>
                                <td>$name</td>
                                <td>$cat</td>                
                                <td>$minp</td>    
                                <td>$price</td>    
                                <td>$maxp</td>        
                                <td>$amount</td>        
                                <td>$PER</td>                        
                                <td>$PBV</td>        
                                <td class="center">$avgp</td>
                                <td>$pct</td>                            
                            </tr>\n                    
EOD;
}

$stock_footer = <<<EOD
                        </tbody>
                    </table>
                    </br>
                    </br>
                </div>
                <div id="sidebar">
                    <h2>DayTradeInq</h2>
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