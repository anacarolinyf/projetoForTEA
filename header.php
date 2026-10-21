<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nomeUsuario =
    $_SESSION['nome']
    ?? $_SESSION['usuario_nome']
    ?? null;

$usuarioId =
    $_SESSION['usuario_id']
    ?? null;




$configSite = [];

if (isset($conexao)) {

    $resultadoConfig = $conexao->query(
        "SELECT chave, valor FROM configuracoes"
    );

    if ($resultadoConfig) {

        while ($row = $resultadoConfig->fetch_assoc()) {

            $configSite[$row['chave']] = $row['valor'];

        }

    }

}




$corPrincipal =
    $configSite['cor_principal']
    ?? '#2454A6';

$corSecundaria =
    $configSite['cor_secundaria']
    ?? '#193F80';




$preferenciasUsuario = [

    'modo_escuro' => 0,

    'reduzir_animacoes' => 0,

    'tamanho_fonte' => 'normal',

    'daltonismo' => 'nenhum'

];




if (
    $usuarioId &&
    isset($conexao)
) {

    $stmtPreferencias = $conexao->prepare(

        "SELECT
            modo_escuro,
            reduzir_animacoes,
            tamanho_fonte,
            daltonismo
         FROM preferencias_usuario
         WHERE usuario_id = ?
         LIMIT 1"

    );


    if ($stmtPreferencias) {

        $stmtPreferencias->bind_param(
            "i",
            $usuarioId
        );

        $stmtPreferencias->execute();


        $resultadoPreferencias =
            $stmtPreferencias->get_result();


        if (
            $resultadoPreferencias &&
            $resultadoPreferencias->num_rows > 0
        ) {

            $dadosPreferencias =
                $resultadoPreferencias->fetch_assoc();


            $preferenciasUsuario =
                array_merge(
                    $preferenciasUsuario,
                    $dadosPreferencias
                );

        }


        $stmtPreferencias->close();

    }

}




$classesBody = [];




if (
    (string)$preferenciasUsuario['modo_escuro']
    === '1'
) {

    $classesBody[] =
        'modo-escuro';

}



if (
    (string)$preferenciasUsuario['reduzir_animacoes']
    === '1'
) {

    $classesBody[] =
        'reduzir-animacoes';

}




$tamanhosPermitidos = [

    'pequena',
    'normal',
    'grande',
    'muito_grande'

];


$tamanhoFonte =
    $preferenciasUsuario['tamanho_fonte'];


if (
    !in_array(
        $tamanhoFonte,
        $tamanhosPermitidos,
        true
    )
) {

    $tamanhoFonte =
        'normal';

}


$classesBody[] =
    'fonte-' . $tamanhoFonte;




$daltonismosPermitidos = [

    'nenhum',
    'protanopia',
    'deuteranopia',
    'tritanopia',
    'acromatopsia'

];


$daltonismo =
    $preferenciasUsuario['daltonismo'];


if (
    !in_array(
        $daltonismo,
        $daltonismosPermitidos,
        true
    )
) {

    $daltonismo =
        'nenhum';

}


if ($daltonismo !== 'nenhum') {

    $classesBody[] =
        'daltonismo-' . $daltonismo;

}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


   

    <link
        rel="stylesheet"
        href="css/estilo.css"
    >

    
    <link
        rel="stylesheet"
        href="css/leisecontato.css"
    >


    
    <link
        rel="stylesheet"
        href="css/configuracoes.css"
    >


    

    <link
        rel="stylesheet"
        href="css/perfil.css"
    >


    <title>ForTEA</title>

    <link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="img/logoatualizada.png"
>

    

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap"
        rel="stylesheet"
    >


    

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >


 
    <style>

        :root {

            --cor-principal:
                <?= htmlspecialchars($corPrincipal) ?>;

            --cor-secundaria:
                <?= htmlspecialchars($corSecundaria) ?>;

        }

    </style>

</head>


<body
    class="<?= htmlspecialchars(
        implode(' ', $classesBody)
    ) ?>"
>


<div vw class="enabled">

    <div vw-access-button class="active"></div>

    <div vw-plugin-wrapper>

        <div class="vw-plugin-top-wrapper"></div>

    </div>

</div>


<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>

<script>

new window.VLibras.Widget(
    "https://vlibras.gov.br/app"
);

</script>


<?php include "chatbot/chatbot.php"; ?>


<link
    rel="stylesheet"
    href="chatbot/chatbot.css"
>


<script src="chatbot/chatbot.js"></script>



<header>

    <div class="topo">

        <div class="logo">

            <img
                src="img/logoatualizada.png"
                alt="Logo ForTEA"
            >


        </div>

        <div class="acoes">
<form class="pesquisa" action="pesquisa.php" method="GET">

    <input
        type="search"
        name="busca"
        placeholder="O que você procura?"
        aria-label="Pesquisar no site"
        required
    >

    <button type="submit" aria-label="Pesquisar">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>

</form>

            <?php if ($nomeUsuario): ?>

                <a
                    href="perfil.php"
                    class="usuario-logado"
                >

                    <i class="fa-regular fa-user"></i>

                    <?= htmlspecialchars(
                        $nomeUsuario
                    ) ?>

                </a>

                <a
                    href="configuracoes.php"
                    class="menu-configuracoes"
                    aria-label="Configurações"
                    title="Configurações"
                >

                    <i class="fa-solid fa-gear"></i>

                </a>


            <?php else: ?>

             <a href="login.php">
    <i class="fa-solid fa-circle-user"></i>
        </a>

            <?php endif; ?>


        </div>

    </div>

    <nav class="menu">

        <ul
            class="menu-links"
            id="js-menu-links"
        >

            <li>
                <a href="index.php">
                    Início
                </a>
            </li>


            <li>
                <a href="sobre.php">
                    Sobre
                </a>
            </li>


            <li>
                <a href="guia.php">
                    Guia para Famílias
                </a>
            </li>


            <li>
                <a href="educacaoinclusiva.php">
                    Educação Inclusiva
                </a>
            </li>


            <li class="menu-dropdown">
    <a href="#" class="biblioteca-link">
        Biblioteca
    </a>

    <div class="submenu-biblioteca">

        <a href="profissionais.php">
            Profissionais especializados 
        </a>

        <a href="materiais.php">
            Materiais Pedagógicos
        </a>

        <a href="noticias.php">
            Notícias
        </a>

        <a href="relatos.php">
            Relatos de Experiências
        </a>

    </div>
</li>
            <li>
                <a href="leis.php">
                    Direitos
                </a>
            </li>


            <li>
                <a href="faq.php">
                    FAQ
                </a>
            </li>


            <li>
                <a href="contato.php">
                    Contato
                </a>
            </li>

              <li>
                <a href="relatos.php">
                  
                </a>
            </li>

            
              <li>
                <a href="material.php">
                  
                </a>
            </li>


        </ul>

    </nav>

        <script src="https://cdn.jsdelivr.net/npm/accessibility-widgets@latest/widget.js"></script>

</header>