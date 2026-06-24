<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Business Forum</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #1e3a5f; color: #ffffff; padding: 28px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p  { margin: 4px 0 0; font-size: 13px; color: #a8c4e0; }
        .body      { padding: 32px; color: #333333; line-height: 1.7; font-size: 15px; }
        .body h2   { font-size: 18px; color: #1e3a5f; margin-top: 0; }
        .info-box  { background: #eef4fb; border-left: 4px solid #1e3a5f; border-radius: 4px; padding: 16px 20px; margin: 20px 0; font-size: 14px; }
        .info-box strong { display: block; margin-bottom: 4px; color: #1e3a5f; }
        .footer    { background: #f8f8f8; padding: 20px 32px; text-align: center; font-size: 12px; color: #999999; border-top: 1px solid #eeeeee; }
        table.details { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px; }
        table.details td { padding: 8px 12px; border-bottom: 1px solid #eeeeee; }
        table.details td:first-child { color: #666; width: 40%; }
        table.details td:last-child  { font-weight: 500; color: #222; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Business Forum</h1>
        <p>Plateforme de rencontres et d'événements professionnels</p>
    </div>
    <div class="body">
        @yield('content')
    </div>
    <div class="footer">
        © {{ date('Y') }} Business Forum. Cet email est automatique, merci de ne pas y répondre.
    </div>
</div>
</body>
</html>