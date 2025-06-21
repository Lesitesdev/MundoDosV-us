<?php include 'includes/header.php'; ?>

<!-- Conteúdo da página aqui -->






  <!-- Conteúdo da Página -->
  <main class="max-w-4xl mx-auto mt-6 p-6 bg-white shadow-md rounded">
    <a href="javascript:history.back()" class="flex items-center text-purple-700 hover:underline mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Voltar
    </a>

    <h1 class="text-2xl font-bold text-purple-800 mb-6">Contato e Informações</h1>

    <div class="grid md:grid-cols-2 gap-8 text-gray-700">
      <!-- Contato -->
      <div>
        <h2 class="text-xl font-semibold text-purple-700 mb-2">📞 Contato</h2>
        <p><strong>WhatsApp:</strong> (11) 91234-5678</p>
        <p><strong>Email:</strong> contato@mundodosveus.com</p>
      </div>

      <!-- Links Úteis -->
      <div>
        <h2 class="text-xl font-semibold text-purple-700 mb-2">🔗 Links Úteis</h2>
        <ul class="list-disc list-inside">
          <li><a href="#politica" class="text-purple-700 hover:underline">Política de Troca</a></li>
          <li><a href="#frete" class="text-purple-700 hover:underline">Frete e Entrega</a></li>
          <li><a href="#termos" class="text-purple-700 hover:underline">Termos e Condições</a></li>
        </ul>
      </div>

      <!-- Redes Sociais -->
      <div>
        <h2 class="text-xl font-semibold text-purple-700 mb-2">📱 Redes Sociais</h2>
        <ul class="list-disc list-inside">
          <li><a href="#" class="text-purple-700 hover:underline">Instagram</a></li>
          <li><a href="#" class="text-purple-700 hover:underline">Facebook</a></li>
          <li><a href="#" class="text-purple-700 hover:underline">Pinterest</a></li>
        </ul>
      </div>
    </div>

    <!-- Conteúdo das Informações Adicionais -->
    <section class="mt-12 space-y-10 text-gray-800">
      <div id="politica">
        <h2 class="text-xl font-bold text-purple-800 mb-2">📦 Política de Troca</h2>
        <p>
          Se você não estiver satisfeita com sua compra, aceitamos trocas em até 7 dias após o recebimento do pedido.
          Os produtos devem estar em perfeito estado, sem sinais de uso, com etiquetas e embalagem original. 
          O custo do frete para a primeira troca é por nossa conta!
        </p>
      </div>

      <div id="frete">
        <h2 class="text-xl font-bold text-purple-800 mb-2">🚚 Frete e Entrega</h2>
        <p>
          Enviamos para todo o Brasil com opções via Correios (PAC e SEDEX). O prazo de entrega varia de acordo com o CEP informado e a forma de envio escolhida.
          O frete é calculado automaticamente no carrinho. Compras acima de R$199 têm frete grátis para todo o Brasil.
        </p>
      </div>

      <div id="termos">
        <h2 class="text-xl font-bold text-purple-800 mb-2">📄 Termos e Condições</h2>
        <p>
          Ao realizar uma compra em nosso site, você concorda com nossos termos de uso. 
          Todos os dados pessoais são protegidos conforme a LGPD. Os pedidos são confirmados após a aprovação do pagamento. 
          Reservamo-nos o direito de corrigir erros de preço e estoque sem aviso prévio.
        </p>
      </div>
    </section>
  </main>

  <?php include 'includes/footer.php'; ?>
  <script type="module" src="/header.js"></script>


  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-app.js";
    import { getAuth, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-auth.js";
    import { getFirestore, doc, getDoc } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore.js";
  
    // Configuração do Firebase
    const firebaseConfig = {
      apiKey: "AIzaSyD9FSLA-EOGRgvDbbaBqBge7j8gRl4WJkI",
      authDomain: "loja-de-veus.firebaseapp.com",
      projectId: "loja-de-veus",
      storageBucket: "loja-de-veus.appspot.com",
      messagingSenderId: "907809130966",
      appId: "1:907809130966:web:800dae192863cddbc2fc77",
      measurementId: "G-XDRVKSRMQP"
    };
  
    // Inicialização do Firebase
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const db = getFirestore(app);
  
    // Referências ao DOM
    const userInfoEl = document.getElementById("user-info");
    const loginLinkEl = document.getElementById("login-link");
    const saudacaoEl = document.getElementById("saudacao");
    const logoutBtnEl = document.getElementById("logout-link");
    const accountLinkEl = document.getElementById("account-link");
  
    // Verifica estado de autenticação
    onAuthStateChanged(auth, async user => {
      if (user) {
        // Busca o nome do Firestore ou fallback
        let nome = user.displayName || "";
        try {
          const snap = await getDoc(doc(db, "usuarios", user.uid));
          if (snap.exists() && snap.data().nome) nome = snap.data().nome;
        } catch (e) {
          console.warn("Erro ao buscar nome:", e);
        }
  
        // Exibe as informações de login
        saudacaoEl.textContent = `Olá, ${nome || user.email.split("@")[0]}!`;
        userInfoEl.classList.remove("hidden");
        loginLinkEl.classList.add("hidden");
        accountLinkEl.classList.remove("hidden");
      } else {
        userInfoEl.classList.add("hidden");
        loginLinkEl.classList.remove("hidden");
        accountLinkEl.classList.add("hidden");
      }
    });
  
    // Função de logout
    logoutBtnEl?.addEventListener("click", () => {
      signOut(auth)
        .then(() => window.location.href = "login.php")
        .catch(err => console.error(err));
    });
  </script>

</body>
</html>
