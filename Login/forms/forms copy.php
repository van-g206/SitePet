<!doctype html>
<html lang="pt-br">
<head>
<title>forms</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/
    css/bootstrap.min.css">
    <link rel="stylesheet" href="forms.css">
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
                        
                        
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" />
                        <label for="reg-log"></label>

                        <div class="card-3d-wrap mx-auto">
                          
                            <div class="card-3d-wrapper">
                                <div class="card-front">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h2 class="mb-4 pb-3">Log-In</h2>
                                            <div class="form-group">
                                                <input type="email" class="form-style" placeholder="Email">
                                                <span class="error-message" id="login-email-error"></span>
                                                <i class="input-icon uil uil-at"></i>
                                            </div>

                                            <div class="form-group mt-2"><input type="password" class="form-style"
                                                    placeholder="Senha">
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                               
                                            </div>
                                            <a type="submit" name="login" class="btn mt-4">Login</a>
                                            <p class="mb-0 mt-4 text-center"><a href="../recovery/recovery.html" class="link">Esqueceu a senha?</a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-back">
                                    <div class="center-wrap">
                                        <div class="section text-center">

                                            <h2 class="mb-3 pb-3">Cadastrar</h2>
                                            <div class="form-group">
                                                <input type="text" class="form-style" placeholder="Nome">
                                                <i class="input-icon uil uil-user"></i>
                                            </div>
        
                                            <div class="form-group mt-2">
                                                <input type="tel" class="form-style" placeholder="Número de telefone" pattern="[0-9]*" inputmode="numeric">
                                                <i class="input-icon uil uil-phone"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="email" class="form-style" placeholder="Email">
                                                <i class="input-icon uil uil-at"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="password" class="form-style" placeholder="Senha">
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            </div>
                                            <a href="" class="btn mt-4">Registrar</a>
                                           
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
    <?php
$lemail_err = $lsenha_err = $cnome_err = $ctel_err = $cemail_err = $csenha_err = "";
$lemail = $lsenha = $cnome = $ctel = $cemail = $csenha = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validação do Login
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

    // Validação do Cadastro
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
        // Usando VARCHAR, então não há necessidade de conversão para double, mas validamos o formato
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
        include_once("conecta.php");

        // Preparar a senha
        $hashed_senha = password_hash($lsenha, PASSWORD_DEFAULT);
        $hashed_csenha = password_hash($csenha, PASSWORD_DEFAULT);

        // Usar prepared statements para inserção
        // Primeiro, inserir na tabela de usuários
        $stmt = $conn->prepare("INSERT INTO usuarios (email, nome) VALUES (?, ?)");
        $stmt->bind_param("ss", $cemail, $cnome);

        if ($stmt->execute()) {
            // Obter o ID do usuário recém-criado
            $usuario_id = $stmt->insert_id;
            $stmt->close();

            // Inserir na tabela de login
            $stmt1 = $conn->prepare("INSERT INTO loginn (usuario_id, lsenha) VALUES (?, ?)");
            $stmt1->bind_param("is", $usuario_id, $hashed_senha);

            if ($stmt1->execute()) {
                echo "<p>Log-In Realizado com Sucesso.</p>";
            } else {
                echo "Erro ao realizar o log-in: " . $conn->error;
            }
            $stmt1->close();

            // Inserir na tabela de cadastro
            $stmt2 = $conn->prepare("INSERT INTO cadastro (usuario_id, csenha, ctel) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $usuario_id, $hashed_csenha, $ctel);

            if ($stmt2->execute()) {
                echo "<p>Cadastro Realizado com Sucesso.</p>";
            } else {
                echo "Erro ao realizar o cadastro: " . $conn->error;
            }
            $stmt2->close();
        } else {
            echo "Erro ao inserir na tabela de usuários: " . $conn->error;
        }
    }
}
?>
</body>

</html>