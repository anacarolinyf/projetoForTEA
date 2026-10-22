<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "conexao.php";
require_once "includes/documentos_config.php";

require_once "includes/header.php";

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Termos de uso - <?= htmlspecialchars($nomePlataforma) ?></title>

    <link rel="stylesheet"
          href="css/estilo.css?v=<?= @filemtime(__DIR__ . '/css/estilo.css') ?>">

    <link rel="stylesheet"
          href="css/documentos.css?v=<?= @filemtime(__DIR__ . '/css/documentos.css') ?>">

    <link rel="icon"
          type="image"
          href="img/logoo.png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="css/laterais.css">

</head>

<body>

<?php include "includes/laterais.php"; ?>

<main class="doc-pagina">

    <article class="doc-card">

        <a href="configuracoes.php" class="doc-voltar">
            <i class="fa-solid fa-arrow-left"></i>
            Voltar às configurações
        </a>

        <h1>
            <i class="fa-solid fa-file-contract"></i>
            Termos de uso
        </h1>

        <p class="doc-atualizacao">
            Última atualização: <?= htmlspecialchars($dataAtualizacao) ?>
        </p>

        <div class="doc-aviso">
            Ao criar uma conta ou usar o <?= htmlspecialchars($nomePlataforma) ?>,
            você concorda com estes termos. Leia com calma e, se tiver qualquer dúvida,
            fale com a gente.
        </div>


        <nav class="doc-sumario">

            <strong>Neste documento</strong>

            <ol>
                <li><a href="#aceitacao">Aceitação dos termos</a></li>
                <li><a href="#plataforma">O que é o <?= htmlspecialchars($nomePlataforma) ?></a></li>
                <li><a href="#informativo">Conteúdo informativo</a></li>
                <li><a href="#cadastro">Cadastro e conta</a></li>
                <li><a href="#uso">Uso adequado</a></li>
                <li><a href="#profissionais">Profissionais e terceiros</a></li>
                <li><a href="#chatbot">Assistente virtual (chatbot)</a></li>
                <li><a href="#conteudo">Conteúdo e propriedade intelectual</a></li>
                <li><a href="#disponibilidade">Disponibilidade da plataforma</a></li>
                <li><a href="#responsabilidade">Limitação de responsabilidade</a></li>
                <li><a href="#encerramento">Suspensão e encerramento</a></li>
                <li><a href="#alteracoes">Alterações nos termos</a></li>
                <li><a href="#lei">Lei aplicável</a></li>
                <li><a href="#contato">Contato</a></li>
            </ol>

        </nav>


        <section id="aceitacao">
            <h2>1. Aceitação dos termos</h2>
            <p>
                Estes Termos de uso regulam o acesso e a utilização do site
                <?= htmlspecialchars($nomePlataforma) ?>. Ao se cadastrar, entrar na sua conta
                ou continuar navegando, você declara que leu e concorda com as regras aqui
                descritas e com a nossa
                <a class="doc-link" href="privacidade.php">Política de privacidade</a>.
                Se você não concordar, pedimos que não utilize a plataforma.
            </p>
        </section>


        <section id="plataforma">
            <h2>2. O que é o <?= htmlspecialchars($nomePlataforma) ?></h2>
            <p>
                O <?= htmlspecialchars($nomePlataforma) ?> é uma plataforma de informação e apoio
                voltada a famílias, responsáveis, educadores e pessoas interessadas no Transtorno
                do Espectro Autista (TEA). Reunimos conteúdos sobre o tema, guias para famílias,
                informações sobre direitos, materiais de apoio, uma biblioteca e uma lista de
                profissionais e clínicas, além de recursos pessoais como favoritos, histórico de
                contatos, trajetória e checklist.
            </p>
        </section>


        <section id="informativo">
            <h2>3. Conteúdo informativo</h2>
            <p>
                Todo o conteúdo do <?= htmlspecialchars($nomePlataforma) ?> tem caráter
                <strong>exclusivamente informativo e educativo</strong>. Ele não substitui
                diagnóstico, avaliação, tratamento, acompanhamento ou orientação de médicos,
                psicólogos, terapeutas, pedagogos, advogados ou outros profissionais habilitados.
            </p>
            <p>
                As informações sobre direitos e leis são um resumo para facilitar a compreensão
                e podem mudar ao longo do tempo. Para decisões importantes, consulte a legislação
                vigente e um profissional de confiança.
            </p>
        </section>


        <section id="cadastro">
            <h2>4. Cadastro e conta</h2>
            <p>Para usar alguns recursos, é preciso criar uma conta. Ao se cadastrar, você se compromete a:</p>
            <ul>
                <li>fornecer informações verdadeiras, completas e atualizadas;</li>
                <li>ser maior de 18 anos ou utilizar a plataforma com a supervisão de um responsável legal;</li>
                <li>manter sua senha em segredo e não compartilhar o acesso à sua conta;</li>
                <li>nos avisar caso perceba qualquer uso não autorizado da sua conta.</li>
            </ul>
            <p>
                Você é responsável por tudo o que for feito com a sua conta.
            </p>
        </section>


        <section id="uso">
            <h2>5. Uso adequado</h2>
            <p>Ao usar o <?= htmlspecialchars($nomePlataforma) ?>, você concorda em <strong>não</strong>:</p>
            <ul>
                <li>usar a plataforma para fins ilegais, ofensivos, discriminatórios ou que violem direitos de outras pessoas;</li>
                <li>tentar acessar áreas restritas, contas de outros usuários ou o banco de dados;</li>
                <li>enviar vírus, códigos maliciosos ou qualquer coisa que prejudique o funcionamento do site;</li>
                <li>copiar, vender ou redistribuir o conteúdo da plataforma sem autorização;</li>
                <li>se passar por outra pessoa ou fornecer dados falsos de propósito.</li>
            </ul>
        </section>


        <section id="profissionais">
            <h2>6. Profissionais e terceiros</h2>
            <p>
                A lista de profissionais e clínicas tem o objetivo de facilitar o contato das
                famílias. O <?= htmlspecialchars($nomePlataforma) ?> apenas divulga as informações
                e <strong>não presta, intermedeia, garante ou recomenda</strong> nenhum serviço
                de saúde, educação ou outro serviço listado.
            </p>
            <ul>
                <li>A conversa, o agendamento, o atendimento e o pagamento acontecem diretamente entre você e o profissional, fora da plataforma.</li>
                <li>Recomendamos que você confira o registro no conselho profissional (como CRM, CRP, CREFITO, CREFONO, entre outros) e avalie se o atendimento é adequado para a sua necessidade.</li>
                <li>Não nos responsabilizamos por informações desatualizadas fornecidas por terceiros, nem por resultados, preços ou condições de atendimento.</li>
                <li>Ao clicar em links para WhatsApp, telefone ou sites externos, você sai do <?= htmlspecialchars($nomePlataforma) ?> e passa a seguir as regras do serviço de terceiros.</li>
            </ul>
        </section>


        <section id="chatbot">
            <h2>7. Assistente virtual (chatbot)</h2>
            <p>
                O assistente virtual oferece respostas automáticas para ajudar na navegação e em
                dúvidas gerais. Ele pode cometer erros ou trazer informações incompletas e
                <strong>não substitui a opinião de um profissional</strong>. Evite informar dados
                muito pessoais ou sensíveis, como documentos, endereço completo ou detalhes de
                saúde, durante a conversa.
            </p>
        </section>


        <section id="conteudo">
            <h2>8. Conteúdo e propriedade intelectual</h2>
            <p>
                O nome, a logomarca, o design, os textos e os demais elementos do
                <?= htmlspecialchars($nomePlataforma) ?> pertencem à equipe responsável ou são usados
                com autorização. Materiais de terceiros, como documentos, leis, vídeos e
                publicações da biblioteca, pertencem aos seus respectivos autores e são
                indicados com a devida origem sempre que possível.
            </p>
            <p>
                Você pode usar o conteúdo para fins pessoais e educativos, citando a fonte.
                Se acreditar que algum conteúdo viola direitos autorais, entre em contato
                para que possamos analisar e corrigir.
            </p>
        </section>


        <section id="disponibilidade">
            <h2>9. Disponibilidade da plataforma</h2>
            <p>
                Trabalhamos para manter o site no ar e funcionando bem, mas não garantimos que
                ele ficará disponível o tempo todo, sem erros ou interrupções. Podemos
                realizar manutenções, atualizações, mudanças ou até descontinuar recursos
                quando necessário.
            </p>
        </section>


        <section id="responsabilidade">
            <h2>10. Limitação de responsabilidade</h2>
            <p>
                Dentro do que a lei permite, o <?= htmlspecialchars($nomePlataforma) ?> não se
                responsabiliza por decisões tomadas com base nas informações do site, por
                serviços prestados por terceiros, por falhas de internet ou de equipamentos do
                usuário, nem por danos causados pelo uso indevido da conta por culpa do próprio
                usuário. Nada nestes termos exclui direitos garantidos por lei, como os do
                Código de Defesa do Consumidor, quando aplicáveis.
            </p>
        </section>


        <section id="encerramento">
            <h2>11. Suspensão e encerramento</h2>
            <p>
                Você pode parar de usar a plataforma a qualquer momento e pedir a exclusão da sua
                conta pelo e-mail de contato. Também podemos suspender ou encerrar contas que
                descumpram estes termos, sempre que possível avisando o motivo.
            </p>
        </section>


        <section id="alteracoes">
            <h2>12. Alterações nos termos</h2>
            <p>
                Podemos atualizar estes termos quando necessário. A data da última atualização
                fica sempre no topo desta página. Se as mudanças forem importantes, avisaremos
                na própria plataforma. Continuar usando o site depois da atualização significa
                que você concorda com a nova versão.
            </p>
        </section>


        <section id="lei">
            <h2>13. Lei aplicável</h2>
            <p>
                Estes termos seguem as leis do Brasil. Para resolver qualquer questão
                relacionada a eles, fica eleito o foro do domicílio do usuário, conforme a
                legislação de defesa do consumidor.
            </p>
        </section>


        <section id="contato">
            <h2>14. Contato</h2>
            <p>
                Dúvidas, sugestões ou pedidos sobre estes termos podem ser enviados para
                <a class="doc-link"
                   href="mailto:<?= htmlspecialchars($emailContato) ?>"><?= htmlspecialchars($emailContato) ?></a>.
            </p>
            <p>Responsável pela plataforma: <?= htmlspecialchars($responsavel) ?>.</p>
        </section>


        <div class="doc-rodape">

            <a href="privacidade.php" class="doc-botao doc-botao-claro">
                <i class="fa-solid fa-shield-halved"></i>
                Ver política de privacidade
            </a>

            <button type="button" class="doc-botao" onclick="window.print()">
                <i class="fa-solid fa-print"></i>
                Imprimir
            </button>

        </div>

    </article>

</main>

</body>

</html>