<?php

namespace App\Http\Controllers;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
        $this->user = $user;
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users,email',
            'password'  => 'required|min:6|confirmed',
        ]);
        if ($validate->fails()){
            return response()->json([
                'message' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $credentials = $request->all();

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'ok',
            'token' => [
                'access_token' => $token,
            ],
            //'expires_in' => auth()->factory()->getTTL() * 60,
        ]);

    }

    public function register(Request $request){
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password'  => 'required|min:6|confirmed',
        ]);

        if ($validate->fails()){
            return response()->json([
                'message' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $fields = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        $user = $this->user->register($fields);

        $token = Auth::login($user);

        return response()->json([
            'data' => [
                'nombre' => $user->name,
                'apellido' => $user->last_name,
                'correo' => $user->email
            ],
            'token' => $token,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'token' => [
                'access_token' => Auth::refresh(),
            ]
        ]);
    }

}
