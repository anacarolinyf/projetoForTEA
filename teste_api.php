<?php
require 'api_client.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['acao'] ?? '') === 'cadastrar') {
        $r = api_cadastrar([
            'nome'          => $_POST['nome'],
            'especialidade' => $_POST['especialidade'],
            'telefone'      => $_POST['telefone'],
        ]);
        $msg = $r['status'] === 201 ? 'Cadastrado!' : 'Erro ao cadastrar.';
    }

    if (($_POST['acao'] ?? '') === 'excluir') {
        $r = api_excluir((int) $_POST['id']);
        $msg = $r['status'] === 200 ? 'Excluído!' : 'Erro ao excluir.';
    }
}

$resultado = api_listar();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Teste da API</title></head>
<body>
    <h1>Profissionais (via API)</h1>

    <?php if ($msg): ?><p><strong><?= htmlspecialchars($msg) ?></strong></p><?php endif; ?>

    <form method="post">
        <input type="hidden" name="acao" value="cadastrar">
        <input name="nome" placeholder="Nome" required>
        <input name="especialidade" placeholder="Especialidade">
        <input name="telefone" placeholder="Telefone">
        <button>Cadastrar</button>
    </form>

    <hr>

    <?php if ($resultado['status'] !== 200): ?>
        <p>Não consegui falar com a API. Ela está rodando?</p>
    <?php else: ?>
        <?php foreach ($resultado['dados'] as $p): ?>
            <form method="post" style="margin-bottom:8px">
                <?= htmlspecialchars($p['nome']) ?> - <?= htmlspecialchars($p['especialidade'] ?? '') ?>
                <input type="hidden" name="acao" value="excluir">
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <button onclick="return confirm('Excluir?')">Excluir</button>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>