<?php

// Handle reservation status updates
if (isset($_GET['table_id']) && isset($_GET['status'])) {
    $tableId = $_GET['table_id'];
    $status = ($_GET['status'] == 'occupied') ? 1 : 0;

    // Check if the table is occupied by a reservation
    $checkOccupiedQuery = "SELECT COUNT(*) FROM reservations WHERE table_id = :table_id AND is_active = 1";
    $stmtCheck = $conn->prepare($checkOccupiedQuery);
    $stmtCheck->bindParam(':table_id', $tableId, PDO::PARAM_INT);
    $stmtCheck->execute();
    $reservationCount = $stmtCheck->fetchColumn();

        // Update the 'is_active' column in the 'reservations' table
        $updateQuery = "UPDATE reservations SET is_active = :status WHERE table_id = :table_id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':table_id', $tableId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Display a success message
            echo "<p>Table status updated successfully.</p>";
            header('Location: dashboard.php');
            exit();
        } else {
            echo "<p>Error updating table status.</p>";
        }
}

// Get table information from the "tables" table
$query = "SELECT table_id, table_name, capacity FROM tables";
$stmt = $conn->prepare($query);
$stmt->execute();
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>