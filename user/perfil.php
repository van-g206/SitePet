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

// Recuperar o ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Buscar informações do usuário na tabela usuarios
$sql = "SELECT nome, email FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($nome, $email);
$stmt->fetch();
$stmt->close();

// Buscar telefone na tabela cadastro
$sql = "SELECT ctel FROM cadastro WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($telefone);
$stmt->fetch();
$stmt->close();

// Fechar a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <!-- <link rel="stylesheet" href="perfil.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        Estilos básicos
        body {
            background-color: #c4b1b1;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            padding: 10px 0;
            color: white;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hamburger-menu {
            cursor: pointer;
        }

        .hamburger-menu span {
            display: block;
            width: 25px;
            height: 3px;
            margin: 5px;
            background-color: white;
        }

        .nav-menu {
            list-style-type: none;
            display: flex;
            gap: 15px;
            margin-right: 20px;
        }

        .nav-menu li {
            margin: 0;
        }

        .nav-menu li a {
            color: white;
            text-decoration: none;
        }

        /* Estilos principais */
        main {
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .new-form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            position: relative;
        }

        .new-form h3 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .new-form label {
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
        }

        .new-form input, .new-form select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .new-form input[disabled], .new-form select[disabled] {
            background-color: #f0f0f0;
        }

        .profile-image {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-image img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #ccc;
        }

        .patas1 {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 100px;
            opacity: 0.2;
        }

        .patas1 img {
            width: 100%;
        }
    </style>
</head>
<body>

    <header>
        <nav>
            <ul class="nav-menu">
                <li><a href="../page1/page1.html"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="../galeria/galeria.html"><i class="fas fa-camera"></i> Galeria</a></li>
                <li><a href="../user/perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="../forms/formAdocao.php"><i class="fas fa-paw"></i> Cadastro de Pet</a></li>
                <li><a id="logout" href="../index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <form class="new-form">
            <div class="patas1">
                <img src="../img/png/patas.png" alt="Patas decorativas">
            </div>
            <div class="profile-image">
                <img src="https://th.bing.com/th/id/R.e5c95af14dfd03878d0bcc7344230389?rik=H5IqelV0wvfpfw&riu=http%3a%2f%2fgetdrawings.com%2fimg%2fman-silhouette-profile-17.jpg&ehk=pu%2fT%2bDN0bWum6YpgucHvjGlETMDdmmkpf6X7McLED4I%3d&risl=&pid=ImgRaw&r=0" alt="Imagem do Perfil">
            </div>
            <h3>Meu Perfil</h3>

            <label for="email">E-mail:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" disabled>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" disabled>

            <label for="tel">Telefone:</label>
            <input type="text" id="tel" name="tel" value="<?php echo htmlspecialchars($telefone); ?>" disabled>

            <label for="dtnsc">Data de Nascimento:</label>
            <input type="text" id="dtnsc" name="dtnsc" value="Não disponível" disabled>

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" value="Não disponível" disabled>

            <label for="preferences">Preferências:</label>
            <select id="preferences" name="preferences">
                <option value="dog">Cachorro</option>
                <option value="cat">Gato</option>
            </select>
        </form>
    </main>

</body>
</html>
