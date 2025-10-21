<?php
// Configuración simple para probar
$db_host = getenv('DB_HOST') ?: 'rubrica-proyecto.postgres.database.azure.com';
$db_user = getenv('DB_USER');
$db_password = getenv('DB_PASSWORD');

// Función simple de conexión
function testDB() {
    global $db_host, $db_user, $db_password;
    
    $conn_string = "host={$db_host} port=5432 dbname=postgres user={$db_user} password={$db_password} sslmode=require";
    $conn = pg_connect($conn_string);
    
    if (!$conn) {
        return ['success' => false, 'error' => pg_last_error()];
    }
    
    $result = pg_query($conn, "SELECT version()");
    if (!$result) {
        return ['success' => false, 'error' => pg_last_error($conn)];
    }
    
    $version = pg_fetch_result($result, 0);
    pg_close($conn);
    
    return ['success' => true, 'version' => $version];
}

// Procesar solicitud de prueba
if (isset($_GET['test'])) {
    header('Content-Type: application/json');
    echo json_encode(testDB());
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>App PHP - Azure</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Aplicación PHP en Azure</h1>
        <p>Probando conexión a PostgreSQL...</p>
        
        <button onclick="testDB()">Probar Conexión DB</button>
        <div id="result"></div>
        
        <h3>Información del Servidor:</h3>
        <ul>
            <li>PHP Version: <?php echo phpversion(); ?></li>
            <li>PostgreSQL Extension: <?php echo extension_loaded('pgsql') ? '✅ Cargada' : '❌ No cargada'; ?></li>
            <li>PDO PostgreSQL: <?php echo extension_loaded('pdo_pgsql') ? '✅ Cargada' : '❌ No cargada'; ?></li>
        </ul>
    </div>

    <script>
        async function testDB() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = 'Probando conexión...';
            
            try {
                const response = await fetch('?test=1');
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = '<div class="success">✅ Conexión exitosa: ' + data.version + '</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error">❌ Error: ' + data.error + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ Error de red: ' + error.message + '</div>';
            }
        }
        
        // Probar automáticamente al cargar
        window.onload = testDB;
    </script>
</body>
</html>
