<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/api_client.php';

/* Mesmos admins do admin_profissionais.php. Troque pelo id da sua conta. */
$ADMIN_IDS = [7];

if (!isset($_SESSION['usuario_id']) || !in_array((int) $_SESSION['usuario_id'], $ADMIN_IDS, true)) {
    header('Location: login.php');
    exit;
}

/* ---------- TABELAS DISPONÍVEIS ----------
   campo => [tipo, rótulo, obrigatório, opções (só para select)]
   tipos: int, text, textarea, date, time, bool, select            */

$TABELAS = [
    'rotinas' => [
        'titulo'  => 'Rotinas',
        'filtros' => ['usuario_id'],
        'campos'  => [
            'usuario_id' => ['int',  'Usuário (id)', true],
            'nome'       => ['text', 'Nome da rotina', true],
        ],
    ],
    'rotina_itens' => [
        'titulo'  => 'Itens da rotina',
        'filtros' => ['rotina_id'],
        'campos'  => [
            'rotina_id' => ['int',  'Rotina (id)', true],
            'titulo'    => ['text', 'Título', true],
            'emoji'     => ['text', 'Emoji', false],
            'imagem'    => ['text', 'Imagem (nome do arquivo)', false],
            'horario'   => ['time', 'Horário', false],
            'ordem'     => ['int',  'Ordem', false],
        ],
    ],
    'atividades_padrao' => [
        'titulo'  => 'Atividades prontas',
        'filtros' => ['categoria'],
        'campos'  => [
            'nome'      => ['text', 'Nome', true],
            'categoria' => ['text', 'Categoria', true],
            'emoji'     => ['text', 'Emoji', true],
        ],
    ],
    'rotina_progresso' => [
        'titulo'  => 'Progresso das rotinas',
        'filtros' => ['item_id', 'dia'],
        'chave'   => ['item_id', 'dia'],   // sem coluna id: só adicionar/excluir
        'campos'  => [
            'item_id' => ['int',  'Item (id)', true],
            'dia'     => ['date', 'Dia', true],
        ],
    ],
    'checklist_jornada' => [
        'titulo'  => 'Checklist: etapa do usuário',
        'filtros' => ['usuario_id'],
        'campos'  => [
            'usuario_id'  => ['int',  'Usuário (id)', true],
            'etapa_atual' => ['text', 'Etapa atual', true],
        ],
    ],
    'checklist_tarefas' => [
        'titulo'  => 'Checklist: tarefas',
        'filtros' => ['usuario_id', 'etapa', 'status'],
        'campos'  => [
            'usuario_id'    => ['int',    'Usuário (id)', true],
            'etapa'         => ['text',   'Etapa', true],
            'titulo'        => ['text',   'Tarefa', true],
            'status'        => ['select', 'Status', true, ['pendente', 'concluido', 'andamento', 'ajuda', 'nao_aplica']],
            'personalizada' => ['bool',   'Tarefa personalizada', false],
        ],
    ],
    'trajetoria_checklists' => [
        'titulo'  => 'Trajetória: checklists',
        'filtros' => ['usuario_id'],
        'campos'  => [
            'usuario_id' => ['int',  'Usuário (id)', true],
            'titulo'     => ['text', 'Título', true],
            'descricao'  => ['text', 'Descrição', false],
            'icone'      => ['text', 'Ícone', false],
            'ordem'      => ['int',  'Ordem', false],
        ],
    ],
    'trajetoria_itens' => [
        'titulo'  => 'Trajetória: itens',
        'filtros' => ['checklist_id', 'concluida'],
        'campos'  => [
            'checklist_id' => ['int',  'Checklist (id)', true],
            'etapa'        => ['text', 'Etapa', true],
            'concluida'    => ['bool', 'Concluída', false],
            'ordem'        => ['int',  'Ordem', false],
        ],
    ],
    'trajetoria_registros' => [
        'titulo'  => 'Trajetória: registros',
        'filtros' => ['usuario_id', 'tipo'],
        'campos'  => [
            'usuario_id'    => ['int',      'Usuário (id)', true],
            'titulo'        => ['text',     'Título', true],
            'tipo'          => ['text',     'Tipo', true],
            'data_registro' => ['date',     'Data', true],
            'profissional'  => ['text',     'Profissional', false],
            'local'         => ['text',     'Local', false],
            'descricao'     => ['textarea', 'Descrição', false],
            'foto'          => ['text',     'Foto (caminho)', false],
        ],
    ],
];

function e($v) {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function avisar($tipo, $texto) {
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

$t = $_GET['t'] ?? 'rotinas';
if (!isset($TABELAS[$t])) {
    $t = 'rotinas';
}

$cfg      = $TABELAS[$t];
$campos   = $cfg['campos'];
$temChave = isset($cfg['chave']);
$voltar   = 'admin_dados.php?t=' . $t;


/* ---------- AÇÕES ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';
    $id   = (int) ($_POST['id'] ?? 0);

    if ($acao === 'salvar') {

        $dados = [];
        $erro  = null;

        foreach ($campos as $nome => $c) {
            [$tipo, $rotulo, $obrig] = $c;

            if ($tipo === 'bool') {
                $dados[$nome] = isset($_POST[$nome]) ? 1 : 0;
                continue;
            }

            $v = trim($_POST[$nome] ?? '');

            if ($v === '') {
                if ($obrig) {
                    $erro = "Preencha o campo: $rotulo.";
                    break;
                }
                $dados[$nome] = $tipo === 'int' ? 0 : null;
                continue;
            }

            $dados[$nome] = $tipo === 'int' ? (int) $v : $v;
        }

        if ($erro) {
            avisar('erro', $erro);
        } else {
            if (!$temChave && $id > 0) {
                $r  = api_t_editar($t, $id, $dados);
                $ok = $r['status'] === 200;
                $msg = 'Registro atualizado.';
            } else {
                $r  = api_t_cadastrar($t, $dados);
                $ok = $r['status'] === 201;
                $msg = 'Registro cadastrado.';
            }
            avisar($ok ? 'ok' : 'erro', $ok ? $msg : api_erro($r, 'Não foi possível salvar.'));
        }

    } elseif ($acao === 'excluir') {

        if ($temChave) {
            $r = api_progresso_excluir((int) ($_POST['item_id'] ?? 0), (string) ($_POST['dia'] ?? ''));
        } else {
            $r = api_t_excluir($t, $id);
        }

        avisar($r['status'] === 200 ? 'ok' : 'erro',
               $r['status'] === 200 ? 'Registro excluído.' : api_erro($r, 'Não foi possível excluir.'));
    }

    header('Location: ' . $voltar);
    exit;
}


/* ---------- DADOS DA PÁGINA ---------- */

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$editar = null;
if (!$temChave && isset($_GET['editar'])) {
    $r = api_t_ver($t, (int) $_GET['editar']);
    if ($r['status'] === 200) {
        $editar = $r['dados'];
    }
}

$filtrosAtivos = [];
foreach ($cfg['filtros'] as $f) {
    if (isset($_GET[$f]) && $_GET[$f] !== '') {
        $filtrosAtivos[$f] = $_GET[$f];
    }
}

$resposta = api_t_listar($t, $filtrosAtivos);
$apiOk    = $resposta['status'] === 200 && is_array($resposta['dados']);
$lista    = $apiOk ? $resposta['dados'] : [];

include __DIR__ . '/includes/header.php';

?>

<link rel="stylesheet" href="../css/estilo.css">

<style>
.adm-wrap { width: 92%; max-width: 1180px; margin: 40px auto 70px; }
.adm-topo h1 { margin: 0; color: #21479b; font-size: 1.7rem; }
.adm-topo p { margin: 4px 0 20px; color: #66738a; font-size: .9rem; }

.adm-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px; }
.adm-tabs a { padding: 8px 14px; border-radius: 20px; background: #eaf2ff; color: #21479b; font-size: .8rem; font-weight: 700; text-decoration: none; }
.adm-tabs a.ativa { background: #21479b; color: #fff; }

.adm-aviso { padding: 13px 18px; border-radius: 12px; margin-bottom: 20px; font-size: .9rem; font-weight: 600; }
.adm-aviso.ok { background: #edf8ee; color: #347a39; }
.adm-aviso.erro { background: #fdeeee; color: #b42318; }

.adm-card { background: #fff; border: 1px solid #e3eaf5; border-radius: 18px; padding: 26px; margin-bottom: 26px; box-shadow: 0 5px 18px rgba(31, 45, 61, .04); }
.adm-card h2 { margin: 0 0 18px; color: #21479b; font-size: 1.2rem; }

.adm-form { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 18px; }
.adm-form .cheio { grid-column: 1 / -1; }
.adm-form label { display: block; margin-bottom: 5px; color: #374151; font-size: .8rem; font-weight: 700; }
.adm-form input[type=text], .adm-form input[type=number], .adm-form input[type=date], .adm-form input[type=time],
.adm-form select, .adm-form textarea { width: 100%; box-sizing: border-box; padding: 11px 13px; border: 1px solid #dfe6f0; border-radius: 10px; font-size: .9rem; color: #263b63; font-family: inherit; outline: none; }
.adm-form textarea { min-height: 80px; resize: vertical; }
.adm-check { display: flex; align-items: center; gap: 8px; font-size: .88rem; color: #374151; }
.adm-botoes { display: flex; gap: 10px; flex-wrap: wrap; }

.btn-adm { display: inline-flex; align-items: center; gap: 7px; padding: 11px 20px; border: none; border-radius: 10px; font-size: .86rem; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn-adm.azul { background: #21479b; color: #fff; }
.btn-adm.claro { background: #eaf2ff; color: #21479b; }

.adm-filtro { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; margin-bottom: 16px; }
.adm-filtro label { display: block; font-size: .72rem; font-weight: 700; color: #6b7a90; margin-bottom: 4px; }
.adm-filtro input { padding: 8px 10px; border: 1px solid #dfe6f0; border-radius: 8px; font-size: .85rem; width: 130px; }
.adm-contador { color: #6b7a90; font-size: .85rem; margin-bottom: 12px; }

.adm-scroll { overflow-x: auto; }
.adm-tab { width: 100%; border-collapse: collapse; font-size: .84rem; }
.adm-tab th { text-align: left; padding: 10px; color: #21479b; border-bottom: 2px solid #e3eaf5; white-space: nowrap; }
.adm-tab td { padding: 10px; border-bottom: 1px solid #eef2f8; color: #374151; vertical-align: top; }
.adm-tab form { margin: 0; display: inline; }
.mini { padding: 7px 11px; border: none; border-radius: 8px; font-size: .75rem; font-weight: 700; cursor: pointer; text-decoration: none; background: #eaf2ff; color: #21479b; }
.mini.perigo { background: #fdeeee; color: #b42318; }

@media (max-width: 760px) { .adm-form { grid-template-columns: 1fr; } }
</style>

<main class="adm-wrap">

    <div class="adm-topo">
        <h1>Dados do site</h1>
        <p>Área restrita: rotinas, checklist e trajetória de todos os usuários, direto da API.</p>
    </div>

    <div class="adm-tabs">
        <a href="admin_profissionais.php">Profissionais</a>
        <?php foreach ($TABELAS as $chave => $tab): ?>
            <a href="admin_dados.php?t=<?= e($chave) ?>" class="<?= $chave === $t ? 'ativa' : '' ?>"><?= e($tab['titulo']) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if ($flash): ?>
        <div class="adm-aviso <?= e($flash['tipo']) ?>"><?= e($flash['texto']) ?></div>
    <?php endif; ?>

    <?php if (!$apiOk): ?>
        <div class="adm-aviso erro">
            Não consegui ler esta tabela na API. Confira se ela está rodando (e reiniciada com o main.py novo).
        </div>
    <?php endif; ?>


    <!-- FORMULÁRIO -->

    <div class="adm-card">

        <h2><?= $editar ? 'Editar registro' : 'Novo registro' ?> · <?= e($cfg['titulo']) ?></h2>

        <form method="post" action="admin_dados.php?t=<?= e($t) ?>" class="adm-form">

            <input type="hidden" name="acao" value="salvar">
            <input type="hidden" name="id" value="<?= $editar ? (int) $editar['id'] : 0 ?>">

            <?php foreach ($campos as $nome => $c): ?>
                <?php
                    [$tipo, $rotulo, $obrig] = $c;
                    $atual = $editar[$nome] ?? '';
                    $cheio = $tipo === 'textarea' ? 'cheio' : '';
                ?>

                <?php if ($tipo === 'bool'): ?>
                    <div class="adm-check">
                        <input type="checkbox" name="<?= e($nome) ?>" id="c_<?= e($nome) ?>" value="1" <?= (int) $atual === 1 ? 'checked' : '' ?>>
                        <label for="c_<?= e($nome) ?>" style="margin:0"><?= e($rotulo) ?></label>
                    </div>

                <?php else: ?>
                    <div class="<?= $cheio ?>">
                        <label for="c_<?= e($nome) ?>"><?= e($rotulo) ?><?= $obrig ? ' *' : '' ?></label>

                        <?php if ($tipo === 'textarea'): ?>
                            <textarea id="c_<?= e($nome) ?>" name="<?= e($nome) ?>"><?= e($atual) ?></textarea>

                        <?php elseif ($tipo === 'select'): ?>
                            <select id="c_<?= e($nome) ?>" name="<?= e($nome) ?>">
                                <?php foreach ($c[3] as $op): ?>
                                    <option value="<?= e($op) ?>" <?= $atual === $op ? 'selected' : '' ?>><?= e($op) ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php else: ?>
                            <?php $inputTipo = ['int' => 'number', 'date' => 'date', 'time' => 'time'][$tipo] ?? 'text'; ?>
                            <input type="<?= $inputTipo ?>" id="c_<?= e($nome) ?>" name="<?= e($nome) ?>" value="<?= e($atual) ?>">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>

            <div class="cheio adm-botoes">
                <button type="submit" class="btn-adm azul"><?= $editar ? 'Salvar alterações' : 'Cadastrar' ?></button>
                <?php if ($editar): ?>
                    <a href="<?= e($voltar) ?>" class="btn-adm claro">Cancelar</a>
                <?php endif; ?>
            </div>

        </form>

    </div>


    <!-- LISTA -->

    <div class="adm-card">

        <h2><?= e($cfg['titulo']) ?></h2>

        <form method="get" class="adm-filtro">
            <input type="hidden" name="t" value="<?= e($t) ?>">
            <?php foreach ($cfg['filtros'] as $f): ?>
                <div>
                    <label for="f_<?= e($f) ?>">Filtrar por <?= e($f) ?></label>
                    <input id="f_<?= e($f) ?>" name="<?= e($f) ?>" value="<?= e($filtrosAtivos[$f] ?? '') ?>">
                </div>
            <?php endforeach; ?>
            <button class="btn-adm azul" type="submit">Filtrar</button>
            <?php if ($filtrosAtivos): ?>
                <a href="<?= e($voltar) ?>" class="btn-adm claro">Limpar</a>
            <?php endif; ?>
        </form>

        <div class="adm-contador"><?= count($lista) ?> registro(s)</div>

        <?php if (empty($lista)): ?>
            <p style="color:#66738a">Nenhum registro encontrado.</p>
        <?php else: ?>

        <div class="adm-scroll">
            <table class="adm-tab">
                <thead>
                    <tr>
                        <?php if (!$temChave): ?><th>id</th><?php endif; ?>
                        <?php foreach ($campos as $nome => $c): ?><th><?= e($nome) ?></th><?php endforeach; ?>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($lista as $linha): ?>
                    <tr>
                        <?php if (!$temChave): ?><td><?= (int) ($linha['id'] ?? 0) ?></td><?php endif; ?>

                        <?php foreach ($campos as $nome => $c): ?>
                            <td><?= e(mb_strimwidth((string) ($linha[$nome] ?? ''), 0, 70, '…')) ?></td>
                        <?php endforeach; ?>

                        <td style="white-space:nowrap">
                            <?php if (!$temChave): ?>
                                <?php $qs = http_build_query(array_merge(['t' => $t, 'editar' => (int) $linha['id']], $filtrosAtivos)); ?>
                                <a class="mini" href="admin_dados.php?<?= e($qs) ?>">Editar</a>
                            <?php endif; ?>

                            <form method="post" action="admin_dados.php?t=<?= e($t) ?>" onsubmit="return confirm('Excluir este registro? Isso não pode ser desfeito.')">
                                <input type="hidden" name="acao" value="excluir">
                                <?php if ($temChave): ?>
                                    <input type="hidden" name="item_id" value="<?= (int) $linha['item_id'] ?>">
                                    <input type="hidden" name="dia" value="<?= e($linha['dia']) ?>">
                                <?php else: ?>
                                    <input type="hidden" name="id" value="<?= (int) $linha['id'] ?>">
                                <?php endif; ?>
                                <button class="mini perigo">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>

    </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>