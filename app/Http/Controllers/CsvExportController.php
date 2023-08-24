<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use APP\Models\User;
use League\Csv\Writer;

class CsvExportController extends Controller
{
    public function export_all()
    {
        $data = User::all();

        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        // Add a header row to the CSV
        $csv->insertOne(['Id', 'Name', 'Email', 'Cargo']);

        // Insert data rows into the CSV
        foreach ($data as $user) {
            $csv->insertOne([$user->id, $user->name, $user->email, $user->role]); 
        }

        // Set the response headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export.csv"',
        ];

        // Return the CSV as a response
        return response()->stream(
            function () use ($csv) {
                echo $csv;
            },
            200,
            $headers
        );
    }

    public function export(Request $request)
    {
        $selectedUsersCsv = $request->input('selected_users_csv');

        if (empty($selectedUsersCsv)) {
            return redirect()->back();
        }

        $selectedUserIds = explode(',', $selectedUsersCsv);
    
        $data = User::whereIn('id', $selectedUserIds)->get();
    
        $csv = Writer::createFromFileObject(new \SplTempFileObject());
    
        // Add a header row to the CSV
        $csv->insertOne(['Id', 'Name', 'Email', 'Cargo']);
    
        // Insert data rows into the CSV
        foreach ($data as $user) {
            $csv->insertOne([$user->id, $user->name, $user->email, $user->role]);
        }
    
        // Set the response headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export.csv"',
        ];
    
        // Return the CSV as a response
        return response()->stream(
            function () use ($csv) {
                echo $csv;
            },
            200,
            $headers
        );
    }
    
}
