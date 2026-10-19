<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/projeto.css">

<main class="pagina-sobre">

    <section class="sobre-texto">

        <div class="texto-container">

            <div class="texto-introducao">


                <span class="texto-label">O ForTEA</span>

                <h2>
                    Informação também é
                    <strong>uma forma de acolher.</strong>
                </h2>

            </div>
            <div class="texto-corpo">

                <p>O ForTEA nasceu da percepção de que encontrar informações confiáveis sobre o Transtorno do Espectro Autista nem sempre é simples. Após uma suspeita ou diagnóstico, famílias, cuidadores e professores podem se deparar com muitas dúvidas sobre o que fazer, onde buscar orientação, quais são seus direitos e quais estratégias podem fazer parte da rotina.</p>

                <p>Durante nossas pesquisas, percebemos que o conteúdo sobre o TEA está distribuído entre diferentes fontes e, muitas vezes, apresentado por meio de uma linguagem técnica. Isso pode dificultar a compreensão de quem busca respostas de forma clara e prática.

                A partir dessa necessidade, criamos o ForTEA, reunindo em um único espaço conteúdos sobre direitos, educação, rotina, estímulos e outras questões relacionadas ao TEA. Nosso objetivo é tornar o acesso ao conhecimento mais simples e contribuir para uma compreensão mais ampla do autismo, utilizando a tecnologia como uma ferramenta de informação e apoio.</p>
            </div>

        </div>

    </section>

<section class="sobre-equipe">

    <div class="equipe-container">

        <div class="equipe-titulo">
            <span class="texto-label">Quem somos?</span>

            <h2>
                As mentes por trás do <strong>ForTEA</strong>
            </h2>

            <p class="equipe-descricao">
                Somos quatro estudantes do curso técnico em Informática para Internet
                da Etec Professora Maria Cristina Medeiros. A escolha do tema nasceu
                da nossa percepção sobre a necessidade de tornar as informações sobre
                o Transtorno do Espectro Autista mais acessíveis, organizadas e fáceis
                de compreender.

                Durante o desenvolvimento do projeto, percebemos que famílias,
                cuidadores e professores podem encontrar dificuldades para localizar
                orientações claras e confiáveis para situações do dia a dia.

                Foi a partir dessa necessidade que decidimos criar o
                <strong>ForTEA</strong> (Famílias Orientadas e Rede TEA), nosso Trabalho
                de Conclusão de Curso, utilizando a tecnologia como uma ferramenta
                de informação e apoio.
            </p>
        </div>

        <div class="equipe-fotos">

            <div class="equipe-membro">
                <div class="foto-redonda">
                    <img src="img/anaportela.jpeg" alt="Ana Beatriz Gilarde Portela">
                </div>
                <h3>Ana Beatriz Gilarde Portela</h3>
            </div>

            <div class="equipe-membro">
                <div class="foto-redonda">
                    <img src="img/anafortes.jpeg" alt="Ana Caroliny Fortes">
                </div>
                <h3>Ana Caroliny Fortes</h3>
            </div>

            <div class="equipe-membro">
                <div class="foto-redonda">
                    <img src="img/heloisa.jpeg" alt="Heloísa Lima Rodrigues">
                </div>
                <h3>Heloísa Lima Rodrigues</h3>
            </div>

            <div class="equipe-membro">
                <div class="foto-redonda">
                    <img src="img/isadora.jpeg" alt="Isadora Ribeiro Jans">
                </div>
                <h3>Isadora Ribeiro Jans</h3>
            </div>

        </div>

    </div>

</section>  
</main>

<?php include 'includes/footer.php'; ?>



<script>
document.addEventListener("DOMContentLoaded", function () {

    const elementos = document.querySelectorAll(
        ".sobre-texto, .sobre-equipe"
    );

    const observer = new IntersectionObserver(
        function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add("sobre-visivel");
                    observer.unobserve(entrada.target);
                }
            });
        },
        {
            threshold: 0.08
        }
    );

    elementos.forEach(function (elemento) {
        observer.observe(elemento);
    });

});
</script>