<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro com Imagem</title>
</head>
<body>
    <h2>Cadastro de Imagem</h2>
    <form action="teste.php" method="POST" enctype="multipart/form-data">
        <label for="image">Selecione uma imagem para upload:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit" name="submit">Cadastrar</button>
    </form>
</body>
</html>
