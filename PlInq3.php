<?php
    include('inc/header.php');
    require "DB.inc";

    // Establish a new MySQL connection using mysqli
    $connection = new mysqli($hostName, $username, $password, $databaseName);
    // Check if the connection was successful
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

// Get the price date and other form inputs
$price_date = isset($_POST['Price_Date']) ? $_POST['Price_Date'] : '';
$Active = isset($_POST['Active']) ? $_POST['Active'] : '';
$order = isset($_POST['Sequence']) ? $_POST['Sequence'] : '';
// Define a whitelist of valid columns for ordering to avoid SQL injection
$valid_columns = ['name', 'period', 'grade', 'date', 'volbuy', 'buy_price', 'mkt_price', 'buy_amt', 'mkt_amt', 'amtpol', 'percent'];

// Check if the provided order column is valid
if (!in_array($order, $valid_columns)) {
    die("Invalid order column.");
}

// Initialize the stock details output
$stock_details = '';

// Dynamically construct the SQL query with the valid column name
$query = "SELECT period, buy.name AS name, buy.date AS date, FORMAT(volbuy,0) AS volbuy,
          FORMAT(buy.price,2) AS buy_price, price.price AS mkt_price,
          FORMAT((volbuy * buy.price),2) AS buy_amt, buy.grade AS grade,
          FORMAT((volbuy * price.price),2) AS mkt_amt, 
          FORMAT(((price.price - buy.price) * volbuy),2) AS amtpol,
          FORMAT((((price.price - buy.price)*volbuy)/(volbuy*buy.price)*100),2) AS percent
          FROM buy INNER JOIN price ON buy.name = price.name
          WHERE price.date = ?
          AND buy.active = ?
          ORDER BY period, $order DESC";  // Insert the column name directly

// Prepare the statement
$stmt = $connection->prepare($query);
$stmt->bind_param('ss', $price_date, $Active);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Initialize the table header
$stock_details = "<table align='center' width='95%' border='0' cellspacing='1' cellpadding='2'>
			<tr>
				<td width='10%'>
					<div align='center'>
					  As end of
					</div>
				</td>
				<td width='78%'>
					<div align='center'>
						<font color='#003366' size='5'>Portfolio Profit/Loss Position</font>
					</div>				
				</td>				
				<td width='12%'>
					<div align='center'>
					  " . htmlspecialchars($price_date) . " 
					</div>
				</td>		
			</tr>
		</table>";

// Initialize the table HTML
$stock_details .= "<table align='center' border='1' cellpadding='5' cellspacing='1' style='border-collapse: collapse;'>
<tr>
    <th>Prd</th>
    <th>Grd</th>
    <th>Name</th>
    <th>Date</th>
    <th>Volume</th>
    <th>Cost</th>
    <th>Market</th>
    <th>Cost Amt</th>
    <th>Market Amt</th>
    <th>Profit/Loss</th>
    <th>P/L%</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    $period = $row['period'];
    $grade = $row['grade'];
    $name = $row['name'];
    $date = $row['date'];
    $volbuy = $row['volbuy'];
    $buy_price = $row['buy_price'];
    $mkt_price = $row['mkt_price'];
    $buy_amt = $row['buy_amt'];
    $mkt_amt = $row['mkt_amt'];
    $amtpol = $row['amtpol'];
    $percent = $row['percent'];

    // Add row class based on profit/loss
    $rowClass = ($mkt_price < $buy_price) ? 'loss' : 'profit';
    
    $stock_details .= "<tr class='$rowClass'>
        <td>$period</td>
        <td>$grade</td>
        <td>$name</td>
        <td align='center'>$date</td>
        <td align='right'>$volbuy</td>
        <td align='right'>$buy_price</td>
        <td align='right'>$mkt_price</td>
        <td align='right'>$buy_amt</td>
        <td align='right'>$mkt_amt</td>
        <td align='right'>$amtpol</td>
        <td align='right'>$percent</td>
    </tr>";
}

// Find total buy amount
$query_buy = "SELECT SUM(volbuy * buy.price) AS ttlbuy
              FROM buy
              WHERE buy.active = ? AND buy.date <= ?";

$stmt_buy = $connection->prepare($query_buy);
$stmt_buy->bind_param('ss', $Active, $price_date);
$stmt_buy->execute();
$result_buy = $stmt_buy->get_result();
$row_buy = $result_buy->fetch_assoc();
$ttlbuy = $row_buy['ttlbuy'];
$cost = number_format($ttlbuy, 2);
$stmt_buy->close();

// Find total market amount
$query_mkt = "SELECT SUM(buy.volbuy * price.price) AS ttlmkt
              FROM buy INNER JOIN price ON buy.name = price.name
              WHERE price.date = ?
              AND buy.active = ?";

$stmt_mkt = $connection->prepare($query_mkt);
$stmt_mkt->bind_param('ss', $price_date, $Active);
$stmt_mkt->execute();
$result_mkt = $stmt_mkt->get_result();
$row_mkt = $result_mkt->fetch_assoc();
$ttlmkt = $row_mkt['ttlmkt'];
$market_value = number_format($ttlmkt, 2);
$stmt_mkt->close();

// Calculate profit/loss and percentage
$profit = number_format($ttlmkt - $ttlbuy, 2);
$percent_profit = ($ttlbuy != 0) ? number_format((($ttlmkt - $ttlbuy) / $ttlbuy) * 100, 2) : 0;

// Get SET index
$query_set = "SELECT setindex FROM setindex WHERE date = ?";
$stmt_set = $connection->prepare($query_set);
$stmt_set->bind_param('s', $price_date);
$stmt_set->execute();
$result_set = $stmt_set->get_result();
$row_set = $result_set->fetch_assoc();
$setindex = $row_set['setindex'];
$stmt_set->close();

// Add the totals row to the table
$stock_details .= "<tr style='font-weight: bold; background-color: #BDD3F7;'>
	<td colspan='6'></td>
    <td align='right'>Total</td>
    <td align='right'>$cost</td>
    <td align='right'>$market_value</td>
    <td align='right'>$profit</td>
    <td align='right'>$percent_profit</td>
</tr>";

$stock_details .="<tr>
                    <td colspan='9'></td>
				    <td  bgcolor='#BDD3F7' colspan='1'>
					    <div align='right'>
						    <b>
							    SET Index : 
						    </b>
					    </div>
				    </td>
				    <td  bgcolor='#BDD3F7'>
					    <div align='right'>
						    $setindex
					    </div>
				    </td>
			    </tr>";

$stock_details .= "</table>";

// Create the SET index display
$stock_set = "<div style='margin-top: 10px;'>
    <strong>SET Index:</strong> " . number_format($setindex, 2) . "
</div>";

// Add CSS styles
echo "<style>
    table { width: 95%; margin-bottom: 20px; }
    th { background-color: #f0f0f0; }
    td, th { padding: 8px; }
    .loss { background-color: #ffe6e6; }
    .profit { background-color: #e6ffe6; }
    tr:hover { background-color: #f5f5f5; }
</style>";

// Output the formatted content
echo $stock_details;
// echo $stock_set;

$connection->close();
?>
