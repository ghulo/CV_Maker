<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CV - {{ $cv->emri }} {{ $cv->mbiemri }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .container { padding: 40px; }
        h1 { font-size: 28px; text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 0; }
        .contact-info { text-align: center; margin-top: 5px; font-size: 12px; color: #555; }
        h2 { font-size: 20px; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 25px; }
        .section { margin-bottom: 20px; }
        .job-title, .degree { font-weight: bold; font-size: 16px; }
        .company, .institution { font-style: italic; color: #555; }
        .date { float: right; color: #555; }
        .description { margin-top: 5px; }
        .skills-list { list-style: none; padding: 0; margin: 0; }
        .skills-list li { display: inline-block; background-color: #f0f0f0; padding: 5px 10px; border-radius: 5px; margin: 2px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $cv->emri }} {{ $cv->mbiemri }}</h1>
        <div class="contact-info">
            {{ $cv->email }} | {{ $cv->telefoni }} | {{ $cv->address }}
        </div>

        <div class="section">
            <h2>Përmbledhja Profesionale</h2>
            <p>{{ $cv->summary }}</p>
        </div>

        <div class="section">
            <h2>Përvoja e Punës</h2>
            @foreach($cv->experience as $exp)
                <div>
                    <span class="date">{{ $exp->start_date }} - {{ $exp->end_date ?? 'Tani' }}</span>
                    <div class="job-title">{{ $exp->job_title }}</div>
                    <div class="company">{{ $exp->company }}</div>
                    <div class="description">{{ $exp->description }}</div>
                </div>
                @if(!$loop->last) <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;"> @endif
            @endforeach
        </div>

        <div class="section">
            <h2>Edukimi</h2>
            @foreach($cv->education as $edu)
                <div>
                    <span class="date">{{ $edu->start_date }} - {{ $edu->end_date ?? 'Tani' }}</span>
                    <div class="degree">{{ $edu->degree }}</div>
                    <div class="institution">{{ $edu->institution }}</div>
                </div>
                 @if(!$loop->last) <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;"> @endif
            @endforeach
        </div>

        <div class="section">
            <h2>Aftësitë</h2>
            <ul class="skills-list">
                @foreach($cv->skills as $skill)
                    <li>{{ $skill->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</body>
</html>
