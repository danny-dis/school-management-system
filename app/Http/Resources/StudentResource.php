<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class StudentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'religion' => $this->religion,
            'blood_group' => $this->blood_group,
            'nationality' => $this->nationality,
            'photo' => $this->photo ? asset('storage/photos/students/' . $this->photo) : null,
            'email' => $this->email,
            'phone_no' => $this->phone_no,
            'extra_activity' => $this->extra_activity,
            'note' => $this->note,
            'father_name' => $this->father_name,
            'father_phone_no' => $this->father_phone_no,
            'mother_name' => $this->mother_name,
            'mother_phone_no' => $this->mother_phone_no,
            'guardian' => $this->guardian,
            'guardian_phone_no' => $this->guardian_phone_no,
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'registrations' => $this->when($this->relationLoaded('registration'), function () {
                return $this->registration->map(function ($registration) {
                    return [
                        'id' => $registration->id,
                        'regi_no' => $registration->regi_no,
                        'roll_no' => $registration->roll_no,
                        'class' => $registration->class ? [
                            'id' => $registration->class->id,
                            'name' => $registration->class->name
                        ] : null,
                        'section' => $registration->section ? [
                            'id' => $registration->section->id,
                            'name' => $registration->section->name
                        ] : null,
                        'academic_year' => $registration->academicYear ? [
                            'id' => $registration->academicYear->id,
                            'title' => $registration->academicYear->title
                        ] : null,
                        'status' => $registration->status
                    ];
                });
            })
        ];
    }
}
