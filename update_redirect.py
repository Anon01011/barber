import sys

code = """    protected function determineRedirectUrl(User $user): string
    {
        try {
            if ($user->hasRole('super_admin')) {
                return route('admin.super.dashboard');
            }

            if ($user->salon_id && $user->salon) {
                $slug = trim($user->salon->slug);

                if ($user->hasRole('employee') && !$user->hasAnyRole(['salon_admin', 'manager', 'receptionist'])) {
                    $isBarberSalon = method_exists($user->salon, 'isBarber') && $user->salon->isBarber();
                    if ($isBarberSalon) {
                        $canUsePos = $user->salon && $user->salon->canUseFeature('POS System') && \\App\\Helpers\\ModuleHelper::posEnabled() && $user->can('pos.view');
                        if ($canUsePos) {
                            return route('admin.pos.index', ['salon_slug' => $slug, 'view' => 'barber']);
                        }
                    }
                    return route('employee.dashboard', ['salon_slug' => $slug]);
                }

                if (method_exists($user->salon, 'isBarber') && $user->salon->isBarber()) {
                    return route('admin.pos.index', ['salon_slug' => $slug]);
                }

                return route('dashboard', ['salon_slug' => $slug]);
            }
        } catch (\\Throwable $e) {
            \\Illuminate\\Support\\Facades\\Log::warning('determineRedirectUrl failed, falling back to home: ' . $e->getMessage());
        }

        return route('home.dashboard');
    }
}
"""

paths = [
    r'c:\Ketan\projects\barber\app\Http\Controllers\Auth\PinLoginController.php',
    r'C:\Ketan\projects\salon-cms-master\app\Http\Controllers\Auth\PinLoginController.php'
]

for p in paths:
    with open(p, 'r', encoding='utf-8') as f:
        content = f.read()

    marker = 'protected function determineRedirectUrl('
    idx = content.find(marker)
    if idx != -1:
        new_content = content[:idx] + code
        with open(p, 'w', encoding='utf-8', newline='\r\n') as f:
            f.write(new_content)
        print("Updated:", p)
    else:
        print("Marker not found in:", p)

