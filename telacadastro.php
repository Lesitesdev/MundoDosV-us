<?php include 'includes/header.php'; ?>

<main class="flex items-center justify-center py-20 px-4">
  <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold text-center text-pink-700 mb-6">Criar Conta</h2>

    <form id="formCadastro" method="POST" action="salvar_cadastro.php">
      <div class="mb-4">
        <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
        <input type="text" class="form-control" id="nome" name="nome" required>
      </div>

      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>

<div class="mb-4">
  <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
  <input type="password" class="form-control" id="senha" name="senha" required minlength="6">
</div>

<div class="mb-4">
  <label for="confirmarSenha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
  <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha" required minlength="6">
  <small id="senhaErro" class="text-red-500 text-sm hidden">As senhas não coincidem.</small>
</div>




      <div class="mb-4">
        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
        <input type="text" class="form-control" id="cpf" name="cpf" required maxlength="14">
        <small id="cpfErro" class="text-red-500 text-sm hidden">CPF inválido.</small>
      </div>

      <div class="mb-4">
        <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
        <input type="text" class="form-control" id="cep" name="cep" required maxlength="9">
      </div>

      <!-- Novos campos adicionados aqui -->
     <div class="mb-4">
  <label for="rua" class="block text-sm font-medium text-gray-700">Rua</label>
  <input type="text" class="form-control" id="rua" name="rua" required>
</div>

<div class="mb-4">
  <label for="numero" class="block text-sm font-medium text-gray-700">Número</label>
  <input type="text" class="form-control" id="numero" name="numero" required>
</div>


      <div class="mb-4">
        <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
        <input type="text" class="form-control" id="cidade" name="cidade" readonly>
      </div>

      <div class="mb-6">
        <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
        <input type="text" class="form-control" id="estado" name="estado" readonly>
      </div>

      <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 rounded-lg transition">
        Cadastrar
      </button>
    </form>

    <p class="mt-4 text-sm text-center text-gray-600">
      Já tem conta?
      <a href="/login.php" class="text-pink-600 hover:underline font-medium">Entrar aqui</a>
    </p>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formCadastro');
  const cpfInput = document.getElementById('cpf');
  const cepInput = document.getElementById('cep');
  const cidadeInput = document.getElementById('cidade');
  const estadoInput = document.getElementById('estado');
  const cpfErro = document.getElementById('cpfErro');

  const senhaInput = document.getElementById('senha');
  const confirmarSenhaInput = document.getElementById('confirmarSenha');
  const senhaErro = document.getElementById('senhaErro');

  // Validação de CPF
  function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

    let soma = 0;
    for (let i = 0; i < 9; i++) soma += parseInt(cpf[i]) * (10 - i);
    let resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf[9])) return false;

    soma = 0;
    for (let i = 0; i < 10; i++) soma += parseInt(cpf[i]) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    return resto === parseInt(cpf[10]);
  }

  // Busca de CEP com ViaCEP
  cepInput.addEventListener('blur', () => {
    const cep = cepInput.value.replace(/\D/g, '');
    if (cep.length === 8) {
      fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(res => res.json())
        .then(data => {
          if (!data.erro) {
            cidadeInput.value = data.localidade;
            estadoInput.value = data.uf;
          } else {
            alert('CEP não encontrado.');
            cidadeInput.value = '';
            estadoInput.value = '';
          }
        })
        .catch(() => {
          alert('Erro ao buscar o CEP.');
        });
    }
  });

  // Envio do formulário
  form.addEventListener('submit', function (e) {
    let valid = true;

    // Valida CPF
    if (!validarCPF(cpfInput.value)) {
      cpfErro.classList.remove('hidden');
      valid = false;
    } else {
      cpfErro.classList.add('hidden');
    }

    // Valida se as senhas batem
    if (senhaInput.value !== confirmarSenhaInput.value) {
      senhaErro.classList.remove('hidden');
      valid = false;
    } else {
      senhaErro.classList.add('hidden');
    }

    if (!valid) e.preventDefault();
  });
});

 
</script>
