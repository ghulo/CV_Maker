# CV Maker Development Setup

## Quick Start

### Option 1: Use the batch script (Windows)
```bash
start-dev.bat
```

### Option 2: Use npm script
```bash
npm run dev:all
```

### Option 3: Manual start
```bash
# Terminal 1 - Vite Dev Server
npm run dev

# Terminal 2 - Laravel Server
php artisan serve --host=0.0.0.0 --port=8000
```

## Access URLs
- **Main Application**: http://localhost:8000
- **Vite Dev Server**: http://localhost:5173

## Troubleshooting

### If CSS/JS not loading on route changes:

1. **Check Vite Dev Server**: Ensure it's running on port 5173
2. **Clear Browser Cache**: Hard refresh (Ctrl+F5)
3. **Check Console**: Look for 404 errors on assets
4. **Restart Servers**: Stop and restart both servers

### If homepage content missing:

1. **Check Vue Router**: Ensure routes are properly configured
2. **Check Component Imports**: Verify all components are imported
3. **Check Console Errors**: Look for JavaScript errors
4. **Check Network Tab**: Ensure all assets are loading

### Common Issues:

1. **Port Conflicts**: If port 5173 is in use, Vite will use the next available port
2. **CORS Issues**: Ensure both servers are running on localhost
3. **Asset Path Issues**: Check vite.config.js base path configuration

## Development Workflow

1. Start both servers using one of the methods above
2. Make changes to Vue components in `resources/js/components/`
3. Make changes to CSS in `resources/css/master.css`
4. Changes will hot-reload automatically
5. For production, run `npm run build`

## File Structure

```
CV_Maker-1/
├── resources/
│   ├── js/
│   │   ├── components/          # Vue components
│   │   ├── router/             # Vue router configuration
│   │   └── app.js              # Main Vue app entry point
│   ├── css/
│   │   └── master.css          # Main CSS file
│   └── views/
│       └── layouts/
│           └── app.blade.php   # Main layout template
├── vite.config.js              # Vite configuration
└── package.json                # Dependencies and scripts
```

## Environment Variables

Make sure your `.env` file has:
```
APP_URL=http://localhost:8000
VITE_BASE_URL=http://localhost:5173
``` 