CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE activity_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE car_manufacturers (
    Manufacturer_id INT AUTO_INCREMENT PRIMARY KEY,
    Manufacturer_name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    date_added DATE DEFAULT CURRENT_DATE
);

CREATE TABLE cars (
    car_id INT AUTO_INCREMENT PRIMARY KEY,
    car_model VARCHAR(50),
    manufacturer_id INT,
    price FLOAT,
    transmission_type VARCHAR(50),
    FOREIGN KEY (manufacturer_id) REFERENCES car_manufacturers(Manufacturer_id)
);