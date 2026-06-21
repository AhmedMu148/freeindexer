<?php

namespace App\Http\Requests\Cms;

use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $section = $this->route('section');
        $sectionId = null;
        if ($section instanceof Section) {
            $sectionId = $section->id;
        } elseif (is_numeric($section)) {
            $sectionId = $section;
        } elseif (is_string($section)) {
            $sectionRecord = Section::where('id', $section)->orWhere('key', $section)->first();
            if ($sectionRecord) {
                $sectionId = $sectionRecord->id;
            }
        }

        return [
            'key' => [
                'nullable',
                'string',
                'alpha_dash',
                'max:255',
                Rule::unique('sections', 'key')->ignore($sectionId),
            ],
            'name' => 'required|string|max:255',
            'type' => [
                'required',
                'string',
                Rule::in(array_keys(Section::TYPES)),
            ],
            'status' => 'nullable|string|in:draft,published',
            'data' => 'nullable|array',
            'html_content' => 'nullable|string',
            'wrapper_class' => 'nullable|string|max:255',
            'anchor_id' => 'nullable|string|max:255',
        ];
    }
}
