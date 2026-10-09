<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaasClinicController extends Controller
{
    public function index(Request $request)
    {
        $query = Clinic::with(['plan', 'users']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'ilike', "%{$s}%")
                  ->orWhere('clinic_code', 'ilike', "%{$s}%")
                  ->orWhere('email', 'ilike', "%{$s}%")
                  ->orWhere('phone_number', 'ilike', "%{$s}%");
            });
        }

        $clinics = $query->orderBy('created_at', 'desc')->paginate(10);
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('admin.saas.clinics.index', compact('clinics', 'plans'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('admin.saas.clinics.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clinics,email',
            'phone_number' => 'required|string',
            'address' => 'nullable|string',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|in:active,trial,suspended',
            'admin_name' => 'required|string|max:255',
            'admin_password' => 'required|string|min:8',
        ]);

        $slug = Str::slug($request->name);
        $count = Clinic::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) $slug .= '-' . ($count + 1);

        $plan = SubscriptionPlan::find($request->subscription_plan_id);

        $clinic = Clinic::create([
            'clinic_code' => Clinic::generateClinicCode(),
            'name' => $request->name,
            'slug' => $slug,
            'subdomain' => Str::slug($request->name),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'status' => $request->status,
            'trial_ends_at' => $request->status === 'trial' ? now()->addDays(14) : null,
            'subscription_plan_id' => $plan->id,
            'max_therapists' => $plan->max_therapists,
            'max_patients' => $plan->max_patients,
            'is_active' => true,
        ]);

        // Buat User Admin Klinik
        $adminUser = User::create([
            'name' => $request->admin_name,
            'email' => $request->email,
            'password' => bcrypt($request->admin_password),
            'clinic_id' => $clinic->id,
        ]);
        $adminUser->assignRole('clinic_admin');

        return redirect()->route('saas.clinics.index')->with('success', "Klinik {$clinic->name} berhasil didaftarkan!");
    }

    public function edit($id)
    {
        $clinic = Clinic::with('plan')->findOrFail($id);
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('admin.saas.clinics.edit', compact('clinic', 'plans'));
    }

    public function update(Request $request, $id)
    {
        $clinic = Clinic::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clinics,email,' . $clinic->id,
            'phone_number' => 'required|string',
            'address' => 'nullable|string',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|in:active,trial,suspended,expired',
            'max_therapists' => 'required|integer|min:1',
            'max_patients' => 'required|integer|min:1',
        ]);

        $clinic->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'subscription_plan_id' => $request->subscription_plan_id,
            'status' => $request->status,
            'max_therapists' => $request->max_therapists,
            'max_patients' => $request->max_patients,
            'is_active' => $request->status !== 'suspended',
            'notes' => $request->notes,
        ]);

        return redirect()->route('saas.clinics.index')->with('success', "Data klinik {$clinic->name} berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $clinic = Clinic::findOrFail($id);
        if ($clinic->id == 1) {
            return redirect()->back()->with('error', 'Klinik default RSTBDI tidak dapat dihapus.');
        }

        $clinic->update(['status' => 'suspended', 'is_active' => false]);
        return redirect()->route('saas.clinics.index')->with('success', "Klinik {$clinic->name} berhasil di-suspend.");
    }

    /**
     * Fitur Impersonate / Masuk & Tinjau Operasional Klinik Ini
     */
    public function impersonate($id)
    {
        $clinic = Clinic::findOrFail($id);
        session(['active_clinic_id' => $clinic->id]);
        return redirect()->route('dashboard')->with('info', "Anda sedang masuk meninjau operasional: {$clinic->name}");
    }

    /**
     * Keluar dari mode tinjau klinik
     */
    public function exitImpersonation()
    {
        session()->forget('active_clinic_id');
        return redirect()->route('saas.clinics.index')->with('info', 'Keluar dari mode tinjau klinik, kembali ke Panel SaaS Administrator.');
    }
}
