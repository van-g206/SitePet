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

// Variáveis para armazenar mensagens
$success_msg = "";
$error_msg = "";

// Processar o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $idade = $_POST['idade'];
    $experiencia = $_POST['experiencia'];
    $nomePet = $_POST['nomePet'];
    $tipo = $_POST['tipo'];
    $idadePet = $_POST['idadePet'];
    $justificativa = $_POST['justificativa'];

    // Validação básica
    if (empty($nome) || empty($email) || empty($telefone) || empty($endereco) || empty($idade) || empty($nomePet) || empty($tipo) || empty($idadePet) || empty($justificativa)) {
        $error_msg = "Todos os campos são obrigatórios.";
    } else {
        // Inserir no banco de dados
        $sqlUsuario = "INSERT INTO usuarios (nome, email, telefone, endereco, idade, experiencia) VALUES ('$nome', '$email', '$telefone', '$endereco', '$idade', '$experiencia')";
        
        if (mysqli_query($conn, $sqlUsuario)) {
            $user_id = mysqli_insert_id($conn); // ID do usuário inserido
            $sqlAdocao = "INSERT INTO adocoes (usuario_id, nomePet, tipo, idadePet, justificativa) VALUES ('$user_id', '$nomePet', '$tipo', '$idadePet', '$justificativa')";
            if (mysqli_query($conn, $sqlAdocao)) {
                $success_msg = "Solicitação de adoção enviada com sucesso!";
            } else {
                $error_msg = "Erro ao inserir informações da adoção: " . mysqli_error($conn);
            }
        } else {
            $error_msg = "Erro ao inserir informações do adotante: " . mysqli_error($conn);
        }
    }
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
                <li><a href="../perfil de usuário/perfil.html"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="../forms/formpet.html"><i class="fas fa-paw"></i> Cadastro de Pet</a></li>
                <li><a id="logout" href="../index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="box">
            <h2>Formulário de Adoção de Pets</h2>

            <?php if ($success_msg): ?>
                <div class="success"><?php echo $success_msg; ?></div>
            <?php elseif ($error_msg): ?>
                <div class="error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

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
</body>
</html>
