<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Actor;
use App\Models\LearningObject;
use App\Models\Statement;
use App\Models\Verb;
use Illuminate\Support\Facades\Artisan;

class WelcomeController extends Controller
{
    public function landingPage(Request $request)
    {
        if (!\Schema::hasTable('migrations')) {
            return redirect('/install');
        }

        $data = [];
        $dates = collect();

        // Collect data for the past 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates->push(Carbon::now()->subDays($i)->format('M d'));

            $data[] = [
                'date' => $date,
                'actors' => Actor::whereDate('created_at', $date)->count(),
                'statements' => Statement::whereDate('created_at', $date)->count(),
                'objects' => LearningObject::whereDate('created_at', $date)->count(),
            ];
        }

        $stats = [
            'actors' => Actor::count(),
            'verbs' => Verb::count(),
            'objects' => LearningObject::count(),
            'statements' => Statement::count(),
        ];

        return view('welcome', compact('stats', 'dates', 'data'));
    }

    public function install(Request $request)
    {
        Artisan::call('migrate', ['--force' => true]);

        return view('install_finished');
    }
}
