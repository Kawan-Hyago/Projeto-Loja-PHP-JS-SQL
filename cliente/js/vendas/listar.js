const tabela = document.getElementById('tabela');
const mensagem = document.getElementById('mensagem');

function mostrarMensagem(texto) {
    mensagem.textContent = texto;
    mensagem.hidden = false;
}

function limparMensagem() {
    mensagem.textContent = '';
    mensagem.hidden = true;
}

function criarCelula(texto) {
    const celula = document.createElement('td');
    celula.textContent = texto;
    return celula;
}

function formatarData(data) {
    const [ano, mes, dia] = data.split('-');
    return `${dia}/${mes}/${ano}`;
}

function montarLinha(venda) {
    const linha = document.createElement('tr');
    linha.appendChild(criarCelula(venda.id));
    linha.appendChild(criarCelula(venda.cliente_nome));
    linha.appendChild(criarCelula(formatarData(venda.data_venda)));
    linha.appendChild(criarCelula(Number(venda.total_venda).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })));

    const acoes = document.createElement('td');

    const editar = document.createElement('a');
    editar.className = 'botao';
    editar.href = `cadastrar.html?id=${venda.id}`;
    editar.textContent = 'Editar';

    const remover = document.createElement('button');
    remover.type = 'button';
    remover.textContent = 'Remover';
    remover.addEventListener('click', () => removerVenda(venda));

    acoes.appendChild(editar);
    acoes.appendChild(remover);
    linha.appendChild(acoes);

    return linha;
}

async function carregarVendas() {
    try {
        const vendas = await chamarApi('venda', 'listar');
        tabela.innerHTML = '';

        if (vendas.length === 0) {
            const linha = document.createElement('tr');
            const celula = criarCelula('Nenhum registro cadastrado');
            celula.colSpan = 5;
            linha.appendChild(celula);
            tabela.appendChild(linha);
            return;
        }

        vendas.forEach(venda => tabela.appendChild(montarLinha(venda)));
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

async function removerVenda(venda) {
    if (!confirm(`Deseja remover a venda ${venda.id}?`)) {
        return;
    }

    try {
        limparMensagem();
        await chamarApi('venda', 'deletar', { id: venda.id });
        await carregarVendas();
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

document.addEventListener('DOMContentLoaded', carregarVendas);
