<?php
session_start();
require 'core/dbConfig.php';
require 'core/DatabaseManager.php';
require 'core/ActivityLog.php'; // Include ActivityLog

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Instantiate the DatabaseManager and ActivityLog
$db = new DatabaseManager($pdo);
$activityLog = new ActivityLog($pdo);

// Retrieve username from the session
$username = $_SESSION['username']; // Assuming you store the username in the session

// Handle Manufacturer Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'addManufacturer') {
        $manufacturerName = $_POST['Manufacturer_name'];
        $country = $_POST['country'];
        $addedBy = $_SESSION['user_id'];

        // Use the addManufacturer function from DatabaseManager
        $db->addManufacturer($manufacturerName, $country, $addedBy);
        
        // Log the action
        $activityLog->logAction($addedBy, $username, "Added Manufacturer: $manufacturerName");
        
        header('Location: index.php');
        exit();
    }
    
    // Handle Car Form Submission
    if (isset($_POST['action']) && $_POST['action'] === 'addCar') {
        $manufacturerId = $_POST['manufacturer_id'];
        $carModel = $_POST['car_model'];
        $price = $_POST['price'];
        $transmissionType = $_POST['transmission_type'];
        $addedBy = $_SESSION['user_id'];

        // Use the addCar function from DatabaseManager
        $db->addCar($manufacturerId, $carModel, $price, $transmissionType, $addedBy);
        
        // Log the action
        $activityLog->logAction($addedBy, $username, "Added Car: $carModel");

        header('Location: index.php');
        exit();
    }

    // Handle Manufacturer Update
    if (isset($_POST['action']) && $_POST['action'] === 'updateManufacturer') {
        $manufacturerId = $_POST['Manufacturer_id'];
        $manufacturerName = $_POST['Manufacturer_name'];
        $country = $_POST['country'];
        $db->updateManufacturer($manufacturerId, $manufacturerName, $country);
        
        // Log the action
        $activityLog->logAction($_SESSION['user_id'], $username, "Updated Manufacturer: $manufacturerName");

        header('Location: index.php');
        exit();
    }

    // Handle Car Update
    if (isset($_POST['action']) && $_POST['action'] === 'updateCar') {
        $carId = $_POST['car_id'];
        $carModel = $_POST['car_model']; // Ensure this is being set
        $price = $_POST['price'];
        $transmissionType = $_POST['transmission_type'];
        
        // Debugging: Check if carModel is set correctly
        if (empty($carModel)) {
            die('Car model is missing!');
        }
        
        $db->updateCar($carId, $carModel, $price, $transmissionType);
        
        // Log the action
        $activityLog->logAction($_SESSION['user_id'], $username, "Updated Car: $carModel");
    
        header('Location: index.php');
        exit();
    }

    // Handle Manufacturer Deletion
    if (isset($_POST['action']) && $_POST['action'] === 'deleteManufacturer') {
        $manufacturerId = $_POST['Manufacturer_id'];
        $manufacturerName = $_POST['Manufacturer_name'];
        $db->deleteManufacturer($manufacturerId);
        
        // Log the action
        $activityLog->logAction($_SESSION['user_id'], $username, "Deleted Manufacturer: $manufacturerName");

        header('Location: index.php');
        exit();
    }

    // Handle Car Deletion
    if (isset($_POST['action']) && $_POST['action'] === 'deleteCar') {
        $carId = $_POST['car_id'];
        $carModel = $_POST['car_model'];
        $db->deleteCar($carId);
        
        // Log the action
        $activityLog->logAction($_SESSION['user_id'], $username, "Deleted Car: $carModel");

        header('Location: index.php');
        exit();
    }
}

// Fetch all manufacturers and cars
$manufacturers = $db->getAllManufacturers();
$cars = $db->getAllCars();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Inventory System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        h1, h2 {
            color: #2c3e50;
        }
        
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background-color: #2c3e50;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e0e0e0;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        form {
            margin-bottom: 20px;
        }

        /* Centering the logout button */
        .logout-button {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Car Inventory System</h1>

    <!-- Add Manufacturer Form -->
    <h2>Add Manufacturer</h2>
    <form method="post">
        <input type="hidden" name="action" value="addManufacturer">
        <input type="text" name="Manufacturer_name" placeholder="Manufacturer Name" required>
        <input type="text" name="country" placeholder="Country" required>
        <button type="submit">Add Manufacturer</button>
    </form>

    <!-- Add Car Form -->
    <h2>Add Car</h2>
    <form method="post">
        <input type="hidden" name="action" value="addCar">
        <select name="manufacturer_id" required>
            <option value="">Select Manufacturer</option>
            <?php foreach ($manufacturers as $manufacturer): ?>
                <option value="<?= $manufacturer['Manufacturer_id'] ?>"><?= $manufacturer['Manufacturer_name'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="car_model" placeholder="Car Model" required>
        <input type="number" name="price" placeholder="Price" step="0.01" required>
        <input type="text" name="transmission_type" placeholder="Transmission Type" required>
        <button type="submit">Add Car</button>
    </form>

    <!-- Display Manufacturers Table -->
    <h2>All Manufacturers</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Country</th>
            <th>Date Added</th>
            <th>Added By</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($manufacturers as $manufacturer): ?>
            <tr>
                <td><?= $manufacturer['Manufacturer_id'] ?></td>
                <td><?= $manufacturer['Manufacturer_name'] ?></td>
                <td><?= $manufacturer['country'] ?></td>
                <td><?= $manufacturer['date_added'] ?></td>
                <td><?= isset($manufacturer['username']) ? $manufacturer['username'] : 'Unknown' ?></td> <!-- Changed to display username -->
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="updateManufacturer">
                        <input type="hidden" name="Manufacturer_id" value="<?= $manufacturer['Manufacturer_id'] ?>">
                        <input type="text" name="Manufacturer_name" value="<?= $manufacturer['Manufacturer_name'] ?>" required>
                        <input type="text" name="country" value="<?= $manufacturer['country'] ?>" required>
                        <button type="submit">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="deleteManufacturer">
                        <input type="hidden" name="Manufacturer_id" value="<?= $manufacturer['Manufacturer_id'] ?>">
                        <input type="hidden" name="Manufacturer_name" value="<?= $manufacturer['Manufacturer_name'] ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this manufacturer?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>


    <!-- Display Cars Table -->
    <h2>All Cars</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Car Model</th>
            <th>Manufacturer</th>
            <th>Price</th>
            <th>Transmission Type</th>
            <th>Added By</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($cars as $car): ?>
            <tr>
                <td><?= $car['car_id'] ?></td>
                <td><?= $car['car_model'] ?></td>
                <td><?= $car['Manufacturer_name'] ?></td>
                <td><?= $car['price'] ?></td>
                <td><?= $car['transmission_type'] ?></td>
                <td><?= $car['added_by'] ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="updateCar">
                        <input type="hidden" name="car_id" value="<?= $car['car_id'] ?>">
                        <input type="text" name="car_model" value="<?= htmlspecialchars($car['car_model']) ?>" required> <!-- Use htmlspecialchars for safety -->
                        <input type="number" name="price" value="<?= $car['price'] ?>" step="0.01" required>
                        <input type="text" name="transmission_type" value="<?= htmlspecialchars($car['transmission_type']) ?>" required>
                        <button type="submit">Update</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="deleteCar">
                        <input type="hidden" name="car_id" value="<?= $car['car_id'] ?>">
                        <input type="hidden" name="car_model" value="<?= $car['car_model'] ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this car?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>



    <!-- Logout Button -->
    <form method="post" action="logout.php" style="text-align: center;">
        <button type="submit">Logout</button>
    </form>
</div>

</body>
</html>