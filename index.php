<?php include 'includes/header.php'; ?>
<?php
$entrada = isset($_GET["entrada"]) && $_GET["entrada"] === "1";
?>
<link rel="icon" type="image" href="img/logoo.png">

<main>
    <section class="hero">
        <div class="hero-texto">
            <h1>Informação, orientação e acolhimento para toda a família.</h1>
            <p>
                Sabemos que cada jornada é única, por isso criamos um espaço para apoiar famílias e responsáveis com 
                informações confiáveis sobre o TEA, acesso a recursos, direitos e orientações que promovam inclusão e qualidade de vida.
            </p>
            <div class="botoes">
                <a href="guia.php" class="btn-principal">Começar agora</a>
                <a href="leis.php" class="btn-secundario">Conheça seus direitos</a>
            </div>
        </div>
        <div class="hero-imagem">
            <img src="img/banner.png" alt="Familiaa">
        </div>
    </section>

    <section class="cards">
        <div class="card">
            <i class="fa-solid fa-puzzle-piece"></i>
            <h3>Entenda o TEA</h3>
            <p>Conheça as características do autismo com informações confiáveis.</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-users"></i>
            <h3>Guia para Famílias</h3>
            <p>Descubra orientações para seu dia a dia com mais segurança.</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-scale-balanced"></i>
            <h3>Direitos</h3>
            <p>Saiba quais são as leis e benefícios garantidos às pessoas com TEA e como acessá-los.</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-book"></i>
            <h3>Biblioteca</h3>
            <p>Acesse notícias, depoimentos e conteúdos preparados para apoiar sua jornada.</p>
        </div>
    </section>

    <!-- Seção Sobre o Projeto -->
    <section class="sobre-projeto">
        <div class="texto">
            <h2>Por que criamos o ForTEA?</h2>
            <p>
                O ForTEA nasceu da necessidade de reunir em um único lugar informações confiáveis sobre o Transtorno do Espectro Autista. 
               Nosso objetivo é oferecer orientação, apoio e recursos para famílias, educadores e pessoas com TEA, promovendo inclusão, conhecimento, qualidade de vida e uma comunicação mais acolhedora sobre o autismo.
            </p>
            <a href="projeto.php" class="btn-secundario">Conheça o projeto</a>
        </div>
        <div class="icone">
            <i class="fa-solid fa-heart"></i>
        </div>
    </section>

<section class="diagnostico">

    <div class="diagnostico-topo">
        <h2>Recebeu um diagnóstico recentemente?</h2>
        <p>Conheça as principais etapas para iniciar essa jornada com mais confiança</p>
    </div>

    <div class="passos">

        <a href="sobre.php" class="passo">
            <div class="icone">
                <i class="fa-solid fa-puzzle-piece"></i>
            </div>
            <span class="numero">1</span>
            <span class="titulo-passo">Entenda o TEA</span>
        </a>

        <span class="seta">
            <i class="fa-solid fa-chevron-right"></i>
        </span>

        <a href="profissionais.php" class="passo">
            <div class="icone">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <span class="numero">2</span>
            <span class="titulo-passo">Procure apoio profissional</span>
        </a>

        <span class="seta">
            <i class="fa-solid fa-chevron-right"></i>
        </span>

        <a href="leis.php" class="passo">
            <div class="icone">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <span class="numero">3</span>
            <span class="titulo-passo">Conheça seus direitos</span>
        </a>

        <span class="seta">
            <i class="fa-solid fa-chevron-right"></i>
        </span>

        <a href="educacaoinclusiva.php" class="passo">
            <div class="icone">
                <i class="fa-solid fa-school"></i>
            </div>
            <span class="numero">4</span>
            <span class="titulo-passo">Saiba sobre a inclusão escolar</span>
        </a>

        <span class="seta">
            <i class="fa-solid fa-chevron-right"></i>
        </span>

        <a href="faq.php" class="passo">
            <div class="icone">
               <i class="fa-solid fa-circle-question"></i>
            </div>
            <span class="numero">5</span>
            <span class="titulo-passo">Confira as dúvidas frequentes</span>
        </a>

        <a href="guia.php" class="btn-guia">
            Ver guia completo
        </a>

    </div>
</section>

    <?php include 'includes/footer.php'; ?>
    
<script src="https://cdn.jsdelivr.net/npm/accessibility-widgets@latest/widget.js"></script>
</script>
</main>
</body>
</html>