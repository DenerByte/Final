
<script>
// Função para alternar entre modo claro e modo escuro
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');

    
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
}


window.addEventListener('load', () => {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
        document.body.classList.add('dark-mode');
    }
});


function searchProducts(query) {
    if (query.length === 0) {
        document.getElementById("autocomplete-results").innerHTML = "";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "search_suggestions.php?q=" + query, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("autocomplete-results").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
</script>
