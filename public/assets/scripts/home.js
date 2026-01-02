const homeSuggestions = document.getElementById("homeSuggestions");
const homeSearchInput = document.getElementById("homeSearch");
let searchTimeout = null;

homeSearchInput.addEventListener("input", () => {
    const query = homeSearchInput.value.trim();

    clearTimeout(searchTimeout);

    if (query.length < 2) {
        hideHomeSuggestions();
        return;
    }

     searchTimeout = setTimeout(() => {
        fetchHomeSuggestions(query);
    }, 300);

});

//fetching autosuggestion when search in home
function fetchHomeSuggestions(query) {
    fetch(`/user/service-suggestions?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== "success") {
                throw new Error("Suggestion fetch failed");
            }
            console.log(data.data);
            renderHomeSuggestions(data.data);
        })
        .catch(err => {
            console.error(err);
            hideHomeSuggestions();
        });
}

function renderHomeSuggestions(items) {
    homeSuggestions.innerHTML = "";

    if (!items.length) {
        hideHomeSuggestions();
        return;
    }

    items.forEach(item => {
        const div = document.createElement("div");
        div.textContent = item.name;
        div.onclick = () => {
            homeSearchInput.value = item.name;
            hideHomeSuggestions();

    const query = encodeURIComponent(item.name);
    window.location.href = `/services?search=${query}`
        };
        homeSuggestions.appendChild(div);
    });
    homeSuggestions.classList.remove("hidden");
}
const homeSearchBtn = document.getElementById("homeSearchBtn");
homeSearchBtn.addEventListener("click",()=>{
    const query = encodeURIComponent(homeSearchInput.value);
     window.location.href = `/services?search=${query}`;
})
function hideHomeSuggestions() {
    homeSuggestions.classList.add("hidden");
    homeSuggestions.innerHTML = "";
}
document.addEventListener("click", e => {
    if (!e.target.closest(".hero-search")) {
        hideHomeSuggestions();
    }
});
const homeClearBtn  = document.getElementById("homeClearBtn");
homeClearBtn.addEventListener("click", () => {
    homeSearchInput.value = "";
    hideHomeSuggestions();
});
function redirectServices(){
         window.location.href = `/services`;

}