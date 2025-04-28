// auth.js
const logoutLink = document.getElementById('logoutLink');

logoutLink.addEventListener('click', () => {
  firebase.auth().signOut().then(() => {
    console.log('Usuário deslogado');
    window.location.reload();  // Redireciona ou recarrega a página após logout
  }).catch((error) => {
    console.error('Erro ao deslogar: ', error);
  });
});

/**auth.js (Funções de autenticação)**/