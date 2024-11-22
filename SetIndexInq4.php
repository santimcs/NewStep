<?php
include('inc/header.php');
require "DB.inc";

try {
    // Create PDO connection
    $dsn = "mysql:host=$hostName;dbname=$databaseName;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Prepare and execute query
    $query = "SELECT date, setindex FROM setindex ORDER BY date DESC";
    $stmt = $pdo->query($query);

    $stock_header = <<<EOD
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SET Index Inquiry</title>
        <link href="css/table_style.css" rel="stylesheet" type="text/css">

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        
        <script type="text/javascript">
        $(document).ready(function() {
            var labels = [];
            var dataPoints = [];
EOD;

    $stock_details = '';
    $table_rows = '';
    
    while($row = $stmt->fetch()) {
        $date = htmlspecialchars($row['date']);
        $setindex = floatval($row['setindex']);

        // Building JavaScript arrays for labels and data
        $stock_details .= "labels.push('$date');\n";
        $stock_details .= "dataPoints.push($setindex);\n";

        // Building HTML table rows
        $table_rows .= <<<EOD
            <tr>
                <td class="date">$date</td>
                <td class="setindex">$setindex</td>
            </tr>
EOD;
    }

    $stock_footer = <<<EOD
            // Reverse arrays for chronological order
            labels.reverse();
            dataPoints.reverse();

            // Create Chart.js chart
            const ctx = document.getElementById('myChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'SET Index',
                        data: dataPoints,
                        fill: true,
                        backgroundColor: 'rgba(75,192,192,0.4)',
                        borderColor: 'rgba(75,192,192,1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Index'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Stock Exchange of Thailand Index'
                        }
                    }
                }
            });

            // Table visibility toggle
            $('#toggleButton').click(function() {
                $('#datatable').fadeToggle(400);
            });
        });
        </script>
    </head>
    <body>
        <div class="container" style="width:80%; margin:20px auto;">
            <canvas id="myChart"></canvas>
            
            <div class="controls" style="margin: 20px 0;">
                <button id="toggleButton" class="btn">Show/Hide Table</button>
            </div>
            
            <div class="table-responsive">
                <table id="datatable" class="myTable" style="margin: 0 auto;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>SET Index</th>
                        </tr>
                    </thead>
                    <tbody>
EOD;

    $stock_footer .= $table_rows;
    $stock_footer .= <<<EOD
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
EOD;

    $stock = $stock_header . $stock_details . $stock_footer;
    echo $stock;

} catch(PDOException $e) {
    // Error handling
    error_log("Database Error: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
    exit;
}
?>