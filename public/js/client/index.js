const fetchClients = async (page = 1, limit = 2, telephone = null) => {
    const url = new URL('/api/clients', window.location.origin);
    url.searchParams.append('page', page);
    url.searchParams.append('limit', limit);
    if (telephone) {
      url.searchParams.append('telephone', telephone);
    }
  
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error('Failed to fetch clients');
      }
      const data = await response.json();
      displayClients(data);
      updatePaginationButtons(data);
    } catch (error) {
      console.error('Error fetching clients:', error);
    }
  };
  
  const displayClients = (data) => {
    const tableBody = document.getElementById('client-table-body');
    tableBody.innerHTML = ''; // Clear previous rows
  
    // Populate table rows
    data.clients.forEach((client, index) => {
      const row = document.createElement('tr');
      row.classList.add('hover:bg-gray-100');
  
      row.innerHTML = `
        <td class="border border-gray-300 px-4 py-2">${client.id}</td>
        <td class="border border-gray-300 px-4 py-2">${client.nom}</td>
        <td class="border border-gray-300 px-4 py-2">${client.telephone}</td>
        <td class="border border-gray-300 px-4 py-2">${client.addresse}</td>
        <td class="border border-gray-300 px-4 py-2">${client.email}</td>
        <td class="border border-gray-300 px-4 py-2">
          <a href="/clients/edit/${client.id}" class="text-blue-500 hover:underline">Edit</a> |
          <a href="/clients/delete/${client.id}" class="text-red-500 hover:underline">Delete</a> |
          <a href="/clients/dette/${client.id}" class="text-gray-900 hover:underline">Dette</a>
        </td>
      `;
      tableBody.appendChild(row);
    });
  
    // Update pagination info
    const paginationInfo = document.getElementById('pagination-info');
    paginationInfo.innerHTML = `Page ${data.current_page} of ${data.total_pages}`;
    
    // Update dataset attributes for current and total pages
    paginationInfo.dataset.page = data.current_page;
    paginationInfo.dataset.totalPages = data.total_pages;
  };
  
  const updatePaginationButtons = (data) => {
    const currentPage = data.current_page;
    const totalPages = data.total_pages;
  
    const prevButton = document.getElementById('prev-page');
    const nextButton = document.getElementById('next-page');
  
    // Add Tailwind classes to indicate inactive state
    if (currentPage === 1) {
      prevButton.setAttribute('disabled', 'true');
      prevButton.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      prevButton.removeAttribute('disabled');
      prevButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  
    if (currentPage === totalPages) {
      nextButton.setAttribute('disabled', 'true');
      nextButton.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      nextButton.removeAttribute('disabled');
      nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  };
  
  // Initial load
  fetchClients();
  
  // Add event listeners for pagination and filtering
  document.getElementById('filter-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const telephone = document.getElementById('telephone-input').value;
    fetchClients(1, 4, telephone);
  });
  
  document.getElementById('next-page').addEventListener('click', () => {
    const currentPage = parseInt(document.getElementById('pagination-info').dataset.page || 1);
    const nextPage = currentPage + 1;
    const totalPages = parseInt(document.getElementById('pagination-info').dataset.totalPages || 1);
    if (nextPage <= totalPages) {
      fetchClients(nextPage);
    }
  });
  
  document.getElementById('prev-page').addEventListener('click', () => {
    const currentPage = parseInt(document.getElementById('pagination-info').dataset.page || 1);
    const prevPage = currentPage > 1 ? currentPage - 1 : 1;
    fetchClients(prevPage);
  });
  