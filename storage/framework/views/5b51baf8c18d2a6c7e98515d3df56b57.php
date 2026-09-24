<?php $__env->startSection('title', 'AI Copilot & Automation Command Center - ' . ($salon->name ?? 'Salon')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .ai-hero-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        border-radius: 20px;
        padding: 24px 30px;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(49, 46, 129, 0.15);
    }
    .nav-tabs-ai {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }
    .nav-tabs-ai .nav-link {
        color: #475569;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 12px 20px;
        border-radius: 12px 12px 0 0;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .nav-tabs-ai .nav-link:hover {
        color: #4f46e5;
        background: rgba(99, 102, 241, 0.05);
    }
    .nav-tabs-ai .nav-link.active {
        color: #4f46e5;
        background: #ffffff;
        border-bottom-color: #4f46e5;
    }
    .ai-copilot-card {
        background: #0f172a;
        color: #ffffff;
        border-radius: 20px;
        border: 1px solid #1e293b;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
    }
    .chat-box {
        height: 440px;
        overflow-y: auto;
        padding: 20px;
        background: rgba(15, 23, 42, 0.85);
        border-radius: 14px;
        border: 1px solid #1e293b;
    }
    .chat-box::-webkit-scrollbar {
        width: 6px;
    }
    .chat-box::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 10px;
    }
    .msg-user {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        border-radius: 16px 16px 4px 16px;
        padding: 14px 20px;
        max-width: 82%;
        margin-left: auto;
        margin-bottom: 16px;
        font-size: 0.94rem;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        line-height: 1.5;
    }
    .msg-ai {
        background: #1e293b;
        color: #f8fafc;
        border-radius: 16px 16px 16px 4px;
        padding: 18px 22px;
        max-width: 90%;
        margin-right: auto;
        margin-bottom: 16px;
        border: 1px solid #334155;
        font-size: 0.94rem;
        line-height: 1.65;
        box-shadow: 0 6px 18px rgba(0,0,0,0.3);
    }
    .action-btn-pill {
        background: rgba(99, 102, 241, 0.14);
        color: #c7d2fe;
        border: 1px solid rgba(99, 102, 241, 0.3);
        padding: 7px 15px;
        border-radius: 30px;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .action-btn-pill:hover {
        background: #4f46e5;
        color: #ffffff;
        border-color: #4f46e5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }
    .auto-workflow-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        background: #ffffff;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .auto-workflow-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }
    .badge-ai-active {
        background: rgba(16, 185, 129, 0.18);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.35);
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 30px;
        font-size: 0.78rem;
    }
    #copilotInput {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border: 1px solid #334155 !important;
        font-size: 0.96rem !important;
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }
    #copilotInput:focus {
        background-color: #1e293b !important;
        color: #ffffff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
    }
    .send-btn-gradient {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border: none;
        color: white;
        font-weight: 600;
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
        padding-left: 24px;
        padding-right: 24px;
    }
    .consultation-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
    }
    .preview-card {
        background: #0f172a;
        color: #ffffff;
        border-radius: 16px;
        padding: 20px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Hero Header Banner -->
    <div class="ai-hero-header mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-brain text-warning"></i> AI Command Center & Features Engine
                <span class="badge bg-warning text-dark ms-2" style="font-size: 0.72rem; vertical-align: middle;">PRO AI</span>
            </h1>
            <p class="mb-0 text-indigo-200" style="font-size: 0.95rem; opacity: 0.9;">
                Autonomous AI reasoning, visual consultations, predictive analytics & marketing automations for <strong><?php echo e($salon->name); ?></strong>
            </p>
        </div>
        <div>
            <a href="<?php echo e(route('admin.reports.ai-insights', ['salon_slug' => $salon->slug])); ?>" class="btn btn-light rounded-pill px-4 fw-semibold text-indigo-900 shadow-sm">
                <i class="fas fa-chart-line text-indigo-600 me-2"></i>AI Forecast Report
            </a>
        </div>
    </div>

    <!-- Multi-Tab Navigation -->
    <ul class="nav nav-tabs nav-tabs-ai mb-4" id="aiTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="copilot-tab" data-bs-toggle="tab" data-bs-target="#copilot-pane" type="button" role="tab"><i class="fas fa-robot me-2 text-indigo-500"></i>AI Business Copilot</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="consultation-tab" data-bs-toggle="tab" data-bs-target="#consultation-pane" type="button" role="tab"><i class="fas fa-scissors me-2 text-pink-500"></i>Hair & Beauty Consultations</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="predictive-tab" data-bs-toggle="tab" data-bs-target="#predictive-pane" type="button" role="tab"><i class="fas fa-chart-line me-2 text-blue-500"></i>Predictive Operations</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="marketing-tab" data-bs-toggle="tab" data-bs-target="#marketing-pane" type="button" role="tab"><i class="fas fa-bullhorn me-2 text-amber-500"></i>Marketing & Growth</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="assistant-tab" data-bs-toggle="tab" data-bs-target="#assistant-pane" type="button" role="tab"><i class="fab fa-whatsapp me-2 text-emerald-500"></i>WhatsApp Assistant</button>
        </li>
    </ul>

    <div class="tab-content" id="aiTabContent">
        <!-- TAB 1: AI BUSINESS COPILOT -->
        <div class="tab-pane fade show active" id="copilot-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card ai-copilot-card h-100 border-0">
                        <div class="card-header bg-transparent border-bottom border-slate-800 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-indigo-600 text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center shadow" style="width: 44px; height: 44px; background: #4f46e5;">
                                    <i class="fas fa-robot fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-white">Salon AI Business Copilot</h5>
                                    <small class="text-slate-400">Ask natural questions: "Why was revenue lower this month?"</small>
                                </div>
                            </div>
                            <span class="badge-ai-active"><i class="fas fa-circle me-1 small"></i> System Active</span>
                        </div>

                        <div class="card-body px-4 py-4 d-flex flex-column">
                            <div id="chatBox" class="chat-box mb-3 flex-grow-1">
                                <div class="msg-ai">
                                    <div class="fw-bold text-white mb-2"><i class="fas fa-robot text-indigo-400 me-2" style="color: #818cf8;"></i>Salon Copilot Assistant</div>
                                    <div>Hello! I am your AI Salon Copilot. Ask me anything or select a quick query:</div>
                                    <div class="mt-3 d-flex flex-wrap gap-2">
                                        <button type="button" class="action-btn-pill" onclick="sendQuickQuery('Why was revenue lower this month?')">
                                            <i class="fas fa-search-dollar me-1"></i> Root Cause: Revenue Drop?
                                        </button>
                                        <button type="button" class="action-btn-pill" onclick="sendQuickQuery('Who is my top performing staff?')">
                                            <i class="fas fa-award me-1"></i> Top Staff Leaderboard
                                        </button>
                                        <button type="button" class="action-btn-pill" onclick="sendQuickQuery('How much revenue did we make today?')">
                                            <i class="fas fa-wallet me-1"></i> Revenue MTD
                                        </button>
                                        <button type="button" class="action-btn-pill" onclick="sendQuickQuery('Which items are low in stock?')">
                                            <i class="fas fa-boxes me-1"></i> Stock Alerts
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <form id="copilotForm" onsubmit="handleCopilotSubmit(event)" class="mt-auto">
                                <div class="input-group">
                                    <input type="text" id="copilotInput" class="form-control" placeholder="Ask AI Copilot e.g. Why was revenue lower this month?..." autocomplete="off">
                                    <button class="btn send-btn-gradient" type="submit" id="sendBtn">
                                        <i class="fas fa-paper-plane me-1"></i> Ask AI
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-bolt text-warning me-2"></i>1-Click Autonomous Actions</h5>
                        <p class="text-muted small mb-4">Trigger instant AI interventions based on real database analytics.</p>
                        
                        <div class="d-flex flex-column gap-3">
                            <div class="auto-workflow-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Run Churn Re-Engagement</h6>
                                        <small class="text-muted">Target 90+ day inactive clients with 20% off offer.</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="triggerAutomation('run_churn_campaign')">Execute</button>
                                </div>
                            </div>

                            <div class="auto-workflow-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Generate Inventory PO</h6>
                                        <small class="text-muted">Create draft purchase order for stockouts.</small>
                                    </div>
                                    <button class="btn btn-outline-success btn-sm rounded-pill" onclick="triggerAutomation('generate_po')">Draft PO</button>
                                </div>
                            </div>

                            <div class="auto-workflow-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Trigger No-Show Confirmations</h6>
                                        <small class="text-muted">Send WhatsApp verifications to high-risk bookings.</small>
                                    </div>
                                    <button class="btn btn-outline-warning text-dark btn-sm rounded-pill" onclick="triggerAutomation('trigger_noshow_confirmations')">Send Confirmations</button>
                                </div>
                            </div>

                            <div class="auto-workflow-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Activate Off-Peak Discount</h6>
                                        <small class="text-muted">Fill slow 9 AM - 11 AM weekday slots with 15% discount.</small>
                                    </div>
                                    <button class="btn btn-outline-info btn-sm rounded-pill" onclick="triggerAutomation('run_offpeak_discount')">Activate</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: AI VISUAL CONSULTATIONS -->
        <div class="tab-pane fade" id="consultation-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="consultation-box shadow-sm">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-scissors text-pink-500 me-2"></i>AI Hair & Beauty Consultation Engine</h5>
                        <p class="text-muted small">Face shape analysis, haircut recommendation, hair color matching & skincare consultation.</p>
                        
                        <form id="consultationForm" onsubmit="runAiConsultation(event)">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Consultation Type</label>
                                    <select class="form-select" id="consultType" onchange="toggleConsultFields()">
                                        <option value="hair">Hairstyle & Cut Recommendation</option>
                                        <option value="hair_color">Hair Color & Shade Recommendation</option>
                                        <option value="skin_beauty">Skin & Beauty Consultation</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Face Shape / Skin Type</label>
                                    <select class="form-select" id="faceShapeInput">
                                        <option value="oval">Oval (Versatile)</option>
                                        <option value="round">Round (Soft Angles)</option>
                                        <option value="square">Square (Defined Jawline)</option>
                                        <option value="heart">Heart (Wider Forehead)</option>
                                        <option value="diamond">Diamond (High Cheekbones)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Occasion / Event</label>
                                    <select class="form-select" id="occasionInput">
                                        <option value="daily">Daily Casual Wear</option>
                                        <option value="wedding">Wedding / Bridal</option>
                                        <option value="party">Party / Gala Evening</option>
                                        <option value="corporate">Corporate / Executive</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Primary Skin Concern / Goal</label>
                                    <select class="form-select" id="concernInput">
                                        <option value="dullness">Dullness & Radiance Glow</option>
                                        <option value="dehydration">Dehydration & Dryness</option>
                                        <option value="pigmentation">Pigmentation & Dark Spots</option>
                                        <option value="acne_prone">Acne-Prone / Breakouts</option>
                                        <option value="severe_acne">Severe Inflammation (Dermatologist Check)</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-pink text-white w-100 rounded-pill fw-semibold py-2" style="background: linear-gradient(135deg, #ec4899, #d946ef);">
                                <i class="fas fa-wand-magic-sparkles me-2"></i>Generate AI Recommendations
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="preview-card h-100 shadow-sm d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold text-white mb-3"><i class="fas fa-sparkles text-warning me-2"></i>AI Recommendations & Service Match</h5>
                            <div id="consultationResultBox">
                                <div class="text-center text-slate-400 py-5">
                                    <i class="fas fa-user-sparkles fa-3x mb-3 opacity-50"></i>
                                    <p>Select consultation parameters and click <strong>Generate AI Recommendations</strong> to receive instant recommendations.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top border-slate-800 text-slate-400 small d-flex justify-content-between">
                            <span><i class="fas fa-shield-alt text-emerald-400 me-1"></i> Medical Disclaimer Active</span>
                            <span>Real Database Matching</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: PREDICTIVE OPERATIONS -->
        <div class="tab-pane fade" id="predictive-pane" role="tabpanel">
            <div class="row g-4">
                <!-- No Show Prediction -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-user-slash text-danger me-2"></i>AI No-Show Risk Detector</h6>
                        </div>
                        <p class="text-muted small">Evaluates booking lead times & client history to prevent empty slots.</p>
                        
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Total Analyzed:</span>
                                <strong><?php echo e($aiData['no_show_risk']['summary']['total_analyzed'] ?? 0); ?> bookings</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>High Risk Bookings:</span>
                                <strong class="text-danger"><?php echo e($aiData['no_show_risk']['summary']['high_risk_count'] ?? 0); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Est. Loss Reduction:</span>
                                <strong class="text-success"><?php echo e($aiData['no_show_risk']['summary']['no_show_rate_reduction_estimate'] ?? '38%'); ?></strong>
                            </div>
                        </div>

                        <button class="btn btn-outline-danger w-100 rounded-pill btn-sm fw-semibold" onclick="triggerAutomation('trigger_noshow_confirmations')">
                            <i class="fab fa-whatsapp me-1"></i> Send WhatsApp Verifications
                        </button>
                    </div>
                </div>

                <!-- Seasonal & Event Demand Surges -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i>AI Demand Surge Predictor</h6>
                        </div>
                        <p class="text-muted small">Predicts upcoming festive, wedding & holiday service spikes.</p>
                        
                        <div class="d-flex flex-column gap-2 mb-3">
                            <?php $__currentLoopData = $aiData['demand_surges']['predicted_surges'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $surge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="border rounded p-2 bg-slate-50">
                                    <div class="fw-bold text-dark small"><?php echo e($surge['category']); ?></div>
                                    <div class="text-success small font-weight-bold"><?php echo e($surge['surge_percentage']); ?></div>
                                    <small class="text-muted"><?php echo e($surge['reason']); ?></small>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                <!-- Inventory Reorder Prediction -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-boxes text-info me-2"></i>AI Inventory Depletion</h6>
                        </div>
                        <p class="text-muted small">Calculates product depletion rates & auto-reorders.</p>
                        
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Low Stock Items:</span>
                                <strong class="text-warning"><?php echo e(count($aiData['inventory_forecast'] ?? [])); ?> items</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Reorder Recommendation:</span>
                                <strong class="text-dark">Auto Purchase Order</strong>
                            </div>
                        </div>

                        <button class="btn btn-outline-info w-100 rounded-pill btn-sm fw-semibold" onclick="triggerAutomation('generate_po')">
                            <i class="fas fa-file-invoice me-1"></i> Generate Draft PO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: MARKETING & GROWTH -->
        <div class="tab-pane fade" id="marketing-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-bullhorn text-amber-500 me-2"></i>AI Multi-Channel Campaign Generator</h5>
                        <p class="text-muted small">Automatically generates high-converting WhatsApp, SMS, Email & Instagram copy.</p>
                        
                        <form id="marketingForm" onsubmit="generateCampaign(event)">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Campaign Theme</label>
                                    <select class="form-select" id="campaignType">
                                        <option value="festival">Festive & Holiday Special</option>
                                        <option value="birthday">Birthday & Anniversary Offer</option>
                                        <option value="winback">Customer Winback (Inactive 90 Days)</option>
                                        <option value="weekend">Weekend Pampering Refresh</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Discount / Offer Value</label>
                                    <input type="text" class="form-control" id="discountInput" value="20% OFF">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-amber text-dark fw-semibold w-100 rounded-pill py-2" style="background-color: #f59e0b;">
                                <i class="fas fa-sparkles me-2"></i>Generate Campaign Copy
                            </button>
                        </form>

                        <div id="campaignResultBox" class="mt-4 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0"><i class="fab fa-whatsapp text-success me-1"></i> Generated WhatsApp Copy:</h6>
                                <span class="badge bg-indigo-600 text-white rounded-pill" id="variationBadge">VAR-1</span>
                            </div>
                            <div class="p-3 bg-light rounded border text-secondary small mb-3" id="whatsappCopy"></div>

                            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-envelope text-primary me-1"></i> Generated Email Newsletter Copy:</h6>
                            <div class="p-3 bg-light rounded border text-secondary small mb-3">
                                <div><strong>Subject:</strong> <span id="emailSubjectCopy"></span></div>
                                <hr class="my-2">
                                <div id="emailBodyCopy"></div>
                            </div>

                            <h6 class="fw-bold text-dark mb-2"><i class="fab fa-instagram text-danger me-1"></i> Generated Instagram Caption:</h6>
                            <div class="p-3 bg-light rounded border text-secondary small" id="instaCopy"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-users-cog text-indigo-600 me-2"></i>AI RFM Customer Segmentation</h5>
                        <p class="text-muted small">Dynamically classifies client database into targeted spending cohorts.</p>
                        
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-indigo-50 text-indigo-900 rounded-3">
                                    <h4 class="fw-bold mb-0 text-indigo-600"><?php echo e($aiData['customer_segmentation']['segments']['vip_champions'] ?? 0); ?></h4>
                                    <small class="fw-semibold">VIP Champions</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-emerald-50 text-emerald-900 rounded-3">
                                    <h4 class="fw-bold mb-0 text-emerald-600"><?php echo e($aiData['customer_segmentation']['segments']['regular_loyalists'] ?? 0); ?></h4>
                                    <small class="fw-semibold">Regulars</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-blue-50 text-blue-900 rounded-3">
                                    <h4 class="fw-bold mb-0 text-blue-600"><?php echo e($aiData['customer_segmentation']['segments']['new_clients'] ?? 0); ?></h4>
                                    <small class="fw-semibold">New Clients</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-rose-50 text-rose-900 rounded-3">
                                    <h4 class="fw-bold mb-0 text-rose-600"><?php echo e($aiData['customer_segmentation']['segments']['at_risk_inactive'] ?? 0); ?></h4>
                                    <small class="fw-semibold">At-Risk</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Birthday & Anniversary Milestone Tracker -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-gift text-pink-500 me-2"></i>Upcoming Birthday Offers</h5>
                        </div>
                        <p class="text-muted small mb-3">Automated detection of client birthdays & anniversary dates in next 7 days.</p>
                        
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3">
                            <div>
                                <span class="fw-bold text-dark">Upcoming Milestones This Week:</span>
                                <div class="text-muted small"><?php echo e($aiData['milestones']['total_upcoming_milestones'] ?? 0); ?> client celebrations</div>
                            </div>
                            <button class="btn btn-outline-pink btn-sm rounded-pill" onclick="triggerAutomation('send_milestone_offers')">
                                Dispatch Offers
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: WHATSAPP ASSISTANT -->
        <div class="tab-pane fade" id="assistant-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3"><i class="fab fa-whatsapp text-emerald-500 me-2"></i>24/7 AI WhatsApp & Booking Assistant</h5>
                        <p class="text-muted small">Automated answers for salon hours, pricing, availability, and guided customer bookings.</p>
                        
                        <div class="chat-box mb-3 bg-light border text-dark" id="assistantChatBox" style="height: 380px;">
                            <div class="msg-ai bg-emerald-900 text-white">
                                <div class="fw-bold mb-1"><i class="fab fa-whatsapp me-2 text-emerald-400"></i>Salon WhatsApp Bot</div>
                                <div>Hello! Ask me anything: "What are your opening hours?", "How much is hair coloring?", or "I need a haircut tomorrow evening".</div>
                            </div>
                        </div>

                        <form onsubmit="handleAssistantSubmit(event)">
                            <div class="input-group">
                                <input type="text" id="assistantInput" class="form-control" placeholder="Type WhatsApp message e.g. What are your opening hours?..." autocomplete="off">
                                <button class="btn btn-success fw-semibold px-4" type="submit">
                                    <i class="fab fa-whatsapp me-1"></i> Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-sliders-h text-indigo-600 me-2"></i>WhatsApp FAQ Configuration</h5>
                        <p class="text-muted small">Connected rules automatically synced with tenant database settings.</p>
                        
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong class="d-block text-dark">Opening Hours & Location</strong>
                                    <span class="text-muted">Auto-answers address, phone & operating hours</span>
                                </div>
                                <span class="badge bg-success">Active</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong class="d-block text-dark">Service Rates & Price List</strong>
                                    <span class="text-muted">Queries live database service rates</span>
                                </div>
                                <span class="badge bg-success">Active</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong class="d-block text-dark">Stylist Availability</strong>
                                    <span class="text-muted">Real-time schedule check</span>
                                </div>
                                <span class="badge bg-success">Active</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong class="d-block text-dark">Cancellation Policy</strong>
                                    <span class="text-muted">Explains 2-hour rescheduling terms</span>
                                </div>
                                <span class="badge bg-success">Active</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const salonSlug = "<?php echo e($salon->slug); ?>";
    const csrfToken = "<?php echo e(csrf_token()); ?>";

    const copilotUrl = "<?php echo e(route('admin.ai.copilot', ['salon_slug' => $salon->slug])); ?>";
    const automationUrl = "<?php echo e(route('admin.ai.automation', ['salon_slug' => $salon->slug])); ?>";
    const consultationUrl = "<?php echo e(route('admin.ai.consultation', ['salon_slug' => $salon->slug])); ?>";
    const marketingUrl = "<?php echo e(route('admin.ai.marketing.generate', ['salon_slug' => $salon->slug])); ?>";
    const assistantUrl = "<?php echo e(route('admin.ai.assistant.chat', ['salon_slug' => $salon->slug])); ?>";

    function sendQuickQuery(queryText) {
        document.getElementById('copilotInput').value = queryText;
        document.getElementById('copilotForm').dispatchEvent(new Event('submit'));
    }

    async function handleCopilotSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('copilotInput');
        const query = input.value.trim();
        if (!query) return;

        const chatBox = document.getElementById('chatBox');
        chatBox.innerHTML += `<div class="msg-user"><div>${escapeHtml(query)}</div></div>`;
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        const loadingId = 'loading-' + Date.now();
        chatBox.innerHTML += `<div class="msg-ai" id="${loadingId}"><div><i class="fas fa-circle-notch fa-spin text-indigo-400 me-2"></i>AI Copilot analyzing salon database...</div></div>`;
        chatBox.scrollTop = chatBox.scrollHeight;

        try {
            const res = await fetch(copilotUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ message: query })
            });
            const data = await res.json();
            const loadingElem = document.getElementById(loadingId);
            
            let html = `<div>${formatMarkdown(data.response || 'No response returned.')}</div>`;
            if (data.suggested_actions && data.suggested_actions.length > 0) {
                html += `<div class="mt-3 d-flex flex-wrap gap-2">`;
                data.suggested_actions.forEach(act => {
                    html += `<button class="action-btn-pill" onclick="triggerAutomation('${act.action}')">${escapeHtml(act.label)}</button>`;
                });
                html += `</div>`;
            }
            loadingElem.innerHTML = html;
            chatBox.scrollTop = chatBox.scrollHeight;
        } catch (err) {
            document.getElementById(loadingId).innerHTML = `<div class="text-danger">Error connecting to AI Copilot engine.</div>`;
        }
    }

    async function triggerAutomation(actionKey) {
        Swal.fire({
            title: 'Executing AI Automation',
            text: 'Processing action: ' + actionKey,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await fetch(automationUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ action: actionKey })
            });
            const data = await res.json();
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'AI Workflow Completed' : 'Automation Failed',
                text: data.result ? data.result.message : (data.error || 'Execution completed.')
            });
        } catch (err) {
            Swal.fire('Error', 'Failed to communicate with AI Automation server.', 'error');
        }
    }

    async function runAiConsultation(e) {
        e.preventDefault();
        const type = document.getElementById('consultType').value;
        const faceShape = document.getElementById('faceShapeInput').value;
        const occasion = document.getElementById('occasionInput').value;
        const skinConcern = document.getElementById('concernInput').value;

        const box = document.getElementById('consultationResultBox');
        box.innerHTML = `<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-pink-400 mb-2"></i><div>Analyzing consultation parameters & database services...</div></div>`;

        try {
            const res = await fetch(consultationUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    consultation_type: type,
                    face_shape: faceShape,
                    occasion: occasion,
                    skin_concern: skinConcern
                })
            });
            const data = await res.json();
            if (data.success) {
                const resData = data.data;
                let html = `<div class="p-3 bg-slate-800 rounded-3 mb-3">`;

                if (type === 'hair') {
                    html += `<h6 class="text-pink-400 fw-bold mb-2"><i class="fas fa-cut me-2"></i>Recommended Hairstyles for ${escapeHtml(resData.analysis.face_shape)} Face:</h6><ul class="mb-3 text-slate-200">`;
                    (resData.recommendations.hairstyles || []).forEach(cut => { html += `<li>${escapeHtml(cut)}</li>`; });
                    html += `</ul><div class="p-2 bg-slate-900 rounded mb-3 small text-slate-300"><strong>Styling Tip:</strong> ${escapeHtml(resData.recommendations.styling_tip)}</div>`;
                    
                    if (resData.recommendations.matched_salon_services && resData.recommendations.matched_salon_services.length > 0) {
                        html += `<h6 class="text-emerald-400 fw-bold mb-2"><i class="fas fa-tags me-2"></i>Matched Salon Services (Database):</h6><ul class="list-unstyled mb-0">`;
                        resData.recommendations.matched_salon_services.forEach(srv => {
                            html += `<li class="d-flex justify-content-between align-items-center p-2 bg-slate-700 rounded mb-2 small text-white"><span>${escapeHtml(srv.name)} (${escapeHtml(srv.duration)})</span><strong>${escapeHtml(srv.price)}</strong></li>`;
                        });
                        html += `</ul>`;
                    }
                } else if (type === 'hair_color') {
                    html += `<h6 class="text-pink-400 fw-bold mb-2"><i class="fas fa-palette me-2"></i>Recommended Shades (${escapeHtml(resData.current_analysis.skin_tone)} Undertone):</h6><ul class="mb-3 text-slate-200">`;
                    (resData.recommendations.recommended_shades || []).forEach(sh => { html += `<li>${escapeHtml(sh)}</li>`; });
                    html += `</ul><div class="p-2 bg-slate-900 rounded mb-3 small text-slate-300"><strong>Maintenance:</strong> ${escapeHtml(resData.recommendations.maintenance_schedule.gloss_toner_touchup)}</div>`;

                    if (resData.recommendations.matched_color_services && resData.recommendations.matched_color_services.length > 0) {
                        html += `<h6 class="text-emerald-400 fw-bold mb-2"><i class="fas fa-tags me-2"></i>Matched Salon Color Services (Database):</h6><ul class="list-unstyled mb-0">`;
                        resData.recommendations.matched_color_services.forEach(srv => {
                            html += `<li class="d-flex justify-content-between align-items-center p-2 bg-slate-700 rounded mb-2 small text-white"><span>${escapeHtml(srv.name)} (${escapeHtml(srv.duration)})</span><strong>${escapeHtml(srv.price)}</strong></li>`;
                        });
                        html += `</ul>`;
                    }
                } else {
                    html += `<h6 class="text-pink-400 fw-bold mb-2"><i class="fas fa-sparkles me-2"></i>Skin Profile Analysis (${escapeHtml(resData.skin_profile.primary_concern)}):</h6>`;
                    if (resData.matched_salon_treatments && resData.matched_salon_treatments.length > 0) {
                        html += `<h6 class="text-emerald-400 fw-bold mb-2 mt-3"><i class="fas fa-tags me-2"></i>Matched Salon Treatments (Database):</h6><ul class="list-unstyled mb-2">`;
                        resData.matched_salon_treatments.forEach(srv => {
                            html += `<li class="d-flex justify-content-between align-items-center p-2 bg-slate-700 rounded mb-2 small text-white"><span>${escapeHtml(srv.name)} (${escapeHtml(srv.duration)})</span><strong>${escapeHtml(srv.price)}</strong></li>`;
                        });
                        html += `</ul>`;
                    }
                    html += `<div class="p-2 bg-slate-900 rounded small text-slate-300 mt-2"><strong>Home Care Routine:</strong> ${escapeHtml(resData.home_care_routine)}</div>`;

                    if (resData.medical_disclaimer) {
                        html += `<div class="p-3 bg-rose-900 border border-rose-700 text-rose-200 rounded small mt-3"><i class="fas fa-exclamation-triangle me-1"></i><strong>${escapeHtml(resData.medical_disclaimer.title)}:</strong> ${escapeHtml(resData.medical_disclaimer.message)}</div>`;
                    }
                }
                html += `</div>`;
                box.innerHTML = html;
            } else {
                box.innerHTML = `<div class="text-danger p-3 bg-slate-800 rounded">Error: ${escapeHtml(data.error || 'Failed to process consultation.')}</div>`;
            }
        } catch (err) {
            box.innerHTML = `<div class="text-danger p-3 bg-slate-800 rounded">Error processing consultation request.</div>`;
        }
    }

    async function generateCampaign(e) {
        e.preventDefault();
        const campaignType = document.getElementById('campaignType').value;
        const discount = document.getElementById('discountInput').value;

        try {
            const res = await fetch(marketingUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ campaign_type: campaignType, discount: discount })
            });
            const data = await res.json();
            if (data.success) {
                const copywriting = data.data.copywriting;
                document.getElementById('campaignResultBox').classList.remove('d-none');
                document.getElementById('variationBadge').innerText = data.data.variation_id || 'AI Variant';
                document.getElementById('whatsappCopy').innerText = copywriting.whatsapp;
                document.getElementById('emailSubjectCopy').innerText = copywriting.email_subject || 'Special Offer';
                document.getElementById('emailBodyCopy').innerText = copywriting.email_body || copywriting.whatsapp;
                document.getElementById('instaCopy').innerText = copywriting.instagram_caption;
            } else {
                alert('Campaign Generation Error: ' + (data.error || 'Unable to generate copy.'));
            }
        } catch (err) {
            alert('Failed to generate campaign.');
        }
    }

    async function handleAssistantSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('assistantInput');
        const query = input.value.trim();
        if (!query) return;

        const box = document.getElementById('assistantChatBox');
        box.innerHTML += `<div class="msg-user bg-indigo-600 text-white mb-2 p-2 rounded"><div>${escapeHtml(query)}</div></div>`;
        input.value = '';
        box.scrollTop = box.scrollHeight;

        try {
            const res = await fetch(assistantUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ message: query, query: query })
            });
            const data = await res.json();
            if (data.success && data.data && data.data.reply) {
                box.innerHTML += `<div class="msg-ai bg-emerald-900 text-white mb-2 p-2 rounded"><div>${formatMarkdown(data.data.reply)}</div></div>`;
            } else {
                const errMsg = data.error || (data.data && data.data.message) || 'Unable to process assistant query.';
                box.innerHTML += `<div class="msg-ai bg-rose-900 text-rose-100 mb-2 p-2 rounded"><i class="fas fa-exclamation-circle me-1"></i>${escapeHtml(errMsg)}</div>`;
            }
            box.scrollTop = box.scrollHeight;
        } catch (err) {
            box.innerHTML += `<div class="msg-ai bg-rose-900 text-rose-100 mb-2 p-2 rounded"><i class="fas fa-exclamation-circle me-1"></i>Error processing assistant request: ${escapeHtml(err.message)}</div>`;
            box.scrollTop = box.scrollHeight;
        }
    }

    function toggleConsultFields() {}

    function escapeHtml(text) {
        return text ? text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
    }

    function formatMarkdown(text) {
        if (!text) return '';
        let formatted = escapeHtml(text);
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        formatted = formatted.replace(/\n/g, '<br>');
        return formatted;
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\ai\hub.blade.php ENDPATH**/ ?>