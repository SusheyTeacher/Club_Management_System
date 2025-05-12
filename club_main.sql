-- Create database
CREATE DATABASE IF NOT EXISTS club_management;
USE club_management;

-- Users Table: Students and Mentors
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  class INT DEFAULT NULL,                          -- For students
  student_number VARCHAR(50) DEFAULT NULL,         -- For students
  email VARCHAR(100) DEFAULT NULL,                 -- For students
  employment_id VARCHAR(50) DEFAULT NULL,          -- For mentors
  password VARCHAR(255) NOT NULL,
  role ENUM('student', 'mentor') NOT NULL,
  club_id INT DEFAULT NULL,                        -- Linked club (student's main club)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL
);

-- Clubs Table
CREATE TABLE clubs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  advisor VARCHAR(100),
  image VARCHAR(100)
);

-- Events Table
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  date DATE NOT NULL,
  image VARCHAR(100),
  club_id INT NOT NULL,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Applications Table
CREATE TABLE event_applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  student_id INT NOT NULL,
  status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
  applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Memberships Table (optional)
CREATE TABLE memberships (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  club_id INT NOT NULL,
  role VARCHAR(50) DEFAULT 'member',
  join_date DATE NOT NULL DEFAULT (CURDATE()),
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE
);
