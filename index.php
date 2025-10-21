<?php
echo "<h1>🚀 ¡Hola Azure!</h1>";
echo "<h2>✅ Aplicación PHP funcionando</h2>";

// Información básica
echo "<h3>Información del servidor:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";

// Probar PostgreSQL
echo "<h3>Extensiones PostgreSQL:</h3>";
echo "<p>pgsql: " . (extension_loaded('pgsql') ? "✅ Sí" : "❌ No") . "</p>";
echo "<p>pdo_pgsql: " . (extension_loaded('pdo_pgsql') ? "✅ Sí" : "❌ No") . "</p>";

// Variables de entorno
echo "<h3>Variables de entorno DB:</h3>";
echo "<p>DB_HOST: " . (getenv('DB_HOST') ?: 'No configurada') . "</p>";
echo "<p>DB_USER: " . (getenv('DB_USER') ? '✅ Sí configurado' : '❌ No configurado') . "</p>";
echo "<p>DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '✅ Sí configurado' : '❌ No configurado') . "</p>";

// PROBAR CONEXIÓN A POSTGRESQL
echo "<h3>🧪 Prueba de Conexión PostgreSQL:</h3>";

$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_password = getenv('DB_PASSWORD');

if ($db_host && $db_user && $db_password) {
    try {
        // Cadena de conexión
        $conn_string = "host={$db_host} port=5432 dbname=postgres user={$db_user} password={$db_password} sslmode=require";
        
        echo "<p>🔗 Intentando conectar a: {$db_host}...</p>";
        
        $conn = pg_connect($conn_string);
        
        if ($conn) {
            echo "<p style='color: green;'>✅ Conexión establecida</p>";
            
            // Probar consulta simple
            $result = pg_query($conn, "SELECT version(), current_database(), current_user");
            
            if ($result) {
                $row = pg_fetch_assoc($result);
                echo "<p><strong>Versión PostgreSQL:</strong> " . $row['version'] . "</p>";
                echo "<p><strong>Base de datos:</strong> " . $row['current_database'] . "</p>";
                echo "<p><strong>Usuario:</strong> " . $row['current_user'] . "</p>";
                
                echo "<p style='color: green; font-weight: bold;'>🎉 ¡CONEXIÓN EXITOSA A POSTGRESQL!</p>";
            } else {
                echo "<p style='color: red;'>❌ Error en consulta: " . pg_last_error($conn) . "</p>";
            }
            
            pg_close($conn);
        } else {
            echo "<p style='color: red;'>❌ No se pudo conectar: " . pg_last_error() . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Excepción: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Faltan variables de entorno. Configura DB_PASSWORD en Azure App Service</p>";
}

// Botón para recargar y probar nuevamente
echo '<br><button onclick="location.reload()">🔄 Probar Conexión Nuevamente</button>';
?>
