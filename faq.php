
<?php
include 'includes/header.php';

$perguntas = [
    [
        "categoria" => "Diagnóstico",
        "pergunta" => "Como é feito o diagnóstico do autismo?",
        "resposta" => "O diagnóstico do Transtorno do Espectro Autista (TEA) é clínico e considera o desenvolvimento, a comunicação, a interação social e os comportamentos da pessoa. A avaliação é realizada por profissionais capacitados, que observam as características individuais. Ao perceber sinais ou ter dúvidas, é importante procurar orientação profissional."
    ],
    [
        "categoria" => "Diagnóstico",
        "pergunta" => "Autismo tem cura?",
        "resposta" => "O autismo não tem cura, pois não é uma doença. O Transtorno do Espectro Autista é uma condição do neurodesenvolvimento que acompanha a pessoa ao longo da vida. Com os apoios adequados, cada pessoa pode desenvolver habilidades, autonomia e qualidade de vida."
    ],
    [
        "categoria" => "Saúde e cuidados",
        "pergunta" => "Como é o processo de tratamento do autismo pelo SUS?",
        "resposta" => "O SUS oferece atendimento às pessoas autistas por meio de diferentes serviços da rede pública de saúde. A porta de entrada pode ser a Unidade Básica de Saúde (UBS), que orienta e encaminha para os atendimentos necessários. Dependendo das necessidades da pessoa, o acompanhamento pode envolver profissionais como fonoaudiólogos, terapeutas ocupacionais, psicólogos e outros especialistas."
    ],
    [
        "categoria" => "Diagnóstico",
        "pergunta" => "Meninos e meninas apresentam o autismo da mesma forma?",
        "resposta" => "Não necessariamente. O autismo pode se manifestar de maneiras diferentes em cada pessoa. Em algumas meninas, os sinais podem ser mais sutis ou passar despercebidos, o que pode dificultar ou atrasar o diagnóstico. Por isso, é importante observar as características individuais de cada pessoa."
    ],
    [
        "categoria" => "Desenvolvimento",
        "pergunta" => "O autismo é somente em crianças?",
        "resposta" => "Não. O autismo não é exclusivo da infância. O TEA acompanha a pessoa ao longo de toda a vida. Embora muitas pessoas sejam diagnosticadas ainda na infância, o autismo também pode ser identificado na adolescência ou na vida adulta."
    ],
    [
        "categoria" => "Desenvolvimento",
        "pergunta" => "Todas as pessoas com Transtorno do Espectro Autista são iguais?",
        "resposta" => "Não. Não existe um único perfil de pessoa autista. As características, habilidades e necessidades de suporte podem variar bastante entre os indivíduos. Cada pessoa possui sua própria maneira de se comunicar, aprender, interagir e perceber o mundo."
    ],
    [
        "categoria" => "Desenvolvimento",
        "pergunta" => "O que é comunicação alternativa no autismo?",
        "resposta" => "A Comunicação Aumentativa e Alternativa (CAA) reúne recursos e estratégias que ajudam a ampliar as possibilidades de comunicação. Ela pode envolver gestos, expressões, imagens, símbolos, pranchas e dispositivos eletrônicos. Esses recursos podem auxiliar pessoas que não utilizam a fala ou que precisam de outras formas para se expressar."
    ],
    [
        "categoria" => "Mitos e verdades",
        "pergunta" => "A falta de carinho e amor dos pais pode influenciar no desenvolvimento do autismo?",
        "resposta" => "Não. A falta de carinho ou afeto dos pais não causa autismo. O TEA é uma condição do neurodesenvolvimento relacionada a fatores genéticos e biológicos. Um ambiente acolhedor é importante para o bem-estar e o desenvolvimento de qualquer criança, mas a ausência de afeto não é a causa do autismo."
    ],
    [
        "categoria" => "Desenvolvimento",
        "pergunta" => "Por que muitas pessoas autistas não demonstram afeto?",
        "resposta" => "Algumas pessoas autistas podem apresentar sensibilidade ao toque, fazendo com que abraços e beijos sejam desconfortáveis. Isso não significa falta de afeto. O carinho pode ser demonstrado de diferentes maneiras, como ajudando, cuidando ou lembrando de detalhes importantes. É importante respeitar a forma de expressão de cada pessoa."
    ],
    [
        "categoria" => "Desenvolvimento",
        "pergunta" => "Pessoas autistas vivem de forma independente?",
        "resposta" => "Sim. Algumas pessoas autistas vivem de forma independente, enquanto outras precisam de diferentes níveis de suporte no dia a dia. Isso varia de acordo com as características, habilidades e necessidades de cada pessoa."
    ],
    [
        "categoria" => "Educação e direitos",
        "pergunta" => "Meu filho pode frequentar a escola regular?",
        "resposta" => "Sim. A criança autista tem direito à educação inclusiva e pode frequentar a escola regular, com os apoios necessários de acordo com suas necessidades. A legislação brasileira garante o acesso à educação e a inclusão de estudantes com deficiência, incluindo pessoas com TEA. O objetivo é garantir que a criança possa aprender, participar e se desenvolver junto aos demais alunos, com o suporte adequado."
    ],
    [
        "categoria" => "Diagnóstico",
        "pergunta" => "O autismo é hereditário? (da mãe ou do pai?)",
        "resposta" => "O autismo possui um forte componente genético, mas não é possível dizer que ele vem exclusivamente da mãe ou do pai. Diferentes genes podem estar envolvidos, podendo ser herdados de um dos pais ou surgir durante o desenvolvimento da criança. Ter familiares autistas pode aumentar a probabilidade, mas não significa que a criança necessariamente terá TEA."
    ],
    [
        "categoria" => "Saúde e cuidados",
        "pergunta" => "Toda pessoa autista precisa fazer terapia?",
        "resposta" => "Não necessariamente. A necessidade de terapia depende das características, necessidades e objetivos de cada pessoa autista. O acompanhamento deve ser individualizado e pode envolver diferentes profissionais, de acordo com o suporte necessário. O mais importante é que as intervenções contribuam para a qualidade de vida, a autonomia e o desenvolvimento da pessoa."
    ],
    [
        "categoria" => "Mitos e verdades",
        "pergunta" => "O uso excessivo de telas causa autismo?",
        "resposta" => "Não. O uso de telas não causa autismo. No entanto, o uso excessivo pode trazer impactos à rotina e ao desenvolvimento da criança. Por isso, é importante manter um uso equilibrado e adequado à idade."
    ],
    [
        "categoria" => "Saúde e cuidados",
        "pergunta" => "Uma pessoa pode ter autismo e outra condição ao mesmo tempo?",
        "resposta" => "Sim. Uma pessoa autista pode apresentar outras condições de saúde ou de desenvolvimento ao mesmo tempo. Por isso, é importante que suas necessidades sejam avaliadas de forma individualizada por profissionais capacitados, permitindo identificar os diferentes tipos de suporte que podem ser necessários."
    ],
    [
        "categoria" => "Mitos e verdades",
        "pergunta" => "É errado chamar uma pessoa de “autista” em vez de “pessoa com autismo”?",
        "resposta" => "Não existe uma única forma que seja adequada para todas as pessoas. Algumas preferem ser chamadas de “pessoa autista”, enquanto outras preferem “pessoa com autismo”. O mais importante é respeitar a preferência de cada indivíduo e evitar termos que sejam ofensivos ou reduzam a pessoa ao diagnóstico."
    ],
    [
        "categoria" => "Informações",
        "pergunta" => "Onde posso encontrar mais informações sobre autismo?",
        "resposta" => "Você pode encontrar informações em livros, artigos científicos e fontes confiáveis de saúde. No ForTEA, você também encontra conteúdos sobre autismo, diagnóstico, sinais, níveis de suporte, direitos e muito mais, de forma simples e acessível."
    ]
];

$categorias = [
    "Todas",
    "Diagnóstico",
    "Desenvolvimento",
    "Saúde e cuidados",
    "Educação e direitos",
    "Mitos e verdades",
    "Informações"
];
?>

<link rel="stylesheet" href="css/estilo.css">

<main class="faq-page">

    
<section class="faq-hero">

    <div class="faq-hero-conteudo">

        <span class="faq-etiqueta">
            <i class="fa-regular fa-comments"></i>
            CENTRAL DE DÚVIDAS
        </span>

        <h1>
            Informação para compreender.
            <span>Respostas para acolher.</span>
        </h1>

        <p>
            Reunimos respostas para as dúvidas mais comuns sobre o
            Transtorno do Espectro Autista. Um espaço para encontrar
            orientação, esclarecer informações e conhecer melhor
            diferentes aspectos do universo autista.
        </p>

        <a href="#faq-conteudo" class="faq-hero-link">
            Encontre sua resposta
            <i class="fa-solid fa-arrow-down"></i>
        </a>

    </div>

</section>


    <section class="faq-conteudo" id="faq-conteudo">

        <div class="faq-cabecalho">

            <div>
                <span class="faq-subtitulo">ESTAMOS AQUI PARA AJUDAR</span>

                <h2>O que você gostaria de saber?</h2>

                <p>
                    Pesquise uma dúvida ou escolha um assunto para encontrar
                    respostas de forma mais rápida.
                </p>
            </div>

            <div class="faq-total">
                <strong><?php echo count($perguntas); ?></strong>
                <span>perguntas<br>respondidas</span>
            </div>

        </div>


        <div class="faq-pesquisa">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="search"
                id="faqBusca"
                placeholder="Digite uma palavra ou sua dúvida..."
                aria-label="Pesquisar perguntas frequentes"
            >

            <span class="faq-atalho">
                <i class="fa-solid fa-arrow-turn-down"></i>
            </span>

        </div>


        <div class="faq-filtros" aria-label="Filtrar perguntas por assunto">

            <?php foreach ($categorias as $categoria): ?>

                <button
                    type="button"
                    class="faq-filtro <?php echo $categoria === 'Todas' ? 'ativo' : ''; ?>"
                    data-categoria="<?php echo htmlspecialchars($categoria); ?>"
                >
                    <?php echo htmlspecialchars($categoria); ?>
                </button>

            <?php endforeach; ?>

        </div>


        <div class="faq-lista-cabecalho">
            <span>PERGUNTAS FREQUENTES</span>
            <span id="faqResultado">
                <?php echo count($perguntas); ?> resultados
            </span>
        </div>


        <div class="faq-lista" id="faqLista">

            <?php foreach ($perguntas as $indice => $item): ?>

                <article
                    class="faq-item"
                    data-categoria="<?php echo htmlspecialchars($item['categoria']); ?>"
                >

                    <button
                        type="button"
                        class="faq-pergunta"
                        aria-expanded="false"
                    >

                        <span class="faq-numero">
                            <?php echo str_pad($indice + 1, 2, '0', STR_PAD_LEFT); ?>
                        </span>

                        <span class="faq-pergunta-texto">
                            <span class="faq-categoria">
                                <?php echo htmlspecialchars($item['categoria']); ?>
                            </span>

                            <strong>
                                <?php echo htmlspecialchars($item['pergunta']); ?>
                            </strong>
                        </span>

                        <span class="faq-expandir">
                            <i class="fa-solid fa-plus"></i>
                        </span>

                    </button>

                    <div class="faq-resposta">

                        <div class="faq-resposta-conteudo">
                            <p>
                                <?php echo htmlspecialchars($item['resposta']); ?>
                            </p>
                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>


        <div class="faq-sem-resultado" id="faqSemResultado" hidden>

            <i class="fa-regular fa-face-thinking"></i>

            <h3>Não encontramos essa pergunta.</h3>

            <p>
                Tente utilizar outras palavras ou selecionar um assunto diferente.
            </p>

            <button type="button" id="faqLimpar">
                Limpar pesquisa
            </button>

        </div>


        <div class="faq-ajuda">

            <div class="faq-ajuda-icone">
                <i class="fa-regular fa-message"></i>
            </div>

            <div class="faq-ajuda-texto">
                <span>AINDA TEM DÚVIDAS?</span>
                <h3>Continue explorando o ForTEA.</h3>
                <p>
                    Encontre outros conteúdos e orientações sobre o universo
                    do autismo em nossas páginas.
                </p>
            </div>

            <a href="guia.php" class="faq-ajuda-link">
                Acessar o guia
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </section>

</main>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const busca = document.getElementById("faqBusca");
    const filtros = document.querySelectorAll(".faq-filtro");
    const itens = document.querySelectorAll(".faq-item");
    const resultado = document.getElementById("faqResultado");
    const semResultado = document.getElementById("faqSemResultado");
    const limpar = document.getElementById("faqLimpar");

    let categoriaSelecionada = "Todas";

    function fecharPerguntas() {
        document.querySelectorAll(".faq-pergunta").forEach(botao => {
            botao.classList.remove("aberta");
            botao.setAttribute("aria-expanded", "false");

            const resposta = botao.nextElementSibling;
            resposta.style.maxHeight = null;
        });
    }

    function filtrarPerguntas() {

        const termo = busca.value
            .toLocaleLowerCase("pt-BR")
            .trim();

        let quantidade = 0;

        itens.forEach(item => {

            const pergunta = item.querySelector("strong").textContent.toLocaleLowerCase("pt-BR");
            const resposta = item.querySelector(".faq-resposta").textContent.toLocaleLowerCase("pt-BR");
            const categoria = item.dataset.categoria;

            const correspondeTexto =
                pergunta.includes(termo) || resposta.includes(termo);

            const correspondeCategoria =
                categoriaSelecionada === "Todas" ||
                categoria === categoriaSelecionada;

            const mostrar = correspondeTexto && correspondeCategoria;

            item.hidden = !mostrar;

            if (mostrar) {
                quantidade++;
            }

        });

        fecharPerguntas();

        resultado.textContent =
            quantidade + (quantidade === 1 ? " resultado" : " resultados");

        semResultado.hidden = quantidade !== 0;
    }


    filtros.forEach(filtro => {

        filtro.addEventListener("click", function () {

            filtros.forEach(botao => botao.classList.remove("ativo"));

            this.classList.add("ativo");

            categoriaSelecionada = this.dataset.categoria;

            filtrarPerguntas();

        });

    });


    busca.addEventListener("input", filtrarPerguntas);


    document.querySelectorAll(".faq-pergunta").forEach(botao => {

        botao.addEventListener("click", function () {

            const estavaAberta = this.classList.contains("aberta");

            fecharPerguntas();

            if (!estavaAberta) {

                this.classList.add("aberta");
                this.setAttribute("aria-expanded", "true");

                const resposta = this.nextElementSibling;

                resposta.style.maxHeight =
                    resposta.scrollHeight + "px";

            }

        });

    });


    limpar.addEventListener("click", function () {

        busca.value = "";
        categoriaSelecionada = "Todas";

        filtros.forEach(botao => {
            botao.classList.toggle(
                "ativo",
                botao.dataset.categoria === "Todas"
            );
        });

        filtrarPerguntas();

        busca.focus();

    });

});
</script>

<?php include 'includes/footer.php'; ?>
