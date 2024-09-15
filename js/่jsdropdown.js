
    document.addEventListener("DOMContentLoaded", function() {
        const accountButton = document.getElementById('accountButton');
        const dropdownContent = document.getElementById('dropdownContent');

        accountButton.addEventListener('click', function() {
            dropdownContent.classList.toggle('show');
        });

        // Close the dropdown if the user clicks outside of it
        window.addEventListener('click', function(event) {
            if (!event.target.matches('#accountButton')) {
                if (dropdownContent.classList.contains('show')) {
                    dropdownContent.classList.remove('show');
                }
            }
        });
    });

