<?php include "crud.php"; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Pacientes</title>
    <link rel="stylesheet" href="styles.css">

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
            document.getElementById('id').readOnly = true;
        }

        function resetarFormulario() {
            document.getElementById('patientForm').reset();
            document.getElementById('acao').value = 'adicionar';
            document.getElementById('submitButton').textContent = 'Adicionar Paciente';
            document.getElementById('formTitle').textContent = 'Registro de Dados';
            document.getElementById('id').readOnly = false;
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

            <label>ID do Paciente:</label>
            <input type="text" id="id" name="id" required>

            <label>Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label>Idade:</label>
            <input type="number" id="idade" name="idade" required>

            <label>IMC:</label>
            <input type="number" id="imc" name="imc" step="0.1" required>

            <label>Sistólica:</label>
            <input type="number" id="sistolica" name="sistolica" required>

            <button type="submit" id="submitButton">Adicionar Paciente</button>
            <button type="button" onclick="resetarFormulario()">Resetar</button>
        </form>

        <hr>

        <h2>Pacientes Cadastrados</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Nome</th><th>Idade</th><th>IMC</th><th>Sistólica</th><th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($pacientes as $p): ?>
                    <tr>
                        <td><?= $p["id"] ?></td>
                        <td><?= $p["nome"] ?></td>
                        <td><?= $p["idade"] ?></td>
                        <td><?= $p["imc"] ?></td>
                        <td><?= $p["sistolica"] ?></td>

                        <td>
                            <button onclick="preencherParaEdicao('<?= $p['id'] ?>','<?= $p['nome'] ?>','<?= $p['idade'] ?>','<?= $p['imc'] ?>','<?= $p['sistolica'] ?>')">
                                Editar
                            </button>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="id_excluir" value="<?= $p["id"] ?>">
                                <button type="submit">Excluir</button>
                            </form>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</body>
</html>
index
