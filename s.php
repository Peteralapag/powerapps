<?php
session_start();

echo "<h2>Session Debugger</h2>";
echo "<strong>Session ID:</strong> " . session_id() . "<br><br>";

if (!empty($_SESSION)) {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "No session data found.";
}
?>
