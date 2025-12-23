document.addEventListener('submit', (e) => {
        if (!e.target.matches("#addServiceForm")) return;
    e.preventDefault();
const addForm = document.getElementById('addServiceForm');

    const serviceData = {
        name: addForm.name.value.trim(),
        description: addForm.description.value.trim(),
        price: parseFloat(addForm.price.value),
        duration: parseInt(addForm.duration.value),
        category_id: addForm.category.value,
        subcategory_id: addForm.subcategory.value
    };

    fetch('/add-service', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(serviceData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "error") {
            // showMessage(data.message, "error");
            alert(data.message);
                    addForm.reset();

            return;
        }
        // showMessage(data.message, "success");
        alert(data.message);
        addForm.reset();
        closeServiceForm();
    })
    .catch(err => {
        console.error("Fetch error:", err);
        showMessage("An error occurred while adding service", "error");
    });
});
function openServiceForm() {
    document.getElementById('modalOverlay').style.display = 'block';
    document.getElementById('serviceModal').style.display = 'block';
   loadCategories();
}


function closeServiceForm() {
    document.getElementById('modalOverlay').style.display = 'none';
    document.getElementById('serviceModal').style.display = 'none';
}
async function loadCategories() {
    try{
    const res = await fetch('/admin/categories',{ headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        credemtials: 'include'
    });
    const categories = await res.json();
    console.log(categories);
    const categorySelect = document.getElementById('category');
    categorySelect.innerHTML = '<option value="">Select category</option>';

    categories.data.forEach(cat => {
        categorySelect.innerHTML +=
            `<option value="${cat.id}">${cat.name}</option>`;
    });
}catch(err){
    console.error("Error loading categories:", err);
}
}
document.addEventListener('change', async function (e) {
        if (!e.target.matches("#category")) return;
    const categoryId = e.target.value;
    const subcategorySelect = document.getElementById('subcategory');

    subcategorySelect.innerHTML = '<option value="">Loading...</option>';
    subcategorySelect.disabled = true;

    if (!categoryId) {
        subcategorySelect.innerHTML = '<option value="">Select subcategory</option>';
        return;
    }

    const res = await fetch(`/admin/subcategories?id=${categoryId}`);
    const subcategories = await res.json();

    subcategorySelect.innerHTML = '<option value="">Select subcategory</option>';

    subcategories.data.forEach(sub => {
        subcategorySelect.innerHTML +=
            `<option value="${sub.id}">${sub.name}</option>`;
    });

    subcategorySelect.disabled = false;
});

