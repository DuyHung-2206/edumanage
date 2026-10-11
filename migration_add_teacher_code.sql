-- Migration: Bổ sung teacher_code vào bảng users và cấp mã cho giáo viên cũ
USE edumanage;

-- 1. Thêm cột teacher_code vào users nếu chưa tồn tại
SET @dbname = DATABASE();
SET @tablename = "users";
SET @columnname = "teacher_code";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE users ADD COLUMN teacher_code VARCHAR(20) NULL UNIQUE AFTER role"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Procedure sinh mã GV-XXXXXXXX duy nhất cho các giáo viên cũ
DELIMITER $$
DROP PROCEDURE IF EXISTS MigrateTeacherCodes$$
CREATE PROCEDURE MigrateTeacherCodes()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE u_id INT;
    DECLARE new_code VARCHAR(20);
    DECLARE code_exists INT;
    
    DECLARE cur CURSOR FOR 
        SELECT id FROM users 
        WHERE role = 'teacher' 
          AND (teacher_code IS NULL OR teacher_code = '');
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO u_id;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET code_exists = 1;
        WHILE code_exists > 0 DO
            SET new_code = CONCAT('GV-', UPPER(SUBSTRING(MD5(RAND()), 1, 8)));
            SELECT COUNT(*) INTO code_exists FROM users WHERE teacher_code = new_code;
        END WHILE;

        UPDATE users SET teacher_code = new_code WHERE id = u_id;
    END LOOP;
    CLOSE cur;
END$$
DELIMITER ;

CALL MigrateTeacherCodes();
DROP PROCEDURE IF EXISTS MigrateTeacherCodes;
