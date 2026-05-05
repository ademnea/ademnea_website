<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Adaptive Environmental Monitoring Networks for East Africa</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <style>
        #collapseExample.collapse:not(.show) {
            display: block;
            height: 3rem;
            overflow: hidden;
        }

        #collapseExample.collapsing {
            height: 3rem;
        }

        .newsletter-hero {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(92, 184, 116, 0.96), rgba(31, 41, 55, 0.94));
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
        }

        .newsletter-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 38%),
                        radial-gradient(circle at bottom left, rgba(255, 255, 255, 0.12), transparent 30%);
            pointer-events: none;
        }

        .newsletter-content {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }

        .newsletter-body {
            color: #1f2937;
            line-height: 1.9;
            font-size: 1.05rem;
        }

        .newsletter-body img {
            max-width: 100%;
            height: auto;
            border-radius: 18px;
        }
    </style>
    @include('website.links')

    <!-- =======================================================
  * Template Name: Green - v4.3.0
  * Template URL: https://bootstrapmade.com/green-free-one-page-bootstrap-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

    <!-- ======= Top Bar ======= -->
    @include('website.top_bar')

    <!-- ======= Header ======= -->
    @include('website.header')
    <!-- End Header -->



    <main id="main">
                <section id="scholarship" class="about py-5">
                    <div class="container py-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-10">
                                <div class="newsletter-hero text-white p-4 p-md-5 mb-4">
                                    <div class="position-relative">
                                        <a href="{{ url('/displaynewsletter') }}" class="btn btn-sm btn-light text-success mb-4 px-3 py-2" style="border-radius: 999px; font-weight: 600;">
                                            <i class="bi bi-arrow-left me-1"></i> Back to newsletters
                                        </a>

                                        <div class="row g-4 align-items-center">
                                            <div class="col-md-7">
                                                <p class="text-uppercase small mb-2" style="letter-spacing: 0.14em; opacity: 0.85;">Newsletter Article</p>
                                                <h1 class="display-6 fw-bold mb-3">{{ $newsletter->title }}</h1>
                                                <p class="mb-0" style="max-width: 42rem; opacity: 0.9; font-size: 1.05rem;">
                                                    Read the full update below, including the featured image and complete article content.
                                                </p>
                                            </div>
                                            <div class="col-md-5 text-md-end">
                                                @if($newsletter->image)
                                                    <img src="{{ asset($newsletter->image) }}" alt="{{ $newsletter->title }}" class="img-fluid" style="max-height: 260px; width: 100%; object-fit: cover; border-radius: 22px; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="newsletter-content p-4 p-md-5">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                                        <div>
                                            <p class="text-muted mb-1 small text-uppercase">Published newsletter</p>
                                            <h2 class="h4 mb-0">Article Details</h2>
                                        </div>
                                        <a href="{{ url('/displaynewsletter') }}" class="btn btn-outline-success" style="border-radius: 999px; padding-left: 1rem; padding-right: 1rem;">
                                            Browse all newsletters
                                        </a>
                                    </div>

                                    <div class="newsletter-body">
                                        {!! $newsletter->article !!}
                                    </div>
                                </div>
                            </div>
                        </div>
