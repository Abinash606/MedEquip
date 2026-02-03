<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Schedule Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <!-- add id to capture selected customer in invite script -->
                            <select class="form-select" id="appointmentCustomer">
                                <option value="" selected>Select Customer</option>
                                <option value="Mercy Hospital">Mercy Hospital</option>
                                <option value="Downtown Clinic">Downtown Clinic</option>
                                <option value="Westside Imaging">Westside Imaging</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Site Name</label>
                            <!-- add id to capture site name in invite script -->
                            <input type="text" class="form-control" id="appointmentSiteName"
                                placeholder="Enter site name">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Site Address</label>
                            <!-- add id to capture site address if needed -->
                            <input type="text" class="form-control" id="appointmentSiteAddress"
                                placeholder="Enter site address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Info</label>
                            <input type="text" class="form-control" placeholder="Phone or email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <!-- status select with id for potential use -->
                            <select class="form-select" id="appointmentStatus">
                                <option value="Ready" selected>Ready</option>
                                <option value="Needs Attention">Needs Attention</option>
                                <option value="Past Due">Past Due</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Visit Type</label>
                            <!-- add id to capture visit type for invites -->
                            <select class="form-select" id="appointmentVisitType">
                                <option value="Routine Inspection">Routine Inspection</option>
                                <option value="Emergency Repair">Emergency Repair</option>
                                <option value="Calibration">Calibration</option>
                                <option value="Preventive Maintenance">Preventive Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Equipment</label>
                            <!-- id used to capture equipment description -->
                            <input type="text" class="form-control" id="appointmentEquipment"
                                placeholder="Enter asset tag or description">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <!-- id used for invite date extraction -->
                            <input type="date" class="form-control" id="appointmentDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time</label>
                            <!-- id used for invite time extraction -->
                            <input type="time" class="form-control" id="appointmentTime">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assign Technician</label>
                            <!-- id used for assigned technician selection -->
                            <select class="form-select" id="appointmentAssignedTech">
                                <option value="Auto-Assign">Auto-Assign</option>
                                <option value="John Doe">John Doe</option>
                                <option value="Sarah Lee">Sarah Lee</option>
                                <option value="Mike Ross">Mike Ross</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Invite Technician (Email)</label>
                            <input type="email" class="form-control" id="inviteTechEmail"
                                placeholder="tech@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Invite Customer (Email)</label>
                            <input type="email" class="form-control" id="inviteCustomerEmail"
                                placeholder="customer@example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="3" placeholder="Additional notes"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createAppointmentBtn">Create Appointment</button>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Modal -->
<div class="modal fade" id="inventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Part #</label><input type="text"
                                class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Part Description</label><input type="text"
                                class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Bin</label><input type="text"
                                class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">QTY</label><input type="number"
                                class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Total Value</label><input type="text"
                                class="form-control"></div>
                        <div class="col-12"><label class="form-label">Image</label><input type="file"
                                class="form-control"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Site Modal -->
<div class="modal fade" id="addSiteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="add-site-name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="add-site-name">
                    </div>
                    <div class="mb-3">
                        <label for="add-site-address" class="form-label">Site Address</label>
                        <input type="text" class="form-control" id="add-site-address">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="add-site-city" class="form-label">City</label>
                            <input type="text" class="form-control" id="add-site-city" placeholder="e.g., New York">
                        </div>
                        <div class="col-md-6">
                            <label for="add-site-state" class="form-label">State</label>
                            <input type="text" class="form-control" id="add-site-state" placeholder="e.g., NY">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="add-site-contact-name" class="form-label">Site Contact Name</label>
                        <input type="text" class="form-control" id="add-site-contact-name">
                    </div>
                    <div class="mb-3">
                        <label for="add-site-phone" class="form-label">Site Phone Number</label>
                        <input type="text" class="form-control" id="add-site-phone">
                    </div>
                    <div class="mb-3">
                        <label for="add-site-email" class="form-label">Site Email</label>
                        <input type="email" class="form-control" id="add-site-email">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTechModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Onboard New Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Specialization</label>
                        <select class="form-select">
                            <option>General Biomedical</option>
                            <option>Imaging (MRI/CT)</option>
                            <option>Sterilization</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Certifications</label>
                        <input type="text" class="form-control" placeholder="e.g. CBET, CRES">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save Technician</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEquipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Asset Tag</label>
                            <input type="text" class="form-control" placeholder="Auto-generated if empty">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Serial Number</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Make</label>
                            <input type="text" class="form-control" placeholder="e.g., Philips, GE">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Model</label>
                            <input type="text" class="form-control" placeholder="Enter model">
                        </div>
                        <!-- Additional details for device classification -->
                        <div class="col-md-6">
                            <label class="form-label">Device Type</label>
                            <input type="text" class="form-control" placeholder="e.g., MRI, CT, Ultrasound">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" placeholder="e.g., Radiology">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room/Location</label>
                            <input type="text" class="form-control" placeholder="e.g., Room 101">
                        </div>
                        <!-- Additional fields for PM Kit selection and quick notes, based off the flow
                             diagrams in the Asset Equipment Entry materials.  These enable the
                             technician to select a preventative maintenance kit and include short
                             pre-set notes before entering a full description. -->
                        <div class="col-md-6">
                            <label class="form-label">PM Kit</label>
                            <select class="form-select">
                                <option selected>Select PM Kit</option>
                                <option>Basic Kit</option>
                                <option>Full Kit</option>
                                <option>Calibration Only</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fast Notes</label>
                            <input type="text" class="form-control" placeholder="Short note for fast entry">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Customer Location</label>
                            <select class="form-select">
                                <option>Mercy Hospital</option>
                                <option>Downtown Clinic</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Installation Date</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Warranty Expires</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Add Asset</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Inspection Workflow -->
<div class="modal fade" id="addInspectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Inspection Workflow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Asset Information -->
                <div class="inspection-step" id="inspection-step-1">
                    <h5 class="fw-bold">Step 1: Enter Asset Information</h5>
                    <!-- Added customer site selection to enable site‑level inventory lookups -->
                    <div class="mb-3">
                        <label for="inspection-site" class="form-label">Customer Site</label>
                        <select class="form-select" id="inspection-site">
                            <option value="" disabled selected>Select site</option>
                            <!-- Options will be populated via JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="asset-barcode" class="form-label">Asset/Barcode Number</label>
                        <input type="text" class="form-control" id="asset-barcode"
                            placeholder="Enter or scan asset/barcode">
                    </div>
                    <p class="text-muted small">The system will search the selected site's inventory for a matching
                        asset.</p>
                </div>

                <!-- Step 2: Verification (Not Found) -->
                <div class="inspection-step" id="inspection-step-2" style="display: none;">
                    <h5 class="fw-bold">Step 2: Asset Verification (Not Found)</h5>
                    <p class="text-danger">Asset not found. Please enter the model number to proceed.</p>
                    <div class="mb-3">
                        <label for="model-search" class="form-label">Search Model</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="model-search" placeholder="Search for model...">
                            <button class="btn btn-outline-secondary" type="button">Search</button>
                        </div>
                        <div class="list-group mt-2" style="max-height: 150px; overflow-y: auto;">
                            <!-- Search results will populate here dynamically based on user input -->
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Manufacturer</label><input type="text"
                                class="form-control" id="new-manufacturer" value="American Optical Company" readonly>
                        </div>
                        <div class="col-md-6"><label class="form-label">Model</label><input type="text"
                                class="form-control" id="new-model" readonly></div>
                        <div class="col-md-6"><label class="form-label">Model Type</label><input type="text"
                                class="form-control" id="new-model-type" value="Microscope" readonly></div>
                        <div class="col-md-6"><label class="form-label">Serial #</label><input type="text"
                                class="form-control" placeholder="Enter Serial Number"></div>
                    </div>
                </div>

                <!-- Step 3: Inspection Details -->
                <div class="inspection-step" id="inspection-step-3" style="display: none;">
                    <h5 class="fw-bold">Step 3: Enter Inspection Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Customer</label><input type="text"
                                id="inspection-customer" class="form-control" value="AFC Urgent Care - Phoenixville"
                                readonly></div>
                        <div class="col-md-6"><label class="form-label">Model</label><input type="text"
                                class="form-control" value="Defibtech - DDU-C2300EN - AED" readonly></div>
                        <div class="col-md-6"><label class="form-label">Department</label><input type="text"
                                class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Room</label><input type="text"
                                class="form-control" value="Nurse Area"></div>
                        <div class="col-md-6"><label class="form-label">Serial #</label><input type="text"
                                class="form-control" value="102048177" readonly></div>
                        <div class="col-md-6"><label class="form-label">Asset ID</label><input type="text"
                                class="form-control" value="13076251" readonly></div>
                        <div class="col-md-6"><label class="form-label">Manufacture PM Frequency (Days)</label><select
                                class="form-select">
                                <option>12 Month</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">Action Performed</label><select
                                class="form-select">
                                <option>Annual Performance Inspection</option>
                            </select></div>
                        <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control"
                                rows="3">Verified Pad and battery expiration date.\nVerified energy delivered using defib analyzer.\nSystem Passed.</textarea>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <p class="small text-muted">Please add detailed description of actions taken, recommended parts
                            and applicable photos.</p>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Pass
                                Inspection</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fail
                                Inspection</button>
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Fail Inspection & Open
                                Work Order</button>
                            <button type="button" class="btn btn-info" data-bs-dismiss="modal">Repair
                                Inspection</button>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <button type="button" class="btn btn-outline-primary" onclick="goToStep(1)">Enter Next
                            Device</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button type="button" class="btn btn-secondary" id="inspection-back-btn" onclick="handleBack()"
                        style="display: none;">Back</button>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="inspection-next-btn"
                        onclick="handleNext()">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Equipment Admin Add -->
<div class="modal fade" id="equipAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Equipment Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Manufacturer</label>
                        <input type="text" class="form-control" placeholder="e.g., Midmark">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" class="form-control" placeholder="Enter model name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Part #</label>
                        <input type="text" class="form-control" placeholder="Part number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" class="form-control" placeholder="Device type (e.g., Exam Table)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">AKA (Also Known As)</label>
                        <input type="text" class="form-control" placeholder="Alias for equipment">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Add Equipment</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for creating new work orders, inspections or service requests -->
<div class="modal fade" id="newWorkOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create New Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Request Type</label>
                            <select class="form-select">
                                <option selected>Work Order</option>
                                <option>Inspection</option>
                                <option>Service</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <select class="form-select">
                                <option selected>Select Customer</option>
                                <option>Mercy Hospital</option>
                                <option>Downtown Clinic</option>
                                <option>Westside Imaging</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Site</label>
                            <input type="text" class="form-control" placeholder="Enter site name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Equipment</label>
                            <input type="text" class="form-control" placeholder="Enter asset tag or description">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3"
                                placeholder="Describe the issue or request"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority</label>
                            <select class="form-select">
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assign Technician</label>
                            <select class="form-select">
                                <option>Auto-Assign</option>
                                <option>John Doe</option>
                                <option>Sarah Lee</option>
                                <option>Mike Ross</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Preferred Date</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Preferred Time</label>
                            <input type="time" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Create Request</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding new inventory items -->
<div class="modal fade" id="addInventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control" placeholder="Enter item name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" placeholder="Category (e.g., Accessory)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" min="0" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" placeholder="Storage location">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reorder Level</label>
                            <input type="number" class="form-control" min="0" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select">
                                <option selected>In Stock</option>
                                <option>Low Stock</option>
                                <option>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" placeholder="Optional notes">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Add Item</button>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Detail Modal -->
<div class="modal fade" id="inventoryDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Inventory Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Part/Item Number:</strong> <span id="modal-item-part"></span></p>
                        <p><strong>Description:</strong> <span id="modal-item-desc"></span></p>
                        <p><strong>Standard Cost:</strong> <span id="modal-item-std-cost"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Cost:</strong> <span id="modal-item-total-cost"></span></p>
                        <p><strong>Average Cost:</strong> <span id="modal-item-avg-cost"></span></p>
                        <p><strong>Markup:</strong> <span id="modal-item-markup"></span></p>
                        <p><strong>Vendor:</strong> <span id="modal-item-vendor"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Site Modal -->
<div class="modal fade" id="siteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="site-name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site-name">
                        </div>
                        <div class="col-md-6">
                            <label for="site-customer-name" class="form-label">Customer Name</label>
                            <select class="form-select" id="site-customer-name">
                                <option selected>Choose...</option>
                                <option>Tiger Nixon</option>
                                <option>Garrett Winters</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="site-address" class="form-label">Site Address</label>
                            <input type="text" class="form-control" id="site-address">
                        </div>
                        <div class="col-md-6">
                            <label for="site-state" class="form-label">State</label>
                            <input type="text" class="form-control" id="site-state" placeholder="e.g., NY">
                        </div>
                        <div class="col-md-6">
                            <label for="site-zip" class="form-label">Zip code</label>
                            <input type="text" class="form-control" id="site-zip" placeholder="e.g., 10001">
                        </div>
                        <div class="col-md-6">
                            <label for="site-contact-name" class="form-label">Site Contact Name</label>
                            <input type="text" class="form-control" id="site-contact-name">
                        </div>
                        <div class="col-md-6">
                            <label for="site-phone" class="form-label">Site Phone Number</label>
                            <input type="text" class="form-control" id="site-phone">
                        </div>
                        <div class="col-12">
                            <label for="site-email" class="form-label">Site Email</label>
                            <input type="email" class="form-control" id="site-email">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>


<!-- Equipment Modal -->
<div class="modal fade" id="equipmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Asset Tag</label><input type="text"
                                class="form-control" id="equipment-asset-tag" placeholder="Auto-generated if empty">
                        </div>
                        <div class="col-md-6"><label class="form-label">Serial Number</label><input type="text"
                                class="form-control" id="equipment-serial-number"></div>
                        <div class="col-md-6"><label class="form-label">Make</label><input type="text"
                                class="form-control" id="equipment-make" placeholder="e.g., Philips, GE"></div>
                        <div class="col-md-6"><label class="form-label">Model</label><input type="text"
                                class="form-control" id="equipment-model" placeholder="Enter model"></div>
                        <div class="col-md-6"><label class="form-label">Device Type</label><input type="text"
                                class="form-control" id="equipment-device-type" placeholder="e.g., MRI, CT, Ultrasound">
                        </div>
                        <div class="col-md-6"><label class="form-label">Department</label><input type="text"
                                class="form-control" id="equipment-department" placeholder="e.g., Radiology"></div>
                        <div class="col-md-6"><label class="form-label">Room/Location</label><input type="text"
                                class="form-control" id="equipment-location" placeholder="e.g., Room 101"></div>
                        <div class="col-md-6"><label class="form-label">Device Status</label><select class="form-select"
                                id="equipment-status">
                                <option selected>Ready</option>
                                <option>Need Attention</option>
                                <option>Not Ready</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">PM Kit</label><select class="form-select">
                                <option selected>Select PM Kit</option>
                                <option>Basic Kit</option>
                                <option>Full Kit</option>
                                <option>Calibration Only</option>
                            </select></div>
                        <div class="col-12"><label class="form-label">Fast Notes</label><input type="text"
                                class="form-control" placeholder="Short note for fast entry"></div>
                        <div class="col-12"><label class="form-label">Customer Location</label><select
                                class="form-select">
                                <option>Mercy Hospital</option>
                                <option>Downtown Clinic</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">Installation Date</label><input type="date"
                                class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Warranty Expires</label><input type="date"
                                class="form-control"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="equipment-save-btn">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="admin-username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="admin-username">
                    </div>
                    <div class="mb-3">
                        <label for="admin-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="admin-email">
                    </div>
                    <div class="mb-3">
                        <label for="admin-role" class="form-label">Role</label>
                        <select class="form-select" id="admin-role">
                            <option>Super Admin</option>
                            <option>Admin</option>
                            <option>Viewer</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Technician Modal -->
<div class="modal fade" id="technicianModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">Add Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="technicianForm" novalidate>
                    <input type="hidden" id="technician-id">
                    <div class="row g-3">

                        <!-- Firstname -->
                        <div class="col-md-6">
                            <label for="technician-firstname" class="form-label">Firstname</label>
                            <input type="text" class="form-control" id="technician-firstname">
                        </div>

                        <!-- Lastname -->
                        <div class="col-md-6">
                            <label for="technician-lastname" class="form-label">Lastname</label>
                            <input type="text" class="form-control" id="technician-lastname">
                        </div>

                        <!-- Username -->
                        <div class="col-md-6">
                            <label for="technician-username" class="form-label">Username <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="technician-username" required
                                oninput="validateTechField(this)">
                            <div class="invalid-feedback" id="error-username">Username is required.</div>
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="technician-password" class="form-label d-flex align-items-center">
                                Password
                                <span class="text-danger ms-1" id="password-required">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="technician-password" required
                                    oninput="validateTechField(this)">
                                <small class="text-muted position-absolute start-0" style="top: 100%; display: none;"
                                    id="password-hint">
                                    Leave blank to keep current password
                                </small>
                            </div>
                            <div class="invalid-feedback" id="error-password">Password is required.</div>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label for="technician-phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="technician-phone"
                                oninput="validateTechPhone(this)">
                            <div class="invalid-feedback" id="error-phone">Please enter a valid phone number (digits,
                                spaces, dashes, or parentheses only).</div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="technician-email" class="form-label">Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="technician-email" required
                                oninput="validateTechField(this)">
                            <div class="invalid-feedback" id="error-email">Please enter a valid email address.</div>
                        </div>

                        <!-- States — populated dynamically from us_states table -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold">Select State(s) <span class="text-danger">*</span></label>
                            <div class="states-grid" id="states-grid-wrapper"
                                style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 1rem; transition: border-color 0.2s ease;">
                                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-2" id="states-checkbox-row">
                                    <!-- checkboxes injected here by fetchStates() -->
                                </div>
                            </div>
                            <!-- States validation feedback -->
                            <div id="error-states"
                                style="color: #dc3545; font-size: 0.875rem; margin-top: 0.4rem; visibility: hidden;">
                                Please select at least one state.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveTechnician()">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="saveSpinner"></span>
                    Save Technician
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Site Inspection Modal -->
<div class="modal fade" id="siteInspectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Site Inspection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Asset Information -->
                <div class="site-inspection-step" id="site-inspection-step-1">
                    <h5 class="fw-bold">Step 1: Enter Asset Information</h5>
                    <p><strong>Site:</strong> <span id="current-site-name"></span></p>
                    <div class="mb-3">
                        <label for="site-asset-barcode" class="form-label">Asset/Barcode Number</label>
                        <input type="text" class="form-control" id="site-asset-barcode"
                            placeholder="Enter or scan asset/barcode">
                    </div>
                    <p class="text-muted small">Enter the asset or barcode number. If the asset exists in the site's
                        inventory, details will be automatically filled.</p>
                </div>

                <!-- Step 2: Asset Verification (Not Found) -->
                <div class="site-inspection-step" id="site-inspection-step-2" style="display: none;">
                    <h5 class="fw-bold">Step 2: Asset Verification (Not Found)</h5>
                    <p class="text-danger">Asset not found. Please search for the device model in the equipment
                        database.</p>
                    <div class="mb-3">
                        <label for="site-model-search" class="form-label">Search Model</label>
                        <input type="text" class="form-control" id="site-model-search"
                            placeholder="Search for model...">
                        <div class="list-group mt-2" id="site-model-results"
                            style="max-height: 150px; overflow-y: auto;"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Manufacturer</label><input type="text"
                                class="form-control" id="site-new-manufacturer" readonly></div>
                        <div class="col-md-6"><label class="form-label">Model</label><input type="text"
                                class="form-control" id="site-new-model" readonly></div>
                        <div class="col-md-6"><label class="form-label">Description</label><input type="text"
                                class="form-control" id="site-new-description" readonly></div>
                        <div class="col-md-6"><label class="form-label">Serial #</label><input type="text"
                                class="form-control" id="site-serial-number" placeholder="Enter Serial Number"></div>
                    </div>
                </div>

                <!-- Step 3: Inspection Details -->
                <div class="site-inspection-step" id="site-inspection-step-3" style="display: none;">
                    <h5 class="fw-bold">Step 3: Enter Inspection Details</h5>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Model</label><input type="text"
                                class="form-control" id="site-model-readonly" readonly></div>
                        <div class="col-md-4"><label class="form-label">Description</label><input type="text"
                                class="form-control" id="site-description-readonly" readonly></div>
                        <div class="col-md-4"><label class="form-label">Serial #</label><input type="text"
                                class="form-control" id="site-serial-readonly" readonly></div>
                        <div class="col-md-4"><label class="form-label">Asset ID</label><input type="text"
                                class="form-control" id="site-assetid-readonly" readonly></div>
                        <div class="col-md-4"><label class="form-label">Department</label><input type="text"
                                class="form-control" id="site-department"></div>
                        <div class="col-md-4"><label class="form-label">Location / Room</label><input type="text"
                                class="form-control" id="site-location"></div>
                        <div class="col-md-6"><label class="form-label">PM Service Frequency</label><select
                                class="form-select" id="site-pm-frequency">
                                <option value="" disabled selected>Select frequency</option>
                                <option>6 Month</option>
                                <option>12 Month</option>
                                <option>24 Month</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">Inspection Type</label><select
                                class="form-select" id="site-inspection-type">
                                <option value="" disabled selected>Select type</option>
                                <option>Annual Performance Inspection</option>
                                <option>Preventive Maintenance</option>
                                <option>Safety Check</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">Technician</label><input type="text"
                                class="form-control" id="site-technician"></div>
                        <div class="col-md-6"><label class="form-label">Inspection Date</label><input type="date"
                                class="form-control" id="site-inspection-date"></div>
                        <div class="col-md-12"><label class="form-label">Service Notes</label><textarea
                                class="form-control" id="site-notes" rows="3"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Status</label><select class="form-select"
                                id="site-status">
                                <option>Pass</option>
                                <option>Fail</option>
                                <option>Repaired</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">Device Complete</label><select
                                class="form-select" id="site-device-completion">
                                <option>Yes</option>
                                <option>No</option>
                            </select></div>
                    </div>
                    <div class="mt-3 text-center">
                        <p class="small text-muted">Add device notes and complete status.</p>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" onclick="siteCompleteInspection('Pass')">Pass
                                Inspection</button>
                            <button type="button" class="btn btn-danger" onclick="siteCompleteInspection('Fail')">Fail
                                Inspection</button>
                            <button type="button" class="btn btn-info"
                                onclick="siteCompleteInspection('Repaired')">Repair Inspection</button>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <button type="button" class="btn btn-outline-primary" onclick="siteGoToStep(1)">Enter Next
                            Device</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button type="button" class="btn btn-secondary" id="site-inspection-back-btn"
                        onclick="siteHandleBack()" style="display: none;">Back</button>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="site-inspection-next-btn"
                        onclick="siteHandleNext()">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>