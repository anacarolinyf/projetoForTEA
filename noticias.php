
<?php

date_default_timezone_set('America/Sao_Paulo');

$apiKey = '765f27309d2a45bdad5dc9e7f53851f5';


$termos = 'autismo OR autista OR autistas OR "transtorno do espectro autista" OR neurodivergência OR neurodivergente OR neurodiversidade';

$parametros = [
    'q' => $termos,
    'searchIn' => 'title,description',
    'language' => 'pt',
    'sortBy' => 'relevancy',
    'pageSize' => 100,
    'page' => 1
];

$urlAPI = 'https://newsapi.org/v2/everything?' .
    http_build_query($parametros);


$dadosAPI = [];
$noticias = [];
$erroAPI = false;

if (function_exists('curl_init')) {

    $ch = curl_init($urlAPI);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-Api-Key: ' . $apiKey,
            'User-Agent: ForTEA/1.0'
        ],
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ]);

    $resposta = curl_exec($ch);
    $codigoHTTP = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

} else {

    $opcoes = [
        'http' => [
            'method' => 'GET',
            'header' =>
                "X-Api-Key: {$apiKey}\r\n" .
                "User-Agent: ForTEA/1.0\r\n",
            'timeout' => 25,
            'ignore_errors' => true
        ]
    ];

    $contexto = stream_context_create($opcoes);

    $resposta = @file_get_contents($urlAPI, false, $contexto);
    $codigoHTTP = 0;
}

if ($resposta !== false && !empty($resposta)) {

    $dadosAPI = json_decode($resposta, true) ?? [];

    if (
        ($dadosAPI['status'] ?? '') === 'ok' &&
        (!isset($codigoHTTP) || $codigoHTTP < 400)
    ) {

        $noticias = $dadosAPI['articles'] ?? [];

    } else {

        $erroAPI = true;
    }

} else {

    $erroAPI = true;
}


/* ==========================================
   3. FILTRAR NOTÍCIAS RELACIONADAS
========================================== */

$noticiasFiltradas = [];

foreach ($noticias as $artigo) {

    if (
        empty($artigo['title']) ||
        empty($artigo['url']) ||
        $artigo['title'] === '[Removed]'
    ) {
        continue;
    }

    $link = filter_var($artigo['url'], FILTER_VALIDATE_URL);

    if (
        !$link ||
        !in_array(parse_url($link, PHP_URL_SCHEME), ['http', 'https'])
    ) {
        continue;
    }

    $texto = mb_strtolower(
        ($artigo['title'] ?? '') . ' ' .
        ($artigo['description'] ?? ''),
        'UTF-8'
    );

    /*
     * Confirma que a notícia tenha relação com
     * autismo ou neurodiversidade.
     */

    $termosRelacionados = [
        'autis',
        'transtorno do espectro autista',
        'neurodiverg',
        'neurodivers'
    ];

    $relacionada = false;

    foreach ($termosRelacionados as $termo) {

        if (mb_strpos($texto, $termo) !== false) {
            $relacionada = true;
            break;
        }
    }

    if (!$relacionada) {
        continue;
    }

    $noticiasFiltradas[] = $artigo;
}


/* ==========================================
   4. ESCOLHER A NOTÍCIA PRINCIPAL
========================================== */

/*
 * A notícia em destaque recebe pontuação maior
 * quando o título fala diretamente de autismo.
 */

function pontuarNoticia($artigo) {

    $titulo = mb_strtolower($artigo['title'] ?? '', 'UTF-8');

    $descricao = mb_strtolower(
        $artigo['description'] ?? '',
        'UTF-8'
    );

    $pontuacao = 0;

    if (str_contains($titulo, 'autismo')) {
        $pontuacao += 15;
    }

    if (str_contains($titulo, 'autista')) {
        $pontuacao += 15;
    }

    if (str_contains($titulo, 'transtorno do espectro autista')) {
        $pontuacao += 20;
    }

    if (str_contains($titulo, 'neurodiverg')) {
        $pontuacao += 8;
    }

    if (str_contains($descricao, 'autismo')) {
        $pontuacao += 5;
    }

    if (str_contains($descricao, 'autista')) {
        $pontuacao += 5;
    }

    return $pontuacao;
}

/*
 * Primeiro, as matérias mais diretamente relacionadas
 * ao autismo são colocadas em destaque.
 */

usort($noticiasFiltradas, function ($a, $b) {

    return pontuarNoticia($b) <=> pontuarNoticia($a);
});

/*
 * Separa a principal das demais.
 */

$destaque = !empty($noticiasFiltradas)
    ? $noticiasFiltradas[0]
    : null;

$demaisNoticias = array_slice($noticiasFiltradas, 1);


/* ==========================================
   5. ORGANIZAR AS OUTRAS NOTÍCIAS POR DATA
========================================== */

usort($demaisNoticias, function ($a, $b) {

    $dataA = strtotime($a['publishedAt'] ?? '') ?: 0;
    $dataB = strtotime($b['publishedAt'] ?? '') ?: 0;

    return $dataB <=> $dataA;
});


/* ==========================================
   6. FUNÇÕES AUXILIARES
========================================== */

function limparTexto($texto, $limite = 160) {

    $texto = trim(strip_tags($texto ?? ''));

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($texto, 0, $limite, '...', 'UTF-8');
    }

    return strlen($texto) > $limite
        ? substr($texto, 0, $limite - 3) . '...'
        : $texto;
}

function formatarData($data) {

    if (empty($data)) {
        return 'Data não informada';
    }

    $timestamp = strtotime($data);

    return $timestamp
        ? date('d/m/Y', $timestamp)
        : 'Data não informada';
}

function imagemNoticia($imagem) {

    if (
        !empty($imagem) &&
        filter_var($imagem, FILTER_VALIDATE_URL) &&
        in_array(parse_url($imagem, PHP_URL_SCHEME), ['http', 'https'])
    ) {
        return htmlspecialchars($imagem, ENT_QUOTES, 'UTF-8');
    }

    return 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?auto=format&fit=crop&w=1000&q=80';
}

function tituloNoticia($titulo) {

    return htmlspecialchars(
        $titulo ?? 'Notícia sem título',
        ENT_QUOTES,
        'UTF-8'
    );
}

function linkNoticia($url) {

    return htmlspecialchars(
        $url,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<?php include 'includes/header.php'; ?>

<style>

/* ==========================================
   CONFIGURAÇÕES GERAIS
========================================== */

.noticias-pagina {
    --azul: #21479B;
    --azul-escuro: #17366F;
    --azul-claro: #EAF2FF;
    --texto: #29364B;
    --cinza: #718096;

    background: #F5F7FB;
    color: var(--texto);
    min-height: 100vh;
    padding-bottom: 70px;
}

.noticias-pagina * {
    box-sizing: border-box;
}

/* ==========================================
   INTRODUÇÃO
========================================== */

.noticias-hero {
    position: relative;
    overflow: hidden;
    background: linear-gradient(120deg, #17366F, #21479B 65%, #3467B9);
    padding: 65px 25px 75px;
    color: #fff;
}

.noticias-hero::before {
    content: "";
    position: absolute;
    width: 350px;
    height: 350px;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 50%;
    right: -90px;
    top: -170px;
}

.noticias-hero-conteudo {
    position: relative;
    z-index: 1;
    max-width: 1120px;
    margin: 0 auto;
}

.noticias-etiqueta {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    background: rgba(255,255,255,0.13);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 8px 15px;
    border-radius: 30px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 22px;
}

.noticias-etiqueta span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #9DC7FF;
}

.noticias-hero h1 {
    max-width: 720px;
    font-size: clamp(2.2rem, 5vw, 3.5rem);
    line-height: 1.12;
    font-weight: 750;
    letter-spacing: -1.5px;
    margin: 0 0 20px;
    color: #fff;
}

.noticias-hero h1 strong {
    color: #BBD7FF;
}

.noticias-hero p {
    max-width: 690px;
    color: rgba(255,255,255,0.86);
    font-size: 1.05rem;
    line-height: 1.8;
    margin: 0;
}

.noticias-hero-linha {
    width: 65px;
    height: 4px;
    background: #9DC7FF;
    border-radius: 5px;
    margin-top: 28px;
}

/* ==========================================
   CONTEÚDO
========================================== */

.noticias-container {
    max-width: 1160px;
    margin: 0 auto;
    padding: 0 24px;
}

.noticias-introducao {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 20px;
    margin: 55px 0 27px;
}

.noticias-introducao span {
    display: block;
    color: #4774BB;
    font-size: 0.75rem;
    font-weight: 750;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 9px;
}

.noticias-introducao h2 {
    font-size: 1.8rem;
    color: #1D3768;
    margin: 0;
    font-weight: 750;
}

.noticias-introducao p {
    color: var(--cinza);
    font-size: 0.9rem;
    margin: 9px 0 0;
    line-height: 1.6;
}

.noticias-total {
    flex-shrink: 0;
    background: #E7EEFC;
    color: #21479B;
    border-radius: 30px;
    padding: 9px 15px;
    font-size: 0.82rem;
    font-weight: 700;
}

/* ==========================================
   DESTAQUE PRINCIPAL
========================================== */

.noticia-destaque {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 7px 28px rgba(31, 57, 100, 0.08);
    margin-bottom: 55px;
    min-height: 340px;
}

.noticia-destaque-imagem {
    min-height: 340px;
    background: #DFE8F7;
    overflow: hidden;
}

.noticia-destaque-imagem img {
    display: block;
    width: 100%;
    height: 100%;
    min-height: 340px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.noticia-destaque:hover .noticia-destaque-imagem img {
    transform: scale(1.035);
}

.noticia-destaque-conteudo {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 35px 38px;
}

.noticia-destaque-tag {
    display: inline-block;
    align-self: flex-start;
    background: #EAF2FF;
    color: #21479B;
    padding: 7px 12px;
    border-radius: 20px;
    font-size: 0.73rem;
    font-weight: 750;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.noticia-destaque h3 {
    color: #1D3768;
    font-size: 1.55rem;
    line-height: 1.4;
    margin: 0 0 15px;
}

.noticia-destaque h3 a {
    color: inherit;
    text-decoration: none;
}

.noticia-destaque h3 a:hover {
    color: #3467B9;
}

.noticia-destaque p {
    color: #66758A;
    font-size: 0.94rem;
    line-height: 1.75;
    margin: 0 0 24px;
}

.noticia-destaque-rodape {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding-top: 17px;
    border-top: 1px solid #EDF1F7;
    color: #8491A5;
    font-size: 0.8rem;
}

.noticia-destaque-rodape strong {
    color: #4774BB;
}

/* ==========================================
   OUTRAS NOTÍCIAS
========================================== */

.noticias-grade {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 25px;
}

.noticia-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(31, 57, 100, 0.055);
    border: 1px solid #EDF1F7;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.noticia-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(31, 57, 100, 0.11);
}

.noticia-card-imagem {
    display: block;
    height: 190px;
    overflow: hidden;
    background: #E5ECF7;
}

.noticia-card-imagem img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.noticia-card:hover .noticia-card-imagem img {
    transform: scale(1.05);
}

.noticia-card-conteudo {
    display: flex;
    flex-direction: column;
    flex: 1;
    padding: 21px;
}

.noticia-card-fonte {
    color: #4774BB;
    font-size: 0.73rem;
    font-weight: 750;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 11px;
}

.noticia-card h3 {
    color: #1D3768;
    font-size: 1.08rem;
    line-height: 1.48;
    margin: 0 0 12px;
}

.noticia-card h3 a {
    color: inherit;
    text-decoration: none;
}

.noticia-card h3 a:hover {
    color: #3467B9;
}

.noticia-card p {
    color: #718096;
    font-size: 0.89rem;
    line-height: 1.7;
    margin: 0 0 20px;
}

.noticia-card-rodape {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid #EDF1F7;
    color: #929EB0;
    font-size: 0.76rem;
}

.noticia-ler {
    color: #21479B;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
}

.noticia-ler:hover {
    color: #3467B9;
}

/* ==========================================
   MENSAGENS
========================================== */

.noticias-mensagem {
    background: #fff;
    border: 1px solid #E5EAF2;
    border-radius: 16px;
    padding: 45px 25px;
    text-align: center;
}

.noticias-mensagem h3 {
    color: #1D3768;
    font-size: 1.25rem;
    margin: 0 0 10px;
}

.noticias-mensagem p {
    color: #718096;
    line-height: 1.7;
    margin: 0;
}

/* ==========================================
   RESPONSIVIDADE
========================================== */

@media (max-width: 900px) {

    .noticias-grade {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .noticia-destaque {
        grid-template-columns: 1fr 1fr;
    }

    .noticia-destaque-conteudo {
        padding: 25px;
    }

    .noticia-destaque h3 {
        font-size: 1.3rem;
    }
}

@media (max-width: 600px) {

    .noticias-hero {
        padding: 48px 22px 55px;
    }

    .noticias-hero h1 {
        font-size: 2.35rem;
    }

    .noticias-hero p {
        font-size: 0.94rem;
    }

    .noticias-container {
        padding: 0 16px;
    }

    .noticias-introducao {
        display: block;
        margin: 40px 0 22px;
    }

    .noticias-introducao h2 {
        font-size: 1.5rem;
    }

    .noticias-total {
        display: inline-block;
        margin-top: 15px;
    }

    .noticia-destaque {
        grid-template-columns: 1fr;
        margin-bottom: 40px;
    }

    .noticia-destaque-imagem,
    .noticia-destaque-imagem img {
        min-height: 220px;
        height: 220px;
    }

    .noticia-destaque-conteudo {
        padding: 25px 22px;
    }

    .noticia-destaque h3 {
        font-size: 1.3rem;
    }

    .noticias-grade {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .noticia-card-imagem {
        height: 210px;
    }
}

</style>


<main class="noticias-pagina">

    <!-- INTRODUÇÃO -->

    <section class="noticias-hero">

        <div class="noticias-hero-conteudo">

            <div class="noticias-etiqueta">
                <span></span>
                Informação e atualidade
            </div>

            <h1>
                O que acontece no universo do
                <strong>autismo?</strong>
            </h1>

            <p>
                Acompanhe histórias, descobertas, conquistas e desafios
                relacionados ao Transtorno do Espectro Autista e à
                neurodiversidade. Informação para ampliar o conhecimento,
                dar visibilidade e fortalecer a inclusão.
            </p>

            <div class="noticias-hero-linha"></div>

        </div>

    </section>


    <!-- NOTÍCIAS -->

    <section class="noticias-container">

        <div class="noticias-introducao">

            <div>
                <span>Portal de notícias ForTEA</span>

                <h2>TEA em foco</h2>

                <p>
                    Informação, conhecimento e acontecimentos sobre o universo autista.
                </p>
            </div>

            <div class="noticias-total">
                <?php echo count($noticiasFiltradas); ?>
                notícias encontradas
            </div>

        </div>


        <?php if ($destaque): ?>

            <?php

                $titulo = tituloNoticia($destaque['title']);

                $descricao = limparTexto(
                    $destaque['description'] ??
                    'Acesse a matéria para conferir todos os detalhes.',
                    240
                );

                $fonte = htmlspecialchars(
                    $destaque['source']['name'] ?? 'Portal de notícias',
                    ENT_QUOTES,
                    'UTF-8'
                );

                $data = formatarData($destaque['publishedAt'] ?? '');

                $imagem = imagemNoticia($destaque['urlToImage'] ?? '');

                $link = linkNoticia($destaque['url']);

            ?>

            <!-- NOTÍCIA PRINCIPAL -->

            <article class="noticia-destaque">

                <div class="noticia-destaque-imagem">

                    <a href="<?php echo $link; ?>"
                       target="_blank"
                       rel="noopener noreferrer">

                        <img
                            src="<?php echo $imagem; ?>"
                            alt="<?php echo $titulo; ?>"
                        >

                    </a>

                </div>


                <div class="noticia-destaque-conteudo">

                    <span class="noticia-destaque-tag">
                        Notícia em destaque
                    </span>

                    <h3>

                        <a href="<?php echo $link; ?>"
                           target="_blank"
                           rel="noopener noreferrer">

                            <?php echo $titulo; ?>

                        </a>

                    </h3>

                    <p>
                        <?php echo htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'); ?>
                    </p>

                    <div class="noticia-destaque-rodape">

                        <strong><?php echo $fonte; ?></strong>

                        <span>•</span>

                        <span><?php echo $data; ?></span>

                    </div>

                </div>

            </article>


            <!-- OUTRAS NOTÍCIAS -->

            <?php if (!empty($demaisNoticias)): ?>

                <div class="noticias-introducao">

                    <div>
                        <span>Fique por dentro</span>

                        <h2>Mais notícias</h2>

                        <p>
                            Outros acontecimentos, pesquisas e iniciativas relacionados ao autismo.
                        </p>
                    </div>

                </div>


                <div class="noticias-grade">

                    <?php foreach ($demaisNoticias as $artigo): ?>

                        <?php

                            $titulo = tituloNoticia($artigo['title']);

                            $descricao = limparTexto(
                                $artigo['description'] ??
                                'Acesse a matéria para conferir todos os detalhes.',
                                150
                            );

                            $fonte = htmlspecialchars(
                                $artigo['source']['name'] ?? 'Portal de notícias',
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            $data = formatarData($artigo['publishedAt'] ?? '');

                            $imagem = imagemNoticia($artigo['urlToImage'] ?? '');

                            $link = linkNoticia($artigo['url']);

                        ?>

                        <article class="noticia-card">

                            <a class="noticia-card-imagem"
                               href="<?php echo $link; ?>"
                               target="_blank"
                               rel="noopener noreferrer">

                                <img
                                    src="<?php echo $imagem; ?>"
                                    alt="<?php echo $titulo; ?>"
                                    loading="lazy"
                                >

                            </a>


                            <div class="noticia-card-conteudo">

                                <span class="noticia-card-fonte">
                                    <?php echo $fonte; ?>
                                </span>

                                <h3>

                                    <a href="<?php echo $link; ?>"
                                       target="_blank"
                                       rel="noopener noreferrer">

                                        <?php echo $titulo; ?>

                                    </a>

                                </h3>

                                <p>
                                    <?php echo htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'); ?>
                                </p>

                                <div class="noticia-card-rodape">

                                    <span><?php echo $data; ?></span>

                                    <a href="<?php echo $link; ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="noticia-ler">

                                        Ler notícia →

                                    </a>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


        <?php elseif ($erroAPI): ?>

            <div class="noticias-mensagem">

                <h3>Não foi possível carregar as notícias</h3>

                <p>
                    Verifique sua chave da NewsAPI e sua conexão com a internet.
                    Tente novamente mais tarde.
                </p>

            </div>

        <?php else: ?>

            <div class="noticias-mensagem">

                <h3>Nenhuma notícia encontrada</h3>

                <p>
                    Não encontramos matérias relacionadas ao autismo
                    neste momento. Volte em breve para conferir as atualizações.
                </p>

            </div>

        <?php endif; ?>

    </section>

</main>

<?php include 'includes/footer.php'; ?>
