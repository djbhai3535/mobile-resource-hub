@extends('layouts.admin')

@section('title', 'WhatsApp Support Settings')
@section('page_title', 'WhatsApp Live Support Configuration')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mrh-card p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="brand-icon-box" style="width: 50px; height: 50px; font-size: 1.8rem; background: rgba(37, 211, 102, 0.15); color: #25D366;">
                    <i class="bi bi-whatsapp"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Floating WhatsApp Chat Button</h4>
                    <p class="text-muted small mb-0">Configure real-time customer and visitor messaging via WhatsApp Click-to-Chat</p>
                </div>
            </div>

            <form action="{{ route('admin.settings.whatsapp.update') }}" method="POST">
                @csrf

                <!-- Enable / Disable Toggle -->
                <div class="form-check form-switch mb-4 p-3 bg-body-tertiary rounded-3 border">
                    <input 
                        class="form-check-input ms-0 me-3" 
                        type="checkbox" 
                        role="switch" 
                        name="whatsapp_enabled" 
                        id="whatsapp_enabled" 
                        value="1" 
                        {{ old('whatsapp_enabled', $enabled) ? 'checked' : '' }}
                        style="width: 2.8em; height: 1.4em;"
                    >
                    <label class="form-check-label fw-bold" for="whatsapp_enabled">
                        Enable Floating WhatsApp Button on Public Website
                    </label>
                    <div class="text-muted small ps-5">
                        When enabled, a responsive floating WhatsApp icon appears on all public pages, allowing visitors to contact support directly.
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="mb-4">
                    <label class="form-label fw-bold" for="whatsapp_number">
                        Support Phone Number (International Format) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone-plus"></i></span>
                        <input 
                            type="text" 
                            name="whatsapp_number" 
                            id="whatsapp_number" 
                            class="form-control font-monospace @error('whatsapp_number') is-invalid @enderror" 
                            placeholder="e.g. 15551234567 or 919876543210 (digits only, include country code, no + or spaces)" 
                            value="{{ old('whatsapp_number', $number) }}"
                        >
                        @error('whatsapp_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text text-muted small">
                        Enter digits only with country code (e.g., <code>15551234567</code> for US, <code>919876543210</code> for India, <code>447123456789</code> for UK). Do not include plus symbols, dashes, or spaces.
                    </div>
                </div>

                <!-- Pre-filled Greeting Message -->
                <div class="mb-4">
                    <label class="form-label fw-bold" for="whatsapp_default_message">
                        Pre-filled Greeting Message
                    </label>
                    <textarea 
                        name="whatsapp_default_message" 
                        id="whatsapp_default_message" 
                        rows="3" 
                        class="form-control @error('whatsapp_default_message') is-invalid @enderror" 
                        placeholder="Hello, I need help with Mobile Resource Hub."
                    >{{ old('whatsapp_default_message', $message) }}</textarea>
                    @error('whatsapp_default_message')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">
                        This message automatically populates the visitor's WhatsApp text box when they click the button.
                    </div>
                </div>

                <!-- Current Live Status & Preview -->
                @if($previewUrl)
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Active Chat Link:</strong>
                        <div class="small font-monospace text-truncate" style="max-width: 500px;">{{ $previewUrl }}</div>
                    </div>
                    <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-success rounded-pill px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Test Chat
                    </a>
                </div>
                @else
                <div class="alert alert-secondary d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-info-circle fs-5"></i>
                    <div>The WhatsApp button is currently <strong>disabled</strong> or lacks a valid phone number.</div>
                </div>
                @endif

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill">
                        <i class="bi bi-save me-1"></i> Save WhatsApp Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
