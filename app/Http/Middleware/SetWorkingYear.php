<?php

namespace App\Http\Middleware;

use App\Models\AcademicYear;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetWorkingYear
{
    public function handle(Request $request, Closure $next): Response
    {
        $yearId = session('working_year_id');
        $year = $yearId
            ? AcademicYear::find($yearId)
            : AcademicYear::where('is_active', true)->first();

        if (!$year) {
            $year = AcademicYear::latest()->first();
        }

        if ($year) {
            session(['working_year_id' => $year->id]);
        }

        View::share('workingYear', $year);
        App::instance('workingYear', $year);

        return $next($request);
    }
}
