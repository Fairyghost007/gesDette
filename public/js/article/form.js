document
  .getElementById("article-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const libelle = document.getElementById("libelle").value;
    const prix = parseFloat(document.getElementById("prix").value);
    const qteStock = parseInt(document.getElementById("qteStock").value);

    try {
      const response = await fetch("/article/store", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ libelle, prix, qteStock }),
      });
      console.log(response);

      if (!response.ok) {
        const contentType = response.headers.get("Content-Type");
        if (contentType && contentType.includes("application/json")) {
          const error = await response.json();
          alert(`Failed to add article: ${error.message}`);
        } else {
          // Handle non-JSON error responses
          const errorText = await response.text();
          console.error("Server error:", errorText);
          alert("An unexpected error occurred. Check the console for details.");
        }
      } else {
        const result = await response.json();
        alert("Article added successfully!");
        window.location.href = "/articles";
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An error occurred. Check the console for details.");
    }
  });
