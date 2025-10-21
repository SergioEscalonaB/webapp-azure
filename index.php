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
echo "<p>DB_USER: " . (getenv('DB_USER') ? 'Sí configurado' : 'No configurado') . "</p>";
?>
