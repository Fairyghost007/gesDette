document.addEventListener("DOMContentLoaded", async () => {
    const articlesTable = document.getElementById("articles-table");

    try {
        const response = await fetch("/api/client");
        const articles = await response.json();

        articles.forEach((article) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="border px-4 py-2">${article.id}</td>
                <td class="border px-4 py-2">${article.libelle}</td>
                <td class="border px-4 py-2">${article.prix.toFixed(2)}</td>
                <td class="border px-4 py-2">${article.qteStock}</td>
            `;
            articlesTable.appendChild(row);
        });
    } catch (error) {
        console.error("Error fetching articles:", error);
    }
});
