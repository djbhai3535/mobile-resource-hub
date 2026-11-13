@extends('layouts.public')

@section('title', 'DMCA & Copyright Policy — Mobile Resource Hub')
@section('meta_description', 'Mobile Resource Hub Copyright and DMCA Notice Policy. Detailed procedure for intellectual property owners to submit takedown notices.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">DMCA Policy</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <h1 class="h2 fw-bold mb-2">DMCA & Copyright Notice</h1>
                <div class="text-muted small mb-4">Effective Date: 13 November 2026</div>

                <div class="content-body" style="line-height: 1.8;">
                    <p>
                        <strong>Mobile Resource Hub</strong> respects intellectual property rights and adheres strictly to applicable copyright laws, including the Digital Millennium Copyright Act (DMCA).
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">1. Notice of Claimed Infringement</h2>
                    <p>
                        If you are a copyright owner or authorized agent and believe that content published or linked on our platform infringes your copyright, you may submit a formal notification containing the following elements:
                    </p>
                    <ul>
                        <li>A physical or electronic signature of a person authorized to act on behalf of the owner of the copyright interest.</li>
                        <li>Identification of the copyrighted work claimed to have been infringed.</li>
                        <li>Identification of the material claimed to be infringing, including the specific URL on Mobile Resource Hub.</li>
                        <li>Your contact information (name, address, telephone number, and email address).</li>
                        <li>A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law.</li>
                        <li>A statement, under penalty of perjury, that the information in the notification is accurate.</li>
                    </ul>

                    <h2 class="h5 fw-bold mt-4 mb-2">2. Submission Channel</h2>
                    <p>
                        Please direct all formal DMCA and copyright notices to our designated agent via our <a href="{{ url('/contact-us') }}">Contact Form</a> with the subject line <code>DMCA Copyright Notice</code>. Valid notices are processed promptly, and disputed links are disabled during review.
                    </p>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
