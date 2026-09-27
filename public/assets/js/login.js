const passwordInput = document.getElementById('password');
const togglePassword = document.querySelector('.login-password-toggle');
if (passwordInput && togglePassword) {
    togglePassword.hidden = false;
    togglePassword.addEventListener('click', () => {
        const visible = passwordInput.type === 'password';
        passwordInput.type = visible ? 'text' : 'password';
        togglePassword.textContent = visible ? 'Sembunyikan' : 'Tampilkan';
        togglePassword.setAttribute('aria-pressed', String(visible));
    });
}
