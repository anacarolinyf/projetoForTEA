
<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/material.css">

<main class="materiais-page">

    <!-- CABEÇALHO -->
    <section class="materiais-intro">
        <div class="materiais-container">

            <span class="materiais-tag">
                CENTRO DE RECURSOS
            </span>

            <h1>Materiais e recursos pedagógicos</h1>

            <p>
                Consulte nossa biblioteca de materiais educativos,
                desenvolvidos para auxiliar no desenvolvimento,
                na aprendizagem e na autonomia de pessoas autistas.
            </p>

        </div>
    </section>

    <!-- ÁREA PRINCIPAL -->
    <section class="materiais-area">

        <div class="materiais-container">

            <div class="materiais-layout">

                <!-- MENU LATERAL -->
                <aside class="materiais-sidebar">

                    <h3 class="sidebar-titulo">
                        Categorias
                    </h3>

                    <div class="categorias-menu">

                        <button class="categoria-link ativo"
                                onclick="filtrarMateriais('TODOS', this)">
                            Todos os materiais
                        </button>

                        <button class="categoria-link"
                                onclick="filtrarMateriais('ROTINA', this)">
                            Rotina
                        </button>

                        <button class="categoria-link"
                                onclick="filtrarMateriais('EMOÇÕES', this)">
                            Emoções
                        </button>

                        <button class="categoria-link"
                                onclick="filtrarMateriais('CUIDADOS', this)">
                            Cuidados
                        </button>

                        <button class="categoria-link"
                                onclick="filtrarMateriais('VIDA ADULTA', this)">
                            Vida adulta
                        </button>

                    </div>

                </aside>

                <!-- CONTEÚDO -->
                <div class="materiais-conteudo-principal">

                    <div class="materiais-topo">

                        <h2>Biblioteca de materiais</h2>

                        <p>
                            Selecione um recurso para consultar
                            o conteúdo completo.
                        </p>

                    </div>

                    <!-- CONTADOR -->
                    <div class="materiais-resultados">

                        <span>Recursos disponíveis</span>

                        <strong id="contadorMateriais">6 materiais</strong>

                    </div>

                    <!-- LISTA DE MATERIAIS -->
                    <div class="materiais-lista" id="listaMateriais">

                        <!-- MATERIAL 1 -->
                        <article class="material-item"
                                 data-categoria="ROTINA">

                            <div class="material-indicador"></div>

                            <div class="material-item-conteudo">

                                <span class="material-item-categoria">
                                    ROTINA
                                </span>

                                <h3>Melhora da Alimentação</h3>

                                <p>
                                    Recursos visuais e estratégias práticas
                                    para auxiliar na introdução alimentar,
                                    aceitação de novos alimentos e organização
                                    das refeições no dia a dia.
                                </p>

                                <div class="material-detalhes">
                                    <span>Pais e educadores</span>
                                    <span>Documento PDF</span>
                                </div>

                            </div>

                            <div class="material-acesso">
                                <a href="pdfs/Alimentação.pdf"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    Consultar material
                                </a>
                            </div>

                        </article>

                        <!-- MATERIAL 2 -->
                        <article class="material-item"
                                 data-categoria="EMOÇÕES">

                            <div class="material-indicador"></div>

                            <div class="material-item-conteudo">

                                <span class="material-item-categoria">
                                    EMOÇÕES
                                </span>

                                <h3>Comportamentos agressivos?</h3>

                                <p>
                                    Ferramentas visuais para auxiliar
                                    na identificação de possíveis gatilhos,
                                    compreensão das emoções intensas e
                                    desenvolvimento de estratégias
                                    de autorregulação.
                                </p>

                                <div class="material-detalhes">
                                    <span>Crianças</span>
                                    <span>Documento PDF</span>
                                </div>

                            </div>

                            <div class="material-acesso">
                                <a href="pdfs/Comportamento agressivos.pdf"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    Consultar material
                                </a>
                            </div>

                        </article>

                        <!-- MATERIAL 3 -->
                        <article class="material-item"
                                 data-categoria="CUIDADOS">

                            <div class="material-indicador"></div>

                            <div class="material-item-conteudo">

                                <span class="material-item-categoria">
                                    CUIDADOS
                                </span>

                                <h3>Corte de Cabelo</h3>

                                <p>
                                    História social e sequenciamento
                                    ilustrado para preparar a criança
                                    antes e durante a visita
                                    ao cabeleireiro.
                                </p>

                                <div class="material-detalhes">
                                    <span>Crianças</span>
                                    <span>Documento PDF</span>
                                </div>

                            </div>

                            <div class="material-acesso">
                                <a href="pdfs/Corte de cabelo.pdf"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    Consultar material
                                </a>
                            </div>

                        </article>

                        <!-- MATERIAL 4 -->
                        <article class="material-item"
                                 data-categoria="CUIDADOS">

                            <div class="material-indicador"></div>

                            <div class="material-item-conteudo">

                                <span class="material-item-categoria">
                                    CUIDADOS
                                </span>

                                <h3>Higiene Bucal</h3>

                                <p>
                                    Guia ilustrado com orientações
                                    para desenvolver hábitos de higiene
                                    bucal, estimular a autonomia e
                                    facilitar a escovação diária.
                                </p>

                                <div class="material-detalhes">
                                    <span>Crianças</span>
                                    <span>Documento PDF</span>
                                </div>

                            </div>

                            <div class="material-acesso">
                                <a href="pdfs/Higiene Bucal.pdf"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    Consultar material
                                </a>
                            </div>

                        </article>

                        <!-- MATERIAL 5 -->
                        <article class="material-item"
                                 data-categoria="VIDA ADULTA">

                            <div class="material-indicador"></div>

                            <div class="material-item-conteudo">

                                <span class="material-item-categoria">
                                    VIDA ADULTA
                                </span>

                                <h3>Transição para a vida adulta</h3>

                                <p>
                                    Materiais voltados ao desenvolvimento
                                    da autonomia, independência pessoal,
                                    organização da rotina e habilidades
                                    essenciais para o cotidiano.
                                </p>

                                <div class="material-detalhes">
                                    <span>Público em transição</span>
                                    <span>Documento PDF</span>
                                </div>

                            </div>

                            <div class="material-acesso">
                                <a href="pdfs/Vida Adulta.pdf"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    Consultar material
                                </a>
                            </div>

                        </article>

                        <!-- MATERIAL 6 -->
                        <article class="material-item"
                                 data-categoria="ROTINA">

                            <div class="material-indicador"></div>

                            <div class="material-item-conteudo">

                                <span class="material-item-categoria">
                                    ROTINA
                                </span>

                                <h3>Desafios com o sono?</h3>

                                <p>
                                    Atividades e rotinas visuais
                                    estruturadas para organizar o
                                    momento de descanso e favorecer
                                    hábitos de sono mais regulares.
                                </p>

                                <div class="material-detalhes">
                                    <span>Crianças</span>
                                    <span>Documento PDF</span>
                                </div>

                            </div>

                            <div class="material-acesso">
                                <a href="pdfs/Melhorar o sono da criança.pdf"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    Consultar material
                                </a>
                            </div>

                        </article>

                    </div>

                    <!-- NENHUM RESULTADO -->
                    <div class="material-vazio" id="materialVazio">

                        <h3>Nenhum material encontrado</h3>

                        <p>
                            Não existem materiais cadastrados
                            nesta categoria.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

<script>

function filtrarMateriais(categoria, botao) {

    const materiais = document.querySelectorAll('.material-item');

    let quantidade = 0;

    document.querySelectorAll('.categoria-link').forEach(item => {
        item.classList.remove('ativo');
    });

    botao.classList.add('ativo');

    materiais.forEach(material => {

        const categoriaMaterial = material.dataset.categoria;

        if (
            categoria === 'TODOS' ||
            categoriaMaterial === categoria
        ) {

            material.style.display = 'grid';
            quantidade++;

        } else {

            material.style.display = 'none';

        }

    });

    document.getElementById('contadorMateriais').textContent =
        quantidade + (quantidade === 1 ? ' material' : ' materiais');

    document.getElementById('materialVazio').style.display =
        quantidade === 0 ? 'block' : 'none';

}

</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
