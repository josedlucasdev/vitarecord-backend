<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Evolution;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get statistics for the doctor's dashboard.
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $doctorId = $user->id;

        // 1. Total Patients (Owned or Accepted Referrals)
        $totalPatients = Patient::where(function($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId)
              ->orWhereHas('referrals', function($rq) use ($doctorId) {
                  $rq->where('to_doctor_id', $doctorId)->where('status', 'accepted');
              });
        })->count();

        // 2. Recent Evolutions (Last 7 days)
        $recentEvolutions = Evolution::where('doctor_id', $doctorId)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // 3. Pending Referrals
        $pendingReferrals = Referral::where('to_doctor_id', $doctorId)
            ->where('status', 'pending')
            ->count();

        // 4. Age Demographics
        $ageDemographics = Patient::where('doctor_id', $doctorId)
            ->select(DB::raw("CASE 
                WHEN age < 18 THEN '0-18'
                WHEN age < 35 THEN '19-35'
                WHEN age < 60 THEN '36-60'
                ELSE '60+' 
              END as age_group"), DB::raw("count(*) as count"))
            ->groupBy('age_group')
            ->get();

        // 5. Popular Occupations (Trends)
        $topOccupations = Patient::where('doctor_id', $doctorId)
            ->whereNotNull('occupation')
            ->select('occupation', DB::raw('count(*) as count'))
            ->groupBy('occupation')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // 6. Activity History (Last 30 days)
        $history = Evolution::where('doctor_id', $doctorId)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('date(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $activityHistory = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $activityHistory[] = [
                'date' => $date,
                'count' => $history[$date] ?? 0
            ];
        }

        // 7. Patient Sources (Own vs Referred)
        $ownCount = Patient::where('doctor_id', $doctorId)->count();
        $referredCount = Referral::where('to_doctor_id', $doctorId)->where('status', 'accepted')->count();

        // 8. Top Reasons for Consultation
        $topReasons = Evolution::where('doctor_id', $doctorId)
            ->select('reason_for_consultation', DB::raw('count(*) as count'))
            ->groupBy('reason_for_consultation')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'kpis' => [
                'total_patients' => $totalPatients,
                'recent_evolutions' => $recentEvolutions,
                'pending_referrals' => $pendingReferrals,
            ],
            'demographics' => $ageDemographics,
            'trends' => $topOccupations,
            'activity' => $activityHistory,
            'sources' => [
                ['label' => 'Directos', 'count' => $ownCount],
                ['label' => 'Referidos', 'count' => $referredCount]
            ],
            'top_reasons' => $topReasons
        ]);
    }
}
