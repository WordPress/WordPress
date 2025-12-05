# Elementor Library Connect REST API

This module provides REST API endpoints for connecting and disconnecting your WordPress site to the Elementor Library, similar in purpose to the [Elementor CLI Library Connect command](https://developers.elementor.com/docs/cli/library-connect/).

## Overview

The REST API allows programmatic connection and disconnection to the Elementor Library, which is useful for automation, integrations, and testing.  
**Note:** The REST API is intended for internal and advanced use, mirroring the functionality of the CLI command.

## Endpoints

### 1. Connect to Elementor Library

- **URL:** `/index.php?rest_route=/elementor/v1/library/connect`
- **Method:** `POST`
- **Permissions:** Requires the `manage_options` capability (typically administrators).
- **Body Parameters:**
  - `token` (string, required): The connect token from your Elementor account dashboard.

#### Example Request

```http
POST /index.php?rest_route=/elementor/v1/library/connect
Content-Type: application/json
Authorization: Basic {{encoded_wp_credentials}}

{
  "token": "YOUR_CLI_TOKEN"
}
```

#### Example Success Response

```json
{
  "success": true,
  "message": "Connected successfully."
}
```

#### Example Error Response

```json
{
  "code": "elementor_library_not_connected",
  "message": "Failed to connect to Elementor Library.",
  "data": {
    "status": 500
  }
}
```

---

### 2. Disconnect from Elementor Library

- **URL:** `/index.php?rest_route=/elementor/v1/library/connect`
- **Method:** `DELETE`
- **Permissions:** Requires the `manage_options` capability.

#### Example Request

```http
DELETE /index.php?rest_route=/elementor/v1/library/connect
Authorization: Basic {{encoded_wp_credentials}}
```

#### Example Success Response

```json
{
  "success": true,
  "message": "Disconnected successfully."
}
```

#### Example Error Response

```json
{
  "code": "elementor_library_disconnect_error",
  "message": "Error message here",
  "data": {
    "status": 500
  }
}
```

---

## Permissions

All endpoints require the user to have the `manage_options` capability.

## Error Handling

Errors are returned as standard WordPress REST API error objects, with a `code`, `message`, and HTTP status.

## Reference

- For CLI usage and more context, see the [Elementor CLI Library Connect documentation](https://developers.elementor.com/docs/cli/library-connect/).
