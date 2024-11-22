<?php include('inc/header.php'); ?>

<div class="container page-content">

    <div class="row">
        
        <div class="col-sm-4 col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="post" action="PriceInq3.php" class="needs-validation" novalidate>
                        <fieldset>
                            <legend>Stock Selection</legend>
                            <div class="mb-3">
                                <label for="Stock_Name" class="form-label">Stock Name</label>
                                <?php
                                try {
                                    require "DB.inc";
                                    require "NameOpt.php";
                                    
                                    $dsn = "mysql:host=$hostName;dbname=$databaseName;charset=utf8mb4";
                                    $pdo = new PDO($dsn, $username, $password, [
                                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                                    ]);
                                    
                                    $stmt = $pdo->query("SELECT DISTINCT name FROM stockname ORDER BY name");
                                    echo '<select class="form-select" name="Stock_Name" id="Stock_Name" required>';
                                    echo '<option value="">Select a stock...</option>';
                                    while ($row = $stmt->fetch()) {
                                        echo '<option value="' . htmlspecialchars($row['name']) . '">' . 
                                             htmlspecialchars($row['name']) . '</option>';
                                    }
                                    echo '</select>';
                                } catch(PDOException $e) {
                                    error_log("Database Error: " . $e->getMessage());
                                    echo "Error loading stock names. Please try again later.";
                                }
                                ?>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>