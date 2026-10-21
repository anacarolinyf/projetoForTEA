<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION['usuario_id'])) {

    header("Location: login.php");
    exit;

}

$usuarioId = (int) $_SESSION['usuario_id'];

$sucesso = '';
$erro = '';


/* Marca a opção selecionada nos <select> */
function sel($atual, $valor) {
    return $atual === $valor ? 'selected' : '';
}


$stmt = $conexao->prepare(
    "INSERT IGNORE INTO preferencias_usuario (usuario_id)
     VALUES (?)"
);

if ($stmt) {

    $stmt->bind_param("i", $usuarioId);

    $stmt->execute();

    $stmt->close();

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $secao = $_POST['secao'] ?? '';


    /* ---------- APARÊNCIA ---------- */

    if ($secao === 'aparencia') {

        $modoEscuro = isset($_POST['modo_escuro']) ? 1 : 0;

        $stmt = $conexao->prepare(
            "UPDATE preferencias_usuario
             SET modo_escuro = ?
             WHERE usuario_id = ?"
        );

        if ($stmt) {

            $stmt->bind_param("ii", $modoEscuro, $usuarioId);

            if ($stmt->execute()) {
                $sucesso = "Aparência atualizada com sucesso!";
            } else {
                $erro = "Não foi possível salvar a aparência.";
            }

            $stmt->close();

        }

    }


    /* ---------- ACESSIBILIDADE ---------- */

    elseif ($secao === 'acessibilidade') {

        $reduzirAnimacoes = isset($_POST['reduzir_animacoes']) ? 1 : 0;

        $tamanhoFonte = $_POST['tamanho_fonte'] ?? 'normal';

        $daltonismo = $_POST['daltonismo'] ?? 'nenhum';

        $tamanhosPermitidos = ['pequena', 'normal', 'grande', 'muito_grande'];

        if (!in_array($tamanhoFonte, $tamanhosPermitidos, true)) {
            $tamanhoFonte = 'normal';
        }

        $daltonismoPermitido = [
            'nenhum',
            'protanopia',
            'deuteranopia',
            'tritanopia',
            'acromatopsia'
        ];

        if (!in_array($daltonismo, $daltonismoPermitido, true)) {
            $daltonismo = 'nenhum';
        }

        $stmt = $conexao->prepare(
            "UPDATE preferencias_usuario
             SET
                reduzir_animacoes = ?,
                tamanho_fonte = ?,
                daltonismo = ?
             WHERE usuario_id = ?"
        );

        if ($stmt) {

            $stmt->bind_param(
                "issi",
                $reduzirAnimacoes,
                $tamanhoFonte,
                $daltonismo,
                $usuarioId
            );

            if ($stmt->execute()) {
                $sucesso = "Acessibilidade atualizada com sucesso!";
            } else {
                $erro = "Não foi possível salvar a acessibilidade.";
            }

            $stmt->close();

        }

    }


    /* ---------- NOTIFICAÇÕES ---------- */

    elseif ($secao === 'notificacoes') {

        $notificacoesSistema   = isset($_POST['notificacoes_sistema']) ? 1 : 0;
        $notificacoesLembretes = isset($_POST['notificacoes_lembretes']) ? 1 : 0;
        $notificacoesNovidades = isset($_POST['notificacoes_novidades']) ? 1 : 0;

        $stmt = $conexao->prepare(
            "UPDATE preferencias_usuario
             SET
                notificacoes_sistema = ?,
                notificacoes_lembretes = ?,
                notificacoes_novidades = ?
             WHERE usuario_id = ?"
        );

        if ($stmt) {

            $stmt->bind_param(
                "iiii",
                $notificacoesSistema,
                $notificacoesLembretes,
                $notificacoesNovidades,
                $usuarioId
            );

            if ($stmt->execute()) {
                $sucesso = "Preferências de notificações salvas!";
            } else {
                $erro = "Não foi possível salvar as notificações.";
            }

            $stmt->close();

        }

    }


    /* ---------- PRIVACIDADE / TROCAR SENHA ---------- */

    elseif ($secao === 'privacidade') {

        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha  = $_POST['nova_senha'] ?? '';
        $confirmar  = $_POST['confirmar_senha'] ?? '';

        if ($senhaAtual === '' || $novaSenha === '' || $confirmar === '') {

            $erro = "Preencha todos os campos de senha.";

        } elseif (strlen($novaSenha) < 8) {

            $erro = "A nova senha precisa ter pelo menos 8 caracteres.";

        } elseif ($novaSenha !== $confirmar) {

            $erro = "A confirmação não é igual à nova senha.";

        } else {

            $stmt = $conexao->prepare(
                "SELECT senha FROM usuarios WHERE id = ?"
            );

            $stmt->bind_param("i", $usuarioId);
            $stmt->execute();

            $linha = $stmt->get_result()->fetch_assoc();
            $hashAtual = $linha['senha'] ?? '';

            $stmt->close();

            if (!password_verify($senhaAtual, $hashAtual)) {

                $erro = "A senha atual está incorreta.";

            } else {

                $novoHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                $stmt = $conexao->prepare(
                    "UPDATE usuarios SET senha = ? WHERE id = ?"
                );

                $stmt->bind_param("si", $novoHash, $usuarioId);

                if ($stmt->execute()) {
                    $sucesso = "Senha alterada com sucesso!";
                } else {
                    $erro = "Não foi possível alterar a senha.";
                }

                $stmt->close();

            }

        }

    }

}


$preferencias = [

    'modo_escuro' => 0,
    'reduzir_animacoes' => 0,
    'tamanho_fonte' => 'normal',
    'daltonismo' => 'nenhum',
    'notificacoes_sistema' => 1,
    'notificacoes_lembretes' => 1,
    'notificacoes_novidades' => 1

];


$stmt = $conexao->prepare(
    "SELECT
        modo_escuro,
        reduzir_animacoes,
        tamanho_fonte,
        daltonismo,
        notificacoes_sistema,
        notificacoes_lembretes,
        notificacoes_novidades
     FROM preferencias_usuario
     WHERE usuario_id = ?
     LIMIT 1"
);

if ($stmt) {

    $stmt->bind_param("i", $usuarioId);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $preferencias = array_merge(
            $preferencias,
            $resultado->fetch_assoc()
        );

    }

    $stmt->close();

}


$config = [];

$resultadoConfig = $conexao->query(
    "SELECT chave, valor FROM configuracoes"
);

if ($resultadoConfig) {

    while ($row = $resultadoConfig->fetch_assoc()) {
        $config[$row['chave']] = $row['valor'];
    }

}


/* Dados para o resumo do topo */

$notifAtivas = (int) $preferencias['notificacoes_sistema']
             + (int) $preferencias['notificacoes_lembretes']
             + (int) $preferencias['notificacoes_novidades'];

$nomesFonte = [
    'pequena'      => 'Pequena',
    'normal'       => 'Normal',
    'grande'       => 'Grande',
    'muito_grande' => 'Muito grande'
];

$nomeExibido = $_SESSION['nome'] ?? $_SESSION['usuario_nome'] ?? 'Usuário';
$emailExibido = $_SESSION['email'] ?? '';


require_once "includes/header.php";

?>


<link rel="stylesheet" href="css/configuracoes.css">
<style>
/* ===== Resumo do topo ===== */

.config-resumo {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #21479b, #3467eb);
    border-radius: 20px;
    color: #fff;
    box-shadow: 0 12px 30px rgba(33, 71, 155, .25);
}

.config-resumo-avatar {
    width: 64px;
    height: 64px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, .2);
    font-size: 1.6rem;
    font-weight: 700;
}

.config-resumo-info {
    flex: 1;
    min-width: 0;
}

.config-resumo-info h2 {
    margin: 0;
    font-size: 1.2rem;
    color: #fff;
}

.config-resumo-info p {
    margin: 2px 0 10px;
    opacity: .85;
    font-size: .9rem;
    color: #fff;
}

.config-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.config-chips .chip {
    padding: 5px 12px;
    border-radius: 20px;
    background: rgba(255, 255, 255, .18);
    font-size: .75rem;
}

.config-chips .chip i {
    margin-right: 5px;
}

.config-resumo-botao {
    padding: 10px 18px;
    border-radius: 22px;
    background: #fff;
    color: #21479b;
    font-weight: 700;
    font-size: .85rem;
    text-decoration: none;
    white-space: nowrap;
    transition: transform .2s;
}

.config-resumo-botao:hover {
    transform: translateY(-2px);
}


/* ===== Busca ===== */

.config-busca {
    position: relative;
    margin-bottom: 16px;
}

.config-busca i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.config-busca input {
    width: 100%;
    box-sizing: border-box;
    padding: 13px 16px 13px 44px;
    border: 1px solid #e3eaf5;
    border-radius: 14px;
    background: #fff;
    font-size: .92rem;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}

.config-busca input:focus {
    border-color: #3467eb;
    box-shadow: 0 0 0 3px rgba(52, 103, 235, .12);
}

.config-vazio {
    margin-top: 20px;
    text-align: center;
    color: #7a8799;
}

.config-vazio i {
    display: block;
    margin-bottom: 8px;
    font-size: 1.6rem;
    color: #b4bfdc;
}


/* ===== Itens da lista ===== */

.config-badge {
    padding: 4px 12px;
    margin-right: 10px;
    border-radius: 20px;
    background: #eaf2ff;
    color: #3467eb;
    font-size: .72rem;
    font-weight: 700;
    white-space: nowrap;
}

.config-item {
    transition: background .2s, transform .2s;
}

.config-item:hover {
    background: #f5f8ff;
    transform: translateX(3px);
}


/* ===== Campos do modal ===== */

.config-select {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #e3eaf5;
    border-radius: 12px;
    background: #fff;
    font-size: .92rem;
}

.config-link-botao {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
    padding: 11px 20px;
    border-radius: 22px;
    background: #3467eb;
    color: #fff;
    font-size: .85rem;
    font-weight: 700;
    text-decoration: none;
    transition: background .2s, transform .2s;
}

.config-link-botao:hover {
    background: #21479b;
    transform: translateY(-2px);
}

.senha-forca {
    height: 6px;
    margin-top: 8px;
    border-radius: 6px;
    background: #e8eefb;
    overflow: hidden;
}

.senha-forca div {
    width: 0;
    height: 100%;
    border-radius: 6px;
    transition: width .3s ease, background .3s ease;
}

.senha-dica {
    display: block;
    margin-top: 6px;
    color: #7a8799;
    font-size: .78rem;
}

.mostrar-senha {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
    color: #475569;
    font-size: .85rem;
    cursor: pointer;
}


/* ===== Prévia do modo escuro ===== */

body.modo-escuro {
    background: #0f172a !important;
    color: #e2e8f0;
}

body.modo-escuro .config-card,
body.modo-escuro .config-modal-content,
body.modo-escuro .config-busca input,
body.modo-escuro .config-select,
body.modo-escuro .config-item {
    background: #1e293b;
    color: #e2e8f0;
    border-color: #334155;
}

body.modo-escuro .config-item:hover {
    background: #26354d;
}

body.modo-escuro .config-info h3,
body.modo-escuro .config-header h1,
body.modo-escuro .config-header p,
body.modo-escuro .config-info p,
body.modo-escuro .modal-header h2,
body.modo-escuro .config-option strong,
body.modo-escuro .config-option p,
body.modo-escuro .form-group label {
    color: #e2e8f0;
}

body.modo-escuro .config-badge {
    background: #26354d;
    color: #8fb0ff;
}


/* ===== Reduzir animações ===== */

body.sem-animacoes *,
body.sem-animacoes *::before,
body.sem-animacoes *::after {
    transition: none !important;
    animation: none !important;
}


@media (max-width: 700px) {

    .config-resumo {
        flex-direction: column;
        text-align: center;
    }

    .config-chips {
        justify-content: center;
    }

}

</style>

<div class="config-container">


    <div class="config-header">

        <h1>

            <i class="fa-solid fa-gear"></i>

            Configurações

        </h1>

        <p>

            Gerencie sua conta e suas preferências

        </p>

    </div>


    <?php if (!empty($sucesso)): ?>

        <div class="alert-success">

            <i class="fa-solid fa-circle-check"></i>

            <?= htmlspecialchars($sucesso) ?>

        </div>

    <?php endif; ?>


    <?php if (!empty($erro)): ?>

        <div class="alert-error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>


    <!-- RESUMO -->

    <div class="config-resumo">

        <div class="config-resumo-avatar">
            <?= strtoupper(mb_substr($nomeExibido, 0, 1)) ?>
        </div>

        <div class="config-resumo-info">

            <h2><?= htmlspecialchars($nomeExibido) ?></h2>

            <p><?= htmlspecialchars($emailExibido) ?></p>

            <div class="config-chips">

                <span class="chip">
                    <i class="fa-solid fa-moon"></i>
                    <?= $preferencias['modo_escuro'] ? 'Modo escuro' : 'Modo claro' ?>
                </span>

                <span class="chip">
                    <i class="fa-solid fa-text-height"></i>
                    Fonte <?= $nomesFonte[$preferencias['tamanho_fonte']] ?? 'Normal' ?>
                </span>

                <span class="chip">
                    <i class="fa-solid fa-bell"></i>
                    <?= $notifAtivas ?> de 3 notificações
                </span>

            </div>

        </div>

        <a href="perfil.php" class="config-resumo-botao">
            Ver perfil
            <i class="fa-solid fa-arrow-right"></i>
        </a>

    </div>


    <!-- BUSCA -->

    <div class="config-busca">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            type="text"
            id="buscaConfig"
            placeholder="Buscar nas configurações..."
            oninput="filtrarConfig(this.value)"
        >

    </div>


    <div class="config-card">


        <!-- APARÊNCIA -->

        <button
            type="button"
            class="config-item"
            data-busca="aparência modo escuro claro tema"
            onclick="abrirConfiguracao('aparencia')"
        >

            <div class="config-icon">
                <i class="fa-solid fa-palette"></i>
            </div>

            <div class="config-info">
                <h3>Aparência</h3>
                <p>Modo claro ou escuro</p>
            </div>

            <span class="config-badge">
                <?= $preferencias['modo_escuro'] ? 'Escuro' : 'Claro' ?>
            </span>

            <div class="config-arrow">
                <i class="fa-solid fa-chevron-right"></i>
            </div>

        </button>


        <!-- ACESSIBILIDADE -->

        <button
            type="button"
            class="config-item"
            data-busca="acessibilidade fonte tamanho daltonismo animações"
            onclick="abrirConfiguracao('acessibilidade')"
        >

            <div class="config-icon">
                <i class="fa-solid fa-universal-access"></i>
            </div>

            <div class="config-info">
                <h3>Acessibilidade</h3>
                <p>Tamanho da fonte, daltonismo e animações</p>
            </div>

            <span class="config-badge">
                <?= $nomesFonte[$preferencias['tamanho_fonte']] ?? 'Normal' ?>
            </span>

            <div class="config-arrow">
                <i class="fa-solid fa-chevron-right"></i>
            </div>

        </button>


        <!-- MINHA CONTA -->

        <button
            type="button"
            class="config-item"
            data-busca="minha conta nome e-mail email foto informações pessoais"
            onclick="abrirConfiguracao('conta')"
        >

            <div class="config-icon">
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="config-info">
                <h3>Minha conta</h3>
                <p>Nome, e-mail, foto e informações pessoais</p>
            </div>

            <div class="config-arrow">
                <i class="fa-solid fa-chevron-right"></i>
            </div>

        </button>


        <!-- NOTIFICAÇÕES -->

        <button
            type="button"
            class="config-item"
            data-busca="notificações lembretes novidades avisos"
            onclick="abrirConfiguracao('notificacoes')"
        >

            <div class="config-icon">
                <i class="fa-solid fa-bell"></i>
            </div>

            <div class="config-info">
                <h3>Notificações</h3>
                <p>Escolha quais notificações deseja receber</p>
            </div>

            <span class="config-badge">
                <?= $notifAtivas ?>/3 ativas
            </span>

            <div class="config-arrow">
                <i class="fa-solid fa-chevron-right"></i>
            </div>

        </button>


        <!-- PRIVACIDADE -->

        <button
            type="button"
            class="config-item"
            data-busca="privacidade segurança senha"
            onclick="abrirConfiguracao('privacidade')"
        >

            <div class="config-icon">
                <i class="fa-solid fa-lock"></i>
            </div>

            <div class="config-info">
                <h3>Privacidade e segurança</h3>
                <p>Senha, segurança e privacidade</p>
            </div>

            <div class="config-arrow">
                <i class="fa-solid fa-chevron-right"></i>
            </div>

        </button>


        <!-- TERMOS -->

        <button
            type="button"
            class="config-item"
            data-busca="termos privacidade documentos política"
            onclick="abrirConfiguracao('termos')"
        >

            <div class="config-icon">
                <i class="fa-solid fa-file-lines"></i>
            </div>

            <div class="config-info">
                <h3>Termos e privacidade</h3>
                <p>Consulte nossos documentos</p>
            </div>

            <div class="config-arrow">
                <i class="fa-solid fa-chevron-right"></i>
            </div>

        </button>

    </div>


    <div class="config-vazio" id="configVazio" style="display:none;">
        <i class="fa-solid fa-magnifying-glass"></i>
        <p>Nenhuma configuração encontrada.</p>
    </div>

</div>


<div
    id="configModal"
    class="config-modal"
    onclick="fecharModalFora(event)"
>

    <div
        class="config-modal-content"
        onclick="event.stopPropagation()"
    >

        <div class="modal-header">

            <h2 id="modalTitulo">
                Configuração
            </h2>

            <button
                type="button"
                class="modal-close"
                onclick="fecharConfiguracao()"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <form
            method="POST"
            id="configForm"
        >

            <div
                id="modalBody"
                class="modal-body"
            ></div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-cancelar"
                    onclick="fecharConfiguracao()"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="btn-salvar"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Salvar alterações
                </button>

            </div>

        </form>

    </div>

</div>


<script>

/* Preferências já salvas (usadas para aplicar e para desfazer a prévia) */

const PREFS = {
    escuro: <?= (int) $preferencias['modo_escuro'] ?>,
    fonte: '<?= htmlspecialchars($preferencias['tamanho_fonte'], ENT_QUOTES) ?>',
    reduzirAnimacoes: <?= (int) $preferencias['reduzir_animacoes'] ?>
};


function previaEscuro(ligado) {
    document.body.classList.toggle('modo-escuro', ligado);
}

function previaFonte(tamanho) {

    const mapa = {
        pequena: '14px',
        normal: '16px',
        grande: '18px',
        muito_grande: '20px'
    };

    document.documentElement.style.fontSize = mapa[tamanho] || '16px';

}

function aplicarPreferenciasSalvas() {
    previaEscuro(!!PREFS.escuro);
    previaFonte(PREFS.fonte);
    document.body.classList.toggle('sem-animacoes', !!PREFS.reduzirAnimacoes);
}


function abrirConfiguracao(tipo) {

    const modal = document.getElementById('configModal');
    const titulo = document.getElementById('modalTitulo');
    const body = document.getElementById('modalBody');

    let conteudo = '';


    /* ---------- APARÊNCIA ---------- */

    if (tipo === 'aparencia') {

        titulo.innerText = 'Aparência';

        conteudo = `

            <input type="hidden" name="secao" value="aparencia">

            <div class="config-option">

                <div>
                    <strong>Modo escuro</strong>
                    <p>Reduz o brilho da tela e descansa a vista.</p>
                </div>

                <label class="switch">
                    <input
                        type="checkbox"
                        name="modo_escuro"
                        onchange="previaEscuro(this.checked)"
                        <?= $preferencias['modo_escuro'] ? 'checked' : '' ?>
                    >
                    <span class="slider"></span>
                </label>

            </div>

            <p class="config-info-extra">
                Você vê a prévia na hora. Clique em "Salvar alterações" para manter.
            </p>

        `;

    }


    /* ---------- ACESSIBILIDADE ---------- */

    else if (tipo === 'acessibilidade') {

        titulo.innerText = 'Acessibilidade';

        conteudo = `

            <input type="hidden" name="secao" value="acessibilidade">

            <div class="form-group">

                <label>Tamanho da fonte</label>

                <select
                    name="tamanho_fonte"
                    class="config-select"
                    onchange="previaFonte(this.value)"
                >
                    <option value="pequena" <?= sel($preferencias['tamanho_fonte'], 'pequena') ?>>Pequena</option>
                    <option value="normal" <?= sel($preferencias['tamanho_fonte'], 'normal') ?>>Normal</option>
                    <option value="grande" <?= sel($preferencias['tamanho_fonte'], 'grande') ?>>Grande</option>
                    <option value="muito_grande" <?= sel($preferencias['tamanho_fonte'], 'muito_grande') ?>>Muito grande</option>
                </select>

            </div>

            <div class="form-group">

                <label>Daltonismo</label>

                <select name="daltonismo" class="config-select">
                    <option value="nenhum" <?= sel($preferencias['daltonismo'], 'nenhum') ?>>Nenhum</option>
                    <option value="protanopia" <?= sel($preferencias['daltonismo'], 'protanopia') ?>>Protanopia</option>
                    <option value="deuteranopia" <?= sel($preferencias['daltonismo'], 'deuteranopia') ?>>Deuteranopia</option>
                    <option value="tritanopia" <?= sel($preferencias['daltonismo'], 'tritanopia') ?>>Tritanopia</option>
                    <option value="acromatopsia" <?= sel($preferencias['daltonismo'], 'acromatopsia') ?>>Acromatopsia</option>
                </select>

            </div>

            <div class="config-option">

                <div>
                    <strong>Reduzir animações</strong>
                    <p>Diminui movimentos e transições na tela.</p>
                </div>

                <label class="switch">
                    <input
                        type="checkbox"
                        name="reduzir_animacoes"
                        <?= $preferencias['reduzir_animacoes'] ? 'checked' : '' ?>
                    >
                    <span class="slider"></span>
                </label>

            </div>

        `;

    }


    /* ---------- MINHA CONTA ---------- */

    else if (tipo === 'conta') {

        titulo.innerText = 'Minha conta';

        conteudo = `

            <div class="form-group">
                <label>Nome</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($nomeExibido) ?>"
                    disabled
                >
            </div>

            <div class="form-group">
                <label>E-mail</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($emailExibido) ?>"
                    disabled
                >
            </div>

            <p class="config-info-extra">
                Para alterar seus dados pessoais, utilize a opção de edição do perfil.
            </p>

            <a href="editar_perfil.php" class="config-link-botao">
                <i class="fa-solid fa-pen"></i>
                Editar perfil
            </a>

        `;

    }


    /* ---------- NOTIFICAÇÕES ---------- */

    else if (tipo === 'notificacoes') {

        titulo.innerText = 'Notificações';

        conteudo = `

            <input type="hidden" name="secao" value="notificacoes">

            <div class="config-option">

                <div>
                    <strong>Notificações do sistema</strong>
                    <p>Receber notificações importantes do ForTEA.</p>
                </div>

                <label class="switch">
                    <input
                        type="checkbox"
                        name="notificacoes_sistema"
                        <?= $preferencias['notificacoes_sistema'] ? 'checked' : '' ?>
                    >
                    <span class="slider"></span>
                </label>

            </div>

            <div class="config-option">

                <div>
                    <strong>Lembretes</strong>
                    <p>Receber lembretes e avisos.</p>
                </div>

                <label class="switch">
                    <input
                        type="checkbox"
                        name="notificacoes_lembretes"
                        <?= $preferencias['notificacoes_lembretes'] ? 'checked' : '' ?>
                    >
                    <span class="slider"></span>
                </label>

            </div>

            <div class="config-option">

                <div>
                    <strong>Novidades do ForTEA</strong>
                    <p>Receber novidades e atualizações.</p>
                </div>

                <label class="switch">
                    <input
                        type="checkbox"
                        name="notificacoes_novidades"
                        <?= $preferencias['notificacoes_novidades'] ? 'checked' : '' ?>
                    >
                    <span class="slider"></span>
                </label>

            </div>

        `;

    }


    /* ---------- PRIVACIDADE ---------- */

    else if (tipo === 'privacidade') {

        titulo.innerText = 'Privacidade e segurança';

        conteudo = `

            <input type="hidden" name="secao" value="privacidade">

            <div class="form-group">
                <label>Senha atual</label>
                <input type="password" name="senha_atual" class="campo-senha" autocomplete="current-password">
            </div>

            <div class="form-group">
                <label>Nova senha</label>
                <input
                    type="password"
                    name="nova_senha"
                    class="campo-senha"
                    autocomplete="new-password"
                    oninput="forcaSenha(this.value)"
                >
                <div class="senha-forca"><div id="senhaForcaBarra"></div></div>
                <small id="senhaForcaTexto" class="senha-dica">Use pelo menos 8 caracteres.</small>
            </div>

            <div class="form-group">
                <label>Confirmar nova senha</label>
                <input type="password" name="confirmar_senha" class="campo-senha" autocomplete="new-password">
            </div>

            <label class="mostrar-senha">
                <input type="checkbox" onchange="mostrarSenhas(this.checked)">
                Mostrar senhas
            </label>

        `;

    }


    /* ---------- TERMOS ---------- */

    else if (tipo === 'termos') {

        titulo.innerText = 'Termos e privacidade';

        conteudo = `

            <div class="document-box">

                <i class="fa-solid fa-file-contract"></i>

                <strong>Termos de uso</strong>

                <p>Consulte os termos de utilização da plataforma ForTEA.</p>

                <button type="button" onclick="alert('Página de termos será adicionada.')">
                    Visualizar termos
                </button>

            </div>

            <div class="document-box">

                <i class="fa-solid fa-shield-halved"></i>

                <strong>Política de privacidade</strong>

                <p>Consulte a política de privacidade do ForTEA.</p>

                <button type="button" onclick="alert('Página de privacidade será adicionada.')">
                    Visualizar política
                </button>

            </div>

        `;

    }


    /* Esconde o botão Salvar onde não há nada para salvar */

    const semSalvar = (tipo === 'conta' || tipo === 'termos');

    document.querySelector('.btn-salvar').style.display = semSalvar ? 'none' : '';


    body.innerHTML = conteudo;

    modal.classList.add('active');

    document.body.style.overflow = 'hidden';

}


function fecharConfiguracao() {

    document.getElementById('configModal').classList.remove('active');

    document.body.style.overflow = '';

    /* Desfaz prévias que não foram salvas */
    aplicarPreferenciasSalvas();

}


function fecharModalFora(event) {

    const modal = document.getElementById('configModal');

    if (event.target === modal) {
        fecharConfiguracao();
    }

}


document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {
        fecharConfiguracao();
    }

});


/* ---------- Busca ---------- */

function filtrarConfig(texto) {

    const termo = texto.toLowerCase().trim();

    let visiveis = 0;

    document.querySelectorAll('.config-item').forEach(function (item) {

        const base = (item.dataset.busca || '') + ' ' + item.innerText.toLowerCase();

        const mostrar = base.includes(termo);

        item.style.display = mostrar ? '' : 'none';

        if (mostrar) visiveis++;

    });

    document.getElementById('configVazio').style.display =
        visiveis === 0 ? '' : 'none';

}


/* ---------- Senha ---------- */

function mostrarSenhas(mostrar) {

    document.querySelectorAll('.campo-senha').forEach(function (campo) {
        campo.type = mostrar ? 'text' : 'password';
    });

}

function forcaSenha(valor) {

    const barra = document.getElementById('senhaForcaBarra');
    const texto = document.getElementById('senhaForcaTexto');

    let pontos = 0;

    if (valor.length >= 8) pontos++;
    if (/[A-Z]/.test(valor) && /[a-z]/.test(valor)) pontos++;
    if (/\d/.test(valor)) pontos++;
    if (/[^A-Za-z0-9]/.test(valor)) pontos++;

    const niveis = [
        { largura: '8%',   cor: '#e53935', msg: 'Muito fraca' },
        { largura: '30%',  cor: '#f28c28', msg: 'Fraca' },
        { largura: '55%',  cor: '#f2c628', msg: 'Razoável' },
        { largura: '80%',  cor: '#7cb342', msg: 'Boa' },
        { largura: '100%', cor: '#2e9e4f', msg: 'Forte' }
    ];

    if (valor === '') {
        barra.style.width = '0';
        texto.textContent = 'Use pelo menos 8 caracteres.';
        return;
    }

    const nivel = niveis[pontos];

    barra.style.width = nivel.largura;
    barra.style.background = nivel.cor;
    texto.textContent = 'Força da senha: ' + nivel.msg;

}


document.addEventListener('DOMContentLoaded', aplicarPreferenciasSalvas);

</script>