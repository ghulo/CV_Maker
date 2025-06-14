{{-- resources/views/cv/preview-content.blade.php --}}
{{-- This file renders the HTML content for different CV templates based on fetched data. --}}
{{-- It is used by CvPreviewApiController and potentially CvController for full preview page. --}}

{{-- The $cv_details, $work_experiences, $educations, $interests_text, $selected_template variables are passed from the controller. --}}

{{-- Main wrapper div for template-specific styling --}}
<div class="cv-preview-content template-{{ $selected_template }}">

    @if ($selected_template === 'classic')
        <div class="cv-header-classic">
            <h1>{{ $cv_details->emri ?? '' }} {{ $cv_details->mbiemri ?? '' }}</h1>
            @if(!empty($cv_details->summary) && strlen($cv_details->summary) < 100)
                <h2 class="professional-title-classic">{{ $cv_details->summary }}</h2>
            @endif
        </div>

        <div class="cv-section personal-details-classic">
            <h3>Informacioni Personal</h3>
            <div class="info-grid">
                @if (!empty($cv_details->email))<div class="info-item"><span class="info-label">Email:</span><span class="info-value">{{ $cv_details->email }}</span></div>@endif
                @if (!empty($cv_details->telefoni))<div class="info-item"><span class="info-label">Telefoni:</span><span class="info-value">{{ $cv_details->telefoni }}</span></div>@endif
                @if (!empty($cv_details->address))<div class="info-item info-address"><span class="info-label">Adresa:</span><span class="info-value">{!! nl2br(e($cv_details->address)) !!}</span></div>@endif
                @if (!empty($cv_details->date_of_birth))<div class="info-item"><span class="info-label">Datëlindja:</span><span class="info-value">{{ \Carbon\Carbon::parse($cv_details->date_of_birth)->format('d F Y') }}</span></div>@endif
                @if (!empty($cv_details->place_of_birth))<div class="info-item"><span class="info-label">Vendlindja:</span><span class="info-value">{{ $cv_details->place_of_birth }}</span></div>@endif
                @if (!empty($cv_details->gender))<div class="info-item"><span class="info-label">Gjinia:</span><span class="info-value">{{ $cv_details->gender }}</span></div>@endif
                @if (!empty($cv_details->nationality))<div class="info-item"><span class="info-label">Kombësia:</span><span class="info-value">{{ $cv_details->nationality }}</span></div>@endif
                @if (!empty($cv_details->zip_code))<div class="info-item"><span class="info-label">Kodi Postar:</span><span class="info-value">{{ $cv_details->zip_code }}</span></div>@endif
                @if (!empty($cv_details->marital_status))<div class="info-item"><span class="info-label">Statusi Martesor:</span><span class="info-value">{{ $cv_details->marital_status }}</span></div>@endif
                @if (!empty($cv_details->driving_license))<div class="info-item"><span class="info-label">Patentë Shoferi:</span><span class="info-value">{{ $cv_details->driving_license }}</span></div>@endif
            </div>
        </div>

        @if (!empty($cv_details->summary) && strlen($cv_details->summary) >= 100)
            <div class="cv-section summary-section-classic">
                <h3>Përmbledhja Profesionale</h3>
                <p>{!! nl2br(e($cv_details->summary)) !!}</p>
            </div>
        @endif

        <div class="cv-section work-experience-classic">
            <h3>Eksperienca e Punës</h3>
            @if (!$work_experiences->isEmpty())
                @foreach ($work_experiences as $work)
                    @if (empty($work->job_title) && empty($work->company)) @continue @endif
                    <div class="entry">
                        <h4>{{ $work->job_title ?? 'Pozitë' }}</h4>
                        <p class="company-duration">{{ $work->company ?? 'Kompani' }}
                            @php
                                $date_range = '';
                                if (!empty($work->start_date)) {
                                    $date_range .= \Carbon\Carbon::parse($work->start_date)->format('M Y');
                                }
                                if ($work->is_current_job) {
                                    $date_range .= ' - Tani';
                                } elseif (!empty($work->end_date)) {
                                    $date_range .= ' - ' . \Carbon\Carbon::parse($work->end_date)->format('M Y');
                                }
                            @endphp
                            @if (!empty($date_range)) ({{ $date_range }}) @endif
                        </p>
                        @if (!empty($work->job_description))<div class="description">{!! nl2br(e($work->job_description)) !!}</div>@endif
                    </div>
                @endforeach
            @else
                <p class="no-data">Nuk ka eksperiencë pune të shtuar.</p>
            @endif
        </div>

        <div class="cv-section education-classic">
            <h3>Edukimi</h3>
            @if (!$educations->isEmpty())
                @foreach ($educations as $edu)
                    @if (empty($edu->school)) @continue @endif
                    <div class="entry">
                        <h4>{{ $edu->degree ?? 'Diplomë' }}</h4>
                        <p class="school-graduation">{{ $edu->school ?? 'Institucion' }}
                            @if (!empty($edu->graduation_year)) - Diplomuar: {{ $edu->graduation_year }} @endif
                        </p>
                        @if (!empty($edu->edu_description))<div class="description">{!! nl2br(e($edu->edu_description)) !!}</div>@endif
                    </div>
                @endforeach
            @else
                <p class="no-data">Nuk ka edukim të shtuar.</p>
            @endif
        </div>

        @if (!empty($interests_text))
            <div class="cv-section interests-classic">
                <h3>Interesat / Aftësitë</h3>
                <p>{!! nl2br(e($interests_text)) !!}</p>
            </div>
        @endif

    @elseif ($selected_template === 'modern')
        <div class="cv-modern-layout">
            <div class="cv-modern-sidebar">
                <h3>Kontakt</h3>
                @if (!empty($cv_details->email))<p><i class="fas fa-envelope icon"></i> {{ $cv_details->email }}</p>@endif
                @if (!empty($cv_details->telefoni))<p><i class="fas fa-phone icon"></i> {{ $cv_details->telefoni }}</p>@endif
                @if (!empty($cv_details->address))<p><i class="fas fa-map-marker-alt icon"></i> {!! nl2br(e($cv_details->address)) !!}</p>@endif
                @if (!empty($cv_details->zip_code))<p style="padding-left:1.4em;">Kodi Postar: {{ $cv_details->zip_code }}</p>@endif

                <h3 class="sidebar-section-title">Informacione Shtesë</h3>
                @if (!empty($cv_details->date_of_birth))<p><i class="fas fa-birthday-cake icon"></i> {{ \Carbon\Carbon::parse($cv_details->date_of_birth)->format('d M Y') }}</p>@endif
                @if (!empty($cv_details->place_of_birth))<p><i class="fas fa-map-pin icon"></i> Vendlindja: {{ $cv_details->place_of_birth }}</p>@endif
                @if (!empty($cv_details->nationality))<p><i class="fas fa-flag icon"></i> Kombësia: {{ $cv_details->nationality }}</p>@endif
                @if (!empty($cv_details->driving_license))<p><i class="fas fa-car icon"></i> Patentë: {{ $cv_details->driving_license }}</p>@endif

                @if (!empty($interests_text))
                    <h3 class="sidebar-section-title skills-header">Aftësitë</h3>
                    @php
                        $skills_array = array_map('trim', explode(',', $interests_text));
                    @endphp
                    <ul class="skills-list">
                        @foreach($skills_array as $skill_item)
                            @if(!empty($skill_item))<li>{{ $skill_item }}</li>@endif
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="cv-modern-main">
                <div class="cv-header-modern">
                    <h1>{{ $cv_details->emri ?? '' }} {{ $cv_details->mbiemri ?? '' }}</h1>
                    @if(!empty($cv_details->summary) && strlen($cv_details->summary) < 100)
                        <h2 class="professional-title-modern">{{ $cv_details->summary }}</h2>
                    @endif
                </div>

                @if (!empty($cv_details->summary) && strlen($cv_details->summary) >= 100)
                    <div class="cv-section summary-section-modern">
                        <h3>Përmbledhja Profesionale</h3>
                        <p>{!! nl2br(e($cv_details->summary)) !!}</p>
                    </div>
                @endif

                <div class="cv-section work-experience-modern">
                    <h3>Eksperienca e Punës</h3>
                    @if (!$work_experiences->isEmpty())
                        @foreach ($work_experiences as $work)
                            @if (empty($work->job_title) && empty($work->company)) @continue @endif
                            <div class="entry">
                                <h4>{{ $work->job_title ?? 'Pozitë' }}</h4>
                                <p class="company-duration">{{ $work->company ?? 'Kompani' }}
                                    @php
                                        $date_range = '';
                                        if (!empty($work->start_date)) {
                                            $date_range .= \Carbon\Carbon::parse($work->start_date)->format('M Y');
                                        }
                                        if ($work->is_current_job) {
                                            $date_range .= ' - Tani';
                                        } elseif (!empty($work->end_date)) {
                                            $date_range .= ' - ' . \Carbon\Carbon::parse($work->end_date)->format('M Y');
                                        }
                                    @endphp
                                    @if (!empty($date_range)) ({{ $date_range }}) @endif
                                </p>
                                @if (!empty($work->job_description))<div class="description">{!! nl2br(e($work->job_description)) !!}</div>@endif
                            </div>
                        @endforeach
                    @else
                        <p class="no-data">Nuk ka eksperiencë pune të shtuar.</p>
                    @endif
                </div>

                <div class="cv-section education-modern">
                    <h3>Edukimi</h3>
                    @if (!$educations->isEmpty())
                        @foreach ($educations as $edu)
                            @if (empty($edu->school)) @continue @endif
                            <div class="entry">
                                <h4>{{ $edu->degree ?? 'Diplomë' }}</h4>
                                <p class="school-graduation">{{ $edu->school ?? 'Institucion' }}
                                    @if (!empty($edu->graduation_year)) - Diplomuar: {{ $edu->graduation_year }} @endif
                                </p>
                                @if (!empty($edu->edu_description))<div class="description">{!! nl2br(e($edu->edu_description)) !!}</div>@endif
                            </div>
                        @endforeach
                    @else
                        <p class="no-data">Nuk ka edukim të shtuar.</p>
                    @endif
                </div>
            </div>
        </div>

    @elseif ($selected_template === 'professional')
        <div class="cv-header-professional">
            <h1>{{ strtoupper($cv_details->emri ?? '') }} {{ strtoupper($cv_details->mbiemri ?? '') }}</h1>
            @php
                $professional_title = !empty($cv_details->summary) && strlen($cv_details->summary) < 100 ? $cv_details->summary : ($cv_details->cv_title ?? 'PROFESSIONAL TITLE');
            @endphp
            <h2 class="professional-title-main">{{ strtoupper($professional_title) }}</h2>
        </div>

        <div class="cv-professional-layout">
            <div class="cv-professional-left">
                <div class="cv-section contact-professional">
                    <h3>KONTAKT</h3>
                    @if (!empty($cv_details->email))<p><i class="fas fa-envelope icon"></i> {{ $cv_details->email }}</p>@endif
                    @if (!empty($cv_details->telefoni))<p><i class="fas fa-phone icon"></i> {{ $cv_details->telefoni }}</p>@endif
                    @if (!empty($cv_details->address))
                        @php
                            $address_html = nl2br(e($cv_details->address));
                            if (!empty($cv_details->zip_code)) {
                                $address_html .= ', ' . e($cv_details->zip_code);
                            }
                        @endphp
                        <p><i class="fas fa-map-marker-alt icon"></i> {!! $address_html !!}</p>
                    @elseif (!empty($cv_details->zip_code))
                        <p><i class="fas fa-map-marker-alt icon"></i> Kodi Postar: {{ $cv_details->zip_code }}</p>
                    @endif
                </div>

                <div class="cv-section education-professional">
                    <h3>EDUKIMI</h3>
                    @if (!$educations->isEmpty())
                        @foreach ($educations as $edu)
                            @if (empty($edu->school)) @continue @endif
                            <div class="entry">
                                <h4>{{ $edu->degree ?? 'Diplomë' }}</h4>
                                <p class="school">{{ $edu->school ?? 'Institucion' }}</p>
                                @if (!empty($edu->graduation_year))<p class="graduation-year">{{ $edu->graduation_year }}</p>@endif
                                @if (!empty($edu->edu_description))<div class="description">{!! nl2br(e($edu->edu_description)) !!}</div>@endif
                            </div>
                        @endforeach
                    @else
                        <p class="no-data">Nuk ka edukim të shtuar.</p>
                    @endif
                </div>

                @if (!empty($interests_text))
                    <div class="cv-section skills-professional">
                        <h3>AFTËSITË</h3>
                        @php
                            $skills_array = array_map('trim', explode(',', $interests_text));
                        @endphp
                        <ul class="skills-list-prof">
                            @foreach($skills_array as $skill_item)
                                @if(!empty($skill_item))<li>{{ $skill_item }}</li>@endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $other_details_html = '';
                    if (!empty($cv_details->date_of_birth)) $other_details_html .= '<p><strong>Datëlindja:</strong> ' . \Carbon\Carbon::parse($cv_details->date_of_birth)->format('d M Y') . '</p>';
                    if (!empty($cv_details->place_of_birth)) $other_details_html .= '<p><strong>Vendlindja:</strong> ' . $cv_details->place_of_birth . '</p>';
                    if (!empty($cv_details->nationality)) $other_details_html .= '<p><strong>Kombësia:</strong> ' . $cv_details->nationality . '</p>';
                    if (!empty($cv_details->driving_license)) $other_details_html .= '<p><strong>Patentë Shoferi:</strong> ' . $cv_details->driving_license . '</p>';
                    if (!empty($cv_details->marital_status)) $other_details_html .= '<p><strong>Statusi Martesor:</strong> ' . $cv_details->marital_status . '</p>';
                @endphp

                @if(!empty($other_details_html))
                    <div class="cv-section other-personal-professional">
                        <h3>TË TJERA</h3>
                        {!! $other_details_html !!}
                    </div>
                @endif
            </div>

            <div class="cv-professional-right">
                @if (!empty($cv_details->summary) && strlen($cv_details->summary) >= 100)
                    <div class="cv-section summary-professional">
                        <h3>PROFILI PROFESIONAL</h3>
                        <p>{!! nl2br(e($cv_details->summary)) !!}</p>
                    </div>
                @endif

                <div class="cv-section work-experience-professional">
                    <h3>EKSPERIENCA E PUNËS</h3>
                    @if (!$work_experiences->isEmpty())
                        @foreach ($work_experiences as $work)
                            @if (empty($work->job_title) && empty($work->company)) @continue @endif
                            <div class="entry">
                                <h4>{{ $work->job_title ?? 'Pozitë' }}</h4>
                                <p class="company-duration">{{ $work->company ?? 'Kompani' }}
                                    @php
                                        $date_range = '';
                                        if (!empty($work->start_date)) {
                                            $date_range .= \Carbon\Carbon::parse($work->start_date)->format('M Y');
                                        }
                                        if ($work->is_current_job) {
                                            $date_range .= ' - Tani';
                                        } elseif (!empty($work->end_date)) {
                                            $date_range .= ' - ' . \Carbon\Carbon::parse($work->end_date)->format('M Y');
                                        }
                                    @endphp
                                    @if (!empty($date_range)) ({{ $date_range }}) @endif
                                </p>
                                @if (!empty($work->job_description))
                                    @php
                                        $desc_points = array_filter(array_map('trim', explode("\n", $work->job_description)));
                                    @endphp
                                    @if (count($desc_points) > 1)
                                        <ul class="description-points">
                                            @foreach($desc_points as $point)
                                                <li>{{ $point }}</li>
                                            @endforeach
                                        </ul>
                                    @elseif (count($desc_points) === 1 && !empty($desc_points[0]))
                                        <p class="description">{!! nl2br(e($desc_points[0])) !!}</p>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="no-data">Nuk ka eksperiencë pune të shtuar.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
