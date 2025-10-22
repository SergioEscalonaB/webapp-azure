<?php
echo "<h1>🚀 Web App - Usando IP Directa</h1>";

$db_host = getenv('DB_HOST') ?: '10.0.0.4'; // Forzar IP si no hay variable
$db_user = getenv('DB_USER') ?: 'actividadbd';
$db_password = getenv('DB_PASSWORD') ?: 'Sergioluis26';

$conn = pg_connect("host=$db_host port=5432 dbname=postgres user=$db_user password=$db_password sslmode=require");

if ($conn) {
    echo "<div style='color: green;'>✅ ¡CONEXIÓN EXITOSA!</div>";
    $result = pg_query($conn, "SELECT version(), current_database()");
    $row = pg_fetch_assoc($result);
    echo "<p>PostgreSQL: " . $row['version'] . "</p>";
    pg_close($conn);
} else {
    echo "<div style='color: red;'>❌ Error: " . pg_last_error() . "</div>";
}
?>
