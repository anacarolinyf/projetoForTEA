<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/conexao.php';

include __DIR__ . '/includes/header.php';




if (!function_exists('normalizarTexto')) {
    function normalizarTexto($texto) {
        $texto = mb_strtolower((string) $texto, 'UTF-8');

        return strtr($texto, [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e',
            'í' => 'i', 'î' => 'i',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
        ]);
    }
}


// Grupos usados nos botões de filtro (a palavra-chave é procurada na especialidade)
$categorias = [
    'neurologia'          => ['label' => 'Neurologia',          'chaves' => ['neuro']],
    'psiquiatria'         => ['label' => 'Psiquiatria',         'chaves' => ['psiquiatr']],
    'psicologia'          => ['label' => 'Psicologia',          'chaves' => ['psicolog']],
    'terapia-ocupacional' => ['label' => 'Terapia ocupacional', 'chaves' => ['terapeuta ocupacional', 'terapia ocupacional']],
    'fonoaudiologia'      => ['label' => 'Fonoaudiologia',      'chaves' => ['fonoaudi']],
];

function categoriaDoProfissional($especialidade, $categorias) {

    $n = normalizarTexto($especialidade);

    foreach ($categorias as $slug => $dados) {
        foreach ($dados['chaves'] as $chave) {
            if (strpos($n, $chave) !== false) {
                return $slug;
            }
        }
    }

    return 'outras';
}

?>

<link rel="stylesheet" href="../css/estilo.css">

<style>



.profissional-card {
    position: relative;
    scroll-margin-top: 160px;
}

.profissional-info h3 {
    padding-right: 28px;
}

.profissional-card.destaque {
    animation: destaqueCard 2.4s ease;
}

@keyframes destaqueCard {
    0%, 60% {
        box-shadow: 0 0 0 4px rgba(52, 103, 235, 0.35);
    }
    100% {
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }
}

.btn-fav-prof {
    position: absolute;
    top: 10px;
    right: 10px;

    border: none;
    background: transparent;
    cursor: pointer;

    color: #94a3b8;
    font-size: 1.05rem;
    line-height: 1;
    padding: 6px;

    transition: 0.2s ease;
}

.btn-fav-prof:hover {
    color: #ef4444;
    transform: scale(1.15);
}

.btn-fav-prof.ativo {
    color: #ef4444;
}




.selo-contato {
    display: inline-flex;
    align-items: center;
    gap: 5px;

    margin-bottom: 8px;
    padding: 3px 9px;

    border-radius: 20px;

    background: #edf8ee;
    color: #347a39;

    font-size: 10.5px;
    font-weight: 700;
}




.profissional-endereco,
.profissional-extra {
    margin-bottom: 10px;
    font-size: 12px;
    line-height: 1.5;
}

.profissional-endereco strong,
.profissional-extra strong {
    display: block;
}

.profissional-endereco span,
.profissional-extra span {
    color: #555;
}

.profissional-acoes {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.btn-ligar,
.btn-site,
.btn-mapa {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    padding: 8px 11px;

    border-radius: 6px;

    text-decoration: none;

    font-size: 11px;
    font-weight: 600;

    transition: 0.2s ease;
}

.btn-ligar {
    background: #3467eb;
    color: #fff;
}

.btn-ligar:hover {
    background: #21479b;
    transform: translateY(-1px);
}

.btn-site,
.btn-mapa {
    background: #eaf2ff;
    color: #21479b;
}

.btn-site:hover,
.btn-mapa:hover {
    background: #d8e6ff;
    transform: translateY(-1px);
}




.filtro-barra {
    max-width: 1080px;
    margin: 0 auto 28px;
}

.filtro-busca {
    position: relative;
    margin-bottom: 16px;
}

.filtro-busca i {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);

    color: #8a97aa;
    font-size: 0.95rem;
}

.filtro-busca input {
    width: 100%;
    height: 50px;

    padding: 0 18px 0 46px;

    box-sizing: border-box;

    border: 1px solid #dfe6f0;
    border-radius: 14px;

    background: #fff;
    color: #263b63;

    font-size: 0.95rem;
    outline: none;

    transition: 0.2s ease;
}

.filtro-busca input:focus {
    border-color: #3467eb;
    box-shadow: 0 0 0 3px rgba(52, 103, 235, 0.12);
}

.filtro-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.chip-filtro {
    border: 1px solid #dfe6f0;
    border-radius: 20px;

    background: #fff;
    color: #21479b;

    padding: 8px 16px;

    font-size: 0.82rem;
    font-weight: 600;

    cursor: pointer;

    transition: 0.2s ease;
}

.chip-filtro:hover {
    background: #eaf2ff;
}

.chip-filtro.ativo {
    background: #21479b;
    border-color: #21479b;
    color: #fff;
}

.filtro-contador {
    margin-top: 14px;

    color: #6b7a90;
    font-size: 0.82rem;
}

.sem-resultado {
    display: none;

    max-width: 560px;

    margin: 25px auto 0;
    padding: 35px;

    background: #fff;
    border-radius: 16px;

    text-align: center;

    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
}

.sem-resultado i {
    margin-bottom: 12px;

    color: #3467eb;
    font-size: 36px;
}

.sem-resultado p {
    margin: 0;
    color: #64748b;
}




.prof-bloco {
    padding: 70px 0;
}

.prof-bloco.branco {
    background: #ffffff;
}

.prof-bloco.cinza {
    background: #f5f7fb;
}

.prof-bloco-container {
    width: 90%;
    max-width: 1080px;
    margin: 0 auto;
}

.prof-bloco-titulo {
    max-width: 720px;
    margin: 0 auto 38px;

    text-align: center;
}

.prof-bloco-titulo span {
    display: block;
    margin-bottom: 8px;

    color: #3467eb;

    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 1.5px;
}

.prof-bloco-titulo h2 {
    margin: 0 0 12px;

    color: #21479b;
    font-size: 1.9rem;
    line-height: 1.25;
}

.prof-bloco-titulo p {
    margin: 0;

    color: #66738a;
    line-height: 1.7;
}


/* Especialidades explicadas */

.especialidades-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
}

.especialidade-card {
    padding: 26px 22px;

    background: #f8faff;
    border: 1px solid #e3eaf5;
    border-radius: 18px;

    transition: 0.25s ease;
}

.especialidade-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 26px rgba(33, 71, 155, 0.09);
}

.especialidade-icone {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 16px;

    border-radius: 14px;

    background: #eaf2ff;
    color: #3467eb;

    font-size: 1.15rem;
}

.especialidade-card h3 {
    margin: 0 0 8px;

    color: #21479b;
    font-size: 1.02rem;
}

.especialidade-card p {
    margin: 0;

    color: #66738a;
    font-size: 0.88rem;
    line-height: 1.65;
}

.nota-diagnostico {
    display: flex;
    align-items: flex-start;
    gap: 14px;

    max-width: 860px;

    margin: 30px auto 0;
    padding: 18px 22px;

    background: #eaf2ff;
    border-left: 5px solid #3467eb;
    border-radius: 12px;
}

.nota-diagnostico i {
    margin-top: 3px;
    color: #3467eb;
}

.nota-diagnostico p {
    margin: 0;

    color: #374151;
    font-size: 0.9rem;
    line-height: 1.65;
}



.dicas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
}

.dica-card {
    padding: 26px 24px;

    background: #fff;
    border: 1px solid #e3eaf5;
    border-radius: 18px;

    box-shadow: 0 5px 18px rgba(31, 45, 61, 0.035);
}

.dica-numero {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 14px;

    border-radius: 12px;

    background: #3467eb;
    color: #fff;

    font-weight: 800;
}

.dica-card h3 {
    margin: 0 0 8px;

    color: #21479b;
    font-size: 1rem;
}

.dica-card p {
    margin: 0;

    color: #66738a;
    font-size: 0.88rem;
    line-height: 1.65;
}




.ajuda-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
}

.ajuda-card {
    padding: 30px;

    background: #f8faff;
    border: 1px solid #e3eaf5;
    border-radius: 20px;
}

.ajuda-card h3 {
    margin: 0 0 10px;

    color: #21479b;
    font-size: 1.15rem;
}

.ajuda-card p {
    margin: 0 0 14px;

    color: #66738a;
    font-size: 0.9rem;
    line-height: 1.7;
}

.ajuda-card ul {
    margin: 0 0 6px;
    padding-left: 18px;

    color: #4d5d74;
    font-size: 0.9rem;
    line-height: 1.7;
}

.btn-ajuda {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 12px 20px;

    border-radius: 12px;

    background: #21479b;
    color: #fff;

    text-decoration: none;

    font-size: 0.88rem;
    font-weight: 700;

    transition: 0.2s ease;
}

.btn-ajuda:hover {
    background: #17397f;
    transform: translateY(-2px);
}




.prof-aviso {
    display: flex;
    align-items: flex-start;
    gap: 14px;

    padding: 22px 26px;

    background: #fffcf5;
    border: 1px solid #e8e0c9;
    border-radius: 16px;
}

.prof-aviso i {
    margin-top: 3px;
    color: #b98219;
    font-size: 1.15rem;
}

.prof-aviso p {
    margin: 0;

    color: #756a55;
    font-size: 0.88rem;
    line-height: 1.7;
}

.prof-aviso strong {
    color: #5f4b22;
}

@media (max-width: 800px) {

    .ajuda-grid {
        grid-template-columns: 1fr;
    }

    .prof-bloco {
        padding: 50px 0;
    }

    .prof-bloco-titulo h2 {
        font-size: 1.55rem;
    }
}

</style>

<?php

$profissionais = [];

if (!isset($conexao) || !$conexao) {
    die("Erro: conexão com o banco de dados não foi encontrada.");
}

$sqlProfissionais = "
    SELECT *
    FROM profissionais
    WHERE ativo = 1
    ORDER BY nome ASC
";

$resultadoProfissionais = $conexao->query($sqlProfissionais);

if (!$resultadoProfissionais) {
    die("Erro ao buscar profissionais: " . $conexao->error);
}

$categoriasUsadas = [];

while ($profissional = $resultadoProfissionais->fetch_assoc()) {

    $profissional['_categoria'] = categoriaDoProfissional(
        $profissional['especialidade'] ?? '',
        $categorias
    );

    $categoriasUsadas[$profissional['_categoria']] = true;

    $profissionais[] = $profissional;
}




$favoritosProf = [];
$contatados    = [];

if (isset($_SESSION['usuario_id'])) {

    $uid = (int) $_SESSION['usuario_id'];

    try {

        $q = $conexao->prepare(
            "SELECT titulo FROM favoritos WHERE usuario_id = ? AND tipo = 'profissional'"
        );
        $q->bind_param("i", $uid);
        $q->execute();

        $r = $q->get_result();

        while ($f = $r->fetch_assoc()) {
            $favoritosProf[] = $f['titulo'];
        }

    } catch (Throwable $e) {
        // Se der erro, a página continua sem os corações preenchidos
    }

    try {

        $q = $conexao->prepare(
            "SELECT profissional_id, ultimo_contato
             FROM contatos_profissionais
             WHERE usuario_id = ?"
        );
        $q->bind_param("i", $uid);
        $q->execute();

        $r = $q->get_result();

        while ($c = $r->fetch_assoc()) {
            $contatados[(int) $c['profissional_id']] = $c['ultimo_contato'];
        }

    } catch (Throwable $e) {
        // Se der erro, a página continua sem o selo "Você já conversou"
    }
}

$totalProfissionais = count($profissionais);

?>

<main class="profissionais-page">


    <section class="profissionais-section">

        <div class="profissionais-container">

            <div class="profissionais-titulo">

                <h2>Profissionais especializados</h2>

                <p>
                    Encontre profissionais especializados que atuam
                    com pessoas com Transtorno do Espectro Autista.
                </p>

            </div>

            <?php if (!empty($profissionais)): ?>

                <!-- BUSCA E FILTROS -->

                <div class="filtro-barra">

                    <div class="filtro-busca">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input
                            type="search"
                            id="busca-prof"
                            placeholder="Buscar por nome, especialidade ou bairro"
                            autocomplete="off"
                        >
                    </div>

                    <div class="filtro-chips">

                        <button type="button" class="chip-filtro ativo" data-categoria="todos">
                            Todos
                        </button>

                        <?php foreach ($categorias as $slug => $dados): ?>

                            <?php if (isset($categoriasUsadas[$slug])): ?>
                                <button type="button" class="chip-filtro" data-categoria="<?= $slug ?>">
                                    <?= htmlspecialchars($dados['label']) ?>
                                </button>
                            <?php endif; ?>

                        <?php endforeach; ?>

                        <?php if (isset($categoriasUsadas['outras'])): ?>
                            <button type="button" class="chip-filtro" data-categoria="outras">
                                Outras especialidades
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="filtro-contador" id="filtro-contador">
                        Mostrando <?= $totalProfissionais ?> de <?= $totalProfissionais ?> profissionais
                    </div>

                </div>


                <div class="profissionais-grid">

                    <?php foreach ($profissionais as $profissional): ?>

                        <?php
                        $pid    = (int) $profissional['id'];
                        $nome   = $profissional['nome'] ?? '';
                        $espec  = $profissional['especialidade'] ?? '';
                        $desc   = $profissional['descricao'] ?? '';
                        $end    = $profissional['endereco'] ?? '';
                        $site   = $profissional['site'] ?? '';
                        $conv   = trim($profissional['convenios'] ?? '');
                        $atend  = trim($profissional['atendimento'] ?? '');
                        $valor  = trim($profissional['valor_consulta'] ?? '');

                        $textoBusca = normalizarTexto($nome . ' ' . $espec . ' ' . $end . ' ' . $desc);

                        $favoritado = in_array($nome, $favoritosProf, true);
                        $jaConversou = array_key_exists($pid, $contatados);

                        $whatsapp = preg_replace('/[^0-9]/', '', $profissional['whatsapp'] ?? '');
                        $telefoneNumeros = preg_replace('/[^0-9]/', '', $profissional['telefone'] ?? '');
                        ?>

                        <div class="profissional-card"
                             id="prof-<?= $pid ?>"
                             data-categoria="<?= htmlspecialchars($profissional['_categoria']) ?>"
                             data-busca="<?= htmlspecialchars($textoBusca) ?>">

                            <button type="button"
                                    class="btn-fav-prof <?= $favoritado ? 'ativo' : '' ?>"
                                    data-id="<?= $pid ?>"
                                    aria-label="Favoritar <?= htmlspecialchars($nome) ?>">
                                <i class="<?= $favoritado ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                            </button>

                            <div class="profissional-foto">

                                <?php if (!empty($profissional['foto'])): ?>

                                    <img
                                        src="<?= htmlspecialchars($profissional['foto']) ?>"
                                        alt="Foto de <?= htmlspecialchars($nome) ?>"
                                    >

                                <?php else: ?>

                                    <div class="sem-foto">
                                        <i class="fa-solid fa-user-doctor"></i>
                                    </div>

                                <?php endif; ?>

                            </div>

                            <div class="profissional-info">

                                <?php if ($jaConversou): ?>
                                    <span class="selo-contato">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Você já conversou
                                        <?php if (!empty($contatados[$pid])): ?>
                                            · <?= date('d/m/Y', strtotime($contatados[$pid])) ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>

                                <h3>
                                    <?= htmlspecialchars($nome) ?>
                                </h3>

                                <?php if (!empty($espec)): ?>
                                    <span class="profissional-especialidade">
                                        <?= htmlspecialchars($espec) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($desc)): ?>
                                    <p>
                                        <?= nl2br(htmlspecialchars($desc)) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($profissional['telefone'])): ?>

                                    <div class="profissional-contato">
                                        <strong>Contato:</strong>
                                        <span>
                                            <?= htmlspecialchars($profissional['telefone']) ?>
                                        </span>
                                    </div>

                                <?php endif; ?>

                                <?php if (!empty($end)): ?>

                                    <div class="profissional-endereco">
                                        <strong>Endereço:</strong>
                                        <span>
                                            <?= htmlspecialchars($end) ?>
                                        </span>
                                    </div>

                                <?php endif; ?>

                                <?php if ($atend !== ''): ?>
                                    <div class="profissional-extra">
                                        <strong>Atendimento:</strong>
                                        <span><?= htmlspecialchars($atend) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($conv !== ''): ?>
                                    <div class="profissional-extra">
                                        <strong>Convênios:</strong>
                                        <span><?= htmlspecialchars($conv) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($valor !== ''): ?>
                                    <div class="profissional-extra">
                                        <strong>Valor da consulta:</strong>
                                        <span><?= htmlspecialchars($valor) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="profissional-acoes">

                                    <?php if (!empty($whatsapp)): ?>

                                        <a
                                            href="contato_profissional.php?id=<?= $pid ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn-whatsapp"
                                        >
                                            <i class="fa-brands fa-whatsapp"></i>
                                            Conversar pelo WhatsApp
                                        </a>

                                    <?php elseif (!empty($telefoneNumeros)): ?>

                                        <a
                                            href="tel:+55<?= $telefoneNumeros ?>"
                                            class="btn-ligar"
                                            onclick="registrarLigacao(<?= $pid ?>)"
                                        >
                                            <i class="fa-solid fa-phone"></i>
                                            Conversar
                                        </a>

                                    <?php endif; ?>

                                    <?php if (!empty($end)): ?>

                                        <a
                                            href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($end) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn-mapa"
                                        >
                                            <i class="fa-solid fa-location-dot"></i>
                                            Como chegar
                                        </a>

                                    <?php endif; ?>

                                    <?php if (!empty($site) && preg_match('#^https?://#i', $site)): ?>

                                        <a
                                            href="<?= htmlspecialchars($site) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn-site"
                                        >
                                            <i class="fa-solid fa-globe"></i>
                                            Visitar site
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

                <div class="sem-resultado" id="sem-resultado">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <p>
                        Nenhum profissional encontrado com esses filtros.
                        Tente buscar por outro nome ou escolha "Todos".
                    </p>

                </div>

            <?php else: ?>

                <div class="sem-profissionais">

                    <i class="fa-solid fa-user-doctor"></i>

                    <p>
                        No momento, não há profissionais cadastrados.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>


    

    <section class="prof-bloco branco">

        <div class="prof-bloco-container">

            <div class="prof-bloco-titulo">

                <span>ENTENDA CADA ÁREA</span>

                <h2>O que faz cada profissional?</h2>

                <p>
                    Cada especialidade olha para um aspecto do desenvolvimento.
                    Saber a diferença ajuda a escolher por onde começar.
                </p>

            </div>

            <div class="especialidades-grid">

                <div class="especialidade-card">
                    <div class="especialidade-icone"><i class="fa-solid fa-brain"></i></div>
                    <h3>Neurologista pediátrico</h3>
                    <p>
                        Médico que avalia o desenvolvimento do cérebro e do sistema nervoso
                        de crianças e adolescentes. Pode participar da investigação do
                        diagnóstico e acompanhar condições que às vezes aparecem junto,
                        como epilepsia e dificuldades de sono.
                    </p>
                </div>

                <div class="especialidade-card">
                    <div class="especialidade-icone"><i class="fa-solid fa-notes-medical"></i></div>
                    <h3>Psiquiatra</h3>
                    <p>
                        Médico especializado em saúde mental. Ajuda na avaliação e, quando
                        necessário, indica e acompanha medicamentos para sintomas como
                        ansiedade, irritabilidade e agitação. Os medicamentos não tratam o
                        autismo em si, e sim sintomas associados.
                    </p>
                </div>

                <div class="especialidade-card">
                    <div class="especialidade-icone"><i class="fa-solid fa-comments"></i></div>
                    <h3>Psicólogo(a)</h3>
                    <p>
                        Faz avaliação e acompanhamento psicológico, orienta a família e
                        trabalha comunicação, emoções e comportamento. As abordagens variam,
                        então vale perguntar como é o trabalho com pessoas com TEA.
                    </p>
                </div>

                <div class="especialidade-card">
                    <div class="especialidade-icone"><i class="fa-solid fa-puzzle-piece"></i></div>
                    <h3>Terapeuta ocupacional</h3>
                    <p>
                        Trabalha a autonomia no dia a dia: vestir-se, comer, brincar e
                        organizar a rotina. Também cuida de questões sensoriais, como
                        sensibilidade a sons, texturas e luz.
                    </p>
                </div>

                <div class="especialidade-card">
                    <div class="especialidade-icone"><i class="fa-solid fa-comment-dots"></i></div>
                    <h3>Fonoaudiólogo(a)</h3>
                    <p>
                        Cuida da comunicação, da linguagem e da fala, além da alimentação
                        e da deglutição. Pode usar recursos de comunicação alternativa,
                        quando necessário.
                    </p>
                </div>

            </div>

            <div class="nota-diagnostico">
                <i class="fa-solid fa-circle-info"></i>
                <p>
                    A avaliação para o diagnóstico de TEA costuma envolver mais de um
                    profissional. Não é preciso passar por todos de uma vez: converse
                    com o pediatra ou com um dos profissionais acima para saber por
                    onde começar.
                </p>
            </div>

        </div>

    </section>


    

    <section class="prof-bloco cinza">

        <div class="prof-bloco-container">

            <div class="prof-bloco-titulo">

                <span>ANTES DE AGENDAR</span>

                <h2>Como escolher um profissional?</h2>

                <p>
                    Algumas perguntas simples ajudam a encontrar quem faz sentido
                    para a sua família.
                </p>

            </div>

            <div class="dicas-grid">

                <div class="dica-card">
                    <div class="dica-numero">1</div>
                    <h3>Confira o registro profissional</h3>
                    <p>
                        Médicos têm CRM, psicólogos CRP, terapeutas ocupacionais CREFITO
                        e fonoaudiólogos CREFONO. Dá para consultar o registro no site
                        do conselho da profissão.
                    </p>
                </div>

                <div class="dica-card">
                    <div class="dica-numero">2</div>
                    <h3>Pergunte sobre a experiência com TEA</h3>
                    <p>
                        Pergunte há quanto tempo atende pessoas com autismo, qual faixa
                        etária atende e como costuma envolver a família no tratamento.
                    </p>
                </div>

                <div class="dica-card">
                    <div class="dica-numero">3</div>
                    <h3>Leve o que já existe</h3>
                    <p>
                        Laudos, exames, relatórios da escola e lista de medicamentos
                        ajudam muito. Anotar o que você observa no dia a dia, ou
                        guardar vídeos curtos, também ajuda na consulta.
                    </p>
                </div>

                <div class="dica-card">
                    <div class="dica-numero">4</div>
                    <h3>Combine os detalhes práticos</h3>
                    <p>
                        Pergunte sobre valor, convênio, frequência das sessões, tempo
                        de cada atendimento e se existe a opção de consulta online.
                    </p>
                </div>

            </div>

        </div>

    </section>


   

    <section class="prof-bloco branco">

        <div class="prof-bloco-container">

            <div class="prof-bloco-titulo">

                <span>PRECISA DE AJUDA?</span>

                <h2>Não encontrou o que procura?</h2>

                <p>
                    Estamos sempre ampliando a lista. Enquanto isso, existem outros
                    caminhos que podem ajudar.
                </p>

            </div>

            <div class="ajuda-grid">

                <div class="ajuda-card">

                    <h3>Fale com a gente</h3>

                    <p>
                        Conte o que você está procurando e a cidade onde mora.
                        Podemos considerar a sua sugestão para novos cadastros.
                    </p>

                    <a href="contato.php" class="btn-ajuda">
                        <i class="fa-solid fa-envelope"></i>
                        Ir para Contato
                    </a>

                </div>

                <div class="ajuda-card">

                    <h3>Serviços públicos de saúde</h3>

                    <p>
                        O atendimento pelo SUS também é um caminho possível.
                    </p>

                    <ul>
                        <li>
                            <strong>UBS (Unidade Básica de Saúde):</strong>
                            porta de entrada do SUS, pode encaminhar para especialistas.
                        </li>
                        <li>
                            <strong>CAPSi:</strong>
                            Centro de Atenção Psicossocial para crianças e adolescentes.
                        </li>
                    </ul>

                    <p style="margin-top: 10px;">
                        Procure a unidade mais próxima da sua casa na prefeitura da
                        sua cidade.
                    </p>

                </div>

            </div>

        </div>

    </section>


    

    <section class="prof-bloco cinza" style="padding: 45px 0;">

        <div class="prof-bloco-container">

            <div class="prof-aviso">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <p>
                    <strong>Importante:</strong> as informações desta página têm caráter
                    informativo. O ForTEA não avalia nem recomenda profissionais, e esta
                    página não substitui orientação médica. Confirme endereço, horários,
                    valores e convênios diretamente com o profissional antes de agendar.
                </p>

            </div>

        </div>

    </section>

</main>


<script>



function registrarLigacao(id) {

    const dados = new URLSearchParams({ id: id });

    if (navigator.sendBeacon) {
        navigator.sendBeacon('registrar_contato.php', dados);
    } else {
        fetch('registrar_contato.php', { method: 'POST', body: dados, keepalive: true });
    }
}


document.addEventListener('DOMContentLoaded', function () {

    const cards   = document.querySelectorAll('.profissional-card');
    const busca   = document.getElementById('busca-prof');
    const chips   = document.querySelectorAll('.chip-filtro');
    const vazio   = document.getElementById('sem-resultado');
    const contador = document.getElementById('filtro-contador');

    let categoriaAtiva = 'todos';

    function normalizar(texto) {
        return texto
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }


    /* ----- busca e filtro ----- */

    function filtrar() {

        const termo = busca ? normalizar(busca.value.trim()) : '';
        let visiveis = 0;

        cards.forEach(function (card) {

            const categoriaOk = categoriaAtiva === 'todos' ||
                                card.dataset.categoria === categoriaAtiva;

            const buscaOk = termo === '' ||
                            card.dataset.busca.includes(termo);

            const mostrar = categoriaOk && buscaOk;

            card.style.display = mostrar ? '' : 'none';

            if (mostrar) {
                visiveis++;
            }
        });

        if (vazio) {
            vazio.style.display = visiveis === 0 ? 'block' : 'none';
        }

        if (contador) {
            contador.textContent = 'Mostrando ' + visiveis + ' de ' + cards.length + ' profissionais';
        }
    }

    if (busca) {
        busca.addEventListener('input', filtrar);
    }

    chips.forEach(function (chip) {

        chip.addEventListener('click', function () {

            chips.forEach(function (c) {
                c.classList.remove('ativo');
            });

            chip.classList.add('ativo');
            categoriaAtiva = chip.dataset.categoria;

            filtrar();
        });
    });


   

    document.querySelectorAll('.btn-fav-prof').forEach(function (btn) {

        btn.addEventListener('click', async function () {

            const pagina = window.location.pathname.split('/').pop() || 'Profissionais.php';

            const dados = new URLSearchParams({
                id: btn.dataset.id,
                url: pagina + '#prof-' + btn.dataset.id
            });

            try {

                const resp = await fetch('favoritar_profissional.php', {
                    method: 'POST',
                    body: dados
                });

                if (resp.status === 401) {
                    window.location.href = 'login.php';
                    return;
                }

                const json = await resp.json();
                const icone = btn.querySelector('i');

                btn.classList.toggle('ativo', json.favoritado);
                icone.classList.toggle('fa-solid', json.favoritado);
                icone.classList.toggle('fa-regular', !json.favoritado);

            } catch (erro) {
                console.error(erro);
            }
        });
    });


   

    if (location.hash) {

        const alvo = document.getElementById(location.hash.slice(1));

        if (alvo && alvo.classList.contains('profissional-card')) {

            alvo.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alvo.classList.add('destaque');
        }
    }

});

</script>

<script src="https://cdn.jsdelivr.net/npm/accessibility-widgets@latest/widget.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>