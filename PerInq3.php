<?php include('inc/header.php'); ?>

<div class="container page-content">

    <div class="panel panel-success">
        <table class="table">
            <thead>  
                <tr>    
                    <th>Rank</th>                
                    <th>Name</th>
                    <th>PER</th>
                    <th>PBV</th>
                </tr>
            </thead>
            <tbody>
<?php

require "DB.inc";

// Establish a new MySQL connection using mysqli
$connection = new mysqli($hostName, $username, $password, $databaseName);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get the sector name from the POST request
$sector_name = isset($_POST['Sector_Name']) ? $_POST['Sector_Name'] : '';

// Build the query
$query = "SELECT per.name, per.Price, EPS, PER, PBV, Yield, Dividend,
          ROA, ROE, NPM, DERatio, Par, Shares, category, ROE.Profit
          FROM per 
          INNER JOIN stockname ON per.name = stockname.name
          INNER JOIN ROE ON per.name = ROE.name
          WHERE sector = '$sector_name' 
          ORDER BY PER";

// Execute the query
if (!($result = $connection->query($query))) {
    die("Error executing query: " . $connection->error);
}

$nbr = 0;  
$stock_details = ''; 

// Fetch the results and build the table rows
while ($row = $result->fetch_assoc()) {
    $nbr += 1;
    $stock_name = $row['name'];
    $PER = $row['PER'];
    $PBV = $row['PBV'];
    
    $stock_details .= <<<EOD
                <tr>
                    <td>$nbr</td>                
                    <td>$stock_name</td>
                    <td>$PER</td>
                    <td>$PBV</td>
                </tr>\n
EOD;
}

// Print the stock details
print $stock_details;

// Close the connection
$connection->close();

?>
            </tbody>
        </table>
    </div>  <!-- End of class="panel panel-default">    
</div>  <!-- End Of Container -->

