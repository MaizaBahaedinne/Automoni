<?php
// app/Views/organizations/create_enhanced.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Create Organization' ?> - Automoni</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet for Maps -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" rel="stylesheet">
    <!-- Phone number input -->
    <link href="https://cdn.jsdelivr.net/npm/intl-tel-input@21.0.0/build/css/intlTelInput.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #22c55e;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-wrapper {
            max-width: 1000px;
            margin: 0 auto;
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header p {
            color: var(--secondary-color);
            margin: 0;
            font-size: 0.95rem;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .form-card-header {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
            padding: 20px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-card-header h5 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .form-card-header i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .form-card-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.95rem;
        }

        .required-badge {
            color: var(--error-color);
            font-weight: 700;
            font-size: 1.2rem;
        }

        .form-control, .form-select, .input-group {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 11px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
            background-color: #f8fafc;
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--error-color);
            background-image: none;
        }

        .form-control.is-invalid:focus,
        .form-select.is-invalid:focus {
            border-color: var(--error-color);
            box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.15);
        }

        .invalid-feedback {
            display: block;
            color: var(--error-color);
            font-size: 0.85rem;
            margin-top: 6px;
            font-weight: 500;
        }

        .form-text {
            color: var(--secondary-color);
            font-size: 0.85rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        textarea.form-control {
            min-height: 100px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Address columns */
        .address-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 768px) {
            .address-row {
                grid-template-columns: 1fr;
            }
        }

        /* Phone input styling */
        .iti {
            width: 100%;
        }

        .iti--separate-dial-code {
            position: relative;
        }

        /* Map section */
        #organizationMap {
            height: 400px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            margin-top: 15px;
        }

        .map-controls {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .map-coords {
            background: #f1f5f9;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: 'Courier New', Courier, monospace;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 250px;
        }

        .map-coords strong {
            color: var(--primary-color);
        }

        /* Parent organization search */
        .parent-org-search {
            position: relative;
        }

        .parent-org-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .parent-org-results.show {
            display: block;
        }

        .parent-org-item {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .parent-org-item:hover {
            background-color: #f1f5f9;
        }

        .parent-org-item-name {
            font-weight: 600;
            color: var(--primary-color);
        }

        .parent-org-item-type {
            font-size: 0.85rem;
            color: var(--secondary-color);
        }

        /* Sectors multi-select */
        .sectors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .sector-checkbox {
            position: relative;
        }

        .sector-checkbox input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .sector-checkbox label {
            display: block;
            padding: 12px 15px;
            background: #f1f5f9;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 0;
            font-weight: 500;
            color: #475569;
        }

        .sector-checkbox input[type="checkbox"]:checked + label {
            background: rgba(37, 99, 235, 0.1);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        /* Action buttons */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #e2e8f0;
        }

        .btn {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            color: white;
            flex: 1;
            justify-content: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            background: white;
            color: var(--secondary-color);
            border: 1.5px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: var(--secondary-color);
            color: var(--secondary-color);
            text-decoration: none;
        }

        .btn-sm-add {
            background: #f1f5f9;
            color: var(--primary-color);
            padding: 8px 15px;
            font-size: 0.85rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 12px;
        }

        .btn-sm-add:hover {
            background: #e2e8f0;
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        /* Tabs for organization types */
        .org-type-tabs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .type-tab {
            position: relative;
        }

        .type-tab input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .type-tab label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            color: #475569;
            transition: all 0.3s ease;
            min-height: 60px;
        }

        .type-tab input[type="radio"]:checked + label {
            background: rgba(37, 99, 235, 0.1);
            border-color: var(--primary-color);
            color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        /* Loading state */
        .spinner-loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .spinner-loading.show {
            display: flex;
        }

        /* Helper text with icons */
        .info-box {
            background: rgba(37, 99, 235, 0.05);
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .info-box i {
            color: var(--primary-color);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-box p {
            margin: 0;
            color: #064e3b;
            font-size: 0.9rem;
        }

        /* Step counter */
        .step-indicator {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .step.active .step-number {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .step.completed .step-number {
            background: var(--success-color);
            color: white;
            border-color: var(--success-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }

            .pages-title {
                font-size: 0.8rem;
            }

            .form-card-body {
                padding: 15px;
            }

            .org-type-tabs {
                grid-template-columns: 1fr;
            }

            .sectors-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
        }

        /* Validation feedback animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .invalid-feedback {
            animation: slideIn 0.2s ease;
        }
    </style>
</head>
<body>
    <div class="container-wrapper">
        <!-- Page Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-building"></i>
                Create New Organization
            </h1>
            <p>Fill in the details to register your organization on Automoni</p>
        </div>

        <!-- Main Form -->
        <form method="POST" enctype="multipart/form-data" id="organizationForm" class="needs-validation" novalidate>
            <?= csrf_field() ?>

            <!-- Step 1: Basic Information -->
            <div class="form-card" data-step="1">
                <div class="form-card-header">
                    <i class="fas fa-info-circle"></i>
                    <h5>Step 1: Basic Information</h5>
                </div>
                <div class="form-card-body">
                    <div class="info-box">
                        <i class="fas fa-lightbulb"></i>
                        <p>Provide the fundamental details about your organization</p>
                    </div>

                    <!-- Organization Type -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-building"></i>
                            Organization Type <span class="required-badge">*</span>
                        </label>
                        <div class="org-type-tabs">
                            <?php $types = [
                                ['id' => 1, 'name' => 'Company', 'icon' => 'fa-industry'],
                                ['id' => 2, 'name' => 'NGO', 'icon' => 'fa-handshake'],
                                ['id' => 3, 'name' => 'Association', 'icon' => 'fa-people-group'],
                                ['id' => 4, 'name' => 'Government', 'icon' => 'fa-landmark'],
                            ];
                            foreach ($types as $type):
                            ?>
                                <div class="type-tab">
                                    <input type="radio" name="type_id" id="type_<?= $type['id'] ?>" 
                                           value="<?= $type['id'] ?>" required>
                                    <label for="type_<?= $type['id'] ?>">
                                        <i class="fas <?= $type['icon'] ?>" style="margin-right: 6px;"></i>
                                        <?= $type['name'] ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="invalid-feedback">Please select an organization type</div>
                    </div>

                    <!-- Organization Name -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-heading"></i>
                                    Organization Name <span class="required-badge">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control" required
                                       placeholder="e.g., TechCorp International"
                                       minlength="3" maxlength="255">
                                <div class="form-text">
                                    <i class="fas fa-comment-dots"></i>
                                    Min 3, Max 255 characters
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="legal_name" class="form-label">
                                    <i class="fas fa-file-contract"></i>
                                    Legal Name
                                </label>
                                <input type="text" name="legal_name" id="legal_name" class="form-control"
                                       placeholder="Official legal name (if different)">
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Description
                        </label>
                        <textarea name="description" id="description" class="form-control"
                                  placeholder="Brief description of your organization..."
                                  maxlength="1000"></textarea>
                        <div class="form-text">Max 1000 characters</div>
                    </div>

                    <!-- Parent Organization -->
                    <div class="form-group parent-org-search">
                        <label for="parent_id" class="form-label">
                            <i class="fas fa-sitemap"></i>
                            Parent Organization (if subsidiary)
                        </label>
                        <input type="text" id="parent_search" class="form-control" 
                               placeholder="Search for parent organization...">
                        <input type="hidden" name="parent_id" id="parent_id" value="">
                        <div id="parent_org_results" class="parent-org-results"></div>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Search and select if this organization is a subsidiary
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Contact Information -->
            <div class="form-card" data-step="2">
                <div class="form-card-header">
                    <i class="fas fa-phone"></i>
                    <h5>Step 2: Contact Information</h5>
                </div>
                <div class="form-card-body">
                    <div class="info-box">
                        <i class="fas fa-shield-alt"></i>
                        <p>All contact information is required and will be verified</p>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email <span class="required-badge">*</span>
                        </label>
                        <input type="email" name="email" id="email" class="form-control" required
                               placeholder="contact@organization.com">
                        <div class="invalid-feedback">Please provide a valid email</div>
                    </div>

                    <!-- Phone Number with Country Code -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="phone_number" class="form-label">
                                    <i class="fas fa-phone-alt"></i>
                                    Phone Number <span class="required-badge">*</span>
                                </label>
                                <input type="tel" name="phone_number" id="phone_number" class="form-control" 
                                       required placeholder="Enter phone number">
                                <input type="hidden" name="phone_country_code" id="phone_country_code">
                                <div class="invalid-feedback">Please provide a valid phone number</div>
                            </div>
                        </div>
                    </div>

                    <!-- Website -->
                    <div class="form-group">
                        <label for="website" class="form-label">
                            <i class="fas fa-globe"></i>
                            Website <span class="required-badge">*</span>
                        </label>
                        <input type="url" name="website" id="website" class="form-control" required
                               placeholder="https://www.example.com">
                        <div class="invalid-feedback">Please provide a valid website URL</div>
                    </div>

                    <!-- Tax ID -->
                    <div class="form-group">
                        <label for="tax_id" class="form-label">
                            <i class="fas fa-landmark"></i>
                            Tax ID / Registration Number
                        </label>
                        <input type="text" name="tax_id" id="tax_id" class="form-control"
                               placeholder="e.g., VAT or Company Registration Number">
                    </div>
                </div>
            </div>

            <!-- Step 3: Address & Location -->
            <div class="form-card" data-step="3">
                <div class="form-card-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h5>Step 3: Address & Location</h5>
                </div>
                <div class="form-card-body">
                    <div class="info-box">
                        <i class="fas fa-map-pin"></i>
                        <p>Precise location helps with organization discovery. Click on the map to set coordinates.</p>
                    </div>

                    <!-- Street Address -->
                    <div class="form-group">
                        <label for="street_address" class="form-label">
                            <i class="fas fa-road"></i>
                            Street Address <span class="required-badge">*</span>
                        </label>
                        <input type="text" name="street_address" id="street_address" class="form-control" required
                               placeholder="123 Business Street">
                        <div class="invalid-feedback">Street address is required</div>
                    </div>

                    <!-- Address breakdown: City, Postal Code, Country -->
                    <div class="address-row">
                        <div class="form-group">
                            <label for="city" class="form-label">
                                <i class="fas fa-city"></i>
                                City <span class="required-badge">*</span>
                            </label>
                            <input type="text" name="city" id="city" class="form-control" required
                                   placeholder="e.g., Paris">
                            <div class="invalid-feedback">City is required</div>
                        </div>

                        <div class="form-group">
                            <label for="postal_code" class="form-label">
                                <i class="fas fa-mailbox"></i>
                                Postal Code <span class="required-badge">*</span>
                            </label>
                            <input type="text" name="postal_code" id="postal_code" class="form-control" required
                                   placeholder="75001">
                            <div class="invalid-feedback">Postal code is required</div>
                        </div>

                        <div class="form-group">
                            <label for="country_code" class="form-label">
                                <i class="fas fa-globe-americas"></i>
                                Country <span class="required-badge">*</span>
                            </label>
                            <select name="country_code" id="country_code" class="form-select" required>
                                <option value="">-- Select Country --</option>
                                <option value="FR">France (FR)</option>
                                <option value="DZ">Algeria (DZ)</option>
                                <option value="MA">Morocco (MA)</option>
                                <option value="TN">Tunisia (TN)</option>
                                <option value="GB">United Kingdom (GB)</option>
                                <option value="US">United States (US)</option>
                                <option value="CA">Canada (CA)</option>
                                <option value="DE">Germany (DE)</option>
                                <option value="ES">Spain (ES)</option>
                                <option value="IT">Italy (IT)</option>
                                <option value="CH">Switzerland (CH)</option>
                                <option value="BE">Belgium (BE)</option>
                                <option value="NL">Netherlands (NL)</option>
                                <option value="AU">Australia (AU)</option>
                                <option value="AE">UAE (AE)</option>
                                <option value="SA">Saudi Arabia (SA)</option>
                                <option value="SG">Singapore (SG)</option>
                                <option value="JP">Japan (JP)</option>
                                <option value="CN">China (CN)</option>
                                <option value="IN">India (IN)</option>
                                <option value="BR">Brazil (BR)</option>
                                <option value="MX">Mexico (MX)</option>
                                <!-- Add more countries as needed -->
                            </select>
                            <div class="invalid-feedback">Country is required</div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map"></i>
                            Location on Map
                        </label>
                        <div id="organizationMap"></div>
                        <div class="map-controls">
                            <button type="button" id="useCurrentLocation" class="btn btn-sm-add">
                                <i class="fas fa-location-arrow"></i> Use Current Location
                            </button>
                            <button type="button" id="centerMap" class="btn btn-sm-add">
                                <i class="fas fa-compress"></i> Center Map
                            </button>
                        </div>
                        <div class="map-coords">
                            <span><strong>Latitude:</strong> <span id="latDisplay">0.0000</span></span>
                            <span><strong>Longitude:</strong> <span id="lonDisplay">0.0000</span></span>
                        </div>
                    </div>

                    <!-- Hidden inputs for coordinates -->
                    <input type="hidden" name="latitude" id="latitude" value="0">
                    <input type="hidden" name="longitude" id="longitude" value="0">
                </div>
            </div>

            <!-- Step 4: Business Details -->
            <div class="form-card" data-step="4">
                <div class="form-card-header">
                    <i class="fas fa-chart-line"></i>
                    <h5>Step 4: Business Details</h5>
                </div>
                <div class="form-card-body">
                    <div class="info-box">
                        <i class="fas fa-briefcase"></i>
                        <p>Tell us more about your organization's operations</p>
                    </div>

                    <!-- Industry/Sectors -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-industry"></i>
                            Business Sectors
                        </label>
                        <div class="sectors-grid">
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_technology" value="technology">
                                <label for="sector_technology">Technology</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_finance" value="finance">
                                <label for="sector_finance">Finance & Banking</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_healthcare" value="healthcare">
                                <label for="sector_healthcare">Healthcare</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_manufacturing" value="manufacturing">
                                <label for="sector_manufacturing">Manufacturing</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_retail" value="retail">
                                <label for="sector_retail">Retail & E-Commerce</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_real_estate" value="real_estate">
                                <label for="sector_real_estate">Real Estate</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_energy" value="energy">
                                <label for="sector_energy">Energy</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_transportation" value="transportation">
                                <label for="sector_transportation">Transportation</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_education" value="education">
                                <label for="sector_education">Education</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_media" value="media">
                                <label for="sector_media">Media & Entertainment</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_hospitality" value="hospitality">
                                <label for="sector_hospitality">Hospitality & Tourism</label>
                            </div>
                            <div class="sector-checkbox">
                                <input type="checkbox" name="sectors[]" id="sector_nonprofit" value="nonprofit">
                                <label for="sector_nonprofit">Non-Profit Organizations</label>
                            </div>
                        </div>
                    </div>

                    <!-- Founded Date -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="founded_at" class="form-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Founded Date
                                </label>
                                <input type="date" name="founded_at" id="founded_at" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_count" class="form-label">
                                    <i class="fas fa-users"></i>
                                    Number of Employees
                                </label>
                                <input type="number" name="employee_count" id="employee_count" class="form-control"
                                       min="0" placeholder="e.g., 250">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="/organizations" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i>
                    Create Organization
                </button>
            </div>
        </form>
    </div>

    <!-- Loading Spinner -->
    <div class="spinner-loading" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@21.0.0/build/js/intlTelInput.min.js"></script>
    
    <script>
        // Initialize international phone input
        const phoneInput = document.querySelector("#phone_number");
        const telInput = window.intlTelInput(phoneInput, {
            initialCountry: "fr",
            preferredCountries: ["fr", "dz", "ma", "tn", "gb", "us"],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@21.0.0/build/js/utils.js"
        });

        phoneInput.addEventListener("change", function() {
            if (phoneInput.value) {
                document.getElementById("phone_country_code").value = telInput.getSelectedCountryData().dialCode;
            }
        });

        // Initialize Map
        let organizationMap = L.map('organizationMap').setView([48.8566, 2.3522], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(organizationMap);

        let marker = L.marker([48.8566, 2.3522]).addTo(organizationMap)
            .bindPopup('Organization Location').openPopup();

        // Update coordinates on map click
        organizationMap.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(4);
            const lng = e.latlng.lng.toFixed(4);
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('latDisplay').textContent = lat;
            document.getElementById('lonDisplay').textContent = lng;

            if (marker) {
                organizationMap.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(organizationMap)
                .bindPopup('Location Set').openPopup();
        });

        // Current Location Button
        document.getElementById('useCurrentLocation')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude.toFixed(4);
                    const lng = position.coords.longitude.toFixed(4);
                    
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('latDisplay').textContent = lat;
                    document.getElementById('lonDisplay').textContent = lng;

                    organizationMap.setView([lat, lng], 13);
                    
                    if (marker) {
                        organizationMap.removeLayer(marker);
                    }
                    marker = L.marker([lat, lng]).addTo(organizationMap)
                        .bindPopup('Current Location').openPopup();
                });
            } else {
                alert('Geolocation is not supported by your browser');
            }
        });

        // Center Map Button
        document.getElementById('centerMap')?.addEventListener('click', function(e) {
            e.preventDefault();
            const lat = parseFloat(document.getElementById('latitude').value) || 48.8566;
            const lng = parseFloat(document.getElementById('longitude').value) || 2.3522;
            organizationMap.setView([lat, lng], 13);
        });

        // Parent Organization Search
        const parentSearch = document.getElementById('parent_search');
        const parentResults = document.getElementById('parent_org_results');
        const parentIdInput = document.getElementById('parent_id');

        parentSearch?.addEventListener('input', async function(e) {
            const query = e.target.value;
            
            if (query.length < 2) {
                parentResults.classList.remove('show');
                return;
            }

            try {
                const response = await fetch(`/api/organizations/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data.length > 0) {
                    parentResults.innerHTML = data.map(org => `
                        <div class="parent-org-item" data-org-id="${org.id}">
                            <div class="parent-org-item-name">${org.name}</div>
                            <div class="parent-org-item-type">${org.type_name}</div>
                        </div>
                    `).join('');
                    parentResults.classList.add('show');

                    // Add click listeners
                    document.querySelectorAll('.parent-org-item').forEach(item => {
                        item.addEventListener('click', function() {
                            parentIdInput.value = this.dataset.orgId;
                            parentSearch.value = this.querySelector('.parent-org-item-name').textContent;
                            parentResults.classList.remove('show');
                        });
                    });
                } else {
                    parentResults.innerHTML = '<div class="parent-org-item">No organizations found</div>';
                    parentResults.classList.add('show');
                }
            } catch (error) {
                console.error('Search error:', error);
            }
        });

        // Form Validation
        document.getElementById('organizationForm')?.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                document.getElementById('loadingSpinner').classList.add('show');
            }
            this.classList.add('was-validated');
        });

        // Hide parent results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.parent-org-search')) {
                parentResults.classList.remove('show');
            }
        });
    </script>
</body>
</html>
