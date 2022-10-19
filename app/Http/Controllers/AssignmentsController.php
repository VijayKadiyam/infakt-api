<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\AssignmentClasscode;
use App\AssignmentExtension;
use App\AssignmentQuestion;
use App\AssignmentQuestionOption;
use App\Classcode;
use App\Notification;
use App\Role;
use App\User;
use Illuminate\Http\Request;

class AssignmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company', 'auth:api']);
        // ->except(['store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roleName = request()->user()->roles[0]->name;
        if ($roleName == 'ADMIN') {
            $assignments = request()->company->assignments()
                ->with('my_results', 'my_assignment_classcodes', 'my_assignment_extensions')
                ->get();
        } else if ($roleName == 'TEACHER') {
            $assignments = request()->company->assignments()
                ->where('created_by_id', '=', request()->user()->id)
                ->with('my_results', 'my_assignment_classcodes', 'my_assignment_extensions');
            if (request()->classcode_id) {
                $assignments = $assignments->wherehas('my_assignment_classcodes', 'my_assignment_extensions', function ($uc) {
                    $uc->where('classcode_id', '=', request()->classcode_id);
                });
            }
            $assignments = $assignments->get();
        } else if ($roleName == 'STUDENT') {
            $assignments = [];
            $userClascodes = request()->user()->user_classcodes;
            foreach ($userClascodes as $userClascode) {
                $classcode = $userClascode->classcode_id;

                $classcodeAssignments = request()->company->assignments()
                    ->whereHas('assignment_classcodes', function ($q) use ($classcode) {
                        $q->where('classcode_id', '=', $classcode);
                    })
                    ->with('my_results', 'my_assignment_classcodes', 'my_assignment_extensions')
                    ->get();
                // return $assignments;
                // array_merge($assignments, $classcodeAssignments);
                $assignments = [...$assignments, ...$classcodeAssignments];
            }
        }

        return response()->json([
            'data'  =>  $assignments,
            'count' =>   sizeof($assignments),
            'success' =>  true,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'assignment_type'  =>  'required'
    //     ]);

    //     $assignment = new Assignment($request->all());
    //     $request->company->assignments()->save($assignment);

    //     return response()->json([
    //         'data'  =>  $assignment
    //     ], 201);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'assignment_type'  =>  'required',
            'assignment_classcodes.*.end_date'  =>  'required',
        ]);
        if ($request->id == null || $request->id == '') {
            // Save Assignment
            $assignment = new Assignment(request()->all());
            $assignment->save();
            // Save Assignment Classcode
            if (isset($request->assignment_classcodes))
                foreach ($request->assignment_classcodes as $classcode) {
                    $assignment_classcode = new AssignmentClasscode($classcode);
                    $assignment->assignment_classcodes()->save($assignment_classcode);
                    // Create Notification Log
                    $c = Classcode::find($assignment_classcode->classcode_id);
                    $students = $c->students;
                    foreach ($students as $key => $user) {
                        $description = "A new Assignment [ $assignment->assignment_title ] has been posted for Classcode [ $c->classcode ].";
                        $notification_data = [
                            'user_id' => $user->id,
                            'description' => $description
                        ];
                        $notifications = new Notification($notification_data);
                        $request->company->notifications()->save($notifications);
                    }
                }
            // ---------------------------------------------------
            // Save Assignment Questions 
            if (isset($request->assignment_questions))
                foreach ($request->assignment_questions as $question) {
                    // dd($question['assignment_question']);
                    $assignment_question = new AssignmentQuestion($question);
                    $assignment->assignment_questions()->save($assignment_question);

                    // Save Assignment Question Options
                    $question_options = $question['assignment_question_options'];
                    // dd($class_codes);
                    if (isset($question_options))
                        foreach ($question_options as $option) {
                            $assignment_question_option = new AssignmentQuestionOption($option);
                            $assignment_question->assignment_question_options()->save($assignment_question_option);
                        }
                    // ---------------------------------------------------

                }
            // ---------------------------------------------------
            // Save Assignment Extensions
            if (isset($request->assignment_extensions))
                foreach ($request->assignment_extensions as $extension) {
                    $assignment_extension = new AssignmentExtension($extension);
                    $assignment->assignment_extensions()->save($assignment_extension);
                }
            // ---------------------------------------------------
        } else {
            // Update Assignmnet
            $assignment = Assignment::find($request->id);
            $assignment->update($request->all());

            // Check if Assignmnet Classcode deleted
            if (isset($request->assignment_classcodes)) {
                $classcodeIdResponseArray = array_pluck($request->assignment_classcodes, 'id');
            } else
                $classcodeIdResponseArray = [];
            $assignmentId = $assignment->id;
            $classcodeIdArray = array_pluck(AssignmentClasscode::where('assignment_id', '=', $assignmentId)->get(), 'id');
            $differenceAssignmentClasscodeIds = array_diff($classcodeIdArray, $classcodeIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceAssignmentClasscodeIds)
                foreach ($differenceAssignmentClasscodeIds as $differenceAssignmentClasscodeId) {
                    $classcode = AssignmentClasscode::find($differenceAssignmentClasscodeId);
                    $c = Classcode::find($classcode['classcode_id']);
                    $students = $c->students;
                    $classcode->delete();
                    // Create Notification Log
                    foreach ($students as $key => $user) {
                        $description = "An existing Assignment [ $assignment->assignment_title ] has been removed for Classcode [ $c->classcode ].";
                        $notification_data = [
                            'user_id' => $user->id,
                            'description' => $description
                        ];
                        $notifications = new Notification($notification_data);
                        $request->company->notifications()->save($notifications);
                    }
                }

            // Update Assignmnet Classcode
            if (isset($request->assignment_classcodes))
                foreach ($request->assignment_classcodes as $classcode) {
                    if (!isset($classcode['id'])) {
                        $assignment_classcode = new AssignmentClasscode($classcode);
                        $assignment->assignment_classcodes()->save($assignment_classcode);
                        // Create Notification Log
                        $c = Classcode::find($classcode['classcode_id']);
                        $students = $c->students;
                        foreach ($students as $key => $user) {
                            $description = "A new Assignment [ $assignment->assignment_title ] has been posted for Classcode [ $c->classcode ].";
                            $notification_data = [
                                'user_id' => $user->id,
                                'description' => $description
                            ];
                            $notifications = new Notification($notification_data);
                            $request->company->notifications()->save($notifications);
                        }
                    } else {
                        $assignment_classcode = AssignmentClasscode::find($classcode['id']);
                        $assignment_classcode->update($classcode);
                    }
                }

            // ---------------------------------------------------

            // Check if Assignment Question deleted
            if (isset($request->assignment_questions))
                $questionIdResponseArray = array_pluck($request->assignment_questions, 'id');
            else
                $questionIdResponseArray = [];
            $assignmentId = $assignment->id;
            $questionIdArray = array_pluck(AssignmentQuestion::where('assignment_id', '=', $assignmentId)->get(), 'id');
            $differenceAssignmentQuestionIds = array_diff($questionIdArray, $questionIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceAssignmentQuestionIds)
                foreach ($differenceAssignmentQuestionIds as $differenceAssignmentQuestionId) {
                    $question = AssignmentQuestion::find($differenceAssignmentQuestionId);
                    $question->delete();
                }

            // Update Assignment Question

            if (isset($request->assignment_questions))

                foreach ($request->assignment_questions as $question) {
                    if (!isset($question['id'])) {
                        $assignmentQuestion = new AssignmentQuestion($question);
                        $assignment->assignment_questions()->save($assignmentQuestion);
                    } else {
                        $assignmentQuestion = AssignmentQuestion::find($question['id']);
                        $assignmentQuestion->update($question);
                    }

                    // Check if Assignment Question Option deleted
                    $assignment_question_options = $question['assignment_question_options'];
                    if (isset($assignment_question_options))
                        $optionIdResponseArray = array_pluck($assignment_question_options, 'id');
                    else
                        $optionIdResponseArray = [];
                    $questionId = $assignmentQuestion->id;
                    $optionIdArray = array_pluck(AssignmentQuestionOption::where('assignment_question_id', '=', $questionId)->get(), 'id');
                    $differenceQuestionIds = array_diff($optionIdArray, $optionIdResponseArray);
                    // Delete which is there in the database but not in the response
                    if ($differenceQuestionIds)
                        foreach ($differenceQuestionIds as $differenceQuestionId) {
                            $option = AssignmentQuestionOption::find($differenceQuestionId);
                            $option->delete();
                        }
                    // Update Assignment Question Option
                    if (isset($options))
                        foreach ($options as $question_option) {
                            if (!isset($question_option['id'])) {
                                $option = new AssignmentQuestionOption($question_option);
                                $question->assignment_question_options()->save($option);
                            } else {
                                $assignmentQuestionOption = AssignmentQuestionOption::find($question_option['id']);
                                $assignmentQuestionOption->update($question_option);
                            }
                        }
                }

            // ---------------------------------------------------

            // Check if Assignmnet Extension deleted
            if (isset($request->assignment_extensions)) {
                $extensionIdResponseArray = array_pluck($request->assignment_extensions, 'id');
            } else
                $extensionIdResponseArray = [];
            $assignmentId = $assignment->id;
            $extensionIdArray = array_pluck(AssignmentExtension::where('assignment_id', '=', $assignmentId)->get(), 'id');
            $differenceAssignmentExtensionIds = array_diff($extensionIdArray, $extensionIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceAssignmentExtensionIds)
                foreach ($differenceAssignmentExtensionIds as $differenceAssignmentExtensionId) {
                    $extension = AssignmentExtension::find($differenceAssignmentExtensionId);
                    $extension->delete();
                }

            // Update Assignmnet Extension
            if (isset($request->assignment_extensions))
                foreach ($request->assignment_extensions as $extension) {
                    if (!isset($extension['id'])) {
                        $assignment_extension = new AssignmentExtension($extension);
                        $assignment->assignment_extensions()->save($assignment_extension);
                    } else {
                        $assignment_extension = AssignmentExtension::find($extension['id']);
                        $assignment_extension->update($extension);
                    }
                }

            // ---------------------------------------------------

        }

        $assignment->content = $assignment->content;
        $assignment->assignment_classcodes = $assignment->assignment_classcodes;
        $assignment->assignment_questions = $assignment->assignment_questions;
        $assignment->assignment_extensions = $assignment->assignment_extensions;
        $assignment->user_assignments = $assignment->user_assignments;

        return response()->json([
            'data'  =>  $assignment
        ], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Assignment $assignment)
    {
        $assignment->content = $assignment->content;
        $assignment->assignment_classcodes = $assignment->assignment_classcodes;
        $assignment->assignment_questions = $assignment->assignment_questions;
        $assignment->assignment_extensions = $assignment->assignment_extensions;
        $assignment->user_assignments = $assignment->user_assignments;

        return response()->json([
            'data'  =>  $assignment
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'assignment_type'  =>  'required',
        ]);

        $assignment->update($request->all());

        return response()->json([
            'data'  =>  $assignment
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assignment = request()->company->assignments()
            ->where('id', $id)->first();
        $assignment->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }

    public function type_overview()
    {
        $assignments = request()->company->assignments()
            ->where('created_by_id', '=', request()->user()->id)
            ->with('my_results', 'my_assignment_classcodes');
        if (request()->classcode_id) {
            $assignments = $assignments->wherehas('my_assignment_classcodes', function ($uc) {
                $uc->where('classcode_id', '=', request()->classcode_id);
            });
        }
        $assignments = $assignments->get();
        $subjective_assignment_count = 0;
        $objective_assignment_count = 0;
        $document_assignment_count = 0;
        foreach ($assignments as $key => $assignment) {
            switch ($assignment->assignment_type) {
                case 'SUBJECTIVE':
                    $subjective_assignment_count++;
                    break;

                case 'OBJECTIVE':
                    $objective_assignment_count++;
                    break;

                case 'DOCUMENT':
                    $document_assignment_count++;
                    break;

                default:
                    # code...
                    break;
            }
        }
        $total_count = sizeof($assignments);
        $subjective_assignment_percantage =  $subjective_assignment_count ? ($subjective_assignment_count / $total_count) * 100 : 0;
        $objective_assignment_percantage =  $objective_assignment_count ? ($objective_assignment_count / $total_count) * 100 : 0;
        $document_assignment_percentage = $document_assignment_count ? ($document_assignment_count / $total_count) * 100 : 0;
        $data = [
            'subjective_assignment_count' => $subjective_assignment_count,
            'objective_assignment_count' => $objective_assignment_count,
            'document_assignment_count' => $document_assignment_count,
            'subjective_assignment_percantage' => round($subjective_assignment_percantage, 2),
            'objective_assignment_percantage' => round($objective_assignment_percantage, 2),
            'document_assignment_percentage' => round($document_assignment_percentage, 2),
            'total_count' => $total_count,
        ];
        return response()->json([
            'data'  =>  $data,
            'count' =>   sizeof($assignments),
            'success' =>  true,
        ], 200);
    }
    public function assignment_wise_performance_overview()
    {
        $assignments = request()->company->assignments()
            ->where('created_by_id', '=', request()->user()->id)
            ->with('my_assignment_classcodes', 'user_assignments');
        if (request()->classcode_id) {
            $assignments = $assignments->wherehas('my_assignment_classcodes', function ($uc) {
                $uc->where('classcode_id', '=', request()->classcode_id);
            });
        }
        if (request()->assignment_id) {
            $assignments = $assignments->where('assignments.id', request()->assignment_id);
        }
        $assignments = $assignments->get();
        $top_students_count = 0;
        $avg_students_count = 0;
        $below_avg_students_count = 0;
        $weak_students_count = 0;
        $total_scored = 0;
        foreach ($assignments as $key => $assignment) {
            $maximum_marks = $assignment->maximum_marks;
            $user_assignments = $assignment->user_assignments;
            foreach ($user_assignments as $key => $ua) {
                $score = $ua->score;
                $percantage = ($score / $maximum_marks) * 100;
                switch (true) {
                    case ($percantage >= 76):
                        $grade = 'A';
                        $top_students_count++;
                        break;
                    case ($percantage >= 60 && $percantage < 76):
                        $grade = 'B';
                        $avg_students_count++;
                        break;
                    case ($percantage >= 59 && $percantage < 36):
                        $grade = 'C';
                        $below_avg_students_count++;
                        break;
                    case ($percantage < 36):
                        $grade = 'D';
                        $weak_students_count++;
                        break;
                }
                $total_scored += $score;
            }
        }

        // $total_count = sizeof($assignments);
        // $subjective_assignment_percantage =  $subjective_assignment_count ? ($subjective_assignment_count / $total_count) * 100 : 0;
        // $objective_assignment_count =  $objective_assignment_count ? ($objective_assignment_count / $total_count) * 100 : 0;
        // $document_assignment_count = $document_assignment_count ? ($document_assignment_count / $total_count) * 100 : 0;
        $data = [
            'total_scored' => $total_scored,
            'top_students_count' => $top_students_count,
            'avg_students_count' => $avg_students_count,
            'below_avg_students_count' => $below_avg_students_count,
            'weak_students_count' => $weak_students_count,
            // 'total_count' => $total_count,
        ];
        return response()->json([
            'data'  =>  $data,
            'success' =>  true,
        ], 200);
    }
    public function student_wise_performance_overview()
    {
        $users = User::where('is_deleted', false)->with('roles', 'user_assignments');
        $role = Role::find(5);
        $users = $users->with('roles')->whereHas('roles', function ($q) use ($role) {
            $q->where('name', '=', $role->name);
        });
        if (request()->classcode_id) {
            $users = $users->whereHas('user_classcodes', function ($uc) {
                $uc->where('classcode_id', '=', request()->classcode_id);
            });
        }
        $users = $users->get();

        $top_students_count = 0;
        $avg_students_count = 0;
        $below_avg_students_count = 0;
        $weak_students_count = 0;
        $total_maximum_marks = 0;
        foreach ($users as $key => $user) {
            $total_scored = 0;
            $average = 0;
            $user_assignments = $user->user_assignments;
            $assignment_submitted = sizeof($user_assignments);
            foreach ($user_assignments as $key => $ua) {
                $total_maximum_marks += $ua->assignment->maximum_marks;
                $score = $ua->score;
                $total_scored += $score;
            }
            if ($assignment_submitted != 0) {
                $average = $total_scored / $assignment_submitted;
            }
            switch (true) {
                case ($average >= 76):
                    $grade = 'A';
                    $top_students_count++;
                    break;
                case ($average >= 60 && $average < 76):
                    $grade = 'B';
                    $avg_students_count++;
                    break;
                case ($average >= 59 && $average < 36):
                    $grade = 'C';
                    $below_avg_students_count++;
                    break;
                case ($average < 36):
                    $grade = 'D';
                    $weak_students_count++;
                    break;
            }
        }

        $total_students = sizeof($users);
        $top_students_percantage =  $top_students_count ? ($top_students_count / $total_students) * 100 : 0;
        $avg_students_percantage =  $avg_students_count ? ($avg_students_count / $total_students) * 100 : 0;
        $below_avg_students_percantage = $below_avg_students_count ? ($below_avg_students_count / $total_students) * 100 : 0;
        $weak_students_percantage = $weak_students_count ? ($weak_students_count / $total_students) * 100 : 0;
        $data = [
            'total_students' => sizeOf($users),
            'top_students_count' => $top_students_count,
            'avg_students_count' => $avg_students_count,
            'below_avg_students_count' => $below_avg_students_count,
            'weak_students_count' => $weak_students_count,
            'top_students_percantage' => round($top_students_percantage),
            'avg_students_percantage' => round($avg_students_percantage),
            'below_avg_students_percantage' => round($below_avg_students_percantage),
            'weak_students_percantage' => round($weak_students_percantage),
        ];
        return response()->json([
            'data'  =>  $data,
            'success' =>  true,
        ], 200);
    }
}
