<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root"; // Altere para seu nome de usuário do banco de dados
$password = ""; // Altere para sua senha do banco de dados
$dbname = "adoc_ani";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Processar o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $idade = intval($_POST['idade']);
    $experiencia = trim($_POST['experiencia']);
    $nomePet = trim($_POST['nomePet']);
    $tipo = trim($_POST['tipo']);
    $idadePet = trim($_POST['idadePet']);
    $justificativa = trim($_POST['justificativa']);

    // Validação básica
    $error_msg = "";
    if (empty($nome) || empty($email) || empty($telefone) || empty($endereco) || empty($idade) || empty($nomePet) || empty($tipo) || empty($idadePet) || empty($justificativa)) {
        $error_msg = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Endereço de e-mail inválido.";
    } elseif (!preg_match("/^\+?[0-9]{10,15}$/", $telefone)) { // Verifica se o telefone é válido
        $error_msg = "Telefone inválido. Deve ter entre 10 a 15 dígitos.";
    } elseif ($idade < 18 || $idade > 120) {
        $error_msg = "A idade deve estar entre 18 e 120 anos.";
    }

    // Aqui armazenaremos a mensagem para exibição posterior
    $toastr_message = "";
    $toastr_type = "";

    if ($error_msg) {
        $toastr_message = $error_msg;
        $toastr_type = "error"; // Tipo de notificação: erro
    } else {
        // Escapar caracteres especiais
        $nome = mysqli_real_escape_string($conn, $nome);
        $email = mysqli_real_escape_string($conn, $email);
        $telefone = mysqli_real_escape_string($conn, $telefone);
        $endereco = mysqli_real_escape_string($conn, $endereco);
        $experiencia = mysqli_real_escape_string($conn, $experiencia);
        $nomePet = mysqli_real_escape_string($conn, $nomePet);
        $tipo = mysqli_real_escape_string($conn, $tipo);
        $idadePet = mysqli_real_escape_string($conn, $idadePet);
        $justificativa = mysqli_real_escape_string($conn, $justificativa);

        // Inserir no banco de dados usando declarações preparadas
        $stmtUsuario = $conn->prepare("INSERT INTO usuarios (nome, email, telefone, endereco, idade, experiencia) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtUsuario->bind_param("ssssss", $nome, $email, $telefone, $endereco, $idade, $experiencia);
        
        if ($stmtUsuario->execute()) {
            $user_id = $stmtUsuario->insert_id; // ID do usuário inserido

            // Inserir informações da adoção
            $stmtAdocao = $conn->prepare("INSERT INTO adocoes (usuario_id, nomePet, tipo, idadePet, justificativa) VALUES (?, ?, ?, ?, ?)");
            $stmtAdocao->bind_param("issss", $user_id, $nomePet, $tipo, $idadePet, $justificativa);
            
            if ($stmtAdocao->execute()) {
                $toastr_message = "Solicitação de adoção enviada com sucesso!";
                $toastr_type = "success"; // Tipo de notificação: sucesso
            } else {
                $toastr_message = "Erro ao inserir informações da adoção: " . $stmtAdocao->error;
                $toastr_type = "error"; // Tipo de notificação: erro
            }
        } else {
            $toastr_message = "Erro ao inserir informações do adotante: " . $stmtUsuario->error;
            $toastr_type = "error"; // Tipo de notificação: erro
        }
        
        // Fechar as declarações
        $stmtUsuario->close();
        $stmtAdocao->close();
    }

    // Armazenar a mensagem de Toastr na sessão
    session_start();
    $_SESSION['toastr_message'] = $toastr_message;
    $_SESSION['toastr_type'] = $toastr_type;
    header("Location: " . $_SERVER['PHP_SELF']); // Redireciona para a mesma página
    exit();
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Pet</title>
    <link rel="stylesheet" href="formAdocao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            background-color: #e4c7c7;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: #bb6809;
        }
        /* Estilos adicionais aqui */
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-menu">
                <li><a href="../page1/page1.html"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="../galeria/galeria.html"><i class="fas fa-camera"></i> Galeria</a></li>
                <li><a href="../user/perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="../forms/formpet.php"><i class="fas fa-paw"></i> Cadastro de Pet</a></li>
                <li><a id="logout" href="../index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="box">
            <h2>Formulário de Adoção de Pets</h2>

            <form action="" method="POST">
                <!-- Informações do Adotante -->
                <fieldset>
                    <legend>Informações do Adotante</legend>
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" required>
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                    <label for="telefone">Telefone:</label>
                    <input type="tel" id="telefone" name="telefone" required>
                    <label for="endereco">Endereço Completo:</label>
                    <textarea id="endereco" name="endereco" required></textarea>
                    <button type="button" onclick="openMap()">Ver no Mapa</button>
                    <label for="idade">Idade:</label>
                    <input type="number" id="idade" name="idade" required min="18" max="120">
                    <label for="experiencia">Você já teve pets antes?</label>
                    <select id="experiencia" name="experiencia">
                        <option value="sim">Sim</option>
                        <option value="nao">Não</option>
                    </select>
                </fieldset>

                <!-- Informações do Pet -->
                <fieldset>
                    <legend>Informações do Pet</legend>
                    <label for="nomePet">Nome do Pet:</label>
                    <input type="text" id="nomePet" name="nomePet" required>
                    <label for="tipo">Tipo de Pet:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="" disabled selected>Selecione</option>
                        <option value="cachorro">Cachorro</option>
                        <option value="gato">Gato</option>
                    </select>
                    <label for="idadePet">Idade do Pet:</label>
                    <input type="text" id="idadePet" name="idadePet" required>
                    <label for="justificativa">Justificativa de adoção:</label>
                    <input type="text" id="justificativa" name="justificativa" required>
                </fieldset>

                <!-- Botão de Envio -->
                <button type="submit">Enviar Solicitação de Adoção</button>
            </form>
        </div>
    </main>

    <script src="formAdocao.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Checar se há mensagem de toastr na sessão e exibi-la
            <?php
            session_start();
            if (isset($_SESSION['toastr_message'])) {
                echo "toastr." . $_SESSION['toastr_type'] . "('{$_SESSION['toastr_message']}');";
                unset($_SESSION['toastr_message']);
                unset($_SESSION['toastr_type']);
            }
            ?>
        });
    </script>
</body>
</html>
