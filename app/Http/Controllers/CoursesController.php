<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;

class CoursesController extends Controller
{
    public function __construct()
  {
    $this->middleware(['company']);
  }

  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $courses = request()->company->courses();
      $count = $courses->count();
      $courses = $courses->paginate(request()->rowsPerPage)->toArray();
      $courses = $courses['data'];
    } else {
      $courses = request()->company->courses; 
      $count = $courses->count();
    }

    return response()->json([
      'data'     =>  $courses,
      'count'    =>   $count,
      'success'   =>  true,
    ], 200);
  }

  /*
   * To store a new course
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'course_name'        =>  'required',
    ]);

    $course = new Course(request()->all());
    $request->company->courses()->save($course);

    return response()->json([
      'data'    =>  $course
    ], 201); 
  }

  /*
   * To view a single course
   *
   *@
   */
  public function show(Course $course)
  {
    return response()->json([
      'data'   =>  $course,
      'success' =>  true
    ], 200);   
  }

  /*
   * To update a course
   *
   *@
   */
  public function update(Request $request, Course $course)
  {
    $course->update($request->all());
      
    return response()->json([
      'data'  =>  $course
    ], 200);
  }

  public function destroy($id)
  {
    $course = Course::find($id);
    $course->delete();

    return response()->json([
      'message' =>  'Deleted'  
    ], 204);
  }
}
