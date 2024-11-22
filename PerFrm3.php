<?php include('inc/header.php'); ?>

<div class="container page-content">

    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <form method="post" action="PerInq3.php">
                </br>
                </br>
                <div>
                    <h3><span class="label label-default">Sector</span></h3>
                    <?php
                        require "DB.inc";
                        require "SectorOpt.php";

                        // Establish a new MySQL connection using mysqli
                        $connection = new mysqli($hostName, $username, $password, $databaseName);

                        // Check connection
                        if ($connection->connect_error) {
                            die("Connection failed: " . $connection->connect_error);
                        }

                        $defaultValue = '';

                        // Call the function to select distinct sector names
                        selectDistinctName($connection, "stockname", "sector", "Sector_Name", $defaultValue);

                        // Close the connection
                        $connection->close();
                    ?>
                </div>
                </br>
                </br>
                <div>
                    <button type="submit" class="btn btn-large btn-primary">Submit</button>
                </div>
            </form>
        </div>  <!-- class="col-md-4" -->
    </div>  <!-- End of Row -->
</div>  <!-- Container -->

