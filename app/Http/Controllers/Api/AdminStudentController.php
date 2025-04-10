<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\StoreStudentRequest;
use App\Http\Requests\Api\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\StudentCollection;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminStudentController extends BaseApiController
{
    /**
     * @var StudentService
     */
    protected $studentService;

    /**
     * AdminStudentController constructor.
     *
     * @param StudentService $studentService
     */
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of the students.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->except(['page', 'per_page']);

        $students = $this->studentService->getStudentsWithFilters($filters, $perPage);

        return $this->collectionResponse(new StudentCollection($students), 'Students retrieved successfully');
    }

    /**
     * Store a newly created student in storage.
     *
     * @param StoreStudentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreStudentRequest $request)
    {
        try {

            $student = $this->studentService->create($request->all());

            if (!$student) {
                return $this->serverErrorResponse('Failed to create student');
            }

            return $this->resourceResponse(new StudentResource($student), 'Student created successfully', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('An error occurred while creating the student', $e->getMessage());
        }
    }

    /**
     * Display the specified student.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\StudentResource
     */
    public function show($id)
    {
        $student = $this->studentService->find($id);

        if (!$student) {
            return $this->notFoundResponse('Student not found');
        }

        return $this->resourceResponse(new StudentResource($student), 'Student retrieved successfully');
    }

    /**
     * Update the specified student in storage.
     *
     * @param UpdateStudentRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        try {
            $student = $this->studentService->find($id);

            if (!$student) {
                return $this->notFoundResponse('Student not found');
            }

            $student = $this->studentService->update($request->all(), $id);

            if (!$student) {
                return $this->serverErrorResponse('Failed to update student');
            }

            return $this->resourceResponse(new StudentResource($student), 'Student updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('An error occurred while updating the student', $e->getMessage());
        }
    }

    /**
     * Remove the specified student from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $student = $this->studentService->find($id);

            if (!$student) {
                return $this->notFoundResponse('Student not found');
            }

            $result = $this->studentService->delete($id);

            if (!$result) {
                return $this->serverErrorResponse('Failed to delete student');
            }

            return $this->successResponse(null, 'Student deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('An error occurred while deleting the student', $e->getMessage());
        }
    }

    /**
     * Get students by class.
     *
     * @param int $classId
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getStudentsByClass($classId)
    {
        $students = $this->studentService->getStudentsByClass($classId);

        return $this->collectionResponse(StudentResource::collection($students), 'Students retrieved successfully');
    }

    /**
     * Get students by section.
     *
     * @param int $sectionId
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getStudentsBySection($sectionId)
    {
        $students = $this->studentService->getStudentsBySection($sectionId);

        return $this->collectionResponse(StudentResource::collection($students), 'Students retrieved successfully');
    }
}
