CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  department_id INT NULL,
  title VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  status ENUM('open','closed') NOT NULL DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX(status, created_at),
  CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_tickets_dept FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

INSERT INTO departments(name) VALUES ('IT'), ('HR'), ('Finance');

-- demo password = "password"
INSERT INTO users(username, password_hash, role) VALUES
('admin', '$2y$10$WECAHh4eyVfBeUO2PXy6sOezcEU77jDGC/RMqsDa1ScANP50jjPNW', 'admin'),
('demo',  '$2y$10$WECAHh4eyVfBeUO2PXy6sOezcEU77jDGC/RMqsDa1ScANP50jjPNW', 'user');
