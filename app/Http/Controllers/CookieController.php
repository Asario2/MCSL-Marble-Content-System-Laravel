    <?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CookierController extends Controller
{
    public function SetCkie(Request $request)
    {
        return response()->json(true);
    }
}
