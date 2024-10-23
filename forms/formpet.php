<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../forms/forms.php");
    exit;
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root"; // Substitua pelo nome de usuário do banco de dados
$password = ""; // Substitua pela senha do banco de dados
$dbname = "adoc_ani"; // Nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Variáveis para mensagem Toastr
$toastr_message = "";
$toastr_type = "";

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar o ID do usuário logado
    $usuario_id = $_SESSION['usuario_id'];

    // Recuperar e sanitizar dados do formulário
    $nome = trim($_POST['nome']);
    $tipo = trim($_POST['type']);
    $raca = trim($_POST['raca']);
    $cor = trim($_POST['cor']);
    $idade = trim($_POST['idade']);
    $sexo = trim($_POST['sexo']);
    $vacinado = trim($_POST['vacinado']);

    // Validar dados do formulário
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "Nome é obrigatório.";
    }

    if (empty($tipo)) {
        $erros[] = "Tipo é obrigatório.";
    }

    if (empty($raca)) {
        $erros[] = "Raça é obrigatória.";
    }

    if (empty($cor)) {
        $erros[] = "Cor é obrigatória.";
    }

    if (empty($idade) || !is_numeric($idade)) {
        $erros[] = "Idade deve ser um número.";
    }

    if (empty($sexo)) {
        $erros[] = "Sexo é obrigatório.";
    }

    if (empty($vacinado)) {
        $erros[] = "Status de vacinação é obrigatório.";
    }

    // Se não houver erros, inserir os dados na tabela pets
    if (empty($erros)) {
        // Preparar a consulta para inserir os dados na tabela pets
        $sql = "INSERT INTO pets (usuario_id, nome, tipo, raca, cor, idade, sexo, vacinado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("issssiss", $usuario_id, $nome, $tipo, $raca, $cor, $idade, $sexo, $vacinado);

            if ($stmt->execute()) {
                $toastr_message = "Pet cadastrado com sucesso!";
                $toastr_type = "success"; // Tipo de notificação: sucesso
            } else {
                $toastr_message = "Erro ao cadastrar pet: " . htmlspecialchars($stmt->error);
                $toastr_type = "error"; // Tipo de notificação: erro
            }

            // Fechar o statement
            $stmt->close();
        } else {
            $toastr_message = "Erro ao preparar a consulta: " . htmlspecialchars($conn->error);
            $toastr_type = "error"; // Tipo de notificação: erro
        }
    } else {
        // Exibir erros de validação
        $toastr_message = implode("<br>", $erros);
        $toastr_type = "error"; // Tipo de notificação: erro
    }

    // Fechar a conexão
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Pet</title>
    <link rel="stylesheet" href="formpet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            background-color: #e4c7c7;
        }
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
        <form class="new-form" method="POST" action="">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="type">Tipo:</label>
            <select id="type" name="type" required>
                <option value="" selected disabled>Selecione</option>
                <option value="Cachorro">Cachorro</option>
                <option value="Gato">Gato</option>
            </select>

            <label for="raca">Raça:</label>
            <input type="text" id="raca" name="raca" required>

            <label for="cor">Cor:</label>
            <input type="text" id="cor" name="cor" required>

            <label for="idade">Idade:</label>
            <input type="text" id="idade" name="idade" required>

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="" selected disabled>Selecione</option>
                <option value="masc">Macho</option>
                <option value="fem">Fêmea</option>
                <option value="her">Hermafrodita</option>
            </select>

            <label for="vacinado">É vacinado?</label>
            <select id="vacinado" name="vacinado" required>
                <option value="" selected disabled>Selecione</option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
            </select>

            <button type="submit">Cadastrar</button>
        </form>
    </main>
    <script src="formpet.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Checar se há mensagem de toastr e exibi-la
            <?php
            if (!empty($toastr_message)) {
                echo "toastr." . $toastr_type . "('" . addslashes($toastr_message) . "');";
            }
            ?>
        });
    </script>
</body>
</html>
