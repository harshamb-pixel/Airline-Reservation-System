-- Basic tables for airline reservation system
CREATE DATABASE IF NOT EXISTS airline;
USE airline;

-- Flight table
CREATE TABLE IF NOT EXISTS flight (
    Flight_number VARCHAR(10) PRIMARY KEY,
    Airline VARCHAR(50),
    Departure_time TIME,
    Arrival_time TIME,
    Departure_city VARCHAR(50),
    Arrival_city VARCHAR(50)
);

-- Airplane table  
CREATE TABLE IF NOT EXISTS airplane (
    Airplane_id VARCHAR(10) PRIMARY KEY,
    Total_seats INT,
    Airplane_type VARCHAR(50)
);

-- Seat table
CREATE TABLE IF NOT EXISTS seat (
    Seat_no VARCHAR(5) PRIMARY KEY,
    Seat_type VARCHAR(20)
);

-- Reservation table
CREATE TABLE IF NOT EXISTS reservation (
    Reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    Flight_number VARCHAR(10),
    Leg_no INT,
    Date DATE,
    Airplane_id VARCHAR(10),
    Seat_no VARCHAR(5),
    Customer_name VARCHAR(100),
    Cphone VARCHAR(15),
    Email VARCHAR(100),
    FOREIGN KEY (Flight_number) REFERENCES flight(Flight_number),
    FOREIGN KEY (Airplane_id) REFERENCES airplane(Airplane_id),
    FOREIGN KEY (Seat_no) REFERENCES seat(Seat_no)
);

-- Insert sample data
INSERT IGNORE INTO flight VALUES 
('AA101', 'American Airlines', '08:00:00', '10:00:00', 'New York', 'Boston'),
('UA202', 'United Airlines', '14:00:00', '16:30:00', 'Chicago', 'Miami');

INSERT IGNORE INTO airplane VALUES 
('A001', 180, 'Boeing 737'),
('A002', 200, 'Airbus A320');

INSERT IGNORE INTO seat VALUES 
('1A', 'First Class'),
('1B', 'First Class'),
('2A', 'Business'),
('2B', 'Business'),
('3A', 'Economy'),
('3B', 'Economy');
