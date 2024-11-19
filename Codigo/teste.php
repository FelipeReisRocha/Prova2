<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações do banco de dados
$servername = "pessoasqlserver.database.windows.net"; // Nome do servidor Azure
$username = "meuUsuarioAdmin@pessoasqlserver";  // Substitua com seu nome de usuário no Azure
$password = "minhaSenhaSegura123"; // Substitua com sua senha
$dbname = "PessoaSQL"; // Nome do banco de dados

// Estabelece a conexão com o MySQL
$mysqli = new mysqli($servername, $username, $password, $dbname, 3306);

// Verifica se a conexão foi bem-sucedida
if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

// Verifica se o formulário foi enviado e se há um arquivo de imagem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    // Caminho absoluto da pasta de upload
    $uploadDir = __DIR__ . '/uploads/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);

    // Verifica se a pasta de uploads existe
    if (!is_dir($uploadDir)) {
        echo "A pasta de uploads não existe.";
        exit;
    }

    // Verifica se houve erro no upload
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo "Erro no upload: " . $_FILES['image']['error'];
        exit;
    }

    // Verifica se a imagem foi enviada corretamente
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        echo "Imagem enviada com sucesso!<br>";

        // Agora que o arquivo foi enviado, enviaremos para a API da Azure
        $endpoint = 'https://reconhecimentoprova2.cognitiveservices.azure.com/face/v1.0/detect'; // URL correta
        $key = '6JXH8i0huVYzbFf1q4I1OAlmhgC9aJKLAbW11lx93AAO6JplIMCrJQQJ99AKACZoyfiXJ3w3AAAKACOGwjts'; // Substitua com sua chave de assinatura

        // Inicializa o cURL
        $ch = curl_init();

        // Configura o cURL
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        // Cabeçalhos exigidos pela Azure
        $headers = [
            'Ocp-Apim-Subscription-Key: ' . $key,
            'Content-Type: application/octet-stream',  // Tipo de conteúdo para arquivo binário
        ];

        // Define os cabeçalhos
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Envia o arquivo de imagem diretamente para a API
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($uploadFile));

        // Executa a requisição e captura a resposta
        $response = curl_exec($ch);

        // Verifica se ocorreu erro no cURL
        if (curl_errno($ch)) {
            echo 'Erro cURL: ' . curl_error($ch);
        } else {
            // Formata e exibe a resposta da Azure de forma amigável
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($statusCode == 200) {
                // Decodificando a resposta JSON
                $jsonResponse = json_decode($response, true);
                echo "Resposta da Azure (detecção de rosto): <pre>";
                print_r($jsonResponse); // Exibe a resposta da Azure
                echo "</pre>";
            } else {
                echo "Erro na resposta da Azure: " . $response;
            }
        }

        // Fecha a conexão cURL
        curl_close($ch);

    } else {
        echo "Erro no upload da imagem.";
    }

    // Armazenar os dados do formulário no banco de dados
    $nome = $mysqli->real_escape_string($_POST['nome']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $telefone = $mysqli->real_escape_string($_POST['telefone']);
    $data_nascimento = $_POST['data_nascimento'];
    $endereco = $mysqli->real_escape_string($_POST['endereco']);
    $foto = $uploadFile; // Caminho da imagem enviada

    // Query para inserir os dados no banco
    $sql = "INSERT INTO Pessoas (nome, email, telefone, data_nascimento, endereco, foto)
            VALUES ('$nome', '$email', '$telefone', '$data_nascimento', '$endereco', '$foto')";

    if ($mysqli->query($sql) === TRUE) {
        echo "Novo registro criado com sucesso!";
    } else {
        echo "Erro ao criar registro: " . $mysqli->error;
    }
} else {
    echo "Nenhuma imagem enviada.";
}

// Fecha a conexão com o banco de dados
$mysqli->close();
?>
