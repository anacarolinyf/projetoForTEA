<?php
// Modelo do config_email.php (Gmail).
// Copie os valores para o seu config_email.php e preencha usuário e senha de app.

return [
    'host'       => 'smtp.gmail.com',
    'username'   => 'projetofortea@gmail.com',
    'password'   => 'SENHA_DE_APP_AQUI',   // 16 letras, sem espaços
    'encryption' => 'tls',                 // STARTTLS
    'port'       => 587,

    'from_email' => 'SEU_EMAIL@gmail.com', // no Gmail, use o mesmo e-mail do username
    'from_name'  => 'ForTEA',
];