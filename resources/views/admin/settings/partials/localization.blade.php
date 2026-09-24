<!-- Regional Settings -->
<div class="settings-section-card">
    <div class="settings-section-header">
        <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
        <h6>Regional & Localization</h6>
    </div>

    <div class="settings-field-group">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="far fa-clock text-primary me-2"></i>System Timezone
                </label>
                <select name="timezone" class="form-select select2">
                    @foreach(timezone_identifiers_list() as $timezone)
                        <option value="{{ $timezone }}" {{ ($settings['timezone'] ?? 'UTC') == $timezone ? 'selected' : '' }}>
                            {{ $timezone }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-language text-success me-2"></i>Primary Language
                </label>
                <select name="language" class="form-select">
                    <option value="en" {{ ($settings['language'] ?? 'en') == 'en' ? 'selected' : '' }}>English (US)
                    </option>
                    <option value="es" {{ ($settings['language'] ?? 'en') == 'es' ? 'selected' : '' }}>Spanish
                        (Español)</option>
                    <option value="fr" {{ ($settings['language'] ?? 'en') == 'fr' ? 'selected' : '' }}>French
                        (Français)</option>
                    <option value="de" {{ ($settings['language'] ?? 'en') == 'de' ? 'selected' : '' }}>German
                        (Deutsch)</option>
                    <option value="ar" {{ ($settings['language'] ?? 'en') == 'ar' ? 'selected' : '' }}>Arabic
                        (العربية)</option>
                </select>
            </div>
        </div>

        <div class="row g-4  pt-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="far fa-calendar-alt text-info me-2"></i>Date Format
                </label>
                <select name="date_format" class="form-select">
                    <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>
                        YYYY-MM-DD</option>
                    <option value="d/m/Y" {{ ($settings['date_format'] ?? 'Y-m-d') == 'd/m/Y' ? 'selected' : '' }}>
                        DD/MM/YYYY</option>
                    <option value="m/d/Y" {{ ($settings['date_format'] ?? 'Y-m-d') == 'm/d/Y' ? 'selected' : '' }}>
                        MM/DD/YYYY</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="fas fa-business-time text-warning me-2"></i>Time Clock
                </label>
                <select name="time_format" class="form-select">
                    <option value="24h" {{ ($settings['time_format'] ?? '24h') == '24h' ? 'selected' : '' }}>24 Hour
                        (Military)</option>
                    <option value="12h" {{ ($settings['time_format'] ?? '24h') == '12h' ? 'selected' : '' }}>12 Hour
                        (AM/PM)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-uppercase text-secondary">
                    <i class="far fa-calendar-check text-danger me-2"></i>Week Starts On
                </label>
                <select name="week_start_day" class="form-select">
                    <option value="monday" {{ ($settings['week_start_day'] ?? 'monday') == 'monday' ? 'selected' : '' }}>
                        Monday</option>
                    <option value="sunday" {{ ($settings['week_start_day'] ?? 'monday') == 'sunday' ? 'selected' : '' }}>
                        Sunday</option>
                    <option value="saturday" {{ ($settings['week_start_day'] ?? 'monday') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                </select>
            </div>
        </div>
    </div>
</div>