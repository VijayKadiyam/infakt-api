<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    /*
     * To get all Profile
       *
     *@
     */

    public function masters(Request $request)
    {
        $usersController = new UsersController();
        $request->request->add(['role_id' => '3']);
        $usersResponse = $usersController->index($request);
        
        $industries = [
            ['id' => 'Industry 1', 'text' => 'Industry 1'],
            ['id' => 'Industry 2', 'text' => 'Industry 2'],
            ['id' => 'Industry 3', 'text' => 'Industry 3'],
        ];

        $productOffered = [
            ['id' => 'Product Offered 1', 'text' => 'Product Offered 1'],
            ['id' => 'Product Offered 2', 'text' => 'Product Offered 2'],
            ['id' => 'Product Offered 3', 'text' => 'Product Offered 3'],
        ];

        return response()->json([
            'users'  =>  $usersResponse->getData()->data,
            'industries'  =>  $industries,
            'productOffered'  =>  $productOffered,
        ], 200);
    }

    public function index(Request $request)
    {
        $profiles = $request->company->profiles()->get();

        return response()->json([
            'data'     =>  $profiles,
            'success'   =>  true,
        ], 200);
    }

    /*
     * To store a new Profile
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    =>  'required',
        ]);



        $profile = new Profile($request->all());
        $request->company->profiles()->save($profile);

        $photoPath = '';
        if ($request->hasFile('photoPath')) {
            $file = $request->file('photoPath');
            $name = $request->filename ?? 'photo.';
            $name = $name . $file->getClientOriginalExtension();;
            $photoPath = 'profile/photo/' .  $request->profileid . '/' . $name;
            Storage::disk('local')->put($photoPath, file_get_contents($file), 'public');

            $profile = Profile::where('id', '=', request()->profileid)->first();
            $profile->photo_1_path = $photoPath;
            $profile->update();
        }
        return response()->json([
            'data'    =>  $profile
        ], 201);
    }

    /*
     * To view a single Profile
     *
     *@
     */
    public function show(Profile $profile)
    {
        return response()->json([
            'data'   =>  $profile,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a Profile
     *
     *@
     */
    public function update(Request $request, Profile $profile)
    {

        $profile->update($request->all());

        return response()->json([
            'data'  =>  $profile
        ], 200);
    }

    public function destroy($id)
    {
        $profile = Profile::find($id);
        $profile->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
