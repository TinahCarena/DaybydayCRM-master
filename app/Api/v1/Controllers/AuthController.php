<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /* public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            //
            $user = Auth::user();
            $token = $user->createToken('SpringBootAccess')->accessToken;
            return response()->json(['token' => $token]);
        }

        //dd($credentials);

        return response()->json(Auth::attempt($credentials));
    }  */

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            // L'utilisateur est authentifié, mais il n'y a pas de token créé
            return response()->json([
                'message' => 'Authenticated successfully',
                'success' => true
            ], 200);
        }
    
        // Retourner une réponse JSON cohérente en cas d'erreur
        return response()->json([
            'error' => 'Authentication failed',
            'success' => false
        ], 401);
    }
    

    /* public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Utiliser le guard 'api' explicitement
        if (Auth::guard('api')->attempt($credentials)) {
            $user = Auth::guard('api')->user();
            $token = $user->createToken('SpringBoot')->accessToken;
            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'Email ou mot de passe incorrect'], 401);
    } */
}