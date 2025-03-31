<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Issue;
use App\Models\TaskUpdate;
use App\Models\Comment;
class HomeController extends Controller
{
    //
    public function index()
    {
        return view('home');
    }
  
}
