# Integration Test Guide

## Prerequisites
1. Laravel backend running on `http://localhost:8000`
2. Vue.js frontend running on `http://localhost:3000`
3. Database migrations run and seeded

## Test Steps

### 1. Backend Setup
```bash
cd server
php artisan serve
```

### 2. Frontend Setup
```bash
cd client
npm run dev
```

### 3. Test Authentication Flow

#### 3.1 Registration
1. Navigate to `http://localhost:3000/register`
2. Fill in registration form:
   - First Name: Test
   - Last Name: User
   - Email: test@example.com
   - Password: password123
   - Confirm Password: password123
   - Check "I agree to terms"
3. Click "Create Account"
4. Should redirect to dashboard

#### 3.2 Login
1. Navigate to `http://localhost:3000/login`
2. Fill in login form:
   - Email: test@example.com
   - Password: password123
3. Click "Sign In"
4. Should redirect to dashboard

#### 3.3 Dashboard Access
1. After login, should see dashboard with:
   - Welcome message with user name
   - Statistics cards (posts, comments, likes)
   - Recent posts list
   - Navigation sidebar

### 4. Test API Endpoints

#### 4.1 Test Registration API
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test2@example.com",
    "password": "password123"
  }'
```

Expected response:
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {...},
    "token": "..."
  }
}
```

#### 4.2 Test Login API
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

#### 4.3 Test Dashboard Stats API
```bash
curl -X GET http://localhost:8000/api/dashboard/stats \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 5. Common Issues & Solutions

#### 5.1 CORS Issues
- Ensure CORS configuration is correct in `server/config/cors.php`
- Check that frontend is running on `http://localhost:3000`

#### 5.2 Authentication Issues
- Check that Sanctum is properly configured
- Verify token is being sent in Authorization header
- Check browser console for errors

#### 5.3 Database Issues
- Run migrations: `php artisan migrate`
- Check database connection in `.env`
- Verify models have correct relationships

### 6. Next Steps
After successful authentication:
1. Test post creation
2. Test post editing
3. Test post deletion
4. Test comment system
5. Test like system

## Current Status
- ✅ CORS Configuration
- ✅ Authentication API
- ✅ Frontend Authentication Store
- ✅ Login/Register Components
- ✅ Route Guards
- ✅ Dashboard Integration
- 🔄 Posts Management (In Progress)
- ⏳ Comments System
- ⏳ Like System






