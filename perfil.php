<?php

session_start();

require_once "conexao.php";


/* =========================
   VERIFICA LOGIN
   ========================= */

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}


$usuarioId = $_SESSION['usuario_id'];


/* =========================
   BUSCA USUÁRIO
   ========================= */

$sql = "SELECT * FROM usuarios WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $usuarioId);

$stmt->execute();

$resultado = $stmt->get_result();

$usuario = $resultado->fetch_assoc();


if (!$usuario) {
    session_destroy();

    header("Location: login.php");
    exit;
}


$nomeUsuario = $usuario['nome'];
$emailUsuario = $usuario['email'];

$fotoUsuario = $usuario['foto'] ?? null;


/* =========================
   FAVORITOS
   ========================= */

$favoritos = [];

$sqlFavoritos = "
    SELECT *
    FROM favoritos
    WHERE usuario_id = ?
    ORDER BY id DESC
";

$stmtFavoritos = $conexao->prepare($sqlFavoritos);

$stmtFavoritos->bind_param("i", $usuarioId);

$stmtFavoritos->execute();

$resultadoFavoritos = $stmtFavoritos->get_result();

while ($favorito = $resultadoFavoritos->fetch_assoc()) {

    $favoritos[] = $favorito;

}


/* =========================
   CONTATOS REALIZADOS
   ========================= */

$profissionais = [];

$sqlProfissionais = "
    SELECT p.*, c.ultimo_contato
    FROM contatos_profissionais c
    INNER JOIN profissionais p ON p.id = c.profissional_id
    WHERE c.usuario_id = ?
    ORDER BY c.ultimo_contato DESC
";

$stmtProfissionais = $conexao->prepare($sqlProfissionais);

$stmtProfissionais->bind_param("i", $usuarioId);

$stmtProfissionais->execute();

$resultadoProfissionais = $stmtProfissionais->get_result();

while ($profissional = $resultadoProfissionais->fetch_assoc()) {

    $profissionais[] = $profissional;

}


function iconeFavorito($tipo) {
    switch ($tipo) {
        case 'profissional': return 'fa-user-doctor';
        case 'material':     return 'fa-book-open';
        case 'lei':          return 'fa-scale-balanced';
        default:             return 'fa-heart';
    }
}


require_once "includes/header.php";

?>


<!DOCTYPE html>


<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Meu perfil - ForTEA</title>

    <link rel="stylesheet"
          href="css/estilo.css?v=<?= @filemtime(__DIR__ . '/css/estilo.css') ?>">

    <link rel="stylesheet"
          href="css/perfil.css?v=<?= @filemtime(__DIR__ . '/css/perfil.css') ?>">

    <link rel="stylesheet"
          href="css/perfil_blocos.css?v=<?= @filemtime(__DIR__ . '/css/perfil_blocos.css') ?>">

    <link rel="icon"
          type="image"
          href="img/logoo.png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <!-- =========================
         ESTILO DOS FAVORITOS
         (fica aqui para não depender de cache nem de outro CSS)
         ========================= -->

    <style>

    #favoritos .pb-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 16px;
        margin-top: 22px;
    }

    #favoritos .pb-fav,
    #favoritos .pb-fav:visited {
        display: flex;
        align-items: center;
        gap: 16px;

        padding: 16px 18px;

        background: #ffffff;
        border: 1px solid #e3eaf5;
        border-radius: 16px;

        color: inherit;
        text-decoration: none;

        box-shadow: 0 4px 14px rgba(33, 71, 155, 0.05);

        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;
    }

    #favoritos .pb-fav:hover {
        transform: translateY(-3px);
        border-color: #bfd1f2;
        box-shadow: 0 10px 24px rgba(33, 71, 155, 0.12);
    }

    /* Ícone */

    #favoritos .pb-fav-icone {
        width: 50px;
        height: 50px;
        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 14px;

        background: #eaf2ff;
        color: #3467eb;

        font-size: 1.2rem;

        transition: 0.25s ease;
    }

    #favoritos .pb-fav:hover .pb-fav-icone {
        background: #3467eb;
        color: #ffffff;
    }

    /* Textos */

    #favoritos .pb-fav .pb-info {
        flex: 1;
        min-width: 0;

        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    #favoritos .pb-fav .pb-tipo {
        align-self: flex-start;

        margin-bottom: 5px;
        padding: 3px 10px;

        border-radius: 20px;

        background: #eaf2ff;
        color: #3467eb;

        font-size: 0.66rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    #favoritos .pb-fav h3 {
        margin: 0;

        color: #21479b;

        font-size: 0.98rem;
        line-height: 1.35;
        text-decoration: none;
    }

    #favoritos .pb-fav small {
        color: #7a8799;

        font-size: 0.78rem;
        text-decoration: none;
    }

    /* Setinha */

    #favoritos .pb-fav .pb-seta {
        flex-shrink: 0;

        color: #b4bfdc;
        font-size: 0.85rem;

        transition: 0.25s ease;
    }

    #favoritos .pb-fav:hover .pb-seta {
        color: #3467eb;
        transform: translateX(4px);
    }

    /* Cores por tipo */

    #favoritos .pb-fav-lei .pb-fav-icone,
    #favoritos .pb-fav-lei .pb-tipo {
        background: #edf8ee;
        color: #347a39;
    }

    #favoritos .pb-fav-lei:hover .pb-fav-icone {
        background: #4caf50;
        color: #ffffff;
    }

    #favoritos .pb-fav-material .pb-fav-icone,
    #favoritos .pb-fav-material .pb-tipo {
        background: #fff4e0;
        color: #b36b00;
    }

    #favoritos .pb-fav-material:hover .pb-fav-icone {
        background: #f28c28;
        color: #ffffff;
    }

    @media (max-width: 600px) {
        #favoritos .pb-grid {
            grid-template-columns: 1fr;
        }
    }


    /* =========================
       CONTATOS REALIZADOS
       ========================= */

    #profissionais .pb-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 16px;
        margin-top: 22px;
    }

    #profissionais .pb-card {
        display: flex;
        align-items: center;
        gap: 16px;

        padding: 16px 18px;

        background: #ffffff;
        border: 1px solid #e3eaf5;
        border-radius: 16px;

        box-shadow: 0 4px 14px rgba(33, 71, 155, 0.05);

        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;
    }

    #profissionais .pb-card:hover {
        transform: translateY(-3px);
        border-color: #bfd1f2;
        box-shadow: 0 10px 24px rgba(33, 71, 155, 0.12);
    }

    /* Foto */

    #profissionais .pb-avatar {
        width: 68px;
        height: 68px;
        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: hidden;

        border-radius: 50%;

        background: #eaf2ff;
        color: #3467eb;

        font-size: 1.5rem;

        border: 3px solid #ffffff;
        box-shadow: 0 0 0 2px #dbe6fa;
    }

    #profissionais .pb-avatar img {
        width: 100%;
        height: 100%;

        object-fit: cover;
        object-position: center top;
    }

    /* Textos */

    #profissionais .pb-info {
        flex: 1;
        min-width: 0;

        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    #profissionais .pb-info h3 {
        margin: 0;

        color: #21479b;

        font-size: 0.98rem;
        line-height: 1.35;
    }

    #profissionais .pb-tag {
        padding: 3px 10px;

        border-radius: 20px;

        background: #eaf2ff;
        color: #3467eb;

        font-size: 0.7rem;
        font-weight: 700;
    }

    #profissionais .pb-info small {
        margin-top: 2px;

        color: #7a8799;

        font-size: 0.76rem;
    }

    /* Botão */

    #profissionais .pb-botao,
    #profissionais .pb-botao:visited {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        flex-shrink: 0;

        padding: 10px 16px;

        border-radius: 22px;

        background: #3467eb;
        color: #ffffff;

        text-decoration: none;
        white-space: nowrap;

        font-size: 0.78rem;
        font-weight: 700;

        transition: 0.2s ease;
    }

    #profissionais .pb-botao:hover {
        background: #21479b;
        transform: translateY(-2px);
    }

    #profissionais .pb-botao-zap,
    #profissionais .pb-botao-zap:visited {
        background: #20c967;
    }

    #profissionais .pb-botao-zap:hover {
        background: #19b85b;
    }

    @media (max-width: 600px) {

        #profissionais .pb-grid {
            grid-template-columns: 1fr;
        }

        #profissionais .pb-card {
            flex-wrap: wrap;
        }

        #profissionais .pb-botao {
            width: 100%;
        }
    }

    </style>

</head>

<body>

<?php include "chatbot/chatbot.php"; ?>

<link rel="stylesheet" href="chatbot/chatbot.css">
<script src="chatbot/chatbot.js"></script>


<main class="perfil-pagina">


    <section class="perfil-card">


        <!-- TOPO DO PERFIL -->

        <div class="perfil-topo">


            <div class="perfil-avatar">

                <?php if (!empty($fotoUsuario)): ?>

                    <img src="<?= htmlspecialchars($fotoUsuario) ?>"
                         alt="Foto de perfil">

                <?php else: ?>

                    <?= strtoupper(
                        substr($nomeUsuario, 0, 1)
                    ) ?>

                <?php endif; ?>

            </div>


            <div class="perfil-titulo">

                <h1>
                    <?= htmlspecialchars($nomeUsuario) ?>
                </h1>

                <p>
                    Bem-vindo ao seu perfil ForTEA.
                </p>

            </div>


            <a href="editar_perfil.php"
               class="botao-editar-topo">

                <i class="fa-solid fa-pen"></i>

                Editar perfil

            </a>

        </div>


        <!-- ABAS -->

        <div class="perfil-abas">


            <button class="aba ativa"
                    onclick="mostrarAba('informacoes', this)">

                <i class="fa-regular fa-user"></i>

                Informações

            </button>


            <button class="aba"
                    onclick="mostrarAba('favoritos', this)">

                <i class="fa-regular fa-heart"></i>

                Favoritos

            </button>


            <button class="aba"
                    onclick="mostrarAba('profissionais', this)">

                <i class="fa-regular fa-comments"></i>

                Contatos realizados

            </button>


        </div>


        <!-- =========================
             INFORMAÇÕES
             ========================= -->

        <div id="informacoes"
             class="conteudo-aba ativa">


            <h2>
                Informações da conta
            </h2>


            <div class="perfil-campo">

                <label>
                    Nome completo
                </label>

                <div class="perfil-valor">

                    <?= htmlspecialchars($nomeUsuario) ?>

                </div>

            </div>


            <div class="perfil-campo">

                <label>
                    E-mail
                </label>

                <div class="perfil-valor">

                    <?= htmlspecialchars($emailUsuario) ?>

                </div>

            </div>


        </div>


        <!-- =========================
             FAVORITOS
             ========================= -->

        <div id="favoritos"
             class="conteudo-aba">


            <div class="aba-titulo">

                <h2>
                    Meus favoritos
                </h2>

                <p>
                    Profissionais, materiais e leis que você salvou.
                </p>

            </div>


            <?php if (count($favoritos) > 0): ?>

                <div class="pb-grid">

                    <?php foreach ($favoritos as $fav): ?>

                        <?php
                        $favTipo   = $fav['tipo'] ?? '';
                        $favTitulo = $fav['titulo'] ?? 'Sem título';
                        $favEspec  = $fav['especialidade'] ?? '';
                        $favUrl    = $fav['url'] ?? '#';
                        ?>

                        <a class="pb-fav pb-fav-<?= htmlspecialchars($favTipo) ?>"
                           href="<?= htmlspecialchars($favUrl) ?>">

                            <div class="pb-fav-icone">
                                <i class="fa-solid <?= iconeFavorito($favTipo) ?>"></i>
                            </div>

                            <div class="pb-info">
                                <span class="pb-tipo">
                                    <?= htmlspecialchars(ucfirst($favTipo)) ?>
                                </span>
                                <h3><?= htmlspecialchars($favTitulo) ?></h3>
                                <?php if ($favEspec !== ''): ?>
                                    <small><?= htmlspecialchars($favEspec) ?></small>
                                <?php endif; ?>
                            </div>

                            <i class="fa-solid fa-chevron-right pb-seta"></i>

                        </a>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="estado-vazio">

                    <i class="fa-regular fa-heart"></i>

                    <h3>
                        Você ainda não tem favoritos
                    </h3>

                    <p>
                        Quando você favoritar um profissional,
                        material ou lei, ele aparecerá aqui.
                    </p>

                </div>

            <?php endif; ?>


        </div>


        <!-- =========================
             CONTATOS
             ========================= -->

        <div id="profissionais"
             class="conteudo-aba">


            <div class="aba-titulo">

                <h2>
                    Contatos realizados
                </h2>

                <p>
                    Médicos e profissionais com quem você já conversou.
                </p>

            </div>


            <?php if (count($profissionais) > 0): ?>


                <div class="pb-grid">

                    <?php foreach ($profissionais as $profissional): ?>

                        <?php
                        $zap = preg_replace('/[^0-9]/', '', $profissional['whatsapp'] ?? '');
                        ?>

                        <article class="pb-card">

                            <div class="pb-avatar">
                                <?php if (!empty($profissional['foto'])): ?>
                                    <img src="<?= htmlspecialchars($profissional['foto']) ?>"
                                         alt="Foto de <?= htmlspecialchars($profissional['nome']) ?>">
                                <?php else: ?>
                                    <i class="fa-solid fa-user-doctor"></i>
                                <?php endif; ?>
                            </div>

                            <div class="pb-info">
                                <h3><?= htmlspecialchars($profissional['nome']) ?></h3>

                                <?php if (!empty($profissional['especialidade'])): ?>
                                    <span class="pb-tag">
                                        <?= htmlspecialchars($profissional['especialidade']) ?>
                                    </span>
                                <?php endif; ?>

                                <small>
                                    Último contato:
                                    <?= date('d/m/Y', strtotime($profissional['ultimo_contato'])) ?>
                                </small>
                            </div>

                            <?php if ($zap !== ''): ?>
                                <a href="contato_profissional.php?id=<?= (int) $profissional['id'] ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="pb-botao pb-botao-zap">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    Conversar novamente
                                </a>
                            <?php elseif (!empty($profissional['telefone'])): ?>
                                <?php $tel = preg_replace('/[^0-9]/', '', $profissional['telefone']); ?>
                                <a href="tel:+55<?= $tel ?>"
                                   onclick="registrarLigacao(<?= (int) $profissional['id'] ?>)"
                                   class="pb-botao">
                                    <i class="fa-solid fa-phone"></i>
                                    Conversar novamente
                                </a>
                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>


            <?php else: ?>


                <div class="estado-vazio">

                    <i class="fa-regular fa-comments"></i>

                    <h3>
                        Nenhum contato realizado
                    </h3>

                    <p>
                        Os profissionais com quem você conversar
                        aparecerão aqui.
                    </p>

                </div>


            <?php endif; ?>


        </div>


        <!-- SAIR -->

        <div class="perfil-final">

            <a href="logout.php"
               class="botao-sair">

                <i class="fa-solid fa-right-from-bracket"></i>

                Sair da conta

            </a>

        </div>


    </section>

</main>


<script>

function mostrarAba(nome, botao) {

    const abas = document.querySelectorAll(".conteudo-aba");

    abas.forEach(function(aba) {

        aba.classList.remove("ativa");

    });


    const botoes = document.querySelectorAll(".aba");

    botoes.forEach(function(item) {

        item.classList.remove("ativa");

    });


    document.getElementById(nome).classList.add("ativa");

    botao.classList.add("ativa");

}


// Atualiza a data do último contato ao ligar de novo
function registrarLigacao(id) {

    const dados = new URLSearchParams({ id: id });

    if (navigator.sendBeacon) {
        navigator.sendBeacon('registrar_contato.php', dados);
    } else {
        fetch('registrar_contato.php', { method: 'POST', body: dados, keepalive: true });
    }

}

</script>


</body>

</html>