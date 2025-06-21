import { getAuth, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-auth.js";

// Inicializar Firebase
const auth = getAuth();

// Função para carregar o cabeçalho dinamicamente
function loadHeader() {
    const headerContainer = document.getElementById('header-container'); // O ID do container onde o header será inserido
  
    // Insere o HTML diretamente (em vez de usar fetch)
    headerContainer.innerHTML = `
      <header class="bg-white shadow-md px-6 py-4">
        <div class="max-w-6xl mx-auto flex items-center">
          <!-- Logo -->
          <a href="index.php" class="text-2xl font-bold text-purple-700">Mundo dos Véus</a>
  
          <!-- Navegação -->
          <nav class="ml-8 hidden md:flex space-x-6">
            <a href="index.php"    class="text-gray-700 hover:text-purple-700">Início</a>
            <a href="produtos.php" class="text-gray-700 hover:text-purple-700">Produtos</a>
            <a href="sobre.php"    class="text-gray-700 hover:text-purple-700">Sobre</a>
            <a href="contato.php"  class="text-gray-700 hover:text-purple-700">Contato</a>
            <!-- Este link só aparece se estiver logado -->
            <a href="cliente.php" id="account-link" class="text-gray-700 hover:text-purple-700 hidden">Meu Cadastro</a>
          </nav>
  
          <div class="flex-1"></div>
  
          <!-- Carrinho -->
          <a href="carrinho.php" class="text-2xl text-gray-700 hover:text-purple-700 mr-6">🛒</a>
  
          <!-- Saudação + Logout (só logado) -->
          <div id="user-info" class="flex items-center space-x-2 hidden">
            <span id="saudacao" class="text-gray-700"></span>
            <button id="logout-link" class="text-gray-700 hover:text-purple-700">🚪 Logout</button>
          </div>
  
          <!-- Login (só não-logado) -->
          <a href="login.php" id="login-link" class="text-gray-700 hover:text-purple-700">👤 Login</a>
        </div>
      </header>
    `;
  
    updateHeader(); // Chama a função para atualizar o cabeçalho com base no estado de login
  }
  
  // Atualiza o cabeçalho (mostra/oculta links conforme login)
  function updateHeader() {
    const user = firebase.auth().currentUser;
    const accountLink = document.getElementById('account-link');
    const loginLink = document.getElementById('login-link');
    const userInfo = document.getElementById('user-info');
    const saudacao = document.getElementById('saudacao');
    const logoutLink = document.getElementById('logout-link');
  
    if (user) {
      accountLink.style.display = 'inline-block';
      loginLink.style.display = 'none';
      userInfo.style.display = 'flex';
      saudacao.textContent = `Olá, ${user.displayName || user.email}`;
      logoutLink.addEventListener('click', async () => {
        await firebase.auth().signOut();
        window.location.href = 'index.php';
      });
    } else {
      accountLink.style.display = 'none';
      loginLink.style.display = 'inline-block';
      userInfo.style.display = 'none';
    }
  }
  
  // Carrega o cabeçalho quando a página for carregada
  window.addEventListener('load', loadHeader);
  