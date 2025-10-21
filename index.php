<?php
// Configuración de la base de datos
$db_host = getenv('DB_HOST') ?: 'rubrica-proyecto.postgres.database.azure.com';
$db_port = getenv('DB_PORT') ?: 5432;
$db_name = getenv('DB_NAME') ?: 'postgres';
$db_user = getenv('DB_USER');
$db_password = getenv('DB_PASSWORD');

// Función para conectar a PostgreSQL
function connectDB() {
    global $db_host, $db_port, $db_name, $db_user, $db_password;
    
    $conn_string = "host={$db_host} port={$db_port} dbname={$db_name} user={$db_user} password={$db_password} sslmode=require";
    $dbconn = pg_connect($conn_string);
    
    return $dbconn;
}

// Procesar solicitudes AJAX
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'test-connection':
            testConnection();
            break;
        case 'tables':
            listTables();
            break;
        case 'create-table':
            createTable();
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    }
    exit;
}

if ($_POST['action'] ?? '' === 'insert') {
    header('Content-Type: application/json');
    insertData();
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web App PRD - Azure PHP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 14px; }
        .status-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }
        button.secondary { background: #764ba2; }
        .form-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }
        .form-group { margin-bottom: 20px; }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea { resize: vertical; min-height: 100px; }
        #result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        #result.success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        #result.error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Web App PRD - Azure PHP</h1>
        <p class="subtitle">Ejercicio 3: Conectividad con PostgreSQL desde DMZ</p>
        
        <div class="status-box">
            <h3>Estado de Conexión</h3>
            <p id="connection-status">Verificando conexión a base de datos...</p>
        </div>

        <div class="button-group">
            <button onclick="testConnection()">🔍 Probar Conexión DB</button>
            <button onclick="showTables()" class="secondary">📋 Listar Tablas</button>
            <button onclick="createTable()">➕ Crear Tabla Test</button>
        </div>

        <div class="form-section">
            <h3>Insertar Registro de Prueba</h3>
            <form onsubmit="insertData(event)">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>
                </div>
                <button type="submit">💾 Guardar en PostgreSQL</button>
            </form>
        </div>

        <div id="result"></div>
    </div>

    <script>
        window.onload = () => { testConnection(); };

        async function testConnection() {
            showLoading('Probando conexión...');
            try {
                const response = await fetch('?action=test-connection');
                const data = await response.json();
                if (data.success) {
                    showResult('✅ Conexión exitosa a PostgreSQL<br><small>Host: ' + data.host + '<br>Versión: ' + data.version + '</small>', 'success');
                    document.getElementById('connection-status').innerHTML = '✅ <strong>Conectado</strong> - PostgreSQL ' + data.version;
                } else {
                    showResult('❌ Error de conexión: ' + data.error, 'error');
                    document.getElementById('connection-status').innerHTML = '❌ <strong>Desconectado</strong> - ' + data.error;
                }
            } catch (error) {
                showResult('❌ Error de red: ' + error.message, 'error');
            }
        }

        async function showTables() {
            showLoading('Consultando tablas...');
            try {
                const response = await fetch('?action=tables');
                const data = await response.json();
                if (data.success) {
                    const tableList = data.tables.map(t => '• ' + t).join('<br>');
                    showResult('📋 <strong>Tablas en la base de datos:</strong><br><br>' + tableList, 'success');
                } else {
                    showResult('❌ Error: ' + data.error, 'error');
                }
            } catch (error) {
                showResult('❌ Error: ' + error.message, 'error');
            }
        }

        async function createTable() {
            showLoading('Creando tabla...');
            try {
                const response = await fetch('?action=create-table');
                const data = await response.json();
                if (data.success) {
                    showResult('✅ Tabla "test_data" creada exitosamente', 'success');
                } else {
                    showResult('❌ Error: ' + data.error, 'error');
                }
            } catch (error) {
                showResult('❌ Error: ' + error.message, 'error');
            }
        }

        async function insertData(event) {
            event.preventDefault();
            showLoading('Guardando datos...');
            
            const formData = new FormData();
            formData.append('action', 'insert');
            formData.append('nombre', document.getElementById('nombre').value);
            formData.append('descripcion', document.getElementById('descripcion').value);

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    showResult('✅ Registro insertado correctamente<br>ID: ' + data.id, 'success');
                    document.getElementById('nombre').value = '';
                    document.getElementById('descripcion').value = '';
                } else {
                    showResult('❌ Error: ' + data.error, 'error');
                }
            } catch (error) {
                showResult('❌ Error: ' + error.message, 'error');
            }
        }

        function showResult(message, type) {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = message;
            resultDiv.className = type;
            resultDiv.style.display = 'block';
        }

        function showLoading(message) {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = message;
            resultDiv.className = '';
            resultDiv.style.display = 'block';
        }
    </script>
</body>
</html>
<?php
function testConnection() {
    $dbconn = connectDB();
    
    if (!$dbconn) {
        echo json_encode(['success' => false, 'error' => 'No se pudo conectar a la base de datos: ' . pg_last_error()]);
        return;
    }
    
    $result = pg_query($dbconn, "SELECT version(), current_database(), current_user, current_timestamp");
    if (!$result) {
        echo json_encode(['success' => false, 'error' => pg_last_error($dbconn)]);
        return;
    }
    
    $row = pg_fetch_assoc($result);
    echo json_encode([
        'success' => true,
        'host' => $GLOBALS['db_host'],
        'version' => $row['version'],
        'database' => $row['current_database'],
        'user' => $row['current_user'],
        'timestamp' => $row['current_timestamp']
    ]);
    
    pg_close($dbconn);
}

function listTables() {
    $dbconn = connectDB();
    
    if (!$dbconn) {
        echo json_encode(['success' => false, 'error' => 'No se pudo conectar a la base de datos']);
        return;
    }
    
    $result = pg_query($dbconn, "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    if (!$result) {
        echo json_encode(['success' => false, 'error' => pg_last_error($dbconn)]);
        return;
    }
    
    $tables = [];
    while ($row = pg_fetch_assoc($result)) {
        $tables[] = $row['table_name'];
    }
    
    echo json_encode(['success' => true, 'tables' => $tables]);
    pg_close($dbconn);
}

function createTable() {
    $dbconn = connectDB();
    
    if (!$dbconn) {
        echo json_encode(['success' => false, 'error' => 'No se pudo conectar a la base de datos']);
        return;
    }
    
    $query = "CREATE TABLE IF NOT EXISTS test_data (
        id SERIAL PRIMARY KEY, 
        nombre VARCHAR(255) NOT NULL, 
        descripcion TEXT, 
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $result = pg_query($dbconn, $query);
    if (!$result) {
        echo json_encode(['success' => false, 'error' => pg_last_error($dbconn)]);
        return;
    }
    
    echo json_encode(['success' => true]);
    pg_close($dbconn);
}

function insertData() {
    $dbconn = connectDB();
    
    if (!$dbconn) {
        echo json_encode(['success' => false, 'error' => 'No se pudo conectar a la base de datos']);
        return;
    }
    
    $nombre = pg_escape_string($_POST['nombre']);
    $descripcion = pg_escape_string($_POST['descripcion']);
    
    $query = "INSERT INTO test_data (nombre, descripcion) VALUES ('$nombre', '$descripcion') RETURNING id";
    $result = pg_query($dbconn, $query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => pg_last_error($dbconn)]);
        return;
    }
    
    $row = pg_fetch_assoc($result);
    echo json_encode(['success' => true, 'id' => $row['id']]);
    pg_close($dbconn);
}
?>
