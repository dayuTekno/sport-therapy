<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaasPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('clinics')->orderBy('sort_order')->get();
        return view('admin.saas.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'monthly_rate' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|in:6_months,yearly,monthly',
            'duration_in_months' => 'nullable|integer|min:1',
            'commitment_label' => 'nullable|string|max:255',
            'max_therapists' => 'required|integer|min:1',
            'max_patients' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|string', // pisahkan baris baru
        ]);

        $featuresArray = array_filter(array_map('trim', explode("\n", $request->features ?: '')));
        
        $duration = $request->duration_in_months ?: ($request->billing_cycle === 'yearly' ? 12 : ($request->billing_cycle === '6_months' ? 6 : 1));
        $monthlyRate = $request->monthly_rate ?: ($duration > 0 ? $request->price / $duration : $request->price);
        
        $commitmentLabel = $request->commitment_label;
        if (empty($commitmentLabel)) {
            if ($duration === 12 || $request->billing_cycle === 'yearly') {
                $commitmentLabel = 'Total Periode 1 Tahun Penuh: Rp ' . number_format($request->price, 0, ',', '.') . ',- / tahun';
            } elseif ($duration === 6 || $request->billing_cycle === '6_months') {
                $commitmentLabel = 'Total Periode 6 Bulan Pertama: Rp ' . number_format($request->price, 0, ',', '.') . ',- (All-in)';
            } else {
                $commitmentLabel = 'Tarif Bulanan: Rp ' . number_format($request->price, 0, ',', '.') . ',- / bulan';
            }
        }

        SubscriptionPlan::create([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'monthly_rate' => $monthlyRate,
            'commitment_label' => $commitmentLabel,
            'duration_in_months' => $duration,
            'billing_cycle' => $request->billing_cycle,
            'max_therapists' => $request->max_therapists,
            'max_patients' => $request->max_patients,
            'description' => $request->description,
            'features_json' => array_values($featuresArray),
            'is_active' => true,
            'sort_order' => SubscriptionPlan::count() + 1,
        ]);

        return redirect()->route('saas.plans.index')->with('success', 'Paket langganan baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'monthly_rate' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|in:6_months,yearly,monthly',
            'duration_in_months' => 'nullable|integer|min:1',
            'commitment_label' => 'nullable|string|max:255',
            'max_therapists' => 'required|integer|min:1',
            'max_patients' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $featuresArray = array_filter(array_map('trim', explode("\n", $request->features ?: '')));

        $duration = $request->duration_in_months ?: ($request->billing_cycle === 'yearly' ? 12 : ($request->billing_cycle === '6_months' ? 6 : 1));
        $monthlyRate = $request->monthly_rate ?: ($duration > 0 ? $request->price / $duration : $request->price);

        $commitmentLabel = $request->commitment_label;
        if (empty($commitmentLabel)) {
            if ($duration === 12 || $request->billing_cycle === 'yearly') {
                $commitmentLabel = 'Total Periode 1 Tahun Penuh: Rp ' . number_format($request->price, 0, ',', '.') . ',- / tahun';
            } elseif ($duration === 6 || $request->billing_cycle === '6_months') {
                $commitmentLabel = 'Total Periode 6 Bulan Pertama: Rp ' . number_format($request->price, 0, ',', '.') . ',- (All-in)';
            } else {
                $commitmentLabel = 'Tarif Bulanan: Rp ' . number_format($request->price, 0, ',', '.') . ',- / bulan';
            }
        }

        $plan->update([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'price' => $request->price,
            'monthly_rate' => $monthlyRate,
            'commitment_label' => $commitmentLabel,
            'duration_in_months' => $duration,
            'billing_cycle' => $request->billing_cycle,
            'max_therapists' => $request->max_therapists,
            'max_patients' => $request->max_patients,
            'description' => $request->description,
            'features_json' => array_values($featuresArray),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('saas.plans.index')->with('success', "Paket {$plan->name} berhasil diperbarui!");
    }
}
