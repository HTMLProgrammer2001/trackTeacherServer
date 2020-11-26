<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserActions\EditMeRequest;
use App\Http\Requests\UserActions\LoginRequest;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use App\Services\PhotoUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActions extends Controller
{
    /**
     * @var PhotoUploader
     */
    private $photoUploader;

    /**
     * UserActions constructor.
     * @param PhotoUploader $photoUploader
     */
    public function __construct(PhotoUploader $photoUploader)
    {
        $this->photoUploader = $photoUploader;
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request){
        $credentials = $request->only(['email', 'password']);

        if(!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Wrong email or password'
            ], 401);
        }

        /**
         * @var $user User
         */
        $user = Auth::user();
        $token = $user->getToken();

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
            'message' => 'User was successfully logged in'
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request){
        $user = $request->user();
        $user->token()->revoke();

        return response()->json([
            'message' => 'User was successfully logged out'
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function getMe(){
        $user = auth('api')->user();

        return response()->json([
           'user' => $user ? new UserResource($user) : null
        ]);
    }

    /**
     * @param EditMeRequest $request
     * @return JsonResponse
     */
    public function editMe(EditMeRequest $request){
        $fields = ['birthday', 'email', 'address', 'phone'];
        $user = $request->user();

        //save attributes
        $user->fill($request->only($fields));
        $user->generatePassword($request->input('password'));

        if($request->file('avatar')) {
            //save new avatar
            $path = $this->photoUploader->uploadAvatar($request->file('avatar'));
            $user->avatar = $path;
        }

        $user->save();

        //return new user in json format
        return response()->json([
           'newUser' => new UserResource($user)
        ]);
    }
}
