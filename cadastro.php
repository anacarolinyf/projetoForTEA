<?php
$erro = $_GET["erro"] ?? "";
$sucesso = $_GET["sucesso"] ?? "";
?>

<div class="pagina-contaa">

    <link rel="stylesheet" href="css/style.css">

    <div class="container-conta">

        <div class="conta-imagem">

          <linkrel="icon" type="image" href="img/logoo.png">


            <a href="login.php" class="voltar-site">
                ← Voltar
            </a>

            <img src="img/loginfoto.png" alt="Família">

            <div class="texto-imagem">
                <h1>Bem-vindo à ForTEA</h1>

                <p>
                    Um espaço de apoio, informação e acolhimento
                    para famílias de pessoas autistas.
                </p>
            </div>

        </div>


        <div class="conta-formulario">

            <div class="logo-conta">
                ForTEA
            </div>

            <h2>Cadastro</h2>

            <p class="conta-subtitulo">
            Crie sua conta para acessar nossa plataforma.
            </p>

            <?php if ($erro): ?>

    <div class="mensagem-erro">
        <?php
        if ($erro == "senhas") {
            echo "As senhas não coincidem.";
        } elseif ($erro == "email") {
            echo "Este e-mail já está cadastrado.";
        } elseif ($erro == "geral") {
            echo "Erro ao cadastrar. Tente novamente.";
        }
        ?>
    </div>

<?php endif; ?>


          <form action="processa_cadastro.php" method="POST">

        <div class="campo">
            <label>Nome completo</label>
            <input
                type="text"
                name="nome"
                placeholder="Digite seu nome"
                required
            >
        </div>

        <div class="campo">
            <label>E-mail</label>
            <input
                type="email"
                name="email"
                placeholder="Digite seu e-mail"
                required
            >
        </div>

        <div class="campo">
            <label>Senha</label>
            <input
                type="password"
                name="senha"
                placeholder="Crie uma senha"
                required
            >
        </div>

        <div class="campo">
            <label>Confirmar senha</label>
            <input
                type="password"
                name="confirmar"
                placeholder="Repita sua senha"
                required
            >
        </div>

        <button type="submit" class="botao-conta">
            Criar minha conta
        </button>

    </form>


            <p class="troca-conta">
                Já possui uma conta?
                <a href="login.php">Acesse agora</a>
            </p>

        </div>

    </div>

</div>