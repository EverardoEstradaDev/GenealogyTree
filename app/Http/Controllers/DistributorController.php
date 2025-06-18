<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Distributor;
use App\Models\Tree;

class DistributorController extends Controller
{
    function openFile(Request $request)
    {
        $request->all();
        $file = $request->file('document');

        $jsonString = file_get_contents($file->path());
        $jsonData = json_decode($jsonString, true);

        $tree = new Tree($jsonData);
        
        // Store tree in session instead of trying to pass it directly
        session(['genealogy_tree' => $tree]);
        
        return redirect()->route('viewer');
    }

    function viewer()
    {
        // Get tree from session
        $tree = session('genealogy_tree');
        
        return Inertia::render('Viewer', [
            'tree' => $tree
        ]);
    }
}
