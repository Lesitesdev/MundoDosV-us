document.addEventListener('DOMContentLoaded', async () => {
  // 1) Buscar dados do cliente no banco e preencher formulário
  try {
    const response = await fetch('buscar_dados_cliente.php');
    const dados = await response.json();

    if (dados.erro) {
      console.warn('Erro ao buscar dados do cliente:', dados.erro);
    } else {
      document.querySelector('input[name="nome"]').value = dados.nome || '';
      document.querySelector('input[name="email"]').value = dados.email || '';
      document.querySelector('input[name="telefone"]').value = dados.telefone || '';
      document.querySelector('input[name="logradouro"]').value = dados.rua || '';
      document.querySelector('input[name="numero"]').value = dados.numero || '';
      document.querySelector('input[name="bairro"]').value = dados.bairro || '';
      document.querySelector('input[name="cidade"]').value = dados.cidade || '';
      document.querySelector('input[name="estado"]').value = dados.estado || '';
      document.querySelector('input[name="cep"]').value = dados.cep || '';
    }
  } catch (error) {
    console.error('Erro ao carregar dados do cliente:', error);
  }

  // 2) Listener do botão Finalizar Compra
  const btnFinalizar = document.getElementById('btnFinalizarCompra');

  btnFinalizar.addEventListener('click', async () => {
    const form = document.querySelector('form');

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const formData = new FormData(form);
    const dadosCliente = {
      nome: formData.get('nome'),
      email: formData.get('email'),
      telefone: formData.get('telefone'),
      endereco: {
        logradouro: formData.get('logradouro'),
        numero: formData.get('numero'),
        bairro: formData.get('bairro'),
        cidade: formData.get('cidade'),
        estado: formData.get('estado'),
        cep: formData.get('cep'),
      },
      formaPagamento: formData.get('formaPagamento'),
      observacoes: formData.get('observacoes'),
    };

    const resumoCompra = {
      subtotal: localStorage.getItem('subtotalCarrinho') || '0.00',
      frete: localStorage.getItem('frete') || '0.00',
      desconto: localStorage.getItem('desconto') || '0.00',
      totalFinal: localStorage.getItem('totalFinal') || '0.00',
    };

    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

    if (carrinho.length === 0) {
      alert('Seu carrinho está vazio.');
      return;
    }

    const pedido = {
      cliente: dadosCliente,
      resumo: resumoCompra,
      itens: carrinho,
      dataPedido: new Date().toISOString(),
      status: 'pendente',
    };

    try {
      const response = await fetch('finalizar_compra.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(pedido),
      });

      const resultado = await response.json();

      console.log("Resposta recebida do PHP:", resultado);

      if (resultado.sucesso) {
        alert('Compra finalizada com sucesso!');
        localStorage.clear();
        window.location.href = 'agradecimento.php?pedido_id=' + resultado.pedido_id;
      } else {
        alert('Erro ao finalizar a compra: ' + (resultado.mensagem || 'Erro desconhecido.'));
      }
    } catch (error) {
      console.error("Erro na requisição:", error);
      alert('Erro na comunicação com o servidor: ' + error.message);
    }
  });
});
