<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\ReportingService;
use App\Providers\RouteServiceProvider;

class DashboardController extends Controller
{
    protected $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        if (!$user || (method_exists($user, 'hasRole') && ($user->hasRole('user') || $user->hasRole('costumer') || $user->hasRole('customer')))) {
            return app(WelcomeController::class)->index();
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from'))->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
            $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to'))->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
            $recapMode = $request->get('recap_mode', 'daily');

            $recap = $this->reportingService->getRecapData($dateFrom, $dateTo, null, $recapMode);
            $summary = $this->reportingService->getSummaryStats($dateFrom, $dateTo);
            $topProducts = $this->reportingService->getTopProducts($dateFrom, $dateTo);
            $outletPerformance = $this->reportingService->getOutletPerformance($dateFrom, $dateTo);
            $chart = $this->reportingService->getChartData($dateFrom, $dateTo);
            $recapData = $recap['recapData'] ?? collect();
            $periods = $recap['periods'] ?? collect();
            $dates = $chart['labels'] ?? collect();
            $omzet7days = $chart['omzet'] ?? collect();
            $orders7days = $chart['orders'] ?? collect();

            return view('dashboard', compact(
                'summary',
                'topProducts',
                'outletPerformance',
                'recapData',
                'periods',
                'dates',
                'omzet7days',
                'orders7days',
                'dateFrom',
                'dateTo',
                'recapMode',
            ));
        }

        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['kasir', 'cashier'])) {
            return redirect()->route('console.payments.index');
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
