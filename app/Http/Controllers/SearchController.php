<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
    
        // Start with the base query
        $results = User::where('name', 'LIKE', "%{$query}%");
    
        // Check if name results are empty, then search by model
        if ($results->count() === 0) {
            $results = User::where('email', 'LIKE', "%{$query}%");
        }
    
        // Retrieve the search results
        $resultsPerPage = $request->query('resultsPerPage', 10);
        $results = $results->paginate($resultsPerPage);
    
        // Append the query parameter for pagination
        $results->appends(['query' => $query]);
    
        return view('registerlisting', ['users' => $results]);
    }
    
    

    public function suggest(Request $request)
    {
        $query = $request->input('query');
        $suggestedTerms = User::where('name', 'LIKE', "%{$query}%")
        ->limit(5)
        ->pluck('name');

        return response()->json($suggestedTerms);
    }
}