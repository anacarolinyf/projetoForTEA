<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/api_client.php';

/* IDs dos usuários que podem gerenciar profissionais.
   TROQUE pelo id da sua conta na tabela de usuários. */
$ADMIN_IDS = [7];

if (!isset($_SESSION['usuario_id']) || !in_array((int) $_SESSION['usuario_id'], $ADMIN_IDS, true)) {
    header('Location: login.php');
    exit;
}

$CAMPOS = [
    'nome', 'especialidade', 'descricao', 'telefone', 'whatsapp',
    'endereco', 'site', 'convenios', 'atendimento', 'valor_consulta', 'foto',
];

function campo($nome) {
    $v = trim($_POST[$nome] ?? '');
    return $v === '' ? null : $v;
}

function valor($arr, $chave) {
    return htmlspecialchars((string) ($arr[$chave] ?? ''));
}

function avisar($tipo, $texto) {
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}


/* ---------- AÇÕES ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';
    $id   = (int) ($_POST['id'] ?? 0);

    if ($acao === 'salvar') {

        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            avisar('erro', 'Informe o nome do profissional.');
        } else {

            $dados = ['nome' => $nome];

            foreach ($CAMPOS as $c) {
                if ($c !== 'nome') {
                    $dados[$c] = campo($c);
                }
            }

            $dados['ativo'] = isset($_POST['ativo']) ? 1 : 0;

            if ($id > 0) {
                $r  = api_editar($id, $dados);
                $ok = $r['status'] === 200;
                $msg = 'Profissional atualizado.';
            } else {
                $r  = api_cadastrar($dados);
                $ok = $r['status'] === 201;
                $msg = 'Profissional cadastrado.';
            }

            avisar($ok ? 'ok' : 'erro', $ok ? $msg : 'Não foi possível salvar. A API está rodando?');
        }

    } elseif ($acao === 'excluir' && $id > 0) {

        $r = api_excluir($id);
        avisar($r['status'] === 200 ? 'ok' : 'erro',
               $r['status'] === 200 ? 'Profissional excluído.' : 'Não foi possível excluir.');

    } elseif ($acao === 'alternar' && $id > 0) {

        $r = api_ver($id);

        if ($r['status'] === 200) {

            $p = $r['dados'];
            $dados = [];

            foreach ($CAMPOS as $c) {
                $dados[$c] = $p[$c] ?? null;
            }

            $dados['ativo'] = ((int) ($p['ativo'] ?? 1) === 1) ? 0 : 1;

            $r2 = api_editar($id, $dados);
            avisar($r2['status'] === 200 ? 'ok' : 'erro',
                   $r2['status'] === 200 ? 'Visibilidade alterada.' : 'Não foi possível alterar.');
        } else {
            avisar('erro', 'Profissional não encontrado.');
        }
    }

    header('Location: admin_profissionais.php');
    exit;
}


/* ---------- DADOS DA PÁGINA ---------- */

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$editar = null;

if (isset($_GET['editar'])) {
    $r = api_ver((int) $_GET['editar']);
    if ($r['status'] === 200) {
        $editar = $r['dados'];
    }
}

$resposta  = api_listar();
$apiOk     = $resposta['status'] === 200 && is_array($resposta['dados']);
$lista     = $apiOk ? $resposta['dados'] : [];

include __DIR__ . '/includes/header.php';

?>

<link rel="stylesheet" href="../css/estilo.css">

<style>
.adm-wrap { width: 90%; max-width: 1080px; margin: 40px auto 70px; }
.adm-topo { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
.adm-topo h1 { margin: 0; color: #21479b; font-size: 1.7rem; }
.adm-topo p { margin: 4px 0 0; color: #66738a; font-size: .9rem; }
.adm-link { color: #21479b; font-weight: 600; text-decoration: none; font-size: .88rem; }
.adm-link:hover { text-decoration: underline; }

.adm-aviso { padding: 13px 18px; border-radius: 12px; margin-bottom: 20px; font-size: .9rem; font-weight: 600; }
.adm-aviso.ok { background: #edf8ee; color: #347a39; }
.adm-aviso.erro { background: #fdeeee; color: #b42318; }

.adm-card { background: #fff; border: 1px solid #e3eaf5; border-radius: 18px; padding: 26px; margin-bottom: 26px; box-shadow: 0 5px 18px rgba(31, 45, 61, .04); }
.adm-card h2 { margin: 0 0 18px; color: #21479b; font-size: 1.2rem; }

.adm-form { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 18px; }
.adm-form .cheio { grid-column: 1 / -1; }
.adm-form label { display: block; margin-bottom: 5px; color: #374151; font-size: .8rem; font-weight: 700; }
.adm-form input[type=text], .adm-form textarea { width: 100%; box-sizing: border-box; padding: 11px 13px; border: 1px solid #dfe6f0; border-radius: 10px; font-size: .9rem; color: #263b63; font-family: inherit; outline: none; transition: .2s; }
.adm-form input:focus, .adm-form textarea:focus { border-color: #3467eb; box-shadow: 0 0 0 3px rgba(52, 103, 235, .12); }
.adm-form textarea { min-height: 80px; resize: vertical; }
.adm-check { display: flex; align-items: center; gap: 8px; font-size: .88rem; color: #374151; }

.adm-botoes { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 6px; }
.btn-adm { display: inline-flex; align-items: center; gap: 7px; padding: 11px 20px; border: none; border-radius: 10px; font-size: .86rem; font-weight: 700; cursor: pointer; text-decoration: none; transition: .2s; }
.btn-adm.azul { background: #21479b; color: #fff; }
.btn-adm.azul:hover { background: #17397f; transform: translateY(-1px); }
.btn-adm.claro { background: #eaf2ff; color: #21479b; }
.btn-adm.claro:hover { background: #d8e6ff; }

.adm-contador { color: #6b7a90; font-size: .85rem; margin-bottom: 14px; }

.adm-item { display: flex; align-items: center; gap: 16px; padding: 16px 0; border-top: 1px solid #eef2f8; }
.adm-item:first-of-type { border-top: none; }
.adm-foto { width: 54px; height: 54px; flex-shrink: 0; border-radius: 14px; background: #eaf2ff; color: #3467eb; display: flex; align-items: center; justify-content: center; overflow: hidden; font-size: 1.2rem; }
.adm-foto img { width: 100%; height: 100%; object-fit: cover; }
.adm-info { flex: 1; min-width: 0; }
.adm-info strong { display: block; color: #21479b; font-size: .98rem; }
.adm-info span { color: #66738a; font-size: .82rem; }
.adm-badge { display: inline-block; margin-left: 8px; padding: 2px 9px; border-radius: 20px; font-size: .68rem; font-weight: 800; vertical-align: middle; }
.adm-badge.on { background: #edf8ee; color: #347a39; }
.adm-badge.off { background: #f1f3f6; color: #6b7280; }
.adm-acoes { display: flex; gap: 6px; flex-wrap: wrap; }
.adm-acoes form { margin: 0; }
.mini { padding: 8px 12px; border: none; border-radius: 8px; font-size: .76rem; font-weight: 700; cursor: pointer; text-decoration: none; background: #eaf2ff; color: #21479b; transition: .2s; }
.mini:hover { background: #d8e6ff; }
.mini.perigo { background: #fdeeee; color: #b42318; }
.mini.perigo:hover { background: #fbd9d9; }

@media (max-width: 760px) {
    .adm-form { grid-template-columns: 1fr; }
    .adm-item { flex-wrap: wrap; }
}
</style>

<main class="adm-wrap">

    <div class="adm-topo">
        <div>
            <h1>Gerenciar profissionais</h1>
            <p>Cadastre, edite e controle quem aparece na página de profissionais.</p>
        </div>
        <a class="adm-link" href="Profissionais.php"><i class="fa-solid fa-arrow-up-right-from-square"></i> Ver página pública</a>
    </div>

    <?php if ($flash): ?>
        <div class="adm-aviso <?= $flash['tipo'] ?>"><?= htmlspecialchars($flash['texto']) ?></div>
    <?php endif; ?>

    <?php if (!$apiOk): ?>
        <div class="adm-aviso erro">
            Não consegui falar com a API. Abra o <strong>iniciar_api.bat</strong> e recarregue a página.
        </div>
    <?php endif; ?>


    <!-- FORMULÁRIO -->

    <div class="adm-card">

        <h2><?= $editar ? 'Editar profissional' : 'Novo profissional' ?></h2>

        <form method="post" class="adm-form">

            <input type="hidden" name="acao" value="salvar">
            <input type="hidden" name="id" value="<?= $editar ? (int) $editar['id'] : 0 ?>">

            <div>
                <label>Nome *</label>
                <input type="text" name="nome" required value="<?= valor($editar, 'nome') ?>">
            </div>

            <div>
                <label>Especialidade</label>
                <input type="text" name="especialidade" value="<?= valor($editar, 'especialidade') ?>">
            </div>

            <div class="cheio">
                <label>Descrição</label>
                <textarea name="descricao"><?= valor($editar, 'descricao') ?></textarea>
            </div>

            <div>
                <label>Telefone</label>
                <input type="text" name="telefone" value="<?= valor($editar, 'telefone') ?>">
            </div>

            <div>
                <label>WhatsApp (só números, com DDD)</label>
                <input type="text" name="whatsapp" value="<?= valor($editar, 'whatsapp') ?>">
            </div>

            <div class="cheio">
                <label>Endereço</label>
                <input type="text" name="endereco" value="<?= valor($editar, 'endereco') ?>">
            </div>

            <div>
                <label>Site (começando com https://)</label>
                <input type="text" name="site" value="<?= valor($editar, 'site') ?>">
            </div>

            <div>
                <label>Foto (caminho ou link da imagem)</label>
                <input type="text" name="foto" value="<?= valor($editar, 'foto') ?>">
            </div>

            <div>
                <label>Convênios</label>
                <input type="text" name="convenios" value="<?= valor($editar, 'convenios') ?>">
            </div>

            <div>
                <label>Atendimento</label>
                <input type="text" name="atendimento" value="<?= valor($editar, 'atendimento') ?>">
            </div>

            <div>
                <label>Valor da consulta</label>
                <input type="text" name="valor_consulta" value="<?= valor($editar, 'valor_consulta') ?>">
            </div>

            <div class="adm-check">
                <input type="checkbox" name="ativo" id="ativo" value="1"
                    <?= (!$editar || (int) ($editar['ativo'] ?? 1) === 1) ? 'checked' : '' ?>>
                <label for="ativo" style="margin:0">Aparecer na página pública</label>
            </div>

            <div class="cheio adm-botoes">
                <button type="submit" class="btn-adm azul">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <?= $editar ? 'Salvar alterações' : 'Cadastrar' ?>
                </button>

                <?php if ($editar): ?>
                    <a href="admin_profissionais.php" class="btn-adm claro">Cancelar</a>
                <?php endif; ?>
            </div>

        </form>

    </div>


    <!-- LISTA -->

    <div class="adm-card">

        <h2>Profissionais cadastrados</h2>

        <div class="adm-contador"><?= count($lista) ?> no total</div>

        <?php if (empty($lista)): ?>
            <p style="color:#66738a">Nenhum profissional cadastrado ainda.</p>
        <?php endif; ?>

        <?php foreach ($lista as $p): ?>

            <?php $ativo = (int) ($p['ativo'] ?? 1) === 1; ?>

            <div class="adm-item">

                <div class="adm-foto">
                    <?php if (!empty($p['foto'])): ?>
                        <img src="<?= htmlspecialchars($p['foto']) ?>" alt="">
                    <?php else: ?>
                        <i class="fa-solid fa-user-doctor"></i>
                    <?php endif; ?>
                </div>

                <div class="adm-info">
                    <strong>
                        <?= htmlspecialchars($p['nome']) ?>
                        <span class="adm-badge <?= $ativo ? 'on' : 'off' ?>"><?= $ativo ? 'Visível' : 'Oculto' ?></span>
                    </strong>
                    <span><?= htmlspecialchars($p['especialidade'] ?? '') ?></span>
                </div>

                <div class="adm-acoes">

                    <a class="mini" href="Profissionais.php#prof-<?= (int) $p['id'] ?>" target="_blank">Ver</a>

                    <a class="mini" href="admin_profissionais.php?editar=<?= (int) $p['id'] ?>">Editar</a>

                    <form method="post">
                        <input type="hidden" name="acao" value="alternar">
                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                        <button class="mini"><?= $ativo ? 'Ocultar' : 'Mostrar' ?></button>
                    </form>

                    <form method="post" onsubmit="return confirm('Excluir este profissional de vez? Isso não pode ser desfeito.')">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                        <button class="mini perigo">Excluir</button>
                    </form>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>