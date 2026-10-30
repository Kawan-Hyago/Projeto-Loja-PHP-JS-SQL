const URL_PROXY = '../../../servidor/proxy.php';

async function chamarApi(modulo, acao, dados = {}) {
    const resposta = await fetch(`${URL_PROXY}?modulo=${modulo}&acao=${acao}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dados)
    });
    const json = await resposta.json();
    if (!resposta.ok || json.sucesso === false) {
        throw new Error(json.mensagem || 'Erro na requisição');
    }
    return json;
}
