<?php
echo "<h1>🚀 Web App en DMZ - Conectando a PostgreSQL en PRD</h1>";

$db_host = getenv('DB_HOST') ?: 'postgresql-rubrica.private.postgres.database.azure.com';
$db_user = getenv('DB_USER') ?: 'actividadbd';  // Usuario sin @
$db_password = getenv('DB_PASSWORD');

echo "<h3>Configuración:</h3>";
echo "<p>Host: " . $db_host . "</p>";
echo "<p>Usuario: " . $db_user . "</p>";

if ($db_host && $db_user && $db_password) {
    $conn_string = "host=$db_host port=5432 dbname=postgres user=$db_user password=$db_password sslmode=require connect_timeout=10";
    $conn = @pg_connect($conn_string);
    
    if ($conn) {
        echo "<div style='color: green; border: 2px solid green; padding: 20px; border-radius: 10px; background: #f0fff0;'>";
        echo "<h3>✅ ¡CONEXIÓN EXITOSA ENTRE VNETS!</h3>";
        
        $result = pg_query($conn, "SELECT version() as version, current_database() as db, current_user as usuario");
        $row = pg_fetch_assoc($result);
        
        echo "<p><strong>PostgreSQL:</strong> " . $row['version'] . "</p>";
        echo "<p><strong>Base de datos:</strong> " . $row['db'] . "</p>";
        echo "<p><strong>Usuario:</strong> " . $row['usuario'] . "</p>";
        echo "<p><strong>Arquitectura:</strong> VNet DMZ → VNet Producción (Peering)</p>";
        echo "</div>";
        
        // Probar inserción de datos
        pg_query($conn, "INSERT INTO test_vnet_peering (mensaje) VALUES ('✅ Conexión desde Web App PHP')");
        $result = pg_query($conn, "SELECT * FROM test_vnet_peering ORDER BY fecha_creacion DESC LIMIT 3");
        
        echo "<h4>📊 Registros de prueba:</h4>";
        while ($row = pg_fetch_assoc($result)) {
            echo "<p>[" . $row['fecha_creacion'] . "] " . $row['mensaje'] . "</p>";
        }
        
        pg_close($conn);
    } else {
        echo "<div style='color: red; border: 2px solid red; padding: 20px; border-radius: 10px; background: #fff0f0;'>";
        echo "<h3>❌ Error de conexión</h3>";
        echo "<p><strong>Error:</strong> " . pg_last_error() . "</p>";
        echo "</div>";
    }
}

echo '<br><button onclick="location.reload()">🔄 Probar Nuevamente</button>';
?>
