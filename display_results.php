<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require "DB.inc";

try {
    // MySQL connection (using existing $conn from DB.inc)
    $db_stock = $conn;

    // Get the maximum date from the 'price' table
    $max_date = '';
    $sql_max_date = "SELECT MAX(date) AS max_date FROM price";
    $stmt_max_date = $db_stock->query($sql_max_date);
    
    // Use fetch() instead of fetch_assoc() for PDO
    if ($stmt_max_date && $row_max_date = $stmt_max_date->fetch(PDO::FETCH_ASSOC)) {
        $max_date = $row_max_date['max_date'];  // Store the max date
    }

    // SQLite connection
    try {
        $sqlite_path = 'C:\\ruby\\portlt\\db\\development.sqlite3';
        if (!file_exists($sqlite_path)) {
            throw new Exception("SQLite database file not found at: " . $sqlite_path);
        }
        $db_development = new PDO("sqlite:$sqlite_path");
        $db_development->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        error_log("SQLite Connection Error: " . $e->getMessage());
        $db_development = null;
    }

    // PostgreSQL connection
    try {
        $pg_host = 'localhost';
        $pg_port = '5432';
        $pg_dbname = 'portpg_development';
        $pg_user = 'postgres';
        $pg_password = 'admin';

        $pg_dsn = "pgsql:host={$pg_host};port={$pg_port};dbname={$pg_dbname};";
        $db_portpg = new PDO($pg_dsn, $pg_user, $pg_password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        error_log("PostgreSQL Connection Error: " . $e->getMessage());
        $db_portpg = null;
    }

    include('inc/header.php');
    ?>

    <div class="container page-content">
        <!-- Date Selection Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group">
                                <label for="inp_date">Select Date:</label>
                                <input type="date" class="form-control" id="inp_date" name="inp_date" 
                                       value="<?php echo isset($_SESSION['inp_date']) ? $_SESSION['inp_date'] : $max_date; ?>" 
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Display Results</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Display database connection warnings if needed
        if (!$db_development) {
            echo '<div class="alert alert-warning">Warning: SQLite database connection failed</div>';
        }
        if (!$db_portpg) {
            echo '<div class="alert alert-warning">Warning: PostgreSQL database connection failed</div>';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Handle input date
                if (isset($_POST['inp_date'])) {
                    $_SESSION['inp_date'] = $_POST['inp_date'];
                }
                $inp_date = isset($_SESSION['inp_date']) ? $_SESSION['inp_date'] : '';

                if (empty($inp_date)) {
                    throw new Exception("No date provided");
                }

                // Update dividend data
                $sql_upd = "UPDATE buy B 
                           SET dividend = (
                               SELECT DIVIDEND 
                               FROM dividend D 
                               WHERE B.name = D.name
                           )";
                $db_stock->exec($sql_upd);

                // First query - Stock data
                $sql1 = "SELECT 
                            B.name, 
                            volbuy, 
                            B.price AS u_cost,
                            dividend, 
                            P.price AS mkt_price, 
                            period AS prd
                        FROM buy B
                        JOIN price P ON B.name = P.name
                        WHERE P.date = :inp_date
                        AND active = 1
                        ORDER BY period, name";
                
                $stmt1 = $db_stock->prepare($sql1);
                $stmt1->execute(['inp_date' => $inp_date]);
                $rows1 = $stmt1->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll() to get all rows

                // Second query - EPS data (if SQLite is connected)
                $rows2 = [];
                if ($db_development) {
                    $sql2 = "SELECT name, aq_eps AS eps
                            FROM epss
                            WHERE year = 2022 AND quarter = 4";
                    $stmt2 = $db_development->prepare($sql2);
                    $stmt2->execute();
                    $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll() for SQLite
                }

                // Third query - Market data (if PostgreSQL is connected)
                $rows3 = [];
                if ($db_portpg) {
                    $sql3 = "SELECT name, market FROM tickers";
                    $stmt3 = $db_portpg->prepare($sql3);
                    $stmt3->execute();
                    $rows3 = $stmt3->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll() for PostgreSQL
                }

                // Helper function to find matching rows
                function findRowByName($rows, $name) {
                    foreach ($rows as $row) {
                        if ($row['name'] === $name) {
                            return $row;
                        }
                    }
                    return [];
                }

                // Merge data from all queries
                $merged = [];
                foreach ($rows1 as $row1) {
                    $matching2 = findRowByName($rows2, $row1['name']);
                    $matching3 = findRowByName($rows3, $row1['name']);
                    $merged[] = array_merge($row1, $matching2, $matching3);
                }

                // Format data for display
                $formatted_rows = [];
                foreach ($merged as $row) {
                    $cost_amt = $row['volbuy'] * $row['u_cost'];
                    $mkt_amt = $row['volbuy'] * $row['mkt_price'];
                    $dividend = isset($row['dividend']) ? $row['dividend'] : 0;
                    $div_amt = $row['volbuy'] * $dividend;
                    $eps = isset($row['eps']) ? $row['eps'] : 0;

                    $formatted_rows[] = [
                        'prd' => $row['prd'],
                        'name' => $row['name'],
                        'shares' => number_format($row['volbuy']),
                        'u_cost' => number_format($row['u_cost'], 2),
                        'mkt_price' => number_format($row['mkt_price'], 2),
                        'dividend' => number_format($dividend, 4),
                        'cst_percent' => number_format($cost_amt ? ($div_amt / $cost_amt * 100) : 0, 2),
                        'mkt_percent' => number_format($mkt_amt ? ($div_amt / $mkt_amt * 100) : 0, 2),
                        'eps' => number_format($eps, 4),
                        'dpr_percent' => number_format($eps ? ($dividend / $eps * 100) : 0, 2),
                        'market' => isset($row['market']) ? $row['market'] : ''
                    ];
                }

                if (!empty($formatted_rows)) {
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <!-- Add DataTables ID here -->
                                        <table id="results-table" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>T</th>
                                                    <th>Name</th>
                                                    <th>Shares</th>
                                                    <th>Div</th>
                                                    <th>U_cost</th>
                                                    <th>Price</th>
                                                    <th>EPS</th>
                                                    <th>Cst-%</th>
                                                    <th>Mkt-%</th>
                                                    <th>Dpr-%</th>
                                                    <th>Market</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($formatted_rows as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['prd']) ?></td>
                                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                                    <td><?= htmlspecialchars($row['shares']) ?></td>
                                                    <td><?= htmlspecialchars($row['dividend']) ?></td>
                                                    <td><?= htmlspecialchars($row['u_cost']) ?></td>
                                                    <td><?= htmlspecialchars($row['mkt_price']) ?></td>
                                                    <td><?= htmlspecialchars($row['eps']) ?></td>
                                                    <td class="<?= floatval($row['cst_percent']) >= 5.00 ? 'text-success' : '' ?>">
                                                        <?= htmlspecialchars($row['cst_percent']) ?>%
                                                    </td>
                                                    <td class="<?= floatval($row['mkt_percent']) >= 5.00 ? 'text-success' : '' ?>">
                                                        <?= htmlspecialchars($row['mkt_percent']) ?>%
                                                    </td>
                                                    <td><?= htmlspecialchars($row['dpr_percent']) ?>%</td>
                                                    <td><?= htmlspecialchars($row['market']) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Include DataTables CSS and JS -->
                    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

                    <script>
                        $(document).ready(function() {
                            $('#results-table').DataTable({
                                "pagingType": "full_numbers",
                                "order": [[1, "asc"]]
                            });
                        });
                    </script>

                    <?php
                } else {
                    echo '<div class="alert alert-info">No data found for the selected date.</div>';
                }

            } catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
                echo '<div class="alert alert-danger">Error processing request: ' . 
                      htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        ?>
    </div>

    <?php
    // include('inc/footer.php');

} catch (Exception $e) {
    error_log("Fatal Error: " . $e->getMessage());
    echo '<div class="alert alert-danger">A system error occurred. Please try again later.</div>';
}
?>