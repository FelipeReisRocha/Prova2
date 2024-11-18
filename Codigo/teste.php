<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    // Caminho absoluto da pasta de upload
    $uploadDir = __DIR__ . '/uploads/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);

    // Verifica se a pasta de uploads existe
    if (!is_dir($uploadDir)) {
        echo "A pasta de uploads não existe.";
        exit;
    }

    // Verifica se a imagem foi enviada corretamente
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        echo "Imagem enviada com sucesso!<br>";

        // Agora que o arquivo foi enviado, enviaremos para a API da Azure
        $endpoint = 'https://reconhecimentoprova2.cognitiveservices.azure.com/face/v1.0/detect'; // Use a URL correta
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

        // Envia o arquivo de imagem diretamente
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($uploadFile));

        // Executa a requisição e captura a resposta
        $response = curl_exec($ch);

        // Verifica se ocorreu erro
        if (curl_errno($ch)) {
            echo 'Erro cURL: ' . curl_error($ch);
        } else {
            // Exibe a resposta da Azure
            echo "Resposta da Azure: " . $response;
        }

        // Fecha a conexão cURL
        curl_close($ch);

    } else {
        echo "Erro no upload da imagem.";
    }
}
?>
