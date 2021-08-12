<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CourseDetail;
use App\Course;

class CourseDetailsController extends Controller
{
    public function __construct()
  {
    $this->middleware(['company']);
  }

  public function index(Course $course)
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $courseDetails = $course->course_details();
      $count = $courseDetails->count();
      $courseDetails = $courseDetails->paginate(request()->rowsPerPage)->toArray();
      $courseDetails = $courseDetails['data'];
    } else {
      $courseDetails = $course->course_details; 
      $count = $courseDetails->count();
    }

    return response()->json([
      'data'     =>   $courseDetails,
      'count'    =>   $count
    ], 200);
  }

  /*
   * To store a new course_branch
   *
   *@
   */
  public function store(Request $request, Course $course)
  {
    $request->validate([
      'title'       =>  'required',
      'description' =>  'required',
    ]);

    $courseDetail = new CourseDetail(request()->all());
    $course->course_details()->save($courseDetail);

    return response()->json([
      'data'    =>  $courseDetail
    ], 201); 
  }

  /*
   * To view a single course_branch
   *
   *@
   */
  public function show(Course $course, CourseDetail $courseDetail)
  {
    return response()->json([
      'data'   =>  $courseDetail,
      'success' =>  true
    ], 200);   
  }

  /*
   * To update a course_branch
   *
   *@
   */
  public function update(Request $request, Course $course, CourseDetail $courseDetail)
  {
    $courseDetail->update($request->all());
      
    return response()->json([
      'data'  =>  $courseDetail
    ], 200);
  }

  public function destroy(Course $course, $id)
  {
    $courseDetail = CourseDetail::find($id);
    $courseDetail->delete();

    return response()->json([
      'message' =>  'Deleted'  
    ], 204);
  }
}
