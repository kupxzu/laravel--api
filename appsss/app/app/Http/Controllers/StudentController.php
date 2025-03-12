<?php

namespace App\Http\Controllers;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function index(){

        Student::all();

    }

    public function store(Request $request){

        return Student::create($request -> all());

    }
}
