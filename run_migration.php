<?php
require_once __DIR__.'/config/config.php';
try {
    $sql = file_get_contents(__DIR__.'/migration_add_notifications.sql');
    db()->exec($sql);
    echo 'Migration successful';
} catch (Exception $e) {
    echo 'Migration failed: ' . $e->getMessage();
}
