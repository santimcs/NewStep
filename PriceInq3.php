<?php include('inc/header.php'); ?>

<div class="container page-content">
    <?php
    try {
        require "DB.inc";
        
        // Create PDO connection
        $dsn = "mysql:host=$hostName;dbname=$databaseName;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        // Get stock name from POST
        $stock_name = isset($_POST['Stock_Name']) ? $_POST['Stock_Name'] : '';
        
        if (empty($stock_name)) {
            throw new Exception("No stock name provided");
        }
        
        // Prepare and execute the main query
        $query = "SELECT 
                    p.name, 
                    p.date,
                    DAYNAME(p.date) AS day,
                    p.price,
                    p.maxp,
                    p.minp,
                    s.setindex,
                    p.qty,
                    (p.qty * p.price) AS amt
                  FROM price p
                  INNER JOIN setindex s ON p.date = s.date
                  WHERE p.name = ?
                  ORDER BY p.date DESC";
                  
        $stmt = $pdo->prepare($query);
        $stmt->execute([$stock_name]);
        
        // Initialize arrays for chart data
        $dates = [];
        $prices = [];
        $setindexes = [];
        $maxps = [];
        $minps = [];
        $tableRows = '';
        
        while ($row = $stmt->fetch()) {
            // Add data points
            $dates[] = $row['date'];
            $prices[] = floatval($row['price']);
            $setindexes[] = floatval($row['setindex']);
            $maxps[] = floatval($row['maxp']);
            $minps[] = floatval($row['minp']);
            
            // Format numbers
            $fmtQty = number_format($row['qty'], 0, '.', ',');
            
            // Build table row
            $tableRows .= "<tr>
                <td>{$row['date']}</td>
                <td>{$row['price']}</td>
                <td>{$row['maxp']}</td>
                <td>{$row['minp']}</td>
                <td>{$row['setindex']}</td>
                <td>{$fmtQty}</td>
            </tr>";
        }
    ?>
    
    <!-- <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="PriceFrm3.php">Price</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($stock_name) ?></li>
            </ol>
        </nav>
    </div> -->

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div style="width:100%; height:400px;">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Maximum</th>
                                    <th>Minimum</th>
                                    <th>Set Index</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $tableRows ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('myChart').getContext('2d');
        
        // Reverse the arrays to show chronological order
        const dates = <?= json_encode(array_reverse($dates)) ?>;
        const prices = <?= json_encode(array_reverse($prices)) ?>;
        const setindexes = <?= json_encode(array_reverse($setindexes)) ?>;
        const maxps = <?= json_encode(array_reverse($maxps)) ?>;
        const minps = <?= json_encode(array_reverse($minps)) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Price',
                        data: prices,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: true,
                        yAxisID: 'yPrice',
                        tension: 0.1
                    },
                    {
                        label: 'Maximum',
                        data: maxps,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        fill: false,
                        yAxisID: 'yPrice',
                        tension: 0.1
                    },
                    {
                        label: 'Minimum',
                        data: minps,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        fill: false,
                        yAxisID: 'yPrice',
                        tension: 0.1
                    },
                    {
                        label: 'SET Index',
                        data: setindexes,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.1)',
                        fill: true,
                        yAxisID: 'ySetIndex',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                plugins: {
                    title: {
                        display: true,
                        text: '<?= htmlspecialchars($stock_name) ?> Price and SET Index Analysis'
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    yPrice: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Price'
                        }
                    },
                    ySetIndex: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'SET Index'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    });
    </script>

    <?php
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo '<div class="alert alert-danger">An error occurred. Please try again later.</div>';
    }
    ?>
</div>

<?php include('inc/footer.php'); ?>