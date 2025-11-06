<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'new_clock_in' => ['required', 'date_format:H:i'],
            'new_clock_out' => ['required', 'date_format:H:i', 'after:start_time'],
            'new_break1_start' => ['nullable', 'date_format:H:i'],
            'new_break1_end' => ['nullable', 'date_format:H:i'],
            'new_break2_start' => ['nullable', 'date_format:H:i'],
            'new_break2_end' => ['nullable', 'date_format:H:i'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input ('new_clock_in');
            $end = $this->input ('new_clock_out');
            $break1Start = $this->input('new_break1_start');
            $break1End = $this->input('new_break1_end');

            if ($start && $end && strtotime($end) <= strtotime($start)) {
                $validator->errors()->add('start_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            if ($break1Start && $start && $end) {
                if (strtotime($break1Start) < strtotime($start) || strtotime($break1Start) > strtotime($end)) {
                    $validator->errors()->add('new_break1_start','休憩時間が不適切な値です');
                }
            }

            if ($break1End && $end) {
                if (strtotime($break1End) > strtotime($end)) {
                    $validator->errors()->add('new_break1_end', '休憩時間もしくは退勤時間が不適切な値です');
                }
            }

            if ($break2Start && $start && $end) {
                if (strtotime($break2Start) < strtotime($start) || strtotime($break1Start) > strtotime($end)) {
                    $validator->errors()->add('new_break2_start','休憩時間が不適切な値です');
                }
            }

            if ($break2End && $end) {
                if (strtotime($break2End) > strtotime($end)) {
                    $validator->errors()->add('new_break2_end', '休憩時間もしくは退勤時間が不適切な値です');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'new_clock_in.required' => '出勤時間を入力してください',
            'new_clock_out.required' => '退勤時間を入力してください',
            'new_clock_in.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'new_clock_out.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'reason.required' => '備考を記入してください'
        ];
    }
}
