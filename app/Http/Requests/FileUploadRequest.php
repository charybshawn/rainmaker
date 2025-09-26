<?php

namespace App\Http\Requests;

use App\Services\FileUploadService;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return FileUploadService::getValidationRules();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'attachments.*.file' => 'Each attachment must be a valid file.',
            'attachments.*.max' => 'Each attachment must not be larger than 10MB.',
            'attachments.*.mimes' => 'Each attachment must be a file of type: pdf, doc, docx, xls, xlsx, ppt, pptx, txt, csv, jpg, jpeg, png, gif, webp, svg.',
            'files.*.file' => 'Each file must be a valid file.',
            'files.*.max' => 'Each file must not be larger than 10MB.',
            'files.*.mimes' => 'Each file must be a file of type: pdf, doc, docx, xls, xlsx, ppt, pptx, txt, csv, jpg, jpeg, png, gif, webp, svg.',
            'document_urls.*.url' => 'Each document URL must be a valid URL.',
            'document_names.*.string' => 'Each document name must be a string.',
            'document_names.*.max' => 'Each document name must not be longer than 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'attachments.*' => 'attachment',
            'files.*' => 'file',
            'document_urls.*' => 'document URL',
            'document_names.*' => 'document name',
        ];
    }
}