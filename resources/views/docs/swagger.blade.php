<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=index.html">
    <title>Peer-Lend REST API Documentation</title>
    <!-- Load Swagger UI CSS from CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.11.0/swagger-ui.min.css" />
    <link rel="icon" type="image/png" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.11.0/favicon-32x32.png" sizes="32x32" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -margin-y;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin: 0;
            background: #fafafa;
        }
        .swagger-ui .topbar {
            background-color: #4f46e5; /* Indigo-600 */
        }
        .swagger-ui .topbar a {
            font-size: 1.5em;
            font-weight: bold;
            color: #ffffff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <!-- Load Swagger UI JS Bundle from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.11.0/swagger-ui-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.11.0/swagger-ui-standalone-preset.min.js"></script>
    <script>
        window.onload = function() {
            // Inisialisasi SwaggerUI
            const ui = SwaggerUIBundle({
                url: "/swagger/openapi.json",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "BaseLayout"
            });
            window.ui = ui;
        };
    </script>
</body>
</html>
