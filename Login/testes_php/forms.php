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
            $lemail = $_POST["lemail"];
        }

        if (empty($_POST["lsenha"])) {
            $lsenha_err = "Senha é obrigatória";
        } else {
            $lsenha = $_POST["lsenha"];
        }

        // Verificar as credenciais
        if (empty($lemail_err) && empty($lsenha_err)) {
            $stmt = $conn->prepare("SELECT id, lsenha FROM loginn WHERE email = ?");
            $stmt->bind_param("s", $lemail);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($usuario_id, $hashed_senha);
                $stmt->fetch();

                if (password_verify($lsenha, $hashed_senha)) {
                    $_SESSION['usuario_id'] = $usuario_id;
                    $_SESSION['lemail'] = $lemail;
                    header("Location: pagina_inicial.php");
                    exit;
                } else {
                    $lsenha_err = "Senha incorreta!";
                }
            } else {
                $lemail_err = "Email não encontrado!";
            }
            $stmt->close();
        }
    }

    // Cadastro
    if (isset($_POST["cnome"]) && isset($_POST["ctel"]) && isset($_POST["cemail"]) && isset($_POST["csenha"])) {
        if (empty($_POST["cnome"])) {
            $cnome_err = "Nome é obrigatório";
        } else {
            $cnome = $_POST["cnome"];
        }

        if (empty($_POST["ctel"])) {
            $ctel_err = "Telefone é obrigatório";
        } elseif (!is_numeric($_POST["ctel"])) {
            $ctel_err = "O telefone deve ser numérico";
        } else {
            $ctel = $_POST["ctel"];
        }

        if (empty($_POST["cemail"])) {
            $cemail_err = "Email é obrigatório";
        } elseif (!filter_var($_POST["cemail"], FILTER_VALIDATE_EMAIL)) {
            $cemail_err = "Formato de email inválido";
        } else {
            $cemail = $_POST["cemail"];
        }

        if (empty($_POST["csenha"])) {
            $csenha_err = "Senha é obrigatória";
        } else {
            $csenha = $_POST["csenha"];
        }

        // Se não houver erros, proceder com a inserção no banco
        if (empty($lemail_err) && empty($lsenha_err) && empty($cnome_err) && empty($ctel_err) && empty($cemail_err) && empty($csenha_err)) {
            $hashed_senha = password_hash($csenha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (email, nome) VALUES (?, ?)");
            $stmt->bind_param("ss", $cemail, $cnome);

            if ($stmt->execute()) {
                $usuario_id = $stmt->insert_id;
                $stmt->close();

                $stmt1 = $conn->prepare("INSERT INTO loginn (usuario_id, lsenha) VALUES (?, ?)");
                $stmt1->bind_param("is", $usuario_id, $hashed_senha);

                if ($stmt1->execute()) {
                    echo "<p>Cadastro realizado com sucesso!</p>";
                } else {
                    echo "Erro ao realizar o log-in: " . $conn->error;
                }
                $stmt1->close();

                $stmt2 = $conn->prepare("INSERT INTO cadastro (usuario_id, csenha, ctel) VALUES (?, ?, ?)");
                $stmt2->bind_param("iss", $usuario_id, $hashed_senha, $ctel);

                if ($stmt2->execute()) {
                    echo "<p>Cadastro realizado com sucesso!</p>";
                } else {
                    echo "Erro ao realizar o cadastro: " . $conn->error;
                }
                $stmt2->close();
            } else {
                echo "Erro ao inserir na tabela de usuários: " . $conn->error;
            }
        }
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <title>Webleb</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="forms.css">
</head>

<body>
    <div class="patas1">
        <img src="img/png/patas.png" alt="Patas">
    </div>
    <div class="patas2">
        <img src="img/png/patas.png" alt="Patas">
    </div>

    <div class="section">
        <div class="container">
            <div class="row full-height justify-content-center">
                <div class="col-12 text-center align-self-center py-5">
                    <div class="section pb-5 pt-5 pt-sm-2 text-center">
                        <h6 class="mb-0 pb-3"><span>Login </span><span>Cadastrar</span></h6>
                        
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log"/>
                        <label for="reg-log"></label>

                        <div class="card-3d-wrap mx-auto">
                            <div class="card-3d-wrapper">
                                <div class="card-front">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-4 pb-3">Log-In</h4>
                                            <form method="post" action="">
                                                <div class="form-group">
                                                    <input type="email" class="form-style" name="lemail" placeholder="Email" required>
                                                    <span class="error-message"><?php echo $lemail_err; ?></span>
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>

                                                <div class="form-group mt-2">
                                                    <input type="password" class="form-style" name="lsenha" placeholder="Senha" required>
                                                    <span class="error-message"><?php echo $lsenha_err; ?></span>
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                                </div>
                                                <button type="submit" class="btn mt-4">Login</button>
                                                <p class="mb-0 mt-4 text-center"><a href="recovery.html" class="link">Esqueceu a senha?</a></p>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-back">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-3 pb-3">Cadastrar</h4>
                                            <form method="post" action="">
                                                <div class="form-group">
                                                    <input type="text" class="form-style" name="cnome" placeholder="Nome" required>
                                                    <span class="error-message"><?php echo $cnome_err; ?></span>
                                                    <i class="input-icon uil uil-user"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="tel" class="form-style" name="ctel" placeholder="Número de telefone" pattern="[0-9]*" inputmode="numeric" required>
                                                    <span class="error-message"><?php echo $ctel_err; ?></span>
                                                    <i class="input-icon uil uil-phone"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="email" class="form-style" name="cemail" placeholder="Email" required>
                                                    <span class="error-message"><?php echo $cemail_err; ?></span>
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="password" class="form-style" name="csenha" placeholder="Senha" required>
                                                    <span class="error-message"><?php echo $csenha_err; ?></span>
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                                </div>
                                                <button type="submit" class="btn mt-4">Registrar</button> 
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
</body>
</html>
