# Site Banners REST API Documentation

## Overview
The Site Banners module exposes a complete REST API for managing banners programmatically. All endpoints use the Repository Pattern implemented in V4.0.2.

**Base URL:** `https://your-magento-store.com/rest` or `http://your-magento-store.com/rest`  
**API Version:** V1  
**Authentication:** OAuth 1.0a or Token-based

---

## Quick Start - Postman Setup

### Step 1: Create Integration Token in Magento Admin

1. Login to Magento Admin
2. Navigate to **System > Extensions > Integrations**
3. Click **Add New Integration**
4. Fill in details:
   - **Name**: `Vodacom Banners API`
   - **Email**: Your email
   - **Your Password**: Enter your admin password
5. Click **API** tab
6. Set **Resource Access** to `Custom`
7. Expand **Vodacom** > **Site Banners** and check:
   - ☑️ **View Banners via API** (`Vodacom_SiteBanners::banner_view`)
   - ☑️ **Save Banners via API** (`Vodacom_SiteBanners::banner_api_save`)
   - ☑️ **Delete Banners via API** (`Vodacom_SiteBanners::banner_api_delete`)
8. Click **Save**
9. Click **Activate** button, then **Allow**
10. **IMPORTANT**: Copy the **Access Token** (looks like: `abc123def456...`)

### Step 2: Configure Postman

**Option A: Use your actual domain (Recommended)**
```
Base URL: https://your-domain.test/rest
OR
Base URL: http://localhost/rest
```

**Option B: Find your Magento base URL**
```bash
# Run this command to find your base URL:
bin/magento config:show web/unsecure/base_url
bin/magento config:show web/secure/base_url
```

**Postman Request Setup:**

1. **Create New Request**
2. **Method**: GET
3. **URL**: `http://YOUR_DOMAIN/rest/V1/vodacom/banners/1`
   - Replace `YOUR_DOMAIN` with your actual domain (e.g., `hyva-tutorial.test`, `localhost`)
4. **Headers** tab:
   - Key: `Authorization` | Value: `Bearer YOUR_ACCESS_TOKEN`
   - Key: `Content-Type` | Value: `application/json`
5. Click **Send**

### Step 3: Test Simple Request

**Get Banner by ID:**
```
GET http://YOUR_DOMAIN/rest/V1/vodacom/banners/1
Headers:
  Authorization: Bearer YOUR_ACCESS_TOKEN
  Content-Type: application/json
```

**Expected Success Response (200 OK):**
```json
{
  "banner_id": 1,
  "title": "Welcome Banner",
  "content": "Welcome to our store!",
  "is_active": 1,
  "sort_order": 10,
  "created_at": "2024-11-28 10:30:00",
  "updated_at": "2024-11-28 10:30:00"
}
```

### Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| **401 Unauthorized** | Check your Access Token is correct and starts with `Bearer ` |
| **403 Forbidden** | Verify integration has the required permissions checked |
| **404 Not Found** | Ensure URL is correct: `/rest/V1/vodacom/banners/1` (case-sensitive) |
| **301 Redirect** | Your Magento redirects HTTP to HTTPS - use HTTPS in Postman |
| **Connection refused** | Check Magento is running: `bin/magento cache:status` |
| **Consumer isn't authorized** | Integration not activated - go to System > Integrations and click Activate |
| **No such entity** | Banner ID doesn't exist - try ID 1, 2, 3, 4, or 5 (from sample data) |

### Verify API is Working (Command Line Test)

Run this from your terminal to test:
```bash
# Replace YOUR_DOMAIN and YOUR_TOKEN with your actual values
curl -X GET "http://YOUR_DOMAIN/rest/V1/vodacom/banners/1" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json"
```

If this works but Postman doesn't, the issue is with Postman configuration, not the API.

---

## Authentication

### Generate Integration Token

1. Go to **System > Extensions > Integrations**
2. Create new integration with these permissions:
   - `Vodacom_SiteBanners::banner_view` - View banners
   - `Vodacom_SiteBanners::banner_api_save` - Create/Update banners
   - `Vodacom_SiteBanners::banner_api_delete` - Delete banners
3. Activate integration and save Access Token

### Using Token in Requests

```bash
curl -X GET "https://your-store.com/rest/V1/vodacom/banners/1" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json"
```

---

## Endpoints

### 1. Get Banner by ID

**Endpoint:** `GET /V1/vodacom/banners/:bannerId`  
**Permission:** `Vodacom_SiteBanners::banner_view`

#### Request
```bash
curl -X GET "https://your-store.com/rest/V1/vodacom/banners/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

#### Response (200 OK)
```json
{
  "banner_id": 1,
  "title": "Welcome Banner",
  "content": "Welcome to our store! Enjoy browsing our latest products.",
  "is_active": 1,
  "sort_order": 10,
  "active_from": null,
  "active_to": null,
  "created_at": "2024-11-28 10:30:00",
  "updated_at": "2024-11-28 10:30:00"
}
```

#### Error Responses
- **404 Not Found:** Banner does not exist
```json
{
  "message": "No such entity with bannerId = 999"
}
```

---

### 2. Get List of Banners (with Search Criteria)

**Endpoint:** `GET /V1/vodacom/banners`  
**Permission:** `Vodacom_SiteBanners::banner_view`

#### Request (All Banners)
```bash
curl -X GET "https://your-store.com/rest/V1/vodacom/banners" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

#### Request (With Filters)
```bash
# Get only active banners
curl -X GET "https://your-store.com/rest/V1/vodacom/banners?searchCriteria[filterGroups][0][filters][0][field]=is_active&searchCriteria[filterGroups][0][filters][0][value]=1&searchCriteria[filterGroups][0][filters][0][conditionType]=eq" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"

# Get banners with pagination (page 1, 10 items per page)
curl -X GET "https://your-store.com/rest/V1/vodacom/banners?searchCriteria[currentPage]=1&searchCriteria[pageSize]=10" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"

# Get banners sorted by sort_order ascending
curl -X GET "https://your-store.com/rest/V1/vodacom/banners?searchCriteria[sortOrders][0][field]=sort_order&searchCriteria[sortOrders][0][direction]=ASC" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"

# Complex query: Active banners, sorted by sort_order, paginated
curl -X GET "https://your-store.com/rest/V1/vodacom/banners?searchCriteria[filterGroups][0][filters][0][field]=is_active&searchCriteria[filterGroups][0][filters][0][value]=1&searchCriteria[filterGroups][0][filters][0][conditionType]=eq&searchCriteria[sortOrders][0][field]=sort_order&searchCriteria[sortOrders][0][direction]=ASC&searchCriteria[currentPage]=1&searchCriteria[pageSize]=5" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

#### Response (200 OK)
```json
{
  "items": [
    {
      "banner_id": 1,
      "title": "Welcome Banner",
      "content": "Welcome to our store!",
      "is_active": 1,
      "sort_order": 10,
      "active_from": null,
      "active_to": null,
      "created_at": "2024-11-28 10:30:00",
      "updated_at": "2024-11-28 10:30:00"
    },
    {
      "banner_id": 2,
      "title": "Holiday Sale 2024",
      "content": "Get 30% off on all items!",
      "is_active": 1,
      "sort_order": 20,
      "active_from": "2024-12-01 00:00:00",
      "active_to": "2024-12-31 23:59:59",
      "created_at": "2024-11-28 10:35:00",
      "updated_at": "2024-11-28 10:35:00"
    }
  ],
  "search_criteria": {
    "filter_groups": [
      {
        "filters": [
          {
            "field": "is_active",
            "value": "1",
            "condition_type": "eq"
          }
        ]
      }
    ],
    "sort_orders": [
      {
        "field": "sort_order",
        "direction": "ASC"
      }
    ],
    "page_size": 5,
    "current_page": 1
  },
  "total_count": 2
}
```

---

### 3. Create New Banner

**Endpoint:** `POST /V1/vodacom/banners`  
**Permission:** `Vodacom_SiteBanners::banner_api_save`

#### Request
```bash
curl -X POST "https://your-store.com/rest/V1/vodacom/banners" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "banner": {
      "title": "New Promotion",
      "content": "Check out our latest deals!",
      "is_active": 1,
      "sort_order": 30,
      "active_from": "2024-12-01 00:00:00",
      "active_to": "2024-12-31 23:59:59"
    }
  }'
```

#### Response (200 OK)
```json
{
  "banner_id": 6,
  "title": "New Promotion",
  "content": "Check out our latest deals!",
  "is_active": 1,
  "sort_order": 30,
  "active_from": "2024-12-01 00:00:00",
  "active_to": "2024-12-31 23:59:59",
  "created_at": "2024-11-30 14:25:00",
  "updated_at": "2024-11-30 14:25:00"
}
```

#### Error Responses
- **400 Bad Request:** Validation failed (e.g., missing title)
```json
{
  "message": "Could not save the banner: Title is required"
}
```

---

### 4. Update Existing Banner

**Endpoint:** `PUT /V1/vodacom/banners/:bannerId`  
**Permission:** `Vodacom_SiteBanners::banner_api_save`

#### Request
```bash
curl -X PUT "https://your-store.com/rest/V1/vodacom/banners/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "banner": {
      "banner_id": 1,
      "title": "Updated Welcome Banner",
      "content": "Welcome! Now with 20% discount.",
      "is_active": 1,
      "sort_order": 5
    }
  }'
```

**Note:** Include `banner_id` in the payload to update existing banner.

#### Response (200 OK)
```json
{
  "banner_id": 1,
  "title": "Updated Welcome Banner",
  "content": "Welcome! Now with 20% discount.",
  "is_active": 1,
  "sort_order": 5,
  "active_from": null,
  "active_to": null,
  "created_at": "2024-11-28 10:30:00",
  "updated_at": "2024-11-30 14:30:00"
}
```

#### Error Responses
- **404 Not Found:** Banner does not exist
- **400 Bad Request:** Validation failed

---

### 5. Delete Banner

**Endpoint:** `DELETE /V1/vodacom/banners/:bannerId`  
**Permission:** `Vodacom_SiteBanners::banner_api_delete`

#### Request
```bash
curl -X DELETE "https://your-store.com/rest/V1/vodacom/banners/6" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

#### Response (200 OK)
```json
true
```

#### Error Responses
- **404 Not Found:** Banner does not exist
```json
{
  "message": "No such entity with bannerId = 999"
}
```
- **500 Internal Server Error:** Could not delete banner
```json
{
  "message": "Could not delete the banner with id: 6"
}
```

---

## SearchCriteria Query Examples

### Filter Operators
- `eq` - Equals
- `neq` - Not equals
- `gt` - Greater than
- `lt` - Less than
- `gteq` - Greater than or equal
- `lteq` - Less than or equal
- `like` - LIKE query (use % for wildcards)
- `in` - IN array
- `nin` - NOT IN array

### Example 1: Get Active Banners with Sort Order < 50
```bash
curl -X GET "https://your-store.com/rest/V1/vodacom/banners?searchCriteria[filterGroups][0][filters][0][field]=is_active&searchCriteria[filterGroups][0][filters][0][value]=1&searchCriteria[filterGroups][0][filters][0][conditionType]=eq&searchCriteria[filterGroups][1][filters][0][field]=sort_order&searchCriteria[filterGroups][1][filters][0][value]=50&searchCriteria[filterGroups][1][filters][0][conditionType]=lt" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Example 2: Search Banners by Title (LIKE)
```bash
curl -X GET "https://your-store.com/rest/V1/vodacom/banners?searchCriteria[filterGroups][0][filters][0][field]=title&searchCriteria[filterGroups][0][filters][0][value]=%25Sale%25&searchCriteria[filterGroups][0][filters][0][conditionType]=like" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Example 3: Get Banners Created After Specific Date
```bash
curl -X GET "https://your-store.com/rest/V1/vodacom/banners?searchCriteria[filterGroups][0][filters][0][field]=created_at&searchCriteria[filterGroups][0][filters][0][value]=2024-11-01&searchCriteria[filterGroups][0][filters][0][conditionType]=gteq" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Testing with Postman

### Import Collection

1. Create new Postman collection: "Vodacom Site Banners API"
2. Add environment variables:
   - `base_url`: `https://your-store.com/rest`
   - `access_token`: Your integration token

### Pre-request Script (for all requests)
```javascript
pm.request.headers.add({
    key: 'Authorization',
    value: 'Bearer ' + pm.environment.get('access_token')
});
pm.request.headers.add({
    key: 'Content-Type',
    value: 'application/json'
});
```

### Tests Script (for validation)
```javascript
// Test: Response status is 200
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

// Test: Response is JSON
pm.test("Response is JSON", function () {
    pm.response.to.be.json;
});

// Test: Banner has required fields (for GET)
pm.test("Banner has required fields", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('banner_id');
    pm.expect(jsonData).to.have.property('title');
    pm.expect(jsonData).to.have.property('content');
});
```

---

## Error Handling

### HTTP Status Codes
- **200 OK** - Success
- **400 Bad Request** - Validation error or malformed request
- **401 Unauthorized** - Missing or invalid authentication token
- **403 Forbidden** - Insufficient permissions
- **404 Not Found** - Banner does not exist
- **500 Internal Server Error** - Server-side error

### Error Response Format
```json
{
  "message": "Error description",
  "parameters": ["param1", "param2"],
  "trace": "Exception trace (only in developer mode)"
}
```

---

## Rate Limiting

Magento 2 does not have built-in rate limiting for REST API. Consider implementing:
- Reverse proxy rate limiting (Nginx, Varnish)
- Custom module for API throttling
- CloudFlare or similar CDN rate limiting

---

## Security Best Practices

1. **Always use HTTPS** - Never send tokens over HTTP
2. **Rotate tokens regularly** - Regenerate integration tokens every 90 days
3. **Limit token scope** - Only grant necessary permissions
4. **Validate input** - Never trust client data
5. **Log API access** - Monitor for suspicious activity
6. **Use IP whitelisting** - Restrict API access to known IPs when possible

---

## Version History

### V4.0.3 (Current)
- Initial REST API implementation
- All CRUD operations exposed
- SearchCriteria support for complex queries
- ACL-based permissions

---

## Support

For issues or questions:
- **Module Version:** 4.0.3
- **Magento Version:** 2.4.x
- **GitHub:** [vodacom-site-banners repository]
- **Documentation:** See `README.md` for module overview

---

## Next Steps

- **V5.0.0:** Dependency Injection & ViewModels
- **V6.0.0:** Plugins (Interceptors) for extensibility

---

**Last Updated:** November 30, 2024  
**API Version:** V1  
**Module Version:** 4.0.3
