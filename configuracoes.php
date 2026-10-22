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


    /* ---------- NOTIFICAÇÕES ---------- */

    if ($secao === 'notificacoes') {

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

    'notificacoes_sistema' => 1,
    'notificacoes_lembretes' => 1,
    'notificacoes_novidades' => 1

];


$stmt = $conexao->prepare(
    "SELECT
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


/* Conta registros do usuário (favoritos, contatos) */

function contarDoUsuario($conexao, $tabela, $usuarioId) {

    $total = 0;

    $stmt = $conexao->prepare(
        "SELECT COUNT(*) AS total FROM $tabela WHERE usuario_id = ?"
    );

    if ($stmt) {

        $stmt->bind_param("i", $usuarioId);

        $stmt->execute();

        $linha = $stmt->get_result()->fetch_assoc();

        $total = (int) ($linha['total'] ?? 0);

        $stmt->close();

    }

    return $total;

}

$totalFavoritos = contarDoUsuario($conexao, 'favoritos', $usuarioId);
$totalContatos  = contarDoUsuario($conexao, 'contatos_profissionais', $usuarioId);


/* Dados para o resumo do topo */

$notifAtivas = (int) $preferencias['notificacoes_sistema']
             + (int) $preferencias['notificacoes_lembretes']
             + (int) $preferencias['notificacoes_novidades'];

$nomeExibido = $_SESSION['nome'] ?? $_SESSION['usuario_nome'] ?? 'Usuário';
$emailExibido = $_SESSION['email'] ?? '';


require_once "includes/header.php";

?>


<link rel="stylesheet" href="css/configuracoes.css">

<?php include "includes/laterais.php"; ?>

<style>

/* Container mais largo para caber o menu lateral */

.config-container {
    max-width: 1000px !important;
}


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


/* ===== Layout com menu lateral ===== */

.config-layout {
    display: grid;
    grid-template-columns: 250px minmax(0, 1fr);
    gap: 20px;
    align-items: start;
}

.config-lateral {
    position: sticky;
    top: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.lateral-card {
    padding: 18px;
    background: #fff;
    border: 1px solid #e3eaf5;
    border-radius: 18px;
    box-shadow: 0 4px 14px rgba(33, 71, 155, .05);
}

.lateral-card h3 {
    margin: 0 0 10px;
    color: #7a8799;
    font-size: .74rem;
    letter-spacing: .8px;
    text-transform: uppercase;
}

.lateral-links {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.lateral-links a,
.lateral-links a:visited {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 9px 10px;
    border-radius: 12px;
    color: #21479b;
    font-size: .88rem;
    font-weight: 600;
    text-decoration: none;
    transition: background .2s, transform .2s;
}

.lateral-links a i {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #eaf2ff;
    color: #3467eb;
    font-size: .82rem;
    transition: background .2s, color .2s;
}

.lateral-links a:hover {
    background: #f5f8ff;
    transform: translateX(3px);
}

.lateral-links a:hover i {
    background: #3467eb;
    color: #fff;
}

.lateral-links .lateral-contador {
    margin-left: auto;
    padding: 2px 9px;
    border-radius: 20px;
    background: #eaf2ff;
    color: #3467eb;
    font-size: .7rem;
    font-weight: 700;
}

.lateral-ajuda {
    background: linear-gradient(135deg, #eaf2ff, #f5f8ff);
    text-align: center;
}

.lateral-ajuda i.icone-grande {
    display: block;
    margin-bottom: 8px;
    color: #3467eb;
    font-size: 1.6rem;
}

.lateral-ajuda strong {
    display: block;
    margin-bottom: 4px;
    color: #21479b;
    font-size: .95rem;
}

.lateral-ajuda p {
    margin: 0 0 12px;
    color: #5b6b82;
    font-size: .8rem;
    line-height: 1.45;
}

.lateral-ajuda a,
.lateral-ajuda a:visited {
    display: inline-block;
    padding: 9px 18px;
    border-radius: 22px;
    background: #3467eb;
    color: #fff;
    font-size: .8rem;
    font-weight: 700;
    text-decoration: none;
    transition: background .2s, transform .2s;
}

.lateral-ajuda a:hover {
    background: #21479b;
    transform: translateY(-2px);
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

.config-link-botao {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
    margin-right: 6px;
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

.dados-grade {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
}

.dados-caixa {
    padding: 16px;
    border-radius: 14px;
    background: #f5f8ff;
    border: 1px solid #e3eaf5;
    text-align: center;
}

.dados-caixa i {
    color: #3467eb;
    font-size: 1.2rem;
}

.dados-caixa strong {
    display: block;
    margin: 6px 0 2px;
    color: #21479b;
    font-size: 1.5rem;
}

.dados-caixa span {
    color: #7a8799;
    font-size: .8rem;
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


@media (max-width: 900px) {

    .config-layout {
        grid-template-columns: 1fr;
    }

    .config-lateral {
        position: static;
        order: 2;
    }

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
                    <i class="fa-solid fa-heart"></i>
                    <?= $totalFavoritos ?> favoritos
                </span>

                <span class="chip">
                    <i class="fa-solid fa-comments"></i>
                    <?= $totalContatos ?> contatos
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


    <div class="config-layout">


        <!-- MENU LATERAL -->

        <aside class="config-lateral">

            <div class="lateral-card">

                <h3>Acesso rápido</h3>

                <nav class="lateral-links">

                    <a href="perfil.php">
                        <i class="fa-solid fa-user"></i> Meu perfil
                    </a>

                    <a href="perfil.php#favoritos">
                        <i class="fa-solid fa-heart"></i> Favoritos
                        <span class="lateral-contador"><?= $totalFavoritos ?></span>
                    </a>

                    <a href="perfil.php#profissionais">
                        <i class="fa-solid fa-comments"></i> Contatos
                        <span class="lateral-contador"><?= $totalContatos ?></span>
                    </a>

                    <a href="minha-trajetoria.php">
                        <i class="fa-solid fa-route"></i> Minha trajetória
                    </a>

                    <a href="minha-checklist.php">
                        <i class="fa-solid fa-list-check"></i> Minha checklist
                    </a>

                    <a href="guia.php">
                        <i class="fa-solid fa-compass"></i> Guia para famílias
                    </a>

                    <a href="Profissionais.php">
                        <i class="fa-solid fa-user-doctor"></i> Profissionais
                    </a>

                    <a href="biblioteca.php">
                        <i class="fa-solid fa-book-open"></i> Biblioteca
                    </a>

                </nav>

            </div>

            <div class="lateral-card lateral-ajuda">

                <i class="fa-solid fa-hand-holding-heart icone-grande"></i>

                <strong>Precisa de ajuda?</strong>

                <p>
                    Tire suas dúvidas ou fale com a nossa equipe.
                </p>

                <a href="contato.php">Fale conosco</a>

            </div>

        </aside>


        <!-- LISTA DE CONFIGURAÇÕES -->

        <div class="config-principal">

            <div class="config-card">


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


                <!-- MEUS DADOS -->

                <button
                    type="button"
                    class="config-item"
                    data-busca="meus dados favoritos contatos atividade resumo"
                    onclick="abrirConfiguracao('dados')"
                >

                    <div class="config-icon">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>

                    <div class="config-info">
                        <h3>Meus dados</h3>
                        <p>Resumo dos seus favoritos e contatos</p>
                    </div>

                    <span class="config-badge">
                        <?= $totalFavoritos + $totalContatos ?> itens
                    </span>

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


                <!-- AJUDA -->

                <button
                    type="button"
                    class="config-item"
                    data-busca="ajuda suporte dúvidas faq contato fale conosco"
                    onclick="abrirConfiguracao('ajuda')"
                >

                    <div class="config-icon">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>

                    <div class="config-info">
                        <h3>Ajuda e suporte</h3>
                        <p>Dúvidas frequentes e fale com a gente</p>
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

function abrirConfiguracao(tipo) {

    const modal = document.getElementById('configModal');
    const titulo = document.getElementById('modalTitulo');
    const body = document.getElementById('modalBody');

    let conteudo = '';


    /* ---------- MINHA CONTA ---------- */

    if (tipo === 'conta') {

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


    /* ---------- MEUS DADOS ---------- */

    else if (tipo === 'dados') {

        titulo.innerText = 'Meus dados';

        conteudo = `

            <div class="dados-grade">

                <div class="dados-caixa">
                    <i class="fa-solid fa-heart"></i>
                    <strong><?= $totalFavoritos ?></strong>
                    <span>Favoritos salvos</span>
                </div>

                <div class="dados-caixa">
                    <i class="fa-solid fa-comments"></i>
                    <strong><?= $totalContatos ?></strong>
                    <span>Contatos realizados</span>
                </div>

            </div>

            <p class="config-info-extra">
                Veja tudo o que você salvou e com quem já conversou no seu perfil.
            </p>

            <a href="perfil.php#favoritos" class="config-link-botao">
                <i class="fa-solid fa-heart"></i>
                Ver favoritos
            </a>

            <a href="perfil.php#profissionais" class="config-link-botao">
                <i class="fa-solid fa-comments"></i>
                Ver contatos
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


    /* ---------- AJUDA ---------- */

    else if (tipo === 'ajuda') {

        titulo.innerText = 'Ajuda e suporte';

        conteudo = `

            <p class="config-info-extra">
                Encontre respostas rápidas ou entre em contato com a nossa equipe.
            </p>

            <a href="faq.php" class="config-link-botao">
                <i class="fa-solid fa-circle-question"></i>
                Dúvidas frequentes
            </a>

            <a href="contato.php" class="config-link-botao">
                <i class="fa-solid fa-envelope"></i>
                Fale conosco
            </a>

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

                <button type="button" onclick="window.location.href='termos.php'">
                    Visualizar termos
                </button>

            </div>

            <div class="document-box">

                <i class="fa-solid fa-shield-halved"></i>

                <strong>Política de privacidade</strong>

                <p>Consulte a política de privacidade do ForTEA.</p>

                <button type="button" onclick="window.location.href='privacidade.php'">
                    Visualizar política
                </button>

            </div>

        `;

    }


    /* Esconde o botão Salvar onde não há nada para salvar */

    const semSalvar = ['conta', 'dados', 'ajuda', 'termos'].includes(tipo);

    document.querySelector('.btn-salvar').style.display = semSalvar ? 'none' : '';


    body.innerHTML = conteudo;

    modal.classList.add('active');

    document.body.style.overflow = 'hidden';

}


function fecharConfiguracao() {

    document.getElementById('configModal').classList.remove('active');

    document.body.style.overflow = '';

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

</script>