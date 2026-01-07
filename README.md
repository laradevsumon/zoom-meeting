# Zoom Meeting Integration for Laravel

A comprehensive Laravel package that provides seamless integration with the Zoom Meeting API using Server-to-Server OAuth authentication.

## Overview

This package simplifies the integration of Zoom's video conferencing capabilities into your Laravel application, enabling you to programmatically create and manage Zoom meetings through a clean, intuitive API.

## Requirements

- PHP 8.2 or higher
- Laravel 10.x or 11.x
- Composer
- Active Zoom account with Server-to-Server OAuth app credentials

## Installation

Install the package via Composer:

```bash
composer require pkc/zoom-meeting
```

## Configuration

### Publishing Configuration Files

Publish the package configuration file to your application:

```bash
php artisan vendor:publish --tag="zoom-config"
```

This will create a `config/zoom.php` file in your application.

### Environment Variables

Add your Zoom Server-to-Server OAuth credentials to your `.env` file:

```env
ZOOM_ACCOUNT_ID=your_account_id
ZOOM_CLIENT_ID=your_client_id
ZOOM_CLIENT_SECRET=your_client_secret
```

#### Obtaining Zoom Credentials

1. Navigate to the [Zoom App Marketplace](https://marketplace.zoom.us/)
2. Create a new Server-to-Server OAuth app
3. Copy your Account ID, Client ID, and Client Secret
4. Configure the appropriate scopes for your application

## Usage

### Basic Implementation

The package provides a `ZoomService` class that can be resolved from Laravel's service container:

```php
use Pkc\ZoomMeeting\ZoomService;

$zoomService = app(ZoomService::class);
```

### Creating a Meeting

Create a new Zoom meeting by calling the `createMeeting` method with your desired parameters:

```php
$meeting = $zoomService->createMeeting([
    'topic' => 'Medical Consultation',
    'start_time' => '2026-01-10T10:00:00Z',
    'duration' => 30,
    'timezone' => 'UTC',
]);
```

### Accessing Meeting Details

The `createMeeting` method returns an array containing the Zoom API response with the following key fields:

```php
$meetingId = $meeting['id'];
$joinUrl = $meeting['join_url'];
$startUrl = $meeting['start_url'];
$password = $meeting['password'] ?? null;
```

### Response Structure

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique identifier for the meeting |
| `join_url` | string | URL for participants to join the meeting |
| `start_url` | string | URL for the host to start the meeting |
| `password` | string | Meeting password (if enabled) |
| `topic` | string | Meeting topic |
| `start_time` | string | Scheduled start time in ISO 8601 format |
| `duration` | integer | Meeting duration in minutes |
| `timezone` | string | Meeting timezone |

### Advanced Usage Example

```php
use Pkc\ZoomMeeting\ZoomService;

class MeetingController extends Controller
{
    public function __construct(
        protected ZoomService $zoomService
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date',
            'duration' => 'required|integer|min:1',
        ]);

        try {
            $meeting = $this->zoomService->createMeeting([
                'topic' => $validated['topic'],
                'start_time' => $validated['start_time'],
                'duration' => $validated['duration'],
                'timezone' => config('app.timezone'),
            ]);

            return response()->json([
                'success' => true,
                'meeting' => $meeting,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create meeting',
            ], 500);
        }
    }
}
```

## API Reference

### ZoomService::createMeeting(array $data)

Creates a new Zoom meeting.

**Parameters:**

- `topic` (string, required): The meeting topic
- `start_time` (string, required): Meeting start time in ISO 8601 format
- `duration` (integer, required): Meeting duration in minutes
- `timezone` (string, optional): Timezone identifier (default: UTC)

**Returns:** Array containing the Zoom API response

**Throws:** `Exception` on API errors

## Security Considerations

- Never commit your `.env` file or expose your Zoom credentials
- Store credentials securely using Laravel's encryption if needed
- Implement proper authorization checks before creating meetings
- Validate and sanitize all user inputs

## Troubleshooting

### Common Issues

**Authentication Errors:**
- Verify your credentials are correctly set in `.env`
- Ensure your Server-to-Server OAuth app has the necessary scopes
- Check that your app is activated in the Zoom Marketplace

**API Rate Limits:**
- Zoom enforces rate limits on API requests
- Implement appropriate error handling and retry logic

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/pkc/zoom-meeting).

## License

This package is open-sourced software licensed under the [MIT License](LICENSE.md).

## Credits

Developed and maintained by PKC.

---

**Version:** 1.0.0  
**Last Updated:** January 2026