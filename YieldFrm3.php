<?php include('inc/header.php'); ?>

<div class="container page-content">
        
    <div class="row">
        <div class="col-sm-4 col-md-4 widget">
            <!-- <p>---------1---------2---------3 Column 1 ---------1---------2---------3</p>
            <div class="thumbnail widget">
                <img src="http://lorempixel.com/400/400" />
            </div> -->
        </div>
        <div class="col-sm-4 col-md-4 widget">
            <!-- <p>---------1---------2---------3 Column 2 ---------1---------2---------3</p> -->

            <form method="post" action="YieldInq3.php" role="form">
                <fieldset>
                    <legend>Legend</legend>    
                    <label>Price date</label>
                    <?php
                        // Database connection details
                        require "DB.inc";
                        require "DateOpt.php";

                        // Establish connection using mysqli
                        $connection = new mysqli($hostName, $username, $password, $databaseName);

                        // Check connection
                        if ($connection->connect_error) {
                            die("Connection failed: " . $connection->connect_error);
                        }

                        // Initialize date select variable
                        $date_select = '';

                        // Call the function to select distinct dates
                        selectDistinctDate($connection, "price", "date", "present", $date_select);

                        // Close the connection
                        $connection->close();
                    ?>
                    <br />
                    <br />                    
                    <button type="submit" class="btn btn-large btn-primary">Submit</button> 
                </fieldset>
            </form>
        </div>  <!-- End of col-md-4 -->

        <div class="col-sm-4 col-md-4 widget">
            <!-- <p>---------1---------2---------3 Column 3 ---------1---------2---------3</p> -->
            <!-- <div class="thumbnail widget">
                <img src="http://lorempixel.com/400/400" />
            </div> -->
        </div> 
    </div>  <!-- End of Row -->       
</div> <!-- End of Container -->