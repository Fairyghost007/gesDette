document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = {
        login: document.getElementById('login').value,
        password: document.getElementById('password').value
    };

    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (response.ok) {
            // Redirect to home page on successful login
            window.location.href = '/clients';
        } else {
            // Show error message
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = data.message || 'Invalid credentials';
            errorDiv.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
    }
});