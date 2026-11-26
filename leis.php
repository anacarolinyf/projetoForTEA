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
/* ===== Fundo e topo ===== */
.secao-leis {
    position: relative;
    overflow: hidden;
    padding-top: 60px;
    background: linear-gradient(180deg, #e3eeff 0, #f4f8ff 460px);
}

.secao-leis::before,
.secao-leis::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(40, 80, 165, .06);
    pointer-events: none;
}

.secao-leis::before { width: 380px; height: 380px; top: -160px; left: -120px; }
.secao-leis::after  { width: 260px; height: 260px; top: 40px; right: -90px; }

.container-leis { position: relative; z-index: 1; }

.leis-selo {
    width: 68px;
    height: 68px;
    margin: 0 auto 18px;
    border-radius: 22px;
    background: linear-gradient(135deg, #2850a5, #4a7be0);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    box-shadow: 0 12px 26px rgba(40, 80, 165, .28);
    transform: rotate(-6deg);
}

.titulo-leis > span {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 999px;
    background: #fff;
    font-size: 12px;
    box-shadow: 0 2px 8px rgba(40, 80, 165, .1);
}

.titulo-leis h2 { font-size: 42px; letter-spacing: -.5px; }

/* ===== Busca ===== */
.leis-busca { position: relative; max-width: 460px; margin: 28px auto 0; }

.leis-busca i {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #7b8fb0;
}

.leis-busca input {
    width: 100%;
    box-sizing: border-box;
    height: 52px;
    padding: 0 20px 0 50px;
    border: 1px solid #d5e1f3;
    border-radius: 999px;
    background: #fff;
    font: inherit;
    font-size: 15px;
    color: #344f73;
    outline: none;
    box-shadow: 0 6px 18px rgba(40, 80, 165, .08);
    transition: .2s ease;
}

.leis-busca input:focus {
    border-color: #2850a5;
    box-shadow: 0 0 0 4px rgba(40, 80, 165, .12);
}

.leis-contador { margin: 14px 0 0 !important; font-size: 13px !important; font-weight: 600; color: #6a7f9f !important; }

.leis-vazio { display: none; text-align: center; padding: 40px 0; color: #61728b; }
.leis-vazio i { display: block; font-size: 30px; color: #9db3d8; margin-bottom: 10px; }

/* ===== Cards ===== */
.cards-legislacao { gap: 20px; }

.card-legislacao {
    position: relative;
    scroll-margin-top: 150px;
    border-radius: 20px;
    border: 1px solid #dbe6f6;
    box-shadow: 0 4px 14px rgba(40, 80, 165, .06);
    animation: leisEntrada .5s ease backwards;
}

.card-legislacao:nth-child(even) { animation-delay: .07s; }
.card-legislacao[hidden] { display: none; }

.card-legislacao::before {
    content: "";
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 5px;
    background: linear-gradient(180deg, #4a7be0, #2850a5);
    opacity: 0;
    transition: opacity .3s ease;
    z-index: 1;
}

.card-legislacao:hover::before,
.card-legislacao.aberto::before { opacity: 1; }

.card-legislacao:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 30px rgba(40, 80, 165, .15);
}

.card-cabecalho {
    min-height: 100px;
    padding: 22px 100px 22px 24px;
    background: transparent;
}

.card-icone {
    width: 56px;
    height: 56px;
    border-radius: 18px;
    background: linear-gradient(135deg, #eaf2ff, #d3e3ff);
}

.card-legislacao.aberto .card-icone {
    background: linear-gradient(135deg, #2850a5, #4a7be0);
    color: #fff;
    box-shadow: 0 8px 18px rgba(40, 80, 165, .25);
}

.card-titulo span {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    background: #eef4ff;
    color: #4a63a0;
    font-size: 11px;
    margin-bottom: 8px;
}

.card-titulo h3 { font-size: 18px; }

/* seta (fica no canto, o clique passa para o botão) */
.seta-card {
    position: absolute;
    right: 20px;
    top: 50px;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #eef4ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    pointer-events: none;
    transition: transform .3s ease, background .3s ease, color .3s ease;
}

.card-legislacao.aberto .seta-card {
    transform: translateY(-50%) rotate(180deg);
    background: #2850a5;
    color: #fff;
}

/* coração de favoritar */
.card-legislacao .btn-favorito {
    position: absolute;
    top: 50px;
    right: 62px;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    border: none;
    border-radius: 50%;
    background: transparent;
    cursor: pointer;
    color: #94a3b8;
    font-size: 1.05rem;
    padding: 0;
    line-height: 1;
    transition: .2s ease;
    z-index: 2;
}

.card-legislacao .btn-favorito:hover {
    color: #ef4444;
    background: #fff0f0;
    transform: translateY(-50%) scale(1.1);
}

.card-legislacao .btn-favorito.ativo { color: #ef4444; }

/* conteúdo aberto */
.card-legislacao.aberto .card-detalhes {
    max-height: 1000px;
    margin: 0 24px;
    padding: 20px 0 24px;
    border-top: 1px dashed #cfdcf1;
}

.card-detalhes strong {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .8px;
}

.card-detalhes ul { list-style: none; padding: 0; }

.card-detalhes li { position: relative; padding-left: 30px; }

.card-detalhes li::before {
    content: "✓";
    position: absolute;
    left: 0;
    top: 3px;
    width: 19px;
    height: 19px;
    border-radius: 50%;
    background: #e3eeff;
    color: #2850a5;
    font-size: 11px;
    font-weight: 800;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===== Aviso ===== */
.aviso-leis {
    background: #fff;
    border: 1px solid #cfe0f7;
    border-left: 5px solid #2850a5;
    border-radius: 16px;
    box-shadow: 0 4px 14px rgba(40, 80, 165, .06);
}

.aviso-leis i {
    flex-shrink: 0;
    width: 38px;
    height: 38px;
    margin-top: 0;
    border-radius: 50%;
    background: #e7f0ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
}

@keyframes leisEntrada {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: none; }
}

@media (prefers-reduced-motion: reduce) {
    .card-legislacao { animation: none; }
}

body.reduzir-animacoes .card-legislacao { animation: none; transition: none; }

@media (max-width: 600px) {
    .secao-leis { padding-top: 40px; }
    .titulo-leis h2 { font-size: 32px; }
    .card-cabecalho { padding: 20px 92px 20px 18px; }
}
</style>

<section class="secao-leis">

    <div class="container-leis">

        <div class="titulo-leis">
            <div class="leis-selo" aria-hidden="true"><i class="fa-solid fa-scale-balanced"></i></div>
            <span>LEGISLAÇÃO</span>

            <h2>Conheça seus direitos</h2>

            <p>
                Reunimos algumas das principais leis e normas relacionadas
                aos direitos das pessoas com TEA. Clique em cada card para
                conhecer mais detalhes.
            </p>

            <div class="leis-busca">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="buscaLeis" placeholder="Buscar por nome ou número da lei" aria-label="Buscar leis">
            </div>

            <p class="leis-contador" id="contadorLeis" aria-live="polite"></p>
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


        <div class="leis-vazio" id="leisVazio">
            <i class="fa-regular fa-face-frown-open" aria-hidden="true"></i>
            Nenhuma lei encontrada. Tente outro nome ou número.
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


    /* ----- busca ----- */

    const campo    = document.getElementById('buscaLeis');
    const contador = document.getElementById('contadorLeis');
    const vazio    = document.getElementById('leisVazio');
    const total    = cards.length;

    const normalizar = function (t) {
        return t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    };

    function filtrar() {
        const q = normalizar(campo.value.trim());
        let achadas = 0;

        cards.forEach(function (card) {
            const ok = !q || normalizar(card.textContent).includes(q);
            card.hidden = !ok;
            if (ok) achadas++;
        });

        contador.textContent = q ? achadas + ' de ' + total + ' leis' : total + ' leis reunidas';
        vazio.style.display = achadas ? 'none' : 'block';
    }

    campo.addEventListener('input', filtrar);
    filtrar();


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