@extends('layouts.student')

@section('title', 'Available Documents')

@section('content')

    {{-- Heading --}}
    <div class="docs-heading-wrap">
        <h2 class="docs-heading">AVAILABLE DOCUMENTS</h2>
    </div>

    {{-- Scrollable container for the grid --}}
    <div class="docs-scroll-container">
        <div class="docs-grid">
            @foreach($documentTypes as $doc)
            <a href="{{ route('student.requests.create', ['select' => $doc->id]) }}" class="doc-card-link">
                <div class="doc-card">
                    <div class="doc-icon-circle">
                        @php
                            $docImages = [
                                'REG'  => 'registration-form.png',
                                'COG'  => 'certificate-of-grades.png',
                                'COE'  => 'certificate-of-enrollment.png',
                                'TOR'  => 'transcript-of-records.png',
                                'CGMC' => 'good-moral-certificate.png',
                            ];
                            $docInfo = [
                                'REG'   => 'Your official enrollment record for the semester.',
                                'COG'   => 'Official record of your grades for specific periods.',
                                'COE'   => 'Proof that you are currently enrolled at CCST.',
                                'TOR'   => 'Complete history of your academic performance.',
                                'CGMC'  => 'Certifies your good conduct as a student.',
                                'CLID'  => 'Replacement for a lost or damaged ID card.',
                                'CGRAD' => 'Proof of graduation or degree completion.',
                                'CGWA'  => 'Certificate of your general weighted average.',
                                'CRANK' => 'Official ranking within your batch or course.',
                                'F138'  => 'Your official high school/grade report card.',
                            ];
                            $imgFile = $docImages[$doc->code] ?? 'document-icon.png';
                            $tooltip = $docInfo[$doc->code] ?? 'Official school document for student records.';
                        @endphp
                        <img src="{{ asset('images/' . $imgFile) }}" alt="{{ $doc->name }}">
                        
                        {{-- Tooltip Speech Bubble --}}
                        <div class="doc-tooltip">
                            {{ $tooltip }}
                        </div>
                    </div>
                    <div class="doc-name">
                        {{ $doc->name }}
                        <span class="doc-code">({{ $doc->code }})</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

@endsection

@push('styles')
<style>
    .docs-heading-wrap {
        margin-bottom: 15px;
        position: relative;
        z-index: 10;
    }

    .docs-heading {
        font-family: 'Volkhov', serif;
        font-weight: 700;
        font-size: 1.8rem;
        color: #1A1A1A;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }

    /* 4-column grid for a more compact layout */
    .docs-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 28px 24px;
        margin-bottom: 53px;
        max-width: 800px;
    }

    .doc-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.2s;
    }

    .doc-card-link:hover {
        transform: scale(1.02);
    }

    .doc-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        text-align: center;
    }

    /* Resized amber circle to fit 4 per row */
    .doc-icon-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #F5A623, #E08A00);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 10px rgba(245,166,35,0.35);
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative; /* for tooltip positioning */
    }

    /* Tooltip speech bubble */
    .doc-tooltip {
        position: absolute;
        bottom: calc(100% + 15px);
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        background: #1B6B3A;
        color: white;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 500;
        width: 180px;
        text-align: center;
        line-height: 1.4;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        pointer-events: none;
        z-index: 9999; /* Ensure it's above everything */
    }

    .doc-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 8px;
        border-style: solid;
        border-color: #1B6B3A transparent transparent transparent;
    }

    .doc-card:hover .doc-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .doc-card:hover .doc-icon-circle {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(245,166,35,0.45);
    }

    .doc-icon-circle img {
        width: 55px;
        height: 55px;
        object-fit: contain;
        /* no filter — shows icon in its natural color */
    }

    .doc-name {
        text-align: center;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1A1A1A;
        line-height: 1.4;
    }

    .doc-code {
        display: block;
        font-weight: 400;
        color: #666;
        font-size: 0.78rem;
    }

    .docs-action { display: none; }

    /* Fix scroll of the page and prevent tooltip clipping */
    .docs-scroll-container {
        overflow-y: visible;
        padding: 65px 10px 40px 10px; /* Moved half inch higher than before */
        position: relative;
        z-index: 5;
    }
    .docs-scroll-container::-webkit-scrollbar {
        display: none; /* Chrome/Safari */
    }
</style>
@endpush
