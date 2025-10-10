<?php
session_start();
include('conexion/conexion.php'); 

// ============================================
// PROCESAMIENTO DEL LOGIN
// ============================================

if(isset($_POST['acceder'])){
    $email = trim($_POST['username']);
    $password = trim($_POST['Password']);

    // Validación de campos vacíos
    if(empty($email) || empty($password)){
        $_SESSION['error'] = 'Por favor complete todos los campos';   
    } else {
        try {
            // CONSULTA OPTIMIZADA: Verificar usuario activo
            $query = "SELECT 
                        u.id, 
                        u.username, 
                        u.email, 
                        u.password_hash, 
                        u.nombres, 
                        u.apellidos, 
                        u.rol as rol_id,
                        tr.nombre as rol_nombre, 
                        tr.descripcion as rol_descripcion, 
                        u.permisos_asignados, 
                        u.estado, 
                        u.ultimo_acceso 
                      FROM usuarios u 
                      INNER JOIN tipo_rol tr ON u.rol = tr.id
                      WHERE u.email = :email AND u.estado = 'activo'
                      LIMIT 1";
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if($usuario){
                $password_valido = false;
                
                // Verificar contraseña hasheada
                if(password_verify($password, $usuario['password_hash'])){
                    $password_valido = true;
                }
                // Contraseñas temporales para pruebas (ELIMINAR EN PRODUCCIÓN)
                elseif($password === 'admin123' && $usuario['rol_nombre'] === 'admin') {
                    $password_valido = true;
                }
                elseif($password === 'operador123' && $usuario['rol_nombre'] === 'operador') {
                    $password_valido = true;
                }
                elseif($password === 'supervisor123' && $usuario['rol_nombre'] === 'consulta') {
                    $password_valido = true;
                }

                // ============================================
                // LOGIN EXITOSO
                // ============================================
                if($password_valido){
                    
                    // Actualizar último acceso
                    $queryUpdate = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id";
                    $stmtUpdate = $pdo->prepare($queryUpdate);
                    $stmtUpdate->bindParam(':id', $usuario['id'], PDO::PARAM_INT);
                    $stmtUpdate->execute();
                    
                    // Crear variables de sesión
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['username'] = $usuario['username'];
                    $_SESSION['email'] = $usuario['email'];
                    $_SESSION['nombres'] = $usuario['nombres'];
                    $_SESSION['apellidos'] = $usuario['apellidos'];
                    $_SESSION['nombre_completo'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                    
                    // Guardar información de rol
                    $_SESSION['rol_id'] = $usuario['rol_id'];
                    $_SESSION['rol'] = $usuario['rol_nombre'];
                    $_SESSION['rol_nombre'] = $usuario['rol_nombre'];
                    $_SESSION['rol_descripcion'] = $usuario['rol_descripcion'];
                    
                    // Decodificar permisos JSON
                    $_SESSION['permisos_asignados'] = json_decode($usuario['permisos_asignados'], true);
                    $_SESSION['login_time'] = date('Y-m-d H:i:s');

                    // Registrar login exitoso en logs
                    $queryLog = "INSERT INTO logs (usuario_id, accion, descripcion, ip_address, user_agent) 
                                 VALUES (:usuario_id, 'LOGIN', CONCAT('Usuario inició sesión exitosamente - Rol: ', :rol_nombre), :ip, :user_agent)";
                    
                    $stmtLog = $pdo->prepare($queryLog);
                    $stmtLog->bindParam(':usuario_id', $usuario['id'], PDO::PARAM_INT);
                    $stmtLog->bindParam(':rol_nombre', $usuario['rol_nombre'], PDO::PARAM_STR);
                    $stmtLog->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                    $stmtLog->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
                    $stmtLog->execute();

                    // Mensaje de bienvenida según rol
                    switch($usuario['rol_nombre']){
                        case 'admin':
                            $_SESSION['mensaje_bienvenida'] = '¡Bienvenido Administrador!';
                            break;
                        case 'fundacion':
                            $_SESSION['mensaje_bienvenida'] = '¡Bienvenido Fundador!';
                            break;
                        case 'comcercial':
                            $_SESSION['mensaje_bienvenida'] = '¡Bienvenido Comercial!';
                            break;
                        default:
                            $_SESSION['mensaje_bienvenida'] = '¡Bienvenido al sistema!';
                    }
                    
                    // Redireccionar al sistema
                    if ($usuario['rol_nombre'] === 'fundacion') {
                        header('Location: bot2.php');
                    } elseif ($usuario['rol_nombre'] === 'inmobiliaria') {
                        header('Location: bot5.php');
                    } else {
                        header('Location: bot.php');
                    }
                    exit();
                    
                } else {
                    // ============================================
                    // CONTRASEÑA INCORRECTA
                    // ============================================
                    $_SESSION['error'] = 'Contraseña incorrecta';

                    // Registrar intento fallido
                    $queryLogFail = "INSERT INTO logs (usuario_id, accion, descripcion, ip_address, user_agent) 
                                     VALUES (:usuario_id, 'LOGIN_FAILED', 'Intento de login fallido - contraseña incorrecta', :ip, :user_agent)";
                    
                    $stmtLogFail = $pdo->prepare($queryLogFail);
                    $stmtLogFail->bindParam(':usuario_id', $usuario['id'], PDO::PARAM_INT);
                    $stmtLogFail->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                    $stmtLogFail->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
                    $stmtLogFail->execute();
                }
                
            } else {
                // Usuario no encontrado o inactivo
                $_SESSION['error'] = 'Usuario no encontrado o inactivo';
            }
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error de conexión. Intente nuevamente.';
            error_log("Error de login: " . $e->getMessage());
        }
    }
}

// ============================================
// OBTENER CONFIGURACIÓN DE EMPRESA
// ============================================
try {
    $queryEmpresa = "SELECT imagen, pestaña FROM empresa LIMIT 1";
    $stmtEmpresa = $pdo->prepare($queryEmpresa);
    $stmtEmpresa->execute();
    $rowEmpresa = $stmtEmpresa->fetch(PDO::FETCH_ASSOC);
    
    $rutaImagen = isset($rowEmpresa['imagen']) ? htmlspecialchars($rowEmpresa['imagen']) : 'system/assets/img/logo.png';
    $pestaña = isset($rowEmpresa['pestaña']) ? htmlspecialchars($rowEmpresa['pestaña']) : 'Sistema - WE';
    
} catch (PDOException $e) {
    $rutaImagen = 'assets/images/we.png';
    $pestaña = 'Sistema - WE';
    error_log("Error al obtener configuración de empresa: " . $e->getMessage());
}

// ============================================
// OBTENER MENSAJES DE LOGIN
// ============================================
try {
    $queryLogin = "SELECT mensaje_bienvenida, suman_mensaje FROM login LIMIT 1";
    $stmtLogin = $pdo->prepare($queryLogin);
    $stmtLogin->execute();
    $mensajes = $stmtLogin->fetch(PDO::FETCH_ASSOC);
    
    $primer_mensaje = isset($mensajes['mensaje_bienvenida']) ? htmlspecialchars($mensajes['mensaje_bienvenida']) : 'Bienvenido al Sistema';
    $segundo_mensaje = isset($mensajes['suman_mensaje']) ? htmlspecialchars($mensajes['suman_mensaje']) : 'Ingrese sus credenciales';
    
} catch(PDOException $e){
    $primer_mensaje = 'Bienvenido al Sistema';
    $segundo_mensaje = 'Ingrese sus credenciales';
    error_log("Error al obtener mensajes de login: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo $rutaImagen; ?>" type="image/x-icon">
    <title><?php echo $pestaña; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            color: #1a1a1a;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 48px 40px;
            box-shadow: 
                0 10px 40px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.12),
                0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 32px;
            position: relative;
        }

        .logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
            border: 3px solid #667eea;
            padding: 8px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
            transition: all 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.35);
        }

        .title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 8px;
            text-align: center;
            color: #1a1a1a;
        }

        .subtitle {
            font-size: 15px;
            color: #666;
            margin-bottom: 36px;
            text-align: center;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            color: #1a1a1a;
            background: #ffffff;
            transition: all 0.2s ease;
            -webkit-appearance: none;
        }

        .form-control:hover {
            border-color: #d1d5db;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .btn {
            width: 100%;
            padding: 14px 16px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 
                0 4px 12px rgba(102, 126, 234, 0.3),
                0 0 0 0 rgba(102, 126, 234, 0);
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 8px 20px rgba(102, 126, 234, 0.4),
                0 0 0 0 rgba(102, 126, 234, 0);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 
                0 2px 8px rgba(102, 126, 234, 0.3),
                0 0 0 0 rgba(102, 126, 234, 0);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 24px;
            border: 2px solid;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #666;
        }

        .footer-text a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-bottom: 1px solid transparent;
        }

        .footer-text a:hover {
            color: #764ba2;
            border-bottom-color: #764ba2;
        }

        .btn-close {
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
            margin-left: auto;
            padding: 0;
            color: inherit;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .btn-close:hover {
            opacity: 1;
        }

        @media (max-width: 480px) {
            .card {
                padding: 32px 24px;
            }

            .logo-container {
                width: 80px;
                height: 80px;
            }

            .title {
                font-size: 22px;
            }

            .subtitle {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="logo-container">
                <img src="<?php echo $rutaImagen; ?>" alt="Logo Sistema" class="logo">
            </div>
            
            <h1 class="title"><?php echo $primer_mensaje; ?></h1>
            <p class="subtitle"><?php echo $segundo_mensaje; ?></p>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <span><?php echo htmlspecialchars($_SESSION['error']); ?></span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['mensaje_logout'])): ?>
                <div class="alert alert-success">
                    <span>✓</span>
                    <span><?php echo htmlspecialchars($_SESSION['mensaje_logout']); ?></span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                </div>
                <?php unset($_SESSION['mensaje_logout']); ?>
            <?php endif; ?>

            <form action="" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username" class="form-label">Correo electrónico</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        placeholder="ejemplo@correo.com" 
                        required 
                        autocomplete="email"
                        autofocus>
                </div>
                
                <div class="form-group">
                    <label for="Password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="Password" 
                        name="Password" 
                        placeholder="Ingrese su contraseña" 
                        required 
                        autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn" name="acceder">
                    Iniciar sesión
                </button>
            </form>
            
            <div class="footer-text">
                <span>¿Olvidaste tu contraseña? <a href="#">Recuperar</a></span>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus en el campo de email
        document.getElementById('username')?.focus();

        // Prevenir doble submit
        const form = document.getElementById('loginForm');
        let isSubmitting = false;
        
        form?.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            
            setTimeout(() => {
                isSubmitting = false;
            }, 3000);
        });
    </script>
</body>
</html>