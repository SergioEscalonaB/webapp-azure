<?php
echo "<h1>🚀 Web App en DMZ - Diagnóstico</h1>";

// Forzar uso del nombre privado temporalmente
$db_host = 'postgresql-rubrica.private.postgres.database.azure.com';
$db_user = 'actividadbd';
$db_password = 'Sergioluis26';

echo "<h3>Configuración Forzada:</h3>";
echo "<p>Host: " . $db_host . "</p>";
echo "<p>Usuario: " . $db_user . "</p>";

// Probar DNS primero
echo "<h3>🔍 Diagnóstico DNS:</h3>";
$ip = gethostbyname($db_host);
echo "<p>Resolución: $db_host → $ip</p>";

if ($ip === $db_host) {
    echo "<p style='color: red;'>❌ DNS no resuelve</p>";
} else {
    echo "<p style='color: green;'>✅ DNS resuelve a: $ip</p>";
}

// Probar conexión
$conn_string = "host=$db_host port=5432 dbname=postgres user=$db_user password=$db_password sslmode=require connect_timeout=10";
$conn = @pg_connect($conn_string);

if ($conn) {
    echo "<div style='color: green; border: 2px solid green; padding: 20px; border-radius: 10px;'>";
    echo "<h3>✅ ¡CONEXIÓN EXITOSA!</h3>";
    
    $result = pg_query($conn, "SELECT version(), current_database()");
    $row = pg_fetch_assoc($result);
    
    echo "<p><strong>PostgreSQL:</strong> " . $row['version'] . "</p>";
    echo "<p><strong>Base de datos:</strong> " . $row['current_database'] . "</p>";
    echo "</div>";
    
    pg_close($conn);
} else {
    echo "<div style='color: red; border: 2px solid red; padding: 20px; border-radius: 10px;'>";
    echo "<h3>❌ Error de conexión</h3>";
    echo "<p><strong>Error:</strong> " . pg_last_error() . "</p>";
    echo "<p><strong>Solución:</strong> Cambiar DB_HOST a 'postgresql-rubrica.private.postgres.database.azure.com' en Azure App Service</p>";
    echo "</div>";
}
?>
