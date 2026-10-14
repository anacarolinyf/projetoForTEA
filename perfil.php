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

?>


<!DOCTYPE html>


<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Meu perfil - ForTEA</title>

    <link rel="stylesheet"
          href="css/estilo.css">

    <link rel="stylesheet"
          href="css/perfil.css">

    <link rel="stylesheet"
          href="css/perfil_blocos.css">

    <link rel="icon"
          type="image"
          href="img/logoo.png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>


<body>

<?php include "chatbot/chatbot.php"; ?>

<link rel="stylesheet" href="chatbot/chatbot.css">
<script src="chatbot/chatbot.js"></script>


<header>

    <div class="topo">

        <div class="logo">

            <img src="img/logoo.png"
                 alt="Logo">

            <h2>ForTEA</h2>

        </div>


        <div class="acoes">

            <div class="pesquisa">

                <input type="text"
                       placeholder="O que você procura?">

            </div>


            <a href="perfil.php"
               class="usuario-logado">

                <i class="fa-regular fa-user"></i>

                <?= htmlspecialchars($nomeUsuario) ?>

            </a>

            <a href="configuracoes.php" class="menu-configuracoes">
                    <i class="fa-solid fa-gear"></i>
                    
                </a>

        </div>

    </div>


    <nav class="menu">

        <ul class="menu-links">

            <li>
                <a href="index.php">Início</a>
            </li>

            <li>
                <a href="sobre.php">Sobre</a>
            </li>

            <li>
                <a href="guia.php">Guia para Famílias</a>
            </li>

            <li>
                <a href="educacaoinclusiva.php">
                    Educação Inclusiva
                </a>
            </li>

            <li>
                <a href="biblioteca.php">
                    Biblioteca
                </a>
            </li>

            <li>
                <a href="direitos.php">
                    Direitos
                </a>
            </li>

            <li>
                <a href="faq.php">FAQ</a>
            </li>

            <li>
                <a href="contato.php">Contato</a>
            </li>

        </ul>

    </nav>

</header>


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
                    Médicos e profissionais que você salvou.
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

                        <a class="pb-fav"
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
                        ele aparecerá aqui.
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
                                   class="pb-botao">
                                    <i class="fa-brands fa-whatsapp"></i>
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

</script>


</body>

</html>