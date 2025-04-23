    function showUploadConfirmationModal(event) {
        event.preventDefault();
        var modal = new bootstrap.Modal(document.getElementById('uploadConfirmationModal'));
        modal.show();
        document.querySelector('#uploadConfirmationModal .btn-secondary').onclick = function() {
            document.getElementById('uploadForm').submit();
        };
    }

    function showDeleteConfirmationModal(fileId) {
        var modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        modal.show();
        document.getElementById('confirmDelete').onclick = function() {
            document.getElementById('deleteForm' + fileId).submit();
        };
    }

    function filterFiles() {
        const searchValue = document.getElementById("searchInput").value.toLowerCase().trim();

        document.querySelectorAll(".product-card").forEach(card => {
            const title = card.querySelector(".card-title")?.textContent.toLowerCase() || '';
            const description = card.querySelector("p")?.textContent.toLowerCase() || '';

            const combinedText = `${title} ${description}`;

            if (combinedText.includes(searchValue)) {
                card.closest(".col-lg-4").style.display = "block";
            } else {
                card.closest(".col-lg-4").style.display = "none";
            }
        });
    }
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    }

    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
    }

    function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
        localStorage.setItem("darkMode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
    }
    
    // Preserve dark mode setting
    window.onload = function () {
        if (localStorage.getItem("darkMode") === "enabled") {
            document.body.classList.add("dark-mode");
        }
    };
    function showProductModal(productId) {
        var modal = new bootstrap.Modal(document.getElementById('productModal' + productId));
        modal.show();
    }
    
    
    