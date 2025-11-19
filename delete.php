<?php
require_once 'connection.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $sql = "DELETE FROM paciente WHERE id = :id";
    
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(':id', $_GET['id']);
        if ($stmt->execute()) {
            header("location: index.php");
            exit();
        } else {
            echo "Erro ao deletar.";
        }
    }
}
?>
