import { firebaseConfig } from './firebaseConfig.js';

import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-app.js";
import {
  getAuth,
  onAuthStateChanged,
  updatePassword,
  updateProfile,
  signOut
} from "https://www.gstatic.com/firebasejs/9.22.2/firebase-auth.js";
import {
  getFirestore,
  doc,
  getDoc,
  updateDoc,
  collection,
  getDocs,
  addDoc,
  deleteDoc
} from "https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore.js";

// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);

// Inputs de dados do cliente
const nomeInput = document.getElementById("clienteNome");
const emailInput = document.getElementById("clienteEmail");
const telInput = document.getElementById("clienteTelefone");
const senhaInput = document.getElementById("clienteSenha");
const cpfInput = document.getElementById("clienteCpf");
const rgInput = document.getElementById("clienteRg");
const salvarBtn = document.getElementById("btnSalvarDados");
const statusMsg = document.getElementById("statusMsg");

// Inputs de endereços
const listaEnderecos = document.getElementById("listaEnderecos");
const btnAddEnd = document.getElementById("btnAdicionarEndereco");
const formEnd = document.getElementById("formEndereco");
const logradouroIn = document.getElementById("logradouro");
const numeroIn = document.getElementById("numero");
const bairroIn = document.getElementById("bairro");
const cidadeIn = document.getElementById("cidade");
const estadoIn = document.getElementById("estado");
const cepIn = document.getElementById("cep");

// Pedidos e logout
const listaPedidos = document.getElementById("listaPedidos");
const logoutBtn = document.getElementById("logoutBtn");

// Mensagem de status
function showStatus(msg, ok = true) {
  statusMsg.textContent = msg;
  statusMsg.className = ok ? "mt-2 text-green-600" : "mt-2 text-red-600";
  statusMsg.classList.remove("hidden");
  setTimeout(() => statusMsg.classList.add("hidden"), 3000);
}

// Ao logar, carregar dados
onAuthStateChanged(auth, async user => {
  if (!user) return void (window.location.href = "login.html");

  emailInput.value = user.email;

  const uRef = doc(db, "usuarios", user.uid);
  const uSnap = await getDoc(uRef);
  if (uSnap.exists()) {
    const data = uSnap.data();
    nomeInput.value = data.nome || "";
    telInput.value = data.telefone || "";
    cpfInput.value = data.cpf || "";
    rgInput.value = data.rg || "";
  }

  await carregarEnderecos(user.uid);
  await carregarPedidos(user.uid);
});

// Salvar dados do cliente
salvarBtn.addEventListener("click", async () => {
  const user = auth.currentUser;
  if (!user) return;

  const novoNome = nomeInput.value.trim();
  const novoTel = telInput.value.trim();
  const novaSenha = senhaInput.value.trim();
  const novoCpf = cpfInput.value.trim();
  const novoRg = rgInput.value.trim();

  // Validação dos campos
  if (!novoNome) {
    showStatus("O nome é obrigatório!", false);
    return;
  }

  if (!novoTel) {
    showStatus("O telefone é obrigatório!", false);
    return;
  }

  await updateDoc(doc(db, "usuarios", user.uid), {
    nome: novoNome,
    telefone: novoTel,
    cpf: novoCpf,
    rg: novoRg
  });

  if (novoNome) {
    await updateProfile(user, { displayName: novoNome });
  }

  if (novaSenha && novaSenha.length >= 6) {
    await updatePassword(user, novaSenha);
  }

  showStatus("Dados salvos com sucesso!");
  senhaInput.value = "";
});

// Carregar endereços
async function carregarEnderecos(uid) {
  listaEnderecos.innerHTML = "<p>Carregando endereços...</p>";
  const col = collection(db, "usuarios", uid, "enderecos");
  const snap = await getDocs(col);
  if (snap.empty) {
    return void (listaEnderecos.innerHTML = "<p>Sem endereços salvos.</p>");
  }
  listaEnderecos.innerHTML = "";
  snap.forEach(d => {
    const e = d.data();
    const div = document.createElement("div");
    div.className = "p-2 border rounded bg-gray-50 flex justify-between items-center mb-2";
    const info = document.createElement("p");
    info.textContent = `${e.logradouro}, ${e.numero} – ${e.bairro}, ${e.cidade}/${e.estado} (CEP ${e.cep})`;
    const btnExcluir = document.createElement("button");
    btnExcluir.textContent = "Excluir";
    btnExcluir.className = "text-red-600 hover:underline text-sm";
    btnExcluir.addEventListener("click", async () => {
      await deleteDoc(d.ref); // Excluir do Firestore
      div.remove(); // Remover do DOM
      showStatus("Endereço excluído com sucesso!");
    });
    div.appendChild(info);
    div.appendChild(btnExcluir);
    listaEnderecos.appendChild(div);
  });
}

// Mostrar/esconder formulário de endereço
btnAddEnd.addEventListener("click", () => {
  formEnd.classList.toggle("hidden");
});

// Salvar novo endereço
formEnd.addEventListener("submit", async ev => {
  ev.preventDefault();
  const user = auth.currentUser;
  if (!user) return;

  // Validação de campos de endereço
  if (!logradouroIn.value || !numeroIn.value || !bairroIn.value || !cidadeIn.value || !estadoIn.value || !cepIn.value) {
    showStatus("Por favor, preencha todos os campos do endereço!", false);
    return;
  }

  await addDoc(collection(db, "usuarios", user.uid, "enderecos"), {
    logradouro: logradouroIn.value,
    numero: numeroIn.value,
    bairro: bairroIn.value,
    cidade: cidadeIn.value,
    estado: estadoIn.value,
    cep: cepIn.value
  });

  // Adicionar o novo endereço no DOM diretamente
  const div = document.createElement("div");
  div.className = "p-2 border rounded bg-gray-50 flex justify-between items-center mb-2";
  const info = document.createElement("p");
  info.textContent = `${logradouroIn.value}, ${numeroIn.value} – ${bairroIn.value}, ${cidadeIn.value}/${estadoIn.value} (CEP ${cepIn.value})`;
  div.appendChild(info);
  listaEnderecos.appendChild(div);

  formEnd.reset();
  formEnd.classList.add("hidden");
  showStatus("Endereço salvo com sucesso!");
});

// Carregar pedidos
async function carregarPedidos(uid) {
  listaPedidos.innerHTML = "<p>Carregando pedidos...</p>";
  const col = collection(db, "usuarios", uid, "pedidos");
  const snap = await getDocs(col);
  if (snap.empty) {
    return void (listaPedidos.innerHTML = "<p>Sem pedidos realizados.</p>");
  }
  listaPedidos.innerHTML = "";
  snap.forEach(d => {
    const p = d.data();
    const div = document.createElement("div");
    div.className = "p-3 border rounded mb-3 bg-white shadow-sm flex items-center gap-3";

    const img = document.createElement("img");
    img.src = p.imagem;
    img.alt = p.produto;
    img.className = "w-16 h-16 object-cover rounded";

    const detalhes = document.createElement("div");
    detalhes.innerHTML = `
      <p class="font-semibold">${p.produto}</p>
      <p class="text-sm text-gray-600">Status: <span class="font-medium">${p.status}</span></p>
      <p class="text-xs text-gray-500">Data: ${p.data || "—"}</p>
    `;

    div.appendChild(img);
    div.appendChild(detalhes);
    listaPedidos.appendChild(div);
  });
}

// Logout
logoutBtn.addEventListener("click", () => {
  signOut(auth).then(() => window.location.href = "login.html");
});
