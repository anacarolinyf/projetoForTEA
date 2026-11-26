<div class="pagina-contaa">

    <link rel="stylesheet" href="css/style.css">
     <link
        rel="stylesheet"
        href="css/responsivo.css"
    >
    <linkrel="icon" type="image" href="img/logoo.png">

    <div class="container-conta">

        <div class="conta-imagem">
    


            <a href="index.php" class="voltar-site">
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

            <h2>Entrar</h2>

            <p class="conta-subtitulo">
                Acesse sua conta para continuar.
            </p>

            <form action="processa_login.php" method="POST">

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
                        placeholder="Digite sua senha"
                        required
                    >
                </div>


                <div class="opcoes-login">

                    <label class="lembrar">
                        <input type="checkbox">
                        Lembrar de mim
                    </label>

                    <a href="redefinir_senha.php" class="link">
                        Esqueci minha senha
                    </a>

                </div>


                <button type="submit" class="botao-conta">
                    Entrar
                </button>

            </form>


            <p class="troca-conta">
                Ainda não possui uma conta?
                <a href="cadastro.php">Cadastre-se</a>
            </p>

        </div>

    </div>

</div>