// assets/js/auth.js

document.addEventListener('DOMContentLoaded', function() {
  const registerForm = document.getElementById('registerForm');
  const loginForm = document.getElementById('loginForm');
  const logoutButton = document.getElementById('logoutButton');

  if (registerForm) {
    registerForm.addEventListener('submit', function(event) {
      event.preventDefault();

      const username = document.getElementById('registerUsername').value;
      const password = document.getElementById('registerPassword').value;
      const email = document.getElementById('registerEmail').value;

      fetch('api/register.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `username=${username}&password=${password}&email=${email}`,
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Registration successful! Redirecting to login.');
          window.location.href = 'login.html';
        } else {
          alert('Registration failed: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during registration.');
      });
    });
  }

  if (loginForm) {
    loginForm.addEventListener('submit', function(event) {
      event.preventDefault();

      const username = document.getElementById('loginUsername').value;
      const password = document.getElementById('loginPassword').value;

      fetch('api/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `username=${username}&password=${password}`,
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          localStorage.setItem('userId', data.userId);
          localStorage.setItem('username', data.username);
          alert('Login successful! Redirecting to home.');
          window.location.href = 'home.html';
        } else {
          alert('Login failed: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during login.');
      });
    });
  }

  if (logoutButton) {
    logoutButton.addEventListener('click', function() {
      fetch('api/logout.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          localStorage.removeItem('userId');
          localStorage.removeItem('username');
          alert('Logout successful! Redirecting to index.');
          window.location.href = 'index.html';
        } else {
          alert('Logout failed: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during logout.');
      });
    });
  }
});