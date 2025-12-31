let currentPage = 1;
let hasMore = true;
let isLoading = false;

const LIMIT = 10;

const filters = {
    category_id: "",
    subcategory_id: "",
    search: ""
};
const searchInput = document.getElementById("searchInput");
const suggestionsBox = document.getElementById("searchSuggestions");
const clearBtn = document.getElementById("clearBtn");

let searchTimeout = null;
const params = new URLSearchParams(window.location.search);
const searchQuery = params.get("search");

if (searchQuery) {
    filters.search = searchQuery;
    fetchServices({ reset: true });
}
document.addEventListener("DOMContentLoaded", () => {
    fetchCategories();
      fetchServices({ reset: true });
          window.addEventListener("scroll", handleInfiniteScroll);

});

function fetchCategories() {
    fetch("/admin/categories")
    .then(res => res.json())
    .then(data => {
            if (data.status !== "success" || !Array.isArray(data.data)) {
                throw new Error("Error getting category data");
            }
            renderCategories(data.data);
        })
        .catch(error => {
            console.error(error);
            document.getElementById("categoryList").innerHTML =
                `<li class="error">Unable to load categories</li>`;
        });
}

function renderCategories(categories) {
    const list = document.getElementById("categoryList");
    list.innerHTML = "";

    const allItem = document.createElement("li");
    allItem.textContent = "All";
    allItem.classList.add("active");
    allItem.dataset.id = "";
    allItem.onclick = () => onCategorySelect("",allItem);
    list.appendChild(allItem);

    categories.forEach(category => {
        const li = document.createElement("li");
        li.textContent = category.name;
        li.dataset.id = category.id;

        li.onclick = () => onCategorySelect(category.id, li);

        list.appendChild(li);
    });
}

function onCategorySelect(categoryId, selectedEl) {
    document.querySelectorAll("#categoryList li").forEach(li =>
        li.classList.remove("active")
    );

    if (selectedEl) selectedEl.classList.add("active");
      filters.category_id = categoryId;
    filters.subcategory_id = "";
       if (!categoryId) {
        clearSubcategories();
        fetchServices({ reset: true });
        return;
    }
        fetchServices({ reset: true });
        if(categoryId){
        fetchSubcategories(categoryId);
        }
}
function fetchSubcategories(categoryId) {
    fetch(`/admin/subcategories?id=${categoryId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== "success" || !Array.isArray(data.data)) {
                throw new Error("Error getting subcategories");
            }
            renderSubcategories(data.data);
        })
        .catch(error => {
            console.error(error);
            clearSubcategories();
        });
}
function renderSubcategories(subcategories) {
    const container = document.getElementById("subcategoryTabs");
    container.innerHTML = "";

    const allBtn = document.createElement("button");
    allBtn.textContent = "All";
    allBtn.classList.add("active");
    allBtn.onclick = () => onSubcategorySelect("", allBtn);
    container.appendChild(allBtn);

    subcategories.forEach(sub => {
        const btn = document.createElement("button");
        btn.textContent = sub.name;
        btn.dataset.id = sub.id;
        btn.onclick = () => onSubcategorySelect(sub.id, btn);
        container.appendChild(btn);
    });
}
function onSubcategorySelect(subcategoryId, selectedEl) {
    document.querySelectorAll("#subcategoryTabs button").forEach(btn =>
        btn.classList.remove("active")
    );
    if (selectedEl) selectedEl.classList.add("active");
     filters.subcategory_id = subcategoryId;
    fetchServices({ reset: true });

}
function fetchServices({ reset = false } = {}) {
    if (isLoading || (!hasMore && !reset)) return;

    if (reset) {
        currentPage = 1;
        hasMore = true;
        document.getElementById("serviceGrid").innerHTML = "";
    }

    isLoading = true;

    const params = new URLSearchParams({
        page: currentPage,
        limit: LIMIT,
        ...filters
    });

    fetch(`/user/services?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== "success") {
                throw new Error("Failed to fetch services");
            }
            renderServices(data.data.services);
            hasMore = data.data.hasMore;
            currentPage++;
        })
        .catch(error => {
            console.error(error);
        })
        .finally(() => {
            isLoading = false;
        });
}
function renderServices(services) {
    const grid = document.getElementById("serviceGrid");

    if (!services.length && currentPage === 1) {
        grid.innerHTML = "<p>No services found</p>";
        return;
    }

    services.forEach(service => {
        const card = document.createElement("div");
        card.className = "service-card";

        card.innerHTML = `
            <div class="service-icon"></div>
            <h3>${service.name}</h3>
            <div class="service-meta">
                <span>⏱ ${service.duration} min</span>
                <span>₹${service.price}</span>
            </div>
            <p class="desc">${service.description}</p>

            <p class="provider">by ${service.provider_name}</p>
            <button id="bookBtn" onclick="openBookNow(this)"  data-service-id="${service.id}"
    data-provider-id="${service.provider_id}">Book Now</button>
        `;

        grid.appendChild(card);
    });
}
// const servicesScroll = document.getElementById("servicesScroll");

// servicesScroll.addEventListener("scroll", () => {
//     const scrollTop = servicesScroll.scrollTop;
//     const scrollHeight = servicesScroll.scrollHeight;
//     const clientHeight = servicesScroll.clientHeight;

//     if (scrollTop + clientHeight >= scrollHeight - 200) {
//         fetchServices();
//     }
// });
function handleInfiniteScroll() {
    console.log("scroll");
    const scrollPosition = window.innerHeight + window.scrollY;
    const threshold = document.body.offsetHeight - 300;

    if (scrollPosition >= threshold) {
        fetchServices();
    }
}
function clearSubcategories() {
    document.getElementById("subcategoryTabs").innerHTML = "";
}
searchInput.addEventListener("input", () => {
    const query = searchInput.value.trim();

    clearTimeout(searchTimeout);

    if (query.length < 2) {
        hideSuggestions();
        return;
    }

    searchTimeout = setTimeout(() => {
        fetchSearchSuggestions(query);
    }, 300);
});

//fetching autosuggestion when search
function fetchSearchSuggestions(query) {
    fetch(`/user/service-suggestions?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== "success") {
                throw new Error("Suggestion fetch failed");
            }
            renderSuggestions(data.data);
        })
        .catch(err => {
            console.error(err);
            hideSuggestions();
        });
}

function renderSuggestions(items) {
    suggestionsBox.innerHTML = "";

    if (!items.length) {
        hideSuggestions();
        return;
    }

    items.forEach(item => {
        const div = document.createElement("div");
        div.textContent = item.name;
        div.onclick = () => {
            searchInput.value = item.name;
            hideSuggestions();

            filters.search = item.name;
            fetchServices({ reset: true });
        };

        suggestionsBox.appendChild(div);
    });
    suggestionsBox.classList.remove("hidden");
}
document.getElementById("searchBtn").addEventListener("click",()=>{
     filters.search = searchInput.value;
                 hideSuggestions();
            fetchServices({ reset: true });
})
function hideSuggestions() {
    suggestionsBox.classList.add("hidden");
    suggestionsBox.innerHTML = "";
}

document.addEventListener("click", e => {
    if (!e.target.closest(".services-search")) {
        hideSuggestions();
    }
});
clearBtn.addEventListener("click", () => {
    searchInput.value = "";
    filters.search = "";
    hideSuggestions();
    clearBtn.classList.add("hidden");
    fetchServices({ reset: true });
});