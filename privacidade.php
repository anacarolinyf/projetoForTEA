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

    <title>Política de privacidade - <?= htmlspecialchars($nomePlataforma) ?></title>

    <link rel="stylesheet"
          href="css/estilo.css?v=<?= @filemtime(__DIR__ . '/css/estilo.css') ?>">

    <link rel="stylesheet"
          href="css/documentos.css?v=<?= @filemtime(__DIR__ . '/css/documentos.css') ?>">

    <link rel="icon"
          type="image"
          href="img/logoo.png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
            <i class="fa-solid fa-shield-halved"></i>
            Política de privacidade
        </h1>

        <p class="doc-atualizacao">
            Última atualização: <?= htmlspecialchars($dataAtualizacao) ?>
        </p>

        <div class="doc-aviso">
            Sua privacidade é importante para o <?= htmlspecialchars($nomePlataforma) ?>.
            Aqui explicamos, de forma simples, quais dados coletamos, para que usamos,
            com quem podemos compartilhar e quais são os seus direitos, de acordo com a
            Lei Geral de Proteção de Dados (LGPD, Lei nº 13.709/2018).
        </div>


        <nav class="doc-sumario">

            <strong>Neste documento</strong>

            <ol>
                <li><a href="#quem">Quem somos</a></li>
                <li><a href="#dados">Dados que coletamos</a></li>
                <li><a href="#finalidades">Para que usamos seus dados</a></li>
                <li><a href="#compartilhamento">Compartilhamento</a></li>
                <li><a href="#cookies">Cookies e sessão</a></li>
                <li><a href="#seguranca">Segurança</a></li>
                <li><a href="#retencao">Por quanto tempo guardamos</a></li>
                <li><a href="#direitos">Seus direitos</a></li>
                <li><a href="#menores">Crianças e adolescentes</a></li>
                <li><a href="#alteracoes">Alterações nesta política</a></li>
                <li><a href="#contato">Contato</a></li>
            </ol>

        </nav>


        <section id="quem">
            <h2>1. Quem somos</h2>
            <p>
                O <?= htmlspecialchars($nomePlataforma) ?> é uma plataforma de informação e apoio
                sobre o Transtorno do Espectro Autista (TEA). O responsável pelo tratamento dos
                seus dados pessoais (o "controlador", na linguagem da LGPD) é
                <strong><?= htmlspecialchars($responsavel) ?></strong>, que pode ser contatado
                pelo e-mail
                <a class="doc-link"
                   href="mailto:<?= htmlspecialchars($emailContato) ?>"><?= htmlspecialchars($emailContato) ?></a>.
            </p>
        </section>


        <section id="dados">
            <h2>2. Dados que coletamos</h2>

            <table class="doc-tabela">
                <thead>
                    <tr>
                        <th>Tipo de dado</th>
                        <th>O que é</th>
                        <th>Como coletamos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Dados de cadastro</td>
                        <td>Nome, e-mail e senha. A senha é guardada de forma criptografada (hash), então nem a equipe consegue ver a original.</td>
                        <td>Você informa ao criar a conta</td>
                    </tr>
                    <tr>
                        <td>Foto de perfil</td>
                        <td>Imagem JPG, PNG ou WEBP, que é opcional.</td>
                        <td>Você envia em "Editar perfil"</td>
                    </tr>
                    <tr>
                        <td>Uso da plataforma</td>
                        <td>Favoritos salvos (profissionais, materiais e leis), profissionais que você contatou e a data do último contato, e suas preferências de notificação.</td>
                        <td>Gerado quando você usa os recursos</td>
                    </tr>
                    <tr>
                        <td>Conversas com o assistente virtual</td>
                        <td>As mensagens que você escreve no chatbot.</td>
                        <td>Você digita no chat</td>
                    </tr>
                    <tr>
                        <td>Dados de sessão</td>
                        <td>Um identificador temporário que mantém você conectado.</td>
                        <td>Automático, por cookie</td>
                    </tr>
                </tbody>
            </table>

            <p>
                <strong>Não pedimos dados de saúde, diagnósticos ou documentos pessoais.</strong>
                Pedimos que você também não escreva esse tipo de informação, sua ou de outra
                pessoa, no chatbot ou em campos livres do site.
            </p>
            <p>
                Quando você clica em "Conversar" com um profissional, registramos apenas
                <strong>que o contato aconteceu e a data</strong>, para montar a lista de
                "Contatos realizados" no seu perfil. Não temos acesso ao conteúdo da conversa
                feita por WhatsApp ou telefone.
            </p>
        </section>


        <section id="finalidades">
            <h2>3. Para que usamos seus dados</h2>

            <table class="doc-tabela">
                <thead>
                    <tr>
                        <th>Finalidade</th>
                        <th>Base legal (LGPD)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Criar e manter sua conta, permitir o login e a recuperação de senha</td>
                        <td>Execução do serviço que você pediu (art. 7º, V)</td>
                    </tr>
                    <tr>
                        <td>Mostrar seus favoritos, contatos realizados e seu perfil</td>
                        <td>Execução do serviço (art. 7º, V)</td>
                    </tr>
                    <tr>
                        <td>Exibir sua foto de perfil e enviar notificações, lembretes e novidades</td>
                        <td>Seu consentimento (art. 7º, I), que você pode retirar quando quiser</td>
                    </tr>
                    <tr>
                        <td>Manter a segurança, evitar fraudes e corrigir erros do site</td>
                        <td>Legítimo interesse (art. 7º, IX)</td>
                    </tr>
                    <tr>
                        <td>Cumprir obrigações legais ou ordens de autoridades</td>
                        <td>Obrigação legal (art. 7º, II)</td>
                    </tr>
                </tbody>
            </table>

            <p>
                Não vendemos seus dados e não os usamos para propaganda de terceiros.
            </p>
        </section>


        <section id="compartilhamento">
            <h2>4. Compartilhamento</h2>
            <p>Seus dados só são compartilhados quando necessário para o site funcionar:</p>
            <ul>
                <li><strong>Serviços técnicos:</strong> hospedagem do site e banco de dados, serviço de envio de e-mails (usado, por exemplo, na recuperação de senha) e arquivos carregados de redes de distribuição (CDN), como ícones e o recurso de acessibilidade. Esses serviços podem registrar dados técnicos como o endereço IP.</li>
                <li><strong>Profissionais:</strong> não enviamos seus dados aos profissionais. O contato é feito por você, por WhatsApp, telefone ou site, e passa a seguir as regras desse serviço.</li>
                <li><strong>Autoridades:</strong> quando houver ordem judicial ou exigência legal.</li>
            </ul>
        </section>


        <section id="cookies">
            <h2>5. Cookies e sessão</h2>
            <p>
                Usamos um cookie de sessão essencial para manter você logado enquanto navega.
                Ele é apagado quando você sai da conta ou fecha o navegador, conforme as
                configurações do seu aparelho. Serviços de terceiros carregados pelo site
                também podem usar seus próprios cookies; você pode gerenciá-los nas
                configurações do navegador.
            </p>
        </section>


        <section id="seguranca">
            <h2>6. Segurança</h2>
            <p>
                Adotamos medidas técnicas e organizacionais razoáveis para proteger seus dados,
                como senhas criptografadas e controle de acesso às áreas logadas. Mesmo assim,
                nenhum sistema é 100% seguro. Por isso, escolha uma senha forte, não a
                compartilhe e saia da conta ao usar computadores de outras pessoas.
            </p>
            <p>
                Se ocorrer um incidente de segurança que possa causar risco ou dano relevante,
                avisaremos você e a Autoridade Nacional de Proteção de Dados (ANPD), como a lei
                determina.
            </p>
        </section>


        <section id="retencao">
            <h2>7. Por quanto tempo guardamos</h2>
            <p>
                Guardamos seus dados enquanto sua conta estiver ativa. Se você pedir a exclusão
                da conta, apagamos ou anonimizamos seus dados, exceto aqueles que precisarmos
                manter para cumprir obrigações legais ou para nos defender em processos, pelo
                tempo que a lei exigir.
            </p>
        </section>


        <section id="direitos">
            <h2>8. Seus direitos</h2>
            <p>De acordo com a LGPD (art. 18), você pode pedir a qualquer momento:</p>
            <ul>
                <li>a confirmação de que tratamos seus dados e o acesso a eles;</li>
                <li>a correção de dados incompletos, errados ou desatualizados;</li>
                <li>a anonimização, o bloqueio ou a eliminação de dados desnecessários ou tratados em desacordo com a lei;</li>
                <li>a portabilidade dos dados para outro serviço, quando aplicável;</li>
                <li>a eliminação dos dados tratados com o seu consentimento;</li>
                <li>informações sobre com quem compartilhamos seus dados;</li>
                <li>informações sobre a possibilidade de não consentir e suas consequências;</li>
                <li>a revogação do consentimento.</li>
            </ul>
            <p>
                Você já pode corrigir nome, e-mail, foto e senha em
                <a class="doc-link" href="editar_perfil.php">Editar perfil</a> e ajustar as
                notificações em <a class="doc-link" href="configuracoes.php">Configurações</a>.
                Para os demais pedidos, escreva para
                <a class="doc-link"
                   href="mailto:<?= htmlspecialchars($emailContato) ?>"><?= htmlspecialchars($emailContato) ?></a>.
                Se achar que seus direitos não foram respeitados, você também pode
                apresentar reclamação à ANPD.
            </p>
        </section>


        <section id="menores">
            <h2>9. Crianças e adolescentes</h2>
            <p>
                O <?= htmlspecialchars($nomePlataforma) ?> é voltado principalmente a adultos:
                famílias, responsáveis, educadores e profissionais. Não coletamos
                intencionalmente dados de menores de 18 anos. Se um adolescente quiser usar a
                plataforma, é importante que isso aconteça com o conhecimento e a supervisão de
                um responsável. Se percebermos que dados de uma criança foram informados sem
                a autorização de um responsável legal, vamos excluí-los.
            </p>
        </section>


        <section id="alteracoes">
            <h2>10. Alterações nesta política</h2>
            <p>
                Podemos atualizar esta política quando houver mudanças no site ou na lei. A data
                da última atualização aparece no topo da página. Se a mudança for importante,
                avisaremos na própria plataforma.
            </p>
        </section>


        <section id="contato">
            <h2>11. Contato</h2>
            <p>
                Para dúvidas sobre esta política ou para exercer seus direitos, fale com a
                gente em
                <a class="doc-link"
                   href="mailto:<?= htmlspecialchars($emailContato) ?>"><?= htmlspecialchars($emailContato) ?></a>.
            </p>
            <p>Responsável pela plataforma: <?= htmlspecialchars($responsavel) ?>.</p>
        </section>


        <div class="doc-rodape">

            <a href="termos.php" class="doc-botao doc-botao-claro">
                <i class="fa-solid fa-file-contract"></i>
                Ver termos de uso
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