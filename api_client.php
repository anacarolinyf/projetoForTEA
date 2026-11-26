<?php
define('API_URL', 'http://localhost:8000');


define('API_KEY', getenv('TEAFOR_API_KEY') ?: '');

function api_request($metodo, $rota, $dados = null) {
    $ch = curl_init(API_URL . $rota);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $cabecalhos = [];
    if (API_KEY !== '') {
        $cabecalhos[] = 'X-API-Key: ' . API_KEY;
    }
    if ($dados !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        $cabecalhos[] = 'Content-Type: application/json';
    }
    if ($cabecalhos) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cabecalhos);
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

// ---------- Profissionais (como antes) ----------
function api_listar()               { return api_request('GET',    '/profissionais'); }
function api_ver($id)               { return api_request('GET',    "/profissionais/$id"); }
function api_cadastrar($dados)      { return api_request('POST',   '/profissionais', $dados); }
function api_editar($id, $dados)    { return api_request('PUT',    "/profissionais/$id", $dados); }
function api_excluir($id)           { return api_request('DELETE', "/profissionais/$id"); }

// ---------- Qualquer outra tabela (rotinas, checklist, trajetória...) ----------
// $tabela é o nome da rota na API, ex.: 'rotina_itens'
function api_t_listar($tabela, $filtros = []) {
    $qs = $filtros ? '?' . http_build_query($filtros) : '';
    return api_request('GET', "/$tabela$qs");
}
function api_t_ver($tabela, $id)            { return api_request('GET',    "/$tabela/" . (int) $id); }
function api_t_cadastrar($tabela, $dados)   { return api_request('POST',   "/$tabela", $dados); }
function api_t_editar($tabela, $id, $dados) { return api_request('PUT',    "/$tabela/" . (int) $id, $dados); }
function api_t_excluir($tabela, $id)        { return api_request('DELETE', "/$tabela/" . (int) $id); }

// rotina_progresso não tem id: a chave é item_id + dia
function api_progresso_excluir($item_id, $dia) {
    return api_request('DELETE', '/rotina_progresso/' . (int) $item_id . '/' . rawurlencode($dia));
}

// Texto de erro amigável a partir da resposta da API
function api_erro($r, $padrao = 'Não foi possível concluir.') {
    if ($r['status'] === 0) {
        return 'A API não respondeu. Ela está rodando?';
    }
    if (is_string($r['dados']['detail'] ?? null)) {
        return $r['dados']['detail'];
    }
    if ($r['status'] === 422) {
        return 'Dados inválidos. Confira os campos e o formato (datas AAAA-MM-DD, horário HH:MM).';
    }
    return $padrao;
}