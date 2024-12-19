document.addEventListener('DOMContentLoaded', () => {
    let articles = [];

    const fetchArticles = async () => {
        try {
            const response = await fetch('/api/articles');
            if (!response.ok) {
                throw new Error('Failed to fetch articles');
            }
            const data = await response.json();
            articles = data.articles;
            populateArticleSelect(data);
        } catch (error) {
            console.error('Error fetching articles:', error);
        }
    };

    const populateArticleSelect = (data) => {
        const articleSelect = document.getElementById('article');
        data.articles.forEach((article) => {
            const option = document.createElement('option');
            option.value = article.id;
            option.textContent = article.libelle;
            articleSelect.appendChild(option);
        });
    };

    const addToCart = () => {
        const articleId = document.getElementById('article').value;
        const quantite = parseInt(document.getElementById('quantite').value);
        const articleText = document.getElementById('article').options[document.getElementById('article').selectedIndex].text;

        const cartItems = JSON.parse(sessionStorage.getItem('cart')) || [];

        // Check if article already exists in cart
        const existingItemIndex = cartItems.findIndex(item => item.articleId === articleId);

        if (existingItemIndex !== -1) {
            // Update quantity if article exists
            cartItems[existingItemIndex].quantite = parseInt(cartItems[existingItemIndex].quantite) + quantite;
        } else {
            // Add new item if article doesn't exist
            cartItems.push({ articleId, quantite, articleText });
        }

        sessionStorage.setItem('cart', JSON.stringify(cartItems));

        displayCartItems();
        updateMontant();

        // Clear the quantity input after adding to cart
        document.getElementById('quantite').value = '';
    };

    const displayCartItems = () => {
        const cartItems = JSON.parse(sessionStorage.getItem('cart')) || [];
        const cartTableContainer = document.getElementById('cart-items');
        cartTableContainer.innerHTML = '';

        // Create the table element
        const table = document.createElement('table');
        table.classList.add('min-w-full', 'bg-white', 'shadow-md', 'rounded-lg', 'overflow-hidden');

        // Create the table header
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        headerRow.innerHTML = `
            <th class="py-2 px-4 border-b">Article</th>
            <th class="py-2 px-4 border-b">Quantité</th>
            <th class="py-2 px-4 border-b">Prix</th>
            <th class="py-2 px-4 border-b">Total</th>
        `;
        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Create the table body
        const tbody = document.createElement('tbody');
        cartItems.forEach((item) => {
            const article = articles.find(article => article.id == item.articleId);
            if (article) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-2 px-4 border-b">${item.articleText}</td>
                    <td class="py-2 px-4 border-b">${item.quantite}</td>
                    <td class="py-2 px-4 border-b">${article.prix.toFixed(2)}</td>
                    <td class="py-2 px-4 border-b">${(article.prix * item.quantite).toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            }
        });
        table.appendChild(tbody);

        // Append the table to the cartTableContainer element
        cartTableContainer.appendChild(table);
    };

    const updateMontant = () => {
        const cartItems = JSON.parse(sessionStorage.getItem('cart')) || [];
        let totalMontant = 0;

        cartItems.forEach((item) => {
            const article = articles.find(article => article.id == item.articleId);
            if (article) {
                totalMontant += article.prix * item.quantite;
            }
        });

        // Store the total montant in session storage
        sessionStorage.setItem('montant', totalMontant.toFixed(2));
    };

    const saveDette = async (event) => {
        event.preventDefault();

        const cartItems = JSON.parse(sessionStorage.getItem('cart')) || [];
        const montant = sessionStorage.getItem('montant');

        const data = {
            montant: parseFloat(montant),
            montantVerser: 0,
            detailDettes: cartItems.map((item) => ({
                articleId: item.articleId,
                quantite: item.quantite,
            })),
        };

        try {
            const response = await fetch('/dette/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const responseData = await response.json();
            console.log('Full response:', responseData);

            if (response.ok) {
                // Clear the cart and montant from sessionStorage
                sessionStorage.removeItem('cart');
                sessionStorage.removeItem('montant');

                alert('Dette added successfully');
                window.location.href = '/dettes';
            } else {
                alert(`Error: ${responseData.message}\n${responseData.error || ''}`);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while adding the dette');
        }
    };

    document.getElementById('add-to-cart').addEventListener('click', addToCart);
    document.getElementById('dette-form').addEventListener('submit', saveDette);

    fetchArticles();
    displayCartItems();
    updateMontant();
});
