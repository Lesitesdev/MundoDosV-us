 <footer class="bg-purple-800 text-white py-10">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 px-4">
      <!-- Contato -->
      <div>
        <h4 class="font-bold mb-2">Contato</h4>
        <p>📱 WhatsApp: (11) 91234-5678</p>
        <p>✉️ Email: contato@mundodosveus.com</p>
      </div>
      <!-- Links Úteis -->
      <div>
        <h4 class="font-bold mb-2">Links Úteis</h4>
        <ul>
          <li><a href="/contatos.php" class="hover:underline">Política de Troca</a></li>
          <li><a href="/contatos.php" class="hover:underline">Frete e Entrega</a></li>
          <li><a href="/contatos.php" class="hover:underline">Termos e Condições</a></li>
        </ul>
      </div>
      <!-- Redes Sociais -->
      <div>
        <h4 class="font-bold mb-2">Redes Sociais</h4>
        <div class="flex flex-col gap-2">
          <a href="https://www.instagram.com" target="_blank" class="flex items-center gap-2 hover:underline text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path d="M16 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
              <path d="M12 2C8.686 2 6 4.686 6 8v8c0 3.314 2.686 6 6 6s6-2.686 6-6V8c0-3.314-2.686-6-6-6z" />
              <path d="M12 11a3 3 0 1 1 0 6 3 3 0 0 1 0-6z" />
            </svg>
            Instagram
          </a>
      
          <a href="https://www.facebook.com" target="_blank" class="flex items-center gap-2 hover:underline text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
            </svg>
            Facebook
          </a>
      
          <a href="https://www.pinterest.com" target="_blank" class="flex items-center gap-2 hover:underline text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path d="M12 2a10 10 0 0 0-3.325 19.478 8.55 8.55 0 0 1 .092-2.063l.914-3.872s-.227-.46-.227-1.14c0-1.067.619-1.863 1.39-1.863.656 0 .973.493.973 1.085 0 .662-.423 1.651-.641 2.573-.182.777.384 1.41 1.14 1.41 1.369 0 2.42-1.444 2.42-3.527 0-1.843-1.326-3.135-3.215-3.135-2.19 0-3.478 1.64-3.478 3.337 0 .663.254 1.377.573 1.763.064.078.073.146.055.223-.06.247-.196.777-.222.884-.035.148-.117.18-.27.109-1.008-.47-1.637-1.945-1.637-3.131 0-2.548 1.853-4.888 5.348-4.888 2.805 0 4.987 2.003 4.987 4.68 0 2.785-1.752 5.03-4.183 5.03-.816 0-1.584-.423-1.846-.926l-.501 1.909c-.182.693-.676 1.563-1.007 2.094A10.004 10.004 0 1 0 12 2z" />
            </svg>
            Pinterest
          </a>
        </div>
      </div>
      
      
      
      <!-- Pagamentos -->
      <div>
        <h4 class="font-bold mb-2">Formas de Pagamento</h4>
        <p>💳 Cartões | Pix | Boleto</p>
        <img src="https://www.mercadopago.com.br/org-img/MP3/home/logos/visa.gif" alt="Visa" class="inline w-10">
        <img src="https://www.mercadopago.com.br/org-img/MP3/home/logos/master.gif" alt="MasterCard" class="inline w-10">
        <img src="https://www.mercadopago.com.br/org-img/MP3/home/logos/pix.gif" alt="Pix" class="inline w-10">
      </div>
    </div>
    <p class="text-center mt-6 text-sm text-purple-200">© 2025 Mundo dos Véus. Todos os direitos reservados.</p>
  </footer>

  <!-- Carrinho Lateral -->
  <div id="carrinho-lateral" class="fixed top-24 right-4 w-72 bg-white shadow-lg rounded-xl p-4 border border-purple-300 z-50 hidden">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-xl font-bold text-purple-700">🛒 Seu Carrinho</h3>
      <button id="fechar-carrinho" class="text-purple-700 text-2xl font-bold hover:text-purple-900">&times;</button>
    </div>
  
    <ul id="itens-carrinho" class="space-y-2 max-h-64 overflow-y-auto"></ul>
    <p class="text-lg font-semibold mt-4">Total: R$ <span id="total-carrinho">0.00</span></p>
  
    <!-- Finalizar Compra -->
    <a href="carrinho.php" id="finalizar-compra" class="inline-block mt-4 bg-purple-700 text-white text-lg font-semibold px-4 py-2 rounded-lg hover:bg-purple-800 transition">
      Finalizar Compra
    </a>
  </div>
  

  </body>
  </html>
