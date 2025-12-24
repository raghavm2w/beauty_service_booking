

        <div class="breadcrumb">Home > Services</div>

        <div class="page-header">
            <div class="header-info">
                <h1>My Services</h1>
                <p>Manage your service offerings, prices, and availability.</p>
            </div>
            <button class="add-btn" onclick="openServiceForm()">+ Add New Service</button>
        </div>
        <div id="modalOverlay" class="modal-overlay" onclick="closeServiceForm()"> </div>

<div id="serviceModal" class="modal">
    <form id="addServiceForm" class="service-form">
    <input type="text" name="name" placeholder="Service name" required>

    <select name="category_id" id="category">
    </select>
    <select name="subcategory_id" id="subcategory">
    </select>

    <input type="number" name="duration" placeholder="Duration (minutes)" required>

    <input type="number" name="price" placeholder="Price" required>

    <textarea name="description" placeholder="Description"></textarea>

    <button type="submit">Add Service</button>
</form>
</div>
       
        <!-- Stats -->
        <div class="stats">
            <div class="card">
                <span>Total Services</span>
                <h2 id="total-count"></h2>
            </div>
            <div class="card">
                <span>Active Services</span>
                <h2 id="active-count"></h2>
            </div>
    
        </div>

        <!-- Search -->
        <div class="table-header">
            <input type="text" id="searchServices" placeholder="Search services...">
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                         <th data-sort="name">Service Name <i class="fa-solid fa-sort"></i></th>
                <th data-sort="price">Price <i class="fa-solid fa-sort"></i></th>
                <th data-sort="duration">Duration <i class="fa-solid fa-sort"></i></th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="serviceTableBody">
                   
            
                </tbody>
            </table>
 <div id="editmodalOverlay" class="modal-overlay" onclick="closeEditService()"> </div>

<div id="editServiceModal" class="modal">
    <form id="editServiceForm" class="service-form">
    <input type="hidden" name="edit_id" id="edit_id">
    <input type="text" id="edit-name" name="name" placeholder="Service name" required>

    <select name="category_id"  id="edit-category">
    </select>
    <select name="subcategory_id" id="edit-subcategory">
    </select>

    <input type="number" id="edit-duration" name="duration" placeholder="Duration (minutes)" required>

    <input type="number" id="edit-price" name="price" placeholder="Price" required>
    <label>Status</label>
<select name="service_status" id="serviceStatus">
    <option value="1">active</option>
    <option value="0">inactive</option>
</select>
<small class="hint">Inactive services won't be visible to customers</small>
    <textarea name="description" id="edit-description"placeholder="Description"></textarea>
    <button type="submit">Update Service details</button>
</form>
</div>
<div id="deletemodalOverlay" class="modal-overlay" onclick="closeDeleteService()"> </div>

<div id="deleteServiceModal" class="modal">
    <form id="deleteServiceForm" class="service-form">
    <input type="hidden" name="delete_id" id="delete_id">
    <h3>Are you sure you want to delete this service?</h3>
        <div class="form-actions">
            <button type="button" class="btn-close" onclick="closeDeleteService()">Cancel</button>
                <button type="submit">Delete Service</button>

        </div>
    </form>
</div>
            <div class="pagination">
            <span id="paginationInfo"></span>
                <div>
                    <button id="servicePrevBtn" >Previous</button>
                    <button class="active" id="serviceNextBtn" >Next</button>
                </div>
            </div>
        </div>

   