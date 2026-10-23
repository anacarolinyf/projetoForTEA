<?php
define('API_URL', 'http://localhost:8000');

function api_request($metodo, $rota, $dados = null) {
    $ch = curl_init(API_URL . $rota);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    if ($dados !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $resposta = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erro     = curl_error($ch);
    curl_close($ch);

    if ($resposta === false) {
        return ['status' => 0, 'dados' => null, 'erro' => $erro];
    }
    return ['status' => $status, 'dados' => json_decode($resposta, true), 'erro' => null];
}

// Atalhos para cada ação
function api_listar()               { return api_request('GET',    '/profissionais'); }
function api_ver($id)               { return api_request('GET',    "/profissionais/$id"); }
function api_cadastrar($dados)      { return api_request('POST',   '/profissionais', $dados); }
function api_editar($id, $dados)    { return api_request('PUT',    "/profissionais/$id", $dados); }
function api_excluir($id)           { return api_request('DELETE', "/profissionais/$id"); }