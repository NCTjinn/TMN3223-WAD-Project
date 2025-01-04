<?php
header('Content-Type: application/json');

$servername = "sql112.infinityfree.com";
$username = "if0_37979402";
$password = "tmn3223ncnhcds";
$dbname = "if0_37979402_pufflab";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(["error" => "Connection failed"]));
}

// SQL query to fetch member data
$sql = "SELECT user_id AS id, username, created_at AS creationDate, points, 
        (SELECT SUM(total_amount) FROM Transactions WHERE Transactions.user_id = Users.user_id) AS totalSpent, 
        (SELECT MAX(transaction_date) FROM Transactions WHERE Transactions.user_id = Users.user_id) AS lastTransaction 
        FROM Users WHERE role = 'member'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $members = [];
    while ($row = $result->fetch_assoc()) {
        $row['totalSpent'] = $row['totalSpent'] ?: 0; // Default to 0 if null
        $row['lastTransaction'] = $row['lastTransaction'] ?: "No transactions yet";
        $members[] = $row;
    }
    echo json_encode($members);
} else {
    echo json_encode([]);
    error_log("No members found or error in query.");
}

$conn->close();
?>
