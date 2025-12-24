

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
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="serviceTableBody">
                   
            
                </tbody>
            </table>

            <div class="pagination">
            <span id="paginationInfo"></span>
                <div>
                    <button id="servicePrevBtn" >Previous</button>
                    <button class="active" id="serviceNextBtn" >Next</button>
                </div>
            </div>
        </div>

   