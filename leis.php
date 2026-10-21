<?php include 'includes/header.php'; ?>

<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "conexao.php";

// Leis que o usuário já favoritou
$leisFavoritas = [];

if (isset($_SESSION['usuario_id'])) {

    $uid = (int) $_SESSION['usuario_id'];

    $q = $conexao->prepare(
        "SELECT titulo FROM favoritos WHERE usuario_id = ? AND tipo = 'lei'"
    );

    $q->bind_param("i", $uid);
    $q->execute();

    $r = $q->get_result();

    while ($f = $r->fetch_assoc()) {
        $leisFavoritas[] = $f['titulo'];
    }
}

?>

<style>
.card-legislacao {
    position: relative;
    scroll-margin-top: 150px;
}

.card-legislacao .btn-favorito {
    position: absolute;
    top: 26px;               /* metade da altura do cabeçalho do card */
    right: 52px;             /* fica ao lado da setinha */
    transform: translateY(-50%);

    border: none;
    background: transparent;
    cursor: pointer;

    color: #94a3b8;
    font-size: 1.05rem;
    padding: 6px;
    line-height: 1;

    transition: 0.2s ease;
    z-index: 2;
}

.card-legislacao .btn-favorito:hover {
    color: #ef4444;
    transform: translateY(-50%) scale(1.15);
}

.card-legislacao .btn-favorito.ativo {
    color: #ef4444;
}
</style>

<section class="secao-leis">

    <div class="container-leis">

        <div class="titulo-leis">
            <span>LEGISLAÇÃO</span>

            <h2>Conheça seus direitos</h2>

            <p>
                Reunimos algumas das principais leis e normas relacionadas
                aos direitos das pessoas com TEA. Clique em cada card para
                conhecer mais detalhes.
            </p>
        </div>


        <div class="cards-legislacao">


            <!-- LEI 1 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-puzzle-piece"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 12.764/2012</span>
                        <h3>Lei do Autismo</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Institui a Política Nacional de Proteção dos
                        Direitos da Pessoa com Transtorno do Espectro Autista.
                    </p>

                    <p>
                        A lei reconhece a pessoa com TEA como pessoa com
                        deficiência para todos os efeitos legais.
                    </p>

                    <strong>O que garante?</strong>

                    <ul>
                        <li>Proteção dos direitos da pessoa com TEA;</li>
                        <li>Acesso à saúde;</li>
                        <li>Educação e ensino profissionalizante;</li>
                        <li>Moradia e assistência social;</li>
                        <li>Trabalho e inclusão social;</li>
                        <li>Combate à discriminação.</li>
                    </ul>

                </div>

            </article>


            <!-- LEI 2 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-id-card"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 13.977/2020</span>
                        <h3>CIPTEA — Carteira do Autista</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Conhecida como Lei Romeo Mion, criou a Carteira de
                        Identificação da Pessoa com Transtorno do Espectro
                        Autista — CIPTEA.
                    </p>

                    <strong>Para que serve?</strong>

                    <p>
                        A carteira facilita a identificação da pessoa com
                        TEA e ajuda a garantir atenção integral, pronto
                        atendimento e prioridade no acesso a serviços
                        públicos e privados.
                    </p>

                </div>

            </article>


            <!-- LEI 3 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-wheelchair"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 13.146/2015</span>
                        <h3>Lei Brasileira de Inclusão</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Também conhecida como Estatuto da Pessoa com
                        Deficiência, estabelece direitos e garantias
                        fundamentais para pessoas com deficiência.
                    </p>

                    <strong>Entre os direitos estão:</strong>

                    <ul>
                        <li>Acessibilidade;</li>
                        <li>Educação inclusiva;</li>
                        <li>Saúde;</li>
                        <li>Trabalho;</li>
                        <li>Transporte;</li>
                        <li>Cultura e lazer;</li>
                        <li>Participação social;</li>
                        <li>Proteção contra discriminação.</li>
                    </ul>

                </div>

            </article>


            <!-- LEI 4 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-clock"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 10.048/2000</span>
                        <h3>Atendimento Prioritário</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Garante atendimento prioritário a pessoas com
                        deficiência e outros grupos previstos na legislação.
                    </p>

                    <p>
                        Pessoas com TEA estão incluídas entre aquelas que
                        possuem direito ao atendimento prioritário.
                    </p>

                    <strong>Onde pode ser aplicado?</strong>

                    <ul>
                        <li>Repartições públicas;</li>
                        <li>Serviços públicos;</li>
                        <li>Instituições financeiras;</li>
                        <li>Estabelecimentos e serviços abrangidos pela lei.</li>
                    </ul>

                </div>

            </article>


            <!-- LEI 5 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-universal-access"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 10.098/2000</span>
                        <h3>Acessibilidade</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Estabelece normas e critérios básicos para promover
                        a acessibilidade das pessoas com deficiência ou
                        mobilidade reduzida.
                    </p>

                    <strong>O que aborda?</strong>

                    <ul>
                        <li>Espaços públicos;</li>
                        <li>Edificações;</li>
                        <li>Transporte;</li>
                        <li>Comunicação;</li>
                        <li>Eliminação de barreiras.</li>
                    </ul>

                </div>

            </article>


            <!-- LEI 6 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-school"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 9.394/1996</span>
                        <h3>Lei de Diretrizes e Bases da Educação</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        A LDB estabelece as diretrizes e bases da educação
                        nacional e possui normas relacionadas à educação
                        especial.
                    </p>

                    <strong>Na prática:</strong>

                    <p>
                        A legislação prevê atendimento educacional
                        especializado e recursos para favorecer a inclusão
                        dos estudantes que necessitam de apoio.
                    </p>

                </div>

            </article>


            <!-- LEI 7 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-child"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 8.069/1990</span>
                        <h3>Estatuto da Criança e do Adolescente</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        O ECA estabelece a proteção integral de crianças
                        e adolescentes.
                    </p>

                    <p>
                        Crianças e adolescentes com deficiência também
                        possuem direito à saúde, educação, dignidade,
                        convivência familiar e proteção.
                    </p>

                </div>

            </article>


            <!-- LEI 8 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-hand-holding-heart"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 8.742/1993</span>
                        <h3>Lei Orgânica da Assistência Social</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Organiza a assistência social no Brasil e estabelece
                        benefícios, serviços e programas destinados à
                        proteção social.
                    </p>

                    <strong>BPC</strong>

                    <p>
                        A legislação também estabelece regras relacionadas
                        ao Benefício de Prestação Continuada para pessoas
                        com deficiência que atendam aos critérios previstos.
                    </p>

                </div>

            </article>


            <!-- LEI 9 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 8.213/1991</span>
                        <h3>Inclusão no Mercado de Trabalho</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        A legislação previdenciária possui regras relacionadas
                        à inclusão profissional de pessoas com deficiência.
                    </p>

                    <p>
                        A chamada Lei de Cotas estabelece reserva de vagas
                        para pessoas com deficiência e beneficiários
                        reabilitados em empresas que atendam aos critérios
                        legais.
                    </p>

                </div>

            </article>


            <!-- LEI 10 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-car"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 8.989/1995</span>
                        <h3>Isenção de IPI</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Prevê hipóteses de isenção do Imposto sobre Produtos
                        Industrializados na aquisição de determinados
                        veículos por pessoas com deficiência.
                    </p>

                    <p>
                        Pessoas com TEA podem estar abrangidas pelas regras
                        específicas da legislação, desde que atendidos
                        os requisitos exigidos.
                    </p>

                </div>

            </article>


            <!-- LEI 11 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-sun"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 14.624/2023</span>
                        <h3>Cordão de Girassóis</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Instituiu o cordão de fita com desenhos de girassóis
                        como símbolo nacional de identificação de pessoas
                        com deficiências ocultas.
                    </p>

                    <strong>Importante:</strong>

                    <p>
                        O uso do cordão é opcional. A ausência do cordão
                        não prejudica o exercício dos direitos e garantias
                        previstos em lei.
                    </p>

                </div>

            </article>


            <!-- LEI 12 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 13.861/2019</span>
                        <h3>Autismo nos Censos</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Determinou a inclusão das especificidades relacionadas
                        ao Transtorno do Espectro Autista nos censos
                        demográficos.
                    </p>

                    <p>
                        A medida contribui para a produção de dados
                        populacionais sobre as pessoas com TEA no Brasil.
                    </p>

                </div>

            </article>


            <!-- LEI 13 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-person-circle-check"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 14.626/2023</span>
                        <h3>Atendimento Prioritário ao TEA</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Alterou a legislação sobre atendimento prioritário
                        e incluiu expressamente as pessoas com Transtorno
                        do Espectro Autista entre os grupos contemplados.
                    </p>

                </div>

            </article>


            <!-- LEI 14 -->
            <article class="card-legislacao">

                <button class="card-cabecalho" type="button">

                    <div class="card-icone">
                        <i class="fa-solid fa-person-walking-arrow-right"></i>
                    </div>

                    <div class="card-titulo">
                        <span>LEI Nº 14.176/2021</span>
                        <h3>Auxílio-Inclusão</h3>
                    </div>

                    <i class="fa-solid fa-chevron-down seta-card"></i>

                </button>


                <div class="card-detalhes">

                    <p>
                        Estabelece regras relacionadas ao auxílio-inclusão,
                        benefício destinado a determinados beneficiários
                        com deficiência que ingressam no mercado de trabalho,
                        conforme os critérios legais.
                    </p>

                </div>

            </article>


        </div>


        <div class="aviso-leis">

            <i class="fa-solid fa-circle-info"></i>

            <p>
                <strong>Importante:</strong> esta página possui finalidade
                informativa. A existência de um direito não significa que
                todos os requisitos para exercê-lo estejam presentes em
                qualquer situação. Consulte a legislação vigente e, quando
                necessário, procure orientação especializada.
            </p>

        </div>

    </div>

</section>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const cards = document.querySelectorAll('.card-legislacao');
    const favoritas = <?= json_encode($leisFavoritas) ?>;

    cards.forEach(function (card) {

        const botao  = card.querySelector('.card-cabecalho');
        const titulo = card.querySelector('h3').textContent.trim();
        const numero = card.querySelector('.card-titulo span').textContent.trim();

        // id estável baseado no número da lei (ex.: lei-12-764-2012)
        const slug = 'lei-' + numero.replace(/[^0-9]+/g, '-').replace(/^-|-$/g, '');
        card.id = slug;


        /* ----- abrir / fechar ----- */

        botao.addEventListener('click', function () {

            const aberto = card.classList.contains('aberto');

            // Fecha os outros cards
            cards.forEach(function (outroCard) {
                outroCard.classList.remove('aberto');
            });

            // Abre o clicado, se ele estava fechado
            if (!aberto) {
                card.classList.add('aberto');
            }

        });


        /* ----- coração de favoritar ----- */

        const ativo = favoritas.includes(titulo);

        const fav = document.createElement('button');
        fav.type = 'button';
        fav.className = 'btn-favorito' + (ativo ? ' ativo' : '');
        fav.setAttribute('aria-label', 'Favoritar ' + titulo);
        fav.innerHTML = '<i class="' + (ativo ? 'fa-solid' : 'fa-regular') + ' fa-heart"></i>';

        card.appendChild(fav);

        fav.addEventListener('click', async function () {

            const dados = new URLSearchParams({
                titulo: titulo,
                lei: numero,
                url: 'leis.php#' + slug
            });

            try {

                const resp = await fetch('favoritar_lei.php', {
                    method: 'POST',
                    body: dados
                });

                if (resp.status === 401) {
                    window.location.href = 'login.php';
                    return;
                }

                const json = await resp.json();
                const icone = fav.querySelector('i');

                fav.classList.toggle('ativo', json.favoritado);
                icone.classList.toggle('fa-solid', json.favoritado);
                icone.classList.toggle('fa-regular', !json.favoritado);

            } catch (erro) {
                console.error(erro);
            }

        });

    });


    /* ----- abre a lei indicada no link (vindo do perfil) ----- */

    if (location.hash) {

        const alvo = document.getElementById(location.hash.slice(1));

        if (alvo && alvo.classList.contains('card-legislacao')) {
            alvo.classList.add('aberto');
            alvo.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

});
</script>


<?php include 'includes/footer.php'; ?>