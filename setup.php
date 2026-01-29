<?php
/**
 * Task 3: Database Setup & Seeding Script
 * Run this script once to initialize the database
 */

echo "=== Task 3: Database Setup ===\n\n";

// Read configuration
$config_file = __DIR__ . '/php/config.php';
if (!file_exists($config_file)) {
    die("Error: config.php not found\n");
}

require_once $config_file;

// Test connection
echo "1. Testing database connection...\n";
if ($mysqli->connect_error) {
    die("   ❌ Connection failed: " . $mysqli->connect_error . "\n");
}
echo "   ✅ Connected to MySQL\n\n";

// Read schema
echo "2. Reading database schema...\n";
$schema_file = __DIR__ . '/database/schema.sql';
if (!file_exists($schema_file)) {
    die("   ❌ schema.sql not found\n");
}

$schema = file_get_contents($schema_file);
echo "   ✅ Schema loaded\n\n";

// Execute schema
echo "3. Creating database and tables...\n";
$queries = explode(';', $schema);

foreach ($queries as $query) {
    $query = trim($query);
    if (empty($query)) continue;
    
    if (!$mysqli->query($query)) {
        echo "   ❌ Error: " . $mysqli->error . "\n";
        echo "   Query: " . substr($query, 0, 100) . "...\n";
    } else {
        echo "   ✅ Executed: " . substr($query, 0, 50) . "...\n";
    }
}

echo "\n4. Verifying database setup...\n";

// Check database exists
$db_check = $mysqli->query("SELECT DATABASE()");
$db = $db_check->fetch_row()[0];
echo "   ✅ Database: $db\n";

// Check tables
$tables_result = $mysqli->query("SHOW TABLES");
echo "   ✅ Tables created:\n";
while ($table = $tables_result->fetch_row()) {
    echo "      • " . $table[0] . "\n";
}

// Check users
$users_result = $mysqli->query("SELECT COUNT(*) as count FROM users");
$users = $users_result->fetch_assoc();
echo "   ✅ Users: " . $users['count'] . "\n";

echo "\n=== Setup Complete! ===\n";
echo "\nDefault Admin Credentials:\n";
echo "  Username: admin\n";
echo "  Password: admin123\n";
echo "\nAccess: http://localhost/task3/\n";

$mysqli->close();
?>
