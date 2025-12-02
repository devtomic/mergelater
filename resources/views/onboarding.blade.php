<!DOCTYPE html>
<html>
<head>
    <title>Onboarding - MergeLater</title>
</head>
<body>
    <h1>Welcome to MergeLater</h1>
    <p>Select your timezone</p>
    <form method="POST" action="/onboarding">
        @csrf
        <select name="timezone">
            <option value="America/New_York">America/New_York</option>
            <option value="America/Chicago">America/Chicago</option>
            <option value="America/Denver">America/Denver</option>
            <option value="America/Los_Angeles">America/Los_Angeles</option>
            <option value="Europe/London">Europe/London</option>
            <option value="Europe/Paris">Europe/Paris</option>
            <option value="Asia/Tokyo">Asia/Tokyo</option>
            <option value="UTC">UTC</option>
        </select>
        <button type="submit">Continue</button>
    </form>
</body>
</html>
