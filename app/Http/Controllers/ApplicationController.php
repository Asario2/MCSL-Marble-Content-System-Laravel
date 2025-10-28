<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Jetstream\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
  //
  public function index(Request $request)
  {
    $auth_user = Auth::user();
    // ermittle user
    $user = User::where('email', $auth_user->email)->first();
    if (!$user) {
      // Keine Anwendung für Anwender vorhanden
      return redirect()->intended(route('home.no_application_found'));
    }
    // speichere users.last_login_at
    $user->last_login_at = Carbon::now();
    $user->save();
    // Gibt es einen Success-Wert?
    $success = session()->get('success');
    //
    if ($user->is_admin) {
      return redirect()->intended(route('admin.dashboard'))->with(['success' => $success]);
    }
    //
    if ($user->is_employee) {
      return redirect()->intended(route('employee.dashboard'))->with(['success' => $success]);
    }
    //
    if ($user->is_customer) {
      return redirect()->intended(route('customer.dashboard'))->with(['success' => $success]);
    }
    // Anwender abmelden
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    // Keine Anwendung für Anwender vorhanden
    return redirect()->intended(route('home.no_application_found'));
  }

  public static function sessions(Request $request)
  {
    if (config('session.driver') !== 'database') {
      return collect();
    }

    return collect(
      DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
        ->where('user_id', $request->user()->getAuthIdentifier())
        ->orderBy('last_activity', 'desc')
        ->get()
    )->map(function ($session) use ($request) {
      $agent = ApplicationController::createAgent($session);

      return (object) [
        'agent' => [
          'is_desktop' => $agent->isDesktop(),
          'platform' => $agent->platform(),
          'browser' => $agent->browser(),
        ],
        'ip_address' => $session->ip_address,
        'is_current_device' => $session->id === $request->session()->getId(),
        'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
      ];
    });
  }

  protected static function createAgent($session)
  {
    return tap(new Agent, function ($agent) use ($session) {
      $agent->setUserAgent($session->user_agent);
    });
  }
  public static function blog_categories($blog)
  {
    $res = DB::table("blogs")->where("id",$blog->id)->leftJoin("blog_categories","blogs.blog_categories_id","=","blog_categories.id")->select("name")->first();
//   \Log::info("RES:".json_encode($res));
  }
}
