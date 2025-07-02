<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CV - {{ $cv->emri ?? 'Professional' }} {{ $cv->mbiemri ?? 'CV' }}</title>
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            color: #333; 
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container { 
            padding: 30px; 
            max-width: 800px; 
            margin: 0 auto;
        }
        h1 { 
            font-size: 32px; 
            text-align: center; 
            border-bottom: 3px solid #3B82F6; 
            padding-bottom: 15px; 
            margin-bottom: 10px;
            color: #1F2937;
        }
        .contact-info { 
            text-align: center; 
            margin: 15px 0 30px 0; 
            font-size: 14px; 
            color: #6B7280;
            padding: 10px;
            background: #F9FAFB;
            border-radius: 8px;
        }
        h2 { 
            font-size: 22px; 
            border-bottom: 2px solid #E5E7EB; 
            padding-bottom: 8px; 
            margin-top: 35px; 
            margin-bottom: 20px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .section { 
            margin-bottom: 25px; 
        }
        .job-title, .degree { 
            font-weight: bold; 
            font-size: 18px; 
            color: #1F2937;
            margin-bottom: 5px;
        }
        .company, .institution { 
            font-style: italic; 
            color: #6B7280; 
            font-size: 16px;
            margin-bottom: 5px;
        }
        .date { 
            float: right; 
            color: #9CA3AF;
            font-weight: 500;
            font-size: 14px;
        }
        .description { 
            margin-top: 10px; 
            color: #4B5563;
            text-align: justify;
        }
        .skills-list { 
            list-style: none; 
            padding: 0; 
            margin: 0; 
        }
        .skills-list li { 
            display: inline-block; 
            background-color: #3B82F6; 
            color: white;
            padding: 8px 15px; 
            border-radius: 20px; 
            margin: 5px 5px 5px 0; 
            font-size: 14px;
            font-weight: 500;
        }
        .item-separator {
            border: 0; 
            border-top: 1px solid #E5E7EB; 
            margin: 20px 0;
        }
        .summary-text {
            font-size: 16px;
            color: #4B5563;
            text-align: justify;
            line-height: 1.7;
            padding: 15px;
            background: #F9FAFB;
            border-radius: 8px;
            border-left: 4px solid #3B82F6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ trim(($cv->emri ?? '') . ' ' . ($cv->mbiemri ?? '')) ?: 'Professional CV' }}</h1>
        
        @if($cv->email || $cv->telefoni || $cv->address)
        <div class="contact-info">
            @if($cv->email){{ $cv->email }}@endif
            @if($cv->email && ($cv->telefoni || $cv->address)) | @endif
            @if($cv->telefoni){{ $cv->telefoni }}@endif
            @if($cv->telefoni && $cv->address) | @endif
            @if($cv->address){{ $cv->address }}@endif
        </div>
        @endif

        @if($cv->summary)
        <div class="section">
            <h2>Professional Summary</h2>
            <div class="summary-text">{{ $cv->summary }}</div>
        </div>
        @endif

        @if($cv->workExperiences && $cv->workExperiences->count() > 0)
        <div class="section">
            <h2>Work Experience</h2>
            @foreach($cv->workExperiences as $exp)
                <div style="margin-bottom: 20px;">
                    <span class="date">{{ $exp->start_date ?? 'N/A' }} - {{ $exp->end_date ?? 'Present' }}</span>
                    <div class="job-title">{{ $exp->job_title ?? 'Position' }}</div>
                    <div class="company">{{ $exp->company ?? 'Company' }}</div>
                    @if($exp->job_description)
                    <div class="description">{{ $exp->job_description }}</div>
                    @endif
                </div>
                @if(!$loop->last) <hr class="item-separator"> @endif
            @endforeach
        </div>
        @endif

        @if($cv->education && $cv->education->count() > 0)
        <div class="section">
            <h2>Education</h2>
            @foreach($cv->education as $edu)
                <div style="margin-bottom: 20px;">
                    <span class="date">{{ $edu->start_date ?? 'N/A' }} - {{ $edu->end_date ?? 'Present' }}</span>
                    <div class="degree">{{ $edu->degree ?? 'Degree' }}</div>
                    <div class="institution">{{ $edu->institution ?? 'Institution' }}</div>
                </div>
                @if(!$loop->last) <hr class="item-separator"> @endif
            @endforeach
        </div>
        @endif

        @if($cv->skills && $cv->skills->count() > 0)
        <div class="section">
            <h2>Skills</h2>
            <ul class="skills-list">
                @foreach($cv->skills as $skill)
                    <li>{{ $skill->name ?? 'Skill' }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($cv->interests && $cv->interests->count() > 0)
        <div class="section">
            <h2>Interests</h2>
            <ul class="skills-list">
                @foreach($cv->interests as $interest)
                    <li style="background-color: #10B981;">{{ $interest->description ?? 'Interest' }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</body>
</html>
