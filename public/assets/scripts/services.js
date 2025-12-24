const tableBody = document.getElementById("serviceTableBody");
const searchInput = document.getElementById("searchServices");
const prevBtn = document.getElementById("servicePrevBtn");
const nextBtn = document.getElementById("serviceNextBtn");
const paginationInfo = document.getElementById("paginationInfo");
const headers = document.querySelectorAll("th[data-sort]");
const totalCountElem = document.getElementById("total-count");
const activeCountElem = document.getElementById("active-count");

let currentPage = 1;
let limit = 4;
let totalRows = 0;
let searchQuery = "";
let sortBy = "name";
let sortOrder = "asc";

/* ------ FETCH SERVICES ------ */
 function fetchServices() {
    const params = new URLSearchParams({
        page: currentPage,
        limit,
        search: searchQuery,
        sort: sortBy,
        order: sortOrder
    });
    fetch(`/admin/services-list?${params}`)
    .then(res => res.json())
    .then(data => {
        totalCountElem.textContent = data.data.overallTotal;
        activeCountElem.textContent = data.data.activeTotal;
        totalRows = data.data.totalRows;
        renderTable(data.data.services);
        renderPagination();
    })
    .catch(err => {
        console.error("Error fetching services:", err);
        showAlert("Failed to load services", "error");
    });
}
function renderTable(services) {
    tableBody.innerHTML = "";

    if (services.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align:center">No services found</td>
            </tr>`;
        return;
    }

    services.forEach(service => {
        tableBody.innerHTML += `
            <tr>
                <td>${service.name}</td>
                <td>₹${service.price}</td>
                <td>${service.duration} min</td>
                <td>${service.category_name || "-"}</td>
                <td>${service.subcategory_name || "-"}</td>
                <td>${service.description?service.description:"-"}</td>
                <td>
                    <span class="badge ${service.service_status === 1 ? 'active' : 'inactive'}">
                        ${service.service_status === 1 ? 'active' : 'inactive'}
                    </span>
                </td>
                <td>
                    <button class="action-btn" onclick="editServiceForm('${service.id}','${service.name}','${service.description}','${service.price}',
                    '${service.duration}','${service.service_status}','${service.category_id}','${service.subcategory_id}')"><i class="fa-solid fa-pen"></i></button>
                    <button class="action-btn" onclick="deleteService(${service.id})"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
        `;
    });
}
function renderPagination() {
    const start = (currentPage - 1) * limit + 1;
    const end = Math.min(currentPage * limit, totalRows);

    paginationInfo.textContent = `Showing ${start}-${end} of ${totalRows} services`;

    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage * limit >= totalRows;
}

prevBtn.addEventListener("click", () => {
    if (currentPage > 1) {
        currentPage--;
        fetchServices();
    }

});

nextBtn.addEventListener("click", () => {
    if (currentPage * limit < totalRows) {
        currentPage++;
        fetchServices();
    }
});
let debounceTimer;
searchInput.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        searchQuery = searchInput.value.trim();
        currentPage = 1;
        fetchServices();
    }, 400);
});

/* ---------------- SORTING ---------------- */
headers.forEach(header => {
    header.addEventListener("click", () => {
        const column = header.dataset.sort;

        if (sortBy === column) {
            sortOrder = sortOrder === "asc" ? "desc" : "asc";
        } else {
            sortBy = column;
            sortOrder = "asc";
        }

        currentPage = 1;
        fetchServices();
    });
});
fetchServices();


function editServiceForm(serviceId,name,description,price,duration,service_status,category_id,subcategory_id) {
    document.getElementById('editmodalOverlay').style.display = 'block';
    document.getElementById('editServiceModal').style.display = 'block';
    document.getElementById('edit_id').value = serviceId;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-description').value = description;
    document.getElementById('edit-price').value = price;
    document.getElementById('edit-duration').value = duration;
    document.getElementById('edit-category').value = category_id;
    document.getElementById('edit-subcategory').value = subcategory_id;
    document.getElementById('serviceStatus').value = service_status;

   loadCategories('edit');
}
function closeEditService(){
      document.getElementById('editmodalOverlay').style.display = 'none';
    document.getElementById('editServiceModal').style.display = 'none';
}
function deleteService(serviceId) {
    document.getElementById('deletemodalOverlay').style.display = 'block';
    document.getElementById('deleteServiceModal').style.display = 'block';
    document.getElementById('delete_id').value = serviceId;
}
function closeDeleteService() {
       document.getElementById('deletemodalOverlay').style.display = 'none';
    document.getElementById('deleteServiceModal').style.display = 'none';
}

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

    fetch('/admin/add-service', {
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
            showAlert(data.message,"error");
                    addForm.reset();

            return;
        }
        // showMessage(data.message, "success");
        showAlert(data.message,"success");
        addForm.reset();
        closeServiceForm();
        fetchServices();
    })
    .catch(err => {
        console.error("Fetch error:", err);
        showAlert("An error occurred while adding service", "error");
    });
});
document.getElementById('editServiceForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const editForm = document.getElementById('editServiceForm');

    const serviceData = {
        id: editForm.edit_id.value,
        name: editForm.name.value.trim(),
        description: editForm.description.value.trim(),
        price: parseFloat(editForm.price.value),
        duration: parseInt(editForm.duration.value),
        category_id: editForm.category_id.value,
        subcategory_id: editForm.subcategory_id.value,
        service_status: parseInt(editForm.serviceStatus.value)
    };
    console.log(serviceData);
    fetch('/admin/edit-service', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(serviceData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "error") {
            showAlert(data.message,"error");
            return;
        }
        showAlert(data.message,"success");
        editForm.reset();
        closeEditService();
        fetchServices();
    })
    .catch(err => {
        console.error("Fetch error:", err);
        showAlert("An error occurred while updating service", "error");
    });
});
document.getElementById('deleteServiceForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const deleteForm = document.getElementById('deleteServiceForm');

    const serviceId = deleteForm.delete_id.value;

    fetch('/admin/delete-service', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: serviceId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "error") {
            showAlert(data.message,"error");
            return;
        }
        showAlert(data.message,"success");
        closeDeleteService();
        fetchServices();
    })
    .catch(err => {
        console.error("Fetch error:", err);
        showAlert("An error occurred while deleting service", "error");
    });
});
function openServiceForm() {
    document.getElementById('modalOverlay').style.display = 'block';
    document.getElementById('serviceModal').style.display = 'block';
   loadCategories('add');
}


function closeServiceForm() {
    document.getElementById('modalOverlay').style.display = 'none';
    document.getElementById('serviceModal').style.display = 'none';
}

async function loadCategories(type='add') {
    try{
    const res = await fetch('/admin/categories',{ headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        credemtials: 'include'
    });
    const categories = await res.json();
    let categorySelect;
    if(type==='add'){
     categorySelect = document.getElementById('category');
    }
    if(type==='edit'){
     categorySelect = document.getElementById('edit-category');
    }
    categorySelect.innerHTML = '<option value="">Select category</option>';

    categories.data.forEach(cat => {
        categorySelect.innerHTML +=
            `<option value="${cat.id}">${cat.name}</option>`;
    });
}catch(err){
    console.error("Error loading categories:", err);
}
}
document.getElementById("category").addEventListener('change', async function (e) {
        // if (!e.target.matches("#category") && !e.target.matches("#edit-category")) return;
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
document.getElementById('edit-category').addEventListener('change', async function (e) {
  const categoryId = e.target.value;
    const subcategorySelect = document.getElementById('edit-subcategory');

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