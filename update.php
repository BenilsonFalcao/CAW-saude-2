<?php
require_once 'connection.php';

$id = $_GET['id'] ?? null;
$paciente = null;

// Buscar dados do paciente para preencher o form
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM paciente WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Processar atualização
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_post = $_POST['id'];
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $imc = $_POST['imc'];
    $pressao = $_POST['pressao_sistolica'];

    $sql = "UPDATE paciente SET nome = :nome, idade = :idade, imc = :imc, pressao_sistolica = :pressao WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':idade' => $idade,
        ':imc' => $imc,
        ':pressao' => $pressao,
        ':id' => $id_post
    ]);

    header("location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Paciente</h1>
        <?php if ($paciente): ?>
        <form action="update.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">
            <div>
                <label>Nome do Paciente:</label>
                <input type="text" name="nome" value="<?php echo $paciente['nome']; ?>" required>
            </div>
            <div>
                <label>Idade:</label>
                <input type="number" name="idade" value="<?php echo $paciente['idade']; ?>" required>
            </div>
            <div>
                <label>IMC:</label>
                <input type="number" step="0.01" name="imc" value="<?php echo $paciente['imc']; ?>" required>
            </div>
            <div>
                <label>Pressão Sistólica:</label>
                <input type="number" name="pressao_sistolica" value="<?php echo $paciente['pressao_sistolica']; ?>" required>
            </div>
            <button type="submit">Atualizar</button>
        </form>
        <div style="text-align:center; margin-top:10px;">
            <a href="index.php">Voltar</a>
        </div>
        <?php else: ?>
            <p>Paciente não encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
