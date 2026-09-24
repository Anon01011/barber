<?php

namespace App\Services\AI;

use App\Models\Salon;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class AiConsultationEngine
{
    /**
     * AI Hair & Beauty Consultation (Feature 1 & 2)
     * Analyzes customer face shape, hair parameters & occasion against REAL salon database services.
     */
    public function analyzeHairAndBeauty(Salon $salon, array $inputData): array
    {
        $faceShape = strtolower($inputData['face_shape'] ?? 'oval');
        $hairType = strtolower($inputData['hair_type'] ?? 'straight');
        $hairLength = strtolower($inputData['hair_length'] ?? 'medium');
        $ageGroup = $inputData['age_group'] ?? '26-35';
        $occasion = strtolower($inputData['occasion'] ?? 'daily');
        $customerId = $inputData['customer_id'] ?? null;

        // Fetch 100% REAL Salon Services from database
        $realServices = Service::where('salon_id', $salon->id)
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('status', '1')
                  ->orWhereNull('status');
            })
            ->get();

        // Query customer history if available
        $customerHistory = [];
        if ($customerId) {
            $customerHistory = Booking::where('salon_id', $salon->id)
                ->where('customer_id', $customerId)
                ->with('service')
                ->latest()
                ->take(5)
                ->get()
                ->pluck('service.name')
                ->filter()
                ->toArray();
        }

        // Recommendation Matrix based on face shape & hair texture
        $styles = match ($faceShape) {
            'round' => [
                'recommended_cuts' => ['Long Layers with Volume', 'Textured Pixie', 'Asymmetrical Bob', 'Curtain Bangs'],
                'styling_tip' => 'Add height at the crown and soft layers to elongate facial features.',
            ],
            'square' => [
                'recommended_cuts' => ['Soft Waves with Side Part', 'Long Shag with Bangs', 'Layered Lob', 'Wispy Fringe'],
                'styling_tip' => 'Soften strong jawlines with textured fringe and loose side-swept waves.',
            ],
            'heart' => [
                'recommended_cuts' => ['Chin-Length Bob', 'Side-Swept Bangs', 'Medium Waves', 'Deep Side Part'],
                'styling_tip' => 'Add volume near the chin and jawline to balance a wider forehead.',
            ],
            'diamond' => [
                'recommended_cuts' => ['Textured Bob', 'Sleek Shoulder Length', 'Side Bangs', 'Chin-Length Shag'],
                'styling_tip' => 'Accentuate cheekbones while adding fullness at the chin.',
            ],
            default => [ // Oval
                'recommended_cuts' => ['Classic Layered Cut', 'Blunt Bob', 'Curtain Bangs with Beach Waves', 'Pixie Cut'],
                'styling_tip' => 'Oval face shapes complement almost any cut, from sleek bobs to voluminous layers.',
            ],
        };

        // Match REAL DB services that fit hair/cut/spa
        $matchedDbServices = [];
        foreach ($realServices as $srv) {
            $nameLower = strtolower($srv->name);
            if (str_contains($nameLower, 'cut') || str_contains($nameLower, 'hair') || str_contains($nameLower, 'spa') || str_contains($nameLower, 'style') || str_contains($nameLower, 'blow') || str_contains($nameLower, 'trim')) {
                $matchedDbServices[] = [
                    'id' => $srv->id,
                    'name' => $srv->name,
                    'price' => format_currency($srv->price),
                    'duration' => "{$srv->duration} mins",
                ];
            }
        }

        // Fallback to top real services if keyword match is empty
        if (empty($matchedDbServices)) {
            foreach ($realServices->take(4) as $srv) {
                $matchedDbServices[] = [
                    'id' => $srv->id,
                    'name' => $srv->name,
                    'price' => format_currency($srv->price),
                    'duration' => "{$srv->duration} mins",
                ];
            }
        }

        return [
            'analysis' => [
                'face_shape' => ucfirst($faceShape),
                'hair_type' => ucfirst($hairType),
                'hair_length' => ucfirst($hairLength),
                'age_group' => $ageGroup,
                'occasion' => ucfirst($occasion),
            ],
            'recommendations' => [
                'hairstyles' => $styles['recommended_cuts'],
                'styling_tip' => $styles['styling_tip'],
                'customer_previous_history' => $customerHistory,
                'matched_salon_services' => array_slice($matchedDbServices, 0, 4),
            ],
        ];
    }

    /**
     * AI Hair Color Recommendation & Maintenance (Feature 3)
     * Matches hair color shades against REAL database services.
     */
    public function analyzeHairColor(Salon $salon, array $inputData): array
    {
        $currentColor = strtolower($inputData['current_color'] ?? 'dark_brown');
        $skinTone = strtolower($inputData['skin_tone'] ?? 'warm');
        $grayPercentage = (int) ($inputData['gray_percentage'] ?? 0);

        // Fetch 100% REAL Salon Color Services
        $colorServices = Service::where('salon_id', $salon->id)
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('status', '1')
                  ->orWhereNull('status');
            })
            ->get()
            ->filter(function ($srv) {
                $n = strtolower($srv->name);
                return str_contains($n, 'color') || str_contains($n, 'dye') || str_contains($n, 'balayage') || str_contains($n, 'highlight') || str_contains($n, 'root') || str_contains($n, 'toner');
            })
            ->values();

        $colorMatrix = match ($skinTone) {
            'cool' => [
                'shades' => ['Ash Blonde', 'Icy Platinum', 'Cool Espresso', 'Burgundy Wine', 'Muted Mocha'],
                'avoid' => ['Golden Blonde', 'Coppery Red'],
                'tone_description' => 'Cool undertones look stunning with blue/violet base hues that counteract warmth.',
            ],
            'warm' => [
                'shades' => ['Warm Honey Blonde', 'Golden Chestnut', 'Rich Chocolate', 'Copper Bronze', 'Caramel Balayage'],
                'avoid' => ['Ashy Silver', 'Blue Black'],
                'tone_description' => 'Warm undertones complement golden, caramel, and copper shades that enhance natural skin radiance.',
            ],
            default => [ // Neutral
                'shades' => ['Soft Rose Gold', 'Rich Espresso', 'Caramel Highlights', 'Hazelnut Brown', 'Auburn'],
                'avoid' => ['Extreme oversaturated neon tones'],
                'tone_description' => 'Neutral skin tone harmonizes seamlessly with both warm highlights and cool bases.',
            ],
        };

        $dbColorList = [];
        foreach ($colorServices as $srv) {
            $dbColorList[] = [
                'id' => $srv->id,
                'name' => $srv->name,
                'price' => format_currency($srv->price),
                'duration' => "{$srv->duration} mins",
            ];
        }

        // Maintenance schedule calculation
        $maintenanceSchedule = [
            'gloss_toner_touchup' => 'Every 3 to 4 weeks (Maintains vibrancy & neutralizes brassiness)',
            'root_touchup' => $grayPercentage > 30 ? 'Every 3 to 4 weeks' : 'Every 6 weeks',
            'full_balayage_refresh' => 'Every 10 to 12 weeks',
            'home_care_regimen' => 'Sulfate-free color protection shampoo + weekly deep moisture hair mask.',
        ];

        return [
            'current_analysis' => [
                'base_shade' => ucwords(str_replace('_', ' ', $currentColor)),
                'skin_tone' => ucfirst($skinTone),
                'gray_coverage_needed' => $grayPercentage > 0 ? "{$grayPercentage}% Gray Hair" : 'None',
            ],
            'recommendations' => [
                'recommended_shades' => $colorMatrix['shades'],
                'shades_to_avoid' => $colorMatrix['avoid'],
                'undertone_guidance' => $colorMatrix['tone_description'],
                'matched_color_services' => $dbColorList,
                'maintenance_schedule' => $maintenanceSchedule,
            ],
        ];
    }

    /**
     * AI Skin & Beauty Consultation with Medical Disclaimer (Feature 4)
     * Matches skin concern treatments against REAL salon database skincare/facial services.
     */
    public function analyzeSkinAndBeauty(Salon $salon, array $inputData): array
    {
        $primaryConcern = strtolower($inputData['skin_concern'] ?? 'dullness');
        $skinType = strtolower($inputData['skin_type'] ?? 'combination');
        $hasMedicalCondition = !empty($inputData['has_medical_condition']) || in_array($primaryConcern, ['severe_acne', 'eczema', 'psoriasis', 'open_wounds', 'dermatitis']);

        // Query REAL facial/skincare services from Database for this salon
        $facialServices = Service::where('salon_id', $salon->id)
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('status', '1')
                  ->orWhereNull('status');
            })
            ->get()
            ->filter(function ($srv) {
                $n = strtolower($srv->name);
                return str_contains($n, 'facial') || str_contains($n, 'cleanup') || str_contains($n, 'skin') || str_contains($n, 'glow') || str_contains($n, 'tan') || str_contains($n, 'peel') || str_contains($n, 'mask');
            })
            ->values();

        $dbFacialList = [];
        foreach ($facialServices as $srv) {
            $dbFacialList[] = [
                'id' => $srv->id,
                'name' => $srv->name,
                'price' => format_currency($srv->price),
                'duration' => "{$srv->duration} mins",
            ];
        }

        // Base regimen advice
        $homeCare = match ($primaryConcern) {
            'acne_prone', 'breakouts' => 'Non-comedogenic moisturizer, oil-free salicylic cleanser, daily mineral sunscreen SPF 50.',
            'pigmentation', 'dark_spots' => 'Niacinamide serum, Vitamin C antioxidant serum, broad-spectrum sunscreen.',
            'dehydration', 'dryness' => 'Ceramide moisture cream, hydrating facial toner mist, gentle creamy cleanser.',
            'aging', 'fine_lines' => 'Retinol night cream, peptide serums, daily SPF 50 sun protection.',
            default => 'Daily cleanser, light hydration lotion, weekly gentle exfoliating scrub.',
        };

        // Medical Disclaimer triggers
        $medicalDisclaimer = null;
        if ($hasMedicalCondition) {
            $medicalDisclaimer = [
                'is_triggered' => true,
                'title' => 'Professional Medical / Dermatologist Advisory',
                'message' => 'Based on the skin characteristics indicated (active inflammation, severe acne, eczema, or lesions), salon cosmetic treatments are NOT a substitute for professional medical care. We strongly advise consulting a board-certified dermatologist for medical evaluation prior to undergoing facial or chemical skin procedures.',
            ];
        }

        return [
            'skin_profile' => [
                'skin_type' => ucfirst($skinType),
                'primary_concern' => ucwords(str_replace('_', ' ', $primaryConcern)),
            ],
            'matched_salon_treatments' => $dbFacialList,
            'home_care_routine' => $homeCare,
            'medical_disclaimer' => $medicalDisclaimer,
        ];
    }
}
