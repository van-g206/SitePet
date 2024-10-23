<?php
// Iniciar a sessão
session_start();

// Conexão com o banco de dados
$servername = "localhost";
$username = "root"; // Substitua pelo seu nome de usuário do MySQL
$password = ""; // Substitua pela sua senha do MySQL
$dbname = "adoc_ani";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Inicialização de variáveis
$lemail_err = $lsenha_err = $cnome_err = $ctel_err = $cemail_err = $csenha_err = "";
$lemail = $lsenha = $cnome = $ctel = $cemail = $csenha = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Login
    if (isset($_POST["lemail"]) && isset($_POST["lsenha"])) {
        if (empty($_POST["lemail"])) {
            $lemail_err = "Email é obrigatório";
        } elseif (!filter_var($_POST["lemail"], FILTER_VALIDATE_EMAIL)) {
            $lemail_err = "Formato de email inválido";
        } else {
            $lemail = trim($_POST["lemail"]); // Limpar espaços em branco
        }

        if (empty($_POST["lsenha"])) {
            $lsenha_err = "Senha é obrigatória";
        } else {
            $lsenha = $_POST["lsenha"];
        }

        // Verificar as credenciais
        if (empty($lemail_err) && empty($lsenha_err)) {
            // Usando JOIN para verificar credenciais
            $stmt = $conn->prepare("SELECT u.id, l.lsenha FROM usuarios u JOIN loginn l ON u.id = l.usuario_id WHERE u.email = ?");
            $stmt->bind_param("s", $lemail);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($usuario_id, $hashed_senha);
                $stmt->fetch();

                if (password_verify($lsenha, $hashed_senha)) {
                    $_SESSION['usuario_id'] = $usuario_id;
                    $_SESSION['lemail'] = $lemail;
                    $_SESSION['toastr_message'] = "Login realizado com sucesso!";
                    $_SESSION['toastr_type'] = "success"; // tipo da notificação
                    header("Location: ../page1/page1.html");
                    exit;
                } else {
                    $lsenha_err = "Senha incorreta!";
                }
            } else {
                $lemail_err = "Email não encontrado!";
            }
            $stmt->close();
        }

        // Exibir erro de login, se houver
        if (!empty($lemail_err)) {
            $_SESSION['toastr_message'] = $lemail_err;
            $_SESSION['toastr_type'] = "error"; // tipo da notificação
        }
        if (!empty($lsenha_err)) {
            $_SESSION['toastr_message'] = $lsenha_err;
            $_SESSION['toastr_type'] = "error"; // tipo da notificação
        }
    }

    // Cadastro
    if (isset($_POST["cnome"]) && isset($_POST["ctel"]) && isset($_POST["cemail"]) && isset($_POST["csenha"])) {
        if (empty($_POST["cnome"])) {
            $cnome_err = "Nome é obrigatório";
        } else {
            $cnome = trim($_POST["cnome"]); // Limpar espaços em branco
        }

        if (empty($_POST["ctel"])) {
            $ctel_err = "Telefone é obrigatório";
        } elseif (!preg_match("/^\+?[0-9]{10,15}$/", $_POST["ctel"])) { // Validar formato do telefone
            $ctel_err = "O telefone deve conter entre 10 a 15 dígitos.";
        } else {
            $ctel = $_POST["ctel"];
        }

        if (empty($_POST["cemail"])) {
            $cemail_err = "Email é obrigatório";
        } elseif (!filter_var($_POST["cemail"], FILTER_VALIDATE_EMAIL)) {
            $cemail_err = "Formato de email inválido";
        } else {
            $cemail = trim($_POST["cemail"]); // Limpar espaços em branco
        }

        if (empty($_POST["csenha"])) {
            $csenha_err = "Senha é obrigatória";
        } elseif (strlen($_POST["csenha"]) < 6) { // Verificar se a senha tem pelo menos 6 caracteres
            $csenha_err = "A senha deve ter pelo menos 6 caracteres.";
        } else {
            $csenha = $_POST["csenha"];
        }

        // Se não houver erros, proceder com a inserção no banco
        if (empty($lemail_err) && empty($lsenha_err) && empty($cnome_err) && empty($ctel_err) && empty($cemail_err) && empty($csenha_err)) {
            $hashed_senha = password_hash($csenha, PASSWORD_DEFAULT);

            // Inserir usuário
            $stmt = $conn->prepare("INSERT INTO usuarios (email, nome) VALUES (?, ?)");
            $stmt->bind_param("ss", $cemail, $cnome);

            if ($stmt->execute()) {
                $usuario_id = $stmt->insert_id;
                $stmt->close();

                // Inserir login
                $stmt1 = $conn->prepare("INSERT INTO loginn (usuario_id, lsenha) VALUES (?, ?)");
                $stmt1->bind_param("is", $usuario_id, $hashed_senha);

                if ($stmt1->execute()) {
                    $stmt1->close();
                    
                    // Inserir cadastro
                    $stmt2 = $conn->prepare("INSERT INTO cadastro (usuario_id, csenha, ctel) VALUES (?, ?, ?)");
                    $stmt2->bind_param("iss", $usuario_id, $hashed_senha, $ctel);

                    if ($stmt2->execute()) {
                        $_SESSION['toastr_message'] = "Cadastro realizado com sucesso!";
                        $_SESSION['toastr_type'] = "success"; // tipo da notificação
                    } else {
                        $_SESSION['toastr_message'] = "Erro ao realizar o cadastro: " . $conn->error;
                        $_SESSION['toastr_type'] = "error"; // tipo da notificação
                    }
                    $stmt2->close();
                } else {
                    $_SESSION['toastr_message'] = "Erro ao realizar o log-in: " . $conn->error;
                    $_SESSION['toastr_type'] = "error"; // tipo da notificação
                }
            } else {
                $_SESSION['toastr_message'] = "Erro ao inserir na tabela de usuários: " . $conn->error;
                $_SESSION['toastr_type'] = "error"; // tipo da notificação
            }
        }

        // Exibir erros de cadastro, se houver
        if (!empty($cnome_err)) {
            $_SESSION['toastr_message'] = $cnome_err;
            $_SESSION['toastr_type'] = "error"; // tipo da notificação
        }
        if (!empty($ctel_err)) {
            $_SESSION['toastr_message'] = $ctel_err;
            $_SESSION['toastr_type'] = "error"; // tipo da notificação
        }
        if (!empty($cemail_err)) {
            $_SESSION['toastr_message'] = $cemail_err;
            $_SESSION['toastr_type'] = "error"; // tipo da notificação
        }
        if (!empty($csenha_err)) {
            $_SESSION['toastr_message'] = $csenha_err;
            $_SESSION['toastr_type'] = "error"; // tipo da notificação
        }
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
<title>Forms</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="forms.css">

<!-- Include Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body>
<div class="patas1">
        <img src="../img//png/patas.png"></img>
    </div>
    <div class="patas2">
        <img src="../img//png/patas.png"></img>
    </div>
   
   
    <div class="section">
        <div class="container">
            <div class="row full-height justify-content-center">
                <div class="col-12 text-center align-self-center py-5">
                    <div class="section pb-5 pt-5 pt-sm-2 text-center">
                    <h3 class="mb-0 pb-3"><span>Login </span> <span>Cadastrar</span></h3>
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log"/>
                        <label for="reg-log"></label>

                        <!---->

                        <div class="card-3d-wrap mx-auto">
                            <div class="card-3d-wrapper">
                                <div class="card-front">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h2 class="mb-4 pb-3">Log-In</h2>
                                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                                <div class="form-group">
                                                    <input type="email" class="form-style" name="lemail" placeholder="Email" value="<?php echo isset($lemail) ? htmlspecialchars($lemail) : ''; ?>" required>
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>

                                                    <!---->

                                                <div class="form-group">
                                                    <input type="password" class="form-style" name="lsenha" placeholder="Senha" required>
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                                </div>
                                                <button type="submit" class="btn">Entrar</button>
                                            </form>
                                            <p class="mb-0 mt-4 text-center"><a href="../recovery/recovery.html" class="link">Esqueceu a senha?</a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-back">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h2 class="mb-3 pb-3">Cadastrar</h2>
                                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                                <div class="form-group">
                                                    <input type="text" class="form-style" name="cnome" placeholder="Nome" value="<?php echo isset($cnome) ? htmlspecialchars($cnome) : ''; ?>" required>
                                                    <i class="input-icon uil uil-user"></i>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" class="form-style" name="ctel" placeholder="Telefone" value="<?php echo isset($ctel) ? htmlspecialchars($ctel) : ''; ?>" required>
                                                    <i class="input-icon uil uil-phone"></i>
                                                </div>
                                                <div class="form-group">
                                                    <input type="email" class="form-style" name="cemail" placeholder="Email" value="<?php echo isset($cemail) ? htmlspecialchars($cemail) : ''; ?>" required>
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>
                                                <div class="form-group">
                                                    <input type="password" class="form-style" name="csenha" placeholder="Senha" required>
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                                </div>
                                                <button type="submit" class="btn">Cadastrar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Script Toastr -->
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['toastr_message'])): ?>
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true
                };
                var type = "<?php echo $_SESSION['toastr_type']; ?>";
                var message = "<?php echo $_SESSION['toastr_message']; ?>";

                switch(type) {
                    case 'success':
                        toastr.success(message);
                        break;
                    case 'error':
                        toastr.error(message);
                        break;
                    default:
                        toastr.info(message);
                }

                <?php 
                    // Limpar a mensagem da sessão após exibir
                    unset($_SESSION['toastr_message']);
                    unset($_SESSION['toastr_type']);
                ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>