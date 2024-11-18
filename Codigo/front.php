<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azure API - Análise de Imagem</title>
    <script>
        // Função para enviar a URL da imagem via AJAX para o PHP
        function analyzeImage(event) {
            event.preventDefault(); // Impede o envio padrão do formulário

            var formData = new FormData(document.getElementById('imageForm'));
            var resultDiv = document.getElementById('result');

            // Enviar a requisição AJAX para o teste.php
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'teste.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Exibir o resultado da API dentro da div result
                    resultDiv.innerHTML = '<pre>' + xhr.responseText + '</pre>';
                }
            };
            xhr.send(formData);
        }
    </script>
</head>
<body>
    <h1>Análise de Imagem com Azure</h1>
    <form id="imageForm" action="teste.php" method="POST" onsubmit="analyzeImage(event)">
        <label for="imageUrl">URL da Imagem:</label>
        <input type="text" id="imageUrl" name="imageUrl" placeholder="Insira a URL" required>
        <button type="submit">Analisar Imagem</button>
    </form>

    <h2>Resultado:</h2>
    <div id="result">
        <!-- O resultado será exibido aqui -->
    </div>
</body>
</html>
