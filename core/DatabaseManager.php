<?php
class DatabaseManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Add manufacturer
    public function addManufacturer($name, $country, $userId) {
        $stmt = $this->pdo->prepare('INSERT INTO car_manufacturers (Manufacturer_name, country, added_by) VALUES (?, ?, ?)');
        $stmt->execute([$name, $country, $userId]);
    }
    

    // Add car
    public function addCar($manufacturerId, $carModel, $price, $transmissionType, $addedBy) {
        $stmt = $this->pdo->prepare('INSERT INTO cars (manufacturer_id, car_model, price, transmission_type, added_by) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$manufacturerId, $carModel, $price, $transmissionType, $addedBy]);
    }

    

    // Update manufacturer
    public function updateManufacturer($id, $name, $country) {
        $stmt = $this->pdo->prepare('UPDATE car_manufacturers SET Manufacturer_name = ?, country = ? WHERE Manufacturer_id = ?');
        $stmt->execute([$name, $country, $id]);
    }

    // Update car
    public function updateCar($carId, $carModel, $price, $transmissionType) {
        $stmt = $this->pdo->prepare('UPDATE cars SET car_model = ?, price = ?, transmission_type = ? WHERE car_id = ?');
        $stmt->execute([$carModel, $price, $transmissionType, $carId]);
    }
    
    

    // Delete manufacturer
    public function deleteManufacturer($id) {
        $stmt = $this->pdo->prepare('DELETE FROM car_manufacturers WHERE Manufacturer_id = ?');
        $stmt->execute([$id]);
    }

    // Delete car
    public function deleteCar($id) {
        $stmt = $this->pdo->prepare('DELETE FROM cars WHERE car_id = ?');
        $stmt->execute([$id]);
    }

    public function getAllManufacturers() {
        $stmt = $this->pdo->prepare("
            SELECT m.*, u.username 
            FROM car_manufacturers m 
            LEFT JOIN users u ON m.added_by = u.id  -- Changed user_id to id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all cars
    public function getAllCars() {
        $stmt = $this->pdo->query('
            SELECT c.car_id, c.car_model, c.price, c.transmission_type, cm.Manufacturer_name, u.username AS added_by
            FROM cars c
            JOIN car_manufacturers cm ON c.manufacturer_id = cm.Manufacturer_id
            LEFT JOIN users u ON c.added_by = u.id
        ');
        return $stmt->fetchAll();
    }
}
