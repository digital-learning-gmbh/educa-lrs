<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to educa LRS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/antd/4.23.1/antd.min.css" rel="stylesheet">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">
<div id="root" style="padding: 20px; max-width: 800px; margin-left: auto; margin-right: auto;">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="/logo.svg" alt="Educa LRS Logo" style="max-width: 150px; margin-bottom: 20px;">
    </div>
    <div style="text-align: center; margin-bottom: 40px;">
        <h1>Welcome to educa Learning Record Store (LRS)</h1>
        <p style="font-size: 16px; color: #555;">A robust system designed for xAPI-compliant learning records management.</p>
    </div>

    <div style="margin-bottom: 40px;">
        <h2 style="color: #1890ff;">Technical Features</h2>
        <ul style="line-height: 1.8;">
            <li><strong>Actors:</strong> Tracks individuals or systems performing actions, securely stored in the database.</li>
            <li><strong>Verbs:</strong> Defines the actions performed (e.g., "completed", "viewed"), dynamically created and validated in compliance with xAPI standards.</li>
            <li><strong>Objects:</strong> Represents learning content or resources, such as courses, modules, videos, or assessments.</li>
            <li><strong>Statements:</strong> Stores complete xAPI-compliant statements with actors, verbs, objects, and optional fields like context and results.</li>
            <li><strong>Health Endpoint:</strong> A Prometheus-compatible endpoint (<code>/up</code>) for monitoring system status and database connectivity.</li>
            <li><strong>Database Migration:</strong> Automatically migrates the database schema during the initial setup.</li>
        </ul>
    </div>

    <div style="margin-bottom: 40px;">
        <h2 style="color: #1890ff;">Available Endpoints</h2>
        <ul style="line-height: 1.8;">
            <li><code>/up</code> - Provides the system's health status for monitoring purposes.</li>
            <li><code>/api/actors</code> - Manage actors within the system.</li>
            <li><code>/api/verbs</code> - Dynamically manage action verbs.</li>
            <li><code>/api/objects</code> - Manage learning objects like courses and resources.</li>
            <li><code>/api/statements</code> - Submit and query xAPI statements.</li>
        </ul>
    </div>

    <div style="margin-bottom: 40px;">
        <h2 style="color: #1890ff;">Getting Started</h2>
        <p>Start using educa LRS by exploring the endpoints or reviewing the documentation.</p>
    </div>

    <div style="margin-bottom: 40px;">
        <h2 style="color: #1890ff;">Database Statistics</h2>
        <p>Explore the current state of your educa LRS database:</p>
        <ul style="line-height: 1.8;">
            <li><strong>Total Actors:</strong> {{ $stats['actors'] }}</li>
            <li><strong>Total Verbs:</strong> {{ $stats['verbs'] }}</li>
            <li><strong>Total Objects:</strong> {{ $stats['objects'] }}</li>
            <li><strong>Total Statements:</strong> {{ $stats['statements'] }}</li>
        </ul>
    </div>
</div>
</body>
</html>
