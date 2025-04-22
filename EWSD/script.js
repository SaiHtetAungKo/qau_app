document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("commentModal");
    let commentsContainer = document.getElementById("commentsContainer");
    let commentButtons = document.querySelectorAll(".comment-btn");

    commentButtons.forEach(button => {
        button.addEventListener("click", function () {
            let ideaId = button.getAttribute("data-idea-id");

            // Fetch comments via AJAX
            fetch("fetch_comments.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "idea_id=" + ideaId
            })
            .then(response => response.text())
            .then(data => {
                commentsContainer.innerHTML = data; // Populate comments
                modal.style.display = "flex"; // Show modal
            });
        });
    });

    // Close modal when clicking the close button
    document.getElementById("closeModal").addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Close modal when clicking outside
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
