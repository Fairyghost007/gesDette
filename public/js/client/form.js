document.addEventListener('DOMContentLoaded', () => {
  const toggleSwitch = document.getElementById('creerCompte');
  const userFields = document.getElementById('user-fields');

  toggleSwitch.addEventListener('change', () => {
    if (toggleSwitch.checked) {
      userFields.classList.remove('hidden');
    } else {
      userFields.classList.add('hidden');
    }
  });

  document.getElementById('client-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    try {
      const response = await fetch('/client/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      });

      if (response.ok) {
        alert('Client added successfully');
        window.location.href = '/clients';
      } else {
        const errorData = await response.json();
        alert(`Error: ${errorData.message}`);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An error occurred while adding the client');
    }
  });
});
