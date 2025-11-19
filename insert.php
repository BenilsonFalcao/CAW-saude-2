<?php
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $imc = $_POST['imc'];
    $pressao = $_POST['pressao_sistolica'];

    $sql = "INSERT INTO paciente (nome, idade, imc, pressao_sistolica) VALUES (:nome, :idade, :imc, :pressao)";
    
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':idade', $idade);
        $stmt->bindParam(':imc', $imc);
        $stmt->bindParam(':pressao', $pressao);

        if ($stmt->execute()) {
            header("location: index.php");
            exit();
        } else {
            echo "Erro ao inserir dados.";
        }
    }
}
?>
