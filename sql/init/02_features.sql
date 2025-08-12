USE techassist;

-- ------- status workflow / priority / timestamps
ALTER TABLE tickets
  ADD COLUMN priority ENUM('low','normal','high') NOT NULL DEFAULT 'normal',
  ADD COLUMN assignee_id INT NULL,
  ADD COLUMN closed_at TIMESTAMP NULL DEFAULT NULL,
  ADD CONSTRAINT fk_tickets_assignee FOREIGN KEY (assignee_id) REFERENCES users(id);

-- ------- comments
CREATE TABLE IF NOT EXISTS ticket_comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  user_id INT NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tc_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  CONSTRAINT fk_tc_user   FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  INDEX ix_tc_ticket_created (ticket_id, created_at)
) ENGINE=InnoDB;

-- ------- audit log
CREATE TABLE IF NOT EXISTS audit_log (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(64) NOT NULL,     -- e.g., ticket.create, ticket.comment, ticket.close
  ticket_id INT NULL,
  details JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX ix_audit_ticket (ticket_id, created_at)
) ENGINE=InnoDB;

-- ------- demo users (extra)
INSERT IGNORE INTO users(username, password_hash, role)
VALUES ('alice', '$2y$10$WECAHh4eyVfBeUO2PXy6sOezcEU77jDGC/RMqsDa1ScANP50jjPNW', 'user'),
       ('bob',   '$2y$10$WECAHh4eyVfBeUO2PXy6sOezcEU77jDGC/RMqsDa1ScANP50jjPNW', 'user');

-- ------- demo tickets (20 mixed)
INSERT INTO tickets(user_id, department_id, title, body, status, priority, created_at)
SELECT u.id, d.id,
       CONCAT('Demo ticket #', n.n),
       CONCAT('Body for ticket #', n.n),
       IF(n.n % 4 = 0, 'closed', 'open'),
       CASE WHEN n.n % 3 = 0 THEN 'high' WHEN n.n % 3 = 1 THEN 'normal' ELSE 'low' END,
       DATE_SUB(NOW(), INTERVAL (21-n.n) DAY)
FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
      UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
      UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
      UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20) n
JOIN users u ON u.username='demo'
LEFT JOIN departments d ON d.name='IT';

-- close some
UPDATE tickets SET closed_at = created_at + INTERVAL 1 DAY
WHERE status='closed' AND closed_at IS NULL;

-- sample comments
INSERT INTO ticket_comments(ticket_id, user_id, body)
SELECT t.id, u.id, CONCAT('Initial comment for ticket ', t.id)
FROM tickets t JOIN users u ON u.username='demo'
WHERE t.id <= (SELECT MIN(id)+5 FROM tickets);
