<?php include('inc/header.php'); ?>

<?php

require "DB.inc";

// Get the POST value or set an empty default
$present = isset($_POST['present']) ? $_POST['present'] : '';

// Create a new mysqli connection
$connection = new mysqli($hostName, $username, $password, $databaseName);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Find maximum date
$query_max = "SELECT MAX(date) AS max_date FROM price"; 
$result_max = $connection->query($query_max);

if (!$result_max) {
    die("Query failed: " . $connection->error);
}

$row_max = $result_max->fetch_assoc();
$to_date = date($row_max['max_date']);

// Update dividend query
$query = "UPDATE dividend SET dividend = q1 + q2 + q3 + q4";
if (!$connection->query($query)) {
    die("Update failed: " . $connection->error);
}

// Main query to fetch dividend and price data
$query = "SELECT Y.name AS name, xdate, paiddate, q4, q3, q2, q1, Y.dividend, P.price, Y.actual
          FROM dividend AS Y, price AS P
          WHERE Y.name = P.name
          AND P.date = '$present'";

$result = $connection->query($query);

if (!$result) {
    die("Query failed: " . $connection->error);
}

$num_rows = $result->num_rows;

// HTML header
$stock_header = <<<EOD
<html>
    <head>
        <title>Div 1</title>
        <link href="css/global.css" rel="stylesheet" type="text/css">
        <link href="css/vendor/jquery.dataTables.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="js/vendor/jquery.js"></script>
        <script type="text/javascript" src="js/vendor/jquery.dataTables.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#example').dataTable({
                    "pagingType": "full_numbers",
                    "order": [[1, "desc"], [2, "desc"], [0, "asc"]]
                });
            });
        </script>
    </head>
    <body id="twoCol" class="dt-example">
        <div id="container">
            <div id="contentWrap">
                <div id="main">
                    <h1>Dividend Inquiry As End Of $to_date</h1>
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>  
                            <tr>
                                <th>Name</th>
                                <th>A</th>
                                <th>X-Date</th>
                                <th>Pay Date</th>
                                <th>Q4</th>
                                <th>Q3</th>
                                <th>Q2</th>
                                <th>Q1</th>
                                <th>Dividend</th>
                                <th>Price</th>
                                <th>Yield</th>
                            </tr>
                        </thead>
                        <tbody>
EOD;

$stock_details = '';
while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    $xdate = $row['xdate'];  
    $paiddate = $row['paiddate'];  
    $q4 = number_format($row['q4'], 4);  
    $q3 = number_format($row['q3'], 4);  
    $q2 = number_format($row['q2'], 4);  
    $q1 = number_format($row['q1'], 4);  
    $dividend = number_format($row['dividend'], 4);  
    $price = $row['price'];   
    $fmtPrice = number_format($price, 2, '.', '');
    $percent = number_format($dividend/$price*100, 2);    
    $actual = $row['actual'];

    $stock_details .= <<<EOD
                            <tr>
                                <td>$name</td>
                                <td>$actual</td>
                                <td>$xdate</td>
                                <td>$paiddate</td>
                                <td>$q4</td>
                                <td>$q3</td>
                                <td>$q2</td>
                                <td>$q1</td>
                                <td>$dividend</td>
                                <td>$price</td>
                                <td>$percent</td>
                            </tr>\n
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
                    <p>Select from dividend join with price on name and price is selected by input date. Dividend table is manually updated. This program also updates dividend by accumulate from q1 to q4.</p>
                </div>
            </div>
        </div>
    </body>
</html>
EOD;

$stock = $stock_header . $stock_details . $stock_footer;
echo $stock;

// Close the connection
$connection->close();
?>