<?php
// Nome do arquivo JSON
$arquivo = "pacientes.json";
$pacientes = [];
$mensagem = "";

// Carregar dados existentes
if (file_exists($arquivo)) {
    $conteudo = file_get_contents($arquivo);
    $dados = json_decode($conteudo, true);

    if ($dados !== null) {
        $pacientes = $dados;
    } else {
        $mensagem = "Erro: Arquivo JSON corrompido. Iniciando vazio.";
        $pacientes = [];
    }
}

// Função para salvar no JSON
function salvarPacientes($pacientes, $arquivo) {
    global $mensagem;
    if (file_put_contents($arquivo, json_encode($pacientes, JSON_PRETTY_PRINT)) === false) {
        $mensagem = "Erro ao salvar dados.";
        return false;
    }
    return true;
}

// Ações CRUD
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id'] ?? '';

    if ($acao === "adicionar" || $acao === "editar") {
        if (!isset($_POST["nome"], $_POST["idade"], $_POST["imc"], $_POST["sistolica"]) || empty($id)) {
            $mensagem = "Erro: Todos os campos são obrigatórios.";
        } else {
            $novoPaciente = [
                "id" => htmlspecialchars(trim($id)),
                "nome" => htmlspecialchars(trim($_POST["nome"])),
                "idade" => (int)$_POST["idade"],
                "imc" => (float)$_POST["imc"],
                "sistolica" => (int)$_POST["sistolica"]
            ];

            if ($acao === "adicionar") {
                foreach ($pacientes as $p) {
                    if ($p['id'] === $novoPaciente['id']) {
                        $mensagem = "ID já existe.";
                        break;
                    }
                }

                if (empty($mensagem)) {
                    $pacientes[] = $novoPaciente;
                    salvarPacientes($pacientes, $arquivo);
                }

            } elseif ($acao === "editar") {
                foreach ($pacientes as $i => $p) {
                    if ($p['id'] === $novoPaciente['id']) {
                        $pacientes[$i] = $novoPaciente;
                        salvarPacientes($pacientes, $arquivo);
                        break;
                    }
                }
            }
        }
    } elseif ($acao === "excluir") {
        $idParaExcluir = trim($_POST['id_excluir']);
        $pacientes = array_values(array_filter($pacientes, fn($p) => $p['id'] !== $idParaExcluir));
        salvarPacientes($pacientes, $arquivo);
    }

    header("Location: index.php");
    exit;
}
?>
crud
