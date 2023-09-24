<?php

namespace App\Http\Controllers\Api;

use App\Models\Form;
use Illuminate\Http\Request;
use App\Models\AllowedDomain;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'invalid field',
                'status' =>  $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'message' => ['Email or password incorrect'],
            ]);
        }

        $user = $request->user(); 
        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json([
            "message" => 'Login success',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'accessToken' => $token
            ]
            ],200);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout success']);

    }

    public function get(Request $request){

    $validator= Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:forms|regex:/^[a-zA-Z0-9.-]+$/',
        'allowed_domains' => 'required|array',
        'description' => 'nullable|string',
        'limit_one_response' => 'boolean',
    ]);

    if ($validator->fails()) {
        // Handle validation errors here
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }
    
   
    $validatedData = $validator->validated();

    $form = Form::create([
        'name' => $validatedData['name'],
        'slug' => $validatedData['slug'],
        'description' => $validatedData['description'],
        'limit_one_response' => $validatedData['limit_one_response'],
        'creator_id' => auth()->id(),
    ]);

   
  

    // Create and associate allowed domains with the form
    foreach ($validatedData['allowed_domains'] as $domain) {
        $allowedDomain = AllowedDomain::create([
            'domain' => $domain,
            'form_id' => $form->id, // Link to the newly created form
        ]);
    }

    return response()->json([
        'message' => 'Create form success',
        'form' => $form,
    ], 200);
    }

    public function getAll(){
        $data = Costumer::all();

        return $data;
    }

    public function search(Request $request){
        
    }

}
