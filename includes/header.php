<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Android Network Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto px-4 py-6">
        <header class="mb-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-indigo-700 flex items-center">
                    <i data-lucide="activity" class="mr-2"></i> Android Network Monitor
                </h1>
                <div class="text-sm text-gray-500">
                    <span id="current-time" class="font-medium"></span>
                </div>
            </div>
            <div class="mt-2 text-gray-600">
                Real-time monitoring for rooted Android devices
            </div>
        </header>