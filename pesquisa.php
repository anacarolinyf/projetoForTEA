
<?php

include 'includes/header.php';

$buscaOriginal = trim($_GET['busca'] ?? '');


function removerAcentos($texto)
{
    $texto = mb_strtolower($texto, 'UTF-8');

    $acentos = [
        'á' => 'a',
        'à' => 'a',
        'ã' => 'a',
        'â' => 'a',
        'ä' => 'a',

        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',

        'í' => 'i',
        'ì' => 'i',
        'î' => 'i',
        'ï' => 'i',

        'ó' => 'o',
        'ò' => 'o',
        'õ' => 'o',
        'ô' => 'o',
        'ö' => 'o',

        'ú' => 'u',
        'ù' => 'u',
        'û' => 'u',
        'ü' => 'u',

        'ç' => 'c'
    ];

    return strtr($texto, $acentos);
}




$paginas = [

    [
        'titulo' => 'Sobre o TEA',
        'descricao' => 'Informações sobre autismo, sinais, diagnóstico, características e neurodiversidade.',
        'link' => 'sobre.php',
        'icone' => 'fa-solid fa-puzzle-piece',

        'palavras' => [
            'tea',
            'autismo',
            'autista',
            'autistas',
            'transtorno do espectro autista',
            'transtorno espectro autista',
            'espectro autista',
            'neurodiversidade',
            'neurodivergencia',
            'neurodesenvolvimento',
            'diagnostico',
            'diagnóstico',
            'sinais',
            'sintomas',
            'caracteristicas',
            'características',
            'comorbidades',
            'mascaramento',
            'masking',
            'nivel 1',
            'nivel 2',
            'nivel 3',
            'nível 1',
            'nível 2',
            'nível 3'
        ]
    ],


    [
        'titulo' => 'Guia para Famílias',
        'descricao' => 'Orientações para famílias compreenderem, organizarem e acompanharem cada etapa.',
        'link' => 'guia.php',
        'icone' => 'fa-solid fa-heart',

        'palavras' => [
            'familia',
            'família',
            'familias',
            'famílias',
            'pais',
            'mae',
            'mãe',
            'pai',
            'responsavel',
            'responsável',
            'cuidados',
            'rotina',
            'acompanhamento',
            'trajetoria',
            'trajetória',
            'guia',
            'orientacao',
            'orientação',
            'acolhimento',
            'diagnostico',
            'diagnóstico',
            'terapia',
            'profissionais',
            'desenvolvimento'
        ]
    ],


    [
        'titulo' => 'Educação Inclusiva',
        'descricao' => 'Informações sobre inclusão, escola, aprendizagem e direitos no ambiente educacional.',
        'link' => 'educacaoinclusiva.php',
        'icone' => 'fa-solid fa-school',

        'palavras' => [
            'educacao',
            'educação',
            'educacao inclusiva',
            'educação inclusiva',
            'inclusao',
            'inclusão',
            'escola',
            'escolar',
            'aluno',
            'alunos',
            'professor',
            'professores',
            'aprendizagem',
            'ensino',
            'sala de aula',
            'aula',
            'colegio',
            'colégio',
            'rede de ensino',
            'acessibilidade',
            'adaptacao',
            'adaptação',
            'inclusivo',
            'inclusiva'
        ]
    ],


    [
        'titulo' => 'Direitos e Leis',
        'descricao' => 'Informações sobre leis, direitos e garantias das pessoas com TEA.',
        'link' => 'leis.php',
        'icone' => 'fa-solid fa-scale-balanced',

        'palavras' => [
            'lei',
            'leis',
            'direito',
            'direitos',
            'legislacao',
            'legislação',
            'politica publica',
            'política pública',
            'beneficio',
            'benefício',
            'beneficios',
            'benefícios',
            'garantia',
            'garantias',
            'cidadania',
            'inclusao',
            'inclusão',
            'educacao',
            'educação',
            'saude',
            'saúde',
            'sus',
            'eca',
            'estatuto',
            'pessoa com deficiencia',
            'pessoa com deficiência',
            'pcd'
        ]
    ],


    [
        'titulo' => 'Biblioteca',
        'descricao' => 'Materiais e conteúdos selecionados para ampliar o conhecimento sobre TEA e inclusão.',
        'link' => 'materiais.php',
        'icone' => 'fa-solid fa-book-open',

        'palavras' => [
            'biblioteca',
            'material',
            'materiais',
            'conteudo',
            'conteúdos',
            'cartilha',
            'cartilhas',
            'guia',
            'guias',
            'arquivo',
            'arquivos',
            'pdf',
            'download',
            'leitura',
            'informacao',
            'informação',
            'estudo',
            'pesquisa'
        ]
    ],


    [
        'titulo' => 'Materiais Pedagógicos',
        'descricao' => 'Materiais e recursos que podem auxiliar no aprendizado e na inclusão.',
        'link' => 'materiais.php',
        'icone' => 'fa-solid fa-graduation-cap',

        'palavras' => [
            'material pedagogico',
            'materiais pedagogicos',
            'material pedagógico',
            'materiais pedagógicos',
            'atividade',
            'atividades',
            'recurso',
            'recursos',
            'didatico',
            'didático',
            'aprendizagem',
            'ensino',
            'professor',
            'professores',
            'escola',
            'educacao',
            'educação',
            'inclusao',
            'inclusão'
        ]
    ],


    [
        'titulo' => 'Profissionais Especializados',
        'descricao' => 'Conheça profissionais e áreas que podem fazer parte do acompanhamento da pessoa com TEA.',
        'link' => 'profissionais.php',
        'icone' => 'fa-solid fa-user-doctor',

        'palavras' => [
            'profissional',
            'profissionais',
            'especialista',
            'especialistas',
            'psicologo',
            'psicóloga',
            'psicologia',
            'fonoaudiologo',
            'fonoaudiólogo',
            'fonoaudiologia',
            'terapeuta',
            'terapia',
            'terapia ocupacional',
            'terapeuta ocupacional',
            'neurologista',
            'psiquiatra',
            'medico',
            'médico',
            'pediatra',
            'acompanhamento',
            'atendimento'
        ]
    ],


    [
        'titulo' => 'Notícias',
        'descricao' => 'Notícias, pesquisas e novidades sobre autismo, inclusão e neurodiversidade.',
        'link' => 'noticias.php',
        'icone' => 'fa-solid fa-newspaper',

        'palavras' => [
            'noticia',
            'notícia',
            'noticias',
            'notícias',
            'novidade',
            'novidades',
            'atualidade',
            'atualidades',
            'pesquisa',
            'pesquisas',
            'estudo',
            'estudos',
            'descoberta',
            'descobertas',
            'ciencia',
            'ciência',
            'saude',
            'saúde',
            'autismo',
            'tea',
            'inclusao',
            'inclusão',
            'neurodiversidade'
        ]
    ],


    [
        'titulo' => 'Relatos de Experiências',
        'descricao' => 'Experiências compartilhadas por famílias e pessoas relacionadas ao autismo.',
        'link' => 'relatos.php',
        'icone' => 'fa-solid fa-comments',

        'palavras' => [
            'relato',
            'relatos',
            'experiencia',
            'experiência',
            'experiencias',
            'experiências',
            'depoimento',
            'depoimentos',
            'historia',
            'história',
            'historias',
            'histórias',
            'vivencia',
            'vivência',
            'vivencias',
            'vivências',
            'familia',
            'família',
            'pessoa autista',
            'autista'
        ]
    ],


    [
        'titulo' => 'Perguntas Frequentes',
        'descricao' => 'Encontre respostas para dúvidas frequentes sobre autismo, diagnóstico, terapias e inclusão.',
        'link' => 'faq.php',
        'icone' => 'fa-solid fa-circle-question',

        'palavras' => [
            'faq',
            'pergunta',
            'perguntas',
            'duvida',
            'dúvida',
            'duvidas',
            'dúvidas',
            'resposta',
            'respostas',
            'terapia',
            'diagnostico',
            'diagnóstico',
            'autismo',
            'tea',
            'escola',
            'tela',
            'comorbidade',
            'comorbidades'
        ]
    ],


    [
        'titulo' => 'Contato',
        'descricao' => 'Entre em contato com a equipe do ForTEA para tirar dúvidas ou enviar uma mensagem.',
        'link' => 'contato.php',
        'icone' => 'fa-solid fa-envelope',

        'palavras' => [
            'contato',
            'contatos',
            'mensagem',
            'mensagens',
            'email',
            'e-mail',
            'enviar',
            'falar',
            'equipe',
            'duvida',
            'dúvida',
            'ajuda'
        ]
    ]

];




$busca = removerAcentos($buscaOriginal);

$resultados = [];


if ($busca !== '') {

    foreach ($paginas as $pagina) {

        $encontrou = false;

        foreach ($pagina['palavras'] as $palavra) {

            $palavraNormalizada = removerAcentos($palavra);

            if (
                $busca === $palavraNormalizada ||
                strpos($palavraNormalizada, $busca) !== false ||
                strpos($busca, $palavraNormalizada) !== false
            ) {

                $encontrou = true;
                break;

            }

        }

        if ($encontrou) {
            $resultados[] = $pagina;
        }

    }

}

?>

<main class="pagina-pesquisa">

    <section class="resultado-pesquisa">

        <span class="tag-pesquisa">
            PESQUISA
        </span>

        <?php if ($buscaOriginal === ''): ?>

            <h1>O que você está procurando?</h1>

            <p>
                Pesquise por informações, direitos, educação,
                materiais, notícias e outros conteúdos do ForTEA.
            </p>

        <?php else: ?>

            <h1>
                Resultados para
                <strong>
                    "<?= htmlspecialchars($buscaOriginal) ?>"
                </strong>
            </h1>

            <?php if (count($resultados) > 0): ?>

                <p>
                    Encontramos <?= count($resultados) ?>
                    conteúdo(s) relacionado(s) à sua pesquisa.
                </p>

                <div class="resultados">

                    <?php foreach ($resultados as $pagina): ?>

                        <a
                            href="<?= htmlspecialchars($pagina['link']) ?>"
                            class="resultado-card"
                        >

                            <div class="resultado-icone">
                                <i class="<?= htmlspecialchars($pagina['icone']) ?>"></i>
                            </div>

                            <div class="resultado-conteudo">

                                <h2>
                                    <?= htmlspecialchars($pagina['titulo']) ?>
                                </h2>

                                <p>
                                    <?= htmlspecialchars($pagina['descricao']) ?>
                                </p>

                            </div>

                            <i class="fa-solid fa-arrow-right resultado-seta"></i>

                        </a>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="nenhum-resultado">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <h2>
                        Não encontramos esse conteúdo
                    </h2>

                    <p>
                        Não encontramos resultados para
                        <strong>
                            "<?= htmlspecialchars($buscaOriginal) ?>"
                        </strong>.
                    </p>

                    <p>
                        Tente pesquisar por termos como
                        <strong>autismo</strong>,
                        <strong>família</strong>,
                        <strong>direitos</strong>,
                        <strong>educação</strong>,
                        <strong>materiais</strong> ou
                        <strong>notícias</strong>.
                    </p>

                </div>

            <?php endif; ?>

        <?php endif; ?>

    </section>

</main>

<?php include 'includes/footer.php'; ?>


<style>/* PÁGINA DE PESQUISA */

.pagina-pesquisa {
    min-height: 70vh;
    padding: 170px 7% 80px;
    background: #f7f9fc;
}

.resultado-pesquisa {
    max-width: 1050px;
    margin: auto;
}

.tag-pesquisa {
    display: inline-block;
    margin-bottom: 12px;
    padding: 7px 14px;
    border-radius: 30px;
    background: #eaf2ff;
    color: #21479b;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.resultado-pesquisa h1 {
    color: #1e3a70;
    font-size: 32px;
    margin-bottom: 12px;
}

.resultado-pesquisa h1 strong {
    color: #3467eb;
}

.resultado-pesquisa > p {
    color: #64748b;
}

.resultados {
    display: grid;
    gap: 15px;
    margin-top: 35px;
}

.resultado-card {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 22px;
    background: #fff;
    border-radius: 16px;
    text-decoration: none;
    box-shadow: 0 5px 20px rgba(30, 58, 112, 0.07);
    transition: 0.2s ease;
}

.resultado-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(30, 58, 112, 0.12);
}

.resultado-icone {
    width: 45px;
    height: 45px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;
    background: #eaf2ff;
    color: #3467eb;
}

.resultado-card h2 {
    margin: 0 0 5px;
    color: #21479b;
    font-size: 19px;
}

.resultado-card p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.nenhum-resultado {
    margin-top: 35px;
    padding: 50px 30px;
    text-align: center;
    background: #fff;
    border-radius: 18px;
}

.nenhum-resultado > i {
    font-size: 40px;
    color: #3467eb;
    margin-bottom: 15px;
}

.nenhum-resultado h2 {
    color: #21479b;
}

.nenhum-resultado p {
    color: #64748b;
}</style>

<?php include 'includes/footer.php'; ?>