<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\User;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.admin');
    }

    public function index(Request $request)
    {
        $resultsPerPage = $request->query('resultsPerPage', 10);
        $users = User::paginate($resultsPerPage);
    
        return view('registerlisting', [
            'users' => $users,
        ]);
    }
    


    public function bulkDelete(Request $request)
    {

        
        $selectedUsers = $request->input('selected_users_delete');
    
        // Perform bulk delete action based on the selected user IDs
        if (strlen($selectedUsers) > 1) {
            $selectedUsers = explode(",", $selectedUsers);
        } else {
            $selectedUsers = [$selectedUsers];
        }

        foreach ($selectedUsers as $userId) {
            if(Auth::id() == $userId){
                return redirect()->back()->with('error', 'Cannot delete user in session!');
            }
        }

    
        foreach ($selectedUsers as $userId) {

            if (is_numeric($userId)) {
                // Delete the user with the given ID from the database
                User::findOrFail($userId)->delete();
            }
        }
    
        // Redirect back to the dashboard
        return redirect()->route('index')->with('success', 'Delete completed successfully.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        

        return view('show', [
            'user' => $user,
        ]);
    }

    public function edit($id)
    {

        $user = User::find($id);

        return view('editar', [
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
        ]);

        // Update user information
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->save();

        return redirect()->route('home');
    }

    public function deleteProfile($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('index')->with('success', 'Delete completed successfully.');
    }
    

}
