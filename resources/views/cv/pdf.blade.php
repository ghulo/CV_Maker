<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $cv->full_name }} - CV</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 28px;
            color: #001f3f;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        h2 {
            font-size: 20px;
            color: #001f3f;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 8px;
            margin: 30px 0 18px 0;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .contact-info {
            font-size: 13px;
            color: #5d6d7e;
            margin-bottom: 8px;
        }
        .section {
            margin-bottom: 28px;
        }
        .experience-item, .education-item {
            margin-bottom: 22px;
            padding-left: 5px;
            border-left: 3px solid #ced4da;
        }
        .experience-header, .education-header {
            margin-bottom: 8px;
        }
        .company-name, .institution {
            font-weight: bold;
            color: #001f3f;
            font-size: 15px;
        }
        .position, .degree {
            color: #34495e;
            font-size: 14px;
            margin-top: 2px;
        }
        .dates {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 4px;
        }
        .description {
            font-size: 13px;
            color: #2c3e50;
            margin-top: 8px;
            font-size: 14px;
            color: #4a5568;
            margin-top: 5px;
        }
        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .skill {
            background-color: #f7fafc;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            color: #4a5568;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $cv->full_name }}</h1>
        <div class="contact-info">
            {{ $cv->email }} | {{ $cv->phone }}<br>
            {{ $cv->address }}
        </div>
    </div>

    <div class="section">
        <h2>Professional Summary</h2>
        <p>{{ $cv->summary }}</p>
    </div>

    @if($cv->workExperiences->count() > 0)
    <div class="section">
        <h2>Work Experience</h2>
        @foreach($cv->workExperiences as $experience)
        <div class="experience-item">
            <div class="experience-header">
                <div class="company-name">{{ $experience->company_name }}</div>
                <div class="position">{{ $experience->position }}</div>
                <div class="dates">
                    {{ $experience->start_date->format('M Y') }} - 
                    {{ $experience->end_date ? $experience->end_date->format('M Y') : 'Present' }}
                </div>
            </div>
            <div class="description">{{ $experience->description }}</div>
        </div>
        @endforeach
    </div>
    @endif

    @if($cv->education->count() > 0)
    <div class="section">
        <h2>Education</h2>
        @foreach($cv->education as $edu)
        <div class="education-item">
            <div class="education-header">
                <div class="institution">{{ $edu->institution }}</div>
                <div class="degree">{{ $edu->degree }}</div>
                <div class="dates">
                    {{ $edu->start_date->format('M Y') }} - 
                    {{ $edu->end_date ? $edu->end_date->format('M Y') : 'Present' }}
                </div>
            </div>
            @if($edu->description)
            <div class="description">{{ $edu->description }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($cv->skills->count() > 0)
    <div class="section">
        <h2>Skills</h2>
        <div class="skills">
            @foreach($cv->skills as $skill)
            <span class="skill">{{ $skill->name }}</span>
            @endforeach
        </div>
    </div>
    @endif
</body>
</html> 