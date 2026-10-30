const formulario = document.getElementById('formulario');
const titulo = document.getElementById('titulo');
const mensagem = document.getElementById('mensagem');
const campoCliente = document.getElementById('cliente');
const campoData = document.getElementById('data');
const listaProdutos = document.getElementById('listaProdutos');
const campoTotal = document.getElementById('total');

const idVenda = new URLSearchParams(location.search).get('id');

function mostrarMensagem(texto) {
    mensagem.textContent = texto;
    mensagem.hidden = false;
}

function limparMensagem() {
    mensagem.textContent = '';
    mensagem.hidden = true;
}

function formatarMoeda(valor) {
    return Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function obterMarcados() {
    return Array.from(listaProdutos.querySelectorAll('input[type="checkbox"]:checked'));
}

function atualizarTotal() {
    const total = obterMarcados().reduce((soma, caixa) => soma + Number(caixa.dataset.preco), 0);
    campoTotal.textContent = formatarMoeda(total);
}

function montarClientes(clientes) {
    clientes.forEach(cliente => {
        const opcao = document.createElement('option');
        opcao.value = cliente.id;
        opcao.textContent = cliente.nome;
        campoCliente.appendChild(opcao);
    });
}

function montarProdutos(produtos) {
    produtos.forEach(produto => {
        const rotulo = document.createElement('label');
        rotulo.className = 'item-produto';

        const caixa = document.createElement('input');
        caixa.type = 'checkbox';
        caixa.value = produto.id;
        caixa.dataset.preco = produto.preco;
        caixa.addEventListener('change', atualizarTotal);

        const texto = document.createElement('span');
        texto.textContent = `${produto.nome} - ${formatarMoeda(produto.preco)}`;

        rotulo.appendChild(caixa);
        rotulo.appendChild(texto);
        listaProdutos.appendChild(rotulo);
    });
}

function preencherVenda(venda) {
    campoCliente.value = venda.cliente_id;
    campoData.value = venda.data_venda;

    const idsMarcados = venda.produtos.map(produto => String(produto.id));
    listaProdutos.querySelectorAll('input[type="checkbox"]').forEach(caixa => {
        caixa.checked = idsMarcados.includes(caixa.value);
    });

    atualizarTotal();
}

async function iniciar() {
    try {
        const [clientes, produtos] = await Promise.all([
            chamarApi('cliente', 'listar'),
            chamarApi('produto', 'listar')
        ]);

        montarClientes(clientes);
        montarProdutos(produtos);

        if (idVenda) {
            titulo.textContent = 'Editar venda';
            const vendas = await chamarApi('venda', 'listar');
            const venda = vendas.find(item => String(item.id) === String(idVenda));

            if (!venda) {
                mostrarMensagem('Venda não encontrada');
                return;
            }

            preencherVenda(venda);
        } else {
            campoData.value = new Date().toLocaleDateString('en-CA');
        }
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}

formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    limparMensagem();

    const dados = {
        cliente_id: Number(campoCliente.value),
        data_venda: campoData.value,
        produtos: obterMarcados().map(caixa => Number(caixa.value))
    };

    try {
        if (idVenda) {
            dados.id = Number(idVenda);
            await chamarApi('venda', 'editar', dados);
        } else {
            await chamarApi('venda', 'salvar', dados);
        }
        window.location.href = 'listar.html';
    } catch (erro) {
        mostrarMensagem(erro.message);
    }
});

document.addEventListener('DOMContentLoaded', iniciar);
