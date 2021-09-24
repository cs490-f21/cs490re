<h1>Database Helper Tool</h1>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../../lib/db.php");
$count = 0;
try {

    foreach (glob(__DIR__ . "/*.sql") as $filename) {
        $sql[$filename] = file_get_contents($filename);
    }

    if (isset($sql) && $sql && count($sql) > 0) {
        echo "<p>Found " . count($sql) . " files...</p>";
        /***
         * Sort the array so queries are executed in anticipated order.
         * Be careful with naming, 1-9 is fine, 1-10 has 10 run after #1 due to string sorting.
         */
        ksort($sql);

        $db = getDB();

        $count++;
        foreach ($sql as $key => $value) {
            $statements = explode("--!!", $value);
            foreach ($statements as $statement) {
?>
                <details>
                    <summary><?php echo "Running: $key"; ?></summary>
                    <pre><code><?php echo $statement; ?></code></pre>
                </details>
                <?php

                $stmt = $db->prepare($statement);
                $result = $stmt->execute();
                $count++;
                $error = $stmt->errorInfo();
                ?>
                <details style="margin-left: 3em">
                    <summary>Status: <?php echo ($error[0] === "00000" ? "Success" : "Error"); ?></summary>
                    <pre><?php echo var_export($error, true); ?></pre>
                </details>
                <br>
<?php
            }
        }
        echo "<p>Init complete, used approximately $count db calls.</p>";
    } else {
        echo "<p>Didn't find any files, please check the directory/directory contents/permissions (note files must end in .sql)</p>";
    }
    $db = null;
} catch (Exception $e) {
    echo $e->getMessage();
    exit("Something went wrong");
}
