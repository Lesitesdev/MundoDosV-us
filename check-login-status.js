// check-login-status.js
function checkLoginStatus() {
    const loginLink = document.getElementById('loginLink');
    const logoutLink = document.getElementById('logoutLink');
    const userInfo = document.getElementById('userInfo');
  
    firebase.auth().onAuthStateChanged(user => {
      if (user) {
        loginLink.classList.add('hidden');
        logoutLink.classList.remove('hidden');
        userInfo.classList.remove('hidden');
        userInfo.innerText = `Olá, ${user.displayName || 'usuário'}`;
      } else {
        loginLink.classList.remove('hidden');
        logoutLink.classList.add('hidden');
        userInfo.classList.add('hidden');
      }
    });
  }
  //**check-login-status.js (Verificação de login) */