<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Error</title>
    <style>
        :root {
            /* Utah Desert Light Theme */
            --background: hsl(60, 30%, 98%);
            --foreground: hsl(85, 19%, 24%);
            --primary: hsl(33, 63%, 77%);
            --primary-hover: hsl(33, 63%, 72%);
            --primary-foreground: hsl(85, 19%, 24%);
            --secondary: hsl(82, 18%, 44%);
            --secondary-foreground: hsl(60, 30%, 98%);
            --muted: hsl(48, 15%, 95%);
            --muted-foreground: hsl(85, 19%, 35%);
            --accent: hsl(33, 54%, 62%);
            --accent-foreground: hsl(85, 19%, 24%);
            --destructive: hsl(5, 68%, 42%);
            --destructive-foreground: hsl(60, 30%, 98%);
            --border: hsl(48, 15%, 80%);
        }
        
        @media (prefers-color-scheme: dark) {
            :root {
                /* Utah Desert Dark Theme */
                --background: hsl(90, 6%, 13%);
                --foreground: hsl(40, 25%, 83%);
                --primary: hsl(17, 57%, 53%);
                --primary-hover: hsl(17, 57%, 48%);
                --primary-foreground: hsl(40, 25%, 83%);
                --secondary: hsl(82, 18%, 44%);
                --secondary-foreground: hsl(40, 25%, 83%);
                --muted: hsl(90, 6%, 23%);
                --muted-foreground: hsl(40, 25%, 70%);
                --accent: hsl(37, 22%, 56%);
                --accent-foreground: hsl(40, 25%, 83%);
                --destructive: hsl(5, 68%, 42%);
                --destructive-foreground: hsl(40, 25%, 83%);
                --border: hsl(82, 10%, 35%);
            }
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--background);
            color: var(--foreground);
            line-height: 1.6;
            padding: 2rem;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: var(--muted);
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
        }
        h1 {
            color: var(--destructive);
            margin-top: 0;
        }
        .error-details {
            background-color: var(--background);
            padding: 1rem;
            border-radius: 0.4rem;
            margin-top: 1rem;
            border-left: 4px solid var(--destructive);
        }
        .actions {
            margin-top: 2rem;
        }
        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: var(--primary-foreground);
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 0.4rem;
            font-weight: 500;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .btn:hover {
            background-color: var(--primary-hover);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Authentication Error</h1>
        <p>There was a problem connecting to the authentication service.</p>
        
        <div class="error-details">
            <strong>Error:</strong> {{ $message ?? 'Unknown error' }}
        </div>
        
        <div class="actions">
            <a href="{{ url('/') }}" class="btn">Return to Home</a>
        </div>
    </div>
</body>
</html> 