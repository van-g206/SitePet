<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Pet</title>
    <link rel="stylesheet" href="formpet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body{
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
                <li><a href="/page1/page1.html"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="/galeria/galeria.html"><i class="fas fa-camera"></i> Galeria</a></li>
                <li><a href="/perfil-de-usuario/perfil.html"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="/forms/formpet.html"><i class="fas fa-paw"></i> Cadastro de Pet</a></li>
                <li><a id="logout" href="/index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
    <div class="box">
            <h2>Cadastro de Animal</h2>
            <form class="new-form" method="post">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
                <span class="error-message"><?php echo $nome_err; ?></span>

                <label for="type">Tipo:</label>
                <select id="type" name="type" required>
                  <option value="" selected disabled>Selecione</option>
                  <option value="masc">Cachorro</option>
                  <option value="fem">Gato</option>
                </select>
                <span class="error-message"><?php echo $type_err; ?></span>
                
                <label for="raca">Raça:</label>
                <input type="text" id="raca" name="raca" required>
                <span class="error-message"><?php echo $raca_err; ?></span>
                
                <label for="cor">Cor:</label>
                <input type="text" id="cor" name="cor" required>
                <span class="error-message"><?php echo $cor_err; ?></span>
                
                <label for="idade">Idade:</label>
                <input type="text" id="idade" name="idade" required>
                <span class="error-message"><?php echo $idade_err; ?></span>
                
                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="" selected disabled>Selecione</option>
                    <option value="masc">Macho</option>
                    <option value="fem">Fêmea</option>
                    <option value="her">Hermafrodita</option>
                </select>
                <span class="error-message"><?php echo $sexo_err; ?></span>
                
                <label for="vacinado">É vacinado?</label>
                <select id="vacinado" name="vacinado" required>
                    <option value="" selected disabled>Selecione</option>
                    <option value="Sim">Sim</option>
                    <option value="Não">Não</option>
                </select>
                <span class="error-message"><?php echo $vacinado_err; ?></span>
                
                <button type="submit">Cadastrar</button>
            </form>
        </div>
    </main>
    <?php
    // Inclui o arquivo de conexão com o banco de dados
    include_once("conecta.php");
    
    // Inicializa variáveis de erro e entrada
    $nome_err = $type_err = $raca_err = $cor_err = $idade_err = $sexo_err = $vacinado_err = "";
    $nome = $type = $raca = $cor = $idade = $sexo = $vacinado = "";
    
    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        // Validação do campo Nome
        if (empty(trim($_POST["nome"]))) {
            $nome_err = "Nome é obrigatório";
        } else {
            $nome = trim($_POST["nome"]);
        }
    
        // Validação do campo Tipo
        if (empty(trim($_POST["type"]))) {
            $type_err = "Tipo é obrigatório";
        } else {
            $type = trim($_POST["type"]);
        }
    
        // Validação do campo Raça
        if (empty(trim($_POST["raca"]))) {
            $raca_err = "Raça é obrigatória";
        } else {
            $raca = trim($_POST["raca"]);
        }
    
        // Validação do campo Cor
        if (empty(trim($_POST["cor"]))) {
            $cor_err = "Cor é obrigatória";
        } else {
            $cor = trim($_POST["cor"]);
        }
    
        // Validação do campo Idade
        if (empty(trim($_POST["idade"]))) {
            $idade_err = "Idade é obrigatória";
        } else if (!ctype_digit($_POST["idade"])) {
            $idade_err = "A idade deve ser um número inteiro";
        } else {
            $idade = trim($_POST["idade"]);
        }
    
        // Validação do campo Sexo
        if (empty(trim($_POST["sexo"]))) {
            $sexo_err = "Sexo é obrigatório";
        } else {
            $sexo = trim($_POST["sexo"]);
        }
    
        // Validação do campo Vacinado
        if (empty(trim($_POST["vacinado"]))) {
            $vacinado_err = "Vacinado é obrigatório";
        } else {
            $vacinado = trim($_POST["vacinado"]);
        }
    
        // Verifica se não há erros antes de inserir no banco de dados
        if (empty($nome_err) && empty($type_err) && empty($raca_err) && empty($cor_err) && empty($idade_err) && empty($sexo_err) && empty($vacinado_err)) {
    
            // Prepara a consulta SQL usando prepared statements
            $sql = "INSERT INTO pets (nome, tipo, raca, cor, idade, sexo, vacinado) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
            if ($stmt = $conn->prepare($sql)) {
                // Vincula os parâmetros
                $stmt->bind_param("ssssiss", $nome, $type, $raca, $cor, $idade, $sexo, $vacinado);
    
                // Executa a declaração
                if ($stmt->execute()) {
                    // Exibe mensagem de sucesso
                    echo "<p>Cadastro realizado com sucesso!</p>";
                } else {
                    // Mensagem de erro de execução
                    echo "Erro ao cadastrar o animal: " . $conn->error;
                }
    
                // Fecha a declaração
                $stmt->close();
            } else {
                echo "Erro de preparação: " . $conn->error;
            }
        }
    }
    
    // Fecha a conexão com o banco de dados
    $conn->close();
    ?>
</body>
<script src="formpet.js" defer></script>
</html>