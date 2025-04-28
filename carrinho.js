import { firebaseConfig } from './firebaseConfig.js';

function adicionarAoCarrinho(nome, preco) {
    const itens = JSON.parse(localStorage.getItem('carrinho')) || [];

    // Verifica se já existe esse item no carrinho
    const existente = itens.find(item => item.nome === nome);
    if (existente) {
        // Aumenta a quantidade se o item já existir
        existente.quantidade += 1;
    } else {
        // Se o item não existir, adiciona um novo item
        itens.push({ nome, preco: parseFloat(preco), quantidade: 1 });
    }

    // Atualiza o LocalStorage com o carrinho
    localStorage.setItem('carrinho', JSON.stringify(itens));
    atualizarCarrinho();

    // Mostrar o carrinho se estiver oculto
    const carrinhoEl = document.getElementById('carrinho-lateral');
    if (carrinhoEl && carrinhoEl.style.display === 'none') {
        carrinhoEl.style.display = 'block';
    }
}

function atualizarCarrinho() {
    const itens = JSON.parse(localStorage.getItem('carrinho')) || [];
    const lista = document.getElementById('itens-carrinho');
    const totalSpan = document.getElementById('total-carrinho');
    
    if (!lista || !totalSpan) return;

    // Limpa a lista de itens antes de adicionar novos
    lista.innerHTML = '';
    let total = 0;

    // Exibe os itens no carrinho
    itens.forEach((item, index) => {
        const li = document.createElement('li');
        li.classList.add("flex", "justify-between", "items-center", "bg-purple-50", "px-3", "py-2", "rounded-md");

        li.innerHTML = `
            <span>${item.nome} x${item.quantidade} - R$ ${(item.preco * item.quantidade).toFixed(2)}</span>
            <button class="remover-item text-red-600 font-bold text-xl" data-index="${index}">×</button>
        `;

        lista.appendChild(li);
        total += item.preco * item.quantidade;
    });

    totalSpan.textContent = total.toFixed(2);

    // Adiciona a lógica para remover itens do carrinho
    document.querySelectorAll('.remover-item').forEach(botao => {
        botao.addEventListener('click', function () {
            const index = this.getAttribute('data-index');
            removerDoCarrinho(index);
        });
    });
}

function removerDoCarrinho(index) {
    const itens = JSON.parse(localStorage.getItem('carrinho')) || [];
    itens.splice(index, 1); // Remove o item pelo índice
    localStorage.setItem('carrinho', JSON.stringify(itens));
    atualizarCarrinho();
}

document.addEventListener('DOMContentLoaded', () => {
    atualizarCarrinho();

    // Lógica para botões de "Adicionar ao Carrinho"
    const buttons = document.querySelectorAll('.adicionar-ao-carrinho');
    if (buttons.length > 0) {
        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const nome = this.getAttribute('data-nome');
                const preco = this.getAttribute('data-preco');
                adicionarAoCarrinho(nome, preco);
            });
        });
    }

    // Botão de fechar o carrinho lateral
    const fecharCarrinhoBtn = document.getElementById('fechar-carrinho');
    const carrinhoEl = document.getElementById('carrinho-lateral');

    if (fecharCarrinhoBtn && carrinhoEl) {
        fecharCarrinhoBtn.addEventListener('click', () => {
            carrinhoEl.style.display = 'none';
        });
    }

    // Exibe carrinho automaticamente se houver itens no localStorage
    const itensSalvos = JSON.parse(localStorage.getItem('carrinho')) || [];
    if (carrinhoEl) {
        carrinhoEl.style.display = itensSalvos.length > 0 ? 'block' : 'none';
    }
});



// Finalizando o pedido ao enviar o formulário de compra
document.getElementById('form-finalizar-compra').addEventListener('submit', function(event) {
    event.preventDefault();  // Previne o envio do formulário
    
    // Recuperando os dados do formulário
    const pedido = {
      nome: document.getElementById('nome').value,
      email: document.getElementById('email').value,
      telefone: document.getElementById('telefone').value,
      cpf: document.getElementById('cpf').value,
      endereco: {
        cep: document.getElementById('cep').value,
        logradouro: document.getElementById('logradouro').value,
        numero: document.getElementById('numero').value,
        bairro: document.getElementById('bairro').value,
        cidade: document.getElementById('cidade').value,
        estado: document.getElementById('estado').value,
      },
      formaPagamento: document.getElementById('forma-pagamento').value,
      observacoes: document.getElementById('observacoes').value,
      itens: JSON.parse(localStorage.getItem('carrinho')) || [],
      total: document.getElementById('total').textContent.replace('R$ ', '')
    };
  
    // Salvando o pedido no localStorage
    localStorage.setItem('pedido', JSON.stringify(pedido));
  
    // Redirecionando para a página de agradecimento
    setTimeout(function() {
        window.location.href = 'agradecimento.html';  // Redireciona para a página de agradecimento
    }, 300000); // Aguarda 5 minutos antes de redirecionar
});
