<?php
echo "<h1>🚀 ¡Hola Azure!</h1>";
echo "<h2>✅ Aplicación PHP funcionando</h2>";

// Información básica
echo "<h3>Información del servidor:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";

// Variables de entorno
echo "<h3>Variables de entorno DB:</h3>";
echo "<p>DB_HOST: " . (getenv('DB_HOST') ?: 'No configurada') . "</p>";
echo "<p>DB_USER: " . (getenv('DB_USER') ? '✅ Sí configurado' : '❌ No configurado') . "</p>";
echo "<p>DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '✅ Sí configurado' : '❌ No configurado') . "</p>";

// PROBAR CONEXIÓN CON TIMEOUT
echo "<h3>🧪 Prueba de Conexión PostgreSQL:</h3>";

$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_password = getenv('DB_PASSWORD');

if ($db_host && $db_user && $db_password) {
    echo "<p>🔗 Intentando conectar a: <strong>{$db_host}</strong>...</p>";
    
    // Configurar timeout
    set_time_limit(30); // 30 segundos máximo
    
    try {
        // Intentar conexión con opciones de timeout
        $conn_string = "host={$db_host} port=5432 dbname=postgres user={$db_user} password={$db_password} sslmode=require";
        
        // Intentar conexión directamente
        $conn = @pg_connect($conn_string);
        
        if (!$conn) {
            $error = pg_last_error();
            echo "<p style='color: red;'>❌ Error de conexión: " . htmlspecialchars($error) . "</p>";
            
            // Diagnóstico adicional
            echo "<h4>🔍 Diagnóstico:</h4>";
            echo "<p>• Verifica que el servidor PostgreSQL esté ejecutándose</p>";
            echo "<p>• Revisa las credenciales de usuario/contraseña</p>";
            echo "<p>• Confirma que la IP de Azure App Service esté permitida en el firewall de PostgreSQL</p>";
            
        } else {
            echo "<p style='color: green;'>✅ Conexión establecida</p>";
            
            // Probar consulta simple
            $result = pg_query($conn, "SELECT version() as version, current_database() as db, current_user as user");
            
            if ($result) {
                $row = pg_fetch_assoc($result);
                echo "<p><strong>Versión PostgreSQL:</strong> " . $row['version'] . "</p>";
                echo "<p><strong>Base de datos:</strong> " . $row['db'] . "</p>";
                echo "<p><strong>Usuario:</strong> " . $row['user'] . "</p>";
                
                echo "<p style='color: green; font-weight: bold;'>🎉 ¡CONEXIÓN EXITOSA A POSTGRESQL!</p>";
            } else {
                echo "<p style='color: red;'>❌ Error en consulta: " . pg_last_error($conn) . "</p>";
            }
            
            pg_close($conn);
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Excepción: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Faltan variables de entorno</p>";
}

// Probar con PDO también
echo "<h3>🧪 Prueba Alternativa con PDO:</h3>";
try {
    $pdo = new PDO(
        "pgsql:host={$db_host};port=5432;dbname=postgres",
        $db_user,
        $db_password,
        [
            PDO::ATTR_TIMEOUT => 10,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
    echo "<p style='color: green;'>✅ PDO: Conexión exitosa</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ PDO Error: " . $e->getMessage() . "</p>";
}

echo '<br><button onclick="location.reload()">🔄 Probar Nuevamente</button>';
?>
