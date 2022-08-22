<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\AssignmentClasscode;
use App\AssignmentExtension;
use App\AssignmentQuestion;
use App\AssignmentQuestionOption;
use Illuminate\Http\Request;

class AssignmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
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
                ->get();
        } else if ($roleName == 'TEACHER') {
            $assignments = request()->company->assignments()
                ->where('created_by_id', '=', request()->user()->id)
                ->get();
        } else if ($roleName == 'STUDENT') {
            $assignments = [];
            $userClascodes = request()->user()->user_classcodes;
            foreach ($userClascodes as $userClascode) {
                $classcode = $userClascode->classcode_id;

                $classcodeAssignments = request()->company->assignments()
                    ->whereHas('assignment_classcodes', function ($q) use ($classcode) {
                        $q->where('classcode_id', '=', $classcode);
                    })
                    ->with('my_results', 'my_assignment_classcodes')
                    ->get();
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
        // dd($request->);
        $request->validate([
            'assignment_type'  =>  'required',
        ]);

        if ($request->id == null || $request->id == '') {
            // Save Assignment
            $assignment = new Assignment(request()->all());
            $request->company->assignments()->save($assignment);
            // Save Assignment Classcode
            if (isset($request->assignment_classcodes))
                foreach ($request->assignment_classcodes as $classcode) {
                    $assignment_classcode = new AssignmentClasscode($classcode);
                    $assignment->assignment_classcodes()->save($assignment_classcode);
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
                    $classcode->delete();
                }

            // Update Assignmnet Classcode
            if (isset($request->assignment_classcodes))
                foreach ($request->assignment_classcodes as $classcode) {
                    if (!isset($classcode['id'])) {
                        $assignment_classcode = new AssignmentClasscode($classcode);
                        $assignment->assignment_classcodes()->save($assignment_classcode);
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
}
