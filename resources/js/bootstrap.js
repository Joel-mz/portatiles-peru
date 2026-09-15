// CSRF token configuration for fetch/axios
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.csrfToken = token.content;
}
