const loginForm = document.getElementById('local-login-form');
let submitCount = 0;
const maxAttempts = 10;

loginForm.action = '/api/auth/local/login';

if (loginForm) {
    loginForm.addEventListener('submit', (event) => {
        event.preventDefault();
        if (submitCount < maxAttempts) {
            handleFormFetch(loginForm.action, event, 'text');
            submitCount++;
        } else {
            // Display an error message or take some other action when the limit is reached.
            console.log('Maximum submission attempts reached.');
        }
    });
}
