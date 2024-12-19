document.addEventListener('DOMContentLoaded', () => {
    const fetchDettes = async () => {
      try {
        const response = await fetch('/api/dettes');
        if (!response.ok) {
          throw new Error('Failed to fetch dettes');
        }
        const data = await response.json();
        displayDettes(data);
      } catch (error) {
        console.error('Error fetching dettes:', error);
      }
    };
  
    const displayDettes = (data) => {
      const tableBody = document.getElementById('dette-table-body');
      tableBody.innerHTML = ''; // Clear previous rows
  
      data.dettes.forEach((dette) => {
        const row = document.createElement('tr');
        row.classList.add('hover:bg-gray-100');
  
        row.innerHTML = `
          <td class="border border-gray-300 px-4 py-2">${dette.id}</td>
          <td class="border border-gray-300 px-4 py-2">${dette.montant}</td>
          <td class="border border-gray-300 px-4 py-2">${dette.montantVerser}</td>
          <td class="border border-gray-300 px-4 py-2">${dette.client.nom}</td>
          <td class="border border-gray-300 px-4 py-2">
            <a href="/dettes/edit/${dette.id}" class="text-blue-500 hover:underline">Edit</a> |
            <a href="/dettes/delete/${dette.id}" class="text-red-500 hover:underline">Delete</a>
          </td>
        `;
        tableBody.appendChild(row);
      });
    };
  
    fetchDettes();
  });
  