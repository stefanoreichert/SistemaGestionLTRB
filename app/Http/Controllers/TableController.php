<?php

namespace App\Http\Controllers;

use App\Models\Table;

class TableController extends Controller
{
    /**
     * Menú principal: grilla de 40 mesas con estado libre/ocupada.
     */
    public function index()
    {
        $tables   = Table::with('activeOrder')->orderBy('number')->get();
        $occupied = $tables->where('status', 'occupied')->count();
        $free     = $tables->where('status', 'free')->count();

        return view('tables.index', compact('tables', 'occupied', 'free'));
    }
}
