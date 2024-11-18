<?php
// Receber a URL da imagem do formulário
$imageUrl = $_POST['imageUrl'];

// Configurações da API
$endpoint = "https://reconhecimentoprova2.cognitiveservices.azure.com/face/v1.0/detect";
$subscriptionKey = "7A3HXGG05bMH1UvACZbc8hPyiWWNdQbXPPjXM07dcHEE9flvzhFbJQQJ99AKACZoyfiXJ3w3AAAKACOGeepd";

// Parâmetros da requisição
$params = [
    "returnFaceId" => "false",
    "returnFaceLandmarks" => "false"
];

// Criar a URL completa com os parâmetros
$url = $endpoint . '?' . http_build_query($params);

// Dados para enviar na requisição
$data = json_encode(["url" => $imageUrl]);

// Configurar o cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Ocp-Apim-Subscription-Key: $subscriptionKey"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Executar a requisição e obter a resposta
$response = curl_exec($ch);

// Verificar se houve erro
if (curl_errno($ch)) {
    echo "Erro: " . curl_error($ch);
    curl_close($ch);
    exit;
}

// Fechar a conexão cURL
curl_close($ch);

// Exibir o resultado da API
header('Content-Type: application/json');
echo $response;
?>
