<?php include 'includes/header.php'; ?>

<!-- Conteúdo da página aqui -->




  <!-- Seção Sobre -->
  <section id="sobre" class="bg-gray-100 py-12 px-6">
    <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Sobre o Mundo dos Véus</h2>
    <p class="text-lg text-gray-700 mb-8">
      O <strong>Mundo dos Véus</strong> é uma loja especializada em véus e acessórios para noivas e mulheres evangélicas que desejam um toque de delicadeza e sofisticação para o seu grande dia. Fundada com o objetivo de oferecer produtos de qualidade e um atendimento personalizado, nossa missão é garantir que cada cliente se sinta especial e única em sua jornada.
    </p>
    <p class="text-lg text-gray-700 mb-8">
      Atuamos com uma curadoria rigorosa para escolher os melhores tecidos, bordados e detalhes que fazem toda a diferença. Cada véu é projetado para refletir a beleza e a elegância de cada mulher, desde modelos tradicionais até as opções mais modernas, sem abrir mão da tradição e do respeito à fé.
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
      <div class="bg-gray-200 p-6 rounded-lg">
        <img src="/imagens/sobre 1.jpg" alt="Imagem de Véu 1" class="w-full h-96 object-cover rounded-lg mb-4">
        <p class="text-center text-gray-600">Detalhes exclusivos em nossos véus</p>
      </div>
      <div class="bg-gray-200 p-6 rounded-lg">
        <img src="/imagens/sobre 2.jpg" alt="Imagem de Véu 2" class="w-full h-96 object-cover rounded-lg mb-4">
        <p class="text-center text-gray-600">A delicadeza e sofisticação que você merece</p>
      </div>
    </div>
    <p class="text-lg text-gray-700 mb-8">
      No <strong>Mundo dos Véus</strong>, estamos comprometidos em fornecer um excelente atendimento ao cliente, desde o momento da escolha do véu até a entrega final. Nossa equipe está pronta para oferecer todas as informações necessárias para garantir que sua compra seja feita com confiança e tranquilidade. Acreditamos que o casamento é um momento único e, por isso, queremos que cada detalhe seja perfeito.
    </p>
    <div class="text-center">
      <a href="#contato" class="text-purple-700 font-semibold hover:text-purple-900">Entre em contato para mais informações sobre nossos produtos e serviços!</a>
    </div>
  </section>

  <!-- Seção Contato -->
  <section id="contato" class="bg-gray-200 py-12 px-6">
    <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Contatos</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div>
        <h3 class="text-xl font-semibold text-purple-700 mb-4">Informações de Contato</h3>
        <p class="text-lg text-gray-700 mb-4">📱 WhatsApp: (11) 91234-5678</p>
        <p class="text-lg text-gray-700 mb-4">✉️ Email: contato@mundodosveus.com</p>
        <div>
          <h4 class="text-lg font-semibold text-purple-700">Redes Sociais</h4>
          <ul>
            <li><a href="#" class="text-purple-700 hover:underline">Instagram</a></li>
            <li><a href="#" class="text-purple-700 hover:underline">Facebook</a></li>
            <li><a href="#" class="text-purple-700 hover:underline">Pinterest</a></li>
          </ul>
        </div>
      </div>
      <div>
        <h3 class="text-xl font-semibold text-purple-700 mb-4">Nos Encontre</h3>
        <div class="w-full h-64 bg-gray-300 rounded-lg mb-4">
          <!-- Google Maps Embed -->
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3656.251571731005!2d-46.64232248502445!3d-23.5557492676259!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce59df312ddc2f%3A0x53d6270a1da0c1b7!2sAvenida%20Paulista%2C%20199!5e0!3m2!1spt-BR!2sbr!4v1651021695637!5m2!1spt-BR!2sbr" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
        <p class="text-lg text-gray-700">Endereço: Avenida Paulista, 199, São Paulo, SP</p>
      </div>
    </div>
  </section>

  <!-- Rodapé padrão -->
   
  <?php include 'includes/footer.php'; ?>

  <script>
    // Carrinho de Compras
    const carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];
    const itensCarrinho = document.getElementById("itens-carrinho");
    const totalCarrinho = document.getElementById("total-carrinho");
    const carrinhoLateral = document.getElementById("carrinho-lateral");
    const fecharCarrinhoBtn = document.getElementById("fechar-carrinho");
  
    function adicionarAoCarrinho(produto) {
      const existente = carrinho.find(item => item.nome === produto.nome);
      if (existente) {
        existente.quantidade++;
      } else {
        carrinho.push({ ...produto, quantidade: 1 });
      }
      atualizarCarrinho();
      salvarCarrinho();
      exibirCarrinho();
    }
  
    function atualizarCarrinho() {
      itensCarrinho.innerHTML = "";
      let total = 0;
  
      carrinho.forEach((item, index) => {
        const subtotal = item.preco * item.quantidade;
        total += subtotal;
  
        const li = document.createElement("li");
        li.className = "flex justify-between items-center border-b pb-1";
        li.innerHTML = `
          <div>
            <p class="font-medium">${item.nome}</p>
            <small class="text-gray-500">Qtd: ${item.quantidade} x R$ ${item.preco.toFixed(2)}</small>
          </div>
          <strong class="text-purple-700">R$ ${subtotal.toFixed(2)}</strong>
          <div class="flex items-center space-x-2 ml-4">
            <button onclick="ajustarQuantidade(${index}, -1)" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">-</button>
            <span class="font-medium">${item.quantidade}</span>
            <button onclick="ajustarQuantidade(${index}, 1)" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">+</button>
          </div>
        `;
        itensCarrinho.appendChild(li);
      });
  
      totalCarrinho.textContent = total.toFixed(2);
    }
  
    function ajustarQuantidade(index, delta) {
      const item = carrinho[index];
      if (!item) return;
      item.quantidade += delta;
      if (item.quantidade < 1) {
        carrinho.splice(index, 1);
      }
      atualizarCarrinho();
      salvarCarrinho();
    }
  
    function salvarCarrinho() {
      localStorage.setItem("carrinho", JSON.stringify(carrinho));
    }
  
    function exibirCarrinho() {
      if (carrinho.length > 0) {
        carrinhoLateral.classList.remove("hidden");
      }
    }
  
    function fecharCarrinho() {
      carrinhoLateral.classList.add("hidden");
    }
  
    fecharCarrinhoBtn.addEventListener("click", fecharCarrinho);
  
    document.addEventListener('DOMContentLoaded', () => {
      atualizarCarrinho();
      if (carrinho.length > 0) {
        exibirCarrinho();
      }
      
      document.querySelectorAll('.adicionar-ao-carrinho').forEach(button => {
        button.addEventListener('click', () => {
          const nome = button.dataset.nome;
          const preco = parseFloat(button.dataset.preco);
          adicionarAoCarrinho({ nome, preco });
        });
      });
    });
  </script>
  
  
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

  </script>
  
  <!-- Importante: Carrinho.js apenas se ainda precisar -->
  <!-- <script src="/carrinho.js"></script> -->
  
  

</body>
</html>
