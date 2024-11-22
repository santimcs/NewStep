<?php
function selectDistinctDate($connection, $tableName, $attributeName, $pulldownName, $defaultValue)
{
    $defaultWithinResultSet = TRUE;

    // Query to find distinct values of $attributeName in $tableName
    $distinctQuery = "SELECT DISTINCT {$attributeName} FROM {$tableName} ORDER BY {$attributeName} DESC LIMIT 60";

    // Run the distinctQuery on the database using MySQLi
    if (!($result_date = $connection->query($distinctQuery))) {
        die("Query failed: " . $connection->error);
    }

    // Start the select widget
    print "\n<select name=\"{$pulldownName}\">";

    // Retrieve each row from the query
    while ($row = $result_date->fetch_assoc()) {
        // Get the value for the attribute to be displayed
        $result = $row[$attributeName];

        // Check if a defaultValue is set and, if so, is it the
        // current database value?
        if (isset($defaultValue) && $result == $defaultValue) {
            // Yes, show as selected
            print "\n\t<option value=\"{$result}\" SELECTED>{$result}</option>";
        } else {
            // No, just show as an option
            print "\n\t<option value=\"{$result}\">{$result}</option>";
        }
    }

    print "\n</select>";
}
?>