<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesazh i Ri nga CV Maker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        .field-value {
            background-color: white;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mesazh i Ri nga CV Maker</h1>
    </div>

    <div class="content">
        <div class="field">
            <div class="field-label">Nga:</div>
            <div class="field-value">{{ $name }} ({{ $email }})</div>
        </div>

        <div class="field">
            <div class="field-label">Subjekti:</div>
            <div class="field-value">{{ $subject }}</div>
        </div>

        <div class="field">
            <div class="field-label">Mesazhi:</div>
            <div class="field-value">{{ $messageContent }}</div>
        </div>
    </div>

    <div class="footer">
        <p>Ky email u dërgua nga forma e kontaktit në CV Maker.</p>
        <p>© {{ date('Y') }} CV Maker. Të gjitha të drejtat e rezervuara.</p>
    </div>
</body>
</html> 