CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    age INT,
    grade VARCHAR(5),
    email VARCHAR(100)
);
INSERT INTO students (first_name, last_name, age, grade, email)
VALUES ('Rahul', 'Mehra', 18, '12A', 'rahul.mehra@example.com');
INSERT INTO students (first_name, last_name, age, grade, email)
VALUES
('Ananya', 'Verma', 17, '11B', 'ananya.verma@example.com'),
('Rohit', 'Patil', 18, '12C', 'rohit.patil@example.com'),
('Sneha', 'Kumar', 17, '11A', 'sneha.kumar@example.com');
SELECT * FROM students;
