CREATE TABLE IF NOT EXISTS 
otifications (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  message varchar(255) NOT NULL,
  is_read tinyint(1) NOT NULL DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT k_notification_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
