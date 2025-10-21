<?php
// Versión mínima para probar el despliegue
echo "<h1>🚀 Aplicación PHP Desplegada en Azure</h1>";
echo "<h2>✅ ¡Despliegue exitoso!</h2>";

// Información del servidor
echo "<h3>Información del Servidor:</h3>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li>Extensiones cargadas:</li>";
echo "</ul>";

// Verificar extensiones de PostgreSQL
$extensions = ['pgsql', 'pdo_pgsql'];
echo "<h3>Extensiones de PostgreSQL:</h3>";
echo "<ul>";
foreach ($extensions as $ext) {
    echo "<li>" . $ext . ": " . (extension_loaded($ext) ? "✅ Cargada" : "❌ No cargada") . "</li>";
}
echo "</ul>";

// Probar variables de entorno
echo "<h3>Variables de Entorno:</h3>";
echo "<ul>";
echo "<li>DB_HOST: " . (getenv('DB_HOST') ?: 'No configurada') . "</li>";
echo "<li>DB_USER: " . (getenv('DB_USER') ? '*** Configurado ***' : 'No configurado') . "</li>";
echo "<li>DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '*** Configurado ***' : 'No configurado') . "</li>";
echo "</ul>";

// Probar conexión simple a PostgreSQL si las variables están configuradas
if (getenv('DB_HOST') && getenv('DB_USER') && getenv('DB_PASSWORD')) {
    echo "<h3>Prueba de Conexión PostgreSQL:</h3>";
    
    try {
        $conn_string = "host=" . getenv('DB_HOST') . " port=5432 dbname=postgres user=" . getenv('DB_USER') . " password=" . getenv('DB_PASSWORD') . " sslmode=require";
        $conn = pg_connect($conn_string);
        
        if ($conn) {
            $result = pg_query($conn, "SELECT version()");
            if ($result) {
                $version = pg_fetch_result($result, 0);
                echo "<p style='color: green;'>✅ Conexión exitosa: " . $version . "</p>";
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
    echo "<p style='color: orange;'>⚠️ Configura las variables de entorno DB_HOST, DB_USER y DB_PASSWORD en Azure App Service</p>";
}
?>
