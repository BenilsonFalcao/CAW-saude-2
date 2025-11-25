<?php

// Nome do arquivo que servirá como banco de dados
$arquivo_db = 'banco_pacientes.json';

// Inicializa variáveis
$pacientes = [];
$mensagem = "";

// Função auxiliar para ler o banco de dados
function lerDados($arquivo) {
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        return json_decode($conteudo, true) ?? [];
    }
    return [];
}

// Função auxiliar para salvar no banco de dados
function salvarDados($arquivo, $dados) {
    file_put_contents($arquivo, json_encode($dados, JSON_PRETTY_PRINT));
}

// Carrega pacientes existentes
$pacientes = lerDados($arquivo_db);

// PROCESSAMENTO DO FORMULÁRIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST['acao'] ?? '';

    // --- CREATE ---
    if ($acao === 'adicionar') {
        $id = $_POST['id'];
        
        // Verifica se ID já existe
        $id_existe = false;
        foreach ($pacientes as $p) {
            if ($p['id'] == $id) {
                $id_existe = true;
                break;
            }
        }

        if (!$id_existe) {
            $novo_paciente = [
                'id' => $id,
                'nome' => $_POST['nome'],
                'idade' => $_POST['idade'],
                'imc' => $_POST['imc'],
                'sistolica' => $_POST['sistolica']
            ];
            $pacientes[] = $novo_paciente;
            salvarDados($arquivo_db, $pacientes);
            $mensagem = "Paciente cadastrado com sucesso!";
        } else {
            $mensagem = "Erro: Já existe um paciente com este ID.";
        }
    }

    // --- UPDATE (Editar) ---
    elseif ($acao === 'editar') {
        $id = $_POST['id'];
        foreach ($pacientes as $key => $p) {
            if ($p['id'] == $id) {
                $pacientes[$key]['nome'] = $_POST['nome'];
                $pacientes[$key]['idade'] = $_POST['idade'];
                $pacientes[$key]['imc'] = $_POST['imc'];
                $pacientes[$key]['sistolica'] = $_POST['sistolica'];
                break;
            }
        }
        salvarDados($arquivo_db, $pacientes);
        $mensagem = "Dados atualizados com sucesso!";
    }

    // --- DELETE ---
    elseif ($acao === 'excluir') {
        $id_excluir = $_POST['id_excluir'];
        // Filtra o array removendo o ID selecionado
        $pacientes = array_filter($pacientes, function($p) use ($id_excluir) {
            return $p['id'] != $id_excluir;
        });
        // Reindexa o array para evitar buracos nos índices numéricos
        $pacientes = array_values($pacientes);
        salvarDados($arquivo_db, $pacientes);
        $mensagem = "Paciente excluído com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Pacientes</title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Estilo de Alertas */
        .alert {
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        /* Formulário em Grid */
        form#patientForm {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }

        form h2 {
            grid-column: 1 / -1;
            margin-top: 0;
            color: #007bff;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Botões do Formulário */
        .btn-submit {
            grid-column: 1 / 2;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-reset {
            grid-column: 2 / 3;
            padding: 12px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-submit:hover { background-color: #0056b3; }
        .btn-reset:hover { background-color: #5a6268; }

        /* Tabela */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #343a40;
            color: white;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Ações da Tabela */
        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-edit {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-edit:hover { background-color: #218838; }
        .btn-delete:hover { background-color: #c82333; }

    </style>

    <script>
        function preencherParaEdicao(id, nome, idade, imc, sistolica) {
            document.getElementById('id').value = id;
            document.getElementById('nome').value = nome;
            document.getElementById('idade').value = idade;
            document.getElementById('imc').value = imc;
            document.getElementById('sistolica').value = sistolica;

            document.getElementById('acao').value = 'editar';
            document.getElementById('submitButton').textContent = 'Salvar Edição';
            document.getElementById('formTitle').textContent = 'Editar Paciente';
            
            // Bloqueia o ID na edição para garantir integridade
            document.getElementById('id').readOnly = true;
            document.getElementById('id').style.backgroundColor = "#e9ecef";
            
            // Rola a página para o formulário
            document.getElementById('patientForm').scrollIntoView({behavior: 'smooth'});
        }

        function resetarFormulario() {
            document.getElementById('patientForm').reset();
            document.getElementById('acao').value = 'adicionar';
            document.getElementById('submitButton').textContent = 'Adicionar Paciente';
            document.getElementById('formTitle').textContent = 'Registro de Dados';
            
            document.getElementById('id').readOnly = false;
            document.getElementById('id').style.backgroundColor = "white";
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Monitoramento de Pacientes (CRUD)</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alert"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="POST" id="patientForm">
            <h2 id="formTitle">Registro de Dados</h2>

            <input type="hidden" name="acao" id="acao" value="adicionar">

            <div class="form-group">
                <label>ID do Paciente:</label>
                <input type="text" id="id" name="id" required placeholder="Ex: 101">
            </div>

            <div class="form-group">
                <label>Nome:</label>
                <input type="text" id="nome" name="nome" required placeholder="Nome Completo">
            </div>

            <div class="form-group">
                <label>Idade:</label>
                <input type="number" id="idade" name="idade" required>
            </div>

            <div class="form-group">
                <label>IMC:</label>
                <input type="number" id="imc" name="imc" step="0.1" required placeholder="00.0">
            </div>

            <div class="form-group full-width">
                <label>Pressão Sistólica:</label>
                <input type="number" id="sistolica" name="sistolica" required placeholder="mmHg">
            </div>

            <button type="submit" id="submitButton" class="btn-submit">Adicionar Paciente</button>
            <button type="button" onclick="resetarFormulario()" class="btn-reset">Resetar / Cancelar</button>
        </form>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

        <h2>Pacientes Cadastrados</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Idade</th>
                    <th>IMC</th>
                    <th>Sistólica</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($pacientes) > 0): ?>
                    <?php foreach ($pacientes as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p["id"]) ?></td>
                            <td><?= htmlspecialchars($p["nome"]) ?></td>
                            <td><?= htmlspecialchars($p["idade"]) ?></td>
                            <td><?= htmlspecialchars($p["imc"]) ?></td>
                            <td><?= htmlspecialchars($p["sistolica"]) ?></td>
                            
                            <td class="action-buttons">
                                <button class="btn-edit" onclick="preencherParaEdicao(
                                    '<?= $p['id'] ?>',
                                    '<?= $p['nome'] ?>',
                                    '<?= $p['idade'] ?>',
                                    '<?= $p['imc'] ?>',
                                    '<?= $p['sistolica'] ?>'
                                )">Editar</button>

                                <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este paciente?');">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id_excluir" value="<?= $p["id"] ?>">
                                    <button type="submit" class="btn-delete">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #777;">
                            Nenhum paciente cadastrado ainda.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>
</html>
