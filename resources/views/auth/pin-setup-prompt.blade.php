<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enable Quick PIN Login - {{ $appName ?? config('app.name') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ app(\App\Services\SettingsService::class)->getFaviconUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        /* ── Clean Card with Sharp, Moderate Radius ── */
        .auth-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            padding: 32px 28px;
        }

        /* ── Compact Icon Badge ── */
        .icon-badge {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #eef2ff;
            border: 1.5px solid #c7d2fe;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
        }

        /* ── Input Styling with Clean 8px Radius ── */
        .pin-input-field {
            width: 100%;
            height: 46px;
            padding: 8px 40px 8px 14px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            font-size: 20px;
            letter-spacing: 4px;
            text-align: center;
            font-weight: 700;
            color: #0f172a;
            background: #ffffff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            outline: none;
            font-family: inherit;
        }

        .pin-input-field:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .pin-input-field.has-error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .pin-input-field.is-valid-match {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        /* ── Button with Clean 8px Radius ── */
        .btn-gradient-primary {
            width: 100%;
            height: 44px;
            border-radius: 8px;
            background: #4f46e5;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: background 0.15s ease;
            font-family: inherit;
        }

        .btn-gradient-primary:hover {
            background: #4338ca;
        }

        .btn-gradient-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-subtle-skip {
            background: transparent;
            color: #64748b;
            font-weight: 500;
            font-size: 13px;
            border: none;
            cursor: pointer;
            padding: 0;
            border-radius: 0;
            transition: color 0.15s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: inherit;
        }

        .btn-subtle-skip:hover {
            color: #1e293b;
            background: transparent;
        }

        /* ── Clean 6px Chips ── */
        .req-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .req-chip.active {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .toggle-btn {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .toggle-btn:hover {
            color: #475569;
        }
    </style>
</head>

<body class="min-h-screen w-full flex items-center justify-center p-4">

    <div class="w-full max-w-md mx-auto">

        <!-- Top Salon Logo -->
        <div class="text-center mb-5">
            @if(!empty($appLogo))
                <a href="{{ url('/') }}" class="inline-block">
                    <img src="{{ $appLogo }}" alt="{{ $appName ?? config('app.name') }}" class="h-9 mx-auto object-contain" onerror="this.style.display='none'; document.getElementById('brandTextFallback').style.display='block';">
                </a>
                <span id="brandTextFallback" style="display: none;" class="text-lg font-bold text-slate-800 tracking-tight">{{ $appName ?? config('app.name') }}</span>
            @else
                <a href="{{ url('/') }}" class="text-lg font-bold text-slate-800 tracking-tight hover:text-indigo-600 transition-colors">
                    {{ $appName ?? config('app.name') }}
                </a>
            @endif
        </div>

        <!-- Main Card -->
        <div class="auth-card">

            <!-- Clean Icon & Header -->
            <div style="text-align: center; margin-bottom: 20px;">
                <div class="icon-badge">
                    <i class="fas fa-lock" style="font-size: 20px;"></i>
                </div>

                <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 6px;">Enable Quick PIN Login</h2>
                <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin: 0 auto; max-width: 300px;">
                    Log in faster next time on this device using a secure 6-digit PIN.
                </p>
            </div>

            <!-- Sleek Device Badge with 8px radius -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                    <div style="width: 28px; height: 28px; border-radius: 6px; background: #ffffff; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; color: #475569; flex-shrink: 0;">
                        <i class="fas fa-laptop" style="font-size: 13px;"></i>
                    </div>
                    <div style="min-width: 0;">
                        <div style="font-size: 10px; text-transform: uppercase; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; line-height: 1.1;">This Device</div>
                        <div style="font-size: 12px; font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $deviceInfo['default_name'] }}
                        </div>
                    </div>
                </div>
                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; flex-shrink: 0;">
                    <span style="width: 5px; height: 5px; border-radius: 50%; background: #10b981;"></span>
                    New
                </span>
            </div>

            <!-- Error Notification -->
            @if(!empty($errors) && $errors->any())
                <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 8px 12px; margin-bottom: 16px; font-size: 12px; color: #b91c1c;">
                    <ul style="margin: 0; padding-left: 14px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Setup Form -->
            <form id="pinSetupForm" method="POST" action="{{ route('pin.setup.complete') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="device_name" value="{{ old('device_name', $deviceInfo['default_name']) }}">

                <!-- Create 6-Digit PIN Input -->
                <div style="margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <label for="pin" style="font-size: 12.5px; font-weight: 600; color: #334155; line-height: 1;">
                            Create 6-Digit PIN <span style="color: #ef4444;">*</span>
                        </label>
                        <span id="pinLengthBadge" style="font-size: 11px; font-weight: 500; color: #94a3b8;">0/6 digits</span>
                    </div>

                    <div style="position: relative;">
                        <input type="password"
                               id="pin"
                               name="pin"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               maxlength="6"
                               required
                               placeholder="••••••"
                               class="pin-input-field"
                               autocomplete="new-password">

                        <button type="button" onclick="togglePinVisibility('pin', this)" class="toggle-btn" title="Toggle visibility">
                            <i class="far fa-eye" style="font-size: 14px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm 6-Digit PIN Input -->
                <div style="margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <label for="pin_confirmation" style="font-size: 12.5px; font-weight: 600; color: #334155; line-height: 1;">
                            Confirm 6-Digit PIN <span style="color: #ef4444;">*</span>
                        </label>
                        <span id="matchStatusText" style="font-size: 11px; font-weight: 600; color: #94a3b8;"></span>
                    </div>

                    <div style="position: relative;">
                        <input type="password"
                               id="pin_confirmation"
                               name="pin_confirmation"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               maxlength="6"
                               required
                               placeholder="••••••"
                               class="pin-input-field"
                               autocomplete="new-password">

                        <button type="button" onclick="togglePinVisibility('pin_confirmation', this)" class="toggle-btn" title="Toggle visibility">
                            <i class="far fa-eye" style="font-size: 14px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Security Standards Chips with 6px radius -->
                <div style="margin-bottom: 20px; display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 6px;">
                    <span id="chipLength" class="req-chip">
                        <span>•</span> 6 Numbers
                    </span>
                    <span id="chipSeq" class="req-chip">
                        <span>•</span> No 123456
                    </span>
                    <span id="chipRepeat" class="req-chip">
                        <span>•</span> No 000000
                    </span>
                </div>

                <!-- Primary Submit Action -->
                <button type="submit" id="btnSubmitSetup" class="btn-gradient-primary">
                    <i class="fas fa-shield-alt" style="font-size: 14px;"></i>
                    <span>Enable PIN &amp; Trust Device</span>
                </button>
            </form>

            <!-- Subtle Skip Action — plain text link, no visible button box -->
            <div style="margin-top: 14px; text-align: center;">
                <form method="POST" action="{{ route('pin.setup.skip') }}" class="inline-block">
                    @csrf
                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer; font-size: 13px; font-weight: 500; color: #64748b; display: inline-flex; align-items: center; gap: 5px; font-family: inherit; transition: color 0.15s;" onmouseover="this.style.color='#1e293b'" onmouseout="this.style.color='#64748b'">
                        Skip for now
                        <i class="fas fa-chevron-right" style="font-size: 10px; opacity: 0.7;"></i>
                    </button>
                </form>
            </div>

        </div>

        <!-- Privacy & Protection Note -->
        <div class="text-center mt-3 text-[11px] text-slate-400">
            Encrypted with Argon2id & multi-tenant isolation
        </div>

    </div>

    <!-- Live Validation & Visibility Toggle Script -->
    <script>
        function togglePinVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'far fa-eye-slash text-indigo-600';
            } else {
                input.type = 'password';
                icon.className = 'far fa-eye';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const pinInput = document.getElementById('pin');
            const confirmInput = document.getElementById('pin_confirmation');
            const pinLengthBadge = document.getElementById('pinLengthBadge');
            const matchStatusText = document.getElementById('matchStatusText');
            const chipLength = document.getElementById('chipLength');
            const chipSeq = document.getElementById('chipSeq');
            const chipRepeat = document.getElementById('chipRepeat');

            // Sanitize input to only accept digits
            [pinInput, confirmInput].forEach(inp => {
                inp.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                    validateLive();
                });
            });

            function validateLive() {
                const pin = pinInput.value;
                const confirm = confirmInput.value;

                pinLengthBadge.textContent = `${pin.length}/6 digits`;

                // Chip 1: 6 digits
                const has6 = pin.length === 6;
                chipLength.className = has6 ? 'req-chip active' : 'req-chip';

                // Chip 2: No sequence
                const seq = ['012345','123456','234567','345678','456789','543210','654321','765432','876543','987654'];
                const isSeq = seq.includes(pin);
                chipSeq.className = (pin.length > 2 && !isSeq) ? 'req-chip active' : 'req-chip';

                // Chip 3: No repeated
                const isRepeat = /^([0-9])\1{5}$/.test(pin);
                chipRepeat.className = (pin.length > 2 && !isRepeat) ? 'req-chip active' : 'req-chip';

                // Confirmation match
                if (confirm.length > 0) {
                    if (pin === confirm && has6) {
                        matchStatusText.textContent = '✓ PINs Match';
                        matchStatusText.className = 'text-[11px] font-semibold text-emerald-600';
                        confirmInput.classList.add('is-valid-match');
                        confirmInput.classList.remove('has-error');
                    } else if (confirm.length === 6 && pin !== confirm) {
                        matchStatusText.textContent = '✗ PINs do not match';
                        matchStatusText.className = 'text-[11px] font-semibold text-rose-600';
                        confirmInput.classList.remove('is-valid-match');
                        confirmInput.classList.add('has-error');
                    } else {
                        matchStatusText.textContent = '';
                        confirmInput.classList.remove('is-valid-match', 'has-error');
                    }
                } else {
                    matchStatusText.textContent = '';
                    confirmInput.classList.remove('is-valid-match', 'has-error');
                }
            }

            setTimeout(() => pinInput.focus(), 150);
        });
    </script>

</body>
</html>
