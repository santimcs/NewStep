<form class="form-horizontal" method="post" action="PLInq3.php">
    <!-- Price Date -->
    <div class="form-group">
        <label class="control-label col-md-4" for="date">Price Date</label>
        <div class="col-md-8">
            <?php
            require "DB.inc";  // This file should contain your database connection credentials
            require "DateOpt.php";  // The file where selectDistinctDate function is defined

            // Use MySQLi to connect to the database
            $connection = new mysqli($hostName, $username, $password, $databaseName);

            // Check if the connection was successful
            if ($connection->connect_error) {
                die("Connection failed: " . $connection->connect_error);
            }

            // Default date
            $date_select = '2005-04-07';

            // Call the function to display the date dropdown
            selectDistinctDate($connection, "price", "date", "Price_Date", $date_select);

            // Close the connection after we're done
            $connection->close();
            ?>
        </div>
    </div>

    <!-- Active -->
    <div class="form-group">
        <label class="control-label col-md-4" for="Active">Active?</label>
        <div class="col-md-8">
            <input type="radio" name="Active" value="1" CHECKED> Yes
            <br/>
            <input type="radio" name="Active" value="no"> No
        </div>
    </div>

    <!-- Order By -->
    <div class="form-group">
        <label class="control-label col-md-4" for="orderby">Order By</label>
        <div class="col-md-8">
            <input type="radio" name="Sequence" value="date"> Date
            <br/>
            <input type="radio" name="Sequence" value="name"> Name
            <br/>
            <input type="radio" name="Sequence" value="percent" CHECKED> Percent
            <br/>
        </div>
    </div>

    <!-- Submit and Reset Buttons -->
    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <br/>
            <button type="submit" class="btn btn-large btn-primary">Submit</button>
            &nbsp;&nbsp;&nbsp;
            <button type="reset" class="btn btn-large btn-danger">Reset</button>
        </div>
    </div>
</form>