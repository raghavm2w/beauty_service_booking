

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
                <h2>12</h2>
            </div>
            <div class="card">
                <span>Active Services</span>
                <h2>10</h2>
            </div>
            <div class="card">
                <span>Most Popular</span>
                <h2>Deep Tissue</h2>
            </div>
        </div>

        <!-- Search -->
        <div class="table-header">
            <input type="text" placeholder="Search services...">
            <button class="btn-secondary">Filter</button>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Deep Tissue Massage</strong>
                            <small>Therapeutic</small>
                        </td>
                        <td>Focuses on realigning deeper layers of muscles.</td>
                        <td>$120.00</td>
                        <td>60 min</td>
                        <td><span class="badge active">Active</span></td>
                        <td>✏️ 🗑️</td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Swedish Massage</strong>
                            <small>Relaxation</small>
                        </td>
                        <td>Gentle massage to relax the entire body.</td>
                        <td>$100.00</td>
                        <td>60 min</td>
                        <td><span class="badge active">Active</span></td>
                        <td>✏️ 🗑️</td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Hot Stone Therapy</strong>
                            <small>Specialty</small>
                        </td>
                        <td>Heated stones placed on specific areas.</td>
                        <td>$150.00</td>
                        <td>90 min</td>
                        <td><span class="badge inactive">Inactive</span></td>
                        <td>✏️ 🗑️</td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Standard Facial</strong>
                            <small>Skincare</small>
                        </td>
                        <td>Cleansing, exfoliating, nourishing skin.</td>
                        <td>$80.00</td>
                        <td>45 min</td>
                        <td><span class="badge active">Active</span></td>
                        <td>✏️ 🗑️</td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination">
                Showing 1–4 of 12 services
                <div>
                    <button>Previous</button>
                    <button class="active">Next</button>
                </div>
            </div>
        </div>

   